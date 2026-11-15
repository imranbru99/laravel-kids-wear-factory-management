<?php

use App\Http\Controllers\Admin\AttendanceController;
use App\Http\Controllers\Admin\FactoryController;
use App\Http\Controllers\Admin\ResourceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'admin.auth'])->prefix('admin')->name('admin.')->group(function (): void {
    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance');
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.check-in');
    Route::post('/attendance/rollup', [AttendanceController::class, 'rollup'])->name('attendance.rollup');

    Route::get('/production/board', [FactoryController::class, 'board'])->name('production.board');
    Route::post('/buyer-orders/{buyerOrder}/confirm', [FactoryController::class, 'confirmOrder'])->name('orders.confirm');
    Route::post('/buyer-orders/{buyerOrder}/lines', [FactoryController::class, 'addOrderLine'])->name('orders.lines');
    Route::post('/buyer-orders/{buyerOrder}/invoice', [FactoryController::class, 'makeInvoice'])->name('orders.invoice');
    Route::post('/styles/{style}/bom', [FactoryController::class, 'addBom'])->name('styles.bom');
    Route::post('/styles/{style}/colorways', [FactoryController::class, 'addColorway'])->name('styles.colorways');
    Route::post('/styles/{style}/measurements', [FactoryController::class, 'addMeasurement'])->name('styles.measurements');
    Route::post('/styles/{style}/tech-packs', [FactoryController::class, 'addTechPack'])->name('styles.tech-packs');
    Route::post('/styles/{style}/costing', [FactoryController::class, 'costStyle'])->name('styles.costing');
    Route::post('/production-orders/{productionOrder}/issue', [FactoryController::class, 'issueMaterials'])->name('production.issue');
    Route::post('/production-orders/{productionOrder}/recalculate', [FactoryController::class, 'recalculate'])->name('production.recalculate');
    Route::post('/production-orders/{productionOrder}/output', [FactoryController::class, 'recordOutput'])->name('production.output');
    Route::post('/production-orders/{productionOrder}/packing-list', [FactoryController::class, 'makePackingList'])->name('production.packing-list');
    Route::get('/bundles/{code}/ticket', [FactoryController::class, 'bundleTicket'])->name('bundles.ticket');
    Route::post('/purchase-orders/{purchaseOrder}/items', [FactoryController::class, 'addPurchaseItem'])->name('purchase.items');
    Route::post('/purchase-orders/{purchaseOrder}/receive', [FactoryController::class, 'receivePurchase'])->name('purchase.receive');
    Route::post('/inspections/{inspection}/defects', [FactoryController::class, 'inspectionDefects'])->name('inspections.defects');

    Route::get('/ocr', [FactoryController::class, 'ocrForm'])->name('ocr');
    Route::post('/ocr', [FactoryController::class, 'ocrStore'])->name('ocr.store');
    Route::get('/ai/styles', [FactoryController::class, 'aiStyles'])->name('ai.styles');
    Route::post('/ai/styles', [FactoryController::class, 'aiStylesStore'])->name('ai.styles.store');

    Route::get('/reports', [FactoryController::class, 'reports'])->name('reports');
    Route::get('/reports/{report}', [FactoryController::class, 'reportShow'])->name('reports.show');
    Route::get('/reports/{report}/xlsx', [FactoryController::class, 'reportXlsx'])->name('reports.xlsx');
    Route::get('/reports/{report}/pdf', [FactoryController::class, 'reportPdf'])->name('reports.pdf');
    Route::get('/invoices/{invoice}/pdf', [FactoryController::class, 'invoicePdf'])->name('invoices.pdf');

    Route::get('/settings', [FactoryController::class, 'settings'])->name('settings');
    Route::post('/settings', [FactoryController::class, 'saveSettings'])->name('settings.save');
    Route::post('/settings/backup', [FactoryController::class, 'backup'])->name('settings.backup');

    Route::get('/resources/{resource}', [ResourceController::class, 'index'])->name('resources.index');
    Route::get('/resources/{resource}/create', [ResourceController::class, 'create'])->name('resources.create');
    Route::post('/resources/{resource}', [ResourceController::class, 'store'])->name('resources.store');
    Route::get('/resources/{resource}/{id}', [ResourceController::class, 'show'])->whereNumber('id')->name('resources.show');
    Route::get('/resources/{resource}/{id}/edit', [ResourceController::class, 'edit'])->whereNumber('id')->name('resources.edit');
    Route::put('/resources/{resource}/{id}', [ResourceController::class, 'update'])->whereNumber('id')->name('resources.update');
    Route::delete('/resources/{resource}/{id}', [ResourceController::class, 'destroy'])->whereNumber('id')->name('resources.destroy');
});
