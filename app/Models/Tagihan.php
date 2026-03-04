<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tagihan extends Model
{
    use HasFactory, SoftDeletes;

    public const STATUS_DRAFT = 'draft';
    public const STATUS_BELUM_BAYAR = 'belum_bayar';
    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    public const STATUS_LUNAS = 'lunas';

    protected $table = 'tagihan';

    protected $fillable = [
        'kartu_id',
        'total',
        'status'
    ];

    protected $casts = [
        'total' => 'decimal:2'
    ];

    public function kartuPembayaran()
    {
        return $this->belongsTo(KartuPembayaran::class, 'kartu_id');
    }

    public function tagihanDetails()
    {
        return $this->hasMany(TagihanDetail::class);
    }

    public function pembayaran()
    {
        return $this->hasMany(Pembayaran::class);
    }

    public function pembayaranTerakhir()
    {
        return $this->hasOne(Pembayaran::class)->where('status', 'diterima')->latest();
    }
}