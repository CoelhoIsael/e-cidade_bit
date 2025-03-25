<?php

namespace App\Models\Patrimonial\Licitacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Julgitemstatus extends Model
{
    use HasFactory;

    // Definindo o nome da tabela no banco de dados
    protected $table = 'licitacao.julgitemstatus';

    // Definindo a chave prim�ria da tabela
    protected $primaryKey = 'l31_codigo';

    // Indicando que a chave prim�ria � auto-incrementada
    public $incrementing = true;

    // Definindo os tipos de dados da chave prim�ria
    protected $keyType = 'int';

    // Informando ao Eloquent que a tabela n�o utiliza timestamps padr�o
    public $timestamps = false;

    // Definindo os campos atribu�veis em massa
    protected $fillable = [
        'l31_label',
        'l31_desc',
    ];

    // Relacionamento com a tabela julgitem
    public function julgitems()
    {
        return $this->hasMany(Julgitem::class, 'l30_julgitemstatus', 'l31_codigo');
    }
}
