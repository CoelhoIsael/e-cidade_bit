<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julglance;

class JulgLanceRepository
{
    /**
     * Retorna todos os registros da tabela Julglance.
     * 
     * @return \Illuminate\Database\Eloquent\Collection Retorna todos os registros encontrados.
     */
    public function all()
    {
        return Julglance::all();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return Julglance
     */
    public function find($column, $conditions)
    {
        return Julglance::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return Julglance Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return Julglance::findOrFail($id);
    }

    /**
     * Cria um novo lance no banco de dados.
     *
     * @param array $dados
     * @return Julglance
     */
    public function create(array $dados)
    {
        return Julglance::create($dados);
    }

    /**
     * Atualiza os dados de um lance existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return Julglance
     */
    public function update($id, array $dados)
    {
        $julglance = Julglance::findOrFail($id);
        $julglance->update($dados);
        return $julglance;
    }

    /**
     * Exclui registros da tabela Julglance com base na condi��o fornecida.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida.
     */
    public function delete($column, $conditions)
    {
        $julglance = Julglance::where($column, $conditions);
        return $julglance->delete();
    }

    /**
     * Remove um lance pelo item de or�amento.
     *
     * @param string $codigoItemOrcamento
     * @return Julglance|null
     */
    public function deleteBidItem($codigoItemOrcamento)
    {
        return Julglance::where('l32_julgitem', $codigoItemOrcamento)->delete();
    }

    /**
     * Busca o �ltimo lance registrado para um item de or�amento.
     *
     * @param string $codigoItemOrcamento
     * @return Julglance|null
     */
    public function findBidItem($codigoItemOrcamento)
    {
        return Julglance::where('l32_julgitem', $codigoItemOrcamento)->get();
    }

    /**
     * Busca o �ltimo lance registrado para um item de or�amento.
     *
     * @param string $codigoItemOrcamento
     * @return Julglance|null
     */
    public function findLastBid($codigoItemOrcamento)
    {
        return Julglance::where('l32_julgitem', $codigoItemOrcamento)
            ->orderBy('l32_codigo', 'desc')
            ->first();
    }

    /**
     * Busca o �ltimo lance registrado para um item de or�amento.
     *
     * @param string $codigoItemOrcamento
     * @return Julglance|null
     */
    public function findLastBidNotNull($codigoItemOrcamento)
    {
        return Julglance::where('l32_julgitem', $codigoItemOrcamento)
            ->whereNotNull('l32_lance')
            ->orderBy('l32_codigo', 'desc')
            ->first();
    }

    /**
     * Busca o �ltimo lance de um fornecedor espec�fico para um item de or�amento.
     *
     * @param string $codigoFornecedor
     * @param string $codigoItemOrcamento
     * @return Julglance|null
     */
    public function findLastBidSupplier($codigoFornecedor, $codigoItemOrcamento)
    {
        return Julglance::where('l32_julgitem', $codigoItemOrcamento)
            ->where('l32_julgforne', $codigoFornecedor)
            ->orderBy('l32_codigo', 'desc')
            ->first();
    }

    /**
     * Busca o �ltimo lance de um fornecedor espec�fico para um item de or�amento.
     *
     * @param string $codigoFornecedor
     * @param string $codigoItemOrcamento
     * @return Julglance|null
     */
    public function findLastBidSupplierNotNull($codigoFornecedor, $codigoItemOrcamento)
    {
        return Julglance::where('l32_julgitem', $codigoItemOrcamento)
            ->where('l32_julgforne', $codigoFornecedor)
            ->whereNotNull('l32_lance')
            ->orderBy('l32_codigo', 'desc')
            ->first();
    }
}
