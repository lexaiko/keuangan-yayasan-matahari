<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Tagihan Siswa</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .school-info {
            font-size: 16px;
            font-weight: bold;
        }
        .student-info {
            margin-bottom: 20px;
        }
        .student-info table {
            width: 100%;
        }
        .student-info td {
            padding: 3px 0;
        }
        .tagihan-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .tagihan-table th,
        .tagihan-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        .tagihan-table th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .total-row {
            font-weight: bold;
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
        }
        .status-badge {
            font-size: 10px;
            font-weight: bold;
        }
        .status-belum-bayar {
            color: #c00;
        }
        .status-sebagian {
            color: #e17055;
        }
        .overdue {
            color: #d63031;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="school-info">Yayasan Matahari Banyuwangi</div>
        <div>Perum Taman Puring Asri Blok G No 10-12 Kel. Sobo Banyuwangi 68418</div>
        <div>Telp/HP: 082337349209 Email: yayasanmatahariB@yahoo.com</div>
        <h2 style="margin: 10px 0;">TAGIHAN SISWA</h2>
    </div>

    <div class="student-info">
        <table>
            <tr>
                <td width="100">Nama Siswa</td>
                <td width="10">:</td>
                <td><strong>{{ $siswa->nama }}</strong></td>
                <td width="100">Tanggal Cetak</td>
                <td width="10">:</td>
                <td>{{ $tanggal_cetak }}</td>
            </tr>
            <tr>
                <td>NIS</td>
                <td>:</td>
                <td>{{ $siswa->nis }}</td>
                <td>Kelas</td>
                <td>:</td>
                <td>{{ $siswa->kelas->nama ?? '-' }}</td>
            </tr>
            @if($siswa->nisn)
            <tr>
                <td>NISN</td>
                <td>:</td>
                <td>{{ $siswa->nisn }}</td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            @endif
        </table>
    </div>

    <table class="tagihan-table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="35%">Jenis Pembayaran</th>
                <th width="15%">Total Tagihan</th>
                <th width="15%">Sudah Dibayar</th>
                <th width="15%">Sisa Tagihan</th>
                <th width="10%">Jatuh Tempo</th>
                <th width="5%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tagihans as $index => $tagihan)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>
                    {{ $tagihan->jenisPembayaran->nama_pembayaran }}
                    @if($tagihan->bulan)
                        - {{ strtoupper($tagihan->bulan) }}
                    @endif
                </td>
                <td class="text-right">Rp {{ number_format($tagihan->jumlah, 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tagihan->detailPembayarans->sum('jumlah_bayar'), 0, ',', '.') }}</td>
                <td class="text-right">Rp {{ number_format($tagihan->sisa_tagihan, 0, ',', '.') }}</td>
                <td class="text-center {{ $tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast() ? 'overdue' : '' }}">
                    {{ $tagihan->tanggal_jatuh_tempo ? \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->format('d/m/Y') : '-' }}
                    @if($tagihan->tanggal_jatuh_tempo && \Carbon\Carbon::parse($tagihan->tanggal_jatuh_tempo)->isPast())
                        <br><small>(Terlambat)</small>
                    @endif
                </td>
                <td class="text-center">
                    <span class="status-{{ $tagihan->status == \App\Models\Tagihan::STATUS_BELUM_BAYAR ? 'belum-bayar' : 'sebagian' }}">
                        {{ $tagihan->status == \App\Models\Tagihan::STATUS_BELUM_BAYAR ? 'Belum Bayar' : 'Sebagian' }}
                    </span>
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr class="total-row">
                <td colspan="4" class="text-right"><strong>TOTAL SEMUA TAGIHAN:</strong></td>
                <td class="text-right"><strong>Rp {{ number_format($total_semua, 0, ',', '.') }}</strong></td>
                <td></td>
                <td></td>
            </tr>
        </tfoot>
    </table>

    @if($tagihans->where('tanggal_jatuh_tempo')->where('tanggal_jatuh_tempo', '<', now())->count() > 0)
    <div style="background-color: #fee; padding: 10px; border-left: 4px solid #d63031; margin-bottom: 20px;">
        <strong>Perhatian:</strong> Terdapat tagihan yang sudah melewati jatuh tempo. Harap segera melakukan pembayaran.
    </div>
    @endif

    <div class="footer">
        <p>Dokumen ini dicetak otomatis pada {{ $tanggal_cetak }}</p>
        <p><em>Harap simpan bukti ini sebagai referensi pembayaran</em></p>
    </div>
</body>
</html>

