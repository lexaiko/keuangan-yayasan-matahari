<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tingkat extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'tingkats';

    protected $fillable = [
        'nama',
    ];

    public function kelas()
    {
        return $this->hasMany(Kelas::class, 'tingkat_id');
    }


}
