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

    public static function getSummaryMasukKeluar(): array
{
    $masuk = static::where('tipe', 'masuk')->sum('jumlah');
    $keluar = static::where('tipe', 'keluar')->sum('jumlah');

    return [
        'Masuk' => $masuk,
        'Keluar' => $keluar,
    ];
}

    public static function getSaldoPerPegawai()
{
    return static::select('pelaku_terkait_id')
        ->selectRaw('SUM(CASE WHEN tipe = "masuk" THEN jumlah ELSE 0 END) as total_masuk')
        ->selectRaw('SUM(CASE WHEN tipe = "keluar" THEN jumlah ELSE 0 END) as total_keluar')
        ->groupBy('pelaku_terkait_id')
        ->havingRaw('SUM(CASE WHEN tipe = "masuk" THEN jumlah ELSE 0 END) - SUM(CASE WHEN tipe = "keluar" THEN jumlah ELSE 0 END) > 0')
        ->with('pelakuTerkait') // relasi ke tabel users
        ->get()
        ->map(function ($item) {
            return [
                'user' => $item->pelakuTerkait,
                'saldo' => $item->total_masuk - $item->total_keluar,
            ];
        });
}

}
