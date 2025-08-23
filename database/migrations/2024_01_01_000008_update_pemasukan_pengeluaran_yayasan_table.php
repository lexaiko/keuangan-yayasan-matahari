<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pemasukan_pengeluaran_yayasan')) {
            Schema::table('pemasukan_pengeluaran_yayasan', function (Blueprint $table) {
                // Drop kolom kategori lama dan bukti_transaksi
                if (Schema::hasColumn('pemasukan_pengeluaran_yayasan', 'kategori')) {
                    $table->dropColumn('kategori');
                }
                if (Schema::hasColumn('pemasukan_pengeluaran_yayasan', 'bukti_transaksi')) {
                    $table->dropColumn('bukti_transaksi');
                }

                // Tambah foreign key ke kategori dengan nama constraint yang lebih pendek
                $table->foreignId('kategori_id')
                    ->after('jenis_transaksi')
                    ->constrained('kategori_pemasukan_pengeluaran')
                    ->onDelete('cascade');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('pemasukan_pengeluaran_yayasan')) {
            Schema::table('pemasukan_pengeluaran_yayasan', function (Blueprint $table) {
                // Cek dan drop foreign key jika ada
                $foreignKeys = DB::select("
                    SELECT CONSTRAINT_NAME
                    FROM information_schema.KEY_COLUMN_USAGE
                    WHERE TABLE_SCHEMA = DATABASE()
                    AND TABLE_NAME = 'pemasukan_pengeluaran_yayasan'
                    AND COLUMN_NAME = 'kategori_id'
                    AND REFERENCED_TABLE_NAME IS NOT NULL
                ");

                foreach ($foreignKeys as $fk) {
                    try {
                        $table->dropForeign($fk->CONSTRAINT_NAME);
                    } catch (\Exception $e) {
                        // Skip jika foreign key tidak ada
                    }
                }

                if (Schema::hasColumn('pemasukan_pengeluaran_yayasan', 'kategori_id')) {
                    $table->dropColumn('kategori_id');
                }

                $table->string('kategori')->after('jenis_transaksi');
                $table->string('bukti_transaksi')->nullable()->after('tanggal_transaksi');
            });
        }
    }
};
