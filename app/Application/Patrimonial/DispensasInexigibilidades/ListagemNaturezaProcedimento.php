<?php
namespace App\Application\Patrimonial\DispensasInexigibilidades;

use App\Repositories\Contracts\HandleRepositoryInterface;

class ListagemNaturezaProcedimento implements HandleRepositoryInterface{
    public function handle(object $data)
    {
        $data = [
            1 => "1 - Normal",
            2 => "2 - Registro de Pre�o",
        ];
        return ['data' => $data];
    }
}
