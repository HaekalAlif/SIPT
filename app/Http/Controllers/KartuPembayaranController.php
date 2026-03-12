<?php

namespace App\Http\Controllers;

use App\Models\KartuPembayaran;
use App\Models\User;
use App\Models\TahunAjaran;
use App\Models\JenisTagihan;
use App\Models\JenisTagihanDisabled;
use App\Models\Tagihan;
use App\Models\TagihanDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KartuPembayaranController extends Controller
{
    public function index(Request $request)
    {
        $query = KartuPembayaran::with(['user', 'tahunAjaran', 'tagihan.pembayaran']);

        // Filter berdasarkan tahun ajaran
        if ($request->filled('tahun_ajaran')) {
            $query->where('tahun_ajaran_id', $request->tahun_ajaran);
        }

        // Filter berdasarkan santri
        if ($request->filled('santri')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('nama_santri', 'like', '%' . $request->santri . '%');
            });
        }

        $kartuPembayarans = $query->orderBy('created_at', 'desc')->paginate(20);
        $tahunAjarans = TahunAjaran::all();

        return view('admin.kartu-pembayaran.index', compact('kartuPembayarans', 'tahunAjarans'));
    }

    public function show($id)
    {
        $kartuPembayaran = KartuPembayaran::with([
            'user.tahunAjaranMasuk',
            'tahunAjaran',
            'tagihan.tagihanDetails.jenisTagihan.kategori',
            'tagihan.pembayaran',
        ])->findOrFail($id);

        return view('admin.kartu-pembayaran.show', compact('kartuPembayaran'));
    }

    public function create()
    {
        $santris = User::where('role', User::ROLE_SANTRI)
            ->where('status', User::STATUS_ACTIVE)
            ->get();
        $tahunAjarans  = TahunAjaran::all();
        $jenisTagihans = JenisTagihan::with('kategori')->get();

        // Build nested map: { user_id: { ta_id: [disabled_jenis_ids] } }
        $disabledMap = JenisTagihanDisabled::buildDisabledMap();

        return view('admin.kartu-pembayaran.create', compact(
            'santris', 'tahunAjarans', 'jenisTagihans', 'disabledMap'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id'              => 'required|exists:users,id',
            'tahun_ajaran_id'      => 'required|exists:tahun_ajaran,id',
            'nomor_kartu'          => 'nullable|string|max:50|unique:kartu_pembayaran,nomor_kartu',
            'jenis_tagihan_ids'    => 'nullable|array',
            'jenis_tagihan_ids.*'  => 'exists:jenis_tagihan,id',
        ]);

        $existingKartu = KartuPembayaran::where('user_id', $request->user_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->first();

        if ($existingKartu) {
            return back()->withInput()
                ->with('error', 'Santri sudah memiliki kartu pembayaran untuk tahun ajaran ini!');
        }

        if (!$request->nomor_kartu) {
            $tahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
            $nomorKartu = 'KP-' . $tahunAjaran->nama . '-' . str_pad($request->user_id, 4, '0', STR_PAD_LEFT);
        } else {
            $nomorKartu = $request->nomor_kartu;
        }

        DB::beginTransaction();
        try {
            $kartu = KartuPembayaran::create([
                'user_id'         => $request->user_id,
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'nomor_kartu'     => $nomorKartu,
            ]);

            $jenisIds = $request->jenis_tagihan_ids ?? [];

            // Filter out jenis tagihan yang dinonaktifkan admin untuk santri ini
            $disabledByAdmin = JenisTagihanDisabled::getDisabledIds(
                (int) $request->tahun_ajaran_id,
                (int) $request->user_id
            );
            $jenisIds = array_filter($jenisIds, fn($id) => !in_array((int)$id, $disabledByAdmin));

            $bulanList = ['Juli','Agustus','September','Oktober','November','Desember',
                          'Januari','Februari','Maret','April','Mei','Juni'];

            foreach ($jenisIds as $jenisId) {
                $jenis = JenisTagihan::find($jenisId);
                if (!$jenis) continue;

                $tagihan = Tagihan::create([
                    'kartu_id' => $kartu->id,
                    'total'    => 0,
                    'status'   => 'belum_bayar',
                ]);

                $total = 0;
                if ($jenis->is_bulanan) {
                    foreach ($bulanList as $bulan) {
                        TagihanDetail::create([
                            'tagihan_id'       => $tagihan->id,
                            'jenis_tagihan_id' => $jenis->id,
                            'bulan'            => $bulan,
                            'nominal'          => $jenis->nominal,
                            'status'           => TagihanDetail::STATUS_BELUM_BAYAR,
                        ]);
                        $total += $jenis->nominal;
                    }
                } else {
                    TagihanDetail::create([
                        'tagihan_id'       => $tagihan->id,
                        'jenis_tagihan_id' => $jenis->id,
                        'bulan'            => null,
                        'nominal'          => $jenis->nominal,
                        'status'           => TagihanDetail::STATUS_BELUM_BAYAR,
                    ]);
                    $total = $jenis->nominal;
                }
                $tagihan->update(['total' => $total]);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal membuat kartu: ' . $e->getMessage());
        }

        return redirect()->route('admin.kartu-pembayaran.show', $kartu->id)
            ->with('success', 'Kartu pembayaran dan tagihan berhasil dibuat!');
    }

    public function edit($id)
    {
        $kartuPembayaran = KartuPembayaran::findOrFail($id);
        $santris = User::where('role', User::ROLE_SANTRI)->get();
        $tahunAjarans = TahunAjaran::all();

        return view('admin.kartu-pembayaran.edit', compact('kartuPembayaran', 'santris', 'tahunAjarans'));
    }

    public function update(Request $request, $id)
    {
        $kartuPembayaran = KartuPembayaran::findOrFail($id);
        
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'nomor_kartu' => 'required|string|max:50|unique:kartu_pembayaran,nomor_kartu,' . $id
        ]);

        // Cek apakah ada kartu lain dengan user dan tahun ajaran yang sama
        $existingKartu = KartuPembayaran::where('user_id', $request->user_id)
            ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
            ->where('id', '!=', $id)
            ->first();

        if ($existingKartu) {
            return back()->withInput()
                ->with('error', 'Santri sudah memiliki kartu pembayaran untuk tahun ajaran ini!');
        }

        $kartuPembayaran->update($request->all());

        return redirect()->route('admin.kartu-pembayaran.show', $id)
            ->with('success', 'Kartu pembayaran berhasil diupdate!');
    }

    public function destroy($id)
    {
        $kartuPembayaran = KartuPembayaran::findOrFail($id);
        
        // Cek apakah kartu sudah punya tagihan
        if ($kartuPembayaran->tagihan()->exists()) {
            return back()->with('error', 'Kartu pembayaran tidak dapat dihapus karena sudah memiliki tagihan!');
        }

        $kartuPembayaran->delete();

        return redirect()->route('admin.kartu-pembayaran.index')
            ->with('success', 'Kartu pembayaran berhasil dihapus!');
    }

    public function generateMassal(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'tingkatan' => 'nullable|string',
            'kelas' => 'nullable|string'
        ]);

        $query = User::where('role', User::ROLE_SANTRI)
            ->where('status', User::STATUS_ACTIVE);

        // Filter berdasarkan tingkatan jika ada
        if ($request->filled('tingkatan')) {
            $query->where('tingkatan', $request->tingkatan);
        }

        // Filter berdasarkan kelas jika ada
        if ($request->filled('kelas')) {
            $query->where('kelas', $request->kelas);
        }

        $santris = $query->get();
        $tahunAjaran = TahunAjaran::find($request->tahun_ajaran_id);
        $created = 0;
        $existing = 0;

        foreach ($santris as $santri) {
            // Cek apakah santri sudah punya kartu untuk tahun ajaran ini
            $existingKartu = KartuPembayaran::where('user_id', $santri->id)
                ->where('tahun_ajaran_id', $request->tahun_ajaran_id)
                ->first();

            if (!$existingKartu) {
                KartuPembayaran::create([
                    'user_id' => $santri->id,
                    'tahun_ajaran_id' => $request->tahun_ajaran_id,
                    'nomor_kartu' => 'KP-' . $tahunAjaran->nama . '-' . str_pad($santri->id, 4, '0', STR_PAD_LEFT)
                ]);
                $created++;
            } else {
                $existing++;
            }
        }

        $message = "Berhasil membuat {$created} kartu pembayaran.";
        if ($existing > 0) {
            $message .= " {$existing} santri sudah memiliki kartu.";
        }

        return redirect()->route('admin.kartu-pembayaran.index')
            ->with('success', $message);
    }

    public function showGenerateMassal()
    {
        $tahunAjarans = TahunAjaran::all();
        $tingkatans = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('tingkatan')
            ->distinct()
            ->pluck('tingkatan');
        $kelas = User::where('role', User::ROLE_SANTRI)
            ->whereNotNull('kelas')
            ->distinct()
            ->pluck('kelas');

        return view('admin.kartu-pembayaran.generate-massal', compact('tahunAjarans', 'tingkatans', 'kelas'));
    }

    public function cetak($id)
    {
        $kartuPembayaran = KartuPembayaran::with([
            'user',
            'tahunAjaran',
            'tagihan.tagihanDetails.jenisTagihan.kategori'
        ])->findOrFail($id);

        return view('admin.kartu-pembayaran.cetak', compact('kartuPembayaran'));
    }
}