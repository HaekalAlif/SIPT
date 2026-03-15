<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\KartuPembayaran;
use App\Models\JenisTagihan;
use App\Models\Pembayaran;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\KategoriTagihan;
use App\Models\JenisTagihanDisabled;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

class TagihanController extends Controller
{
    public function manualPaymentIndex(Request $request)
    {
        $query = User::query()->where('role', User::ROLE_SANTRI);

        if ($request->filled('santri')) {
            $query->where(function ($q) use ($request) {
                $q->where('nama_santri', 'like', '%' . $request->santri . '%');
            });
        }

        if ($request->filled('tingkatan')) {
            $query->where('tingkatan', $request->tingkatan);
        }

        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        if ($request->filled('tingkatan_ngaji')) {
            $query->where('tingkatan_ngaji', $request->tingkatan_ngaji);
        }

        $santris = $query->orderBy('nama_santri')->paginate(20);

        $tingkatanOptions = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->distinct()
            ->orderBy('tingkatan')
            ->pluck('tingkatan');

        $kelasOptions = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('kelas')
            ->distinct()
            ->orderBy('kelas')
            ->pluck('kelas');

        $ngajiOptions = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan_ngaji')
            ->distinct()
            ->orderBy('tingkatan_ngaji')
            ->pluck('tingkatan_ngaji');

        $summaryByUser = $this->buildSantriFinancialSummary($santris->getCollection());

        return view('admin.tagihan.manual-payment-index', compact(
            'santris',
            'summaryByUser',
            'tingkatanOptions',
            'kelasOptions',
            'ngajiOptions'
        ));
    }

    public function manualPaymentShow($userId)
    {
        $user = User::where('role', User::ROLE_SANTRI)->findOrFail($userId);

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->orderByDesc('id')->first()
            ?? TahunAjaran::orderByDesc('id')->first();

        $disabledJenisIds = $tahunAjaranAktif
            ? JenisTagihanDisabled::getDisabledIds((int) $tahunAjaranAktif->id, (int) $user->id)
            : [];

        $kategoriTagihan = KategoriTagihan::with([
            'jenisTagihan' => function ($q) use ($user, $disabledJenisIds) {
                $q->applicableForUser($user)
                    ->when(!empty($disabledJenisIds), fn($sq) => $sq->whereNotIn('id', $disabledJenisIds))
                    ->orderBy('nama_tagihan');
            },
        ])->orderBy('nama')->get();

        $lunasDetails = TagihanDetail::with('jenisTagihan')
            ->whereHas('tagihan.kartuPembayaran', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->where('status', TagihanDetail::STATUS_LUNAS)
            ->get();

        $paidJenisIds = [];
        $paidBulanMap = [];

        foreach ($lunasDetails as $detail) {
            if (!$detail->jenis_tagihan_id) {
                continue;
            }

            if ($detail->bulan) {
                if (!isset($paidBulanMap[$detail->jenis_tagihan_id])) {
                    $paidBulanMap[$detail->jenis_tagihan_id] = [];
                }
                if (!in_array($detail->bulan, $paidBulanMap[$detail->jenis_tagihan_id], true)) {
                    $paidBulanMap[$detail->jenis_tagihan_id][] = $detail->bulan;
                }
            } else {
                $paidJenisIds[] = (int) $detail->jenis_tagihan_id;
            }
        }

        $paidJenisIds = array_values(array_unique($paidJenisIds));

        $pembayarans = Pembayaran::with('tagihan')
            ->whereHas('tagihan.kartuPembayaran', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->where('status', Pembayaran::STATUS_DITERIMA)
            ->latest('tanggal_bayar')
            ->get();

        $summary = $this->buildSantriFinancialSummary(collect([$user]))[$user->id] ?? [
            'total_tagihan_nominal' => 0,
            'total_sudah_dibayar' => 0,
            'total_sisa' => 0,
        ];

        return view('admin.tagihan.manual-payment-show', compact(
            'user',
            'pembayarans',
            'summary',
            'kategoriTagihan',
            'paidJenisIds',
            'paidBulanMap',
            'tahunAjaranAktif'
        ));
    }

    public function storeBulkManualPayment(Request $request, $userId)
    {
        $user = User::where('role', User::ROLE_SANTRI)->findOrFail($userId);

        $tahunAjaranAktif = TahunAjaran::where('is_active', true)->orderByDesc('id')->first()
            ?? TahunAjaran::orderByDesc('id')->first();

        if (!$tahunAjaranAktif) {
            return back()->with('error', 'Tahun ajaran aktif belum tersedia.');
        }

        $request->validate([
            'tanggal_bayar' => 'required|date',
            'selected_items' => 'required|array|min:1',
            'selected_items.*' => 'string',
            'catatan' => 'nullable|string|max:500',
        ]);

        try {
            DB::transaction(function () use ($request, $user, $tahunAjaranAktif) {
                $selectedItems = collect($request->selected_items)
                    ->map(fn($value) => trim((string) $value))
                    ->filter()
                    ->unique()
                    ->values();

                if ($selectedItems->isEmpty()) {
                    throw new \RuntimeException('Tidak ada item yang dipilih.');
                }

                $disabledJenisIds = JenisTagihanDisabled::getDisabledIds((int) $tahunAjaranAktif->id, (int) $user->id);
                $allowedJenisIds = JenisTagihan::applicableForUser($user)
                    ->when(!empty($disabledJenisIds), fn($q) => $q->whereNotIn('id', $disabledJenisIds))
                    ->pluck('id')
                    ->map(fn($id) => (int) $id)
                    ->all();

                $kartu = KartuPembayaran::firstOrCreate(
                    [
                        'user_id' => $user->id,
                        'tahun_ajaran_id' => $tahunAjaranAktif->id,
                    ],
                    [
                        'nomor_kartu' => 'KP-' . str_replace('/', '', (string) $tahunAjaranAktif->nama) . '-' . str_pad((string) $user->id, 4, '0', STR_PAD_LEFT),
                    ]
                );

                $detailsByTagihan = [];

                foreach ($selectedItems as $item) {
                    $parts = explode('|', $item, 2);
                    $jenisId = (int) ($parts[0] ?? 0);
                    $bulan = isset($parts[1]) && $parts[1] !== '-' ? $parts[1] : null;

                    if ($jenisId <= 0 || !in_array($jenisId, $allowedJenisIds, true)) {
                        continue;
                    }

                    $jenis = JenisTagihan::find($jenisId);
                    if (!$jenis) {
                        continue;
                    }

                    $alreadyPaid = TagihanDetail::whereHas('tagihan.kartuPembayaran', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                        ->where('jenis_tagihan_id', $jenisId)
                        ->where('status', TagihanDetail::STATUS_LUNAS)
                        ->when($bulan !== null, fn($q) => $q->where('bulan', $bulan), fn($q) => $q->whereNull('bulan'))
                        ->exists();

                    if ($alreadyPaid) {
                        continue;
                    }

                    $detail = TagihanDetail::whereHas('tagihan.kartuPembayaran', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    })
                        ->where('jenis_tagihan_id', $jenisId)
                        ->where('status', TagihanDetail::STATUS_BELUM_BAYAR)
                        ->when($bulan !== null, fn($q) => $q->where('bulan', $bulan), fn($q) => $q->whereNull('bulan'))
                        ->with('tagihan')
                        ->first();

                    if (!$detail) {
                        $tagihan = Tagihan::create([
                            'kartu_id' => $kartu->id,
                            'total' => 0,
                            'status' => Tagihan::STATUS_BELUM_BAYAR,
                        ]);

                        $detail = TagihanDetail::create([
                            'tagihan_id' => $tagihan->id,
                            'jenis_tagihan_id' => $jenis->id,
                            'bulan' => $bulan,
                            'nominal' => (float) $jenis->nominal,
                            'status' => TagihanDetail::STATUS_BELUM_BAYAR,
                        ]);

                        $tagihan->update(['total' => (float) $tagihan->total + (float) $jenis->nominal]);
                    }

                    $detailsByTagihan[$detail->tagihan_id][] = $detail;
                }

                if (empty($detailsByTagihan)) {
                    throw new \RuntimeException('Item terpilih sudah lunas atau tidak valid.');
                }

                foreach ($detailsByTagihan as $tagihanId => $detailItems) {
                    $tagihan = Tagihan::findOrFail((int) $tagihanId);
                    $detailCollection = collect($detailItems);
                    $jumlahBayar = (float) $detailCollection->sum('nominal');

                    Pembayaran::create([
                        'tagihan_id' => $tagihan->id,
                        'tanggal_bayar' => $request->tanggal_bayar,
                        'jumlah_bayar' => $jumlahBayar,
                        'bukti_pembayaran' => null,
                        'catatan' => $request->catatan ?: 'Pembayaran manual checklist admin',
                        'status' => Pembayaran::STATUS_DITERIMA,
                        'verified_by' => Auth::id(),
                        'verified_at' => now(),
                        'catatan_verifikator' => 'Input manual checklist oleh admin',
                    ]);

                    TagihanDetail::whereIn('id', $detailCollection->pluck('id')->all())
                        ->update(['status' => TagihanDetail::STATUS_LUNAS]);

                    $remaining = $tagihan->tagihanDetails()
                        ->where('status', TagihanDetail::STATUS_BELUM_BAYAR)
                        ->count();

                    $tagihan->update([
                        'status' => $remaining === 0 ? Tagihan::STATUS_LUNAS : Tagihan::STATUS_BELUM_BAYAR,
                    ]);
                }
            });
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.manual-payment.show', $user->id)
            ->with('success', 'Tagihan terpilih berhasil dibuat (jika belum ada) dan langsung dilunasi.');
    }

    private function buildSantriFinancialSummary(Collection $users): array
    {
        $summary = [];

        foreach ($users as $user) {
            if (!$user?->id) {
                continue;
            }

            $totalTagihanNominal = 0;
            $totalSudahDibayar = 0;

            if ($user->tahun_ajaran_masuk_id) {
                $tahunAjaranMasuk = TahunAjaran::find($user->tahun_ajaran_masuk_id);
                if ($tahunAjaranMasuk) {
                    $yearCount = TahunAjaran::where('id', '>=', $tahunAjaranMasuk->id)->count();

                    $allKategori = KategoriTagihan::with([
                        'jenisTagihan' => fn($q) => $q->applicableForUser($user),
                    ])->get();

                    foreach ($allKategori as $kategori) {
                        $katNama = strtolower($kategori->nama);
                        $isRegistrasi = str_contains($katNama, 'registrasi');
                        $isSyariah = str_contains($katNama, 'syariah');

                        foreach ($kategori->jenisTagihan as $jenis) {
                            if ($isRegistrasi) {
                                $totalTagihanNominal += (float) $jenis->nominal;
                            } elseif ($isSyariah && $jenis->is_bulanan) {
                                $totalTagihanNominal += (float) $jenis->nominal * 12 * $yearCount;
                            } else {
                                $totalTagihanNominal += (float) $jenis->nominal * $yearCount;
                            }
                        }
                    }
                }
            }

            $allUserTagihan = Tagihan::whereHas('kartuPembayaran', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })->get();

            foreach ($allUserTagihan as $t) {
                if ($t->status === Tagihan::STATUS_LUNAS) {
                    $totalSudahDibayar += (float) $t->total;
                }
            }

            $summary[$user->id] = [
                'total_tagihan_nominal' => $totalTagihanNominal,
                'total_sudah_dibayar' => $totalSudahDibayar,
                'total_sisa' => max(0, $totalTagihanNominal - $totalSudahDibayar),
            ];
        }

        return $summary;
    }

    public function index(Request $request)
    {
        $query = Tagihan::with(['kartuPembayaran.user', 'kartuPembayaran.tahunAjaran']);

        // Filter berdasarkan status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter berdasarkan santri
        if ($request->filled('santri')) {
            $query->whereHas('kartuPembayaran.user', function($q) use ($request) {
                $q->where('nama_santri', 'like', '%' . $request->santri . '%');
            });
        }

        // Filter berdasarkan tahun ajaran
        if ($request->filled('tahun_ajaran')) {
            $query->whereHas('kartuPembayaran', function($q) use ($request) {
                $q->where('tahun_ajaran_id', $request->tahun_ajaran);
            });
        }

        $tagihans = $query->orderBy('created_at', 'desc')->paginate(20);
        
        return view('admin.tagihan.index', compact('tagihans'));
    }

    public function show($id)
    {
        $tagihan = Tagihan::with([
            'kartuPembayaran.user',
            'kartuPembayaran.tahunAjaran',
            'tagihanDetails.jenisTagihan.kategori',
            'pembayaran.verifiedBy'
        ])->findOrFail($id);

        return view('admin.tagihan.show', compact('tagihan'));
    }

    public function create(Request $request)
    {
        $kartuPembayarans = KartuPembayaran::with(['user', 'tahunAjaran'])
            ->whereHas('tahunAjaran', function($q) {
                $q->where('is_active', true);
            })
            ->get();

        $selectedKartuId = $request->kartu_id;
        $selectedKartu = $selectedKartuId
            ? KartuPembayaran::with('user')->find($selectedKartuId)
            : null;

        $jenisTagihans = JenisTagihan::with('kategori')
            ->when($selectedKartu?->user, fn($q) => $q->applicableForUser($selectedKartu->user))
            ->get();

        return view('admin.tagihan.create', compact('kartuPembayarans', 'jenisTagihans', 'selectedKartuId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kartu_id' => 'required|exists:kartu_pembayaran,id',
            'jenis_tagihan_ids' => 'required|array',
            'jenis_tagihan_ids.*' => 'exists:jenis_tagihan,id',
            'status' => 'required|in:draft,belum_bayar,menunggu_verifikasi,lunas'
        ]);

        $kartu = KartuPembayaran::with('user')->findOrFail($request->kartu_id);
        $allowedJenisIds = JenisTagihan::applicableForUser($kartu->user)
            ->pluck('id')
            ->map(fn($id) => (int) $id)
            ->all();

        foreach ($request->jenis_tagihan_ids as $jenisId) {
            if (!in_array((int) $jenisId, $allowedJenisIds)) {
                return back()->withInput()->with('error', 'Ada jenis tagihan yang tidak sesuai jenjang/tingkatan ngaji santri.');
            }
        }

        DB::beginTransaction();
        try {
            // Buat tagihan baru
            $tagihan = Tagihan::create([
                'kartu_id' => $kartu->id,
                'total' => 0,
                'status' => $request->status
            ]);

            $totalTagihan = 0;

            // Buat detail tagihan
            foreach ($request->jenis_tagihan_ids as $jenisTagihanId) {
                $jenisTagihan = JenisTagihan::find($jenisTagihanId);
                
                if ($jenisTagihan->is_bulanan) {
                    // Untuk tagihan bulanan
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
                    // Untuk tagihan satu kali
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

            DB::commit();

            return redirect()->route('admin.tagihan.index')
                ->with('success', 'Tagihan berhasil dibuat!');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()
                ->with('error', 'Gagal membuat tagihan: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $tagihan = Tagihan::with(['tagihanDetails.jenisTagihan', 'kartuPembayaran.user'])
            ->findOrFail($id);
        
        $jenisTagihans = JenisTagihan::with('kategori')->get();
        
        return view('admin.tagihan.edit', compact('tagihan', 'jenisTagihans'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:draft,belum_bayar,menunggu_verifikasi,lunas'
        ]);

        $tagihan = Tagihan::findOrFail($id);
        $tagihan->update(['status' => $request->status]);

        // Update status detail jika tagihan lunas
        if ($request->status === Tagihan::STATUS_LUNAS) {
            $tagihan->tagihanDetails()->update(['status' => TagihanDetail::STATUS_LUNAS]);
        }

        return redirect()->route('admin.tagihan.show', $id)
            ->with('success', 'Status tagihan berhasil diupdate!');
    }

    public function destroy($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        
        // Cek apakah tagihan sudah ada pembayaran
        if ($tagihan->pembayaran()->exists()) {
            return back()->with('error', 'Tagihan tidak dapat dihapus karena sudah ada pembayaran!');
        }

        $tagihan->delete();

        return redirect()->route('admin.tagihan.index')
            ->with('success', 'Tagihan berhasil dihapus!');
    }

    public function updateDetail(Request $request, $tagihanId, $detailId)
    {
        $request->validate([
            'status' => 'required|in:belum_bayar,lunas',
            'nominal' => 'required|numeric|min:0'
        ]);

        $detail = TagihanDetail::where('tagihan_id', $tagihanId)
            ->findOrFail($detailId);

        $detail->update([
            'status' => $request->status,
            'nominal' => $request->nominal
        ]);

        // Update total tagihan
        $tagihan = $detail->tagihan;
        $totalBaru = $tagihan->tagihanDetails()->sum('nominal');
        $tagihan->update(['total' => $totalBaru]);

        return back()->with('success', 'Detail tagihan berhasil diupdate!');
    }

    public function deleteDetail($tagihanId, $detailId)
    {
        $detail = TagihanDetail::where('tagihan_id', $tagihanId)
            ->findOrFail($detailId);

        $tagihan = $detail->tagihan;
        $detail->delete();

        // Update total tagihan
        $totalBaru = $tagihan->tagihanDetails()->sum('nominal');
        $tagihan->update(['total' => $totalBaru]);

        return back()->with('success', 'Item tagihan berhasil dihapus!');
    }

    public function storeManualPayment(Request $request, $tagihanId)
    {
        $tagihan = Tagihan::with(['tagihanDetails', 'pembayaran'])->findOrFail($tagihanId);

        if ($tagihan->status === Tagihan::STATUS_LUNAS) {
            return back()->with('info', 'Tagihan ini sudah lunas.');
        }

        $request->validate([
            'tanggal_bayar' => 'required|date',
            'selected_detail_ids' => 'nullable|array',
            'selected_detail_ids.*' => 'exists:tagihan_detail,id',
            'jumlah_bayar' => 'nullable|numeric|min:1',
            'catatan' => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($request, $tagihan) {
            $selectedDetailIds = collect($request->input('selected_detail_ids', []))
                ->map(fn($id) => (int) $id)
                ->unique()
                ->values();

            $detailsToSettle = collect();

            if ($selectedDetailIds->isNotEmpty()) {
                $detailsToSettle = $tagihan->tagihanDetails()
                    ->whereIn('id', $selectedDetailIds->all())
                    ->where('status', TagihanDetail::STATUS_BELUM_BAYAR)
                    ->get();
            } elseif ($request->filled('jumlah_bayar')) {
                $remaining = (float) $request->jumlah_bayar;
                $unpaidDetails = $tagihan->tagihanDetails()
                    ->where('status', TagihanDetail::STATUS_BELUM_BAYAR)
                    ->orderBy('id')
                    ->get();

                foreach ($unpaidDetails as $detail) {
                    $nominal = (float) $detail->nominal;
                    if ($remaining >= $nominal) {
                        $detailsToSettle->push($detail);
                        $remaining -= $nominal;
                    }
                }
            }

            if ($detailsToSettle->isEmpty()) {
                throw new \RuntimeException('Tidak ada rincian tagihan yang bisa ditandai lunas dari input ini.');
            }

            $jumlahBayar = (float) $detailsToSettle->sum('nominal');

            Pembayaran::create([
                'tagihan_id' => $tagihan->id,
                'tanggal_bayar' => $request->tanggal_bayar,
                'jumlah_bayar' => $jumlahBayar,
                'bukti_pembayaran' => null,
                'catatan' => $request->catatan ?: 'Pembayaran manual admin',
                'status' => Pembayaran::STATUS_DITERIMA,
                'verified_by' => Auth::id(),
                'verified_at' => now(),
                'catatan_verifikator' => 'Input manual oleh admin',
            ]);

            $tagihan->tagihanDetails()
                ->whereIn('id', $detailsToSettle->pluck('id'))
                ->update(['status' => TagihanDetail::STATUS_LUNAS]);

            $remainingDetails = $tagihan->tagihanDetails()
                ->where('status', TagihanDetail::STATUS_BELUM_BAYAR)
                ->count();

            if ($remainingDetails === 0) {
                $tagihan->update(['status' => Tagihan::STATUS_LUNAS]);
            } else {
                $tagihan->update(['status' => Tagihan::STATUS_BELUM_BAYAR]);
            }
        });

        $userId = $tagihan->kartuPembayaran->user_id ?? null;

        if ($userId) {
            return redirect()->route('admin.manual-payment.show', $userId)
                ->with('success', 'Pembayaran manual berhasil dicatat.');
        }

        return redirect()->route('admin.manual-payment.index')
            ->with('success', 'Pembayaran manual berhasil dicatat.');
    }
}