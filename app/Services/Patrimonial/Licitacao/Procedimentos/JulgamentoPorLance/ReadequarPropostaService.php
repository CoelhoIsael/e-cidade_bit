<?php

namespace App\Services\Patrimonial\Licitacao\Procedimentos\JulgamentoPorLance;

use App\Exceptions\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\JulgamentoException;
use App\Models\Patrimonial\Compras\Pcorcamjulg;
use App\Models\Patrimonial\Compras\Pcorcamval;
use App\Repositories\Patrimonial\Compras\PcorcamjulgRepository;
use App\Repositories\Patrimonial\Compras\PcorcamvalRepository;
use App\Repositories\Patrimonial\Licitacao\JulgItemRepository;
use App\Repositories\Patrimonial\Licitacao\JulgItemStatusRepository;
use App\Repositories\Patrimonial\Licitacao\JulgLanceRepository;
use App\Repositories\Patrimonial\Licitacao\LicilicitemRepository;
use Illuminate\Support\Facades\DB;

class ReadequarPropostaService
{
    protected $licilicitemRepository;
    protected $julgitemRepository;
    protected $julgLanceRepository;
    protected $pcorcamjulgRepository;
    protected $pcorcamvalRepository;
    protected $julgamentoLoteService;
    protected $itemStatusRepository;

    public function __construct() {
        $this->licilicitemRepository = new LicilicitemRepository;
        $this->julgitemRepository = new JulgItemRepository;
        $this->julgLanceRepository = new JulgLanceRepository;
        $this->pcorcamjulgRepository = new PcorcamjulgRepository;
        $this->pcorcamvalRepository = new pcorcamvalRepository;
        $this->julgamentoLoteService = new JulgamentoLoteService;
        $this->itemStatusRepository = new JulgItemStatusRepository;
    }

    /**
     * Obt�m os itens da readequa��o de proposta e calcula os valores unit�rios e totais.
     *
     * - Recupera os itens de proposta com base no c�digo de licita��o, c�digo do fornecedor e n�mero do lote.
     * - Faz a jun��o dos itens com os valores unit�rios e marcas, se existirem, e calcula os valores totais para cada item.
     * - Retorna a lista de itens com os valores calculados.
     *
     * @param int $licitacaoCodigo C�digo da licita��o.
     * @param string $orcamforne C�digo do fornecedor.
     * @param int $numeroLote N�mero do lote.
     * 
     * @return \Illuminate\Support\Collection A lista de itens da proposta com valores unit�rios e totais calculados.
     */
    public function obterItensDaReadequecaoDeProposta($licitacaoCodigo, $orcamforne, $numeroLote)
    {
        $rsitensProposta = $this->licilicitemRepository->getBiddingItems($licitacaoCodigo, $orcamforne, $numeroLote);

        $itens = $rsitensProposta->pluck('pc22_orcamitem')->toArray();
        $pcorcamval = Pcorcamval::whereIn('pc23_orcamitem', $itens)->get();

        $keys = $pcorcamval->pluck('pc23_orcamitem');
        $values = $pcorcamval->map(fn($item) => [
            'pc23_vlrun' => $item['pc23_vlrun'], 
            'pc23_obs' => $item['pc23_obs']
        ]);

        $orcamitemValorUnitarioEMarca = $keys->combine($values)->toArray();

        $rsitensProposta->map(function ($item) use ($orcamitemValorUnitarioEMarca) {
            $orcamitem = $item->pc22_orcamitem;
        
            if (isset($orcamitemValorUnitarioEMarca[$orcamitem])) {
                $item->vlr_unitario = $orcamitemValorUnitarioEMarca[$orcamitem]['pc23_vlrun'];
                $item->vlr_total = "R$ " . number_format(round(($item->quantidade * $orcamitemValorUnitarioEMarca[$orcamitem]['pc23_vlrun']), 2), 2, ',', '.');
            } else {
                $item->vlr_unitario = (empty($orcamitemValorUnitarioEMarca[$orcamitem]['pc23_vlrun'])) ? '' : $orcamitemValorUnitarioEMarca[$orcamitem]['pc23_vlrun'];
                $item->vlr_total = (empty($orcamitemValorUnitarioEMarca[$orcamitem]['pc23_vlrun'])) ? 'R$ 0,00' : "R$ " . number_format(round(($item->quantidade * $orcamitemValorUnitarioEMarca[$orcamitem]['pc23_vlrun']), 2), 2, ',', '.');
            }
        
            return $item;
        });

        return $rsitensProposta;
    }

    /**
     * Verifica se a proposta existe no banco de dados, com base nos itens fornecidos.
     * 
     * A fun��o verifica a exist�ncia de itens nas tabelas 'Pcorcamval' e 'Pcorcamjulg'.
     * Se houver registros em ambas, a proposta existe. Se n�o houver nenhum, a proposta n�o existe.
     * Se houver registros em uma tabela mas n�o na outra, � gerada uma exce��o de julgamento.
     *
     * @param array $orcamItens Lista de itens com o campo 'orcamitem' que ser� utilizado para buscar nas tabelas.
     * 
     * @return bool Retorna true se a proposta existir, false se n�o existir.
     * 
     * @throws JulgamentoException Lan�a exce��o se houver inconsist�ncia entre as tabelas.
     */
    public function verificarPropostaExistente($orcamItens)
    {
        $itens = array_column($orcamItens, 'orcamitem');

        $pcorcamval = Pcorcamval::whereIn('pc23_orcamitem', $itens)->get();
        $pcorcamjulg = Pcorcamjulg::whereIn('pc24_orcamitem', $itens)->get();

        if ($pcorcamval->isNotEmpty() && $pcorcamjulg->isNotEmpty()) {
            return true;
        } else if ($pcorcamval->isEmpty() && $pcorcamjulg->isEmpty()) {
            return false;
        } else if (($pcorcamval->isNotEmpty() && $pcorcamjulg->isEmpty()) || ($pcorcamval->isEmpty() && $pcorcamjulg->isNotEmpty())) {
            throw new JulgamentoException("#RP01 - Ocorreu uma inconsist�ncia na verifica��o das propostas, onde valores ou julgamentos est�o presentes de forma isolada; verifique os dados e assegure a integridade das informa��es antes de prosseguir.");
        }
    }

    /**
     * Salva ou atualiza a proposta com base na exist�ncia de uma proposta j� cadastrada.
     * 
     * @param string $licitacao C�digo da licita��o.
     * @param string $lote C�digo do lote.
     * @param string $orcamforne C�digo do fornecedor.
     * @param array $itens Itens da proposta, incluindo os dados necess�rios para salvar a proposta.
     * 
     * @return mixed Retorna o resultado da inser��o ou atualiza��o da proposta.
     */
    public function salvarProposta($licitacao, $lote, $orcamforne, $itens)
    {
        $this->validarProposta($lote, $itens, $licitacao);

        if ($this->verificarPropostaExistente($itens)) {

            return $this->atualizarProposta($licitacao, $lote, $orcamforne, $itens);

        } else {

            return $this->inserirProposta($licitacao, $lote, $orcamforne, $itens);

        }
    }

    /**
     * Valida os itens da proposta em rela��o ao lance vencedor do lote e a integridade dos valores unit�rios.
     * 
     * Este m�todo realiza a valida��o dos itens da proposta, verificando se o valor total dos itens n�o ultrapassa o lance
     * vencedor do lote e se todos os valores unit�rios dos itens s�o v�lidos (n�o nulos ou zero).
     * 
     * Caso algum item tenha valor unit�rio inv�lido ou caso a soma dos valores unit�rios ultrapasse o lance vencedor, uma exce��o
     * ser� lan�ada para impedir que a proposta seja salva.
     * 
     * @param string $lote C�digo do lote para o qual os itens est�o sendo validados.
     * @param array $itens Lista de itens da proposta contendo os valores unit�rios a serem validados.
     * 
     * @throws JulgamentoException Caso a soma dos valores unit�rios dos itens seja maior que o lance vencedor ou
     *                             se algum item tiver valor unit�rio nulo ou igual a zero.
     */
    private function validarProposta($lote, $itens, $licitacao)
    {
        $julgItem = $this->julgitemRepository->find('l30_numerolote', $lote);
        $lanceVencedor = $this->julgLanceRepository->findLastBidNotNull($julgItem[0]->l30_codigo);

        $vlrTotal = array_column($itens, 'vlrTotal');
        $soma = array_sum(array_filter($vlrTotal));

        if (empty($lanceVencedor)) {
            $menorProposta = $this->licilicitemRepository->getLowestBidWithoutBidding($licitacao, $lote);
            $lanceVencedor = $menorProposta->total_valor;
        } else {
            $lanceVencedor = $lanceVencedor->l32_lance;
        }

        if ($soma > floatval($lanceVencedor)) {
            throw new JulgamentoException("#RP02 - O valor total da proposta n�o pode exceder o �ltimo lance ou proposta vencedora. Verifique os valores unit�rios dos itens antes de prosseguir.");
        }

        $itensInvalidos = array_filter($itens, function($item) {
            return empty($item['vlrUnitario']) || $item['vlrUnitario'] === 0;
        });
        
        if (!empty($itensInvalidos)) {
            throw new JulgamentoException("#RP03 - Todos os itens da proposta devem possuir um valor unit�rio v�lido e maior que zero. Revise os dados e tente novamente.");
        }
    }

    /**
     * Insere os itens da proposta nas tabelas de julgamento e valores do or�amento.
     * 
     * Este m�todo processa os itens fornecidos na proposta, calcula os valores necess�rios e insere as informa��es nas tabelas
     * `Pcorcamjulg` (julgamento de or�amento) e `Pcorcamval` (valores de or�amento). Para cada item da proposta, s�o calculados
     * os valores unit�rios e totais com base nas quantidades do item, al�m de associar as marcas e outras informa��es relevantes.
     * 
     * Caso ocorra algum erro durante a inser��o dos dados nas tabelas, uma exce��o `JulgamentoException` � lan�ada.
     * 
     * @param string $licitacao C�digo da licita��o para a qual os itens s�o inseridos.
     * @param string $lote C�digo do lote para o qual os itens s�o inseridos.
     * @param string $orcamforne C�digo do fornecedor relacionado � proposta.
     * @param array $itens Lista de itens da proposta a serem inseridos, cada um contendo dados como `orcamitem`, `vlrUnitario`, `marca`, etc.
     * 
     * @return array Retorna um array com as informa��es inseridas nas tabelas `Pcorcamjulg` e `Pcorcamval`.
     * 
     * @throws JulgamentoException Caso ocorra um erro na inser��o dos itens nas tabelas de or�amento.
     */
    private function inserirProposta($licitacao, $lote, $orcamforne, $itens)
    {
        $quantidadesDosItens = $this->licilicitemRepository->getBidItemQuantities($licitacao, $lote, $orcamforne);
        $mapaQuantidade = $quantidadesDosItens->pluck('pc11_quant', 'l21_ordem');

        $pcorcamjulgData = array();
        $pcorcamvalData = array();

        foreach ($itens as $i => &$item) {
            $pcorcamjulgData[$i]['pc24_orcamitem'] = $item['orcamitem'];
            $pcorcamjulgData[$i]['pc24_pontuacao'] = 1;
            $pcorcamjulgData[$i]['pc24_orcamforne'] = $orcamforne;

            $pcorcamvalData[$i]['pc23_orcamforne'] = $orcamforne;
            $pcorcamvalData[$i]['pc23_orcamitem'] = $item['orcamitem'];

            $valorCalculado = $mapaQuantidade[$item['ordem']] * $item['vlrUnitario'];
            $pcorcamvalData[$i]['pc23_valor'] = round($valorCalculado, 2);

            $pcorcamvalData[$i]['pc23_quant'] = $mapaQuantidade[$item['ordem']] ?? null;
            $pcorcamvalData[$i]['pc23_obs'] = $item['marca'];
            $pcorcamvalData[$i]['pc23_vlrun'] = floatval($item['vlrUnitario']);
        }

        $pcorcamjulg = Pcorcamjulg::insert($pcorcamjulgData);

        if (!$pcorcamjulg) {
            throw new JulgamentoException("#RP04 - Falha ao inserir os dados de julgamento da proposta. Verifique as informa��es e tente novamente.");
        }
        
        $pcorcamval = Pcorcamval::insert($pcorcamvalData);

        if (!$pcorcamval) {
            throw new JulgamentoException("#RP05 - Erro ao registrar os valores da proposta. Certifique-se de que os dados est�o corretos e tente novamente.");
        }

        $statusItem = $this->itemStatusRepository->findLabel(mb_convert_encoding('Julgado', 'UTF-8', 'ISO-8859-1'));
        $statusItemUpdate = $this->julgamentoLoteService->alterarStatusItem($lote, $statusItem->l31_codigo, mb_convert_encoding('O status do item foi atualizado para julgado ap�s a conclus�o da rotina de julgamento. em readequar propostas.', 'UTF-8', 'ISO-8859-1'));
        
        if (empty($statusItemUpdate)) {
            throw new JulgamentoException("#RP06 - O status do item n�o p�de ser atualizado para Julgado. Verifique a integridade dos dados e reexecute o processo.");
        }

        return [
            'pcorcamjulg' => $pcorcamjulg,
            'pcorcamval' => $pcorcamval
        ];
    }

    /**
     * Atualiza os itens da proposta nas tabelas de julgamento e valores de or�amento.
     * 
     * Este m�todo atualiza os dados existentes de uma proposta nas tabelas `Pcorcamjulg` (julgamento de or�amento) e `Pcorcamval` (valores de or�amento).
     * Para cada item fornecido, ele atualiza as informa��es de pontua��o, quantidade, valores e outras propriedades. O processo de atualiza��o � feito
     * por meio de uma transa��o para garantir a consist�ncia dos dados. Caso ocorra um erro durante a atualiza��o, a transa��o � revertida e uma exce��o � lan�ada.
     * 
     * @param string $licitacao C�digo da licita��o que est� sendo atualizada.
     * @param string $lote C�digo do lote relacionado � proposta.
     * @param string $orcamforne C�digo do fornecedor para o qual os itens est�o sendo atualizados.
     * @param array $orcamItens Lista de itens da proposta que precisam ser atualizados, contendo informa��es como `orcamitem`, `vlrUnitario`, `marca`, etc.
     * 
     * @return array Retorna um array indicando o sucesso da atualiza��o nas tabelas `Pcorcamjulg` e `Pcorcamval`. Ex: ['pcorcamjulg' => true, 'pcorcamval' => true].
     * 
     * @throws JulgamentoException Caso ocorra um erro ao tentar atualizar os itens nas tabelas de or�amento. A transa��o ser� revertida.
     */
    private function atualizarProposta($licitacao, $lote, $orcamforne, $orcamItens)
    {
        $itens = array_column($orcamItens, 'orcamitem');

        $quantidadesDosItens = $this->licilicitemRepository->getBidItemQuantities($licitacao, $lote, $orcamforne);
        $mapaQuantidade = $quantidadesDosItens->pluck('pc11_quant', 'l21_ordem');

        $pcorcamjulgData = [];
        $pcorcamvalData = [];

        foreach ($orcamItens as $i => &$item) {
            $pcorcamjulgData[$item['orcamitem']] = [
                'pc24_orcamitem' => $item['orcamitem'],
                'pc24_pontuacao' => 1,
                'pc24_orcamforne' => $orcamforne,
            ];
    
            $pcorcamvalData[$item['orcamitem']] = [
                'pc23_orcamforne' => $orcamforne,
                'pc23_orcamitem' => $item['orcamitem'],
                'pc23_valor' => round($mapaQuantidade[$item['ordem']] * $item['vlrUnitario'], 2),
                'pc23_quant' => $mapaQuantidade[$item['ordem']] ?? null,
                'pc23_obs' => $item['marca'],
                'pc23_vlrun' => floatval($item['vlrUnitario']),
            ];
        }

        DB::beginTransaction();

        try {
            foreach ($itens as $i => $item) {
                Pcorcamjulg::where('pc24_orcamitem', $item)->update($pcorcamjulgData[$item]);
            }

            foreach ($itens as $i => $item) {
                Pcorcamval::where('pc23_orcamitem', $item)->update($pcorcamvalData[$item]);
            }

            DB::commit();

            return [
                'pcorcamjulg' => true,
                'pcorcamval' => true
            ];

        } catch (\Exception $e) {
            DB::rollback();
            throw new JulgamentoException("#RP07 - Ocorreu um erro durante a atualiza��o da proposta. A transa��o foi revertida para evitar inconsist�ncias. Verifique os dados e tente novamente: " . $e->getMessage());
        }
    }

    /**
     * Deleta a proposta relacionada aos itens de or�amento fornecidos.
     * 
     * Este m�todo verifica se a proposta existe, e, se for o caso, exclui os registros correspondentes nas tabelas `Pcorcamjulg` (julgamento de or�amento)
     * e `Pcorcamval` (valores de or�amento) para os itens fornecidos. O processo de dele��o � realizado em duas etapas, e caso alguma opera��o de dele��o
     * falhe, uma exce��o ser� lan�ada, garantindo que a integridade dos dados seja mantida. Caso a proposta n�o exista, uma exce��o diferente ser� gerada.
     * 
     * @param array $orcamItens Lista de itens de or�amento que precisam ser exclu�dos. Cada item cont�m dados como `orcamitem`, entre outros.
     * 
     * @return array Retorna um array indicando o sucesso da dele��o nas tabelas `Pcorcamjulg` e `Pcorcamval`. Ex: ['pcorcamjulg' => true, 'pcorcamval' => true].
     * 
     * @throws JulgamentoException Caso a proposta n�o exista ou ocorra um erro ao tentar deletar os itens nas tabelas de or�amento.
     */
    public function deletarProposta($orcamItens, $lote)
    {
        if ($this->verificarPropostaExistente($orcamItens)) {

            $itens = array_column($orcamItens, 'orcamitem');

            $pcorcamjulg = Pcorcamjulg::whereIn('pc24_orcamitem', $itens)->delete();
            
            if (!$pcorcamjulg) {
                throw new JulgamentoException("#RP08 - Erro ao excluir os dados de julgamento da proposta. Verifique e tente novamente.");
            }

            $pcorcamval = Pcorcamval::whereIn('pc23_orcamitem', $itens)->delete();
    
            if (!$pcorcamval) {
                throw new JulgamentoException("#RP09 - Falha ao remover os valores da proposta. Confirme os dados e tente novamente.");
            }

            $statusItem = $this->itemStatusRepository->findLabel(mb_convert_encoding('Aguardando Readequa��o', 'UTF-8', 'ISO-8859-1'));
            $statusItemUpdate = $this->julgamentoLoteService->alterarStatusItem($lote, $statusItem->l31_codigo, mb_convert_encoding('O status do item foi atualizado para Aguardando Readequa��o ap�s a remover a conclus�o da rotina de julgamento em readequar propostas.', 'UTF-8', 'ISO-8859-1'));
    
            if (empty($statusItemUpdate)) {
                throw new JulgamentoException("#RP10 - O status do item n�o p�de ser atualizado para Aguardando Readequa��o. Verifique a integridade dos dados e reexecute o processo.");
            }   

            return [
                'pcorcamjulg' => $pcorcamjulg,
                'pcorcamval' => $pcorcamval
            ];

        } else {
            throw new JulgamentoException("#RP11 - N�o h� proposta existente para ser exclu�da. Verifique os dados antes de prosseguir.");
        }
    }

    public function importarItens()
    {}
}
