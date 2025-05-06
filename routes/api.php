<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ServiceProviderController;



Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/providers', [ServiceProviderController::class, 'index']);
Route::get('/providers/{provider:slug}', [ServiceProviderController::class, 'show']);
