<?php

// app/Models/AngsuranPinjaman.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AngsuranPinjaman extends Model
{
    use HasFactory;

    protected $table = 'angsuran_pinjamans';
    protected $primaryKey = 'id_angsuran';

    protected $fillable = [
        'id_pinjaman',
        'angsuran_ke',
        'tanggal_jatuh_tempo',
        'tanggal_bayar',
        'jumlah_bayar',
        'status',
        'keterangan',
    ];

    public function pinjaman()
    {
        return $this->belongsTo(PinjamanUser::class, 'id_pinjaman');
    }
}
