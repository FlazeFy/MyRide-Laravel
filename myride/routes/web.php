<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GarageController;
use App\Http\Controllers\GarageEditController;
use App\Http\Controllers\GarageDetailController;
use App\Http\Controllers\CleanController;
use App\Http\Controllers\AddCleanController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\StatsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\TripController;
use App\Http\Controllers\AddTripController;
use App\Http\Controllers\EmbedController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\AddReminderController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\FuelController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\AddServiceController;
use App\Http\Controllers\AddFuelController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\AddInventoryController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\AddDriverController;

Route::prefix('/')->group(function () {
    Route::get('/', [WelcomeController::class, 'index'])->name('welcome');
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::post('/login/validate', [LoginController::class, 'login_auth']);
    Route::post('/sign_out', [DashboardController::class, 'sign_out']);
});

Route::prefix('/dashboard')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('/toogle_view_stats_fuel', [DashboardController::class, 'toogle_view_stats_fuel']);
    Route::post('/toogle_year', [DashboardController::class, 'toogle_year']);
});

Route::prefix('/garage')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [GarageController::class, 'index'])->name('garage');

    Route::get('/edit/{id}', [GarageEditController::class, 'index'])->name('edit_garage');
    Route::post('/edit_doc/{id}', [GarageEditController::class, 'edit_vehicle_doc']);

    Route::get('/detail/{id}', [GarageDetailController::class, 'index'])->name('detail_garage');
});

Route::prefix('/clean')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [CleanController::class, 'index'])->name('clean');

    Route::get('/add', [AddCleanController::class, 'index'])->name('add_clean');
});

Route::prefix('/trip')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [TripController::class, 'index'])->name('trip');

    Route::get('/add', [AddTripController::class, 'index'])->name('add_trip');
});

Route::prefix('/reminder')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [ReminderController::class, 'index'])->name('reminder');
    Route::get('/add', [AddReminderController::class, 'index'])->name('add_reminder');
});

Route::prefix('/inventory')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventory');

    Route::get('/add', [AddInventoryController::class, 'index'])->name('add_inventory');
});

Route::prefix('/history')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [HistoryController::class, 'index'])->name('history');
});

Route::prefix('/driver')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [DriverController::class, 'index'])->name('driver');

    Route::get('/add', [AddDriverController::class, 'index'])->name('add_driver');
});

Route::prefix('/service')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [ServiceController::class, 'index'])->name('service');
    Route::get('/add', [AddServiceController::class, 'index'])->name('add_service');
});

Route::prefix('/fuel')->middleware(['auth_v2:sanctum'])->group(function () {
    Route::get('/', [FuelController::class, 'index'])->name('fuel');
    Route::get('/add', [AddFuelController::class, 'index'])->name('add_fuel');
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