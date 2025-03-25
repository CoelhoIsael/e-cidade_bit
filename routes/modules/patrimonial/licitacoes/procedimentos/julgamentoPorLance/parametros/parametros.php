<?php

use App\Http\Controllers\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\Parametros\ParametrosController;
use Illuminate\Support\Facades\Route;

// Rotas espec�ficas para o m�dulo de parametros
Route::prefix('parametros')->group(function () {
    Route::get('/', [ParametrosController::class, 'index'])->name('parametros.index');
    Route::post('/update', [ParametrosController::class, 'update'])->name('parametros.update');
});
