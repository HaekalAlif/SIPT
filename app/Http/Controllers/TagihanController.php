<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\TagihanDetail;
use App\Models\KartuPembayaran;
use App\Models\JenisTagihan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TagihanController extends Controller
{
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

        $jenisTagihans = JenisTagihan::with('kategori')->get();
        $selectedKartuId = $request->kartu_id;

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

        DB::beginTransaction();
        try {
            // Buat tagihan baru
            $tagihan = Tagihan::create([
                'kartu_id' => $request->kartu_id,
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
}