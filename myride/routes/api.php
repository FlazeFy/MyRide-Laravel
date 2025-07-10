<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApi\Commands as CommandAuthApi;
use App\Http\Controllers\Api\AuthApi\Queries as QueryAuthApi;
use App\Http\Controllers\Api\VehicleApi\Queries as QueriesVehicleApi;
use App\Http\Controllers\Api\VehicleApi\Commands as CommandsVehicleApi;
use App\Http\Controllers\Api\CleanApi\Queries as QueriesCleanApi;
use App\Http\Controllers\Api\DictionaryApi\Queries as QueriesDictionaryApi;
use App\Http\Controllers\Api\DictionaryApi\Commands as CommandsDictionaryApi;
use App\Http\Controllers\Api\TripApi\Commands as CommandsTripApi;
use App\Http\Controllers\Api\TripApi\Queries as QueriesTripApi;
use App\Http\Controllers\Api\StatsApi\Queries as QueriesStatsApi;
use App\Http\Controllers\Api\UserApi\Queries as QueriesUserController;
use App\Http\Controllers\Api\UserApi\Commands as CommandsUserController;
use App\Http\Controllers\Api\ExportApi\Queries as QueriesExportController;
use App\Http\Controllers\Api\QuestionApi\FAQQueries as QueriesFAQController;

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
    Route::get('/', [QueriesCleanApi::class, 'getAllCleanHistory']);
});

Route::prefix('/v1/stats')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/total/trip/{context}', [QueriesStatsApi::class, 'getTotalTripByContext']);
    Route::get('/total/vehicle/{context}', [QueriesStatsApi::class, 'getTotalVehicleByContext']);
    Route::get('/total/trip/monthly/{year}/{vehicle_id}', [QueriesStatsApi::class, 'getTotalTripByVehiclePerYear']);
});

Route::prefix('/v1/trip')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsTripApi::class, 'postTrip']);
    Route::get('/', [QueriesTripApi::class, 'getAllTrip']);
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
});