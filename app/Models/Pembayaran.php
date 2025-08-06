<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pembayaran extends Model
{
    protected $table = 'pembayarans';
    protected $primaryKey = 'id';
    public $incrementing = false; // Karena pakai custom ID
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'siswa_id',
        'user_id',
        'jumlah_bayar',
        'tanggal_bayar',
        'keterangan',
        'tunai',
        'kembalian',
    ];


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            // Ambil ID terakhir dari DB
            $last = self::orderBy('id', 'desc')->first();

            // Ambil nomor terakhir (misalnya B00009 -> 9)
            if ($last) {
                $lastNumber = (int) substr($last->id, 1);
                $nextNumber = $lastNumber + 1;
            } else {
                $nextNumber = 1;
            }

            // Format ke B00001, B00002, dst.
            $model->id = 'B' . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
        });
    }

    // Method untuk route key binding
    public function getRouteKeyName()
    {
        return 'id';
    }

    // Relasi ke Siswa
    public function siswa()
    {
        return $this->belongsTo(Siswa::class);
    }

    // Relasi ke Tagihan
    public function tagihan()
    {
        return $this->belongsTo(Tagihan::class);
    }

    // Relasi ke User (admin yang menangani)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function detailPembayarans()
    {
        return $this->hasMany(DetailPembayaran::class);
    }
}
