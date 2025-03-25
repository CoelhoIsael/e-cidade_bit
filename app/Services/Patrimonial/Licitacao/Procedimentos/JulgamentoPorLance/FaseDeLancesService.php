<?php

namespace App\Services\Patrimonial\Licitacao\Procedimentos\JulgamentoPorLance;

use App\Exceptions\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\JulgamentoException;
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

class FaseDeLancesService
{
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
    public function __construct() {
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
    }

    /**
     * Obt�m os par�metros de uma institui��o espec�fica.
     *
     * Esta fun��o recupera os par�metros de julgamento para a institui��o definida na sess�o 
     * atrav�s do m�todo `findParamInstit` do reposit�rio de par�metros (`paramRepo`).
     * O c�digo da institui��o � obtido a partir da sess�o com a chave `'DB_instit'`.
     *
     * @return mixed Retorna os par�metros de julgamento encontrados para a institui��o especificada 
     * ou `null` se n�o houver dados.
     */
    public function obterParametros()
    {
        return $this->julgParamRepository->findParamInstit(session('DB_instit'));
    }

    /**
     * Retorna o status de todos os itens.
     *
     * @return mixed Cole��o com os status dos itens.
     */
    public function obterStatusDosItens()
    {
        return $this->itemStatusRepository->all();
    }

    /**
     * Retorna o status de todos os fornecedores.
     *
     * @return mixed Cole��o com os status dos fornecedores.
     */
    public function obterStatusDosFornecedores()
    {
        return $this->julgForneStatusRepository->all();
    }

    /**
     * Altera os par�metros de julgamento de uma institui��o.
     *
     * Esta fun��o verifica se j� existem par�metros de julgamento registrados para a institui��o 
     * atual. Caso existam, ela tenta atualizar esses par�metros com os dados fornecidos. Se n�o 
     * houver par�metros registrados, novos par�metros s�o criados. Em ambos os casos, se ocorrer 
     * algum erro, uma exce��o personalizada (`JulgamentoException`) � lan�ada para informar o problema.
     *
     * @param array $data Dados a serem usados para atualizar ou criar os par�metros de julgamento.
     * 
     * @throws JulgamentoException Lan�a uma exce��o se ocorrer um erro ao tentar atualizar ou criar 
     * os par�metros de julgamento.
     */
    public function alterarParametro($data)
    {
        try {
            
            $paramInstit = $this->julgParamRepository->findParamInstit(session('DB_instit'));

            if ($paramInstit) {
                
                $paramUpdate = $this->julgParamRepository->update($paramInstit->l13_julgparam, $data);
                
                if (!$paramUpdate) {
                    throw new JulgamentoException("#JU33 - Exce��o ao atualizar os parametros de julgamentos.");
                }

            } else {

                $data['l13_instit'] = session('DB_instit');
                $paramInsert = $this->julgParamRepository->create($data);
                
                if (!$paramInsert) {
                    throw new JulgamentoException("#JU34 - Exce��o ao criar os parametros de julgamentos.");
                }

            }

        } catch (\Exception $e) {

            throw new JulgamentoException("#JU35 - Exce��o ao alterar status do fornecedor: " . $e->getMessage());

        }
    }

    /**
     * Altera o status de um fornecedor e registra o hist�rico da altera��o.
     *
     * Esta fun��o busca o registro do fornecedor, valida o novo status fornecido, 
     * atualiza o status no reposit�rio de fornecedores e registra o hist�rico da altera��o.
     *
     * @param int $julgForneCodigo C�digo do julgamento do fornecedor.
     * @param int $statusFonecedor C�digo do novo status do fornecedor.
     * @param string $motivoFornecedor Motivo para a altera��o do status.
     *
     * @throws JulgamentoException Se o fornecedor n�o for encontrado, o status for inv�lido, 
     *                             a atualiza��o falhar ou o hist�rico n�o for registrado.
     */
    public function alterarStatusFornecedor($julgForneCodigo, $statusFonecedor, $motivoFornecedor)
    {
        $julgForne = $this->julgForneRepository->findId($julgForneCodigo);

        if (empty($julgForne)) {
            throw new JulgamentoException("#JU48 - Exce��o: Julgamento de fornecedor n�o encontrado para o c�digo informado.");
        }

        $statusFornecedor = $this->julgForneStatusRepository->findId($statusFonecedor);

        if (empty($statusFornecedor)) {
            throw new JulgamentoException("#JU49 - Exce��o: Novo status informado � inv�lido.");
        }

        $resultado = $this->julgForneRepository->update($julgForne->l34_codigo, [
            'l34_julgfornestatus' => $statusFornecedor->l35_codigo,
        ]);

        if (!$resultado) {
            throw new JulgamentoException("#JU50 - Exce��o ao atualizar o status do fornecedor.");
        }

        $historico = $this->julgForneHistRepository->create([
            'l36_julgforne' => $julgForne->l34_codigo,
            'l36_julgfornestatus' => $julgForne->l34_julgfornestatus,
            'l36_motivo' => $motivoFornecedor,
        ]);

        if (!$historico) {
            throw new JulgamentoException("#JU51 - Exce��o ao registrar o hist�rico do fornecedor.");
        }
    }
}
