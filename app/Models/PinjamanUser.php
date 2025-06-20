<?php

// app/Models/PinjamanUser.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PinjamanUser extends Model
{
    use HasFactory;

    protected $table = 'pinjaman_users';
    protected $primaryKey = 'id_pinjaman';

    protected $fillable = [
        'user_id',
        'tanggal_pinjam',
        'jumlah_pinjam',
        'bunga_persen',
        'total_kembali',
        'tenor_bulan',
        'status',
        'catatan',
    ];

    // Relasi ke user (peminjam)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relasi ke angsuran
    public function angsuran()
    {
        return $this->hasMany(AngsuranPinjaman::class, 'id_pinjaman');
    }
}
