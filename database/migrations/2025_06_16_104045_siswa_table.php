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
        Schema::create('siswas', function (Blueprint $table) {
            $table->uuid('id')->primary(); // id (primary key)
            $table->string('nama');
            $table->uuid('kelas_id')->nullable();
           $table->foreign('kelas_id')
    ->references('id')
    ->on('kelas')
    ->onUpdate('cascade')
    ->onDelete('set null');
            $table->string('nis')->unique()->nullable();
            $table->string('nisn')->unique()->nullable();
            $table->string('nik')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['L', 'P'])->nullable();
            $table->text('alamat')->nullable();
            $table->string('nama_ayah')->nullable();
            $table->string('nama_ibu')->nullable();
            $table->string('telepon')->nullable();
            $table->string('email')->nullable();
            $table->string('foto')->nullable(); // path ke file foto
            $table->string('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('siswas');
        //
    }
};
