<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('gaji_bulanan', 15, 2)->default(0)->after('password');
            $table->boolean('is_pegawai')->default(true)->after('gaji_bulanan');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['gaji_bulanan', 'is_pegawai']);
        });
    }
};
