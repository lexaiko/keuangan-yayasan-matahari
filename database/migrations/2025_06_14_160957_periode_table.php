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
        Schema::create('tahun_akademiks', function (Blueprint $table) {
            $table->uuid('id')->primary(); // id (primary key)
            $table->string('nama')->unique(); // Nama periode, misal "2025/2026"
            $table->date('mulai'); // Tanggal mulai periode
            $table->date('selesai'); // Tanggal akhir periode
            $table->boolean('is_active')->default(false); // Tanggal akhir periode
            $table->timestamps(); // Timestamps untuk created_at dan updated_at
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tahun_akademiks');
    }
};
