<?php

namespace App\Http\Controllers;

use App\Models\PembayaranLain;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PembayaranLainController extends Controller
{
    public function printInvoice($id)
    {
        $pembayaran = PembayaranLain::with([
            'siswa',
            'jenisPembayaranLain'
        ])->findOrFail($id);

        $pdf = Pdf::loadView('pdf.invoice-pembayaran-lain', compact('pembayaran'))
            ->setPaper([0, 0, 595.28, 311.81]) // 21cm x 11cm in points (1cm ≈ 28.35pt)
            ->setOptions([
                'defaultFont' => 'sans-serif',
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true
            ]);

        return $pdf->stream("Invoice-Pembayaran-Lain-{$pembayaran->id}.pdf");
    }
}
