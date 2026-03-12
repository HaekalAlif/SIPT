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
        $santris      = User::where('role', User::ROLE_SANTRI)
            ->where('status', User::STATUS_ACTIVE)
            ->orderBy('nama_santri')
            ->get();

        $selectedTaId   = $request->tahun_ajaran_id
            ? (int) $request->tahun_ajaran_id
            : ($tahunAjarans->firstWhere('is_active', true)?->id ?? $tahunAjarans->first()?->id);

        $selectedUserId = $request->santri_id ? (int) $request->santri_id : null;

        $jenisTagihans = JenisTagihan::with('kategori')->get();
        $disabledIds   = ($selectedTaId && $selectedUserId)
            ? JenisTagihanDisabled::getDisabledIds($selectedTaId, $selectedUserId)
            : [];
        $grouped = $jenisTagihans->groupBy(fn($j) => $j->kategori->nama ?? 'Lainnya');

        return view('admin.master-tagihan.index', compact(
            'tahunAjarans', 'santris', 'selectedTaId', 'selectedUserId',
            'jenisTagihans', 'disabledIds', 'grouped'
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
            ])
            ->with('success', 'Konfigurasi tagihan untuk santri berhasil disimpan.');
    }
}
