<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Siswa extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'siswas';

    protected $fillable = [
        'nama',
        'kelas_id',
        'nis',
        'nisn',
        'status',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'alamat',
        'nama_ayah',
        'nama_ibu',
        'telepon',
        'email',
        'foto',
    ];
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
    public function tagihan()
{
    return $this->hasMany(Tagihan::class);
}
    public function pembayarans()
    {
        return $this->hasMany(Pembayaran::class);
    }
}
