<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julgitem;

class JulgItemRepository
{
    /**
     * Retorna todos os registros da tabela JulgItem.
     * 
     * @return \Illuminate\Database\Eloquent\Collection Retorna todos os registros encontrados.
     */
    public function all()
    {
        return JulgItem::all();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return JulgItem
     */
    public function find($column, $conditions)
    {
        return JulgItem::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return JulgItem Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return JulgItem::findOrFail($id);
    }

    /**
     * Cria um novo item no banco de dados.
     *
     * @param array $dados
     * @return JulgItem
     */
    public function create(array $dados)
    {
        return JulgItem::create($dados);
    }

    /**
     * Atualiza os dados de um item existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return JulgItem
     */
    public function update($id, array $dados)
    {
        $julgItem = JulgItem::findOrFail($id);
        $julgItem->update($dados);
        return $julgItem;
    }

    /**
     * Exclui registros da tabela JulgItem com base na condi��o fornecida.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida.
     */
    public function delete($column, $conditions)
    {
        $julgItem = JulgItem::where($column, $conditions);
        return $julgItem->delete();
    }

    /**
     * Busca um item de or�amento espec�fico pelo c�digo do item.
     *
     * @param string $codigoItemOrcamento
     * @return JulgItem|null
     */
    public function findItemBudget($codigoItemOrcamento)
    {
        return JulgItem::where('l30_orcamitem', $codigoItemOrcamento)
            ->orderBy('l30_codigo', 'desc')
            ->first();
    }

    /**
     * Busca um lote de or�amento espec�fico pelo c�digo do item.
     *
     * @param string $numeroloteCodigo
     * @return JulgItem|null
     */
    public function findLotBudget($numeroloteCodigo)
    {
        return JulgItem::where('l30_numerolote', $numeroloteCodigo)
            ->orderBy('l30_codigo', 'desc')
            ->first();
    }
}
