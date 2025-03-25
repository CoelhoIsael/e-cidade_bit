<?php
namespace App\Application\Patrimonial\DispensasInexigibilidades;

use App\Repositories\Contracts\HandleRepositoryInterface;;

class ListagemCategoriaProcesso implements HandleRepositoryInterface{
    public function handle(object $data)
    {
        $data = [
            1  => "1 - Cess�o",
            2  => "2 - Compras",
            3  => "3 - Inform�tica (TIC)",
            4  => "4 - Internacional",
            5  => "5 - Loca��o Im�veis",
            6  => "6 - M�o de Obra",
            7  => "7 - Obras",
            8  => "8 - Servi�os",
            9  => "9 - Servi�os de Engenharia",
            10 => "10 - Servi�os de Sa�de"
        ];
        return ['data' => $data];
    }
}
