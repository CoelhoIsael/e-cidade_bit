<?php

namespace App\Repositories\Patrimonial\Licitacao;
use cl_pcorcamforne;
use App\Models\Patrimonial\Licitacao\Pcorcamforne;
use Illuminate\Support\Facades\DB;

class PcorcamforneRepository
{
    private Pcorcamforne $model;

    public function __construct()
    {
        $this->model = new Pcorcamforne();
    }

    /**
     * Retorna todos os registros da tabela Pcorcamforne.
     * 
     * @return \Illuminate\Database\Eloquent\Collection Retorna todos os registros encontrados.
     */
    public function all()
    {
        return Pcorcamforne::all();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return Pcorcamforne
     */
    public function find($column, $conditions)
    {
        return Pcorcamforne::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return Pcorcamforne Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return Pcorcamforne::findOrFail($id);
    }

    public function getfornecedoreslicitacao($l20_codigo)
    {
        $clpcorcamforne = new cl_pcorcamforne();
        $sql = $clpcorcamforne->queryfornecedores($l20_codigo);
        return DB::select($sql);
    }

    public function getSupplierAndTheirDataFromCgm($orcamforne)
    {
        return Pcorcamforne::where('pc21_orcamforne', $orcamforne)
            ->join('protocolo.cgm', 'protocolo.cgm.z01_numcgm', '=', 'compras.pcorcamforne.pc21_numcgm')
            ->first();
    }
}

