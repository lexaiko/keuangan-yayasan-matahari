<?php

// app/Models/Tagihan.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;
use App\Models\JenisPembayaran;
use App\Models\TahunAkademik;

class Tagihan extends Model
{
    protected $table = 'tagihan';

    // Konstanta untuk status
    const STATUS_BELUM_BAYAR = 'belum_bayar';
    const STATUS_SEBAGIAN = 'sebagian';
    const STATUS_LUNAS = 'lunas';

    protected $fillable = [
        'siswa_id',
        'jenis_pembayaran_id',
        'tahun_akademik_id',
        'bulan',
        'jumlah',
        'status',
        'tanggal_jatuh_tempo',
    ];

    // Relasi
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    public function jenisPembayaran()
    {
        return $this->belongsTo(JenisPembayaran::class);
    }

    public function getNamaPembayaranAttribute()
{
    return $this->jenisPembayaran?->nama_pembayaran ?? '-';
}

    public function tahunAkademik()
    {
        return $this->belongsTo(TahunAkademik::class);
    }
    public function detailPembayarans()
{
    return $this->hasMany(DetailPembayaran::class);
}


}
