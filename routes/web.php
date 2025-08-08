<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PembayaranController;

Route::get('/', function () {
    return redirect('/admin/login'); // ⬅️ Arahkan ke login Filament
});

Route::get('/pembayaran/{id}/print', [PembayaranController::class, 'printInvoice'])
    ->name('pembayaran.print');
