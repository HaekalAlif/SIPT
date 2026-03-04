<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JenisTagihan extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'jenis_tagihan';

    protected $fillable = [
        'kategori_id',
        'nama_tagihan',
        'nominal',
        'is_bulanan',
    ];

    protected $casts = [
        'nominal' => 'float',
        'is_bulanan' => 'boolean',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriTagihan::class, 'kategori_id');
    }
}