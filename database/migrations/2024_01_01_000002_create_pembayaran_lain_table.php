<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran_lain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pembayaran_lain_id')->constrained('jenis_pembayaran_lain');
            $table->string('nama_pembayar');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_pembayaran');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris', 'lainnya']);
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status', ['pending', 'lunas', 'batal'])->default('pending');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_lain');
    }
};
