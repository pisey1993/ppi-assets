<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\AssetsController;
use App\Http\Controllers\AssetTransferController;
use App\Http\Controllers\RepairController;

// --- Routes for the RepairController ---
Route::post('/assets/{asset}/repairs', [RepairController::class, 'store'])->name('repairs.store');
Route::controller(RepairController::class)->group(function () {
    // This route corresponds to the `index` method and is used to render the initial page.
    // It's functionally similar to the assets.show route above if the component is on that page.
    // If 'Asset/Repairs' is a standalone page, you would use this route to get there.
    Route::get('/assets/{assetId}/repairs', 'index')->name('repairs.index');

    // POST route to store a new repair. Corresponds to the `store` method.
    Route::post('/assets/{assetId}/repairs', 'store')->name('repairs.store');

    // PUT route to update an existing repair. Corresponds to the `update` method.
    Route::put('/repairs/{repairId}', 'update')->name('repairs.update');

    // DELETE route to destroy a repair. Corresponds to the `destroy` method.
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
