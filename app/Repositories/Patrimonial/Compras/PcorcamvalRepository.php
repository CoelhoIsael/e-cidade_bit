<?php

namespace App\Repositories\Patrimonial\Compras;

use App\Models\Patrimonial\Compras\Pcorcamval;
use Illuminate\Support\Facades\DB;

class PcorcamvalRepository
{
    private Pcorcamval $model;

    /**
     * Construtor da classe PcorcamvalRepository.
     * Inicializa o modelo Pcorcamval que ser� utilizado nas opera��es de banco de dados.
     */
    public function __construct()
    {
        $this->model = new Pcorcamval();
    }

    /**
     * Encontra registros no banco de dados com base em uma coluna e condi��o fornecidas.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de pesquisa.
     * @param mixed $conditions Valor da condi��o para a busca.
     * @return \Illuminate\Database\Eloquent\Collection Conjunto de resultados encontrados.
     */
    public function find($column, $conditions)
    {
        return $this->model->where($column, $conditions)->get();
    }

    /**
     * Insere dados na tabela Pcorcamval.
     * 
     * @param array $dados Dados a serem inseridos na tabela.
     * @return bool Retorna verdadeiro em caso de sucesso ou falso em caso de falha.
     */
    public function insertData($dados)
    {
        return $this->model->insert($dados);
    }

    /**
     * Cria um novo registro na tabela Pcorcamval.
     * 
     * @param array $dados Dados a serem inseridos no novo registro.
     * @return Pcorcamval Retorna a inst�ncia do modelo Pcorcamval criado.
     */
    public function insert($dados): Pcorcamval
    {
        return $this->model->create($dados);
    }

    /**
     * Atualiza um registro existente na tabela Pcorcamval.
     * 
     * @param int $pc23_orcamitem Identificador do item or�ament�rio a ser atualizado.
     * @param array $dados Dados a serem atualizados no registro.
     * @return bool Retorna verdadeiro se a atualiza��o for bem-sucedida, falso caso contr�rio.
     */
    public function update(int $pc23_orcamitem, array $dados): bool
    {
        return DB::table('pcorcamval')->where('pc23_orcamitem',$pc23_orcamitem)->where('pc23_orcamforne',$dados->pc23_orcamforne)->update($dados);
    }

    /**
     * Exclui um registro da tabela Pcorcamval.
     * 
     * @param int $pc23_orcamitem Identificador do item or�ament�rio a ser exclu�do.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida, falso caso contr�rio.
     */
    public function excluir(int $pc23_orcamitem): bool
    {
        $sql = "DELETE FROM pcorcamval WHERE pc23_orcamitem IN ($pc23_orcamitem)";
        return DB::statement($sql);
    }

    /**
     * Exclui registros da tabela Pcorcamval com base na coluna e condi��o fornecidas.
     * 
     * Este m�todo permite excluir um ou mais registros da tabela Pcorcamval, 
     * com base em uma condi��o especificada para uma coluna. Ele utiliza o m�todo 
     * `where` do Eloquent para filtrar os registros a serem exclu�dos e, em seguida, 
     * executa a exclus�o.
     * 
     * @param string $column Nome da coluna para aplicar a condi��o de exclus�o.
     * @param mixed $conditions Valor da condi��o para identificar os registros a serem exclu�dos.
     * @return bool Retorna verdadeiro se a exclus�o for bem-sucedida, falso caso contr�rio.
     */
    public function delete($column, $conditions)
    {
        $pcorcamval = $this->model->where($column, $conditions);
        return $pcorcamval->delete();
    }
}
