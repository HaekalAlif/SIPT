<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TahunAjaran extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tahun_ajaran';

    protected $fillable = [
        'nama',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // Accessor for backward compatibility
    public function getTahunAjaranAttribute()
    {
        return $this->nama;
    }

    public function kartuPembayaran()
    {
        return $this->hasMany(KartuPembayaran::class);
    }
}