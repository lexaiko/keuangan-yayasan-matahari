<?php

namespace App\Exports;

use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SiswaExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $filters;

    public function __construct($filters = [])
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Siswa::with(['kelas.tingkat', 'kelas.tahun']);

        // Apply filters if provided
        if (!empty($this->filters['kelas_id'])) {
            $query->where('kelas_id', $this->filters['kelas_id']);
        }

        if (!empty($this->filters['status'])) {
            $query->where('status', $this->filters['status']);
        }

        return $query->orderBy('nama')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama',
            'NIS',
            'NISN',
            'NIK',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Alamat',
            'Nama Ayah',
            'Nama Ibu',
            'Telepon',
            'Email',
            'Kelas',
            'Tingkat',
            'Tahun Akademik',
            'Status',
        ];
    }

    public function map($siswa): array
    {
        static $no = 1;

        $status = match ($siswa->status) {
            '1' => 'Aktif',
            '2' => 'Baru',
            '3' => 'Pindahan',
            '4' => 'Keluar',
            '5' => 'Lulus',
            default => 'Tidak Diketahui',
        };

        $jenisKelamin = $siswa->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan';

        return [
            $no++,
            $siswa->nama,
            $siswa->nis,
            $siswa->nisn,
            $siswa->nik,
            $siswa->tempat_lahir,
            $siswa->tanggal_lahir ? \Carbon\Carbon::parse($siswa->tanggal_lahir)->format('d/m/Y') : '',
            $jenisKelamin,
            $siswa->alamat,
            $siswa->nama_ayah,
            $siswa->nama_ibu,
            $siswa->telepon,
            $siswa->email,
            $siswa->kelas->nama ?? '',
            $siswa->kelas->tingkat->nama ?? '',
            $siswa->kelas->tahun->nama ?? '',
            $status,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FFE2E2E2',
                    ],
                ],
            ],
        ];
    }
}
