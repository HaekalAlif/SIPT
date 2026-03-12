<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'nis',
        'email',
        'password',
        'role',
        'status',
        'email_verified_at',
        'remember_token',
        'nama_santri',
        'nama_orang_tua',
        'no_telp',
        'tingkatan',
        'kelas',
        'tahun_ajaran_masuk_id',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'tingkatan_ngaji',
        'foto_profile',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'tanggal_lahir' => 'date',
    ];

    public function kartuPembayaran()
    {
        return $this->hasMany(KartuPembayaran::class);
    }

    public function tahunAjaranMasuk()
    {
        return $this->belongsTo(TahunAjaran::class, 'tahun_ajaran_masuk_id');
    }
        
    public const ROLE_ADMIN = 'admin';
    public const ROLE_BENDAHARA = 'bendahara';
    public const ROLE_SANTRI = 'santri';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_INACTIVE = 'inactive';

    public const JENIS_KELAMIN_L = 'L';
    public const JENIS_KELAMIN_P = 'P';
}