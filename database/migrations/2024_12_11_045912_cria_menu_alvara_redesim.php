<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CriaMenuAlvaraRedesim extends Migration
{
    public function up()
    {
        $sql = "
            INSERT INTO configuracoes.db_itensmenu VALUES ((select max(id_item)+1 from configuracoes.db_itensmenu), 'Inclus�o (Redesim)', 'Incluir inscri��o via Redesim', 'web/redesim/alvara', 1, 1, 'Inclus�o do Alvar� via importa��o de dados do REDESIM.', 'true');
            INSERT INTO configuracoes.db_menu VALUES(1490,(select max(id_item) from configuracoes.db_itensmenu),(SELECT MAX(menusequencia)+1 AS COUNT FROM db_menu WHERE id_item = 1490),40);
        ";

        DB::unprepared($sql);
    }
}
