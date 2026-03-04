<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\KartuPembayaran;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\KategoriTagihan;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SantriController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        if (!$tahunAjaranAktif) {
            // Handle case when no active academic year
            return view('santri.dashboard', [
                'user' => $user,
                'tahunAjaranAktif' => null,
                'kartuPembayaran' => null,
                'tagihans' => collect(),
                'totalTagihan' => 0,
                'belumBayar' => 0,
                'menungguVerifikasi' => 0,
                'lunas' => 0,
                'recentTagihan' => collect(),
                'totalPembayaran' => 0
            ]);
        }
        
        // Ambil atau buat kartu pembayaran untuk tahun ajaran aktif
        $kartuPembayaran = KartuPembayaran::firstOrCreate([
            'user_id' => $user->id,
            'tahun_ajaran_id' => $tahunAjaranAktif->id
        ], [
            'nomor_kartu' => 'KP-' . str_replace('/', '', $tahunAjaranAktif->nama) . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT)
        ]);

        // Ambil semua tagihan santri untuk tahun ajaran aktif
        $tagihans = Tagihan::where('kartu_id', $kartuPembayaran->id)
            ->with(['tagihanDetails.jenisTagihan.kategori', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculate statistics
        $totalTagihan = $tagihans->count();
        $belumBayar = $tagihans->where('status', Tagihan::STATUS_BELUM_BAYAR)->count();
        $menungguVerifikasi = $tagihans->where('status', Tagihan::STATUS_MENUNGGU_VERIFIKASI)->count();
        $lunas = $tagihans->where('status', Tagihan::STATUS_LUNAS)->count();

        // Get recent tagihan for quick access
        $recentTagihan = $tagihans->take(3);

        // Count pembayaran
        $totalPembayaran = Pembayaran::whereHas('tagihan', function($q) use ($kartuPembayaran) {
            $q->where('kartu_id', $kartuPembayaran->id);
        })->count();

        return view('santri.dashboard', compact(
            'user', 
            'tahunAjaranAktif', 
            'kartuPembayaran',
            'tagihans',
            'totalTagihan',
            'belumBayar', 
            'menungguVerifikasi',
            'lunas',
            'recentTagihan',
            'totalPembayaran'
        ));
    }

    public function formBuatTagihan(Request $request)
    {
        $user = Auth::user();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        
        $kartuPembayaran = KartuPembayaran::where('user_id', $user->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->first();

        // Get kategori from query parameter, default to 'registrasi'
        $kategoriNama = $request->get('kategori', 'registrasi');
        
        // Get the selected kategori dengan jenis tagihan
        $kategori = KategoriTagihan::where('nama', ucfirst($kategoriNama))
            ->with('jenisTagihan')
            ->first();

        if (!$kategori) {
            // Fallback to first kategori if not found
            $kategori = KategoriTagihan::with('jenisTagihan')->first();
        }

        return view('santri.buat-tagihan', compact(
            'user',
            'tahunAjaranAktif',
            'kartuPembayaran',
            'kategori',
            'kategoriNama'
        ));
    }

    public function storeTagihan(Request $request)
    {
        $request->validate([
            'jenis_tagihan_ids' => 'required|array',
            'jenis_tagihan_ids.*' => 'exists:jenis_tagihan,id'
        ]);

        $user = Auth::user();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $kartuPembayaran = KartuPembayaran::where('user_id', $user->id)
            ->where('tahun_ajaran_id', $tahunAjaranAktif->id)
            ->first();

        // Buat tagihan baru
        $tagihan = Tagihan::create([
            'kartu_id' => $kartuPembayaran->id,
            'total' => 0,
            'status' => Tagihan::STATUS_BELUM_BAYAR
        ]);

        $totalTagihan = 0;

        // Buat detail tagihan
        foreach ($request->jenis_tagihan_ids as $jenisTagihanId) {
            $jenisTagihan = JenisTagihan::find($jenisTagihanId);
            
            if ($jenisTagihan->is_bulanan) {
                // Untuk tagihan bulanan (Syariah)
                $bulanList = ['Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember', 
                             'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'];
                
                foreach ($bulanList as $bulan) {
                    TagihanDetail::create([
                        'tagihan_id' => $tagihan->id,
                        'jenis_tagihan_id' => $jenisTagihan->id,
                        'bulan' => $bulan,
                        'nominal' => $jenisTagihan->nominal,
                        'status' => TagihanDetail::STATUS_BELUM_BAYAR
                    ]);
                    $totalTagihan += $jenisTagihan->nominal;
                }
            } else {
                // Untuk tagihan satu kali (Registrasi/Lainnya)
                TagihanDetail::create([
                    'tagihan_id' => $tagihan->id,
                    'jenis_tagihan_id' => $jenisTagihan->id,
                    'bulan' => null,
                    'nominal' => $jenisTagihan->nominal,
                    'status' => TagihanDetail::STATUS_BELUM_BAYAR
                ]);
                $totalTagihan += $jenisTagihan->nominal;
            }
        }

        // Update total tagihan
        $tagihan->update(['total' => $totalTagihan]);

        return redirect()->route('santri.tagihan-pembayaran', ['id' => $tagihan->id])
            ->with('success', 'Tagihan berhasil dibuat!');
    }

    public function konfirmasiTagihan($id)
    {
        $user = Auth::user();
        $tagihan = Tagihan::whereHas('kartuPembayaran', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['tagihanDetails.jenisTagihan', 'kartuPembayaran.tahunAjaran'])
        ->findOrFail($id);

        return view('santri.konfirmasi-tagihan', compact('tagihan', 'user'));
    }

    public function tagihanPembayaran(Request $request)
    {
        $user = Auth::user();
        
        // Get all tahun ajaran for selector
        $tahunAjaranList = TahunAjaran::orderBy('created_at', 'desc')->get();
        
        // Determine selected tahun ajaran
        $selectedTahunAjaran = null;
        if ($request->filled('tahun_ajaran')) {
            $selectedTahunAjaran = TahunAjaran::find($request->tahun_ajaran);
        }
        
        if (!$selectedTahunAjaran) {
            $selectedTahunAjaran = TahunAjaran::where('is_active', true)->first() ?: $tahunAjaranList->first();
        }
        
        if (!$selectedTahunAjaran) {
            return view('santri.tagihan-pembayaran', [
                'user' => $user,
                'tahunAjaranList' => collect(),
                'selectedTahunAjaran' => null,
                'tagihans' => collect(),
                'selectedTagihan' => null,
                'totalTagihan' => 0,
                'belumBayar' => 0,
                'menungguVerifikasi' => 0,
                'lunas' => 0
            ]);
        }
        
        // Get kartu pembayaran for selected tahun ajaran
        $kartuPembayaran = KartuPembayaran::where('user_id', $user->id)
            ->where('tahun_ajaran_id', $selectedTahunAjaran->id)
            ->first();
            
        $tagihans = collect();
        $selectedTagihan = null;
        
        if ($kartuPembayaran) {
            // Get all tagihan for this kartu
            $tagihans = Tagihan::where('kartu_id', $kartuPembayaran->id)
                ->with(['tagihanDetails.jenisTagihan.kategori', 'pembayaran'])
                ->orderBy('created_at', 'desc')
                ->get();
                
            // Get selected tagihan if ID provided
            if ($request->filled('id')) {
                $selectedTagihan = $tagihans->where('id', $request->id)->first();
            }
        }
        
        // Calculate statistics
        $totalTagihan = $tagihans->count();
        $belumBayar = $tagihans->where('status', Tagihan::STATUS_BELUM_BAYAR)->count();
        $menungguVerifikasi = $tagihans->where('status', Tagihan::STATUS_MENUNGGU_VERIFIKASI)->count();
        $lunas = $tagihans->where('status', Tagihan::STATUS_LUNAS)->count();
        
        return view('santri.tagihan-pembayaran', compact(
            'user',
            'tahunAjaranList', 
            'selectedTahunAjaran',
            'tagihans',
            'selectedTagihan',
            'totalTagihan',
            'belumBayar',
            'menungguVerifikasi', 
            'lunas'
        ));
    }

    public function uploadPembayaran($tagihanId)
    {
        $user = Auth::user();
        $tagihan = Tagihan::whereHas('kartuPembayaran', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with(['tagihanDetails.jenisTagihan', 'kartuPembayaran.tahunAjaran'])
        ->findOrFail($tagihanId);

        // Check if tagihan can be paid
        if ($tagihan->status === Tagihan::STATUS_LUNAS) {
            return redirect()->route('santri.show-tagihan', $tagihan->id)
                ->with('error', 'Tagihan ini sudah lunas');
        }

        return view('santri.upload-pembayaran', compact('tagihan', 'user'));
    }

    public function storeUploadPembayaran(Request $request, $tagihanId)
    {
        // Debug info
        \Log::info('Upload pembayaran method called', [
            'tagihan_id' => $tagihanId,
            'request_data' => $request->all(),
            'has_file' => $request->hasFile('bukti_pembayaran'),
            'file_info' => $request->hasFile('bukti_pembayaran') ? [
                'name' => $request->file('bukti_pembayaran')->getClientOriginalName(),
                'size' => $request->file('bukti_pembayaran')->getSize(),
                'mime' => $request->file('bukti_pembayaran')->getMimeType()
            ] : null
        ]);

        try {
            $request->validate([
                'tanggal_bayar' => 'required|date',
                'jumlah_bayar' => 'required|numeric|min:1',
                'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed', ['errors' => $e->errors()]);
            throw $e;
        }

        $user = Auth::user();
        $tagihan = Tagihan::whereHas('kartuPembayaran', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->findOrFail($tagihanId);

        // Check if tagihan can be paid
        if ($tagihan->status === Tagihan::STATUS_LUNAS) {
            return redirect()->route('santri.show-tagihan', $tagihan->id)
                ->with('error', 'Tagihan ini sudah lunas');
        }

        // Upload bukti pembayaran
        $buktiPath = null;
        if ($request->hasFile('bukti_pembayaran')) {
            $file = $request->file('bukti_pembayaran');
            $fileName = 'bukti_' . $tagihan->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $buktiPath = $file->storeAs('bukti-pembayaran', $fileName, 'public');
        }

        // Create pembayaran record
        $pembayaran = Pembayaran::create([
            'tagihan_id' => $tagihan->id,
            'tanggal_bayar' => $request->tanggal_bayar,
            'jumlah_bayar' => $request->jumlah_bayar,
            'bukti_pembayaran' => $buktiPath,
            'status' => Pembayaran::STATUS_MENUNGGU_VERIFIKASI
        ]);

        // Update tagihan status
        $tagihan->update(['status' => Tagihan::STATUS_MENUNGGU_VERIFIKASI]);

        return redirect()->route('santri.tagihan-pembayaran', ['id' => $tagihan->id])
            ->with('success', 'Bukti pembayaran berhasil diupload! Menunggu verifikasi dari bendahara.');
    }

    public function showTagihan($id)
    {
        $user = Auth::user();
        $tagihan = Tagihan::whereHas('kartuPembayaran', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })->with([
            'tagihanDetails.jenisTagihan',
            'kartuPembayaran.tahunAjaran',
            'pembayaran' => function($q) {
                $q->orderBy('created_at', 'desc');
            }
        ])->findOrFail($id);

        return view('santri.show-tagihan', compact('tagihan', 'user'));
    }

    public function riwayatPembayaran(Request $request)
    {
        $user = auth()->user();
        
        $query = Pembayaran::whereHas('tagihan', function ($query) use ($user) {
            $query->whereHas('kartuPembayaran', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        })->with(['tagihan.kartuPembayaran.user', 'tagihan.tagihanDetails.jenisTagihan']);

        // Apply filters
        if ($request->filled('bulan')) {
            $query->whereMonth('tanggal_bayar', $request->bulan);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pembayaran = $query->orderBy('created_at', 'desc')->paginate(10);

        // Get statistics
        $allPembayaran = Pembayaran::whereHas('tagihan', function ($query) use ($user) {
            $query->whereHas('kartuPembayaran', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        });

        $totalPembayaran = $allPembayaran->count();
        $menungguVerifikasi = $allPembayaran->where('status', 'menunggu_verifikasi')->count();
        $diterima = $allPembayaran->where('status', 'diterima')->count();
        $ditolak = $allPembayaran->where('status', 'ditolak')->count();

        return view('santri.riwayat-pembayaran', compact(
            'pembayaran', 'totalPembayaran', 'menungguVerifikasi', 'diterima', 'ditolak'
        ));
    }

    public function kartuPembayaran(Request $request)
    {
        $user = Auth::user();
        
        // Cek apakah user sudah memiliki tahun ajaran masuk
        if (!$user->tahun_ajaran_masuk_id) {
            return view('santri.kartu-pembayaran', [
                'user' => $user,
                'error' => 'Tahun ajaran masuk belum diset. Silakan hubungi admin.',
                'tahunAjaran' => null,
                'allTahunAjaran' => collect(),
                'kategoriTagihan' => collect(),
                'pembayaranData' => []
            ]);
        }
        
        // Get tahun ajaran yang bisa diakses santri (dari tahun masuknya hingga sekarang)
        $allTahunAjaran = TahunAjaran::where('id', '>=', $user->tahun_ajaran_masuk_id)
            ->orderBy('nama', 'desc')->get();
        
        // Get selected tahun ajaran or default to active one
        $selectedTahunAjaranId = $request->get('tahun_ajaran_id');
        if ($selectedTahunAjaranId) {
            $tahunAjaran = TahunAjaran::where('id', $selectedTahunAjaranId)
                ->where('id', '>=', $user->tahun_ajaran_masuk_id)
                ->first();
        } else {
            $tahunAjaran = TahunAjaran::where('is_active', true)
                ->where('id', '>=', $user->tahun_ajaran_masuk_id)
                ->first();
        }
        
        if (!$tahunAjaran) {
            return view('santri.kartu-pembayaran')->with('error', 'Tahun ajaran tidak ditemukan atau tidak dapat diakses');
        }
        
        // Get kartu pembayaran for selected year
        $kartuPembayaran = KartuPembayaran::where('user_id', $user->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->with('tahunAjaran')
            ->first();
            
        // Get all kategori tagihan
        $kategoriTagihan = KategoriTagihan::with(['jenisTagihan'])->get();
        
        // Get pembayaran data for this year
        $pembayaranData = [];
        if ($kartuPembayaran) {
            $tagihans = Tagihan::where('kartu_id', $kartuPembayaran->id)
                ->with(['tagihanDetails.jenisTagihan.kategori', 'pembayaranTerakhir'])
                ->get();
                
            foreach ($tagihans as $tagihan) {
                foreach ($tagihan->tagihanDetails as $detail) {
                    $bulan = $detail->bulan;
                    $kategoriId = $detail->jenisTagihan->kategori->id;
                    
                    if (!isset($pembayaranData[$bulan])) {
                        $pembayaranData[$bulan] = [];
                    }
                    
                    $pembayaranData[$bulan][$kategoriId] = [
                        'sudah_bayar' => $tagihan->status === Tagihan::STATUS_LUNAS,
                        'jumlah' => $detail->jumlah,
                        'tanggal_bayar' => $tagihan->pembayaranTerakhir?->tanggal_bayar
                    ];
                }
            }
        }

        return view('santri.kartu-pembayaran', compact(
            'user', 'kartuPembayaran', 'tahunAjaran', 'allTahunAjaran', 
            'kategoriTagihan', 'pembayaranData'
        ));
    }
}