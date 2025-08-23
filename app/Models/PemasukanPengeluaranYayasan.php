<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PemasukanPengeluaranYayasan extends Model
{
    use HasFactory;

    protected $table = 'pemasukan_pengeluaran_yayasan';

    protected $fillable = [
        'jenis_transaksi',
        'kategori_id',
        'jumlah',
        'tanggal_transaksi',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_transaksi' => 'date',
        'jumlah' => 'decimal:2'
    ];

    public function kategoriPemasukanPengeluaran(): BelongsTo
    {
        return $this->belongsTo(KategoriPemasukanPengeluaran::class, 'kategori_id');
    }
}
