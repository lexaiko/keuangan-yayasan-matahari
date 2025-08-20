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
            padding: 15px;
            font-size: 11px;
            line-height: 1.3;
        }

        .container {
            width: 100%;
            max-width: 1000px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }

        .header h1 {
            margin: 0;
            font-size: 18px;
            color: #333;
        }

        .header .address {
            font-size: 10px;
            margin: 5px 0;
            color: #666;
        }

        .invoice-title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 10px 0;
            text-transform: uppercase;
        }

        .invoice-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .left-info, .right-info {
            width: 48%;
        }

        .info-table {
            width: 100%;
            font-size: 11px;
        }

        .info-table td {
            padding: 2px 5px;
            vertical-align: top;
        }

        .info-table .label {
            width: 120px;
            font-weight: bold;
        }

        .info-table .colon {
            width: 10px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #333;
            padding: 5px;
            text-align: left;
        }

        .payment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
        }

        .payment-table .text-center {
            text-align: center;
        }

        .payment-table .text-right {
            text-align: right;
        }

        .notes-section {
            margin: 15px 0;
        }

        .notes-label {
            font-weight: bold;
            margin-bottom: 5px;
        }

        .total-section {
            margin-top: 15px;
            float: right;
            width: 250px;
        }

        .total-table {
            width: 100%;
            font-size: 11px;
        }

        .total-table td {
            padding: 3px 5px;
        }

        .total-table .total-label {
            text-align: right;
            width: 60%;
        }

        .total-table .total-amount {
            text-align: right;
            width: 40%;
            border-bottom: 1px solid #333;
        }

        .grand-total {
            font-weight: bold;
            font-size: 12px;
        }

        .signature-section {
            clear: both;
            margin-top: 30px;
            text-align: right;
            margin-right: 50px;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            width: 200px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin: 40px 0 5px 0;
        }

        .footer-note {
            text-align: center;
            font-size: 9px;
            color: #666;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1>YAYASAN MATAHARI</h1>
            <div class="address">Perum Taman Puring Asri Blok G No 10-12 Kel. Sobo Banyuwangi 68418</div>
            <div class="address">Telp/HP: 082337349209 Email: yayasanmatahari@gmail.com</div>
        </div>

        <!-- Invoice Title -->
        <div class="invoice-title">BUKTI PEMBAYARAN</div>

        <!-- Invoice Info -->
        <div class="invoice-info">
            <div class="left-info">
                <table class="info-table">
                    <tr>
                        <td class="label">NO.</td>
                        <td class="colon">:</td>
                        <td>{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="label">TANGGAL</td>
                        <td class="colon">:</td>
                        <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d F Y') }}</td>
                    </tr>
                </table>
            </div>
            <div class="right-info">
                <table class="info-table">
                    <tr>
                        <td class="label">NAMA</td>
                        <td class="colon">:</td>
                        <td>{{ $pembayaran->siswa->nama }} ({{ $pembayaran->siswa->nis }})</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment Details Table -->
        <table class="payment-table">
            <thead>
                <tr>
                    <th width="5%">#</th>
                    <th width="45%">URAIAN PEMBAYARAN</th>
                    <th width="50%">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pembayaran->detailPembayarans as $index => $detail)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $detail->tagihan->jenisPembayaran->nama_pembayaran ?? '-' }}@if($detail->tagihan->bulan) - {{ $detail->tagihan->bulan }}@endif</td>
                    <td class="text-right">Rp {{ number_format($detail->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Notes Section -->
        @if($pembayaran->keterangan)
        <div class="notes-section">
            <div class="notes-label">Terbilang:</div>
            <div>{{ $pembayaran->keterangan }}</div>
        </div>
        @endif

        <!-- Total Section -->
        <div class="total-section">
            <table class="total-table">
                <tr>
                    <td class="total-label">Grand Total :</td>
                    <td class="total-amount">Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div>{{ \Carbon\Carbon::parse($pembayaran->tanggal_bayar)->format('d F Y') }}</div>
                <div>Petugas Administrasi,</div>
                <div class="signature-line"></div>
                <div>{{ auth()->user()->name ?? 'Administrator' }}</div>
            </div>
        </div>

        <!-- Footer -->
        {{-- <div class="footer-note">
            <p>Invoice ini dicetak secara otomatis pada {{ now()->format('d/m/Y H:i:s') }}</p>
            <p>Terima kasih atas pembayaran Anda</p>
        </div> --}}
    </div>
</body>
</html>
</html>
        </div>
    </div>
</body>
</html>
