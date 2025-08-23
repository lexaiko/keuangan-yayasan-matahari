<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JenisPembayaranLain extends Model
{
    use HasFactory;

    protected $table = 'jenis_pembayaran_lain';

    protected $fillable = [
        'nama_jenis',
        'deskripsi',
        'is_aktif'
    ];

    protected $casts = [
        'is_aktif' => 'boolean'
    ];

    public function pembayaranLain(): HasMany
    {
        return $this->hasMany(PembayaranLain::class);
    }
}
