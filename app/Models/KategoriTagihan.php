<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KategoriTagihan extends Model
{
    use HasFactory;

    protected $table = 'kategori_tagihan';
    
    protected $fillable = [
        'nama'
    ];

    public function jenisTagihan()
    {
        return $this->hasMany(JenisTagihan::class, 'kategori_id');
    }
}