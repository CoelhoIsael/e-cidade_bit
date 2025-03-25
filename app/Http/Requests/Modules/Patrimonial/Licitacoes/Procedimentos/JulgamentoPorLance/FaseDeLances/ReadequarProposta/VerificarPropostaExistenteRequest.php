<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\ReadequarProposta;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class VerificarPropostaExistenteRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'itens' => 'required|array',
            'itens.*.orcamitem' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'itens.required' => StringHelper::toUtf8('O campo itens � obrigat�rio.'),
            'itens.array' => StringHelper::toUtf8('O campo itens deve ser um array.'),

            'itens.*.orcamitem.required' => StringHelper::toUtf8('O campo "orcamitem" � obrigat�rio e n�o foi enviado na opera��o de salvar a proposta.'),
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