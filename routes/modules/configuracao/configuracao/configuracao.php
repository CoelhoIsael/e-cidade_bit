<?php

use Illuminate\Support\Facades\Route;

// Agrupar rotas do m�dulo Configura��o
Route::prefix('configuracao')->group(function () {
    // Rotas para Procedimentos
    require __DIR__.'/procedimentos/procedimentos.php';
});
