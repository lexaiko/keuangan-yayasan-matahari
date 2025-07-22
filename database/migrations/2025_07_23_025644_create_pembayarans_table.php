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
    $table->foreignUuid('siswa_id')->constrained('siswas')->onDelete('cascade');
    $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // admin/operator
    $table->date('tanggal_bayar');
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
