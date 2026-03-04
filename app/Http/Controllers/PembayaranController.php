<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class PembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pembayaran::with([
            'tagihan.kartuPembayaran.user',
            'tagihan.kartuPembayaran.tahunAjaran',
            'verifiedBy'
        ]);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan santri
        if ($request->filled('santri')) {
            $query->whereHas('tagihan.kartuPembayaran.user', function($q) use ($request) {
                $q->where('nama_santri', 'like', '%' . $request->santri . '%');
            });
        }

        // Filter berdasarkan tanggal
        if ($request->filled('tanggal_mulai')) {
            $query->whereDate('tanggal_bayar', '>=', $request->tanggal_mulai);
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate('tanggal_bayar', '<=', $request->tanggal_selesai);
        }

        $pembayarans = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.pembayaran.index', compact('pembayarans'));
    }

    public function show($id)
    {
        $pembayaran = Pembayaran::with([
            'tagihan.kartuPembayaran.user',
            'tagihan.kartuPembayaran.tahunAjaran',
            'tagihan.tagihanDetails.jenisTagihan.kategori',
            'verifiedBy'
        ])->findOrFail($id);

        return view('admin.pembayaran.show', compact('pembayaran'));
    }

    public function create($tagihanId = null)
    {
        $tagihans = Tagihan::with(['kartuPembayaran.user'])
            ->whereIn('status', [Tagihan::STATUS_BELUM_BAYAR, Tagihan::STATUS_MENUNGGU_VERIFIKASI])
            ->get();

        $selectedTagihan = null;
        if ($tagihanId) {
            $selectedTagihan = Tagihan::findOrFail($tagihanId);
        }

        return view('admin.pembayaran.create', compact('tagihans', 'selectedTagihan'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tagihan_id' => 'required|exists:tagihan,id',
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1',
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'status' => 'required|in:menunggu_verifikasi,diterima,ditolak'
        ]);

        // Upload bukti pembayaran
        $fileName = 'bukti_' . $request->tagihan_id . '_' . time() . '.' . $request->bukti_pembayaran->extension();
        $filePath = $request->bukti_pembayaran->storeAs('bukti_pembayaran', $fileName, 'public');

        $pembayaran = Pembayaran::create([
            'tagihan_id' => $request->tagihan_id,
            'tanggal_bayar' => $request->tanggal_bayar,
            'jumlah_bayar' => $request->jumlah_bayar,
            'bukti_pembayaran' => $filePath,
            'status' => $request->status,
            'verified_by' => $request->status !== Pembayaran::STATUS_MENUNGGU_VERIFIKASI ? Auth::id() : null,
            'verified_at' => $request->status !== Pembayaran::STATUS_MENUNGGU_VERIFIKASI ? now() : null
        ]);

        // Update status tagihan jika pembayaran langsung diterima
        if ($request->status === Pembayaran::STATUS_DITERIMA) {
            $tagihan = $pembayaran->tagihan;
            $totalPembayaran = $tagihan->pembayaran()
                ->where('status', Pembayaran::STATUS_DITERIMA)
                ->sum('jumlah_bayar');
                
            if ($totalPembayaran >= $tagihan->total) {
                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
            } else {
                $tagihan->update(['status' => Tagihan::STATUS_MENUNGGU_VERIFIKASI]);
            }
        }

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $pembayaran = Pembayaran::with(['tagihan.kartuPembayaran.user'])
            ->findOrFail($id);

        return view('admin.pembayaran.edit', compact('pembayaran'));
    }

    public function update(Request $request, $id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        $request->validate([
            'tanggal_bayar' => 'required|date',
            'jumlah_bayar' => 'required|numeric|min:1',
            'status' => 'required|in:menunggu_verifikasi,diterima,ditolak',
            'bukti_pembayaran' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $updateData = [
            'tanggal_bayar' => $request->tanggal_bayar,
            'jumlah_bayar' => $request->jumlah_bayar,
            'status' => $request->status
        ];

        // Update bukti pembayaran jika ada file baru
        if ($request->hasFile('bukti_pembayaran')) {
            // Hapus file lama
            if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
                Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
            }

            // Upload file baru
            $fileName = 'bukti_' . $pembayaran->tagihan_id . '_' . time() . '.' . $request->bukti_pembayaran->extension();
            $filePath = $request->bukti_pembayaran->storeAs('bukti_pembayaran', $fileName, 'public');
            $updateData['bukti_pembayaran'] = $filePath;
        }

        // Update verified info jika status berubah
        if ($pembayaran->status !== $request->status) {
            $updateData['verified_by'] = Auth::id();
            $updateData['verified_at'] = now();
        }

        $pembayaran->update($updateData);

        // Update status tagihan berdasarkan pembayaran
        $tagihan = $pembayaran->tagihan;
        if ($request->status === Pembayaran::STATUS_DITERIMA) {
            $totalPembayaran = $tagihan->pembayaran()
                ->where('status', Pembayaran::STATUS_DITERIMA)
                ->sum('jumlah_bayar');
                
            if ($totalPembayaran >= $tagihan->total) {
                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
            }
        }

        return redirect()->route('admin.pembayaran.show', $id)
            ->with('success', 'Pembayaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        // Hapus file bukti pembayaran
        if ($pembayaran->bukti_pembayaran && Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
            Storage::disk('public')->delete($pembayaran->bukti_pembayaran);
        }

        $pembayaran->delete();

        return redirect()->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran berhasil dihapus!');
    }

    public function verifikasi(Request $request, $id)
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
            $totalPembayaran = $tagihan->pembayaran()
                ->where('status', Pembayaran::STATUS_DITERIMA)
                ->sum('jumlah_bayar');
                
            if ($totalPembayaran >= $tagihan->total) {
                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
            }
        }

        $message = $request->status === Pembayaran::STATUS_DITERIMA 
            ? 'Pembayaran berhasil diterima!' 
            : 'Pembayaran ditolak!';

        return back()->with('success', $message);
    }

    public function downloadBukti($id)
    {
        $pembayaran = Pembayaran::findOrFail($id);
        
        if (!$pembayaran->bukti_pembayaran || !Storage::disk('public')->exists($pembayaran->bukti_pembayaran)) {
            return back()->with('error', 'File bukti pembayaran tidak ditemukan!');
        }

        return Storage::disk('public')->download($pembayaran->bukti_pembayaran);
    }
}