<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JenisPembayaran extends Model
{
    //

    protected $table = 'jenis_pembayaran';

    protected $fillable = [
        'nama_pembayaran',
        'nominal',
        'tipe',
        'aktif',
    ];

    public function tagihan()
{
    return $this->hasMany(Tagihan::class);
}

}
