<?php
namespace App\Application\Patrimonial\DispensasInexigibilidades;

use App\Repositories\Contracts\HandleRepositoryInterface;

class ListagemCriterioJugamento implements HandleRepositoryInterface{
    public function handle(object $data)
    {
        $response = [];
        if(!empty($data->l03_pctipocompratribunal) && in_array($data->l03_pctipocompratribunal, [101])){
            $response = [
                1 => "1 - Menor Pre�o",
                2 => "2 - Maior Desconto",
                5 => "5 - Maior Lance",
                7 => "7 - N�o Aplic�vel",
            ];
        } else if(!empty($data->l03_pctipocompratribunal) && in_array($data->l03_pctipocompratribunal, [103, 100, 102])){
            $response = [
                7 => "7 - N�o Aplic�vel"
            ];
        } else if(!empty($data->l03_pctipocompratribunal) && in_array($data->l03_pctipocompratribunal, [48,51,50,110,54,53,52,49,104,110])){
            $response = [
                1 => "1 - Menor Pre�o",
                2 => "2 - Maior Desconto",
                4 => "4 - T�cnica e Pre�o",
                5 => "5 - Maior Lance",
                6 => "6 - Maior Retorno Econ�mico",
                7 => "7 - N�o Aplic�vel",
                8 => "8 - Melhor T�cnica",
                9 => "9 - Conte�do Art�stico",
            ];
        }

        if(!empty($data->l20_leidalicitacao) && $data->l20_leidalicitacao == 2){
            unset($response[2]);
            unset($response[6]);
            unset($response[8]);
            unset($response[9]);
        }

        return ['data' => $response];
    }
}
