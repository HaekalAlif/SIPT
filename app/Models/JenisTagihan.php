<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisTagihan extends Model
{
    use HasFactory;

    protected $table = 'jenis_tagihan';
    
    protected $fillable = [
        'kategori_id',
        'nama_tagihan',
        'nominal',
        'is_bulanan'
    ];

    protected $casts = [
        'nominal' => 'decimal:2',
        'is_bulanan' => 'boolean'
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriTagihan::class, 'kategori_id');
    }

    public function tagihanDetails()
    {
        return $this->hasMany(TagihanDetail::class);
    }
}