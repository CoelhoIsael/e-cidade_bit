<?php

use Illuminate\Support\Facades\Route;

// Agrupar rotas do m�dulo Configura��o
Route::prefix('relatorios')->group(function () {
    // Rotas para Procedimentos
    require __DIR__.'/relatorios/relatoriodeusuarios.php';
});
