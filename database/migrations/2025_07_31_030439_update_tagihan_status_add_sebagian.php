<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Update ENUM untuk menambahkan 'sebagian'
            DB::statement("ALTER TABLE tagihan MODIFY COLUMN status ENUM('belum_bayar', 'sebagian', 'lunas') DEFAULT 'belum_bayar'");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tagihan', function (Blueprint $table) {
            // Kembalikan ke ENUM asli
            DB::statement("ALTER TABLE tagihan MODIFY COLUMN status ENUM('belum_bayar', 'lunas') DEFAULT 'belum_bayar'");
        });
    }
};
