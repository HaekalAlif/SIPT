<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JenisTagihan;
use App\Models\KategoriTagihan;
use App\Models\TahunAjaran;
use App\Models\KartuPembayaran;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_santri' => User::where('role', 'santri')->count(),
            'menunggu_verifikasi' => Pembayaran::where('status', 'menunggu_verifikasi')->count(),
            'pemasukan_bulan_ini' => Pembayaran::where('status', 'diterima')
                ->whereMonth('tanggal_bayar', now()->month)
                ->whereYear('tanggal_bayar', now()->year)
                ->sum('jumlah_bayar'),
            'tagihan_belum_lunas' => Tagihan::whereIn('status', ['belum_bayar', 'menunggu_verifikasi'])->count(),
        ];

        // Recent santri (exclude admin)
        $recent_users = User::where('role', 'santri')->latest()->take(5)->get();
        
        // Pending payments for verification table
        $pending_payments = Pembayaran::with(['tagihan.kartuPembayaran.user'])
            ->where('status', 'menunggu_verifikasi')
            ->orderBy('created_at', 'asc')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact(
            'stats',
            'recent_users',
            'pending_payments'
        ));
    }

    // === MASTER DATA: Users ===
    public function users(Request $request)
    {
        $query = User::query();
        
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_santri', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%')
                  ->orWhere('nis', 'like', '%'.$request->search.'%');
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10);
        
        return view('admin.users.index', compact('users'));
    }

    public function createUser()
    {
        $tahunAjaran = TahunAjaran::all();
        return view('admin.users.form', ['user' => null, 'tahunAjaran' => $tahunAjaran]);
    }

    public function storeUser(Request $request)
    {
        $request->validate([
            'nama_santri' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:8|confirmed',
            'role' => 'required|in:admin,bendahara,santri',
            'nis' => 'nullable|unique:users,nis',
            'no_telp' => 'nullable|string|max:15',
            'tahun_ajaran_masuk_id' => 'nullable|exists:tahun_ajaran,id'
        ]);

        $newUser = User::create([
            'nama_santri' => $request->nama_santri,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'nis' => $request->nis,
            'no_telp' => $request->no_telp,
            'status' => 'active',
            'alamat' => $request->alamat,
            'nama_orang_tua' => $request->nama_orang_tua,
            'tingkatan' => $request->tingkatan,
            'kelas' => $request->kelas,
            'tahun_ajaran_masuk_id' => $request->tahun_ajaran_masuk_id,
        ]);

        return redirect()->route('admin.users')->with('success', 'User berhasil ditambahkan');
    }

    public function showUser($id)
    {
        $user = User::with(['kartuPembayaran.tagihan.pembayaran'])->findOrFail($id);
        return view('admin.users.show', compact('user'));
    }

    public function editUser($id)
    {
        $user = User::findOrFail($id);
        $tahunAjaran = TahunAjaran::all();
        return view('admin.users.form', compact('user', 'tahunAjaran'));
    }

    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);
        
        $request->validate([
            'nama_santri' => 'required|string|max:150',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => 'nullable|min:8|confirmed',
            'role' => 'required|in:admin,bendahara,santri',
            'nis' => 'nullable|unique:users,nis,' . $id,
            'tahun_ajaran_masuk_id' => 'nullable|exists:tahun_ajaran,id'
        ]);

        $data = [
            'nama_santri' => $request->nama_santri,
            'email' => $request->email,
            'role' => $request->role,
            'nis' => $request->nis,
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'nama_orang_tua' => $request->nama_orang_tua,
            'tingkatan' => $request->tingkatan,
            'kelas' => $request->kelas,
            'tahun_ajaran_masuk_id' => $request->tahun_ajaran_masuk_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $previousTahunAjaranId = $user->tahun_ajaran_masuk_id;
        $user->update($data);
        $user->refresh();

        return redirect()->route('admin.users')->with('success', 'User berhasil diperbarui');
    }

    public function destroyUser($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return redirect()->route('admin.users')->with('success', 'User berhasil dihapus');
    }

    // === MASTER DATA: Tahun Ajaran ===
    public function tahunAjaran()
    {
        $tahunAjaran = TahunAjaran::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.tahun_ajaran.index', compact('tahunAjaran'));
    }

    public function storeTahunAjaran(Request $request) 
    {
        $request->validate([
            'nama' => 'required|string|unique:tahun_ajaran,nama',
            'is_active' => 'boolean'
        ]);

        if ($request->is_active) {
            // Deactivate other active years
            TahunAjaran::where('is_active', true)->update(['is_active' => false]);
        }

        TahunAjaran::create($request->all());

        return redirect()->back()->with('success', 'Tahun ajaran berhasil ditambahkan');
    }

    public function updateTahunAjaran(Request $request, $id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        
        $request->validate([
            'nama' => 'required|string|unique:tahun_ajaran,nama,' . $id,
            'is_active' => 'boolean'
        ]);

        if ($request->is_active) {
            // Deactivate other active years
            TahunAjaran::where('id', '!=', $id)->update(['is_active' => false]);
        }

        $tahunAjaran->update($request->all());

        return redirect()->back()->with('success', 'Tahun ajaran berhasil diperbarui');
    }

    public function destroyTahunAjaran($id)
    {
        $tahunAjaran = TahunAjaran::findOrFail($id);
        $tahunAjaran->delete();
        return redirect()->back()->with('success', 'Tahun ajaran berhasil dihapus');
    }

    // === MASTER DATA: Kategori Tagihan ===
    public function kategoriTagihan()
    {
        $kategoriTagihan = KategoriTagihan::orderBy('nama', 'asc')->paginate(10);
        return view('admin.kategori_tagihan.index', compact('kategoriTagihan'));
    }

    public function storeKategoriTagihan(Request $request)
    {
        $request->validate(['nama' => 'required|string|unique:kategori_tagihan,nama']);
        KategoriTagihan::create($request->all());
        return redirect()->back()->with('success', 'Kategori tagihan berhasil ditambahkan');
    }

    public function updateKategoriTagihan(Request $request, $id)
    {
        $request->validate(['nama' => 'required|string|unique:kategori_tagihan,nama,' . $id]);
        KategoriTagihan::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Kategori tagihan berhasil diperbarui');
    }

    public function destroyKategoriTagihan($id)
    {
        KategoriTagihan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori tagihan berhasil dihapus');
    }

    // === MASTER DATA: Jenis Tagihan ===
    public function jenisTagihan()
    {
        $jenisTagihan = JenisTagihan::with('kategori')->orderBy('created_at', 'desc')->paginate(10);
        $kategoriTagihan = KategoriTagihan::all();
        return view('admin.jenis_tagihan.index', compact('jenisTagihan', 'kategoriTagihan'));
    }

    public function storeJenisTagihan(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihan,id',
            'nama_tagihan' => 'required|string',
            'nominal' => 'required|numeric'
        ]);
        
        JenisTagihan::create($request->all());
        return redirect()->back()->with('success', 'Jenis tagihan berhasil ditambahkan');
    }

    public function updateJenisTagihan(Request $request, $id)
    {
        $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihan,id',
            'nama_tagihan' => 'required|string',
            'nominal' => 'required|numeric'
        ]);
        
        JenisTagihan::findOrFail($id)->update($request->all());
        return redirect()->back()->with('success', 'Jenis tagihan berhasil diperbarui');
    }

    public function destroyJenisTagihan($id)
    {
        JenisTagihan::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Jenis tagihan berhasil dihapus');
    }

    // === MASTER DATA: Kartu Pembayaran ===
    public function kartuPembayaran(Request $request)
    {
        $query = KartuPembayaran::with(['user', 'tahunAjaran']);
        
        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('user', function($q) use ($search) {
                $q->where('nama_santri', 'like', "%{$search}%")
                  ->orWhere('nis', 'like', "%{$search}%");
            });
        }
        
        if ($request->has('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran_id);
        }
        
        $kartuPembayaran = $query->orderBy('created_at', 'desc')->paginate(15);
        $tahunAjaran = TahunAjaran::all();
        
        return view('admin.kartu_pembayaran.index', compact('kartuPembayaran', 'tahunAjaran'));
    }
    
    // === Verifikasi Pembayaran ===
    public function verifikasiPembayaran()
    {
        $pembayarans = Pembayaran::with(['tagihan.kartuPembayaran.user', 'tagihan'])
            ->where('status', 'menunggu_verifikasi')
            ->orderBy('created_at', 'asc')
            ->paginate(10);
        return view('admin.verifikasi.index', compact('pembayarans'));
    }

    public function showVerifikasi($id)
    {
        $pembayaran = Pembayaran::with(['tagihan.kartuPembayaran.user', 'tagihan.tagihanDetails.jenisTagihan'])
            ->findOrFail($id);
        return view('admin.verifikasi.show', compact('pembayaran'));
    }

    public function prosesVerifikasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:diterima,ditolak',
            'catatan_verifikator' => 'nullable|string'
        ]);

        $pembayaran = Pembayaran::findOrFail($id);
        
        DB::transaction(function () use ($pembayaran, $request) {
            $pembayaran->update([
                'status' => $request->status,
                'verified_by' => auth()->id(),
                'verified_at' => now(),
                'catatan_verifikator' => $request->catatan_verifikator
            ]);

            if ($request->status === 'diterima') {
                $tagihan = $pembayaran->tagihan;
                
                // Calculate total accepted payments
                $totalBayar = $tagihan->pembayaran()
                    ->where('status', 'diterima')
                    ->sum('jumlah_bayar');

                // Check if fully paid
                if ($totalBayar >= $tagihan->total) {
                    // Update tagihan status to lunas
                    $tagihan->update(['status' => 'lunas']);
                    // Update all tagihan details to lunas
                    $tagihan->tagihanDetails()->update(['status' => 'lunas']);
                } else {
                    // Partially paid - update tagihan to menunggu_verifikasi or keep as belum_bayar
                    if ($tagihan->status === 'belum_bayar') {
                        $tagihan->update(['status' => 'menunggu_verifikasi']);
                    }
                }
            } elseif ($request->status === 'ditolak') {
                // If payment is rejected, tagihan goes back to belum_bayar
                $tagihan = $pembayaran->tagihan;
                if ($tagihan->status !== 'lunas') {
                    $tagihan->update(['status' => 'belum_bayar']);
                }
            }
        });

        return redirect()->route('admin.verifikasi-pembayaran')
            ->with('success', 'Verifikasi pembayaran berhasil diproses');
    }

    // === Laporan ===
    public function laporanPemasukan(Request $request) {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        $query = Pembayaran::with(['tagihan.kartuPembayaran.user', 'tagihan.tagihanDetails'])
            ->where('status', 'diterima')
            ->whereBetween(DB::raw('DATE(tanggal_bayar)'), [$startDate, $endDate])
            ->orderBy('tanggal_bayar', 'desc');

        $pembayarans = $query->paginate(20);
        $totalPemasukan = $query->sum('jumlah_bayar');
        
        return view('admin.laporan.pemasukan', compact('pembayarans', 'totalPemasukan', 'startDate', 'endDate'));
    }

    public function laporanTunggakan(Request $request) {
        $query = User::where('role', 'santri')
            ->whereHas('kartuPembayaran.tagihan', function($q) {
                $q->whereIn('status', ['belum_bayar', 'menunggu_verifikasi']);
            })
            ->with(['kartuPembayaran.tagihan' => function($q) {
                $q->whereIn('status', ['belum_bayar', 'menunggu_verifikasi']);
            }, 'kartuPembayaran.tagihan.pembayaran']);

        if ($request->search) {
            $query->where('nama_santri', 'like', '%'.$request->search.'%')
                  ->orWhere('nis', 'like', '%'.$request->search.'%');
        }

        $santriBelumLunas = $query->paginate(20);

        return view('admin.laporan.tunggakan', compact('santriBelumLunas'));
    }

    public function laporanRekap(Request $request) {
        $tahunAjaran = TahunAjaran::orderByDesc('is_active')->orderByDesc('id')->get();
        $activeTahunAjaran = $request->tahun_ajaran
            ? TahunAjaran::find($request->tahun_ajaran)
            : TahunAjaran::where('is_active', true)->first() ?? $tahunAjaran->first();

        $totalPemasukan = Pembayaran::where('status', 'diterima')
            ->when($activeTahunAjaran, fn($q) => $q->whereHas('tagihan.kartuPembayaran',
                fn($q2) => $q2->where('tahun_ajaran_id', $activeTahunAjaran->id)))
            ->sum('jumlah_bayar');

        $totalTagihan = Tagihan::when($activeTahunAjaran, fn($q) => $q->whereHas('kartuPembayaran',
                fn($q2) => $q2->where('tahun_ajaran_id', $activeTahunAjaran->id)))
            ->sum('total');

        $totalTunggakan = Tagihan::whereIn('status', ['belum_bayar', 'menunggu_verifikasi'])
            ->when($activeTahunAjaran, fn($q) => $q->whereHas('kartuPembayaran',
                fn($q2) => $q2->where('tahun_ajaran_id', $activeTahunAjaran->id)))
            ->sum('total');

        $santriCount = User::where('role', 'santri')->count();
        $pembayaranPending = Pembayaran::where('status', 'menunggu_verifikasi')->count();

        return view('admin.laporan.rekap', compact(
            'tahunAjaran', 'activeTahunAjaran',
            'totalPemasukan', 'totalTagihan', 'totalTunggakan',
            'santriCount', 'pembayaranPending'
        ));
    }

    // === Settings ===
    public function settings() {
        return view('admin.settings');
    }

}
