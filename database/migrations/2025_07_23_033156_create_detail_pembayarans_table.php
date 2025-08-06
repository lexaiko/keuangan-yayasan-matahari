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
        Schema::create('detail_pembayarans', function (Blueprint $table) {
    $table->id();
    $table->foreignUuid('pembayaran_id')->constrained('pembayarans')->onDelete('cascade');
    $table->foreignId('tagihan_id')->constrained('tagihan')->onDelete('cascade');
    $table->integer('jumlah_bayar');
    $table->string('keterangan')->nullable(); // opsional, misal: "Diskon 10%", dsb
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pembayarans');
    }
};
