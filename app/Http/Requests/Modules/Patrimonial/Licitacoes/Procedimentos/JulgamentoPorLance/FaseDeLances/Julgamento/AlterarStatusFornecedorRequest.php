<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\Julgamento;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AlterarStatusFornecedorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipoJulg' => 'required|numeric',
            'itemCodigo' => 'required|numeric',
            'fornecedorCodigo' => 'required|numeric',
            'fornecedorCategoria' => 'required|numeric',
            'fornecedorMotivo' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'tipoJulg.required' => StringHelper::toUtf8('O n�mero do lote � obrigat�rio.'),
            'tipoJulg.numeric' => StringHelper::toUtf8('O n�mero do lote deve ser num�rico.'),

            'itemCodigo.required' => StringHelper::toUtf8('O c�digo do item � obrigat�rio.'),
            'itemCodigo.numeric' => StringHelper::toUtf8('O c�digo do item deve ser um valor num�rico.'),

            'fornecedorCodigo.required' => StringHelper::toUtf8('O c�digo do fornecedor � obrigat�rio.'),
            'fornecedorCodigo.numeric' => StringHelper::toUtf8('O c�digo do fornecedor deve ser um valor num�rico.'),

            'fornecedorCategoria.required' => StringHelper::toUtf8('A categoria do fornecedor � obrigat�rio.'),
            'fornecedorCategoria.numeric' => StringHelper::toUtf8('A categoria do fornecedor deve ser um valor num�rico.'),

            'fornecedorMotivo.required' => StringHelper::toUtf8('O motivo � obrigat�rio para alterar o status do fornecedor.'),
            'fornecedorMotivo.string' => StringHelper::toUtf8('O motivo deve ser um texto v�lido.'),
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => StringHelper::toUtf8('Erro de valida��o do status do fornecedor.'),
            'errors' => $validator->errors(),
        ], 422));
    }
}