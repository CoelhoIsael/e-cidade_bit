<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\Julgamento;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AlterarStatusItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipoJulg' => 'required|string',
            'ids' => 'required|string',
            'categorias' => 'required|string',
            'motivo' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'tipoJulg.required' => StringHelper::toUtf8('O tipo do julgamento obrigat�rio.'),
            'tipoJulg.string' => StringHelper::toUtf8('O c�digo tipo do julgamento deve ser um texto v�lido.'),

            'ids.required' => StringHelper::toUtf8('O c�digo sequencial do item � obrigat�rio.'),
            'ids.numeric' => StringHelper::toUtf8('O c�digo sequencial do item deve ser um valor num�rico.'),

            'categorias.required' => StringHelper::toUtf8('A categoria � obrigat�rio para alterar o status do item.'),
            'categorias.string' => StringHelper::toUtf8('A categoria deve ser um texto v�lido.'),

            'motivo.required' => StringHelper::toUtf8('O motivo � obrigat�rio para alterar o status do item.'),
            'motivo.string' => StringHelper::toUtf8('O motivo deve ser um texto v�lido.'),
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