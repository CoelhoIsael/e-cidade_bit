<?php

use Illuminate\Support\Facades\Route;

// Agrupar rotas do m�dulo Configura��o
Route::prefix('configuracao')->group(function () {
    // Inclui rotas do subm�dulo Compras
    require __DIR__.'/configuracao/configuracao.php';
});
