<?php

namespace App\Traits;

use Exception;

trait ValidatesControllerProperties
{
    /**
     * Valida as propriedades obrigat�rias do controlador.
     *
     * Este m�todo verifica se a propriedade 'model' � um modelo Eloquent v�lido e se
     * a propriedade 'columns' � um array n�o vazio, onde cada coluna cont�m tanto
     * as chaves 'name' quanto 'label', que n�o podem estar vazias. Se qualquer uma
     * dessas condi��es falhar, uma exce��o � lan�ada com uma mensagem apropriada.
     *
     * @throws Exception Se 'model' n�o for um modelo Eloquent v�lido ou 'columns' estiver definido incorretamente.
     */
    protected function validateRequiredProperties()
    {
        if (!isset($this->columns) || !is_array($this->columns) || empty($this->columns)) {
            throw new Exception("A propriedade 'columns' deve ser um array n�o vazio.");
        }

        foreach ($this->columns as $column) {
            // if (!isset($column['name']) || empty($column['name']) || !isset($column['label']) || empty($column['label'])) {
            if (!isset($column['name']) || empty($column['name'])) {
                throw new Exception("Cada coluna deve conter 'name' e 'label', e ambos n�o podem ser vazios.");
            }
        }
    }
}
