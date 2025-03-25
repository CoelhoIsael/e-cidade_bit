<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julgfornehist;

class JulgForneHistRepository
{
    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return JulgForneHist
     */
    public function find($column, $conditions)
    {
        return Julgfornehist::where($column, $conditions)->get();
    }

    /**
     * Cria um novo registro no hist�rico de fornecedor.
     *
     * @param array $dados
     * @return JulgForneHist
     */
    public function create(array $dados)
    {
        return JulgForneHist::create($dados);
    }

    /**
     * Atualiza um registro de hist�rico de fornecedor existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return JulgForneHist
     */
    public function update($id, array $dados)
    {
        $julgForneHist = JulgForneHist::findOrFail($id);
        $julgForneHist->update($dados);
        return $julgForneHist;
    }

    /**
     * Exclui registros da tabela JulgForneHist com base na condi��o fornecida.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida.
     */
    public function delete($column, $conditions)
    {
        $julgForneHist = JulgForneHist::where($column, $conditions);
        return $julgForneHist->delete();
    }

    public function deleteIn($column, array $conditions)
    {
        $julgForneHist = JulgForneHist::whereIn($column, $conditions);
        return $julgForneHist->delete();
    }
}
