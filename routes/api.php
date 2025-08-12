<?php

use App\Http\Controllers\RedesimController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('redesimAuth')
    ->prefix('redesim')
    ->group(function () {
        Route::post('/companies', [RedesimController::class, 'index'])
            ->name('redesim.companies');
    });
