<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AssetTransferController;
use App\Http\Controllers\RepairController;


Route::prefix('assets/{asset}/repairs')->group(function () {
    Route::get('/', [RepairController::class, 'index']);      // GET /assets/{asset}/repairs - list repairs for asset
    Route::post('/', [RepairController::class, 'store']);     // POST /assets/{asset}/repairs - create repair
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

require __DIR__.'/auth.php';
