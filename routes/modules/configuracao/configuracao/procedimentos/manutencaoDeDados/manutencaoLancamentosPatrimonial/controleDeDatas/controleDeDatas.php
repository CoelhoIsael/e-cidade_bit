<?php

use Illuminate\Support\Facades\Route;

// Rotas espec�ficas para o m�dulo de procedimentos
Route::prefix('controle-de-datas')->group(function () {
    require __DIR__.'/autorizacoesDeEmpenho.php';
});
