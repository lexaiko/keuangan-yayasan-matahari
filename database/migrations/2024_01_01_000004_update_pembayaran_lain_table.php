<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pembayaran_lain', function (Blueprint $table) {
            // Tambah kolom siswa_id hanya jika belum ada
            if (!Schema::hasColumn('pembayaran_lain', 'siswa_id')) {
                $table->unsignedBigInteger('siswa_id')->nullable()->after('jenis_pembayaran_lain_id');
            }

            // Drop kolom yang tidak diperlukan jika ada
            if (Schema::hasColumn('pembayaran_lain', 'metode_pembayaran')) {
                $table->dropColumn('metode_pembayaran');
            }
            if (Schema::hasColumn('pembayaran_lain', 'bukti_pembayaran')) {
                $table->dropColumn('bukti_pembayaran');
            }
            if (Schema::hasColumn('pembayaran_lain', 'status')) {
                $table->dropColumn('status');
            }
        });

        // Tambah foreign key constraint setelah tabel dibuat
        // Hanya jika tabel siswa sudah ada dan constraint belum ada
        if (Schema::hasTable('siswa') && Schema::hasColumn('pembayaran_lain', 'siswa_id')) {
            try {
                Schema::table('pembayaran_lain', function (Blueprint $table) {
                    $table->foreign('siswa_id')->references('id')->on('siswa')->onDelete('set null');
                });
            } catch (\Exception $e) {
                // Foreign key constraint mungkin sudah ada, skip
            }
        }
    }

    public function down(): void
    {
        Schema::table('pembayaran_lain', function (Blueprint $table) {
            // Drop foreign key jika ada
            try {
                $table->dropForeign(['siswa_id']);
            } catch (\Exception $e) {
                // Foreign key mungkin tidak ada, skip
            }

            if (Schema::hasColumn('pembayaran_lain', 'siswa_id')) {
                $table->dropColumn('siswa_id');
            }

            // Tambah kembali kolom yang dihapus
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'qris', 'lainnya'])->after('tanggal_pembayaran');
            $table->string('bukti_pembayaran')->nullable()->after('metode_pembayaran');
            $table->enum('status', ['pending', 'lunas', 'batal'])->default('pending')->after('bukti_pembayaran');
        });
    }
};
