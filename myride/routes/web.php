<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LandingController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\CleanController;

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
});

Route::prefix('/garage')->group(function () {
    Route::get('/', [GarageController::class, 'index'])->name('garage');
});

Route::prefix('/clean')->group(function () {
    Route::get('/', [CleanController::class, 'index'])->name('clean');
});

