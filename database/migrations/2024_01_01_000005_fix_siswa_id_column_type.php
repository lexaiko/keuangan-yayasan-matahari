<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cek apakah ada foreign key constraints yang perlu di-drop
        $foreignKeys = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
            AND TABLE_NAME = 'pembayaran_lain'
            AND COLUMN_NAME = 'siswa_id'
            AND REFERENCED_TABLE_NAME IS NOT NULL
        ");

        Schema::table('pembayaran_lain', function (Blueprint $table) use ($foreignKeys) {
            // Drop foreign key constraints jika ada
            foreach ($foreignKeys as $fk) {
                try {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                } catch (\Exception $e) {
                    // Skip jika error
                }
            }

            // Drop kolom siswa_id yang lama jika ada
            if (Schema::hasColumn('pembayaran_lain', 'siswa_id')) {
                $table->dropColumn('siswa_id');
            }
        });

        // Tambah kolom baru dengan tipe yang benar
        Schema::table('pembayaran_lain', function (Blueprint $table) {
            $table->string('siswa_id', 36)->nullable()->after('jenis_pembayaran_lain_id');
        });

        // Tambah foreign key constraint jika tabel siswa ada
        if (Schema::hasTable('siswa')) {
            try {
                Schema::table('pembayaran_lain', function (Blueprint $table) {
                    $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Skip jika ada error
            }
        }
    }

    public function down(): void
    {
        Schema::table('pembayaran_lain', function (Blueprint $table) {
            // Drop foreign key jika ada
            $foreignKeys = DB::select("
                SELECT CONSTRAINT_NAME
                FROM information_schema.KEY_COLUMN_USAGE
                WHERE TABLE_SCHEMA = DATABASE()
                AND TABLE_NAME = 'pembayaran_lain'
                AND COLUMN_NAME = 'siswa_id'
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");

            foreach ($foreignKeys as $fk) {
                try {
                    $table->dropForeign($fk->CONSTRAINT_NAME);
                } catch (\Exception $e) {
                    // Skip
                }
            }

            if (Schema::hasColumn('pembayaran_lain', 'siswa_id')) {
                $table->dropColumn('siswa_id');
            }

            // Tambah kembali dengan tipe bigint
            $table->unsignedBigInteger('siswa_id')->nullable()->after('jenis_pembayaran_lain_id');
        });
    }
};
