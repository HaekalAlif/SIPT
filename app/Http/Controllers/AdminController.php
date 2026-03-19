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
use App\Models\MetodePembayaran;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

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
        $query = User::with('tahunAjaranMasuk');
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('id')->get();
        $tingkatanOptions = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->distinct()
            ->orderBy('tingkatan')
            ->pluck('tingkatan');

        $kelasPairs = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->whereNotNull('kelas')
            ->select('tingkatan', 'kelas')
            ->distinct()
            ->orderBy('tingkatan')
            ->orderBy('kelas')
            ->get();

        $kelasOptionsByTingkatan = $kelasPairs
            ->groupBy('tingkatan')
            ->map(fn($rows) => $rows->pluck('kelas')->values())
            ->toArray();
        
        if ($request->has('role') && $request->role != '') {
            $query->where('role', $request->role);
        }

        if ($request->filled('tahun_ajaran_id')) {
            $query->where('tahun_ajaran_masuk_id', $request->tahun_ajaran_id);
        }

        if ($request->filled('tingkatan')) {
            $query->where('tingkatan', $request->tingkatan);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }
        
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('nama_santri', 'like', '%'.$request->search.'%')
                  ->orWhere('email', 'like', '%'.$request->search.'%');
            });
        }
        
        $users = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();
        
        return view('admin.users.index', compact(
            'users',
            'tahunAjarans',
            'tingkatanOptions',
            'kelasOptionsByTingkatan'
        ));
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
            'no_telp' => 'nullable|string|max:15',
            'tahun_ajaran_masuk_id' => 'nullable|exists:tahun_ajaran,id',
            'nama_orang_tua' => 'required_if:role,santri|string|max:150',
            'tempat_lahir' => 'required_if:role,santri|string|max:100',
            'tanggal_lahir' => 'required_if:role,santri|date',
            'jenis_kelamin' => 'required_if:role,santri|in:L,P',
            'alamat' => 'required_if:role,santri|string',
            'tingkatan' => 'required_if:role,santri|in:MI,SMP/MTs,SMK/MA,Perguruan Tinggi',
            'kelas' => 'required_if:role,santri|string|max:50',
            'tingkatan_ngaji' => 'required_if:role,santri|string|max:100',
        ]);

        $newUser = User::create([
            'nama_santri' => $request->nama_santri,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'no_telp' => $request->no_telp,
            'status' => $this->normalizeStatus($request->status),
            'alamat' => $request->alamat,
            'nama_orang_tua' => $request->nama_orang_tua,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tingkatan' => $request->tingkatan,
            'kelas' => $request->kelas,
            'tingkatan_ngaji' => $request->tingkatan_ngaji,
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
            'tahun_ajaran_masuk_id' => 'nullable|exists:tahun_ajaran,id',
            'nama_orang_tua' => 'required_if:role,santri|string|max:150',
            'tempat_lahir' => 'required_if:role,santri|string|max:100',
            'tanggal_lahir' => 'required_if:role,santri|date',
            'jenis_kelamin' => 'required_if:role,santri|in:L,P',
            'alamat' => 'required_if:role,santri|string',
            'no_telp' => 'required_if:role,santri|string|max:15',
            'tingkatan' => 'required_if:role,santri|in:MI,SMP/MTs,SMK/MA,Perguruan Tinggi',
            'kelas' => 'required_if:role,santri|string|max:50',
            'tingkatan_ngaji' => 'required_if:role,santri|string|max:100',
        ]);

        $data = [
            'nama_santri' => $request->nama_santri,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $this->normalizeStatus($request->status),
            'no_telp' => $request->no_telp,
            'alamat' => $request->alamat,
            'nama_orang_tua' => $request->nama_orang_tua,
            'tempat_lahir' => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'tingkatan' => $request->tingkatan,
            'kelas' => $request->kelas,
            'tingkatan_ngaji' => $request->tingkatan_ngaji,
            'tahun_ajaran_masuk_id' => $request->tahun_ajaran_masuk_id,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);
        $user->refresh();

        $message = 'User berhasil diperbarui';
        if ($user->role === User::ROLE_SANTRI && $this->isSantriProfileComplete($user)) {
            $result = $this->generateTagihanForSantri($user);
            $message .= '. Auto-generate tagihan: ' . $result['created_tagihan'] . ' tagihan baru, ' .
                $result['created_details'] . ' detail dibuat.';
        }

        return redirect()->route('admin.users')->with('success', $message);
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
        $tingkatanOptions = ['MI', 'SMP/MTs', 'SMK/MA', 'Perguruan Tinggi'];
        $tingkatanNgajiOptions = [
            'I Tsanawiyyah',
            'II Tsanawiyyah',
            'III Tsanawiyyah',
            "VI Ibtida'iyah",
            "V Ibtida'iyah",
            "IV Ibtida'iyah",
            "III I'dad",
            'PTQ',
        ];

        return view('admin.jenis_tagihan.index', compact(
            'jenisTagihan',
            'kategoriTagihan',
            'tingkatanOptions',
            'tingkatanNgajiOptions'
        ));
    }

    public function storeJenisTagihan(Request $request)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihan,id',
            'nama_tagihan' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'is_bulanan' => 'nullable|boolean',
            'target_scope' => ['required', Rule::in([
                JenisTagihan::TARGET_SCOPE_ALL,
                JenisTagihan::TARGET_SCOPE_TINGKATAN,
                JenisTagihan::TARGET_SCOPE_NGAJI,
            ])],
            'target_value' => 'nullable|string|max:100|required_if:target_scope,tingkatan,ngaji',
        ]);

        $validated['is_bulanan'] = (bool) ($validated['is_bulanan'] ?? false);
        $validated['target_value'] = JenisTagihan::normalizeTargetValue(
            $validated['target_scope'],
            $validated['target_value'] ?? null
        );

        JenisTagihan::create($validated);
        return redirect()->back()->with('success', 'Jenis tagihan berhasil ditambahkan');
    }

    public function updateJenisTagihan(Request $request, $id)
    {
        $validated = $request->validate([
            'kategori_id' => 'required|exists:kategori_tagihan,id',
            'nama_tagihan' => 'required|string',
            'nominal' => 'required|numeric|min:0',
            'is_bulanan' => 'nullable|boolean',
            'target_scope' => ['required', Rule::in([
                JenisTagihan::TARGET_SCOPE_ALL,
                JenisTagihan::TARGET_SCOPE_TINGKATAN,
                JenisTagihan::TARGET_SCOPE_NGAJI,
            ])],
            'target_value' => 'nullable|string|max:100|required_if:target_scope,tingkatan,ngaji',
        ]);

        $validated['is_bulanan'] = (bool) ($validated['is_bulanan'] ?? false);
        $validated['target_value'] = JenisTagihan::normalizeTargetValue(
            $validated['target_scope'],
            $validated['target_value'] ?? null
        );

        JenisTagihan::findOrFail($id)->update($validated);
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
                  ->orWhere('email', 'like', "%{$search}%");
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
    public function verifikasiPembayaran(Request $request)
    {
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('id')->get();
        $tingkatanOptions = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->distinct()
            ->orderBy('tingkatan')
            ->pluck('tingkatan');

        $kelasPairs = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->whereNotNull('kelas')
            ->select('tingkatan', 'kelas')
            ->distinct()
            ->orderBy('tingkatan')
            ->orderBy('kelas')
            ->get();

        $kelasOptionsByTingkatan = $kelasPairs
            ->groupBy('tingkatan')
            ->map(fn($rows) => $rows->pluck('kelas')->values())
            ->toArray();

        $query = Pembayaran::with(['tagihan.kartuPembayaran.user', 'tagihan'])
            ->where('status', 'menunggu_verifikasi');

        if ($request->filled('tahun_ajaran_id')) {
            $query->whereHas('tagihan.kartuPembayaran', function ($q) use ($request) {
                $q->where('tahun_ajaran_id', $request->tahun_ajaran_id);
            });
        }

        if ($request->filled('tingkatan')) {
            $query->whereHas('tagihan.kartuPembayaran.user', function ($q) use ($request) {
                $q->where('tingkatan', $request->tingkatan);
            });
        }

        if ($request->filled('kelas')) {
            $query->whereHas('tagihan.kartuPembayaran.user', function ($q) use ($request) {
                $q->where('kelas', $request->kelas);
            });
        }

        if ($request->filled('search')) {
            $query->whereHas('tagihan.kartuPembayaran.user', function ($q) use ($request) {
                $q->where('nama_santri', 'like', '%' . $request->search . '%');
            });
        }

        $pembayarans = $query->orderBy('created_at', 'asc')->paginate(10)->withQueryString();

        return view('admin.verifikasi.index', compact(
            'pembayarans',
            'tahunAjarans',
            'tingkatanOptions',
            'kelasOptionsByTingkatan'
        ));
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
                  ->orWhere('email', 'like', '%'.$request->search.'%');
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

    public function laporanRekapKelas(Request $request)
    {
        $data = $this->buildLaporanRekapKelasData($request);

        return view('admin.laporan.rekap-kelas', $data);
    }

    public function downloadLaporanRekapKelasPdf(Request $request)
    {
        $data = $this->buildLaporanRekapKelasData($request);

        if (!$data['hasRequiredFilters']) {
            return redirect()->route('admin.laporan-rekap-kelas', $request->only(['tahun_ajaran_id', 'tingkatan', 'kelas']))
                ->with('error', 'Pilih tingkatan dan kelas terlebih dahulu.');
        }

        $options = new Options();
        $options->set('defaultFont', 'sans-serif');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('admin.laporan.pdf.rekap-kelas', $data)->render());
        $pdf->setPaper('a4', 'landscape');
        $pdf->render();

        $filename = 'rekap-kelas-' . ($data['selectedTingkatan'] ?? 'tingkatan') . '-' . ($data['selectedKelas'] ?? 'kelas') . '-' . ($data['selectedTahunAjaran']->nama ?? 'tahun') . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . str_replace([' ', '/'], ['-', '-'], $filename) . '"',
        ]);
    }

    private function buildLaporanRekapKelasData(Request $request): array
    {
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('id')->get();

        $tingkatanOptions = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->distinct()
            ->orderBy('tingkatan')
            ->pluck('tingkatan');

        $kelasPairs = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->whereNotNull('kelas')
            ->select('tingkatan', 'kelas')
            ->distinct()
            ->orderBy('tingkatan')
            ->orderBy('kelas')
            ->get();

        $kelasOptionsByTingkatan = $kelasPairs
            ->groupBy('tingkatan')
            ->map(fn($rows) => $rows->pluck('kelas')->values())
            ->toArray();

        $selectedTingkatan = $request->tingkatan;
        $selectedKelas = $request->kelas;

        $kelasOptions = collect($kelasOptionsByTingkatan[$selectedTingkatan] ?? [])->values();

        $selectedTahunAjaran = $request->filled('tahun_ajaran_id')
            ? TahunAjaran::find($request->tahun_ajaran_id)
            : (TahunAjaran::where('is_active', true)->first() ?? $tahunAjarans->first());

        $hasRequiredFilters = !blank($selectedTingkatan) && !blank($selectedKelas) && $kelasOptions->contains($selectedKelas);

        $santris = collect();
        $santriIds = [];

        if ($hasRequiredFilters) {
            $santriQuery = User::where('role', User::ROLE_SANTRI)
                ->where('tingkatan', $selectedTingkatan)
                ->where('kelas', $selectedKelas);

            $santris = $santriQuery->orderBy('nama_santri')->get();
            $santriIds = $santris->pluck('id')->all();
        }

        $payments = collect();
        if ($selectedTahunAjaran && !empty($santriIds)) {
            $payments = Pembayaran::with([
                'tagihan.kartuPembayaran',
                'tagihan.tagihanDetails.jenisTagihan.kategori',
            ])
                ->where('status', Pembayaran::STATUS_DITERIMA)
                ->whereHas('tagihan.kartuPembayaran', function ($q) use ($santriIds, $selectedTahunAjaran) {
                    $q->whereIn('user_id', $santriIds)
                        ->where('tahun_ajaran_id', $selectedTahunAjaran->id);
                })
                ->orderBy('tanggal_bayar')
                ->get();
        }

        $paymentsByUser = $payments->groupBy(function ($payment) {
            return $payment->tagihan->kartuPembayaran->user_id ?? 0;
        });

        $middleSections = [
            ['label' => 'PENDAFTARAN', 'keys' => ['pendaftaran', 'registrasi']],
            ['label' => 'KTK, SPP, RAPORT', 'keys' => ['ktk', 'spp', 'raport', 'raprot']],
            ['label' => 'DPP PESANTREN', 'keys' => ['dpp', 'pesantren']],
            ['label' => 'FASILITAS KAMAR', 'keys' => ['fasilitas', 'kamar']],
            ['label' => "TA'ARUF", 'keys' => ['taaruf', "ta'aruf", 'taruf', 'aruf', 'ta,aruf']],
        ];

        $rightSections = [
            ['label' => 'INFAQ', 'keys' => ['infaq', 'infak']],
            ['label' => 'SERAGAM', 'keys' => ['seragam']],
            ['label' => 'RAMADHAN', 'keys' => ['ramadhan', 'ramadan']],
            ['label' => 'IMTIHAN AWAL', 'keys' => ['imtihan awal']],
            ['label' => 'IMTIHAN TSANI', 'keys' => ['imtihan tsani', 'tsani']],
            ['label' => 'HAFLAH AKHIR SANAH', 'keys' => ['haflah', 'akhir sanah']],
        ];

        $sectionDefs = array_merge($middleSections, $rightSections);

        $bulanList = [
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
            'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        ];

        $rows = $santris->map(function ($santri) use ($paymentsByUser, $sectionDefs, $bulanList) {
            $sectionMap = [];
            foreach ($sectionDefs as $def) {
                $sectionMap[$def['label']] = ['nominal' => 0, 'tanggal' => null];
            }

            $syariahMap = [];
            foreach ($bulanList as $bulan) {
                $syariahMap[$bulan] = ['nominal' => 0, 'tanggal' => null];
            }

            $totalDibayar = 0;
            $userPayments = $paymentsByUser->get($santri->id, collect());

            foreach ($userPayments as $payment) {
                $tagihan = $payment->tagihan;
                if (!$tagihan) {
                    continue;
                }

                foreach ($tagihan->tagihanDetails as $detail) {
                    $detailLunas = $detail->status === TagihanDetail::STATUS_LUNAS;
                    if (!$detailLunas) {
                        continue;
                    }

                    $nominal = (float) $detail->nominal;
                    $tanggal = $payment->verified_at
                        ? date('d-m-y', strtotime((string) $payment->verified_at))
                        : date('d-m-y', strtotime((string) $payment->tanggal_bayar));

                    $isSyariah = str_contains(strtolower($detail->jenisTagihan->kategori->nama ?? ''), 'syariah');

                    if ($isSyariah && $detail->bulan && array_key_exists($detail->bulan, $syariahMap)) {
                        $syariahMap[$detail->bulan]['nominal'] += $nominal;
                        $syariahMap[$detail->bulan]['tanggal'] = $tanggal;
                        $totalDibayar += $nominal;
                        continue;
                    }

                    $jenisKey = strtolower(trim((string) ($detail->jenisTagihan->nama_tagihan ?? '')));
                    foreach ($sectionDefs as $def) {
                        foreach ($def['keys'] as $key) {
                            if (str_contains($jenisKey, strtolower($key))) {
                                $sectionMap[$def['label']]['nominal'] += $nominal;
                                $sectionMap[$def['label']]['tanggal'] = $tanggal;
                                $totalDibayar += $nominal;
                                break 2;
                            }
                        }
                    }
                }
            }

            return [
                'user' => $santri,
                'sections' => $sectionMap,
                'syariah' => $syariahMap,
                'total_dibayar' => $totalDibayar,
            ];
        })->values();

        return [
            'tahunAjarans' => $tahunAjarans,
            'tingkatanOptions' => $tingkatanOptions,
            'kelasOptionsByTingkatan' => $kelasOptionsByTingkatan,
            'kelasOptions' => $kelasOptions,
            'selectedTahunAjaran' => $selectedTahunAjaran,
            'selectedTingkatan' => $selectedTingkatan,
            'selectedKelas' => $selectedKelas,
            'hasRequiredFilters' => $hasRequiredFilters,
            'rows' => $rows,
            'bulanList' => $bulanList,
            'middleSections' => $middleSections,
            'rightSections' => $rightSections,
        ];
    }

    // === Settings ===
    public function settings() {
        return redirect()->route('admin.settings.metode-pembayaran');
    }

    public function metodePembayaran()
    {
        $metodePembayaran = MetodePembayaran::ordered()->paginate(10);

        return view('admin.settings.metode-pembayaran', compact('metodePembayaran'));
    }

    public function storeMetodePembayaran(Request $request)
    {
        $validated = $request->validate([
            'nama_metode' => 'required|string|max:100',
            'nama_bank' => 'nullable|string|max:100',
            'nomor_rekening' => 'nullable|string|max:80',
            'atas_nama' => 'nullable|string|max:150',
            'logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['urutan'] = (int) ($validated['urutan'] ?? 0);

        if ($request->hasFile('logo_file')) {
            $validated['logo_path'] = $request->file('logo_file')->store('metode-pembayaran', 'public');
        }

        unset($validated['logo_file']);

        MetodePembayaran::create($validated);

        return redirect()->route('admin.settings.metode-pembayaran')
            ->with('success', 'Metode pembayaran berhasil ditambahkan.');
    }

    public function updateMetodePembayaran(Request $request, $id)
    {
        $metode = MetodePembayaran::findOrFail($id);

        $validated = $request->validate([
            'nama_metode' => 'required|string|max:100',
            'nama_bank' => 'nullable|string|max:100',
            'nomor_rekening' => 'nullable|string|max:80',
            'atas_nama' => 'nullable|string|max:150',
            'logo_file' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool) ($validated['is_active'] ?? false);
        $validated['urutan'] = (int) ($validated['urutan'] ?? 0);

        if ($request->hasFile('logo_file')) {
            if ($metode->logo_path && Storage::disk('public')->exists($metode->logo_path)) {
                Storage::disk('public')->delete($metode->logo_path);
            }

            $validated['logo_path'] = $request->file('logo_file')->store('metode-pembayaran', 'public');
        }

        unset($validated['logo_file']);

        $metode->update($validated);

        return redirect()->route('admin.settings.metode-pembayaran')
            ->with('success', 'Metode pembayaran berhasil diperbarui.');
    }

    public function destroyMetodePembayaran($id)
    {
        $metode = MetodePembayaran::findOrFail($id);

        if ($metode->logo_path && Storage::disk('public')->exists($metode->logo_path)) {
            Storage::disk('public')->delete($metode->logo_path);
        }

        $metode->delete();

        return redirect()->route('admin.settings.metode-pembayaran')
            ->with('success', 'Metode pembayaran berhasil dihapus.');
    }

    private function normalizeStatus(?string $status): string
    {
        return $status === 'nonaktif' ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
    }

    private function isSantriProfileComplete(User $user): bool
    {
        $requiredFields = [
            'tahun_ajaran_masuk_id',
            'tingkatan',
            'kelas',
            'tingkatan_ngaji',
            'nama_orang_tua',
            'no_telp',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin',
            'alamat',
        ];

        foreach ($requiredFields as $field) {
            if (blank($user->{$field})) {
                return false;
            }
        }

        return true;
    }

    private function generateTagihanForSantri(User $user): array
    {
        $tahunAjaranId = (int) $user->tahun_ajaran_masuk_id;
        $kartu = KartuPembayaran::firstOrCreate(
            [
                'user_id' => $user->id,
                'tahun_ajaran_id' => $tahunAjaranId,
            ],
            [
                'nomor_kartu' => 'KP-' . str_pad($user->id, 4, '0', STR_PAD_LEFT) . '-' . $tahunAjaranId,
            ]
        );

        $disabledByAdmin = \App\Models\JenisTagihanDisabled::getDisabledIds($tahunAjaranId, $user->id);
        $jenisTagihans = JenisTagihan::with('kategori')
            ->applicableForUser($user)
            ->whereNotIn('id', $disabledByAdmin)
            ->get();

        $bulanList = ['Juli','Agustus','September','Oktober','November','Desember','Januari','Februari','Maret','April','Mei','Juni'];

        $createdTagihan = 0;
        $createdDetails = 0;

        foreach ($jenisTagihans as $jenis) {
            $kategoriNama = strtolower($jenis->kategori->nama ?? '');
            $isRegistrasi = str_contains($kategoriNama, 'registrasi');

            if ($isRegistrasi) {
                $exists = TagihanDetail::whereHas('tagihan.kartuPembayaran', fn($q) => $q->where('user_id', $user->id))
                    ->where('jenis_tagihan_id', $jenis->id)
                    ->exists();
            } else {
                $exists = TagihanDetail::whereHas('tagihan', fn($q) => $q->where('kartu_id', $kartu->id))
                    ->where('jenis_tagihan_id', $jenis->id)
                    ->exists();
            }

            if ($exists) {
                continue;
            }

            $tagihan = Tagihan::create([
                'kartu_id' => $kartu->id,
                'total' => 0,
                'status' => Tagihan::STATUS_BELUM_BAYAR,
            ]);
            $createdTagihan++;

            $total = 0;
            if ($jenis->is_bulanan) {
                foreach ($bulanList as $bulan) {
                    TagihanDetail::create([
                        'tagihan_id' => $tagihan->id,
                        'jenis_tagihan_id' => $jenis->id,
                        'bulan' => $bulan,
                        'nominal' => $jenis->nominal,
                        'status' => TagihanDetail::STATUS_BELUM_BAYAR,
                    ]);
                    $createdDetails++;
                    $total += (float) $jenis->nominal;
                }
            } else {
                TagihanDetail::create([
                    'tagihan_id' => $tagihan->id,
                    'jenis_tagihan_id' => $jenis->id,
                    'bulan' => null,
                    'nominal' => $jenis->nominal,
                    'status' => TagihanDetail::STATUS_BELUM_BAYAR,
                ]);
                $createdDetails++;
                $total = (float) $jenis->nominal;
            }

            $tagihan->update(['total' => $total]);
        }

        return [
            'created_tagihan' => $createdTagihan,
            'created_details' => $createdDetails,
        ];
    }

}
