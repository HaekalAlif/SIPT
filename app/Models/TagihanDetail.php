<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagihanDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tagihan_detail';

    protected $fillable = [
        'tagihan_id',
        'jenis_tagihan_id',
        'bulan',
        'nominal',
        'status',
    ];

    protected $casts = [
        'nominal' => 'float',
    ];

    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class, 'tagihan_id');
    }

    public function jenisTagihan()
    {
        return $this->belongsTo(JenisTagihan::class, 'jenis_tagihan_id');
    }
}