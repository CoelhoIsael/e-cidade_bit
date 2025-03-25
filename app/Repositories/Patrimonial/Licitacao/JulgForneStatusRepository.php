<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julgfornestatus;

class JulgForneStatusRepository
{
    /**
     * Retorna todos os registros da tabela Julgfornestatus.
     * 
     * @return \Illuminate\Database\Eloquent\Collection Retorna todos os registros encontrados.
     */
    public function all()
    {
        return Julgfornestatus::all();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return Julgfornestatus
     */
    public function find($column, $conditions)
    {
        return Julgfornestatus::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return Julgfornestatus Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return Julgfornestatus::findOrFail($id);
    }

    /**
     * Cria um novo registro de status de fornecedor.
     *
     * @param array $dados
     * @return JulgForneStatus
     */
    public function create(array $dados)
    {
        return JulgForneStatus::create($dados);
    }

    /**
     * Atualiza um registro de status de fornecedor existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return JulgForneStatus
     */
    public function update($id, array $dados)
    {
        $status = JulgForneStatus::findOrFail($id);
        $status->update($dados);
        return $status;
    }

    /**
     * Exclui registros da tabela JulgForneStatus com base na condi��o fornecida.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida.
     */
    public function delete($column, $conditions)
    {
        $julgForne = JulgForneStatus::where($column, $conditions);
        return $julgForne->delete();
    }

    /**
     * Busca um status de fornecedor pelo r�tulo.
     *
     * @param string $rotulo
     * @return JulgForneStatus|null
     */
    public function findLabel($rotulo)
    {
        return JulgForneStatus::where('l35_label', $rotulo)->first();
    }
}
