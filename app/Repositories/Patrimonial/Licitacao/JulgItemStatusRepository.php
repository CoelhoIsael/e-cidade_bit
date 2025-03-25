<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julgitemstatus;

class JulgItemStatusRepository
{
    /**
     * Retorna todos os registros da tabela JulgItem.
     * 
     * @return \Illuminate\Database\Eloquent\Collection Retorna todos os registros encontrados.
     */
    public function all()
    {
        return JulgItemStatus::all();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return JulgItemStatus
     */
    public function find($column, $conditions)
    {
        return JulgItemStatus::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return JulgItemStatus Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return JulgItemStatus::findOrFail($id);
    }

    /**
     * Cria um novo status de item.
     *
     * @param array $dados
     * @return JulgItemStatus
     */
    public function create(array $dados)
    {
        return JulgItemStatus::create($dados);
    }

    /**
     * Atualiza um status de item existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return JulgItemStatus
     */
    public function update($id, array $dados)
    {
        $julgItemStatus = JulgItemStatus::findOrFail($id);
        $julgItemStatus->update($dados);
        return $julgItemStatus;
    }

    /**
     * Exclui registros da tabela JulgItemStatus com base na condi��o fornecida.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida.
     */
    public function delete($column, $conditions)
    {
        $julgItemStatus = JulgItemStatus::where($column, $conditions);
        return $julgItemStatus->delete();
    }

    /**
     * Busca um status de item pelo rotulo.
     *
     * @param string $rotulo
     * @return JulgItemStatus|null
     */
    public function findLabel($rotulo)
    {
        return JulgItemStatus::where('l31_label', $rotulo)->first();
    }
}
