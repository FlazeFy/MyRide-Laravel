<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\CleanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TripController;

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
});

Route::prefix('/garage')->group(function () {
    Route::get('/', [GarageController::class, 'index'])->name('garage');
});

Route::prefix('/clean')->group(function () {
    Route::get('/', [CleanController::class, 'index'])->name('clean');
});

Route::prefix('/trip')->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('trip');
});

