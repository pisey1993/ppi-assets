<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AssetTransferController;
use App\Http\Controllers\RepairController;
use App\Exports\AssetsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
// --- Routes for the RepairController ---
Route::get('/assets/{asset}/print', [AssetsController::class, 'print'])->name('assets.print');
Route::get('/repairs/report/export', [RepairController::class, 'export'])->name('repairs.report.export');
Route::get('/assets/report/export', function (Request $request) {
    $filters = $request->only([
        'name', 'status', 'department',
        'purchase_date_from', 'purchase_date_to', 'purchase_age',
    ]);

    return Excel::download(new AssetsExport($filters), 'asset_report.xlsx');
})->name('assets.report.export');

Route::get('/assets/report', [AssetsController::class, 'report'])->name('assets.report');
Route::post('/assets/{asset}/repairs', [RepairController::class, 'store'])->name('repairs.store');
Route::controller(RepairController::class)->group(function () {
    Route::get('/repairs/report', 'report')->name('repairs.report');

    Route::get('/assets/{assetId}/repairs', 'index')->name('repairs.index');
    Route::post('/assets/{assetId}/repairs', 'store')->name('repairs.store');
    Route::put('/repairs/{repairId}', 'update')->name('repairs.update');
    Route::delete('/repairs/{repairId}', 'destroy')->name('repairs.destroy');
});


Route::prefix('repairs')->group(function () {
    Route::put('{repair}', [RepairController::class, 'update']);    // PUT /repairs/{repair} - update repair
    Route::delete('{repair}', [RepairController::class, 'destroy']); // DELETE /repairs/{repair} - delete repair
});


Route::resource('asset-transfers', AssetTransferController::class);
Route::get('/asset-transfers/{transfer}/print', [AssetTransferController::class, 'print'])
    ->name('asset-transfers.print');

Route::resource('employees', EmployeeController::class);
Route::resource('assets', AssetsController::class);

Route::get('/welcome', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

//Route::get('/manage_employees', function () {
//    return Inertia::render('Employees');
//})->middleware(['auth', 'verified'])->name('manage_employees');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';
