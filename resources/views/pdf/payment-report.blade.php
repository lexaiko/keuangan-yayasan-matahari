<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Rekap Pembayaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            font-size: 8px;
            line-height: 1.0;
        }

        .header {
            text-align: center;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 14px;
            text-transform: uppercase;
            font-weight: bold;
            line-height: 1.1;
        }

        .header h2 {
            margin: 3px 0;
            font-size: 12px;
            font-weight: bold;
            line-height: 1.1;
        }

        .header p {
            margin: 2px 0;
            font-size: 9px;
        }

        .info-section {
            margin-bottom: 10px;
            font-size: 10px;
        }

        .info-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }

        .info-label {
            font-weight: bold;
            width: 150px;
        }

        .report-table {
            width: 100%;
            border-collapse: collapse;
            margin: 10px 0;
            font-size: 7px;
            line-height: 1.0;
        }

        .report-table th,
        .report-table td {
            border: 1px solid #333;
            padding: 2px;
            text-align: left;
            vertical-align: top;
        }

        .report-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
            line-height: 1.0;
        }

        .report-table .text-center {
            text-align: center;
        }

        .report-table .text-right {
            text-align: right;
        }

        .total-section {
            margin-top: 15px;
            text-align: right;
        }

        .total-row {
            margin-bottom: 3px;
            font-size: 10px;
        }

        .grand-total {
            font-weight: bold;
            font-size: 12px;
            border-top: 2px solid #333;
            padding-top: 3px;
        }

        .footer {
            margin-top: 20px;
            text-align: right;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            width: 150px;
            font-size: 8px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin: 30px 0 3px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN REKAP PEMBAYARAN YAYASAN MATAHARI BANYUWANGI</h1>
    </div>

    <div class="info-section">
        <div class="info-row">
            <span class="info-label">Jenis Pembayaran:</span>
            <span>{{ $jenisPembayaran ? $jenisPembayaran->nama_pembayaran : 'Semua Jenis Pembayaran' }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Dari Tanggal:</span>
            <span>{{ $dateRange }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Jumlah Transaksi:</span>
            <span>{{ $pembayarans->count() }} transaksi</span>
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th width="3%">No</th>
                <th width="6%">Kode</th>
                <th width="8%">Tanggal</th>
                <th width="18%">Nama Siswa</th>
                <th width="22%">Jenis Pembayaran</th>
                <th width="12%">Nominal</th>
                <th width="21%">Keterangan</th>
                <th width="10%">Petugas</th>
            </tr>
        </thead>
        <tbody>
            @forelse($pembayarans as $index => $pembayaran)
                @foreach($pembayaran->detailPembayarans as $detailIndex => $detail)
                <tr>
                    @if($detailIndex === 0)
                        <td class="text-center" rowspan="{{ $pembayaran->detailPembayarans->count() }}">{{ $index + 1 }}</td>
                        <td class="text-center" rowspan="{{ $pembayaran->detailPembayarans->count() }}">{{ str_pad($pembayaran->id, 5, '0', STR_PAD_LEFT) }}</td>
                        <td class="text-center" rowspan="{{ $pembayaran->detailPembayarans->count() }}">{{ Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d/m/Y') }}</td>
                        <td rowspan="{{ $pembayaran->detailPembayarans->count() }}">{{ $pembayaran->siswa->nama }}</td>
                    @endif
                    <td>{{ $detail->tagihan->jenisPembayaran->nama_pembayaran ?? '-' }}@if($detail->tagihan->bulan) - {{ $detail->tagihan->bulan }}@endif</td>
                    <td class="text-right">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</td>
                    @if($detailIndex === 0)
                        <td rowspan="{{ $pembayaran->detailPembayarans->count() }}">{{ $pembayaran->keterangan ?? '-' }}</td>
                        <td rowspan="{{ $pembayaran->detailPembayarans->count() }}">{{ $pembayaran->user->name ?? 'Admin' }}</td>
                    @endif
                </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="8" class="text-center">Tidak ada data pembayaran pada periode ini</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="total-section">
        <div class="total-row">
            <span>JUMLAH TOTAL: Rp {{ number_format($totalAmount, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- <div class="footer">
        <div class="signature-box">
            <div>{{ now()->format('d F Y') }}</div>
            <div>Petugas Administrasi,</div>
            <div class="signature-line"></div>
            <div>{{ auth()->user()->name ?? 'Administrator' }}</div>
        </div>
    </div> --}}

    <div style="margin-top: 15px; text-align: center; font-size: 6px; color: #666;">
        <p>Laporan ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</body>
</html>
