<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\TahunAjaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BendaharaController extends Controller
{
    public function dashboard()
    {
        $pembayaranMenunggu = Pembayaran::where('status', Pembayaran::STATUS_MENUNGGU_VERIFIKASI)
            ->with(['tagihan.kartuPembayaran.user'])
            ->count();

        $pembayaranHarini = Pembayaran::whereDate('created_at', today())
            ->count();

        $pendapatanBulanIni = Pembayaran::where('status', Pembayaran::STATUS_DITERIMA)
            ->whereMonth('created_at', now()->month)
            ->sum('jumlah_bayar');

        $totalSantri = User::where('role', User::ROLE_SANTRI)->count();

        return view('bendahara.dashboard', compact(
            'pembayaranMenunggu',
            'pembayaranHarini',
            'pendapatanBulanIni',
            'totalSantri'
        ));
    }

    public function pembayaranMenunggu()
    {
        $pembayarans = Pembayaran::where('status', Pembayaran::STATUS_MENUNGGU_VERIFIKASI)
            ->with(['tagihan.kartuPembayaran.user.tahunAjaran'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('bendahara.pembayaran-menunggu', compact('pembayarans'));
    }

    public function showPembayaran($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihan.kartuPembayaran.user',
            'tagihan.kartuPembayaran.tahunAjaran',
            'tagihan.tagihanDetails.jenisTagihan'
        ])->findOrFail($id);

        return view('bendahara.show-pembayaran', compact('pembayaran'));
    }

    public function verifikasiPembayaran(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'catatan' => 'nullable|string'
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        
        $pembayaran->update([
            'status' => $request->status,
            'verified_by' => Auth::id(),
            'verified_at' => now(),
            'catatan' => $request->catatan
        ]);

        // Update status tagihan jika pembayaran diterima
        if ($request->status === Pembayaran::STATUS_DITERIMA) {
            $tagihan = $pembayaran->tagihan;
            
            // Cek apakah total pembayaran sudah mencukupi
            $totalPembayaran = $tagihan->pembayaran()
                ->where('status', Pembayaran::STATUS_DITERIMA)
                ->sum('jumlah_bayar');
                
            if ($totalPembayaran >= $tagihan->total) {
                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
                
                // Update status detail tagihan
                $tagihan->tagihanDetails()->update(['status' => TagihanDetail::STATUS_LUNAS]);
            }
        }

        $message = $request->status === Pembayaran::STATUS_DITERIMA 
            ? 'Pembayaran berhasil diterima!' 
            : 'Pembayaran ditolak!';

        return redirect()->back()->with('success', $message);
    }

    public function riwayatVerifikasi()
    {
        $pembayarans = Pembayaran::whereIn('status', [
            Pembayaran::STATUS_DITERIMA, 
            Pembayaran::STATUS_DITOLAK
        ])
        ->with(['tagihan.kartuPembayaran.user', 'verifiedBy'])
        ->orderBy('verified_at', 'desc')
        ->paginate(20);

        return view('bendahara.riwayat-verifikasi', compact('pembayarans'));
    }

    public function laporanPembayaran(Request $request)
    {
        $query = Pembayaran::where('status', Pembayaran::STATUS_DITERIMA)
            ->with(['tagihan.kartuPembayaran.user']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_selesai);
        }

        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('tagihan.kartuPembayaran', function($q) use ($request) {
                $q->where('tahun_ajaran_id', $request->tahun_ajaran);
            });
        }

        $pembayarans = $query->orderBy('tanggal_bayar', 'desc')->paginate(50);
        $tahunAjarans = TahunAjaran::all();

        $totalPendapatan = $query->sum('jumlah_bayar');

        return view('bendahara.laporan-pembayaran', compact(
            'pembayarans', 
            'tahunAjarans', 
            'totalPendapatan'
        ));
    }

    public function dataSantri()
    {
        $santris = User::where('role', User::ROLE_SANTRI)
            ->with(['kartuPembayaran' => function($q) {
                $q->whereHas('tahunAjaran', function($q2) {
                    $q2->where('is_active', true);
                })->with('tahunAjaran');
            }])
            ->paginate(20);

        return view('bendahara.data-santri', compact('santris'));
    }

    public function detailSantri($id)
    {
        $santri = User::where('role', User::ROLE_SANTRI)
            ->with([
                'kartuPembayaran.tagihan.tagihanDetails.jenisTagihan',
                'kartuPembayaran.tagihan.pembayaran'
            ])
            ->findOrFail($id);

        return view('bendahara.detail-santri', compact('santri'));
    }
}