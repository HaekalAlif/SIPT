<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisTagihanDisabled extends Model
{
    protected $table = 'jenis_tagihan_disabled';

    protected $fillable = [
        'user_id',
        'tahun_ajaran_id',
        'jenis_tagihan_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class);
    }

    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class);
    }

    /**
     * Ambil array jenis_tagihan_id yang dinonaktifkan untuk santri + tahun ajaran tertentu.
     */
    public static function getDisabledIds(int $tahunAjaranId, int $userId): array
    {
        return self::where('user_id', $userId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->pluck('jenis_tagihan_id')
            ->toArray();
    }

    /**
     * Sinkronisasi: simpan jenis tagihan yang dinonaktifkan untuk santri + tahun ajaran.
     * $activeIds = array ID yang AKTIF (dicentang admin).
     * Sisanya di-disable.
     */
    public static function syncForSantri(int $userId, int $tahunAjaranId, array $activeIds): void
    {
        $allIds      = JenisTagihan::pluck('id')->toArray();
        $disabledIds = array_values(array_diff($allIds, $activeIds));

        self::where('user_id', $userId)
            ->where('tahun_ajaran_id', $tahunAjaranId)
            ->delete();

        foreach ($disabledIds as $jenisId) {
            self::create([
                'user_id'          => $userId,
                'tahun_ajaran_id'  => $tahunAjaranId,
                'jenis_tagihan_id' => $jenisId,
            ]);
        }
    }

    /**
     * Build nested map [user_id => [tahun_ajaran_id => [jenis_tagihan_id, ...]]]
     * For use in JS when admin creates a kartu.
     */
    public static function buildDisabledMap(): array
    {
        $map = [];
        self::all()->each(function ($row) use (&$map) {
            $map[$row->user_id][$row->tahun_ajaran_id][] = $row->jenis_tagihan_id;
        });
        return $map;
    }
}
