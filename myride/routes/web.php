<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\CleanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StatsController;

use App\Http\Controllers\TripController;
use App\Http\Controllers\AddTripController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::prefix('/')->group(function () {
    Route::get('/', [LandingController::class, 'index'])->name('landing');

    Route::get('/login', [LoginController::class, 'index']);
    Route::post('/login/validate', [LoginController::class, 'login_auth']);
    Route::post('/sign_out', [LandingController::class, 'sign_out']);
});

Route::prefix('/garage')->group(function () {
    Route::get('/', [GarageController::class, 'index'])->name('garage');
});

Route::prefix('/clean')->group(function () {
    Route::get('/', [CleanController::class, 'index'])->name('clean');

    Route::post('/destroy/{id}', [CleanController::class, 'hard_del_clean']);
});

Route::prefix('/trip')->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('trip');

    Route::get('/add', [AddTripController::class, 'index'])->name('add_trip');
    Route::post('/add', [AddTripController::class, 'post_trip']);
});

Route::prefix('/stats')->group(function () {
    Route::get('/', [StatsController::class, 'index'])->name('stats');

    Route::post('/convert/csv', [StatsController::class, 'convert_csv']);
});

