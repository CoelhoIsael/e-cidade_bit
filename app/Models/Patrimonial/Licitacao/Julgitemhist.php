<?php

namespace App\Models\Patrimonial\Licitacao;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Julgitemhist extends Model
{
    use HasFactory;

    /**
     * O nome da tabela.
     *
     * @var string
     */
    protected $table = 'licitacao.julgitemhist';

    /**
     * A chave prim�ria da tabela.
     *
     * @var string
     */
    protected $primaryKey = 'l33_codigo';

    /**
     * Indica se o ID � auto-incrementado.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * O tipo da chave prim�ria.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indica se os campos de timestamp s�o gerenciados automaticamente.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * Os atributos que podem ser atribu�dos em massa.
     *
     * @var array
     */
    protected $fillable = [
        'l33_julgitem',
        'l33_julgitemstatus',
        'l33_motivo',
    ];

    /**
     * Os nomes dos campos de timestamps.
     *
     * @var string
     */
    const CREATED_AT = 'l33_created_at';
    const UPDATED_AT = 'l33_updated_at';

    /**
     * Define a rela��o com o modelo Julgitem.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function julgitem()
    {
        return $this->belongsTo(Julgitem::class, 'l33_julgitem', 'l30_codigo');
    }

    /**
     * Define a rela��o com o modelo Julgitemstatus.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function julgitemstatus()
    {
        return $this->belongsTo(Julgitemstatus::class, 'l33_julgitemstatus', 'l31_codigo');
    }
}
