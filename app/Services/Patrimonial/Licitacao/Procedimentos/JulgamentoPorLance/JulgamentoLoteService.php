<?php

namespace App\Services\Patrimonial\Licitacao\Procedimentos\JulgamentoPorLance;

use App\Exceptions\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\JulgamentoException;
use App\Models\Patrimonial\Licitacao\Liclicitemlote;
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
use App\Repositories\Patrimonial\Licitacao\LiclicitemLoteRepository;
use App\Repositories\Patrimonial\Licitacao\PcorcamforneRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class JulgamentoLoteService
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
    protected $liclicitemLoteRepository;

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
     * @param LiclicitemLoteRepository $liclicitemLoteRepository
     */
    public function __construct() {
        $this->faseDeLancesService = new FaseDeLancesService;
        $this->julgItemRepository = new JulgItemRepository;
        $this->julgItemHistRepository = new JulgItemHistRepository;
        $this->itemStatusRepository = new JulgItemStatusRepository;
        $this->julgLanceRepository = new JulgLanceRepository;
        $this->julgForneRepository = new JulgForneRepository;
        $this->julgForneHistRepository = new JulgForneHistRepository;
        $this->julgForneStatusRepository = new JulgForneStatusRepository;
        $this->julgParamRepository = new JulgParamRepository;
        $this->pcorcamitemRepository = new PcorcamitemRepository;
        $this->pcorcamforneRepository = new PcorcamforneRepository;
        $this->pcorcamvalRepository = new PcorcamvalRepository;
        $this->pcorcamjulgRepository = new PcorcamjulgRepository;
        $this->licilicitemRepository = new LicilicitemRepository;
        $this->liclicitemLoteRepository = new LiclicitemLoteRepository;
    }

    /**
     * Obt�m o pre�o de refer�ncia para uma licita��o espec�fica.
     *
     * Esta fun��o utiliza o reposit�rio de itens de licita��o (`licilicitemRepository`) para recuperar 
     * o pre�o de refer�ncia para a licita��o com o c�digo fornecido. O pre�o de refer�ncia � 
     * normalmente utilizado para compara��es com as propostas apresentadas.
     *
     * @param int $liclicitaCodigo C�digo da licita��o para a qual se deseja obter o pre�o de refer�ncia.
     * @param int $numeroLoteCodigo C�digo do lote da licita��o para a qual se deseja obter o pre�o de refer�ncia.
     *
     * @return mixed Retorna o pre�o de refer�ncia associado � licita��o, ou `null` caso n�o seja encontrado.
     */
    public function obterPrecoReferencia($liclicitaCodigo, $numeroLoteCodigo)
    {
        return $this->licilicitemRepository->getReferencePriceLot($liclicitaCodigo, $numeroLoteCodigo);
    }

    /**
     * Obt�m a lista de fornecedores e propostas para uma licita��o espec�fica.
     * Utiliza cache para otimizar a consulta.
     *
     * @param int $liclicitaCodigo O c�digo da licita��o.
     * @param int $numeroLoteCodigo O c�digo do lote da licita��o.
     * @param int $valorDeReferencia O valor de refer�ncia utilizado para determinar, com base nas regras de par�metros, quais propostas est�o habilitadas a participar, considerando os limites de toler�ncia para valores excedentes e car�ncia.
     * @param boolean $ignorarCache Indica se o cache deve ser ignorado, geralmente utilizado em rotinas de atualiza��o e persist�ncia de dados no banco.
     * 
     * @return \Illuminate\Support\Collection A cole��o de resultados da consulta.
     */
    public function obterListaDeFornecedoresEProposta($liclicitaCodigo, $numeroLoteCodigo, $valorDeReferencia = null, $ignorarCache = false)
    {
        try {

            if ($ignorarCache) {
                return $this->licilicitemRepository->getProposalSupplierListLots($liclicitaCodigo, $numeroLoteCodigo, $valorDeReferencia);
            }

            $cacheKey = "fornecedores_propostas_lotes_{$liclicitaCodigo}_{$numeroLoteCodigo}";

            return Cache::remember($cacheKey, 5, function () use ($liclicitaCodigo, $numeroLoteCodigo, $valorDeReferencia) {
                return $this->licilicitemRepository->getProposalSupplierListLots($liclicitaCodigo, $numeroLoteCodigo, $valorDeReferencia);
            });

        } catch (\Exception $e) {
            throw new JulgamentoException("#JUL01 - Exce��o ao obter fornecedores e propostas: " . $e->getMessage() . "");
        }
    }

    /**
     * Obt�m o c�digo do lote de julgamento para uma licita��o espec�fica e um lote de licita��o.
     *
     * Esta fun��o invoca o reposit�rio respons�vel por obter o c�digo do lote de or�amento relacionado
     * � licita��o e ao lote de licita��o fornecidos como par�metros.
     *
     * @param int $numeroloteCodigo O c�digo do lote da licita��o.
     * 
     * @return mixed O c�digo do lote de or�amento correspondente ao lote de julgamento.
     */
    public function obterCodigoDoItemDeJulgamento($numeroloteCodigo)
    {
        try {

            return $this->julgItemRepository->findLotBudget($numeroloteCodigo);

        } catch (\Exception $e) {

            throw new JulgamentoException("#JUL02 - Exce��o ao obter c�digo do lote de julgamento: " . $e->getMessage());

        }
    }

    /**
     * Obt�m os dados do lote selecionado com base no c�digo do lote da licita��o.
     * 
     * Esta fun��o interage com o reposit�rio `liclicitemLoteRepository` para buscar os dados de um lote espec�fico
     * da licita��o a partir dos par�metros fornecidos. Caso ocorra um erro durante o processo, uma exce��o
     * personalizada `JulgamentoException` ser� lan�ada.
     *
     * @param int $numeroloteCodigo C�digo do lote.
     * @return mixed Dados do lote selecionado, retornados pelo reposit�rio.
     * 
     * @throws JulgamentoException Se ocorrer um erro ao obter os dados do lote selecionado.
     */
    public function obterDadosDoLotesSelecionado($numeroloteCodigo)
    {
        try {

            return $this->liclicitemLoteRepository->getDataLotsSelected($numeroloteCodigo);

        } catch (\Exception $e) {

            throw new JulgamentoException("#JUL213 - Exce��o ao obter dados do lote selecionado: " . $e->getMessage());

        }
    }

    /**
     * Obt�m o pr�ximo fornecedor v�lido para registrar um lance em uma licita��o.
     * 
     * A fun��o percorre a lista de fornecedores e suas propostas, encontra o �ltimo lance registrado e
     * retorna o c�digo do pr�ximo fornecedor dispon�vel para registrar um lance, considerando o status
     * de cada fornecedor.
     * 
     * Se o c�digo do lote de or�amento n�o for informado, a fun��o tenta obter esse c�digo
     * a partir do lote de julgamento. Caso contr�rio, um erro � lan�ado se n�o for poss�vel encontrar
     * um fornecedor v�lido.
     *
     * @param int $liclicitaCodigo O c�digo da licita��o.
     * @param int $liclicitemCodigo O c�digo do lote da licita��o.
     * @param int|null $pcorcamitemCodigo O c�digo do lote de or�amento (opcional).
     * 
     * @return int O c�digo do pr�ximo fornecedor dispon�vel para registrar um lance.
     * 
     * @throws JulgamentoException Se n�o for poss�vel encontrar um fornecedor v�lido.
     */
    public function obterProximoFornecedorParaLance($liclicitaCodigo, $numeroloteCodigo, $julgItemCodigo = null, $valorDeReferencia = null, $ignorarCache = false)
    {
        try {

            $listaDeFornecedoresEProposta = $this->obterListaDeFornecedoresEProposta($liclicitaCodigo, $numeroloteCodigo, $valorDeReferencia, $ignorarCache);

            if(empty($julgItemCodigo)) {

                $julgItemObject = $this->obterCodigoDoItemDeJulgamento($numeroloteCodigo);

                if (!empty($julgItemObject)) {
                    $julgItemCodigo = $julgItemObject->l30_codigo;
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

                        if (empty($numeroloteCodigo)) {
                            $julgItemObject = $this->obterCodigoDoItemDeJulgamento($numeroloteCodigo);

                            // numerolote alterar
                            if (!empty($julgItemObject)) {
                                $numeroloteCodigo = $julgItemObject->l30_numerolote;
                            }
                        }

                        $proximoJulgForne = $this->julgForneRepository->findLotSupplier($proximoFornecedorCode, $numeroloteCodigo);

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
                        throw new JulgamentoException("#JUL05 - Exce��o: Nenhum fornecedor v�lido encontrado para o lote de or�amento {$julgItemCodigo}.");
                    }

                }

            }

        } catch (\Exception $e) {

            throw new JulgamentoException("#JUL06 - Exce��o ao encontrar o pr�ximo fornecedor para lance: " . $e->getMessage());

        }
    }

    /**
     * Obt�m o �ltimo lance registrado para um fornecedor e um lote de or�amento espec�ficos.
     *
     * Esta fun��o busca o lance mais recente de um fornecedor para um lote de or�amento fornecido,
     * verificando se tanto o fornecedor quanto o lote de or�amento existem antes de realizar a consulta.
     * 
     * Se o fornecedor ou o lote de or�amento n�o forem encontrados, a fun��o retorna `false`.
     *
     * @param int $pcorcamforneCodigo O c�digo do fornecedor.
     * @param int $pcorcamitemCodigo O c�digo do lote de or�amento.
     * 
     * @return mixed O �ltimo lance registrado ou `false` caso o fornecedor ou o lote de or�amento n�o sejam encontrados.
     */
    public function obterLance($pcorcamforneCodigo, $numeroloteCodigo)
    {
        try {

            $listaJulgForne = $this->julgForneRepository->findLotSupplier($pcorcamforneCodigo, $numeroloteCodigo);
            $julgItem = $this->julgItemRepository->findLotBudget($numeroloteCodigo);

            if (!empty($julgItem) && !empty($listaJulgForne)) {

                return $this->julgLanceRepository->findLastBidSupplierNotNull($listaJulgForne->l34_codigo, $julgItem->l30_codigo);

            } else {

                return false;

            }

        } catch (\Exception $e) {

            throw new JulgamentoException("#JUL07 - Exce��o ao obter lance: " . $e->getMessage());

        }
    }

    /**
     * Obt�m uma lista de fornecedores que possuem o modelo de microempresa para um lote espec�fico em uma licita��o.
     *
     * Esta fun��o utiliza o reposit�rio `licilicitemRepository` para buscar os fornecedores associados a um lote 
     * de licita��o e filtra os que possuem o modelo de microempresa. Em caso de falha, lan�a uma exce��o 
     * personalizada para lidar com erros no processo de obten��o de dados.
     *
     * @param mixed $liclicita Identificador da licita��o para o qual os fornecedores ser�o buscados.
     * @param mixed $liclicitem Identificador do lote de licita��o relacionado.
     *
     * @return mixed Retorna a lista de fornecedores com modelo de microempresa.
     *
     * @throws JulgamentoException Caso ocorra um erro durante o processo, lan�a uma exce��o contendo detalhes do erro.
     */
    public function obterFornecedoresComModeloDeMicroempresa($liclicita, $numeroloteCodigo, $valorDeReferencia)
    {
        try {
            
            return $this->licilicitemRepository->getSuppliersWithAMicroEnterpriseLot($liclicita, $numeroloteCodigo, $valorDeReferencia);

        } catch (\Exception $e) {

            throw new JulgamentoException(
                sprintf(
                    "#JU08 - Exce��o ao obter fornecedores com modelo de microempresa. Licita��o: %s, Item: %s. Detalhes do erro: %s - #JU1",
                    $liclicita,
                    $numeroloteCodigo,
                    $e->getMessage()
                )
            );

        }
    }

    /**
     * Registra um lance para um fornecedor e um lote de or�amento espec�ficos.
     *
     * Esta fun��o verifica se o lote de or�amento e o fornecedor existem. Se algum deles n�o existir, 
     * ele cria um novo registro para o lote de or�amento e/ou fornecedor com o status correspondente.
     * Depois, registra o lance no reposit�rio de lances.
     * 
     * Caso haja algum erro ao registrar o lance, uma exce��o � lan�ada.
     *
     * @param int $pcorcamforneCodigo O c�digo do fornecedor.
     * @param int $numeroLote O c�digo do lote de or�amento.
     * @param float $valorLance O valor do lance a ser registrado.
     * @param int $liclicitaCodigo O c�digo da licita��o.
     * @param int $liclicitemCodigo O c�digo dos itens licita��o.
     * 
     * @return mixed O objeto do lance registrado.
     * 
     * @throws JulgamentoException Se ocorrer um erro ao registrar o lance.
     */
    public function registrarLance($liclicitaCodigo, $pcorcamforneCodigo, $valorLance, $numeroLote)
    {
        DB::beginTransaction();

        try {
            
            $julgItem = $this->julgItemRepository->findLotBudget($numeroLote);

            if (empty($julgItem)) {
                $julgItem = $this->registrarJulgItem($numeroLote, 'Em aberto');
            }

            $julgForne = $this->julgForneRepository->findLotSupplier($pcorcamforneCodigo, $numeroLote);

            if (empty($julgForne)) {
                $julgForne = $this->registrarJulgForne($pcorcamforneCodigo, $numeroLote, 'Normal');
            }

            $this->validaValorDoLance($julgItem->l30_codigo, $valorLance, $liclicitaCodigo, $numeroLote);

            $julgLance = $this->julgLanceRepository->create([
                'l32_julgitem' => $julgItem->l30_codigo,
                'l32_julgforne' => $julgForne->l34_codigo,
                'l32_lance' => $valorLance
            ]);

            if (!$julgLance) {
                throw new JulgamentoException("#JUL09 - Exce��o ao registrar lance. Tente novamente.");
            }

            if ($valorLance === null) {
                $julgForneStatus = $this->julgForneStatusRepository->findLabel(mb_convert_encoding('Sem lance', 'UTF-8', 'ISO-8859-1'));
                $this->faseDeLancesService->alterarStatusFornecedor($julgForne->l34_codigo, $julgForneStatus->l35_codigo, mb_convert_encoding('O fornecedor foi desclassificado das pr�ximas fases do preg�o por n�o ter conseguido igualar ou superar o valor estipulado na sequ�ncia de lances.', 'UTF-8', 'ISO-8859-1'));
            }

            DB::commit();
            return $julgLance;

        } catch (\Exception $e) {
            
            DB::rollBack();
            throw new JulgamentoException("#JUL10 - Exce��o ao registrar lance: " . $e->getMessage());

        }
    }

    /**
     * Reverte o �ltimo lance registrado para um lote de or�amento espec�fico.
     *
     * Esta fun��o busca o �ltimo lance registrado para o lote de or�amento fornecido. 
     * Se o lance existir, ele � deletado do reposit�rio de lances. 
     * Caso contr�rio, nenhuma a��o � tomada.
     * 
     * @param int $numeroLote O c�digo do lote de or�amento.
     * 
     * @return void
     */
    public function reverterLance($numeroLote)
    {
        DB::beginTransaction();

        try {

            $julgItem = $this->julgItemRepository->findLotBudget($numeroLote);

            if (empty($julgItem)) {
                throw new JulgamentoException("#JUL11 - Exce��o: Lote de or�amento n�o encontrado.");
            }

            $ultimoJulgLance = $this->julgLanceRepository->findLastBid($julgItem->l30_codigo);

            if (!empty($ultimoJulgLance)) {
                $this->julgLanceRepository->delete('l32_codigo', $ultimoJulgLance->l32_codigo);
            } else {
                throw new JulgamentoException("#JUL12 - Exce��o: Nenhum lance encontrado para o lote de or�amento informado.");
            }

            if ($ultimoJulgLance->l32_lance === null) {
                $julgForneStatus = $this->julgForneStatusRepository->findLabel(mb_convert_encoding('Normal', 'UTF-8', 'ISO-8859-1'));
                $this->faseDeLancesService->alterarStatusFornecedor($ultimoJulgLance->l32_julgforne, $julgForneStatus->l35_codigo, mb_convert_encoding('Revertendo o lance que desclassificou o fornecedor das pr�ximas fases do preg�o por n�o ter conseguido igualar ou superar o valor estipulado na sequ�ncia de lances.', 'UTF-8', 'ISO-8859-1'));
            }

            DB::commit();

        } catch (\Exception $e) {
            
            DB::rollBack();
            throw new JulgamentoException("#JUL13 - Exce��o ao reverter o lance: " . $e->getMessage());

        }
    }

    /**
     * Limpa todos os lances registrados para um lote de or�amento espec�fico.
     *
     * Esta fun��o remove todos os lances associados ao lote de or�amento fornecido.
     * Primeiro, busca o lote de or�amento com base no c�digo fornecido e, em seguida,
     * exclui todos os lances registrados para esse lote.
     *
     * @param int $numeroLote O c�digo do lote de or�amento.
     * 
     * @return void
     */
    public function limparLances($numeroLote)
    {
        $codigosItensLista = explode(",", $numeroLote);

        DB::beginTransaction();

        try {

            foreach ($codigosItensLista as $i => $value) {

                $julgItemRepository = $this->julgItemRepository->findLotBudget($value);
    
                if (empty($julgItemRepository)) {
                    throw new JulgamentoException("#JUL14 - Exce��o: Lote de or�amento n�o encontrado.");
                }
    
                $lances = $this->julgLanceRepository->find('l32_julgitem', $value);

                if (!empty($lances)) {
                    $resultado = $this->julgLanceRepository->deleteBidItem($julgItemRepository->l30_codigo);
        
                    if ($resultado === false) {
                        throw new JulgamentoException("#JUL15 - Exce��o ao limpar os lances do lote de or�amento.");
                    }
                }
                
                $julgForne = $this->julgForneRepository->find('l34_numerolote', $value);

                $codigoArray = [];
                foreach ($julgForne as $item) {
                    $codigoArray[] = $item->l34_codigo;
                }

                $julgForneHist = $this->julgForneHistRepository->deleteIn('l36_julgforne', $codigoArray);
                
                if ($julgForneHist === false) {
                    throw new JulgamentoException("#JUL121 - Exce��o ao limpar os historicos dos fornecedores do julgamento do item.");
                }

                if ($julgForne->isNotEmpty()) {
                    
                    $julgForne = $this->julgForneRepository->delete('l34_numerolote', $value);
                    
                    if (empty($julgForne)) {
                        throw new JulgamentoException("#JUL18 - Exce��o ao limpar os fornecedores do julgamento do item.");
                    }

                }

            }

            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            throw new JulgamentoException("#JUL19 - Exce��o ao limpar lances: " . $e->getMessage());

        }
    }

    /**
     * Altera o status de um lote de or�amento e registra o motivo da altera��o.
     *
     * Esta fun��o verifica se o lote de or�amento existe. Caso o lote n�o exista, ele ser� criado
     * com o status "Em aberto". Se o lote j� existir, o status � alterado para o novo status fornecido.
     * Ap�s a altera��o, o motivo da altera��o � registrado no hist�rico de status do lote.
     *
     * @param int $codigosItens O c�digos dos itens.
     * @param int $novoStatus O c�digo do novo status a ser atribu�do ao lote de or�amento.
     * @param string $motivo O motivo para a altera��o do status do lote de or�amento.
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

                // Busca a exist�ncia do lote no or�amento pelo c�digo
                $liclicitemLote = Liclicitemlote::where('l04_numerolote', $codigoItem)->first();

                if (empty($liclicitemLote)) {
                    throw new JulgamentoException("#JUL20 - Exce��o: Lote de or�amento com c�digo {$codigoItem} n�o encontrado.");
                }

                $pcorcamitens = $this->liclicitemLoteRepository->getOrcamItemByLoteNumber($codigoItem);

                if (empty($pcorcamitens)) {
                    throw new JulgamentoException("#JUL20 - Exce��o: Lote de or�amento com c�digo {$codigoItem} n�o encontrado.");
                }

                // Busca o lote de julgamento associado ao or�amento
                $julgItem = $this->julgItemRepository->findLotBudget($liclicitemLote->l04_numerolote);
    
                if (!empty($julgItem) && $julgItem->l30_julgitemstatus == 8 && $novoStatus !== 8) {

                    foreach ($pcorcamitens as $i => $pcorcamitem) {

                        $pcorcamvalItens = $this->pcorcamvalRepository->find('pc23_orcamitem', $pcorcamitem->pc22_orcamitem);
    
                        if (!empty($pcorcamvalItens)) {
                            $pcorcamval = $this->pcorcamvalRepository->delete('pc23_orcamitem', $pcorcamitem->pc22_orcamitem);
        
                            if ($pcorcamval === false) {
                                throw new JulgamentoException(
                                    sprintf(
                                        "#JU21 - Exce��o ao tentar excluir os registros de pcorcamval. C�digo do lote: %s. Verifique os logs e o estado do banco de dados.",
                                        $pcorcamitem->pc22_orcamitem
                                    )
                                );
                            }
                        }
        
                        $pcorcamjulgItens = $this->pcorcamjulgRepository->find('pc24_orcamitem', $pcorcamitem->pc22_orcamitem);
        
                        if (!empty($pcorcamjulgItens)) {
                            $pcorcamjulg = $this->pcorcamjulgRepository->deletePcorcamitemRecords($pcorcamitem->pc22_orcamitem);
        
                            if ($pcorcamjulg === false) {
                                throw new JulgamentoException(
                                    sprintf(
                                        "#JU22 - Exce��o ao tentar excluir registros de pcorcamitem para o c�digo do lote: %s. A opera��o de exclus�o retornou false. Verifique os dados no banco de dados e os logs para mais detalhes.",
                                        $pcorcamitem->pc22_orcamitem
                                    )
                                );
                            }
                        }
                        
                    }
                }

                if (!empty($julgItem) && $julgItem->l30_julgitemstatus == 7 && $novoStatus !== 2) {

                    if (!empty($this->julgForneRepository->find('l34_numerolote', $codigoItem))) {
        
                        $updateSuppliers = $this->julgForneRepository->updateStatusSuppliersFromBestBidToNoBidLot($codigoItem, ['l34_julgfornestatus' => 3]);
        
                        if (empty($updateSuppliers)) {
                            throw new JulgamentoException(
                                sprintf(
                                    "#JU23 - Exce��o ao atualizar o status dos fornecedores para o c�digo do lote: %s. Nenhum registro foi alterado. Verifique o estado dos dados no banco de dados e os logs da aplica��o para mais detalhes.",
                                    $codigoItem
                                )
                            );
                        }
                    }
                }

                // Caso o lote de julgamento n�o exista, cria um novo com o status "Em Aberto"
                if (empty($julgItem)) {

                    // Busca o status padr�o "Em Aberto"
                    $statusJulgItem = $this->itemStatusRepository->findLabel(mb_convert_encoding('Em aberto', 'UTF-8', 'ISO-8859-1'));
    
                    if (empty($statusJulgItem)) {
                        throw new JulgamentoException("#JUL24 - Exce��o: Status Em aberto n�o encontrado.");
                    }
    
                    // Cria o lote de julgamento no banco de dados
                    $julgItem = $this->julgItemRepository->create([
                        'l30_numerolote' => $liclicitemLote->l04_numerolote,
                        'l30_julgitemstatus' => $statusJulgItem->l31_codigo,
                    ]);
    
                    if (!$julgItem) {
                        throw new JulgamentoException("#JUL25 - Exce��o ao criar o lote de or�amento.");
                    }
    
                    // Busca o novo status para atualiza��o
                    $novoStatusJulgItem = $this->itemStatusRepository->findId($novoStatus);
    
                    if (empty($novoStatusJulgItem)) {
                        throw new JulgamentoException("#JUL26 - Exce��o: Novo status informado � inv�lido.");
                    }
    
                    // Atualiza o status do lote de julgamento
                    $resultado = $this->julgItemRepository->update($julgItem->l30_codigo, [
                        'l30_julgitemstatus' => $novoStatusJulgItem->l31_codigo,
                    ]);
    
                    if (!$resultado) {
                        throw new JulgamentoException("#JUL27 - Exce��o ao atualizar o status do lote de or�amento.");
                    }

                } else {

                    // Caso o lote de julgamento j� exista, busca o novo status para atualiza��o
                    $novoStatusJulgItem = $this->itemStatusRepository->findId($novoStatus);
    
                    if (empty($novoStatusJulgItem)) {
                        throw new JulgamentoException("#JUL28 - Exce��o: Novo status informado � inv�lido.");
                    }
    
                    // Atualiza o status do lote de julgamento existente
                    $resultado = $this->julgItemRepository->update($julgItem->l30_codigo, [
                        'l30_julgitemstatus' => $novoStatusJulgItem->l31_codigo,
                    ]);
    
                    if (!$resultado) {
                        throw new JulgamentoException("#JUL29 - Exce��o ao atualizar o status do lote de or�amento.");
                    }

                }

                // Cria um registro no hist�rico para o lote atualizado
                $historico = $this->julgItemHistRepository->create([
                    'l33_julgitem' => $julgItem->l30_codigo,
                    'l33_julgitemstatus' => $julgItem->l30_julgitemstatus,
                    'l33_motivo' => $motivo,
                ]);
    
                if (!$historico) {
                    throw new JulgamentoException("#JUL30 - Exce��o ao registrar o hist�rico do lote.");
                }
            }
    
            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            throw new JulgamentoException("#JUL31 - Exce��o ao alterar status do lote: " . $e->getMessage());

        }
    }     

    public function atualizaStatusFornecedor($numeroLote, $pcorcamforneCodigo, $statusFonecedor, $motivoFornecedor)
    {
        try {

            $julgForne = $this->julgForneRepository->findLotSupplier($pcorcamforneCodigo, $numeroLote);

            if (empty($julgForne)) {
                $julgForne = $this->registrarJulgForne($pcorcamforneCodigo, $numeroLote, 'Normal');
            }

            $this->faseDeLancesService->alterarStatusFornecedor($julgForne->l34_codigo, $statusFonecedor, $motivoFornecedor);

        } catch (\Exception $e) {

            throw new JulgamentoException("#JUL32 - Exce��o ao alterar status do fornecedor: " . $e->getMessage());

        }
    }

    /**
     * Finaliza o processo de julgamento de um lote de or�amento em uma licita��o.
     *
     * Este m�todo realiza as seguintes opera��es:
     * - Obt�m fornecedores vinculados ao lote de or�amento.
     * - Recupera e organiza os detalhes dos lances, ordenando-os pelo menor valor.
     * - Gera a pontua��o e armazena os dados processados nos reposit�rios correspondentes.
     * - Atualiza o status dos fornecedores e do lote de or�amento para "Melhor Proposta" e "Julgado", respectivamente.
     * - Remove registros antigos de lances e julgamentos, substituindo-os pelos novos dados processados.
     * - Garante a integridade do processo com transa��es de banco de dados.
     *
     * @param mixed $liclicitaCodigo C�digo da licita��o.
     * @param mixed $liclicitemCodigo C�digo do lote da licita��o.
     * @param mixed $numeroLote C�digo do lote de or�amento.
     * 
     * @return bool Retorna `true` se o processo for conclu�do com sucesso.
     * 
     * @throws JulgamentoException Em caso de falhas em qualquer etapa do processo.
     */
    public function finalizar($numeroLote)
    {
        DB::beginTransaction();
    
        try {
            
            $julgFornecedores = $this->julgForneRepository->findLotSuppliers($numeroLote);

            if (empty($julgFornecedores)) {
                throw new JulgamentoException(
                    sprintf(
                        "#JU36 - Exce��o ao buscar fornecedores para o lote com c�digo: %s. Nenhum fornecedor encontrado.",
                        $numeroLote
                    )
                );
            }

            foreach ($julgFornecedores as $k => $julgFornecedor) {
                $statusFornecedor = $this->julgForneStatusRepository->findLabel(mb_convert_encoding('Melhor Proposta', 'UTF-8', 'ISO-8859-1'));
                $this->faseDeLancesService->alterarStatusFornecedor($julgFornecedor->l34_codigo, $statusFornecedor->l35_codigo, mb_convert_encoding('O status dos fornecedores foram atualizados para melhor lance ap�s a conclus�o da rotina de julgamento.', 'UTF-8', 'ISO-8859-1'));
            }

            $statusItem = $this->itemStatusRepository->findLabel(mb_convert_encoding('Aguardando Readequa��o', 'UTF-8', 'ISO-8859-1'));
            $statusItemUpdate = $this->alterarStatusItem($numeroLote, $statusItem->l31_codigo, mb_convert_encoding('O status do lote foi atualizado para Aguardando Readequa��o ap�s a conclus�o da rotina de julgamento.', 'UTF-8', 'ISO-8859-1'));

            if (empty($statusItemUpdate)) {
                throw new JulgamentoException("#JUL40 - Exce��o ao atualizar o status do lote ap�s a conclus�o da rotina de julgamento.");
            }

            DB::commit();
            return true;

        } catch (\Exception $e) {

            DB::rollBack();
            throw new JulgamentoException("#JUL43 - Exce��o na rotina de finaliza��o de julgamento: " . $e->getMessage());

        }
    }

    /**
     * Libera fornecedores com modelo de microempresa para um lote de licita��o.
     *
     * - Obt�m fornecedores utilizando `obterFornecedoresComModeloDeMicroempresa`.
     * - Valida se h� fornecedores e se o status "Normal" existe no reposit�rio.
     * - Atualiza o status de cada fornecedor encontrado para "Normal".
     *
     * @param mixed $liclicita ID da licita��o.
     * @param mixed $liclicitem ID do lote de licita��o.
     * 
     * @throws JulgamentoException Em caso de erro durante o processo.
     */
    public function liberarMicroEmpresas($liclicita, $numeroLote, $valorDeReferencia=null)
    {
        try {

            $fornecedoresComModeloDeMicroempresa = $this->obterFornecedoresComModeloDeMicroempresa($liclicita, $numeroLote, $valorDeReferencia);
            
            if (empty($fornecedoresComModeloDeMicroempresa)) {
                throw new JulgamentoException(
                    sprintf(
                        "#JU44 - Exce��o ao obter fornecedores com modelo de microempresa. Licita��o: %s, Item: %s. Nenhum fornecedor encontrado.",
                        $liclicita,
                        $numeroLote
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

                $julgForne = $this->julgForneRepository->findLotSupplier($fornecedorMicroempresa->pc21_orcamforne, $fornecedorMicroempresa->l04_numerolote);

                if (empty($julgForne)) {
                    throw new JulgamentoException(
                        sprintf(
                            "#JU46 - Exce��o ao buscar fornecedor para o lote com c�digo de fornecedor: %s e c�digo de item: %s. Nenhum fornecedor encontrado.",
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
                    "#JU47 - Exce��o ao liberar microempresas para a licita��o %s e lote %s. Detalhes do erro: %s",
                    $liclicita,
                    $numeroLote,
                    $e->getMessage()
                )
            );
            
        }
    }

    /**
     * Registra o julgamento de um lote de or�amento.
     *
     * Esta fun��o cria um registro no reposit�rio de itens com os detalhes do julgamento,
     * incluindo o c�digo do lote e o status associado.
     *
     * @param int $numeroLote C�digo do lote no or�amento.
     * @param string $statusLabel R�tulo do status do lote.
     *
     * @return mixed Retorna o registro criado do lote de or�amento.
     *
     * @throws JulgamentoException Se o status do lote n�o for encontrado ou ocorrer um erro na cria��o.
     */
    private function registrarJulgItem($numeroLote, $statusLabel)
    {
        $statusItem = $this->itemStatusRepository->findLabel(mb_convert_encoding($statusLabel, 'UTF-8', 'ISO-8859-1'));

        if (empty($statusItem)) {
            throw new JulgamentoException("#JUL52 - Exce��o: Status do lote de or�amento n�o encontrado.");
        }

        $julgItem = $this->julgItemRepository->create([
            'l30_numerolote' => $numeroLote,
            'l30_julgitemstatus' => $statusItem->l31_codigo,
        ]);

        if (!$julgItem) {

            throw new JulgamentoException("#JUL53 - Exce��o ao criar o lote de or�amento.");

        } else {

            return $julgItem;

        }
    }

    /**
     * Registra o julgamento de um fornecedor para um lote de or�amento.
     *
     * Esta fun��o cria um registro no reposit�rio de fornecedores com os detalhes do 
     * julgamento, incluindo o c�digo do fornecedor, o c�digo do lote e o status associado.
     *
     * @param int $pcorcamforneCodigo C�digo do fornecedor no or�amento.
     * @param int $numeroLote C�digo do lote no or�amento.
     * @param string $statusLabel (Opcional) R�tulo do status do fornecedor, padr�o � "Normal".
     *
     * @return mixed Retorna o registro criado do fornecedor.
     *
     * @throws JulgamentoException Se o status do fornecedor n�o for encontrado ou ocorrer um erro na cria��o.
     */
    private function registrarJulgForne($pcorcamforneCodigo, $numeroLote, $statusLabel="Normal")
    {
        $statusFornecedor = $this->julgForneStatusRepository->findLabel(mb_convert_encoding($statusLabel, 'UTF-8', 'ISO-8859-1'));

        if (empty($statusFornecedor)) {
            throw new JulgamentoException("#JUL54 - Exce��o: Status do fornecedor n�o encontrado.");
        }

        $julgForne = $this->julgForneRepository->create([
            'l34_orcamforne' => $pcorcamforneCodigo,
            'l34_numerolote' => $numeroLote,
            'l34_julgfornestatus' => $statusFornecedor->l35_codigo,
        ]);

        if (!$julgForne) {

            throw new JulgamentoException("#JUL55 - Exce��o ao criar o fornecedor.");

        } else {

            return $julgForne;

        }
    }

    /**
     * Valida o valor de um lance fornecido para um lote em uma licita��o.
     *
     * Esta fun��o verifica se o valor do lance � v�lido, seguindo as regras:
     * - O valor deve ser menor que a menor proposta inicial ou lance anterior, 
     *   considerando a diferen�a m�nima permitida (`l13_difminlance`).
     *
     * @param int $julgItemCodigo C�digo do lote em julgamento.
     * @param float $valorLance Valor do lance fornecido.
     * @param int $liclicitaCodigo C�digo da licita��o.
     * @param int $liclicitemCodigo C�digo do lote na licita��o.
     *
     * @throws JulgamentoException Se o valor do lance n�o for v�lido de acordo com as regras.
     */
    private function validaValorDoLance($julgItemCodigo, $valorLance, $liclicitaCodigo, $numeroLote)
    {
        $param = $this->faseDeLancesService->obterParametros();

        $ultimoLance = $this->julgLanceRepository->findLastBidNotNull($julgItemCodigo);

        if (empty($ultimoLance)) {

            $ultimaProposta = $this->licilicitemRepository->getTheLowestBidLot($liclicitaCodigo, $numeroLote, $param->l13_clapercent);

            if ($valorLance > (floatval($ultimaProposta->l224_vlrun) - floatval($param->l13_difminlance))) {
                throw new JulgamentoException("#JUL56 - Exce��o: O fornecedor deve apresentar um lance inferior ao �ltimo registrado, respeitando o valor m�nimo estipulado para redu��o entre lances.");
            }

        } else {
            
            if ($valorLance !== null && $valorLance > ($ultimoLance->l32_lance - $param->l13_difminlance)) {
                throw new JulgamentoException("#JUL57 - Exce��o: O fornecedor n�o pode enviar um lance maior que o menor lance.");
            }

        }
    }
}
