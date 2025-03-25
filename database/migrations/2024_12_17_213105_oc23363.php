<?php

use Illuminate\Database\Migrations\Migration;
use App\Support\Database\InsertMenu;

class Oc23363 extends Migration
{
    use InsertMenu;

    public function up()
    {
        
        $descrMenuPai   = 'Relat�rios de Acompanhamento';
        $descrModulo    = 'Contabilidade'; // tabela db_modulos
        $helpItemPai    = 'Relat�rios de Acompanhamento';
        $desctecItemPai = 'Relat�rios de Acompanhamento';

        $descrNovoMenu  = 'Gastos com Folha C�MARA';
        $arquivoMenu    = 'con2_gastoscomfolhacamara_001.php';
        $helpNovoMenu   = 'Gastos com Folha C�MARA';
        $this->criaMenuNovo($descrMenuPai, $descrModulo, $descrNovoMenu, $arquivoMenu, $helpNovoMenu, $helpItemPai, $desctecItemPai);
    }

    // DB:FINANCEIRO > Contabilidade > Relat�rios > Relat�rios de Acompanhamento > Gastos com Folha C�MARA

    private function criaMenuNovo($descrMenuPai, $descrModulo, $descrNovoMenu, $arquivoMenu, $helpNovoMenu, $helpItemPai, $desctecItemPai)
    {
        $this->insertItemMenu($descrNovoMenu, $arquivoMenu, $helpNovoMenu);

        $this->insertMenu($descrMenuPai, $descrModulo, $helpItemPai, $desctecItemPai);
    }
}
