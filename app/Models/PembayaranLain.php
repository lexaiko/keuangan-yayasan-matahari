<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PembayaranLain extends Model
{
    use HasFactory;

    protected $table = 'pembayaran_lain';

    protected $fillable = [
        'jenis_pembayaran_lain_id',
        'siswa_id',
        'nama_pembayar',
        'jumlah',
        'tanggal_pembayaran',
        'keterangan'
    ];

    protected $casts = [
        'tanggal_pembayaran' => 'date',
        'jumlah' => 'decimal:2',
        'siswa_id' => 'string'
    ];

    protected static function boot()
    {
        parent::boot();

        // Automatically add to foundation balance when payment is created
        static::created(function ($model) {
            SaldoYayasan::addPendapatan(
                'Pembayaran Lain-Lain',
                $model->jumlah,
                "Pembayaran lain-lain dari {$model->nama_pembayar} - {$model->jenisPembayaranLain->nama_jenis}",
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
                    'jumlah' => $model->jumlah,
                    'keterangan' => "Pembayaran lain-lain dari {$model->nama_pembayar} - {$model->jenisPembayaranLain->nama_jenis} (Updated)",
                    'tanggal_transaksi' => $model->tanggal_pembayaran,
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

    public function jenisPembayaranLain(): BelongsTo
    {
        return $this->belongsTo(JenisPembayaranLain::class);
    }

    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class, 'siswa_id', 'id');
    }

    public function saldoYayasan()
    {
        return $this->morphOne(SaldoYayasan::class, 'referensi');
    }
}
