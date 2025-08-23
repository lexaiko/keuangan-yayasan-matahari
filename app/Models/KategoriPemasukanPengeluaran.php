<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class KategoriPemasukanPengeluaran extends Model
{
    use HasFactory;

    protected $table = 'kategori_pemasukan_pengeluaran';

    protected $fillable = [
        'nama_kategori',
        'jenis', // pemasukan atau pengeluaran
        'deskripsi',
        'is_aktif'
    ];

    protected $casts = [
        'is_aktif' => 'boolean'
    ];

    public function pemasukanPengeluaranYayasan(): HasMany
    {
        return $this->hasMany(PemasukanPengeluaranYayasan::class);
    }

    public function scopePemasukan($query)
    {
        return $query->where('jenis', 'pemasukan');
    }

    public function scopePengeluaran($query)
    {
        return $query->where('jenis', 'pengeluaran');
    }

    public function scopeAktif($query)
    {
        return $query->where('is_aktif', true);
    }
}
