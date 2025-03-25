<?php

namespace App\Models\Patrimonial\Licitacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Julgfornestatus extends Model
{
    use HasFactory;

    // Definindo o nome da tabela no banco de dados
    protected $table = 'licitacao.julgfornestatus';

    // Definindo a chave prim�ria da tabela
    protected $primaryKey = 'l35_codigo';

    // Indicando que a chave prim�ria � auto-incrementada
    public $incrementing = true;

    // Definindo os tipos de dados da chave prim�ria
    protected $keyType = 'int';

    // Informando ao Eloquent que a tabela n�o utiliza timestamps padr�o
    public $timestamps = false;

    // Definindo os campos atribu�veis em massa
    protected $fillable = [
        'l35_label',
        'l35_desc',
    ];

    // Relacionamento com a tabela julgforne
    public function julgfornes()
    {
        return $this->hasMany(Julgforne::class, 'l34_julgfornestatus', 'l35_codigo');
    }
}
