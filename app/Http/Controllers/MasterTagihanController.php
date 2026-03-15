<?php

namespace App\Http\Controllers;

use App\Models\JenisTagihan;
use App\Models\JenisTagihanDisabled;
use App\Models\TahunAjaran;
use App\Models\User;
use Illuminate\Http\Request;

class MasterTagihanController extends Controller
{
    public function index(Request $request)
    {
        $tahunAjarans = TahunAjaran::orderByDesc('is_active')->orderByDesc('id')->get();
        $selectedTaId = $request->tahun_ajaran_id
            ? (int) $request->tahun_ajaran_id
            : ($tahunAjarans->firstWhere('is_active', true)?->id ?? $tahunAjarans->first()?->id);
        $selectedTingkatan = $request->get('tingkatan');
        $selectedKelas = $request->get('kelas');

        $tingkatanOptions = User::where('role', User::ROLE_SANTRI)
            ->where('status', User::STATUS_ACTIVE)
            ->whereNotNull('tingkatan')
            ->distinct()
            ->orderBy('tingkatan')
            ->pluck('tingkatan');

        $kelasPairs = User::where('role', User::ROLE_SANTRI)
            ->where('status', User::STATUS_ACTIVE)
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

        $allSantris = User::where('role', User::ROLE_SANTRI)
            ->where('status', User::STATUS_ACTIVE)
            ->orderBy('nama_santri')
            ->get([
                'id',
                'nama_santri',
                'tingkatan',
                'kelas',
                'tingkatan_ngaji',
                'tahun_ajaran_masuk_id',
            ]);

        $santris = $allSantris->filter(function ($santri) use ($selectedTaId, $selectedTingkatan, $selectedKelas) {
            if ($selectedTaId && !is_null($santri->tahun_ajaran_masuk_id) && (int) $santri->tahun_ajaran_masuk_id > (int) $selectedTaId) {
                return false;
            }

            if ($selectedTingkatan && (string) $santri->tingkatan !== (string) $selectedTingkatan) {
                return false;
            }

            if ($selectedKelas && (string) $santri->kelas !== (string) $selectedKelas) {
                return false;
            }

            return true;
        })->values();

        $santriOptions = $allSantris->map(fn($s) => [
            'id' => (int) $s->id,
            'nama_santri' => (string) ($s->nama_santri ?? ''),
            'tingkatan' => (string) ($s->tingkatan ?? ''),
            'kelas' => (string) ($s->kelas ?? ''),
            'tingkatan_ngaji' => (string) ($s->tingkatan_ngaji ?? ''),
            'tahun_ajaran_masuk_id' => $s->tahun_ajaran_masuk_id ? (int) $s->tahun_ajaran_masuk_id : null,
        ])->values();

        $selectedUserId = $request->santri_id ? (int) $request->santri_id : null;
        $selectedUser   = $selectedUserId
            ? User::where('role', User::ROLE_SANTRI)
                ->where('status', User::STATUS_ACTIVE)
                ->find($selectedUserId)
            : null;

        $jenisTagihans = JenisTagihan::with('kategori')
            ->when($selectedUser, fn($q) => $q->applicableForUser($selectedUser))
            ->get();
        $disabledIds   = ($selectedTaId && $selectedUserId)
            ? JenisTagihanDisabled::getDisabledIds($selectedTaId, $selectedUserId)
            : [];
        $grouped = $jenisTagihans->groupBy(fn($j) => $j->kategori->nama ?? 'Lainnya');

        return view('admin.master-tagihan.index', compact(
            'tahunAjarans', 'santris', 'selectedTaId', 'selectedUserId',
            'jenisTagihans', 'disabledIds', 'grouped',
            'selectedTingkatan', 'selectedKelas',
            'tingkatanOptions', 'kelasOptionsByTingkatan', 'santriOptions'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'tahun_ajaran_id' => 'required|exists:tahun_ajaran,id',
            'user_id'         => 'required|exists:users,id',
            'active_ids'      => 'nullable|array',
            'active_ids.*'    => 'exists:jenis_tagihan,id',
        ]);

        JenisTagihanDisabled::syncForSantri(
            (int) $request->user_id,
            (int) $request->tahun_ajaran_id,
            $request->active_ids ?? []
        );

        return redirect()
            ->route('admin.master-tagihan', [
                'tahun_ajaran_id' => $request->tahun_ajaran_id,
                'santri_id'       => $request->user_id,
                'tingkatan'       => $request->tingkatan,
                'kelas'           => $request->kelas,
            ])
            ->with('success', 'Konfigurasi tagihan untuk santri berhasil disimpan.');
    }
}
