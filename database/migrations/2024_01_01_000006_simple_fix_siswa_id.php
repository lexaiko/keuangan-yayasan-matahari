<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Hapus tabel dan buat ulang dengan struktur yang benar
        Schema::dropIfExists('pembayaran_lain');

        Schema::create('pembayaran_lain', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jenis_pembayaran_lain_id')->constrained('jenis_pembayaran_lain')->onDelete('cascade');
            $table->string('siswa_id', 36)->nullable();
            $table->string('nama_pembayar');
            $table->decimal('jumlah', 15, 2);
            $table->date('tanggal_pembayaran');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            // Foreign key ke siswa jika tabel ada
            if (Schema::hasTable('siswa')) {
                $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('set null');
            }
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_lain');
    }
};
