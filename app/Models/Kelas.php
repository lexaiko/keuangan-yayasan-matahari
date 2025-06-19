<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Kelas extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'tahun_id',
        'tingkat_id',
    ];
    public function tahun()
    {
        return $this->belongsTo(TahunAkademik::class, 'tahun_id');
    }

    public function tingkat()
    {
        return $this->belongsTo(Tingkat::class, 'tingkat_id');
    }

    public function siswas()
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }
}
