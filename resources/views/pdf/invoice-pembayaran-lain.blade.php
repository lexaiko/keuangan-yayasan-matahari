{{-- filepath: c:\coding\laravel\kaido\resources\views\pdf\invoice-pembayaran-lain.blade.php --}}
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Pembayaran Lain - {{ $pembayaran->id }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 8px;
            font-size: 9px;
            line-height: 1.2;
        }

        .container {
            width: 100%;
            max-width: 580px;
            margin: 0 auto;
        }

        .header {
            text-align: center;
            border-bottom: 1px solid #333;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }

        .header h1 {
            margin: 0;
            font-size: 12px;
            color: #333;
        }

        .header .address {
            font-size: 7px;
            margin: 2px 0;
            color: #666;
        }

        .invoice-title {
            text-align: center;
            font-size: 11px;
            font-weight: bold;
            margin: 5px 0;
            text-transform: uppercase;
        }

        .invoice-info {
            display: table;
            width: 100%;
            margin-bottom: 8px;
        }

        .left-info, .right-info {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }

        .info-table {
            width: 100%;
            font-size: 8px;
        }

        .info-table td {
            padding: 1px 3px;
            vertical-align: top;
        }

        .info-table .label {
            width: 60px;
            font-weight: bold;
        }

        .info-table .colon {
            width: 8px;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            margin: 8px 0;
            font-size: 8px;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #333;
            padding: 3px;
            text-align: left;
        }

        .payment-table th {
            background-color: #f0f0f0;
            font-weight: bold;
            text-align: center;
            font-size: 7px;
        }

        .payment-table .text-center {
            text-align: center;
        }

        .payment-table .text-right {
            text-align: right;
        }

        .notes-section {
            margin: 8px 0;
            font-size: 7px;
        }

        .notes-label {
            font-weight: bold;
            margin-bottom: 2px;
        }

        .total-section {
            margin-top: 8px;
            float: right;
            width: 180px;
        }

        .total-table {
            width: 100%;
            font-size: 8px;
        }

        .total-table td {
            padding: 2px 3px;
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
            font-size: 9px;
        }

        .signature-section {
            clear: both;
            margin-top: 15px;
            text-align: right;
            margin-right: 30px;
        }

        .signature-box {
            display: inline-block;
            text-align: center;
            width: 120px;
            font-size: 7px;
        }

        .signature-line {
            border-bottom: 1px solid #333;
            margin: 20px 0 3px 0;
        }

        .footer-note {
            text-align: center;
            font-size: 6px;
            color: #666;
            margin-top: 10px;
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
                        <td>PL-{{ str_pad($pembayaran->id, 6, '0', STR_PAD_LEFT) }}</td>
                    </tr>
                    <tr>
                        <td class="label">TANGGAL</td>
                        <td class="colon">:</td>
                        <td>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}</td>
                    </tr>
                </table>
            </div>
            <div class="right-info">
                <table class="info-table">
                    <tr>
                        <td class="label">NAMA</td>
                        <td class="colon">:</td>
                        <td>{{ $pembayaran->nama_pembayar }}</td>
                    </tr>
                    <tr>
                        <td class="label">Jenis Pembayaran</td>
                        <td class="colon">:</td>
                        <td>{{ $pembayaran->jenisPembayaranLain->nama_jenis }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Payment Details Table -->
        <table class="payment-table">
            <thead>
                <tr>
                    <th width="8%">#</th>
                    <th width="62%">URAIAN PEMBAYARAN</th>
                    <th width="30%">JUMLAH</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center">1</td>
                    <td>{{ $pembayaran->jenisPembayaranLain->nama_jenis }}</td>
                    <td class="text-right">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Notes Section -->
        @if($pembayaran->keterangan)
        <div class="notes-section">
            <div class="notes-label">Keterangan:</div>
            <div>{{ $pembayaran->keterangan }}</div>
        </div>
        @endif

        <!-- Total Section -->
        <div class="total-section">
            <table class="total-table">
                <tr class="grand-total">
                    <td class="total-label">Grand Total :</td>
                    <td class="total-amount">Rp {{ number_format($pembayaran->jumlah, 0, ',', '.') }}</td>
                </tr>
            </table>
        </div>

        <!-- Signature Section -->
        <div class="signature-section">
            <div class="signature-box">
                <div>{{ \Carbon\Carbon::parse($pembayaran->tanggal_pembayaran)->format('d/m/Y') }}</div>
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
