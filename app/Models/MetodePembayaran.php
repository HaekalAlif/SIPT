<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MetodePembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'metode_pembayaran';

    protected $fillable = [
        'nama_metode',
        'nama_bank',
        'nomor_rekening',
        'atas_nama',
        'logo_path',
        'keterangan',
        'is_active',
        'urutan',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('urutan')->orderBy('nama_metode');
    }
}
