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

######################### Public Route #########################

Route::post('/v1/login', [CommandAuthApi::class, 'login']);

######################### Private Route #########################

Route::post('/v1/logout', [QueryAuthApi::class, 'logout'])->middleware(['auth:sanctum']);

Route::prefix('/v1/vehicle')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/header', [QueriesVehicleApi::class, 'getAllVehicleHeader']);
    Route::get('/detail/{id}', [QueriesVehicleApi::class, 'getVehicleDetailById']);
    Route::get('/detail/full/{id}', [QueriesVehicleApi::class, 'getVehicleFullDetailById']);
    Route::put('/detail/{id}', [CommandsVehicleApi::class, 'putVehicleDetailById']);
});

Route::prefix('/v1/dictionary')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/type/{type}', [QueriesDictionaryApi::class, 'getDictionaryByType']);
    Route::post('/', [CommandsDictionaryApi::class, 'postDictionary']);
    Route::delete('/{id}', [CommandsDictionaryApi::class, 'hardDeleteDictionaryById']);
});

Route::prefix('/v1/clean')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesCleanApi::class, 'getAllCleanHistory']);
});

Route::prefix('/v1/trip')->middleware(['auth:sanctum'])->group(function () {
    Route::post('/', [CommandsTripApi::class, 'postTrip']);
    Route::get('/', [QueriesTripApi::class, 'getAllTrip']);
});