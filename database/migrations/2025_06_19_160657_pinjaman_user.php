<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
       Schema::create('pinjaman_users', function (Blueprint $table) {
    $table->id('id_pinjaman');

    // Tambahkan kolom user_id
    $table->foreignId('user_id')
        ->constrained('users')
        ->onUpdate('cascade')
        ->onDelete('cascade');

    $table->date('tanggal_pinjam');
    $table->decimal('jumlah_pinjam', 12, 2);
    $table->decimal('bunga_persen', 5, 2)->default(0);
    $table->decimal('total_kembali', 12, 2);
    $table->unsignedInteger('tenor_bulan');
    $table->enum('status', ['berjalan', 'lunas'])->default('berjalan');
    $table->text('catatan')->nullable();
    $table->timestamps();
});
    }

    public function down(): void {
        Schema::dropIfExists('pinjaman_users');
    }
};
