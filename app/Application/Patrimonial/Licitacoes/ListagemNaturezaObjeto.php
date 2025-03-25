<?php

namespace App\Application\Patrimonial\Licitacoes;

use App\Repositories\Contracts\HandleRepositoryInterface;

class ListagemNaturezaObjeto implements HandleRepositoryInterface {
    public function handle(object $data)
    {
        $data = [
            1 => '1 - Obras e Servi�os de Engenharia',
            2 => '2 - Compras e outros servi�os',
            3 => '3 - Loca��o de im�veis',
            4 => '4 - Concess�o',
            5 => '5 - Permiss�o',
            6 => '6 - Aliena��o de bens',
            7 => '7 - Compras para obras e/ou servi�os de engenharia'
        ];
        return ['data' => $data];
    }
}
