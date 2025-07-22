<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailPembayaran extends Model
{
    protected $table = 'detail_pembayarans';

    protected $fillable = [
        'pembayaran_id',
        'tagihan_id',
        'jumlah_bayar',
    ];

    // Relasi ke pembayaran
    public function pembayaran()
    {
        return $this->belongsTo(Pembayaran::class);
    }

    // Relasi ke tagihan
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }
}
