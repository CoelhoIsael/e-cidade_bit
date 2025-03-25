<?php

namespace App\Services\Configuracao\Empenho;

use App\Models\Empenho\EmpAutoriza;
use App\Repositories\Configuracao\Empenho\EmpAutorizaRepository;
use App\Support\String\StringHelper;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Capsule\Manager as DB;

class EmpAutorizaService
{
    /**
     * @var EmpAutorizaRepository
     */
    private $empAutorizaRepository;

    public function __construct()
    {
        $this->empAutorizaRepository = new EmpAutorizaRepository();
    }

    public function getByPrimaryKeyRange($iCodigoEmpenhoInicial, $iCodigoEmpenhoFinal)
    {
        if (!EmpAutoriza::find($iCodigoEmpenhoInicial)) {
            throw new Exception(mb_convert_encoding('Usu�rio: C�digo de autoriza��o inicial n�o existe.', 'UTF-8', 'ISO-8859-1'));
        }

        if (!EmpAutoriza::find($iCodigoEmpenhoFinal)) {
            throw new Exception(mb_convert_encoding('Usu�rio: C�digo de autoriza��o final n�o existe.', 'UTF-8', 'ISO-8859-1'));
        }

        return StringHelper::convertToUtf8($this->empAutorizaRepository->getByPrimaryKeyRange($iCodigoEmpenhoInicial, $iCodigoEmpenhoFinal));
    }

    public function updateDateByIds($dados)
    {
        $datasInvalidas = [];

        foreach ($dados as $id => $novaDataEmissao) {
            if (empty($novaDataEmissao)) {
                throw new \Exception(mb_convert_encoding('Usu�rio: preencha todas as datas das autoriza��es selecionadas.', 'UTF-8', 'ISO-8859-1'));
            }

            $novaDataEmissao = Carbon::createFromFormat('d/m/Y', $novaDataEmissao)->format('Y-m-d');

            // Consulta sql para verificar se a data de autoriza��o � maior que a data do empenho.
            $rsVinculoEmpenho = DB::select("SELECT * FROM empempaut INNER JOIN empempenho ON e60_numemp = e61_numemp WHERE e61_autori = $id and e60_emiss < '$novaDataEmissao'");

            if (!empty($rsVinculoEmpenho)) {
                array_push($datasInvalidas, $id);
            }
        }

        if (!empty($datasInvalidas)) {
            throw new \Exception(mb_convert_encoding('Erro ao atualizar registros! Usu�rio: A(s) autoriza��o(�es) ['.implode(',', $datasInvalidas).'] est�(�o) vinculadas(s) a empenho(s), e a data da autoriza��o n�o pode ser superior a data do empenho. Gentileza verificar', 'UTF-8', 'ISO-8859-1'));
        }

        return $this->empAutorizaRepository->updateDateByIds($dados);
    }
}
