<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pembayarans', function (Blueprint $table) {
    $table->uuid('id')->primary();
    $table->foreignUuid('siswa_id')->constrained('siswas');
    $table->foreignId('tagihan_id')->constrained('tagihan');
    $table->foreignId('user_id')->constrained('users'); // jangan onDelete
    $table->bigInteger('jumlah_bayar');
    $table->date('tanggal_bayar');
    $table->text('keterangan')->nullable();
    $table->boolean('tunai')->default(false);
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayarans');
    }
};
