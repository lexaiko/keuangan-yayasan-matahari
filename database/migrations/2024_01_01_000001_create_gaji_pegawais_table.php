<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('gaji_pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('bulan'); // Januari, Februari, dst
            $table->year('tahun');
            $table->decimal('total_gaji', 15, 2);
            $table->enum('status', ['pending', 'dibayar', 'ditunda'])->default('pending');
            $table->date('tanggal_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'bulan', 'tahun']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('gaji_pegawais');
    }
};
