<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('log_transaksi_koperasis', function (Blueprint $table) {
            $table->id('id_log');
            $table->dateTime('tanggal');
            $table->enum('jenis', ['pinjaman','angsuran','saldo']);
            $table->text('keterangan')->nullable();
            $table->decimal('nominal', 12, 2)->nullable();
            $table->foreignId('user_id')
        ->constrained('users')
        ->onUpdate('cascade')
        ->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('log_transaksi_koperasi');
    }
};
