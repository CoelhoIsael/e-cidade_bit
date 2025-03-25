<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\Julgamento;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class LiberarMicroEmpresasRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'codigoLicitacao' => 'required|numeric',
            'codigoLicitacaoItem' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'codigoLicitacao.required' => StringHelper::toUtf8('O c�digo da licita��o � obrigat�rio.'),
            'codigoLicitacao.numeric' => StringHelper::toUtf8('O c�digo da licita��o deve ser um valor num�rico.'),
            'codigoLicitacaoItem.required' => StringHelper::toUtf8('O c�digo da licita��o item � obrigat�rio.'),
            'codigoLicitacaoItem.numeric' => StringHelper::toUtf8('O c�digo da licita��o item deve ser um valor num�rico.'),
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