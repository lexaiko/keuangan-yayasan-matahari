<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaldoYayasan extends Model
{
    protected $table = 'saldo_yayasans';

    protected $fillable = [
        'jenis_transaksi',
        'kategori',
        'jumlah',
        'keterangan',
        'tanggal_transaksi',
        'user_id',
        'referensi_id',
        'referensi_tipe',
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function referensi()
    {
        return $this->morphTo();
    }

    public static function getSaldoTerkini()
    {
        // Pemasukan - Pengeluaran dari transaksi manual
        $pemasukanManual = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pemasukan')->sum('jumlah');
        $pengeluaranManual = PemasukanPengeluaranYayasan::where('jenis_transaksi', 'pengeluaran')->sum('jumlah');

        // Pembayaran siswa
        $pembayaranSiswa = Pembayaran::sum('jumlah_bayar');

        // Pembayaran lain-lain
        $pembayaranLain = PembayaranLain::sum('jumlah');

        // Formula: (Pemasukan - Pengeluaran) + Pembayaran Siswa + Pembayaran Lain-Lain
        return ($pemasukanManual - $pengeluaranManual) + $pembayaranSiswa + $pembayaranLain;
    }

    public static function addPendapatan($kategori, $jumlah, $keterangan, $referensi = null)
    {
        return self::create([
            'jenis_transaksi' => 'pendapatan',
            'kategori' => $kategori,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan,
            'tanggal_transaksi' => now(),
            'user_id' => auth()->id(),
            'referensi_id' => $referensi ? $referensi->id : null,
            'referensi_tipe' => $referensi ? get_class($referensi) : null,
        ]);
    }

    public static function addPengeluaran($kategori, $jumlah, $keterangan, $referensi = null)
    {
        return self::create([
            'jenis_transaksi' => 'pengeluaran',
            'kategori' => $kategori,
            'jumlah' => $jumlah,
            'keterangan' => $keterangan,
            'tanggal_transaksi' => now(),
            'user_id' => auth()->id(),
            'referensi_id' => $referensi ? $referensi->id : null,
            'referensi_tipe' => $referensi ? get_class($referensi) : null,
        ]);
    }
}
