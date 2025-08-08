{{-- filepath: c:\coding\laravel\kaido\resources\views\pdf\invoice.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembayaran - {{ $pembayaran->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            font-size: 12px;
            line-height: 1.4;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }

        .header h1 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .header h2 {
            margin: 5px 0;
            font-size: 18px;
            color: #666;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 30px;
        }

        .invoice-info div {
            width: 48%;
        }

        .invoice-number {
            text-align: right;
            font-size: 14px;
            font-weight: bold;
            color: #333;
        }

        .student-info h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #333;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .info-row {
            margin-bottom: 5px;
        }

        .info-label {
            display: inline-block;
            width: 120px;
            font-weight: bold;
        }

        .table-container {
            margin: 20px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th,
        table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .total-section {
            margin-top: 20px;
            border-top: 2px solid #333;
            padding-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .total-row.grand-total {
            font-weight: bold;
            font-size: 16px;
            border-top: 1px solid #333;
            padding-top: 8px;
        }

        .footer {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
        }

        .signature {
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin-top: 60px;
            margin-bottom: 5px;
        }

        .notes {
            margin-top: 30px;
            padding: 15px;
            background-color: #f8f9fa;
            border-left: 4px solid #007bff;
        }

        .notes h4 {
            margin: 0 0 10px 0;
            color: #333;
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .badge-success {
            background-color: #d4edda;
            color: #155724;
        }

        .badge-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>YAYASAN MATAHARI</h1>
        <h2>INVOICE PEMBAYARAN</h2>
        <p>Jl. Pendidikan No. 123, Kaido | Telp: (0271) 123456</p>
    </div>

    <div class="invoice-info">
        <div class="student-info">
            <h3>Informasi Siswa</h3>
            <div class="info-row">
                <span class="info-label">Nama Siswa:</span>
                <span>{{ $pembayaran->siswa->nama }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">NIS:</span>
                <span>{{ $pembayaran->siswa->nis }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Kelas:</span>
                <span>{{ $pembayaran->siswa->kelas->nama ?? '-' }} ({{ $pembayaran->siswa->kelas->tingkat->nama ?? '-' }})</span>
            </div>
            <div class="info-row">
                <span class="info-label">Tahun Akademik:</span>
                <span>{{ $pembayaran->siswa->kelas->tahun->nama ?? '-' }}</span>
            </div>
        </div>

        <div class="invoice-number">
            <h3>Invoice #{{ $pembayaran->id }}</h3>
            <div class="info-row">
                <span class="info-label">Tanggal Bayar:</span>
                <span>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Petugas:</span>
                <span>{{ $pembayaran->user->name ?? 'Admin' }}</span>
            </div>
        </div>
    </div>

    <div class="table-container">
        <h3>Detail Pembayaran</h3>
        <table>
            <thead>
                <tr>
                    <th width="5%">No</th>
                    <th width="25%">Jenis Pembayaran</th>
                    <th width="15%">Bulan</th>
                    <th width="20%">Total Tagihan</th>
                    <th width="20%">Jumlah Bayar</th>
                    <th width="15%">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembayaran->detailPembayarans as $index => $detail)
                    @php
                        $totalTagihan = $detail->tagihan->jumlah;
                        $totalDibayar = $detail->tagihan->detailPembayarans()->sum('jumlah_bayar');
                        $status = $totalDibayar >= $totalTagihan ? 'Lunas' : ($totalDibayar > 0 ? 'Sebagian' : 'Belum Bayar');
                        $statusClass = $totalDibayar >= $totalTagihan ? 'badge-success' : ($totalDibayar > 0 ? 'badge-warning' : 'badge-danger');
                    @endphp
                    <tr>
                        <td class="text-center">{{ $index + 1 }}</td>
                        <td>{{ $detail->tagihan->jenisPembayaran->nama_pembayaran ?? '-' }}</td>
                        <td class="text-center">{{ $detail->tagihan->bulan ?? '-' }}</td>
                        <td class="text-right">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                        <td class="text-right">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</td>
                        <td class="text-center">
                            <span class="badge {{ $statusClass }}">{{ $status }}</span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="total-section">
        <div class="total-row">
            <span>Subtotal:</span>
            <span>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
        </div>
        <div class="total-row grand-total">
            <span>Total Bayar:</span>
            <span>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Uang Tunai:</span>
            <span>Rp {{ number_format($pembayaran->tunai, 0, ',', '.') }}</span>
        </div>
        <div class="total-row">
            <span>Kembalian:</span>
            <span>Rp {{ number_format($pembayaran->kembalian, 0, ',', '.') }}</span>
        </div>
    </div>

    @if($pembayaran->keterangan)
        <div class="notes">
            <h4>Keterangan:</h4>
            <p>{{ $pembayaran->keterangan }}</p>
        </div>
    @endif

    <div class="footer">
        <div class="signature">
            <p>Siswa/Wali Siswa</p>
            <div class="signature-line"></div>
            <p>{{ $pembayaran->siswa->nama }}</p>
        </div>

        <div class="signature">
            <p>Petugas Keuangan</p>
            <div class="signature-line"></div>
            <p>{{ $pembayaran->user->name ?? 'Admin' }}</p>
        </div>
    </div>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #666;">
        <p>Invoice ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>Terima kasih atas pembayaran Anda</p>
    </div>
</body>
</html>
