<?php

// app/Models/LogTransaksiKoperasi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogTransaksiKoperasi extends Model
{
    use HasFactory;

    protected $table = 'log_transaksi_koperasis';
    protected $primaryKey = 'id_log';

    protected $fillable = [
        'tanggal',
        'jenis',
        'keterangan',
        'nominal',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
