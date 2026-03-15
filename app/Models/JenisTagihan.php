<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTagihan extends Model
{
    use HasFactory;

    public const TARGET_SCOPE_ALL = 'all';
    public const TARGET_SCOPE_TINGKATAN = 'tingkatan';
    public const TARGET_SCOPE_NGAJI = 'ngaji';

    protected $table = 'jenis_tagihan';
    
    protected $fillable = [
        'kategori_id',
        'nama_tagihan',
        'nominal',
        'is_bulanan',
        'target_scope',
        'target_value',
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'is_bulanan' => 'boolean',
    ];

    public static function normalizeTargetValue(string $scope, ?string $value): ?string
    {
        if ($scope === self::TARGET_SCOPE_ALL) {
            return null;
        }

        $value = trim((string) $value);
        if ($value === '') {
            return null;
        }

        if ($scope === self::TARGET_SCOPE_TINGKATAN) {
            return strtoupper($value);
        }

        return $value;
    }

    public function scopeApplicableForUser($query, ?User $user)
    {
        $tingkatan = strtoupper(trim((string) ($user?->tingkatan ?? '')));
        $ngaji     = trim((string) ($user?->tingkatan_ngaji ?? ''));

        return $query->where(function ($q) use ($tingkatan, $ngaji) {
            $q->whereNull('target_scope')
                ->orWhere('target_scope', self::TARGET_SCOPE_ALL);

            if ($tingkatan !== '') {
                $q->orWhere(function ($sq) use ($tingkatan) {
                    $sq->where('target_scope', self::TARGET_SCOPE_TINGKATAN)
                        ->where('target_value', $tingkatan);
                });
            }

            if ($ngaji !== '') {
                $q->orWhere(function ($sq) use ($ngaji) {
                    $sq->where('target_scope', self::TARGET_SCOPE_NGAJI)
                        ->where('target_value', $ngaji);
                });
            }
        });
    }

    public function kategori()
    {
        return $this->belongsTo(KategoriTagihan::class, 'kategori_id');
    }

    public function tagihanDetails()
    {
        return $this->hasMany(TagihanDetail::class);
    }
}