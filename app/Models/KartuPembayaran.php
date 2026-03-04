<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KartuPembayaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'kartu_pembayaran';

    protected $fillable = [
        'user_id',
        'tahun_ajaran_id',
        'nomor_kartu',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_id');
    }
}