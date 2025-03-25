<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\Julgamento;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ReverterLanceRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipoJulg' => 'required|numeric',
            'numeroLoteCodigo' => 'required_if:tipoJulg,3|numeric',
            'orcamentoItemCodigo' => 'required_if:tipoJulg,1|numeric',
        ];
    }

    public function messages()
    {
        return [
            'tipoJulg.required' => StringHelper::toUtf8('O n�mero do lote � obrigat�rio.'),
            'tipoJulg.numeric' => StringHelper::toUtf8('O n�mero do lote deve ser num�rico.'),
            
            'numeroLoteCodigo.required_if' => StringHelper::toUtf8('O n�mero do lote � obrigat�rio.'),
            'numeroLoteCodigo.numeric' => StringHelper::toUtf8('O n�mero do lote deve ser num�rico.'),

            'orcamentoItemCodigo.required_if' => StringHelper::toUtf8('O c�digo do item de or�amento � obrigat�rio.'),
            'orcamentoItemCodigo.numeric' => StringHelper::toUtf8('O c�digo do item de or�amento deve ser num�rico.')
        ];
    }
    
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json([
            'message' => StringHelper::toUtf8('Erro de valida��o para registrar lance.'),
            'errors' => $validator->errors(),
        ], 422));
    }
}
