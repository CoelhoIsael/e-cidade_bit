<?php

namespace App\Services\Patrimonial\Licitacao\Procedimentos\JulgamentoPorLance;

use App\Exceptions\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\JulgamentoException;
use App\Services\Patrimonial\Licitacao\Procedimentos\JulgamentoPorLance\FaseDeLancesService;
use App\Repositories\Patrimonial\Compras\PcorcamitemRepository;
use App\Repositories\Patrimonial\Compras\PcorcamjulgRepository;
use App\Repositories\Patrimonial\Compras\PcorcamvalRepository;
use App\Repositories\Patrimonial\Licitacao\JulgForneHistRepository;
use App\Repositories\Patrimonial\Licitacao\JulgForneRepository;
use App\Repositories\Patrimonial\Licitacao\JulgForneStatusRepository;
use App\Repositories\Patrimonial\Licitacao\JulgItemHistRepository;
use App\Repositories\Patrimonial\Licitacao\JulgItemRepository;
use App\Repositories\Patrimonial\Licitacao\JulgItemStatusRepository;
use App\Repositories\Patrimonial\Licitacao\JulgLanceRepository;
use App\Repositories\Patrimonial\Licitacao\JulgParamRepository;
use App\Repositories\Patrimonial\Licitacao\LicilicitemRepository;
use App\Repositories\Patrimonial\Licitacao\PcorcamforneRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class JulgamentoItemService
{
    protected $faseDeLancesService;
    protected $julgItemRepository;
    protected $julgItemHistRepository;
    protected $itemStatusRepository;
    protected $julgLanceRepository;
    protected $julgForneRepository;
    protected $julgForneHistRepository;
    protected $julgForneStatusRepository;
    protected $julgParamRepository;
    protected $pcorcamitemRepository;
    protected $pcorcamforneRepository;
    protected $pcorcamvalRepository;
    protected $pcorcamjulgRepository;
    protected $licilicitemRepository;

    /**
     * Construtor para inje��o de depend�ncias dos reposit�rios necess�rios.
     *
     * @param FaseDeLancesService $faseDeLancesService
     * @param JulgItemRepository $julgItemRepository
     * @param JulgItemHistRepository $julgItemHistRepository
     * @param JulgItemStatusRepository $itemStatusRepository
     * @param JulgLanceRepository $julgLanceRepository
     * @param JulgForneRepository $julgForneRepository
     * @param JulgForneHistRepository $julgForneHistRepository
     * @param JulgForneStatusRepository $julgForneStatusRepository
     * @param JulgParamRepository $julgParamRepository
     * @param PcorcamitemRepository $pcorcamitemRepository
     * @param PcorcamforneRepository $pcorcamforneRepository
     * @param PcorcamvalRepository $pcorcamvalRepository,
     * @param PcorcamjulgRepository $pcorcamjulgRepository,
     * @param LicilicitemRepository $licilicitemRepository
     */
    public function __construct(
        FaseDeLancesService $faseDeLancesService,
        JulgItemRepository $julgItemRepository,
        JulgItemHistRepository $julgItemHistRepository,
        JulgItemStatusRepository $itemStatusRepository,
        JulgLanceRepository $julgLanceRepository,
        JulgForneRepository $julgForneRepository,
        JulgForneHistRepository $julgForneHistRepository,
        JulgForneStatusRepository $julgForneStatusRepository,
        JulgParamRepository $julgParamRepository,
        PcorcamitemRepository $pcorcamitemRepository,
        PcorcamforneRepository $pcorcamforneRepository,
        PcorcamvalRepository $pcorcamvalRepository,
        PcorcamjulgRepository $pcorcamjulgRepository,
        LicilicitemRepository $licilicitemRepository
    ) {
        $this->faseDeLancesService = $faseDeLancesService;
        $this->julgItemRepository = $julgItemRepository;
        $this->julgItemHistRepository = $julgItemHistRepository;
        $this->itemStatusRepository = $itemStatusRepository;
        $this->julgLanceRepository = $julgLanceRepository;
        $this->julgForneRepository = $julgForneRepository;
        $this->julgForneHistRepository = $julgForneHistRepository;
        $this->julgForneStatusRepository = $julgForneStatusRepository;
        $this->julgParamRepository = $julgParamRepository;
        $this->pcorcamitemRepository = $pcorcamitemRepository;
        $this->pcorcamforneRepository = $pcorcamforneRepository;
        $this->pcorcamvalRepository = $pcorcamvalRepository;
        $this->pcorcamjulgRepository = $pcorcamjulgRepository;
        $this->licilicitemRepository = $licilicitemRepository;
    }

    /**
     * Obt�m o pre�o de refer�ncia para uma licita��o espec�fica.
     *
     * Esta fun��o utiliza o reposit�rio de itens de licita��o (`licilicitemRepository`) para recuperar 
     * o pre�o de refer�ncia para a licita��o com o c�digo fornecido. O pre�o de refer�ncia � 
     * normalmente utilizado para compara��es com as propostas apresentadas.
     *
     * @param int $liclicitaCodigo C�digo da licita��o para a qual se deseja obter o pre�o de refer�ncia.
     * @param int $liclicitemCodigo C�digo do item da licita��o para a qual se deseja obter o pre�o de refer�ncia.
     *
     * @return mixed Retorna o pre�o de refer�ncia associado � licita��o, ou `null` caso n�o seja encontrado.
     */
    public function obterPrecoReferencia($liclicitaCodigo, $liclicitemCodigo)
    {
        return $this->licilicitemRepository->getReferencePrice($liclicitaCodigo, $liclicitemCodigo);
    }

    /**
     * Obt�m a lista de fornecedores e propostas para uma licita��o espec�fica.
     * Utiliza cache para otimizar a consulta.
     *
     * @param int $liclicitaCodigo O c�digo da licita��o.
     * @param int $liclicitemCodigo O c�digo do item da licita��o.
     * @param int $valorDeReferencia O valor de refer�ncia utilizado para determinar, com base nas regras de par�metros, quais propostas est�o habilitadas a participar, considerando os limites de toler�ncia para valores excedentes e car�ncia.
     * @param boolean $ignorarCache Indica se o cache deve ser ignorado, geralmente utilizado em rotinas de atualiza��o e persist�ncia de dados no banco.
     * 
     * @return \Illuminate\Support\Collection A cole��o de resultados da consulta.
     */
    public function obterListaDeFornecedoresEProposta($liclicitaCodigo, $liclicitemCodigo, $valorDeReferencia = null, $ignorarCache = false)
    {
        try {

            if ($ignorarCache) {
                return $this->licilicitemRepository->getProposalSupplierList($liclicitaCodigo, $liclicitemCodigo, $valorDeReferencia);
            }

            $cacheKey = "fornecedores_propostas_{$liclicitaCodigo}_{$liclicitemCodigo}";

            return Cache::remember($cacheKey, 5, function () use ($liclicitaCodigo, $liclicitemCodigo, $valorDeReferencia) {
                return $this->licilicitemRepository->getProposalSupplierList($liclicitaCodigo, $liclicitemCodigo, $valorDeReferencia);
            });

        } catch (\Exception $e) {
            throw new JulgamentoException("#JU01 - Exce��o ao obter fornecedores e propostas: " . $e->getMessage() . "");
        }
    }

    /**
     * Obt�m o c�digo do item de julgamento para uma licita��o espec�fica e um item de licita��o.
     *
     * Esta fun��o invoca o reposit�rio respons�vel por obter o c�digo do item de or�amento relacionado
     * � licita��o e ao item de licita��o fornecidos como par�metros.
     *
     * @param int $liclicitaCodigo O c�digo da licita��o.
     * @param int $liclicitemCodigo O c�digo do item da licita��o.
     * 
     * @return mixed O c�digo do item de or�amento correspondente ao item de julgamento.
     */
    public function obterCodigoDoItemDeJulgamento($liclicitaCodigo, $liclicitemCodigo)
    {
        try {

            return $this->licilicitemRepository->getJulgItemCode($liclicitaCodigo, $liclicitemCodigo);

        } catch (\Exception $e) {

            throw new JulgamentoException("#JU02 - Exce��o ao obter c�digo do item de julgamento: " . $e->getMessage());

        }
    }

    /**
     * Obt�m os dados do item selecionado com base no c�digo da licita��o e o c�digo do item da licita��o.
     * 
     * Esta fun��o interage com o reposit�rio `liclicitemRepo` para buscar os dados de um item espec�fico
     * da licita��o a partir dos par�metros fornecidos. Caso ocorra um erro durante o processo, uma exce��o
     * personalizada `JulgamentoException` ser� lan�ada.
     *
     * @param int $liclicitaCodigo C�digo da licita��o.
     * @param int $liclicitemCodigo C�digo do item da licita��o.
     * @return mixed Dados do item selecionado, retornados pelo reposit�rio.
     * 
     * @throws JulgamentoException Se ocorrer um erro ao obter os dados do item selecionado.
     */
    public function obterDadosDoItemSelecionado($liclicitaCodigo, $liclicitemCodigo)
    {
        try {

            return $this->licilicitemRepository->getDataItemSelected($liclicitaCodigo, $liclicitemCodigo);

        } catch (\Exception $e) {

            throw new JulgamentoException("#JU03 - Exce��o ao obter dados do item selecionado: " . $e->getMessage());

        }
    }

    /**
     * Obt�m o c�digo do item de or�amento com base no c�digo da licita��o e o c�digo do item da licita��o.
     * 
     * Esta fun��o consulta o reposit�rio `liclicitemRepo` para recuperar o c�digo do item de or�amento
     * relacionado � licita��o e item fornecidos. Caso ocorra um erro, uma exce��o personalizada
     * `JulgamentoException` ser� lan�ada para tratar a falha.
     *
     * @param int $liclicitaCodigo C�digo da licita��o.
     * @param int $liclicitemCodigo C�digo do item da licita��o.
     * @return mixed C�digo do item de or�amento.
     * 
     * @throws JulgamentoException Se ocorrer um erro ao obter o c�digo do item de or�amento.
     */
    public function obterCodigoDoItemDeOrcamento($liclicitaCodigo, $liclicitemCodigo)
    {
        try {

            return $this->licilicitemRepository->getOrcamItemCode($liclicitaCodigo, $liclicitemCodigo);

        } catch (\Exception $e) {

            throw new JulgamentoException("#JU04 - Exce��o ao obter c�digo do item do orcamento: " . $e->getMessage());

        }
    }

    /**
     * Obt�m o pr�ximo fornecedor v�lido para registrar um lance em uma licita��o.
     * 
     * A fun��o percorre a lista de fornecedores e suas propostas, encontra o �ltimo lance registrado e
     * retorna o c�digo do pr�ximo fornecedor dispon�vel para registrar um lance, considerando o status
     * de cada fornecedor.
     * 
     * Se o c�digo do item de or�amento n�o for informado, a fun��o tenta obter esse c�digo
     * a partir do item de julgamento. Caso contr�rio, um erro � lan�ado se n�o for poss�vel encontrar
     * um fornecedor v�lido.
     *
     * @param int $liclicitaCodigo O c�digo da licita��o.
     * @param int $liclicitemCodigo O c�digo do item da licita��o.
     * @param int|null $pcorcamitemCodigo O c�digo do item de or�amento (opcional).
     * 
     * @return int O c�digo do pr�ximo fornecedor dispon�vel para registrar um lance.
     * 
     * @throws JulgamentoException Se n�o for poss�vel encontrar um fornecedor v�lido.
     */
    public function obterProximoFornecedorParaLance($liclicitaCodigo, $liclicitemCodigo, $julgItemCodigo = null, $valorDeReferencia = null, $ignorarCache = false)
    {
        try {

            $pcorcamItemCodigo = null;

            $listaDeFornecedoresEProposta = $this->obterListaDeFornecedoresEProposta($liclicitaCodigo, $liclicitemCodigo, $valorDeReferencia, $ignorarCache);

            if(empty($julgItemCodigo)) {

                $julgItemObject = $this->obterCodigoDoItemDeJulgamento($liclicitaCodigo, $liclicitemCodigo);

                if (!empty($julgItemObject)) {
                    $julgItemCodigo = $julgItemObject->l30_codigo;
                    $pcorcamItemCodigo = $julgItemObject->pc22_orcamitem;
                }

            }

            if(empty($julgItemCodigo)) {

                return $listaDeFornecedoresEProposta[0]->pc21_orcamforne;

            } else {

                $ultimoLance = $this->julgLanceRepository->findLastBid($julgItemCodigo);

                if (empty($ultimoLance)) {

                    return $listaDeFornecedoresEProposta[0]->pc21_orcamforne;
                    
                } else {

                    $listaDeFornecedores = collect($listaDeFornecedoresEProposta)->map(function ($item) {
                        return $item->pc21_orcamforne;
                    })->toArray();
    
                    $ultimoFornecedor = $this->julgForneRepository->findId($ultimoLance->l32_julgforne);
                    $indiceAtual = array_search($ultimoFornecedor->l34_orcamforne, $listaDeFornecedores);
            
                    $totalFornecedores = count($listaDeFornecedores);
                    $totalFornecedoresInaptos = 0;
                    $tentativas = 0;
            
                    do {
    
                        $indiceAtual = ($indiceAtual + 1) % $totalFornecedores;
                        $proximoFornecedorCode = $listaDeFornecedores[$indiceAtual];

                        $proximoFornecedor = $this->pcorcamforneRepository->findId($proximoFornecedorCode);

                        if (empty($pcorcamItemCodigo)) {
                            $pcorcamItemObject = $this->obterCodigoDoItemDeJulgamento($liclicitaCodigo, $liclicitemCodigo);

                            if (!empty($pcorcamItemObject)) {
                                $pcorcamItemCodigo = $pcorcamItemObject->pc22_orcamitem;
                            }
                        }

                        $proximoJulgForne = $this->julgForneRepository->findItemSupplier($proximoFornecedorCode, $pcorcamItemCodigo);

                        if (!empty($proximoFornecedor)) {
                            if (!empty($proximoJulgForne)) {
                                if ($proximoJulgForne->l34_julgfornestatus == 1) {
                                    return $proximoJulgForne->l34_orcamforne;
                                } else {
                                    $totalFornecedoresInaptos++;
                                }
                            } else {
                                return $proximoFornecedor->pc21_orcamforne;
                            }
                        }
                        
                        $tentativas++;
            
                    } while ($tentativas < $totalFornecedores);
            
                    if ($totalFornecedoresInaptos !== $totalFornecedores) {
                        throw new JulgamentoException("#JU05 - Exce��o: Nenhum fornecedor v�lido encontrado para o item de or�amento {$julgItemCodigo}.");
                    }

                }

            }

        } catch (\Exception $e) {

            throw new JulgamentoException("#JU06 - Exce��o ao encontrar o pr�ximo fornecedor para lance: " . $e->getMessage());

        }
    }

    /**
     * Obt�m o �ltimo lance registrado para um fornecedor e um item de or�amento espec�ficos.
     *
     * Esta fun��o busca o lance mais recente de um fornecedor para um item de or�amento fornecido,
     * verificando se tanto o fornecedor quanto o item de or�amento existem antes de realizar a consulta.
     * 
     * Se o fornecedor ou o item de or�amento n�o forem encontrados, a fun��o retorna `false`.
     *
     * @param int $pcorcamforneCodigo O c�digo do fornecedor.
     * @param int $pcorcamitemCodigo O c�digo do item de or�amento.
     * 
     * @return mixed O �ltimo lance registrado ou `false` caso o fornecedor ou o item de or�amento n�o sejam encontrados.
     */
    public function obterLance($pcorcamforneCodigo, $pcorcamitemCodigo)
    {
        try {

            $listaJulgForne = $this->julgForneRepository->findItemSupplier($pcorcamforneCodigo, $pcorcamitemCodigo);
            $julgItem = $this->julgItemRepository->findItemBudget($pcorcamitemCodigo);

            if (!empty($julgItem) && !empty($listaJulgForne)) {

                return $this->julgLanceRepository->findLastBidSupplierNotNull($listaJulgForne->l34_codigo, $julgItem->l30_codigo);

            } else {

                return false;

            }

        } catch (\Exception $e) {

            throw new JulgamentoException("#JU07 - Exce��o ao obter lance: " . $e->getMessage());

        }
    }

    /**
     * Obt�m uma lista de fornecedores que possuem o modelo de microempresa para um item espec�fico em uma licita��o.
     *
     * Esta fun��o utiliza o reposit�rio `licilicitemRepository` para buscar os fornecedores associados a um item 
     * de licita��o e filtra os que possuem o modelo de microempresa. Em caso de falha, lan�a uma exce��o 
     * personalizada para lidar com erros no processo de obten��o de dados.
     *
     * @param mixed $liclicita Identificador da licita��o para o qual os fornecedores ser�o buscados.
     * @param mixed $liclicitem Identificador do item de licita��o relacionado.
     *
     * @return mixed Retorna a lista de fornecedores com modelo de microempresa.
     *
     * @throws JulgamentoException Caso ocorra um erro durante o processo, lan�a uma exce��o contendo detalhes do erro.
     */
    public function obterFornecedoresComModeloDeMicroempresa($liclicita, $liclicitem, $valorDeReferencia)
    {
        try {
            
            return $this->licilicitemRepository->getSuppliersWithAMicroEnterprise($liclicita, $liclicitem, $valorDeReferencia);

        } catch (\Exception $e) {

            throw new JulgamentoException(
                sprintf(
                    "#JU08 - Exce��o ao obter fornecedores com modelo de microempresa. Licita��o: %s, Item: %s. Detalhes do erro: %s - #JU1",
                    $liclicita,
                    $liclicitem,
                    $e->getMessage()
                )
            );

        }
    }

    /**
     * Registra um lance para um fornecedor e um item de or�amento espec�ficos.
     *
     * Esta fun��o verifica se o item de or�amento e o fornecedor existem. Se algum deles n�o existir, 
     * ele cria um novo registro para o item de or�amento e/ou fornecedor com o status correspondente.
     * Depois, registra o lance no reposit�rio de lances.
     * 
     * Caso haja algum erro ao registrar o lance, uma exce��o � lan�ada.
     *
     * @param int $pcorcamforneCodigo O c�digo do fornecedor.
     * @param int $pcorcamitemCodigo O c�digo do item de or�amento.
     * @param float $valorLance O valor do lance a ser registrado.
     * @param int $liclicitaCodigo O c�digo da licita��o.
     * @param int $liclicitemCodigo O c�digo dos itens licita��o.
     * 
     * @return mixed O objeto do lance registrado.
     * 
     * @throws JulgamentoException Se ocorrer um erro ao registrar o lance.
     */
    public function registrarLance($liclicitaCodigo, $liclicitemCodigo, $pcorcamforneCodigo, $valorLance, $pcorcamitemCodigo)
    {
        DB::beginTransaction();

        try {
            
            $julgItem = $this->julgItemRepository->findItemBudget($pcorcamitemCodigo);

            if (empty($julgItem)) {
                $julgItem = $this->registrarJulgItem($pcorcamitemCodigo, 'Em aberto');
            }

            $julgForne = $this->julgForneRepository->findItemSupplier($pcorcamforneCodigo, $pcorcamitemCodigo);

            if (empty($julgForne)) {
                $julgForne = $this->registrarJulgForne($pcorcamforneCodigo, $pcorcamitemCodigo, 'Normal');
            }

            $this->validaValorDoLance($julgItem->l30_codigo, $valorLance, $liclicitaCodigo, $liclicitemCodigo);

            $julgLance = $this->julgLanceRepository->create([
                'l32_julgitem' => $julgItem->l30_codigo,
                'l32_julgforne' => $julgForne->l34_codigo,
                'l32_lance' => $valorLance
            ]);

            if (!$julgLance) {
                throw new JulgamentoException("#JU09 - Exce��o ao registrar lance. Tente novamente.");
            }

            if ($valorLance === null) {
                $julgForneStatus = $this->julgForneStatusRepository->findLabel(mb_convert_encoding('Sem lance', 'UTF-8', 'ISO-8859-1'));
                $this->faseDeLancesService->alterarStatusFornecedor($julgForne->l34_codigo, $julgForneStatus->l35_codigo, mb_convert_encoding('O fornecedor foi desclassificado das pr�ximas fases do preg�o por n�o ter conseguido igualar ou superar o valor estipulado na sequ�ncia de lances.', 'UTF-8', 'ISO-8859-1'));
            }

            DB::commit();
            return $julgLance;

        } catch (\Exception $e) {
            
            DB::rollBack();
            throw new JulgamentoException("#JU10 - Exce��o ao registrar lance: " . $e->getMessage());

        }
    }

    /**
     * Reverte o �ltimo lance registrado para um item de or�amento espec�fico.
     *
     * Esta fun��o busca o �ltimo lance registrado para o item de or�amento fornecido. 
     * Se o lance existir, ele � deletado do reposit�rio de lances. 
     * Caso contr�rio, nenhuma a��o � tomada.
     * 
     * @param int $pcorcamitemCodigo O c�digo do item de or�amento.
     * 
     * @return void
     */
    public function reverterLance($pcorcamitemCodigo)
    {
        DB::beginTransaction();

        try {

            $julgItem = $this->julgItemRepository->findItemBudget($pcorcamitemCodigo);

            if (empty($julgItem)) {
                throw new JulgamentoException("#JU11 - Exce��o: Item de or�amento n�o encontrado.");
            }

            $ultimoJulgLance = $this->julgLanceRepository->findLastBid($julgItem->l30_codigo);

            if (!empty($ultimoJulgLance)) {
                $this->julgLanceRepository->delete('l32_codigo', $ultimoJulgLance->l32_codigo);
            } else {
                throw new JulgamentoException("#JU12 - Exce��o: Nenhum lance encontrado para o item de or�amento informado.");
            }

            if ($ultimoJulgLance->l32_lance === null) {
                $julgForneStatus = $this->julgForneStatusRepository->findLabel(mb_convert_encoding('Normal', 'UTF-8', 'ISO-8859-1'));
                $this->faseDeLancesService->alterarStatusFornecedor($ultimoJulgLance->l32_julgforne, $julgForneStatus->l35_codigo, mb_convert_encoding('Revertendo o lance que desclassificou o fornecedor das pr�ximas fases do preg�o por n�o ter conseguido igualar ou superar o valor estipulado na sequ�ncia de lances.', 'UTF-8', 'ISO-8859-1'));
            }

            DB::commit();

        } catch (\Exception $e) {
            
            DB::rollBack();
            throw new JulgamentoException("#JU13 - Exce��o ao reverter o lance: " . $e->getMessage());

        }
    }

    /**
     * Limpa todos os lances registrados para um item de or�amento espec�fico.
     *
     * Esta fun��o remove todos os lances associados ao item de or�amento fornecido.
     * Primeiro, busca o item de or�amento com base no c�digo fornecido e, em seguida,
     * exclui todos os lances registrados para esse item.
     *
     * @param int $pcorcamitemCodigo O c�digo do item de or�amento.
     * 
     * @return void
     */
    public function limparLances($pcorcamitemCodigo)
    {
        $codigosItensLista = explode(",", $pcorcamitemCodigo);

        DB::beginTransaction();

        try {

            foreach ($codigosItensLista as $i => $value) {

                $julgItemRepository = $this->julgItemRepository->findItemBudget($value);
    
                if (empty($julgItemRepository)) {
                    throw new JulgamentoException("#JU14 - Exce��o: Item de or�amento n�o encontrado.");
                }
    
                $lances = $this->julgLanceRepository->find('l32_julgitem', $value);

                if (!empty($lances)) {
                    $resultado = $this->julgLanceRepository->deleteBidItem($julgItemRepository->l30_codigo);
        
                    if ($resultado === false) {
                        throw new JulgamentoException("#JU15 - Exce��o ao limpar os lances do item de or�amento.");
                    }
                }
                
                $pcorcamvalItens = $this->pcorcamvalRepository->find('pc23_orcamitem', $value);

                if (!empty($pcorcamvalItens)) {
                    $pcorcamval = $this->pcorcamvalRepository->delete('pc23_orcamitem', $value);

                    if ($pcorcamval === false) {
                        throw new JulgamentoException("#JU16 - Exce��o ao limpar os lances do item de or�amento.");
                    }
                }

                $pcorcamjulgItens = $this->pcorcamjulgRepository->find('pc24_orcamitem', $value);

                if (!empty($pcorcamjulgItens)) {
                    $pcorcamjulg = $this->pcorcamjulgRepository->deletePcorcamitemRecords($value);

                    if ($pcorcamjulg === false) {
                        throw new JulgamentoException("#JU17 - Exce��o ao limpar os lances do item de or�amento.");
                    }
                }

                $julgForne = $this->julgForneRepository->find('l34_orcamitem', $value);

                $codigoArray = [];
                foreach ($julgForne as $item) {
                    $codigoArray[] = $item->l34_codigo;
                }

                $julgForneHist = $this->julgForneHistRepository->deleteIn('l36_julgforne', $codigoArray);
                
                if ($julgForneHist === false) {
                    throw new JulgamentoException("#JU121 - Exce��o ao limpar os historicos dos fornecedores do julgamento do item.");
                }

                if ($julgForne->isNotEmpty()) {

                    $julgForne = $this->julgForneRepository->delete('l34_orcamitem', $value);
                    
                    if (empty($julgForne)) {
                        throw new JulgamentoException("#JU18 - Exce��o ao limpar os fornecedores do julgamento do item.");
                    }

                }

            }

            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            throw new JulgamentoException("#JU19 - Exce��o ao limpar lances: " . $e->getMessage());

        }
    }

    /**
     * Altera o status de um item de or�amento e registra o motivo da altera��o.
     *
     * Esta fun��o verifica se o item de or�amento existe. Caso o item n�o exista, ele ser� criado
     * com o status "Em aberto". Se o item j� existir, o status � alterado para o novo status fornecido.
     * Ap�s a altera��o, o motivo da altera��o � registrado no hist�rico de status do item.
     *
     * @param int $codigosItens O c�digos dos itens.
     * @param int $novoStatus O c�digo do novo status a ser atribu�do ao item de or�amento.
     * @param string $motivo O motivo para a altera��o do status do item de or�amento.
     * 
     * @return void
     */
    public function alterarStatusItem($codigosItens, $novoStatus, $motivo)
    {
        // Pega os ids dos itens do or�amento e os transforma em um array
        $codigosItensLista = explode(",", $codigosItens);
    
        // Inicia uma transa��o no banco de dados
        DB::beginTransaction();
    
        try {

            foreach ($codigosItensLista as $i => $codigoItem) {

                // Busca a exist�ncia do item no or�amento pelo c�digo
                $itemOrcamento = $this->pcorcamitemRepository->find($codigoItem);

                if (empty($itemOrcamento)) {
                    throw new JulgamentoException("#JU20 - Exce��o: Item de or�amento com c�digo {$codigoItem} n�o encontrado.");
                }
    
                // Busca o item de julgamento associado ao or�amento
                $julgItem = $this->julgItemRepository->findItemBudget($itemOrcamento->pc22_orcamitem);
    
                if (!empty($julgItem) && $julgItem->l30_julgitemstatus == 2 && $novoStatus !== 2) {

                    $pcorcamvalItens = $this->pcorcamvalRepository->find('pc23_orcamitem', $codigoItem);

                    if (!empty($pcorcamvalItens)) {
                        $pcorcamval = $this->pcorcamvalRepository->delete('pc23_orcamitem', $codigoItem);
    
                        if ($pcorcamval === false) {
                            throw new JulgamentoException(
                                sprintf(
                                    "#JU21 - Exce��o ao tentar excluir os registros de pcorcamval. C�digo do item: %s. Verifique os logs e o estado do banco de dados.",
                                    $codigoItem
                                )
                            );
                        }
                    }
    
                    $pcorcamjulgItens = $this->pcorcamjulgRepository->find('pc24_orcamitem', $codigoItem);
    
                    if (!empty($pcorcamjulgItens)) {
                        $pcorcamjulg = $this->pcorcamjulgRepository->deletePcorcamitemRecords($codigoItem);
    
                        if ($pcorcamjulg === false) {
                            throw new JulgamentoException(
                                sprintf(
                                    "#JU22 - Exce��o ao tentar excluir registros de pcorcamitem para o c�digo do item: %s. A opera��o de exclus�o retornou false. Verifique os dados no banco de dados e os logs para mais detalhes.",
                                    $codigoItem
                                )
                            );
                        }
                    }
                    
                    $updateSuppliers = $this->julgForneRepository->updateStatusSuppliersFromBestBidToNoBid($codigoItem, ['l34_julgfornestatus' => 3]);

                    if (empty($updateSuppliers)) {
                        throw new JulgamentoException(
                            sprintf(
                                "#JU23 - Exce��o ao atualizar o status dos fornecedores para o c�digo do item: %s. Nenhum registro foi alterado. Verifique o estado dos dados no banco de dados e os logs da aplica��o para mais detalhes.",
                                $codigoItem
                            )
                        );
                    }

                }

                // Caso o item de julgamento n�o exista, cria um novo com o status "Em Aberto"
                if (empty($julgItem)) {

                    // Busca o status padr�o "Em Aberto"
                    $statusJulgItem = $this->itemStatusRepository->findLabel(mb_convert_encoding('Em aberto', 'UTF-8', 'ISO-8859-1'));
    
                    if (empty($statusJulgItem)) {
                        throw new JulgamentoException("#JU24 - Exce��o: Status Em aberto n�o encontrado.");
                    }
    
                    // Cria o item de julgamento no banco de dados
                    $julgItem = $this->julgItemRepository->create([
                        'l30_orcamitem' => $itemOrcamento->pc22_orcamitem,
                        'l30_julgitemstatus' => $statusJulgItem->l31_codigo,
                    ]);
    
                    if (!$julgItem) {
                        throw new JulgamentoException("#JU25 - Exce��o ao criar o item de or�amento.");
                    }
    
                    // Busca o novo status para atualiza��o
                    $novoStatusJulgItem = $this->itemStatusRepository->findId($novoStatus);
    
                    if (empty($novoStatusJulgItem)) {
                        throw new JulgamentoException("#JU26 - Exce��o: Novo status informado � inv�lido.");
                    }
    
                    // Atualiza o status do item de julgamento
                    $resultado = $this->julgItemRepository->update($julgItem->l30_codigo, [
                        'l30_julgitemstatus' => $novoStatusJulgItem->l31_codigo,
                    ]);
    
                    if (!$resultado) {
                        throw new JulgamentoException("#JU27 - Exce��o ao atualizar o status do item de or�amento.");
                    }

                } else {

                    // Caso o item de julgamento j� exista, busca o novo status para atualiza��o
                    $novoStatusJulgItem = $this->itemStatusRepository->findId($novoStatus);
    
                    if (empty($novoStatusJulgItem)) {
                        throw new JulgamentoException("#JU28 - Exce��o: Novo status informado � inv�lido.");
                    }
    
                    // Atualiza o status do item de julgamento existente
                    $resultado = $this->julgItemRepository->update($julgItem->l30_codigo, [
                        'l30_julgitemstatus' => $novoStatusJulgItem->l31_codigo,
                    ]);
    
                    if (!$resultado) {
                        throw new JulgamentoException("#JU29 - Exce��o ao atualizar o status do item de or�amento.");
                    }

                }

                // Cria um registro no hist�rico para o item atualizado
                $historico = $this->julgItemHistRepository->create([
                    'l33_julgitem' => $julgItem->l30_codigo,
                    'l33_julgitemstatus' => $julgItem->l30_julgitemstatus,
                    'l33_motivo' => $motivo,
                ]);
    
                if (!$historico) {
                    throw new JulgamentoException("#JU30 - Exce��o ao registrar o hist�rico do item.");
                }
            }
    
            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            throw new JulgamentoException("#JU31 - Exce��o ao alterar status do item: " . $e->getMessage());

        }
    }     

    public function atualizaStatusFornecedor($pcorcamitemCodigo, $pcorcamforneCodigo, $statusFonecedor, $motivoFornecedor)
    {
        try {

            $julgForne = $this->julgForneRepository->findItemSupplier($pcorcamforneCodigo, $pcorcamitemCodigo);

            if (empty($julgForne)) {
                $julgForne = $this->registrarJulgForne($pcorcamforneCodigo, $pcorcamitemCodigo, 'Normal');
            }

            $this->faseDeLancesService->alterarStatusFornecedor($julgForne->l34_codigo, $statusFonecedor, $motivoFornecedor);

        } catch (\Exception $e) {

            throw new JulgamentoException("#JU32 - Exce��o ao alterar status do fornecedor: " . $e->getMessage());

        }
    }

    /**
     * Finaliza o processo de julgamento de um item de or�amento em uma licita��o.
     *
     * Este m�todo realiza as seguintes opera��es:
     * - Obt�m fornecedores vinculados ao item de or�amento.
     * - Recupera e organiza os detalhes dos lances, ordenando-os pelo menor valor.
     * - Gera a pontua��o e armazena os dados processados nos reposit�rios correspondentes.
     * - Atualiza o status dos fornecedores e do item de or�amento para "Melhor Proposta" e "Julgado", respectivamente.
     * - Remove registros antigos de lances e julgamentos, substituindo-os pelos novos dados processados.
     * - Garante a integridade do processo com transa��es de banco de dados.
     *
     * @param mixed $liclicitaCodigo C�digo da licita��o.
     * @param mixed $liclicitemCodigo C�digo do item da licita��o.
     * @param mixed $pcorcamitemCodigo C�digo do item de or�amento.
     * 
     * @return bool Retorna `true` se o processo for conclu�do com sucesso.
     * 
     * @throws JulgamentoException Em caso de falhas em qualquer etapa do processo.
     */
    public function finalizar($liclicitaCodigo, $liclicitemCodigo, $pcorcamitemCodigo)
    {
        DB::beginTransaction();
    
        try {
            
            $julgFornecedores = $this->julgForneRepository->findItemSuppliers($pcorcamitemCodigo);

            if (empty($julgFornecedores)) {
                throw new JulgamentoException(
                    sprintf(
                        "#JU36 - Exce��o ao buscar fornecedores para o item com c�digo: %s. Nenhum fornecedor encontrado.",
                        $pcorcamitemCodigo
                    )
                );
            }

            $pcorcamvalData = [];

            foreach ($julgFornecedores as $i => $julgFornecedor) {
                $detalhesDaMenorOfertaDoFornecedor = $this->licilicitemRepository->getLowestBidDetailsForSupplier($liclicitaCodigo, $liclicitemCodigo, $julgFornecedor->l34_orcamforne, $pcorcamitemCodigo);

                if (empty($detalhesDaMenorOfertaDoFornecedor)) {
                    throw new JulgamentoException(
                        sprintf(
                            "#JU37 - Exce��o ao buscar os detalhes da menor oferta para o fornecedor. Licita��o: %s, Item: %s, Fornecedor: %s, C�digo do item: %s. Nenhum detalhe encontrado.",
                            $liclicitaCodigo,
                            $liclicitemCodigo,
                            $julgFornecedor->l34_orcamforne,
                            $pcorcamitemCodigo
                        )
                    );
                }

                if ($detalhesDaMenorOfertaDoFornecedor->l32_lance === null) {
                    $proposalSupplier = $this->licilicitemRepository->getProposalSupplier($liclicitaCodigo, $liclicitemCodigo, $julgFornecedor->l34_orcamforne);
                    
                    if (empty($proposalSupplier)) {
                        throw new JulgamentoException(
                            sprintf(
                                "#JU58 - Exce��o ao buscar os detalhes da menor oferta para o fornecedor. Licita��o: %s, Item: %s, Fornecedor: %s.",
                                $liclicitaCodigo,
                                $liclicitemCodigo,
                                $julgFornecedor->l34_orcamforne,
                            )
                        );
                    }

                    $detalhesDaMenorOfertaDoFornecedor->l32_lance = $proposalSupplier->l224_vlrun;
                }

                $pcorcamvalData[] = [
                    'pc23_orcamitem' => $detalhesDaMenorOfertaDoFornecedor->pc22_orcamitem,
                    'pc23_orcamforne' => $detalhesDaMenorOfertaDoFornecedor->pc21_orcamforne,
                    'pc23_valor' =>  round((floatval($detalhesDaMenorOfertaDoFornecedor->l32_lance) * intval($detalhesDaMenorOfertaDoFornecedor->l224_quant)), 2),
                    'pc23_quant' => $detalhesDaMenorOfertaDoFornecedor->l224_quant,
                    'pc23_vlrun' => $detalhesDaMenorOfertaDoFornecedor->l32_lance
                ];
            }

            usort($pcorcamvalData, function ($a, $b) {
                $valorA = is_numeric($a['pc23_valor']) ? (float) $a['pc23_valor'] : 0;
                $valorB = is_numeric($b['pc23_valor']) ? (float) $b['pc23_valor'] : 0;
            
                return $valorA <=> $valorB;
            });

            $pcorcamjulgData = [];

            foreach ($pcorcamvalData as $i => $pcorcamval) {
                $pcorcamjulgData[] = [
                    'pc24_orcamitem' => $pcorcamval['pc23_orcamitem'],
                    'pc24_pontuacao' => ($i + 1),
                    'pc24_orcamforne' => $pcorcamval['pc23_orcamforne']
                ];
            }

            $pcorcamvalItens = $this->pcorcamvalRepository->find('pc23_orcamitem', $pcorcamitemCodigo);

            if (!empty($pcorcamvalItens)) {
                $pcorcamval = $this->pcorcamvalRepository->delete('pc23_orcamitem', $pcorcamitemCodigo);

                if ($pcorcamval === false) {
                    throw new JulgamentoException("#JU38 - Exce��o ao limpar os registros da pcorcamval.");
                }
            }

            $pcorcamvalInsert = $this->pcorcamvalRepository->insertData($pcorcamvalData);

            if (empty($pcorcamvalInsert)) {
                throw new JulgamentoException("#JU39 - Exce��o ao inserir os registros na pcorcamval.");
            }

            foreach ($julgFornecedores as $k => $julgFornecedor) {
                $statusFornecedor = $this->julgForneStatusRepository->findLabel(mb_convert_encoding('Melhor Proposta', 'UTF-8', 'ISO-8859-1'));
                $this->faseDeLancesService->alterarStatusFornecedor($julgFornecedor->l34_codigo, $statusFornecedor->l35_codigo, mb_convert_encoding('O status dos fornecedores foram atualizados para melhor lance ap�s a conclus�o da rotina de julgamento.', 'UTF-8', 'ISO-8859-1'));
            }

            $statusItem = $this->itemStatusRepository->findLabel(mb_convert_encoding('Julgado', 'UTF-8', 'ISO-8859-1'));
            $statusItemUpdate = $this->alterarStatusItem($pcorcamitemCodigo, $statusItem->l31_codigo, mb_convert_encoding('O status do item foi atualizado para julgado ap�s a conclus�o da rotina de julgamento.', 'UTF-8', 'ISO-8859-1'));

            if (empty($statusItemUpdate)) {
                throw new JulgamentoException("#JU40 - Exce��o ao atualizar o status do item ap�s a conclus�o da rotina de julgamento.");
            }

            $pcorcamjulgItens = $this->pcorcamjulgRepository->find('pc24_orcamitem', $pcorcamitemCodigo);

            if (!empty($pcorcamjulgItens)) {
                $pcorcamjulg = $this->pcorcamjulgRepository->deletePcorcamitemRecords($pcorcamitemCodigo);

                if ($pcorcamjulg === false) {
                    throw new JulgamentoException("#JU41 - Exce��o ao limpar os lances do item de or�amento.");
                }
            }

            $pcorcamjulgInsert = $this->pcorcamjulgRepository->insert($pcorcamjulgData);

            if (empty($pcorcamjulgInsert)) {
                throw new JulgamentoException("#JU42 - Exce��o ao registrar os melhores lances na ordem dos fornecedores vencedores em pcorcamjulg ap�s a conclus�o da rotina de julgamento.");
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            throw new JulgamentoException("#JU43 - Exce��o na rotina de finaliza��o de julgamento: " . $e->getMessage());

        }
    }

    /**
     * Libera fornecedores com modelo de microempresa para um item de licita��o.
     *
     * - Obt�m fornecedores utilizando `obterFornecedoresComModeloDeMicroempresa`.
     * - Valida se h� fornecedores e se o status "Normal" existe no reposit�rio.
     * - Atualiza o status de cada fornecedor encontrado para "Normal".
     *
     * @param mixed $liclicita ID da licita��o.
     * @param mixed $liclicitem ID do item de licita��o.
     * 
     * @throws JulgamentoException Em caso de erro durante o processo.
     */
    public function liberarMicroEmpresas($liclicita, $liclicitem, $valorDeReferencia=null)
    {
        try {

            $fornecedoresComModeloDeMicroempresa = $this->obterFornecedoresComModeloDeMicroempresa($liclicita, $liclicitem, $valorDeReferencia);
            
            if (empty($fornecedoresComModeloDeMicroempresa)) {
                throw new JulgamentoException(
                    sprintf(
                        "#JU44 - Exce��o ao obter fornecedores com modelo de microempresa. Licita��o: %s, Item: %s. Nenhum fornecedor encontrado.",
                        $liclicita,
                        $liclicitem
                    )
                );
            }

            $statusFornecedor = $this->julgForneStatusRepository->findLabel(mb_convert_encoding('Normal', 'UTF-8', 'ISO-8859-1'));

            if (empty($statusFornecedor)) {
                throw new JulgamentoException(
                    "#JU45 - Exce��o ao buscar o status do fornecedor com o valor 'Normal'. Nenhum status encontrado. Verifique os dados no banco de dados.",
                );
            }

            foreach ($fornecedoresComModeloDeMicroempresa as $i => $fornecedorMicroempresa) {

                $julgForne = $this->julgForneRepository->findItemSupplier($fornecedorMicroempresa->pc21_orcamforne, $fornecedorMicroempresa->pc22_orcamitem);

                if (empty($julgForne)) {
                    throw new JulgamentoException(
                        sprintf(
                            "#JU46 - Exce��o ao buscar fornecedor para o item com c�digo de fornecedor: %s e c�digo de item: %s. Nenhum fornecedor encontrado.",
                            $fornecedorMicroempresa->pc21_orcamforne,
                            $fornecedorMicroempresa->pc22_orcamitem
                        )
                    );
                }

                $this->faseDeLancesService->alterarStatusFornecedor($julgForne->l34_codigo, $statusFornecedor->l35_codigo, mb_convert_encoding('A altera��o de status foi efetuada em raz�o da libera��o de novos lances, conforme estabelecido pela Lei 123/2006.', 'UTF-8', 'ISO-8859-1'));
            }

        } catch (\Exception $e) {

            throw new JulgamentoException(
                sprintf(
                    "#JU47 - Exce��o ao liberar microempresas para a licita��o %s e item %s. Detalhes do erro: %s",
                    $liclicita,
                    $liclicitem,
                    $e->getMessage()
                )
            );
            
        }
    }

    /**
     * Registra o julgamento de um item de or�amento.
     *
     * Esta fun��o cria um registro no reposit�rio de itens com os detalhes do julgamento,
     * incluindo o c�digo do item e o status associado.
     *
     * @param int $pcorcamitemCodigo C�digo do item no or�amento.
     * @param string $statusLabel R�tulo do status do item.
     *
     * @return mixed Retorna o registro criado do item de or�amento.
     *
     * @throws JulgamentoException Se o status do item n�o for encontrado ou ocorrer um erro na cria��o.
     */
    private function registrarJulgItem($pcorcamitemCodigo, $statusLabel)
    {
        $statusItem = $this->itemStatusRepository->findLabel(mb_convert_encoding($statusLabel, 'UTF-8', 'ISO-8859-1'));

        if (empty($statusItem)) {
            throw new JulgamentoException("#JU52 - Exce��o: Status do item de or�amento n�o encontrado.");
        }

        $julgItem = $this->julgItemRepository->create([
            'l30_orcamitem' => $pcorcamitemCodigo,
            'l30_julgitemstatus' => $statusItem->l31_codigo,
        ]);

        if (!$julgItem) {

            throw new JulgamentoException("#JU53 - Exce��o ao criar o item de or�amento.");

        } else {

            return $julgItem;

        }
    }

    /**
     * Registra o julgamento de um fornecedor para um item de or�amento.
     *
     * Esta fun��o cria um registro no reposit�rio de fornecedores com os detalhes do 
     * julgamento, incluindo o c�digo do fornecedor, o c�digo do item e o status associado.
     *
     * @param int $pcorcamforneCodigo C�digo do fornecedor no or�amento.
     * @param int $pcorcamitemCodigo C�digo do item no or�amento.
     * @param string $statusLabel (Opcional) R�tulo do status do fornecedor, padr�o � "Normal".
     *
     * @return mixed Retorna o registro criado do fornecedor.
     *
     * @throws JulgamentoException Se o status do fornecedor n�o for encontrado ou ocorrer um erro na cria��o.
     */
    private function registrarJulgForne($pcorcamforneCodigo, $pcorcamitemCodigo, $statusLabel="Normal")
    {
        $statusFornecedor = $this->julgForneStatusRepository->findLabel(mb_convert_encoding($statusLabel, 'UTF-8', 'ISO-8859-1'));

        if (empty($statusFornecedor)) {
            throw new JulgamentoException("#JU54 - Exce��o: Status do fornecedor n�o encontrado.");
        }

        $julgForne = $this->julgForneRepository->create([
            'l34_orcamforne' => $pcorcamforneCodigo,
            'l34_orcamitem' => $pcorcamitemCodigo,
            'l34_julgfornestatus' => $statusFornecedor->l35_codigo,
        ]);

        if (!$julgForne) {

            throw new JulgamentoException("#JU55 - Exce��o ao criar o fornecedor.");

        } else {

            return $julgForne;

        }
    }

    /**
     * Valida o valor de um lance fornecido para um item em uma licita��o.
     *
     * Esta fun��o verifica se o valor do lance � v�lido, seguindo as regras:
     * - O valor deve ser menor que a menor proposta inicial ou lance anterior, 
     *   considerando a diferen�a m�nima permitida (`l13_difminlance`).
     *
     * @param int $julgItemCodigo C�digo do item em julgamento.
     * @param float $valorLance Valor do lance fornecido.
     * @param int $liclicitaCodigo C�digo da licita��o.
     * @param int $liclicitemCodigo C�digo do item na licita��o.
     *
     * @throws JulgamentoException Se o valor do lance n�o for v�lido de acordo com as regras.
     */
    private function validaValorDoLance($julgItemCodigo, $valorLance, $liclicitaCodigo, $liclicitemCodigo)
    {
        $param = $this->faseDeLancesService->obterParametros();

        $ultimoLance = $this->julgLanceRepository->findLastBidNotNull($julgItemCodigo);

        if (empty($ultimoLance)) {

            $ultimaProposta = $this->licilicitemRepository->getTheLowestBid($liclicitaCodigo, $liclicitemCodigo, $param->l13_clapercent);

            if($valorLance > (floatval($ultimaProposta->l224_vlrun) - floatval($param->l13_difminlance))) {
                throw new JulgamentoException("#JU56 - Exce��o: O fornecedor deve apresentar um lance inferior ao �ltimo registrado, respeitando o valor m�nimo estipulado para redu��o entre lances.");
            }

        } else {
            
            if ($valorLance !== null && $valorLance > ($ultimoLance->l32_lance - $param->l13_difminlance)) {
                throw new JulgamentoException("#JU57 - Exce��o: O fornecedor n�o pode enviar um lance maior que o menor lance.");
            }

        }
    }
}
