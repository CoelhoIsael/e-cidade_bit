<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julgitemhist;

class JulgItemHistRepository
{
    /**
     * Retorna todos os registros da tabela JulgItemHist.
     * 
     * @return \Illuminate\Database\Eloquent\Collection Retorna todos os registros encontrados.
     */
    public function all()
    {
        return JulgItemHist::all();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return JulgItemHist
     */
    public function find($column, $conditions)
    {
        return JulgItemHist::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return JulgItemHist Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return JulgItemHist::findOrFail($id);
    }

    /**
     * Cria um novo registro de hist�rico de item.
     *
     * @param array $dados
     * @return JulgItemHist
     */
    public function create(array $dados)
    {
        return JulgItemHist::create($dados);
    }

    /**
     * Atualiza um registro de hist�rico de item existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return JulgItemHist
     */
    public function update($id, array $dados)
    {
        $historico = JulgItemHist::findOrFail($id);
        $historico->update($dados);
        return $historico;
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
        $julgForne = JulgItemHist::where($column, $conditions);
        return $julgForne->delete();
    }
}
