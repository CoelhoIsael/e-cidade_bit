<?php

namespace App\Repositories\Patrimonial\Licitacao;

use App\Models\Patrimonial\Licitacao\Julgforne;

class JulgForneRepository
{
    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     *
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return Julgforne
     */
    public function find($column, $conditions)
    {
        return Julgforne::where($column, $conditions)->get();
    }

    /**
     * Encontra um registro pelo ID ou gera uma exce��o se n�o encontrado.
     * 
     * @param int $id Identificador �nico do registro.
     * @return Julgforne Retorna o registro encontrado.
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException Se o registro n�o for encontrado.
     */
    public function findId($id)
    {
        return Julgforne::findOrFail($id);
    }

    /**
     * Cria um novo registro de fornecedor no banco de dados.
     *
     * @param array $dados
     * @return Julgforne
     */
    public function create(array $dados)
    {
        return Julgforne::create($dados);
    }

    /**
     * Atualiza os dados de um fornecedor existente pelo ID.
     *
     * @param int $id
     * @param array $dados
     * @return Julgforne
     */
    public function update($id, array $dados)
    {
        $julgForne = Julgforne::findOrFail($id);
        $julgForne->update($dados);
        return $julgForne;
    }

    /**
     * Exclui registros da tabela Julgforne com base na condi��o fornecida.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida.
     */
    public function delete($column, $conditions)
    {
        $julgForne = Julgforne::where($column, $conditions);
        return $julgForne->delete();
    }

    /**
     * Atualiza o status dos fornecedores com base no c�digo do item or�ament�rio.
     * 
     * @param int $pcorcamitem C�digo do item or�ament�rio para identificar os registros.
     * @param array $dados Dados a serem atualizados nos registros dos fornecedores.
     * @return \Illuminate\Database\Eloquent\Collection Retorna os registros atualizados.
     */
    public function updateStatusSuppliers($pcorcamitem, array $dados)
    {
        $julgForne = Julgforne::where('l34_orcamitem', $pcorcamitem);
        $julgForne->update($dados);
        return $julgForne->get();
    }

    public function updateStatusSuppliersFromBestBidToNoBid($pcorcamitem, array $dados)
    {
        $julgForne = Julgforne::where('l34_orcamitem', $pcorcamitem);
        $julgForne->where('l34_julgfornestatus', 2);
        $julgForne->update($dados);
        return $julgForne->get();
    }

    public function updateStatusSuppliersLot($numerolote, array $dados)
    {
        $julgForne = Julgforne::where('l34_numerolote', $numerolote);
        $julgForne->update($dados);
        return $julgForne->get();
    }

    public function updateStatusSuppliersFromBestBidToNoBidLot($numerolote, array $dados)
    {
        $julgForne = Julgforne::where('l34_numerolote', $numerolote);
        $julgForne->where('l34_julgfornestatus', 2);
        $julgForne->update($dados);
        return $julgForne->get();
    }

    /**
     * Encontra todos os fornecedores relacionados a um item or�ament�rio.
     * 
     * @param int $pcorcamitem C�digo do item or�ament�rio para a busca.
     * @return \Illuminate\Database\Eloquent\Collection Retorna os fornecedores encontrados, ordenados por c�digo.
     */
    public function findItemSuppliers($pcorcamitem)
    {
        return Julgforne::where('l34_orcamitem', $pcorcamitem)
            ->orderBy('l34_codigo', 'desc')
            ->get();
    }

    /**
     * Encontra todos os fornecedores relacionados a um item or�ament�rio.
     * 
     * @param int $pcorcamitem C�digo do item or�ament�rio para a busca.
     * @return \Illuminate\Database\Eloquent\Collection Retorna os fornecedores encontrados, ordenados por c�digo.
     */
    public function findLotSuppliers($numerolote)
    {
        return Julgforne::where('l34_numerolote', $numerolote)
            ->orderBy('l34_codigo', 'desc')
            ->get();
    }
    
    /**
     * Encontra um fornecedor espec�fico relacionado a um item or�ament�rio.
     * 
     * @param int $pcorcamforne C�digo do fornecedor para a busca.
     * @param int $pcorcamitem C�digo do item or�ament�rio para a busca.
     * @return Julgforne|null Retorna o fornecedor encontrado ou null se n�o encontrado.
     */
    public function findItemSupplier($pcorcamforne, $pcorcamitem)
    {
        return Julgforne::where('l34_orcamforne', $pcorcamforne)
            ->where('l34_orcamitem', $pcorcamitem)
            ->orderBy('l34_codigo', 'desc')
            ->first();
    }

    /**
     * Encontra um fornecedor espec�fico relacionado a um lote.
     * 
     * @param int $pcorcamforne C�digo do fornecedor para a busca.
     * @param int $numerolote C�digo do lote para a busca.
     * @return Julgforne|null Retorna o fornecedor encontrado ou null se n�o encontrado.
     */
    public function findLotSupplier($pcorcamforne, $numerolote)
    {
        return Julgforne::where('l34_orcamforne', $pcorcamforne)
            ->where('l34_numerolote', $numerolote)
            ->orderBy('l34_codigo', 'desc')
            ->first();
    }
}
