<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class InsertDataJulgitemstatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $data = [
            ['l31_label' => 'Em aberto', 'l31_desc' => 'Dispon�vel para receber lances.'],
            ['l31_label' => 'Julgado', 'l31_desc' => 'Etapa de lances conclu�da para o item.'],
            ['l31_label' => 'Frustrado', 'l31_desc' => 'Falta de Propostas: N�o houve empresas interessadas em fornecer o item ou servi�o e, portanto, nenhuma proposta foi apresentada. Propostas Desclassificadas: Todas as propostas apresentadas foram desclassificadas por n�o atenderem aos requisitos t�cnicos ou legais estabelecidos no edital. Pre�os Invi�veis: As propostas apresentadas tiveram pre�os superiores aos valores de refer�ncia ou ao or�amento dispon�vel, inviabilizando a contrata��o. Falta de Documenta��o: Os licitantes n�o apresentaram a documenta��o exigida corretamente, levando � desclassifica��o das propostas.'],
            ['l31_label' => 'Recurso', 'l31_desc' => 'Significa que uma ou mais partes envolvidas no processo apresentaram um recurso administrativo contestando alguma decis�o relacionada a esse item espec�fico.'],
            ['l31_label' => 'Cancelado', 'l31_desc' => 'Altera��es nas Necessidades: A administra��o pode ter identificado que o item n�o � mais necess�rio ou que as especifica��es precisam ser revisadas. Erro no Edital: Pode ter sido detectado algum erro ou inconsist�ncia no edital que comprometa a legalidade ou a clareza do processo licitat�rio. Quest�es Or�ament�rias: Pode ocorrer uma reavalia��o do or�amento dispon�vel, levando � decis�o de cancelar o item para adequa��o financeira. Mudan�as de Planejamento: Altera��es estrat�gicas ou de planejamento que influenciam as prioridades e necessidades da administra��o. Impugna��o ou Resultado de Recurso: O item pode ter sido objeto de impugna��o por parte dos licitantes ou terceiros, resultando na necessidade de cancelamento para evitar futuros problemas legais.'],
            ['l31_label' => 'Sem acordo', 'l31_desc' => 'Pre�os Incompat�veis: As propostas apresentadas n�o atenderam �s expectativas de pre�o da administra��o p�blica ou estavam acima do valor de refer�ncia estabelecido. Negocia��es Mal Sucedidas: Durante as fases de lances e negocia��es, n�o foi poss�vel obter um pre�o que fosse considerado justo ou vantajoso para a administra��o. Condi��es Inadequadas: �s condi��es propostas pelos licitantes (prazo de entrega, qualidade do produto, etc.) n�o foram consideradas adequadas ou aceit�veis.'],
            ['l31_label' => 'Aguardando Readequa��o', 'l31_desc' => 'O lote est� pendente de ajustes na proposta antes de seguir para a pr�xima etapa']
        ];

        foreach ($data as &$item) {
            $item['l31_label'] = mb_convert_encoding($item['l31_label'], 'UTF-8', 'ISO-8859-1');
            $item['l31_desc'] = mb_convert_encoding($item['l31_desc'], 'UTF-8', 'ISO-8859-1');
        }

        DB::table('licitacao.julgitemstatus')->insert($data);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('licitacao.julgitemstatus')->whereIn('l31_label', [
            mb_convert_encoding('Em aberto', 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding('Julgado', 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding('Frustrado', 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding('Recurso', 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding('Cancelado', 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding('Sem acordo', 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding('Melhor Proposta', 'UTF-8', 'ISO-8859-1'),
            mb_convert_encoding('Aguardando Readequa��o', 'UTF-8', 'ISO-8859-1')
        ])->delete();
    }
}
