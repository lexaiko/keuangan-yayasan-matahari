<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('saldo_koperasis', function (Blueprint $table) {
            $table->id('id_saldo');
            $table->foreignId('pelaku_terkait_id')->constrained('users');
            $table->enum('kategori', ['tabungan','pinjaman']);
            $table->datetime('tanggal')->default(now());
            $table->enum('tipe', ['masuk', 'keluar']);
            $table->decimal('jumlah', 12, 2);
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('saldo_koperasis');
    }
};
