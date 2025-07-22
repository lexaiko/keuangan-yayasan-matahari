<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TahunAkademik extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tahun_akademiks';

    protected $fillable = [
        'nama',
        'mulai',
        'selesai',
        'is_active',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'tahun_id');
    }
    public function tagihan()
{
    return $this->hasMany(Tagihan::class);
}

}
