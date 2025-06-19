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
        Schema::create('kelas', function (Blueprint $table) {
            $table->uuid('id')->primary(); // id (primary key)
            $table->string('nama')->unique(); // Nama kelas, misal "Kelas 10 A"
            $table->uuid('tingkat_id'); // Kolom UUID foreign key ke tabel tingkats
            $table->uuid('tahun_id');
            $table->foreign('tingkat_id')
                ->references('id')
                ->on('tingkats')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->foreign('tahun_id')
                ->references('id')
                ->on('tahun_akademiks')
                ->onUpdate('cascade')
                ->onDelete('cascade');
            $table->timestamps(); // Timestamps untuk created_at dan updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kelas');
        //
    }
};
