<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApi\Commands as CommandAuthApi;
use App\Http\Controllers\Api\AuthApi\Queries as QueryAuthApi;
use App\Http\Controllers\Api\VehicleApi\Queries as QueriesVehicleApi;
use App\Http\Controllers\Api\VehicleApi\Commands as CommandsVehicleApi;
use App\Http\Controllers\Api\WashApi\Queries as QueriesWashController;
use App\Http\Controllers\Api\WashApi\Commands as CommandsWashController;
use App\Http\Controllers\Api\DictionaryApi\Queries as QueriesDictionaryApi;
use App\Http\Controllers\Api\DictionaryApi\Commands as CommandsDictionaryApi;
use App\Http\Controllers\Api\TripApi\Commands as CommandsTripController;
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
use App\Http\Controllers\Api\ErrorApi\Queries as QueriesErrorController;
use App\Http\Controllers\Api\ErrorApi\Commands as CommandsErrorController;

######################### Public Route #########################

Route::post('/v1/login', [CommandAuthApi::class, 'login']);

Route::prefix('/v1/register')->group(function () {
    Route::post('/token', [CommandAuthApi::class, 'get_register_validation_token']);
    Route::post('/account', [CommandAuthApi::class, 'post_validate_register']);
    Route::post('/regen_token', [CommandAuthApi::class, 'regenerate_register_token']);
});

Route::prefix('/v1/stats')->group(function () {
    Route::get('/summary', [QueriesStatsApi::class, 'getSummaryApps']);
    Route::get('/total/trip/monthly/{year}', [QueriesStatsApi::class, 'getTotalTripPerYear']);
    Route::get('/total/fuel/monthly/{context}/{year}', [QueriesStatsApi::class, 'getTotalFuelPerYear']);
    Route::get('/total/service/monthly/{context}/{year}', [QueriesStatsApi::class, 'getTotalServicePerYear']);
    Route::get('/total/wash/monthly/{context}/{year}', [QueriesStatsApi::class, 'getTotalWashPerYear']);
});

Route::prefix('/v1/vehicle')->group(function () {
    Route::get('/readiness', [QueriesVehicleApi::class, 'getVehicleReadiness']);
});

Route::prefix('/v1/question')->group(function () {
    Route::get('/faq', [QueriesFAQController::class, 'getShowingFAQ']);
});

Route::prefix('/v1/dictionary')->group(function () {
    Route::get('/type/{type}', [QueriesDictionaryApi::class, 'getDictionaryByType']);
});

######################### Private Route #########################

Route::post('/v1/logout', [QueryAuthApi::class, 'logout'])->middleware(['auth:sanctum']);

Route::prefix('/v1/vehicle')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsVehicleApi::class, 'postVehicle']);
    Route::post('/doc/{id}', [CommandsVehicleApi::class, 'postVehicleDoc']);
    Route::get('/header', [QueriesVehicleApi::class, 'getAllVehicleHeader']);
    Route::get('/name', [QueriesVehicleApi::class, 'getAllVehicleName']);
    Route::get('/fuel', [QueriesVehicleApi::class, 'getAllVehicleFuel']);
    Route::get('/detail/{id}', [QueriesVehicleApi::class, 'getVehicleDetailById']);
    Route::get('/detail/full/{id}', [QueriesVehicleApi::class, 'getVehicleFullDetailById']);
    Route::get('/trip/summary/{id}', [QueriesVehicleApi::class, 'getVehicleTripSummaryById']);
    Route::put('/detail/{id}', [CommandsVehicleApi::class, 'putVehicleDetailById']);
    Route::put('/recover/{id}', [CommandsVehicleApi::class, 'recoverVehicleById']);
    Route::delete('/delete/{id}', [CommandsVehicleApi::class, 'softDeleteVehicleById']);
    Route::delete('/destroy/{id}', [CommandsVehicleApi::class, 'hardDeleteVehicleById']);
});

Route::prefix('/v1/dictionary')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsDictionaryApi::class, 'postCreateDictionary']);
    Route::delete('/{id}', [CommandsDictionaryApi::class, 'hardDeleteDictionaryById']);
});

Route::prefix('/v1/wash')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesWashController::class, 'getAllWashHistory']);
    Route::delete('/destroy/{id}', [CommandsWashController::class, 'hardDeleteWashById']);
    Route::get('/last', [QueriesWashController::class, 'getLastWashByVehicleId']);
    Route::get('/summary', [QueriesWashController::class, 'getWashSummaryByVehicleId']);
    Route::post('/', [CommandsWashController::class, 'postWash']);
    Route::put('/finish/{id}', [CommandsWashController::class, 'putFinishWash']);
    Route::put('/{id}', [CommandsWashController::class, 'putWashById']);
});

Route::prefix('/v1/history')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesHistoryController::class, 'getAllHistory']);
    Route::delete('/destroy/{id}', [CommandsHistoryController::class, 'hardDeleteHistoryById']);
});

Route::prefix('/v1/error')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesErrorController::class, 'getAllError']);
    Route::delete('/destroy/{id}', [CommandsErrorController::class, 'hard_delete_error_by_id']);
});

Route::prefix('/v1/reminder')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/next', [QueriesReminderController::class, 'getNextReminder']);
    Route::get('/', [QueriesReminderController::class, 'getAllReminder']);
    Route::get('/recently', [QueriesReminderController::class, 'getRecentlyReminder']);
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
    Route::get('/name', [QueriesDriverController::class, 'getAllDriverName']);
    Route::get('/vehicle', [QueriesDriverController::class, 'getDriverVehicle']);
    Route::get('/vehicle/list', [QueriesDriverController::class, 'getDriverVehicleManageList']);
    Route::post('/vehicle', [CommandsDriverController::class, 'postDriverVehicle']);
    Route::post('/', [CommandsDriverController::class, 'postDriver']);
    Route::delete('/destroy/{id}', [CommandsDriverController::class, 'hardDeleteDriverById']);
    Route::delete('/destroy/relation/{id}', [CommandsDriverController::class, 'hardDeleteDriverRelationById']);
    Route::put('/{id}', [CommandsDriverController::class, 'updateDriver']);
});

Route::prefix('/v1/stats')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/total/trip/{context}', [QueriesStatsApi::class, 'getTotalTripByContext']);
    Route::get('/total/inventory/{context}', [QueriesStatsApi::class, 'getTotalInventoryByContext']);
    Route::get('/total/vehicle/{context}', [QueriesStatsApi::class, 'getTotalVehicleByContext']);
    Route::get('/total/service/{context}', [QueriesStatsApi::class, 'getTotalServicePriceByContext']);
    Route::get('/total/trip/monthly/{year}/{vehicle_id}', [QueriesStatsApi::class, 'getTotalTripByVehiclePerYear']);
});

Route::prefix('/v1/fuel')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesFuelController::class, 'getAllFuel']);
    Route::get('/last', [QueriesFuelController::class, 'getLastFuel']);
    Route::get('/summary/{month_year}', [QueriesFuelController::class, 'getMonthlyFuelSummary']);
    Route::delete('/destroy/{id}', [CommandsFuelController::class, 'hardDeleteFuelById']);
    Route::post('/', [CommandsFuelController::class, 'postFuel']);
    Route::put('/{id}', [CommandsFuelController::class, 'updateFuelById']);
});

Route::prefix('/v1/trip')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsTripController::class, 'postTrip']);
    Route::get('/', [QueriesTripApi::class, 'getAllTrip']);
    Route::get('/last', [QueriesTripApi::class, 'getLastTrip']);
    Route::get('/driver/{driver_id}', [QueriesTripApi::class, 'getAllTripByDriverId']);
    Route::delete('/destroy/{id}', [CommandsTripController::class, 'hardDeleteTripById']);
    Route::put('/{id}', [CommandsTripController::class, 'updateTripById']);
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
    Route::get('/wash', [QueriesExportController::class, 'exportWashHistory']);
    Route::get('/trip', [QueriesExportController::class, 'exportTripHistory']);
    Route::get('/fuel', [QueriesExportController::class, 'exportFuelHistory']);
    Route::get('/inventory', [QueriesExportController::class, 'exportInventory']);
    Route::get('/service', [QueriesExportController::class, 'exportService']);
    Route::get('/driver', [QueriesExportController::class, 'exportDriver']);
});