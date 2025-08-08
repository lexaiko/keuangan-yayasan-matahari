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

        // Automatically add to foundation balance when payment is created
        static::created(function ($model) {
            SaldoYayasan::addPendapatan(
                'Pembayaran Siswa',
                $model->jumlah_bayar,
                "Pembayaran dari {$model->siswa->nama} - Invoice #{$model->id}",
                $model
            );
        });

        // Update foundation balance when payment is updated
        static::updated(function ($model) {
            // Find existing saldo yayasan record for this payment
            $existingSaldo = SaldoYayasan::where('referensi_id', $model->id)
                ->where('referensi_tipe', get_class($model))
                ->first();

            if ($existingSaldo) {
                $existingSaldo->update([
                    'jumlah' => $model->jumlah_bayar,
                    'keterangan' => "Pembayaran dari {$model->siswa->nama} - Invoice #{$model->id} (Updated)",
                    'tanggal_transaksi' => $model->tanggal_bayar,
                ]);
            }
        });

        // Remove from foundation balance when payment is deleted
        static::deleted(function ($model) {
            SaldoYayasan::where('referensi_id', $model->id)
                ->where('referensi_tipe', get_class($model))
                ->delete();
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

    public function saldoYayasan()
    {
        return $this->morphOne(SaldoYayasan::class, 'referensi');
    }
}
