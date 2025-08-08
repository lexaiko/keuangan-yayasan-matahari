<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranController extends Controller
{
    public function printInvoice($id)
    {
        $pembayaran = Pembayaran::with([
            'siswa.kelas.tingkat',
            'siswa.kelas.tahun',
            'detailPembayarans.tagihan.jenisPembayaran',
            'user'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice', compact('pembayaran'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream("Invoice-{$pembayaran->id}.pdf");
    }
}
