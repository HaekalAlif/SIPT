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
use App\Models\JenisTagihanDisabled;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Collection;

class SantriController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        $userHasTahunAjaran = $user->tahun_ajaran_masuk_id !== null;

        if (!$tahunAjaranAktif) {
            return view('santri.dashboard', [
                'user'                 => $user,
                'tahunAjaranAktif'     => null,
                'kartuPembayaran'      => null,
                'tagihansTahunAktif'   => collect(),
                'activTagihans'        => collect(),
                'totalTagihanNominal'  => 0,
                'totalSudahDibayar'    => 0,
                'tagihanPerKategori'   => [],
                'belumBayar'           => 0,
                'menungguVerifikasi'   => 0,
                'lunas'                => 0,
                'recentTagihan'        => collect(),
                'totalPembayaran'      => 0,
                'userHasTahunAjaran'   => $userHasTahunAjaran,
            ]);
        }

        // Ambil atau buat kartu pembayaran untuk tahun ajaran aktif
        $kartuPembayaran = KartuPembayaran::firstOrCreate([
            'user_id'         => $user->id,
            'tahun_ajaran_id' => $tahunAjaranAktif->id,
        ], [
            'nomor_kartu' => 'KP-' . str_replace('/', '', $tahunAjaranAktif->nama) . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
        ]);

        // Semua tagihan di tahun ajaran aktif
        $tagihansTahunAktif = Tagihan::where('kartu_id', $kartuPembayaran->id)
            ->with(['tagihanDetails.jenisTagihan.kategori', 'pembayaran'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Tagihan yang belum selesai (perlu perhatian santri)
        $activTagihans = $tagihansTahunAktif->whereIn('status', [
            Tagihan::STATUS_BELUM_BAYAR,
            Tagihan::STATUS_MENUNGGU_VERIFIKASI,
        ]);

        $belumBayar         = $tagihansTahunAktif->where('status', Tagihan::STATUS_BELUM_BAYAR)->count();
        $menungguVerifikasi = $tagihansTahunAktif->where('status', Tagihan::STATUS_MENUNGGU_VERIFIKASI)->count();
        $lunas              = $tagihansTahunAktif->where('status', Tagihan::STATUS_LUNAS)->count();
        $recentTagihan      = $activTagihans->take(3);

        $totalPembayaran = Pembayaran::whereHas('tagihan', function ($q) use ($kartuPembayaran) {
            $q->where('kartu_id', $kartuPembayaran->id);
        })->where('status', 'diterima')->sum('jumlah_bayar');

        // ─── Total tagihan: TEORITIS (dari JenisTagihan) & aktual yang sudah dibayar ───
        $totalTagihanNominal = 0;
        $totalSudahDibayar   = 0;
        $tagihanPerKategori  = [];

        if ($userHasTahunAjaran) {
            $tahunAjaranMasuk = TahunAjaran::find($user->tahun_ajaran_masuk_id);

            if ($tahunAjaranMasuk) {
                $allTahunAjaran = TahunAjaran::where('id', '>=', $tahunAjaranMasuk->id)
                    ->orderBy('id')
                    ->get();
                $yearCount = $allTahunAjaran->count();

                // 1. Hitung total TEORITIS berdasarkan JenisTagihan dan aturan per kategori
                $allKategori = KategoriTagihan::with([
                    'jenisTagihan' => fn($q) => $q->applicableForUser($user),
                ])->get();

                foreach ($allKategori as $kategori) {
                    $katNama      = strtolower($kategori->nama);
                    $isRegistrasi = str_contains($katNama, 'registrasi');
                    $isSyariah    = str_contains($katNama, 'syariah');

                    $katTotal = 0;
                    foreach ($kategori->jenisTagihan as $jenis) {
                        if ($isRegistrasi) {
                            // Registrasi: 1x seumur hidup
                            $katTotal += $jenis->nominal;
                        } elseif ($isSyariah && $jenis->is_bulanan) {
                            // Syariah bulanan: 12 bulan × jumlah tahun
                            $katTotal += $jenis->nominal * 12 * $yearCount;
                        } else {
                            // Syariah non-bulanan & Lainnya: 1x per tahun
                            $katTotal += $jenis->nominal * $yearCount;
                        }
                    }

                    if ($katTotal <= 0) continue;

                    $totalTagihanNominal += $katTotal;
                    $tagihanPerKategori[$katNama] = [
                        'total_nominal' => $katTotal,
                        'sudah_dibayar' => 0,
                        'sisa'          => $katTotal,
                        'tagihan_count' => 0,
                        'lunas_count'   => 0,
                    ];
                }

                // 2. Ambil aktual yang sudah LUNAS dari record Tagihan nyata
                $allUserTagihan = Tagihan::whereHas('kartuPembayaran', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })->with(['tagihanDetails.jenisTagihan.kategori'])->get();

                foreach ($allUserTagihan as $t) {
                    $katNama = strtolower(
                        $t->tagihanDetails->first()?->jenisTagihan?->kategori?->nama ?? ''
                    );

                    if (isset($tagihanPerKategori[$katNama])) {
                        $tagihanPerKategori[$katNama]['tagihan_count']++;
                        if ($t->status === Tagihan::STATUS_LUNAS) {
                            $totalSudahDibayar += $t->total;
                            $tagihanPerKategori[$katNama]['sudah_dibayar'] += $t->total;
                            $tagihanPerKategori[$katNama]['lunas_count']++;
                        }
                    }
                }

                // 3. Hitung sisa per kategori
                foreach ($tagihanPerKategori as &$data) {
                    $data['sisa'] = max(0, $data['total_nominal'] - $data['sudah_dibayar']);
                }
                unset($data);
            }
        }

        $totalSisa = max(0, $totalTagihanNominal - $totalSudahDibayar);

        return view('santri.dashboard', compact(
            'user',
            'tahunAjaranAktif',
            'kartuPembayaran',
            'tagihansTahunAktif',
            'activTagihans',
            'totalTagihanNominal',
            'totalSudahDibayar',
            'totalSisa',
            'tagihanPerKategori',
            'belumBayar',
            'menungguVerifikasi',
            'lunas',
            'recentTagihan',
            'totalPembayaran',
            'userHasTahunAjaran'
        ));
    }

    public function formBuatTagihan(Request $request)
    {
        $user = Auth::user();

        if (!$user->tahun_ajaran_masuk_id) {
            return redirect()->route('santri.dashboard')
                ->with('error', 'Tahun ajaran Anda belum diatur. Silakan hubungi admin.');
        }

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaranAktif) {
            return redirect()->route('santri.dashboard')
                ->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $kategoriInput = strtolower($request->get('kategori', 'registrasi'));

        $kategori = KategoriTagihan::with([
                'jenisTagihan' => fn($q) => $q->applicableForUser($user),
            ])
            ->whereRaw('LOWER(nama) LIKE ?', ['%' . $kategoriInput . '%'])
            ->first();

        if (!$kategori) {
            return redirect()->route('santri.dashboard')
                ->with('error', 'Kategori tagihan tidak ditemukan.');
        }

        /** @var \App\Models\KategoriTagihan $kategori */

        $kartuPembayaran = KartuPembayaran::firstOrCreate(
            ['user_id' => $user->id, 'tahun_ajaran_id' => $tahunAjaranAktif->id],
            ['nomor_kartu' => 'KP-' . str_replace('/', '', $tahunAjaranAktif->nama) . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT)]
        );

        $isRegistrasi = str_contains(strtolower($kategori->nama), 'registrasi');

        // Cari detail yang sudah dibayar (ada di TagihanDetail yang sudah pernah dibuat)
        $paidQuery = TagihanDetail::whereHas('jenisTagihan', fn($q) => $q->where('kategori_id', $kategori->id));

        if ($isRegistrasi) {
            // Registrasi: cek seluruh history user
            $paidQuery->whereHas('tagihan.kartuPembayaran', fn($q) => $q->where('user_id', $user->id));
        } else {
            // Syariah/Lainnya: cek hanya tahun aktif
            $paidQuery->whereHas('tagihan', fn($q) => $q->where('kartu_id', $kartuPembayaran->id));
        }

        $paidDetails = $paidQuery->get();
        $paidJenisIds = $paidDetails->pluck('jenis_tagihan_id')->unique()->values()->toArray();
        $paidBulanMap = [];
        foreach ($paidDetails as $d) {
            if ($d->bulan) {
                $paidBulanMap[$d->jenis_tagihan_id][] = $d->bulan;
            }
        }

        // Jenis tagihan yang dinonaktifkan admin untuk santri ini + tahun ajaran aktif
        $disabledJenisIds = JenisTagihanDisabled::getDisabledIds($tahunAjaranAktif->id, $user->id);

        // Filter jenisTagihan di kategori agar yang disabled tidak tampil,
        // lalu pilih item paling relevan jika ada versi general + versi khusus.
        $filteredJenisTagihan = $kategori->jenisTagihan
            ->filter(fn($j) => !in_array($j->id, $disabledJenisIds))
            ->values();

        $kategori->setRelation('jenisTagihan', $this->pickPreferredJenisTagihan($filteredJenisTagihan, $user));

        $kategoriNama = $kategoriInput;

        return view('santri.buat-tagihan', compact(
            'user', 'tahunAjaranAktif', 'kategori', 'kategoriNama',
            'kartuPembayaran', 'paidJenisIds', 'paidBulanMap', 'disabledJenisIds'
        ));
    }

    public function storeTagihan(Request $request)
    {
        $user = Auth::user();

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->first();
        if (!$tahunAjaranAktif) {
            return back()->with('error', 'Tidak ada tahun ajaran aktif.');
        }

        $kartu = KartuPembayaran::firstOrCreate(
            ['user_id' => $user->id, 'tahun_ajaran_id' => $tahunAjaranAktif->id],
            ['nomor_kartu' => 'KP-' . str_replace('/', '', $tahunAjaranAktif->nama) . '-' . str_pad($user->id, 4, '0', STR_PAD_LEFT)]
        );

        // Ambil jenis tagihan yang dinonaktifkan admin untuk santri ini
        $disabledJenisIds = JenisTagihanDisabled::getDisabledIds($tahunAjaranAktif->id, $user->id);
        $allowedJenisIds = JenisTagihan::applicableForUser($user)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        // syariah_items[jenisId][Bulan] = "1"
        $syariahItems    = $request->input('syariah_items', []);
        // jenis_tagihan_ids[] untuk registrasi & lainnya
        $jenisTagihanIds = $request->input('jenis_tagihan_ids', []);

        // Strip out any disabled/unavailable ids (server-side protection)
        $jenisTagihanIds = array_filter($jenisTagihanIds, function ($id) use ($disabledJenisIds, $allowedJenisIds) {
            $id = (int) $id;
            return !in_array($id, $disabledJenisIds) && in_array($id, $allowedJenisIds);
        });
        $syariahItems = array_filter($syariahItems, function ($id) use ($disabledJenisIds, $allowedJenisIds) {
            $id = (int) $id;
            return !in_array($id, $disabledJenisIds) && in_array($id, $allowedJenisIds);
        }, ARRAY_FILTER_USE_KEY);

        if (empty($syariahItems) && empty($jenisTagihanIds)) {
            return back()->with('error', 'Pilih minimal satu jenis tagihan.');
        }

        $details = [];
        $total   = 0;

        // Syariah (bulanan) – cek duplikat per jenis+bulan di kartu ini
        foreach ($syariahItems as $jenisId => $bulanMap) {
            $jenis = JenisTagihan::find($jenisId);
            if (!$jenis) continue;
            foreach ($bulanMap as $bulan => $checked) {
                $exists = TagihanDetail::whereHas('tagihan', fn($q) => $q->where('kartu_id', $kartu->id))
                    ->where('jenis_tagihan_id', $jenisId)
                    ->where('bulan', $bulan)
                    ->exists();
                if (!$exists) {
                    $details[] = ['jenis_id' => $jenisId, 'bulan' => $bulan, 'nominal' => $jenis->nominal];
                    $total += $jenis->nominal;
                }
            }
        }

        // Registrasi & Lainnya – cek duplikat berdasarkan aturan kategori
        foreach ($jenisTagihanIds as $jenisId) {
            $jenis = JenisTagihan::with('kategori')->find($jenisId);
            if (!$jenis) continue;
            $isRegistrasi = str_contains(strtolower($jenis->kategori?->nama ?? ''), 'registrasi');
            if ($isRegistrasi) {
                // Cek seluruh history user
                $exists = TagihanDetail::whereHas('tagihan.kartuPembayaran', fn($q) => $q->where('user_id', $user->id))
                    ->where('jenis_tagihan_id', $jenisId)
                    ->exists();
            } else {
                // Cek hanya tahun aktif
                $exists = TagihanDetail::whereHas('tagihan', fn($q) => $q->where('kartu_id', $kartu->id))
                    ->where('jenis_tagihan_id', $jenisId)
                    ->exists();
            }
            if (!$exists) {
                $details[] = ['jenis_id' => $jenisId, 'bulan' => null, 'nominal' => $jenis->nominal];
                $total += $jenis->nominal;
            }
        }

        if (empty($details)) {
            return back()->with('info', 'Semua tagihan yang dipilih sudah pernah dibuat atau sedang diproses.');
        }

        DB::transaction(function () use ($kartu, $details, $total) {
            $tagihan = Tagihan::create([
                'kartu_id' => $kartu->id,
                'total'    => $total,
                'status'   => Tagihan::STATUS_BELUM_BAYAR,
            ]);

            foreach ($details as $d) {
                TagihanDetail::create([
                    'tagihan_id'       => $tagihan->id,
                    'jenis_tagihan_id' => $d['jenis_id'],
                    'bulan'            => $d['bulan'],
                    'nominal'          => $d['nominal'],
                    'status'           => TagihanDetail::STATUS_BELUM_BAYAR,
                ]);
            }
        });

        return redirect()->route('santri.tagihan-pembayaran')
            ->with('success', 'Tagihan berhasil dibuat! Silakan upload bukti pembayaran.');
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
                'user'                => $user,
                'tahunAjaranList'     => collect(),
                'selectedTahunAjaran' => null,
                'tagihans'            => collect(),
                'selectedTagihan'     => null,
                'filterKategori'      => null,
                'totalTagihan'        => 0,
                'belumBayar'          => 0,
                'menungguVerifikasi'  => 0,
                'lunas'               => 0,
            ]);
        }

        // Get kartu pembayaran for selected tahun ajaran
        $kartuPembayaran = KartuPembayaran::where('user_id', $user->id)
            ->where('tahun_ajaran_id', $selectedTahunAjaran->id)
            ->first();

        $tagihans        = collect();
        $selectedTagihan = null;
        $filterKategori  = $request->get('kategori'); // 'registrasi', 'syariah', 'lainnya', atau null

        if ($kartuPembayaran) {
            $query = Tagihan::where('kartu_id', $kartuPembayaran->id)
                ->with(['tagihanDetails.jenisTagihan.kategori', 'pembayaran'])
                ->orderBy('created_at', 'desc');

            $tagihans = $query->get();

            // Filter by kategori jika diminta
            if ($filterKategori) {
                $tagihans = $tagihans->filter(function ($t) use ($filterKategori) {
                    $katNama = strtolower(
                        $t->tagihanDetails->first()?->jenisTagihan?->kategori?->nama ?? ''
                    );
                    return str_contains($katNama, strtolower($filterKategori));
                });
            }

            // Get selected tagihan if ID provided
            if ($request->filled('id')) {
                $selectedTagihan = Tagihan::where('kartu_id', $kartuPembayaran->id)
                    ->with(['tagihanDetails.jenisTagihan.kategori', 'pembayaran'])
                    ->find($request->id);
            }
        }

        $totalTagihan       = $tagihans->count();
        $belumBayar         = $tagihans->where('status', Tagihan::STATUS_BELUM_BAYAR)->count();
        $menungguVerifikasi = $tagihans->where('status', Tagihan::STATUS_MENUNGGU_VERIFIKASI)->count();
        $lunas              = $tagihans->where('status', Tagihan::STATUS_LUNAS)->count();

        return view('santri.tagihan-pembayaran', compact(
            'user',
            'tahunAjaranList',
            'selectedTahunAjaran',
            'tagihans',
            'selectedTagihan',
            'filterKategori',
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
        $data = $this->buildKartuPembayaranViewData($request, Auth::user());

        return view('santri.kartu-pembayaran', $data);
    }

    public function downloadKartuPembayaranPdf(Request $request)
    {
        $data = $this->buildKartuPembayaranViewData($request, Auth::user());

        if (isset($data['error']) || empty($data['kartuPembayaran'])) {
            return redirect()->route('santri.kartu-pembayaran', $request->only('tahun_ajaran_id'))
                ->with('error', $data['error'] ?? 'Kartu pembayaran belum tersedia.');
        }

        $options = new Options();
        $options->set('defaultFont', 'sans-serif');
        $options->set('isHtml5ParserEnabled', true);
        $options->set('isRemoteEnabled', true);

        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('santri.pdf.kartu-pembayaran', $data)->render());
        $pdf->setPaper('a4', 'landscape');
        $pdf->render();

        $filename = 'kartu-pembayaran-' . ($data['user']->nama_santri ?? 'santri') . '-' . ($data['tahunAjaran']->nama ?? 'tahun') . '.pdf';

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . str_replace([' ', '/'], ['-', '-'], $filename) . '"',
        ]);
    }

    private function buildKartuPembayaranViewData(Request $request, User $user): array
    {
        if (!$user->tahun_ajaran_masuk_id) {
            return [
                'user'                  => $user,
                'error'                 => 'Tahun ajaran masuk belum diset. Silakan hubungi admin.',
                'tahunAjaran'           => null,
                'allTahunAjaran'        => collect(),
                'kartuPembayaran'       => null,
                'syariahPembayaran'     => [],
                'nonSyariahPembayaran'  => [],
            ];
        }

        $allTahunAjaran = TahunAjaran::where('id', '>=', $user->tahun_ajaran_masuk_id)
            ->orderBy('nama', 'desc')->get();

        $selectedId = $request->get('tahun_ajaran_id');
        if ($selectedId) {
            $tahunAjaran = TahunAjaran::where('id', $selectedId)
                ->where('id', '>=', $user->tahun_ajaran_masuk_id)->first();
        } else {
            $tahunAjaran = TahunAjaran::where('is_active', true)
                ->where('id', '>=', $user->tahun_ajaran_masuk_id)->first()
                ?? $allTahunAjaran->first();
        }

        if (!$tahunAjaran) {
            return [
                'user'                  => $user,
                'error'                 => 'Tahun ajaran tidak ditemukan.',
                'tahunAjaran'           => null,
                'allTahunAjaran'        => $allTahunAjaran,
                'kartuPembayaran'       => null,
                'syariahPembayaran'     => [],
                'nonSyariahPembayaran'  => [],
            ];
        }

        $kartuPembayaran      = KartuPembayaran::where('user_id', $user->id)
            ->where('tahun_ajaran_id', $tahunAjaran->id)
            ->first();

        $syariahPembayaran    = [];
        $nonSyariahPembayaran = [];

        if ($kartuPembayaran) {
            $tagihans = Tagihan::where('kartu_id', $kartuPembayaran->id)
                ->with([
                    'tagihanDetails.jenisTagihan.kategori',
                    'pembayaran' => fn($q) => $q->where('status', 'diterima')->latest('verified_at'),
                ])
                ->get();

            foreach ($tagihans as $tagihan) {
                $isLunas           = $tagihan->status === Tagihan::STATUS_LUNAS;
                $acceptedPembayaran = $tagihan->pembayaran->first();
                // Use verified_at if available, fall back to tanggal_bayar
                $tanggalVerif = null;
                if ($acceptedPembayaran) {
                    $raw = $acceptedPembayaran->verified_at ?? $acceptedPembayaran->tanggal_bayar;
                    $tanggalVerif = $raw ? \Carbon\Carbon::parse($raw)->format('d.m.Y') : null;
                }

                // Determine category from first detail
                $firstDetail = $tagihan->tagihanDetails->first();
                $kategori    = $firstDetail?->jenisTagihan?->kategori;
                if (!$kategori) continue;

                $katNama   = strtolower($kategori->nama);
                $isSyariah = str_contains($katNama, 'syariah') || str_contains($katNama, 'syahriyah');

                if ($isSyariah) {
                    foreach ($tagihan->tagihanDetails as $detail) {
                        $detailLunas = $isLunas || $detail->status === TagihanDetail::STATUS_LUNAS;
                        if ($detail->bulan) {
                            // Prefer data from lunas detail/tagihan for monthly card breakdown.
                            if (!isset($syariahPembayaran[$detail->bulan]) || $detailLunas) {
                                $syariahPembayaran[$detail->bulan] = [
                                    'nominal' => (float) $detail->nominal,
                                    'tanggal' => $tanggalVerif,
                                    'lunas'   => $detailLunas,
                                ];
                            }
                        }
                    }
                } else {
                    // For non-syariah: store per individual jenis tagihan name so that
                    // items belonging to the same broad category (e.g. 'Registrasi') are
                    // each stored independently and can be found by the card sections.
                    foreach ($tagihan->tagihanDetails as $detail) {
                        $detailLunas = $isLunas || $detail->status === TagihanDetail::STATUS_LUNAS;
                        $jenisNama = strtolower(trim($detail->jenisTagihan?->nama_tagihan ?? ''));
                        if (!$jenisNama) continue;

                        $existing = $nonSyariahPembayaran[$jenisNama] ?? null;
                        // Prefer lunas detail/tagihan record if multiple tagihans cover the same item.
                        if (!$existing || $detailLunas) {
                            $nonSyariahPembayaran[$jenisNama] = [
                                'label'   => strtoupper($detail->jenisTagihan->nama_tagihan),
                                'nominal' => (float) $detail->nominal,
                                'tanggal' => $tanggalVerif,
                                'lunas'   => $detailLunas,
                            ];
                        }
                    }
                }
            }
        }

        return compact(
            'user', 'kartuPembayaran', 'tahunAjaran', 'allTahunAjaran',
            'syariahPembayaran', 'nonSyariahPembayaran'
        );
    }

    private function pickPreferredJenisTagihan(Collection $jenisTagihan, User $user): Collection
    {
        $tingkatan = strtoupper(trim((string) ($user->tingkatan ?? '')));
        $ngaji = trim((string) ($user->tingkatan_ngaji ?? ''));

        return $jenisTagihan
            ->groupBy(fn($item) => $this->normalizeJenisTagihanKey($item->nama_tagihan ?? ''))
            ->map(function (Collection $group) use ($tingkatan, $ngaji) {
                return $group
                    ->sortByDesc(function ($item) use ($tingkatan, $ngaji) {
                        $scope = $item->target_scope ?? JenisTagihan::TARGET_SCOPE_ALL;
                        $value = (string) ($item->target_value ?? '');

                        if ($scope === JenisTagihan::TARGET_SCOPE_TINGKATAN && strtoupper($value) === $tingkatan) {
                            return 30;
                        }

                        if ($scope === JenisTagihan::TARGET_SCOPE_NGAJI && $value === $ngaji) {
                            return 20;
                        }

                        if ($scope === JenisTagihan::TARGET_SCOPE_ALL || $scope === null || $scope === '') {
                            return 10;
                        }

                        return 0;
                    })
                    ->first();
            })
            ->filter()
            ->values();
    }

    private function normalizeJenisTagihanKey(string $name): string
    {
        $normalized = strtolower(trim($name));
        $normalized = preg_replace('/\s*\(.*?\)\s*/', ' ', $normalized) ?? $normalized;
        $normalized = preg_replace('/\s+/', ' ', $normalized) ?? $normalized;

        return trim($normalized);
    }

    // ─────────────────────────────────────────────
    //  PROFIL
    // ─────────────────────────────────────────────
    public function profil()
    {
        $user = Auth::user();
        $tingkatanOptions = ['MI', 'MTs', 'MA', 'SMP', 'SMA', 'PT'];
        $kelasOptions     = ['1', '2', '3', '4', '5', '6'];
        $tingkatanNgajiOptions = ['Iqro 1', 'Iqro 2', 'Iqro 3', 'Iqro 4', 'Iqro 5', 'Iqro 6', 'Al-Quran'];
        return view('santri.profil', compact('user', 'tingkatanOptions', 'kelasOptions', 'tingkatanNgajiOptions'));
    }

    public function updateProfil(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'nama_santri'    => 'required|string|max:150',
            'nama_orang_tua' => 'nullable|string|max:150',
            'no_telp'        => 'nullable|string|max:20',
            'tempat_lahir'   => 'nullable|string|max:100',
            'tanggal_lahir'  => 'nullable|date',
            'jenis_kelamin'  => 'nullable|in:L,P',
            'alamat'         => 'nullable|string',
            'foto_profile'   => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // tingkatan, kelas, tingkatan_ngaji hanya bisa diubah oleh Admin — tidak diproses di sini
        $data = $request->only([
            'nama_santri', 'nama_orang_tua', 'no_telp',
            'tempat_lahir', 'tanggal_lahir', 'jenis_kelamin', 'alamat',
        ]);

        // Handle photo upload
        if ($request->hasFile('foto_profile')) {
            // Delete old photo if exists
            if ($user->foto_profile && Storage::disk('public')->exists($user->foto_profile)) {
                Storage::disk('public')->delete($user->foto_profile);
            }
            $path = $request->file('foto_profile')->store('foto_profile', 'public');
            $data['foto_profile'] = $path;
        }

        $user->update($data);

        return redirect()->route('santri.profil')->with('success', 'Profil berhasil diperbarui.');
    }
}