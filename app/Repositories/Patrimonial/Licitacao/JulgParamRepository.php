<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julgparam;

class JulgParamRepository
{
    /**
     * Retorna todos os registros da tabela Julgparam.
     * 
     * @return \Illuminate\Database\Eloquent\Collection Retorna todos os registros encontrados.
     */
    public function all()
    {
        return Julgparam::all();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return Julgparam
     */
    public function find($column, $conditions)
    {
        return Julgparam::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return Julgparam Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return Julgparam::findOrFail($id);
    }

    /**
     * Cria um novo registro no hist�rico de fornecedor.
     *
     * @param array $dados
     * @return Julgparam
     */
    public function create(array $dados)
    {
        return Julgparam::create($dados);
    }

    /**
     * Atualiza um registro de hist�rico de fornecedor existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return Julgparam
     */
    public function update($id, array $dados)
    {
        $julgParam = Julgparam::findOrFail($id);
        $julgParam->update($dados);
        return $julgParam;
    }

    /**
     * Exclui registros da tabela Julgparam com base na condi��o fornecida.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida.
     */
    public function delete($column, $conditions)
    {
        $julgParam = Julgparam::where($column, $conditions);
        return $julgParam->delete();
    }

    /**
     * Encontra um par�metro de julgamento com base na institui��o.
     *
     * Esta fun��o consulta o modelo Julgparam para localizar o primeiro registro
     * que corresponde � institui��o fornecida.
     *
     * @param string $instit O c�digo ou identificador da institui��o.
     * 
     * @return \App\Models\Julgparam|null Retorna uma inst�ncia de Julgparam se encontrado, 
     * ou `null` caso n�o exista um registro correspondente.
     */
    public function findParamInstit($instit)
    {
        return Julgparam::where('l13_instit', $instit)->first();
    }
}
