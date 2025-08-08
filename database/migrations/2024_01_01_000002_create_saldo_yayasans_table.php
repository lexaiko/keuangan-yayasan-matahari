<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saldo_yayasans', function (Blueprint $table) {
            $table->id();
            $table->enum('jenis_transaksi', ['pendapatan', 'pengeluaran']);
            $table->string('kategori'); // SPP, Donasi, Gaji Pegawai, Operasional, dll
            $table->decimal('jumlah', 15, 2);
            $table->text('keterangan');
            $table->date('tanggal_transaksi');
            $table->foreignId('user_id')->constrained(); // user yang mencatat
            $table->string('referensi_id')->nullable(); // ID pembayaran/gaji yang terkait
            $table->string('referensi_tipe')->nullable(); // App\Models\Pembayaran, App\Models\GajiPegawai
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('saldo_yayasans');
    }
};
