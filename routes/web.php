<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KasirController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\PosScannerController;

Route::get('/', [AuthController::class, 'showLoginForm']);
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/export', [AdminDashboardController::class, 'exportRecap'])->name('dashboard.export');
    Route::get('/low-stock', [AdminDashboardController::class, 'lowStock'])->name('low_stock');
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [AdminReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/stock', [AdminReportController::class, 'stock'])->name('reports.stock');
});

Route::middleware(['auth', 'role:kasir'])->prefix('kasir')->name('kasir.')->group(function () {
    Route::get('/dashboard', [KasirController::class, 'dashboard'])->name('dashboard');
    Route::get('/pos', [KasirController::class, 'pos'])->name('pos');
    Route::post('/transaction', [KasirController::class, 'createTransaction'])->name('transaction.create');
    Route::get('/transaction/{id}/print', [KasirController::class, 'printReceipt'])->name('transaction.print');
    Route::get('/transaction/history', [KasirController::class, 'transactionHistory'])->name('transaction.history');
    Route::post('/scan-preview', [PosScannerController::class, 'preview'])->name('scan.preview');
    Route::get('/mobile-scanner', [PosScannerController::class, 'index'])->name('mobile.scanner');
    Route::post('/scan-item', [PosScannerController::class, 'store'])->name('scan.store');
    Route::get('/check-scan', [PosScannerController::class, 'check'])->name('scan.check');
});

Route::middleware(['auth', 'role:gudang'])->prefix('gudang')->name('gudang.')->group(function () {
    Route::get('/dashboard', [GudangController::class, 'dashboard'])->name('dashboard');
    Route::get('/products', [GudangController::class, 'products'])->name('products.index');
    Route::get('/products/create', [GudangController::class, 'productsCreate'])->name('products.create');
    Route::post('/products', [GudangController::class, 'productsStore'])->name('products.store');
    Route::get('/products/low-stock', [GudangController::class, 'productsLowStock'])->name('products.low_stock');
    Route::get('/products/{product}/qr', [GudangController::class, 'productsQr'])->name('products.qr');
    Route::get('/products/{product}', [GudangController::class, 'productsShow'])->name('products.show');
    Route::get('/products/{product}/edit', [GudangController::class, 'productsEdit'])->name('products.edit');
    Route::put('/products/{product}', [GudangController::class, 'productsUpdate'])->name('products.update');
    Route::delete('/products/{product}', [GudangController::class, 'productsDestroy'])->name('products.destroy');
    Route::get('/stock-movements', [GudangController::class, 'stockMovements'])->name('stock.movements');
    Route::post('/stock-adjustment', [GudangController::class, 'stockAdjustment'])->name('stock.adjustment');
    Route::get('/reports/stock', [GudangController::class, 'stockReport'])->name('reports.stock');
});
