<?php
namespace App\Application\Patrimonial\DispensasInexigibilidades;

use App\Repositories\Contracts\HandleRepositoryInterface;

class ListagemRegimeExecucao implements HandleRepositoryInterface{
    public function handle(object $data)
    {
        $result = [
            1 => "1 - Empreitada por Pre�o Global",
            2 => "2 - Empreitada por Pre�o Unit�rio",
            3 => "3 - Empreitada Integral",
            4 => "4 - Tarefa",
            // 5 => "5 - Execu��o Direta",
            6 => "6 - Contrata��o integrada",
            7 => "7 - Contrata��o semi-integrada",
            8 => "8 - Fornecimento e presta��o de servi�o associado"
        ];

        if(!empty($data->l20_leidalicitacao) && $data->l20_leidalicitacao == 2){
            unset($result[6]);
            unset($result[7]);
            unset($result[8]);
        }

        return ['data' => $result];
    }
}
