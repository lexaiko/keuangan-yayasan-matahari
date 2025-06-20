<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('angsuran_pinjamans', function (Blueprint $table) {
            $table->id('id_angsuran');
            $table->unsignedBigInteger('id_pinjaman');
$table->foreign('id_pinjaman')
    ->references('id_pinjaman')  // pastikan ini benar-benar nama kolom PRIMARY di `pinjaman_users`
    ->on('pinjaman_users')
    ->onUpdate('cascade')
    ->onDelete('cascade');

            $table->unsignedInteger('angsuran_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('jumlah_bayar', 12, 2);
            $table->enum('status', ['belum', 'sudah', 'terlambat'])->default('belum');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('angsuran_pinjamans');
    }
};
