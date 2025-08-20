<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembayaranController;

Route::get('/', function () {
    return redirect('/admin/login'); // ⬅️ Arahkan ke login Filament
});

Route::get('/pembayaran/{id}/print', [PembayaranController::class, 'printInvoice'])
    ->name('pembayaran.print');
Route::get('/tagihan/print', [App\Http\Controllers\TagihanController::class, 'printPdf'])->name('tagihan.print');
Route::get('/pembayaran/report/export', [App\Http\Controllers\PembayaranReportController::class, 'exportPdf'])->name('pembayaran.report.export');
