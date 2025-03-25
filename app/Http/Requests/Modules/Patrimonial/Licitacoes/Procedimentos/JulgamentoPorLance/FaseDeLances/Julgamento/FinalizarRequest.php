<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\Julgamento;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class FinalizarRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'tipoJulg' => 'required|numeric',
            'licitacaoCodigo' => 'required|numeric|min:1',
            'numeroLoteCodigo' => 'required_if:tipoJulg,3|numeric',
            'orcamentoItemCodigo' => 'required_if:tipoJulg,1|numeric',
            'licitacaoItemCodigo' => 'required_if:tipoJulg,1|numeric|min:1'
        ];
    }

    public function messages()
    {
        return [
            'tipoJulg.required' => StringHelper::toUtf8('O n�mero do lote � obrigat�rio.'),
            'tipoJulg.numeric' => StringHelper::toUtf8('O n�mero do lote deve ser num�rico.'),

            'licitacaoCodigo.required' => StringHelper::toUtf8('O c�digo da licita��o � obrigat�rio.'),
            'licitacaoCodigo.numeric' => StringHelper::toUtf8('O c�digo da licita��o deve ser num�rico.'),
            'licitacaoCodigo.min' => StringHelper::toUtf8('O c�digo da licita��o deve ser maior ou igual a um.'),

            'numeroLoteCodigo.required_if' => StringHelper::toUtf8('O n�mero do lote � obrigat�rio.'),
            'numeroLoteCodigo.numeric' => StringHelper::toUtf8('O n�mero do lote deve ser num�rico.'),

            'orcamentoItemCodigo.required_if' => StringHelper::toUtf8('O c�digo do item de or�amento � obrigat�rio.'),
            'orcamentoItemCodigo.numeric' => StringHelper::toUtf8('O c�digo do item de or�amento deve ser num�rico.'),

            'licitacaoItemCodigo.required_if' => StringHelper::toUtf8('O c�digo do item da licita��o � obrigat�rio.'),
            'licitacaoItemCodigo.numeric' => StringHelper::toUtf8('O c�digo do item da licita��o deve ser num�rico.'),
            'licitacaoItemCodigo.min' => StringHelper::toUtf8('O c�digo do item da licita��o deve ser maior ou igual a um.'),
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
