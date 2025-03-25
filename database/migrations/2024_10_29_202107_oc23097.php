<?php

use App\Support\Database\InsertMenu;
use Illuminate\Database\Migrations\Migration;

class Oc23097 extends Migration
{
    use InsertMenu;

    public function up()
    {
        $descrMenuPai   = 'Relat�rios';
        $descrModulo    = 'Configura��o';
        $helpItemPai    = 'relat�rios';
        $desctecItemPai = '';

        $descrNovoMenu  = 'Assinantes';
        $arquivoMenu    = 'con2_assinantes_001.php';
        $helpNovoMenu   = 'Relat�rio de Assinantes.';
        $this->criaMenuNovo($descrMenuPai, $descrModulo, $descrNovoMenu, $arquivoMenu, $helpNovoMenu, $helpItemPai, $desctecItemPai);
    }

    //DB:CONFIGURA��O > Configura��o > Relat�rios > Assinantes

    private function criaMenuNovo($descrMenuPai, $descrModulo, $descrNovoMenu, $arquivoMenu, $helpNovoMenu, $helpItemPai, $desctecItemPai)
    {
        $this->insertItemMenu($descrNovoMenu, $arquivoMenu, $helpNovoMenu);

        $this->insertMenu($descrMenuPai, $descrModulo, $helpItemPai, $desctecItemPai);
    }
}