<?php

namespace App\Http\Controllers;

use App\Models\Pembayaran;
use App\Models\JenisPembayaran;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class PembayaranReportController extends Controller
{
    public function exportPdf(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $jenisPembayaranId = $request->input('jenis_pembayaran_id');

        // Build query
        $query = Pembayaran::with([
            'siswa',
            'detailPembayarans.tagihan.jenisPembayaran',
            'user'
        ]);

        // Filter by date range
        if ($startDate && $endDate) {
            $query->whereBetween('tanggal_bayar', [$startDate, $endDate]);
        }

        // Filter by payment type if specified
        if ($jenisPembayaranId) {
            $query->whereHas('detailPembayarans.tagihan.jenisPembayaran', function ($q) use ($jenisPembayaranId) {
                $q->where('id', $jenisPembayaranId);
            });
        }

        $pembayarans = $query->orderBy('tanggal_bayar', 'asc')->get();

        // Calculate totals
        $totalAmount = $pembayarans->sum('jumlah_bayar');

        // Get filter info for display
        $jenisPembayaran = $jenisPembayaranId ? JenisPembayaran::find($jenisPembayaranId) : null;
        $dateRange = $startDate && $endDate ?
            Carbon::parse($startDate)->format('d/m/Y') . ' - ' . Carbon::parse($endDate)->format('d/m/Y') :
            'Semua Tanggal';

        $pdf = Pdf::loadView('pdf.payment-report', compact(
            'pembayarans',
            'totalAmount',
            'dateRange',
            'jenisPembayaran',
            'startDate',
            'endDate'
        ))
        ->setPaper('a4', 'portrait')
        ->setOptions([
            'defaultFont' => 'sans-serif',
            'isHtml5ParserEnabled' => true,
            'isPhpEnabled' => true
        ]);

        $filename = 'Laporan-Pembayaran-' . ($startDate ? Carbon::parse($startDate)->format('dmY') : 'All') .
                   '-' . ($endDate ? Carbon::parse($endDate)->format('dmY') : 'All') . '.pdf';

        return $pdf->stream($filename);
    }
}
