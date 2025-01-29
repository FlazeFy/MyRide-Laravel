<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\GarageEditController;
use App\Http\Controllers\CleanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StatsController;

use App\Http\Controllers\TripController;
use App\Http\Controllers\AddTripController;

Route::prefix('/')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');

    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login/validate', [LoginController::class, 'login_auth']);
    Route::post('/sign_out', [LandingController::class, 'sign_out']);
});

Route::prefix('/garage')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [GarageController::class, 'index'])->name('garage');

    Route::get('/edit/{id}', [GarageEditController::class, 'index'])->name('edit_garage');
    Route::post('/edit_doc/{id}', [GarageEditController::class, 'edit_vehicle_doc']);
});

Route::prefix('/clean')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [CleanController::class, 'index'])->name('clean');

    Route::post('/destroy/{id}', [CleanController::class, 'hard_del_clean']);
});

Route::prefix('/trip')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('trip');

    Route::get('/add', [AddTripController::class, 'index'])->name('add_trip');
});

Route::prefix('/stats')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [StatsController::class, 'index'])->name('stats');

    Route::post('/convert/csv', [StatsController::class, 'convert_csv']);
});

