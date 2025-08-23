<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Keuangan</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            margin: 15px;
            line-height: 1.4;
        }
        .header {
            text-align: center;
            margin-bottom: 25px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            font-weight: bold;
            color: #333;
        }
        .header .subtitle {
            margin: 5px 0;
            color: #666;
            font-size: 12px;
        }
        .header .period {
            margin: 10px 0 5px 0;
            font-weight: bold;
            color: #333;
        }
        .summary {
            margin-bottom: 20px;
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            border: 1px solid #e9ecef;
        }
        .summary-grid {
            display: table;
            width: 100%;
        }
        .summary-item {
            display: table-cell;
            text-align: center;
            vertical-align: top;
            width: 33.33%;
            padding: 0 10px;
        }
        .summary-item .label {
            font-weight: bold;
            color: #666;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .summary-item .value {
            font-size: 14px;
            font-weight: bold;
            margin-top: 5px;
        }
        .debit { color: #22c55e; }
        .kredit { color: #ef4444; }
        .saldo { color: #3b82f6; }
        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 10px;
        }
        .table th,
        .table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
        }
        .table th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-align: center;
            font-size: 9px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .table .no-col { width: 30px; text-align: center; }
        .table .dari-col { width: 150px; }
        .table .tanggal-col { width: 70px; text-align: center; }
        .table .debit-col { width: 90px; text-align: right; }
        .table .kredit-col { width: 90px; text-align: right; }
        .table .keterangan-col { width: auto; }
        .table .center {
            text-align: center;
        }
        .total-row {
            background-color: #f8f9fa;
            font-weight: bold;
            border-top: 2px solid #333;
        }
        .saldo-row {
            background-color: #e3f2fd;
            font-weight: bold;
            border-top: 1px solid #2196f3;
        }
        .footer {
            margin-top: 30px;
            text-align: right;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN KEUANGAN</h1>
        <div class="subtitle">Sistem Informasi Keuangan Yayasan</div>
        <div class="period">
            Periode: {{ \Carbon\Carbon::parse($dari_tanggal)->format('d F Y') }} - {{ \Carbon\Carbon::parse($sampai_tanggal)->format('d F Y') }}
        </div>
        <div class="subtitle">Jenis Laporan: {{ ucfirst($jenis_laporan) }}</div>
    </div>


    <table class="table">
        <thead>
            <tr>
                <th class="no-col">No</th>
                <th class="dari-col">Dari</th>
                <th class="tanggal-col">Tanggal</th>
                <th class="debit-col">Debit (Rp)</th>
                <th class="kredit-col">Kredit (Rp)</th>
                <th class="keterangan-col">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $item)
            <tr>
                <td class="center">{{ $item->no }}</td>
                <td>{{ $item->dari }}</td>
                <td class="center">{{ \Carbon\Carbon::parse($item->tanggal)->format('d/m/Y') }}</td>
                <td class="debit-col">
                    {{ $item->debit > 0 ? number_format($item->debit, 0, ',', '.') : '-' }}
                </td>
                <td class="kredit-col">
                    {{ $item->kredit > 0 ? number_format($item->kredit, 0, ',', '.') : '-' }}
                </td>
                <td>{{ $item->keterangan }}</td>
            </tr>
            @endforeach

            <tr class="total-row">
                <td colspan="3" class="center"><strong>TOTAL</strong></td>
                <td class="debit-col"><strong>{{ number_format($total_debit, 0, ',', '.') }}</strong></td>
                <td class="kredit-col"><strong>{{ number_format($total_kredit, 0, ',', '.') }}</strong></td>
                <td></td>
            </tr>

            {{-- <tr class="saldo-row">
                <td colspan="5" class="center"><strong>SALDO AKHIR</strong></td>
                <td>
                    <strong>{{ number_format($saldo_akhir, 0, ',', '.') }}</strong>
                </td>
            </tr> --}}
        </tbody>
    </table>

    <div class="footer">
        <p>Dicetak pada: {{ now()->format('d F Y H:i:s') }} WIB</p>
        <p>Sistem Informasi Keuangan Yayasan</p>
    </div>
</body>
</html>

