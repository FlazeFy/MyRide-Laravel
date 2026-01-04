<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApi\Commands as CommandAuthApi;
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
use App\Http\Controllers\Api\QuestionApi\Queries as QueriesFAQController;
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

Route::post('/v1/login', [CommandAuthApi::class, 'postLogin']);

Route::prefix('/v1/register')->group(function () {
    Route::post('/token', [CommandAuthApi::class, 'getRegisterValidationToken']);
    Route::post('/account', [CommandAuthApi::class, 'postValidateRegister']);
    Route::post('/regen_token', [CommandAuthApi::class, 'postRegenerateRegisterToken']);
});

Route::prefix('/v1/stats')->group(function () {
    Route::get('/summary', [QueriesStatsApi::class, 'getSummaryApps']);
    Route::get('/total/trip/monthly/{year}', [QueriesStatsApi::class, 'getTotalTripPerYear']);
    Route::get('/total/fuel/monthly/{context}/{year}', [QueriesStatsApi::class, 'getTotalFuelPerYear']);
    Route::get('/total/service/monthly/{context}/{year}', [QueriesStatsApi::class, 'getTotalServicePerYear']);
    Route::get('/total/wash/monthly/{context}/{year}', [QueriesStatsApi::class, 'getTotalWashPerYear']);
});

Route::prefix('/v1/question')->group(function () {
    Route::get('/faq', [QueriesFAQController::class, 'getShowingFAQ']);
});

Route::prefix('/v1/dictionary')->group(function () {
    Route::get('/type/{type}', [QueriesDictionaryApi::class, 'getDictionaryByType']);
});

Route::prefix('/v1/trip')->group(function () {
    Route::get('/discovered', [QueriesTripApi::class, 'getTripDiscovered']);
});

######################### Private Route #########################

Route::post('/v1/logout', [CommandAuthApi::class, 'postLogout'])->middleware(['auth:sanctum']);

Route::prefix('/v1/vehicle')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsVehicleApi::class, 'postVehicle']);
    Route::post('/doc/{id}', [CommandsVehicleApi::class, 'postVehicleDoc']);
    Route::get('/header', [QueriesVehicleApi::class, 'getAllVehicleHeader']);
    Route::get('/name', [QueriesVehicleApi::class, 'getAllVehicleName']);
    Route::get('/fuel', [QueriesVehicleApi::class, 'getAllVehicleFuel']);
    Route::get('/detail/{id}', [QueriesVehicleApi::class, 'getVehicleDetailById']);
    Route::get('/detail/full/{id}', [QueriesVehicleApi::class, 'getVehicleFullDetailById']);
    Route::get('/trip/summary/{id}', [QueriesVehicleApi::class, 'getVehicleTripSummaryById']);
    Route::get('/readiness', [QueriesVehicleApi::class, 'getVehicleReadiness']);
    Route::put('/detail/{id}', [CommandsVehicleApi::class, 'putUpdateVehicleDetailById']);
    Route::post('/image/{id}', [CommandsVehicleApi::class, 'postUpdateVehicleImageById']);
    Route::post('/image_collection/{id}', [CommandsVehicleApi::class, 'postUpdateVehicleImageCollectionById']);
    Route::put('/recover/{id}', [CommandsVehicleApi::class, 'putRecoverVehicleById']);
    Route::delete('/delete/{id}', [CommandsVehicleApi::class, 'softDeleteVehicleById']);
    Route::delete('/destroy/{id}', [CommandsVehicleApi::class, 'hardDeleteVehicleById']);
    Route::delete('/document/destroy/{vehicle_id}/{doc_id}', [CommandsVehicleApi::class, 'hardDeleteVehicleDocById']);
    Route::delete('/image_collection/destroy/{vehicle_id}/{image_id}', [CommandsVehicleApi::class, 'hardDeleteVehicleImageCollectionById']);
});

Route::prefix('/v1/dictionary')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsDictionaryApi::class, 'postCreateDictionary']);
    Route::delete('/{id}', [CommandsDictionaryApi::class, 'hardDeleteDictionaryById']);
});

Route::prefix('/v1/wash')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesWashController::class, 'getAllWashHistory']);
    Route::delete('/destroy/{id}', [CommandsWashController::class, 'hardDeleteWashById']);
    Route::get('/last/{vehicle_id}', [QueriesWashController::class, 'getLastWashByVehicleId']);
    Route::get('/summary', [QueriesWashController::class, 'getWashSummaryByVehicleId']);
    Route::post('/', [CommandsWashController::class, 'postCreateWash']);
    Route::put('/finish/{id}', [CommandsWashController::class, 'putFinishWashById']);
    Route::put('/{id}', [CommandsWashController::class, 'putUpdateWashById']);
});

Route::prefix('/v1/history')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesHistoryController::class, 'getAllHistory']);
    Route::delete('/destroy/{id}', [CommandsHistoryController::class, 'hardDeleteHistoryById']);
});

Route::prefix('/v1/error')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesErrorController::class, 'getAllError']);
    Route::delete('/destroy/{id}', [CommandsErrorController::class, 'hardDeleteErrorById']);
});

Route::prefix('/v1/reminder')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/next', [QueriesReminderController::class, 'getNextReminder']);
    Route::get('/', [QueriesReminderController::class, 'getAllReminder']);
    Route::get('/recently', [QueriesReminderController::class, 'getRecentlyReminder']);
    Route::get('/vehicle/{vehicle_id}', [QueriesReminderController::class, 'getReminderByVehicle']);
    Route::delete('/destroy/{id}', [CommandsReminderController::class, 'hardDeleteReminderById']);
    Route::post('/', [CommandsReminderController::class, 'postCreateReminder']);
});

Route::prefix('/v1/service')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesServiceController::class, 'getAllService']);
    Route::get('/next', [QueriesServiceController::class, 'getNextService']);
    Route::get('/spending', [QueriesServiceController::class, 'getAllServiceSpending']);
    Route::get('/vehicle/{vehicle_id}', [QueriesServiceController::class, 'getServiceByVehicle']);
    Route::post('/', [CommandsServiceController::class, 'postService']);
    Route::delete('/destroy/{id}', [CommandsServiceController::class, 'hardDeleteServiceById']);
    Route::put('/{id}', [CommandsServiceController::class, 'putUpdateServiceById']);
});

Route::prefix('/v1/driver')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesDriverController::class, 'getAllDriver']);
    Route::get('/name', [QueriesDriverController::class, 'getAllDriverName']);
    Route::get('/vehicle', [QueriesDriverController::class, 'getDriverVehicle']);
    Route::get('/vehicle/list', [QueriesDriverController::class, 'getDriverVehicleManageList']);
    Route::post('/vehicle', [CommandsDriverController::class, 'postCreateDriverVehicle']);
    Route::post('/', [CommandsDriverController::class, 'postCreateDriver']);
    Route::delete('/destroy/{id}', [CommandsDriverController::class, 'hardDeleteDriverById']);
    Route::delete('/destroy/relation/{id}', [CommandsDriverController::class, 'hardDeleteDriverRelationById']);
    Route::put('/{id}', [CommandsDriverController::class, 'putUpdateDriverById']);
});

Route::prefix('/v1/stats')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/total/trip/{context}', [QueriesStatsApi::class, 'getTotalTripByContext']);
    Route::get('/total/most_person_trip_with', [QueriesStatsApi::class, 'getPersonWithMostTripWith']);
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
    Route::post('/', [CommandsFuelController::class, 'postCreateFuel']);
    Route::put('/{id}', [CommandsFuelController::class, 'putUpdateFuelById']);
});

Route::prefix('/v1/trip')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsTripController::class, 'postCreateTrip']);
    Route::get('/', [QueriesTripApi::class, 'getAllTrip']);
    Route::get('/last', [QueriesTripApi::class, 'getLastTrip']);
    Route::get('/calendar', [QueriesTripApi::class, 'getTripCalendar']);
    Route::get('/coordinate/{trip_location_name}', [QueriesTripApi::class, 'getCoordinateByTripLocationName']);
    Route::get('/driver/{driver_id}', [QueriesTripApi::class, 'getAllTripByDriverId']);
    Route::delete('/destroy/{id}', [CommandsTripController::class, 'hardDeleteTripById']);
    Route::put('/{id}', [CommandsTripController::class, 'putUpdateTripById']);
});

Route::prefix('/v1/inventory')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesInventoryController::class, 'getAllInventory']);
    Route::get('/vehicle/{vehicle_id}', [QueriesInventoryController::class, 'getInventoryByVehicle']);
    Route::delete('/destroy/{id}', [CommandsInventoryController::class, 'hardDeleteInventoryById']);
    Route::post('/', [CommandsInventoryController::class, 'postCreateInventory']);
    Route::put('/{id}', [CommandsInventoryController::class, 'putUpdateInventoryById']);
});

Route::prefix('/v1/user')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesUserController::class, 'getAllUser']);
    Route::get('/my_year', [QueriesUserController::class, 'getContentYear']);
    Route::get('/my_profile', [QueriesUserController::class, 'getMyProfile']);
    Route::put('/update_telegram_id', [CommandsUserController::class, 'putUpdateTelegramId']);
    Route::put('/validate_telegram_id', [CommandsUserController::class, 'putValidateTelegramId']);
    Route::put('/update_profile', [CommandsUserController::class, 'putUpdateProfile']);
});

Route::prefix('/v1/export')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/wash', [QueriesExportController::class, 'exportWashHistory']);
    Route::get('/trip', [QueriesExportController::class, 'exportTripHistory']);
    Route::get('/fuel', [QueriesExportController::class, 'exportFuelHistory']);
    Route::get('/inventory', [QueriesExportController::class, 'exportInventory']);
    Route::get('/service', [QueriesExportController::class, 'exportService']);
    Route::get('/driver', [QueriesExportController::class, 'exportDriver']);
});