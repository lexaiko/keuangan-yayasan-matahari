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
        Schema::table('pembayarans', function (Blueprint $table) {
            // Drop kolom tagihan_id karena sudah pindah ke detail_pembayarans
            $table->dropForeign(['tagihan_id']);
            $table->dropColumn('tagihan_id');
            
            // Ubah tunai dari boolean ke bigInteger
            $table->dropColumn('tunai');
            $table->bigInteger('tunai')->default(0)->after('jumlah_bayar');
            
            // Tambah kolom kembalian
            $table->bigInteger('kembalian')->default(0)->after('tunai');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pembayarans', function (Blueprint $table) {
            // Kembalikan ke struktur asli
            $table->dropColumn(['tunai', 'kembalian']);
            $table->boolean('tunai')->default(false);
            $table->foreignId('tagihan_id')->constrained('tagihan')->onDelete('cascade');
        });
    }
};
