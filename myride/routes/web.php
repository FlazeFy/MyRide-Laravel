<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\GarageEditController;
use App\Http\Controllers\GarageDetailController;
use App\Http\Controllers\CleanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\AddTripController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\HistoryController;

Route::prefix('/')->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login/validate', [LoginController::class, 'login_auth']);
    Route::post('/sign_out', [DashboardController::class, 'sign_out']);
});

Route::prefix('/garage')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [GarageController::class, 'index'])->name('garage');

    Route::get('/edit/{id}', [GarageEditController::class, 'index'])->name('edit_garage');
    Route::post('/edit_doc/{id}', [GarageEditController::class, 'edit_vehicle_doc']);

    Route::get('/detail/{id}', [GarageDetailController::class, 'index'])->name('detail_garage');
});

Route::prefix('/clean')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [CleanController::class, 'index'])->name('clean');

    Route::post('/destroy/{id}', [CleanController::class, 'hard_del_clean']);
});

Route::prefix('/trip')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('trip');

    Route::get('/add', [AddTripController::class, 'index'])->name('add_trip');
});

Route::prefix('/reminder')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [ReminderController::class, 'index'])->name('reminder');
});

Route::prefix('/history')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('history');
});

Route::prefix('/stats')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [StatsController::class, 'index'])->name('stats');

    Route::post('/convert/csv', [StatsController::class, 'convert_csv']);
});

Route::prefix('/profile')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [ProfileController::class, 'index'])->name('profile');
});

Route::prefix('/embed')->group(function () {
    Route::get('/app_summary', [EmbedController::class, 'app_summary']);
    Route::get('/trip_discovered', [EmbedController::class, 'trip_discovered']);
});