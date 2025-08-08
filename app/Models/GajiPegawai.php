<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GajiPegawai extends Model
{
    protected $table = 'gaji_pegawais';

    protected $fillable = [
        'user_id',
        'bulan',
        'tahun',
        'total_gaji',
        'status',
        'tanggal_bayar',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_bayar' => 'date',
        'total_gaji' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $originalStatus = $model->getOriginal('status');
            $newStatus = $model->status;

            // If status changed from not-paid to paid
            if ($originalStatus !== 'dibayar' && $newStatus === 'dibayar') {
                SaldoYayasan::addPengeluaran(
                    'Gaji Pegawai',
                    $model->total_gaji,
                    "Gaji {$model->user->name} - {$model->bulan} {$model->tahun}",
                    $model
                );
            }
            
            // If status changed from paid to not-paid (rollback payment)
            elseif ($originalStatus === 'dibayar' && $newStatus !== 'dibayar') {
                // Remove the expense record from foundation balance
                SaldoYayasan::where('referensi_id', $model->id)
                    ->where('referensi_tipe', get_class($model))
                    ->delete();
            }
            
            // If salary amount changed and already paid, update saldo yayasan
            elseif ($model->isDirty('total_gaji') && $model->status === 'dibayar') {
                $existingSaldo = SaldoYayasan::where('referensi_id', $model->id)
                    ->where('referensi_tipe', get_class($model))
                    ->first();

                if ($existingSaldo) {
                    $existingSaldo->update([
                        'jumlah' => $model->total_gaji,
                        'keterangan' => "Gaji {$model->user->name} - {$model->bulan} {$model->tahun} (Updated)",
                    ]);
                }
            }
        });

        // Remove from foundation balance when salary record is deleted
        static::deleted(function ($model) {
            SaldoYayasan::where('referensi_id', $model->id)
                ->where('referensi_tipe', get_class($model))
                ->delete();
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function saldoYayasan()
    {
        return $this->morphOne(SaldoYayasan::class, 'referensi');
    }
}

