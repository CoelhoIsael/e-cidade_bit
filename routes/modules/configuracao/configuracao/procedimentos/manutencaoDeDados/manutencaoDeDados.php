<?php

use Illuminate\Support\Facades\Route;

// Rotas espec�ficas para o m�dulo de procedimentos
Route::prefix('manutencao-de-dados')->group(function () {
    require __DIR__.'/manutencaoLancamentosPatrimonial/manutencaoLancamentosPatrimonial.php';
});
