<?php

use Illuminate\Support\Facades\Route;

// Rotas espec�ficas para o m�dulo de procedimentos
Route::prefix('manutencao-lancamentos-patrimonial')->group(function () {
    require __DIR__.'/controleDeDatas/controleDeDatas.php';
});
