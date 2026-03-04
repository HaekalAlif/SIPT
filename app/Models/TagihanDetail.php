<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagihanDetail extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_BELUM_BAYAR = 'belum_bayar';
    public const STATUS_LUNAS = 'lunas';

    protected $table = 'tagihan_detail';

    protected $fillable = [
        'tagihan_id',
        'jenis_tagihan_id',
        'bulan',
        'nominal',
        'status'
    ];

    protected $casts = [
        'nominal' => 'decimal:2'
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class);
    }
}