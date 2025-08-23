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
            ->setPaper([0, 0, 595.28, 311.81]) // 21cm x 11cm in points (21*28.35, 11*28.35)
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream("Invoice-{$pembayaran->id}.pdf");
    }
}
