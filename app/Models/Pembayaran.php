<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'pembayaran';

    protected $fillable = [
        'tagihan_id',
        'tanggal_bayar',
        'jumlah_bayar',
        'bukti_pembayaran',
        'catatan',
        'status',
        'verified_by',
        'verified_at',
        'catatan_verifikator',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'jumlah_bayar' => 'float',
        'verified_at' => 'datetime',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public const STATUS_MENUNGGU_VERIFIKASI = 'menunggu_verifikasi';
    public const STATUS_DITERIMA = 'diterima';
    public const STATUS_DITOLAK = 'ditolak';
}