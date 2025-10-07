<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApi\Commands as CommandAuthApi;
use App\Http\Controllers\Api\AuthApi\Queries as QueryAuthApi;
use App\Http\Controllers\Api\VehicleApi\Queries as QueriesVehicleApi;
use App\Http\Controllers\Api\VehicleApi\Commands as CommandsVehicleApi;
use App\Http\Controllers\Api\CleanApi\Queries as QueriesCleanController;
use App\Http\Controllers\Api\CleanApi\Commands as CommandsCleanController;
use App\Http\Controllers\Api\DictionaryApi\Queries as QueriesDictionaryApi;
use App\Http\Controllers\Api\DictionaryApi\Commands as CommandsDictionaryApi;
use App\Http\Controllers\Api\TripApi\Commands as CommandsTripApi;
use App\Http\Controllers\Api\TripApi\Queries as QueriesTripApi;
use App\Http\Controllers\Api\StatsApi\Queries as QueriesStatsApi;
use App\Http\Controllers\Api\UserApi\Queries as QueriesUserController;
use App\Http\Controllers\Api\UserApi\Commands as CommandsUserController;
use App\Http\Controllers\Api\ExportApi\Queries as QueriesExportController;
use App\Http\Controllers\Api\QuestionApi\FAQQueries as QueriesFAQController;
use App\Http\Controllers\Api\ReminderApi\Queries as QueriesReminderController;
use App\Http\Controllers\Api\ReminderApi\Commands as CommandsReminderController;
use App\Http\Controllers\Api\HistoryApi\Queries as QueriesHistoryController;
use App\Http\Controllers\Api\HistoryApi\Commands as CommandsHistoryController;
use App\Http\Controllers\Api\FuelApi\Queries as QueriesFuelController;
use App\Http\Controllers\Api\FuelApi\Commands as CommandsFuelController;
use App\Http\Controllers\Api\InventoryApi\Queries as QueriesInventoryController;
use App\Http\Controllers\Api\InventoryApi\Commands as CommandsInventoryController;
use App\Http\Controllers\Api\ServiceApi\Queries as QueriesServiceController;
use App\Http\Controllers\Api\ServiceApi\Commands as CommandsServiceController;
use App\Http\Controllers\Api\DriverApi\Queries as QueriesDriverController;
use App\Http\Controllers\Api\DriverApi\Commands as CommandsDriverController;

######################### Public Route #########################

Route::post('/v1/login', [CommandAuthApi::class, 'login']);
Route::post('/v1/register', [CommandAuthApi::class, 'register']);
Route::post('/v1/register/validate', [CommandAuthApi::class, 'validate_register']);

Route::prefix('/v1/stats')->group(function () {
    Route::get('/summary', [QueriesStatsApi::class, 'getSummaryApps']);
});

Route::prefix('/v1/question')->group(function () {
    Route::get('/faq', [QueriesFAQController::class, 'getShowingFAQ']);
});

######################### Private Route #########################

Route::post('/v1/logout', [QueryAuthApi::class, 'logout'])->middleware(['auth:sanctum']);

Route::prefix('/v1/vehicle')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/header', [QueriesVehicleApi::class, 'getAllVehicleHeader']);
    Route::get('/name', [QueriesVehicleApi::class, 'getAllVehicleName']);
    Route::get('/fuel', [QueriesVehicleApi::class, 'getAllVehicleFuel']);
    Route::get('/readiness', [QueriesVehicleApi::class, 'getVehicleReadiness']);
    Route::get('/detail/{id}', [QueriesVehicleApi::class, 'getVehicleDetailById']);
    Route::get('/detail/full/{id}', [QueriesVehicleApi::class, 'getVehicleFullDetailById']);
    Route::get('/trip/summary/{id}', [QueriesVehicleApi::class, 'getVehicleTripSummaryById']);
    Route::put('/detail/{id}', [CommandsVehicleApi::class, 'putVehicleDetailById']);
});

Route::prefix('/v1/dictionary')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/type/{type}', [QueriesDictionaryApi::class, 'getDictionaryByType']);
    Route::post('/', [CommandsDictionaryApi::class, 'postCreateDictionary']);
    Route::delete('/{id}', [CommandsDictionaryApi::class, 'hardDeleteDictionaryById']);
});

Route::prefix('/v1/clean')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesCleanController::class, 'getAllCleanHistory']);
    Route::delete('/destroy/{id}', [CommandsCleanController::class, 'hardDeleteCleanById']);
    Route::get('/last', [QueriesCleanController::class, 'getLastCleanByVehicleId']);
    Route::post('/', [CommandsCleanController::class, 'postClean']);
});

Route::prefix('/v1/history')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesHistoryController::class, 'getAllHistory']);
    Route::delete('/destroy/{id}', [CommandsHistoryController::class, 'hardDeleteHistoryById']);
});

Route::prefix('/v1/reminder')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/next', [QueriesReminderController::class, 'getNextReminder']);
    Route::get('/', [QueriesReminderController::class, 'getAllReminder']);
    Route::get('/vehicle/{vehicle_id}', [QueriesReminderController::class, 'getReminderByVehicle']);
    Route::delete('/destroy/{id}', [CommandsReminderController::class, 'hardDeleteReminderById']);
    Route::post('/', [CommandsReminderController::class, 'postReminder']);
});

Route::prefix('/v1/service')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesServiceController::class, 'getAllService']);
    Route::get('/next', [QueriesServiceController::class, 'getNextService']);
    Route::get('/spending', [QueriesServiceController::class, 'getAllServiceSpending']);
    Route::get('/vehicle/{vehicle_id}', [QueriesServiceController::class, 'getServiceByVehicle']);
    Route::post('/', [CommandsServiceController::class, 'postService']);
    Route::delete('/destroy/{id}', [CommandsServiceController::class, 'hardDeleteServiceById']);
    Route::put('/{id}', [CommandsServiceController::class, 'updateService']);
});

Route::prefix('/v1/driver')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesDriverController::class, 'getAllDriver']);
    Route::delete('/destroy/{id}', [CommandsDriverController::class, 'hardDeleteDriverById']);
});

Route::prefix('/v1/stats')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/total/trip/{context}', [QueriesStatsApi::class, 'getTotalTripByContext']);
    Route::get('/total/inventory/{context}', [QueriesStatsApi::class, 'getTotalInventoryByContext']);
    Route::get('/total/vehicle/{context}', [QueriesStatsApi::class, 'getTotalVehicleByContext']);
    Route::get('/total/service/{context}', [QueriesStatsApi::class, 'getTotalServicePriceByContext']);
    Route::get('/total/fuel/monthly/{context}/{year}', [QueriesStatsApi::class, 'getTotalFuelPerYear']);
    Route::get('/total/trip/monthly/{year}/{vehicle_id}', [QueriesStatsApi::class, 'getTotalTripByVehiclePerYear']);
    Route::get('/total/trip/monthly/{year}', [QueriesStatsApi::class, 'getTotalTripPerYear']);
});

Route::prefix('/v1/fuel')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesFuelController::class, 'getAllFuel']);
    Route::get('/last', [QueriesFuelController::class, 'getLastFuel']);
    Route::get('/summary/{month_year}', [QueriesFuelController::class, 'getMonthlyFuelSummary']);
    Route::delete('/destroy/{id}', [CommandsFuelController::class, 'hardDeleteFuelById']);
    Route::post('/', [CommandsFuelController::class, 'postFuel']);
});

Route::prefix('/v1/trip')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsTripApi::class, 'postTrip']);
    Route::get('/', [QueriesTripApi::class, 'getAllTrip']);
});

Route::prefix('/v1/trip')->group(function () {
    Route::get('/discovered', [QueriesTripApi::class, 'getTripDiscovered']);
});

Route::prefix('/v1/inventory')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesInventoryController::class, 'getAllInventory']);
    Route::get('/vehicle/{vehicle_id}', [QueriesInventoryController::class, 'getInventoryByVehicle']);
    Route::delete('/destroy/{id}', [CommandsInventoryController::class, 'hardDeleteInventoryById']);
    Route::post('/', [CommandsInventoryController::class, 'postInventory']);
    Route::put('/{id}', [CommandsInventoryController::class, 'updateInventory']);
});

Route::prefix('/v1/user')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesUserController::class, 'get_all_user']);
    Route::get('/my_year', [QueriesUserController::class, 'get_content_year']);
    Route::get('/my_profile', [QueriesUserController::class, 'get_my_profile']);
    Route::put('/update_telegram_id', [CommandsUserController::class, 'update_telegram_id']);
    Route::put('/validate_telegram_id', [CommandsUserController::class, 'validate_telegram_id']);
    Route::put('/update_profile', [CommandsUserController::class, 'update_profile']);
});

Route::prefix('/v1/export')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/clean', [QueriesExportController::class, 'exportCleanHistory']);
    Route::get('/fuel', [QueriesExportController::class, 'exportFuelHistory']);
    Route::get('/inventory', [QueriesExportController::class, 'exportInventory']);
    Route::get('/service', [QueriesExportController::class, 'exportService']);
    Route::get('/driver', [QueriesExportController::class, 'exportDriver']);
});