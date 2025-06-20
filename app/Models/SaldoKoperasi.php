<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SaldoKoperasi extends Model
{
    use HasFactory;

    protected $table = 'saldo_koperasis';
    protected $primaryKey = 'id_saldo';

    protected $fillable = [
        'pelaku_terkait_id',
        'kategori', // 'tabungan', 'pinjaman'
        'tanggal',
        'tipe',     // 'masuk', 'keluar'
        'jumlah',
        'keterangan',
    ];

    // 🔁 Relasi ke tabel users
    public function pelakuTerkait()
    {
        return $this->belongsTo(User::class, 'pelaku_terkait_id');
    }

    // 🧮 Menghitung total saldo koperasi secara umum
    public static function getSaldo(): float
    {
        $masuk = static::where('tipe', 'masuk')->sum('jumlah');
        $keluar = static::where('tipe', 'keluar')->sum('jumlah');

        return $masuk - $keluar;
    }

    // 🧮 Menghitung saldo tabungan khusus user tertentu
    public static function getSaldoTabunganByUser($userId): float
    {
        $masuk = static::where('pelaku_terkait_id', $userId)
            ->where('kategori', 'tabungan')
            ->where('tipe', 'masuk')
            ->sum('jumlah');

        $keluar = static::where('pelaku_terkait_id', $userId)
            ->where('kategori', 'tabungan')
            ->where('tipe', 'keluar')
            ->sum('jumlah');

        return $masuk - $keluar;
    }
}
