<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummySeeder extends Seeder
{
    public function run(): void
    {
        // 1. User Seeder - Update or Create
        User::updateOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'operator@admin.com'],
            [
                'name' => 'Operator',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        // 2. Tahun Akademik - Update or Create
        DB::table('tahun_akademiks')->updateOrInsert(
            ['nama' => '2024/2025'],
            [
                'id' => Str::uuid(),
                'mulai' => '2024-07-01',
                'selesai' => '2025-06-30',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        DB::table('tahun_akademiks')->updateOrInsert(
            ['nama' => '2025/2026'],
            [
                'id' => Str::uuid(),
                'mulai' => '2025-07-01',
                'selesai' => '2026-06-30',
                'is_active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        // Get tahun aktif ID
        $tahunAktifRecord = DB::table('tahun_akademiks')->where('nama', '2024/2025')->first();

        // 3. Tingkat - Update or Create
        $tingkatData = [
            'Kelas 10' => Str::uuid(),
            'Kelas 11' => Str::uuid(),
            'Kelas 12' => Str::uuid(),
        ];

        foreach ($tingkatData as $nama => $id) {
            DB::table('tingkats')->updateOrInsert(
                ['nama' => $nama],
                [
                    'id' => $id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Get tingkat records
        $tingkats = DB::table('tingkats')->whereIn('nama', array_keys($tingkatData))->get()->keyBy('nama');

        // 4. Kelas - Update or Create
        foreach ($tingkats as $tingkat) {
            $sufiks = ['A', 'B', 'C'];
            foreach ($sufiks as $suf) {
                $namaKelas = $tingkat->nama . ' ' . $suf;
                DB::table('kelas')->updateOrInsert(
                    ['nama' => $namaKelas],
                    [
                        'id' => Str::uuid(),
                        'tingkat_id' => $tingkat->id,
                        'tahun_id' => $tahunAktifRecord->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }

        // 5. Jenis Pembayaran - Update or Create
        $jenisPembayaranData = [
            [
                'nama_pembayaran' => 'SPP',
                'nominal' => 350000,
                'tipe' => 'bulanan',
                'aktif' => true,
            ],
            [
                'nama_pembayaran' => 'Uang Pangkal',
                'nominal' => 2500000,
                'tipe' => 'sekali',
                'aktif' => true,
            ],
            [
                'nama_pembayaran' => 'Seragam',
                'nominal' => 500000,
                'tipe' => 'bebas',
                'aktif' => true,
            ],
            [
                'nama_pembayaran' => 'Buku Paket',
                'nominal' => 750000,
                'tipe' => 'sekali',
                'aktif' => true,
            ],
            [
                'nama_pembayaran' => 'Praktikum',
                'nominal' => 200000,
                'tipe' => 'bulanan',
                'aktif' => true,
            ],
        ];

        foreach ($jenisPembayaranData as $jenis) {
            DB::table('jenis_pembayaran')->updateOrInsert(
                ['nama_pembayaran' => $jenis['nama_pembayaran']],
                array_merge($jenis, [
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }

        // 6. Siswa - Data sample tanpa faker
        $siswaCount = DB::table('siswas')->count();
        if ($siswaCount == 0) {
            $kelasRecords = DB::table('kelas')->get();
            $namaList = [
                'Ahmad Rizki', 'Siti Nurhaliza', 'Budi Santoso', 'Dewi Sartika', 'Arif Rahman',
                'Maya Sari', 'Doni Pratama', 'Lia Amelia', 'Reza Fahlevi', 'Nina Safitri',
                'Eko Wijaya', 'Rina Marlina', 'Fadli Hassan', 'Diah Permata', 'Yoga Aditya'
            ];

            $kotaList = ['Jakarta', 'Bandung', 'Surabaya', 'Medan', 'Semarang', 'Yogyakarta', 'Palembang'];
            $jenisKelamin = ['L', 'P'];

            foreach ($kelasRecords as $index => $kelas) {
                for ($i = 0; $i < 15; $i++) {
                    $nama = $namaList[$i % count($namaList)] . ' ' . ($index + 1);
                    $siswas[] = [
                        'id' => Str::uuid(),
                        'nama' => $nama,
                        'kelas_id' => $kelas->id,
                        'nis' => '2025' . str_pad(($index * 15 + $i + 1), 4, '0', STR_PAD_LEFT),
                        'nisn' => '00' . str_pad(($index * 15 + $i + 1), 8, '0', STR_PAD_LEFT),
                        'nik' => '32' . str_pad(rand(1000000000000, 9999999999999), 14, '0', STR_PAD_LEFT),
                        'tempat_lahir' => $kotaList[array_rand($kotaList)],
                        'tanggal_lahir' => '200' . rand(5, 9) . '-' . str_pad(rand(1, 12), 2, '0', STR_PAD_LEFT) . '-' . str_pad(rand(1, 28), 2, '0', STR_PAD_LEFT),
                        'jenis_kelamin' => $jenisKelamin[array_rand($jenisKelamin)],
                        'alamat' => 'Jalan Contoh No. ' . rand(1, 100),
                        'nama_ayah' => 'Ayah ' . $nama,
                        'nama_ibu' => 'Ibu ' . $nama,
                        'telepon' => '08' . rand(1000000000, 9999999999),
                        'email' => strtolower(str_replace(' ', '.', $nama)) . '@student.com',
                        'status' => 'aktif',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                }
            }

            // Insert siswa in chunks
            collect($siswas)->chunk(50)->each(function ($chunk) {
                DB::table('siswas')->insert($chunk->toArray());
            });
        }

        // 7. Sample Tagihan SPP
        $tagihanCount = DB::table('tagihan')->count();
        if ($tagihanCount == 0) {
            $allSiswas = DB::table('siswas')->get();
            $sppRecord = DB::table('jenis_pembayaran')->where('nama_pembayaran', 'SPP')->first();
            
            if ($sppRecord && $allSiswas->count() > 0) {
                $tagihans = [];
                $bulanArray = [
                    'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
                    'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni'
                ];
                $statusArray = ['belum_bayar', 'lunas'];

                foreach ($allSiswas as $siswa) {
                    foreach ($bulanArray as $bulan) {
                        $tagihans[] = [
                            'siswa_id' => $siswa->id,
                            'jenis_pembayaran_id' => $sppRecord->id,
                            'tahun_akademik_id' => $tahunAktifRecord->id,
                            'bulan' => $bulan,
                            'jumlah' => 350000,
                            'status' => $statusArray[array_rand($statusArray)],
                            'tanggal_jatuh_tempo' => date('Y-m-d', strtotime('+' . rand(1, 30) . ' days')),
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                }

                // Insert tagihan in chunks
                collect($tagihans)->chunk(100)->each(function ($chunk) {
                    DB::table('tagihan')->insert($chunk->toArray());
                });
            }
        }

        $this->command->info('✅ DummySeeder berhasil dijalankan!');
        $this->command->info('📊 Data berhasil dibuat/diupdate tanpa duplikat');
        $this->command->info('🔑 Login: admin@admin.com / password');
        $this->command->info('🔑 Login: operator@admin.com / password');
    }
}