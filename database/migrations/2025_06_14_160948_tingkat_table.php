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
        Schema::create('tingkats', function (Blueprint $table) {
            $table->uuid('id')->primary(); // id (primary key)
            $table->string('nama')->unique(); // Nama tingkat, misal "10", "11", "12"
            $table->timestamps(); // Timestamps untuk created_at dan updated_at
        });
        //
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tingkats');
        //
    }
};
