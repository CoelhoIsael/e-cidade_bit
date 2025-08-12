<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\IptuFotosController;
use App\Http\Controllers\RedesimController;
use App\Http\Controllers\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\FaseDeLancesController;
use Illuminate\Support\Facades\Route;

Route::middleware(['legacySession', 'authEcidadeUser', 'auth.basic'])
    ->prefix('web')
    ->group(function () {
        Route::get('/welcome', function () {
            return view('modelo');
        })->name('welcome');
        Route::post('iptufoto/upload', [IptuFotosController::class, 'upload'])->name('iptufotos-upload');
        Route::get('iptufoto/list/{matric}', [IptuFotosController::class, 'list'])->name('iptufotos-list');
        Route::post('iptufoto/update', [IptuFotosController::class, 'update'])->name('iptufotos-update');
        Route::delete('iptufoto/delete/{id}/{matric}', [IptuFotosController::class, 'delete'])->name('iptufotos-delete');
        Route::get('iptufoto/show/{id}', [IptuFotosController::class, 'show'])->name('iptufotos-show');

        //audit
        Route::get('/audits', [AuditController::class, 'index'])->name('audits.index');

        //redesim
        Route::prefix('redesim')->group(function () {
            Route::post('/companiesReport', [RedesimController::class, 'companiesReport'])
                ->name('redesim.companies.report');
            Route::get('/alvara', [RedesimController::class, 'alvara'])
                ->name('redesim.alvara');
            Route::post('/alvara/create', [RedesimController::class, 'create'])
                ->name('redesim.alvara.create');
        });

        require base_path('routes/modules/patrimonial/patrimonial.php');
        require base_path('routes/modules/configuracao/configuracao.php');

        Route::prefix('datagrid')->group(function () {
            Route::get('/get-liclicita', [FaseDeLancesController::class, 'getLiclicita'])->name('datagrid.getLiclicita');
            Route::get('/get-liclicita-item', [FaseDeLancesController::class, 'getLiclicitaItens'])->name('datagrid.getLiclicitaItens');
        });
    });

Route::fallback(function () {
    abort(404);
});
