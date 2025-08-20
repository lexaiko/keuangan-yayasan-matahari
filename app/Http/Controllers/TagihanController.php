<?php

namespace App\Http\Controllers;

use App\Models\Tagihan;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class TagihanController extends Controller
{
    public function printPdf(Request $request)
    {
        $siswaId = $request->get('siswa_id');

        $siswa = Siswa::with('kelas')->findOrFail($siswaId);

        $tagihans = Tagihan::where('siswa_id', $siswaId)
            ->where('status', '!=', Tagihan::STATUS_LUNAS)
            ->with(['jenisPembayaran', 'detailPembayarans'])
            ->orderBy('tanggal_jatuh_tempo')
            ->get();

        $totalSemua = 0;
        foreach ($tagihans as $tagihan) {
            $totalDibayar = $tagihan->detailPembayarans->sum('jumlah_bayar');
            $sisaTagihan = $tagihan->jumlah - $totalDibayar;
            $tagihan->sisa_tagihan = $sisaTagihan;
            $totalSemua += $sisaTagihan;
        }

        $data = [
            'siswa' => $siswa,
            'tagihans' => $tagihans,
            'total_semua' => $totalSemua,
            'tanggal_cetak' => now()->format('d/m/Y H:i:s')
        ];

        $pdf = Pdf::loadView('pdf.tagihan', $data)
            ->setPaper('a4', 'portrait');

        // Stream PDF to browser instead of download
        return $pdf->stream('tagihan-' . $siswa->nama . '-' . now()->format('Y-m-d') . '.pdf');
    }
}
