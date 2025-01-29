<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthApi\Commands as CommandAuthApi;
use App\Http\Controllers\Api\AuthApi\Queries as QueryAuthApi;
use App\Http\Controllers\Api\VehicleApi\Queries as QueriesVehicleApi;
use App\Http\Controllers\Api\CleanApi\Queries as QueriesCleanApi;
use App\Http\Controllers\Api\DictionaryApi\Queries as QueriesDictionaryController;
use App\Http\Controllers\Api\DictionaryApi\Commands as CommandsDictionaryController;

######################### Public Route #########################

Route::post('/v1/login', [CommandAuthApi::class, 'login']);

######################### Private Route #########################

Route::post('/v1/logout', [QueryAuthApi::class, 'logout'])->middleware(['auth:sanctum']);

Route::prefix('/v1/vehicle')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/header', [QueriesVehicleApi::class, 'getAllVehicleHeader']);
    Route::get('/detail/{id}', [QueriesVehicleApi::class, 'getVehicleDetailById']);
});

Route::prefix('/v1/dictionary')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/type/{type}', [QueriesDictionaryController::class, 'getDictionaryByType']);
    Route::post('/', [CommandsDictionaryController::class, 'postDictionary']);
    Route::delete('/{id}', [CommandsDictionaryController::class, 'hardDeleteDictionaryById']);
});

Route::prefix('/v1/clean')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/', [QueriesCleanApi::class, 'getAllCleanHistory']);
});