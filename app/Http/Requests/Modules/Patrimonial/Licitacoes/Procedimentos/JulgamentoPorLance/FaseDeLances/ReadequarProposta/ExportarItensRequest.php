<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\ReadequarProposta;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ExportarItensRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'licitacao' => 'required|string',
            'fornecedor' => 'required|string',
            'lote' => 'required|string',
            'descrforne' => 'required|string',
            'cnpj' => 'required|string',
        ];
    }

    public function messages()
    {
        return [
            'licitacao.required' => StringHelper::toUtf8('O campo de licita��o � obrigat�rio.'),
            'licitacao.string' => StringHelper::toUtf8('O campo de licita��o deve ser um texto v�lida.'),
            
            'fornecedor.required' => StringHelper::toUtf8('O campo fornecedor � obrigat�rio.'),
            'fornecedor.string' => StringHelper::toUtf8('O campo fornecedor deve ser uma texto v�lida.'),
            
            'lote.required' => StringHelper::toUtf8('O campo lote � obrigat�rio.'),
            'lote.string' => StringHelper::toUtf8('O campo lote deve ser uma texto v�lida.'),
            
            'descrforne.required' => StringHelper::toUtf8('O campo descrforne � obrigat�rio.'),
            'descrforne.string' => StringHelper::toUtf8('O campo descrforne deve ser uma texto v�lida.'),
            
            'cnpj.required' => StringHelper::toUtf8('O campo cnpj � obrigat�rio.'),
            'cnpj.string' => StringHelper::toUtf8('O campo cnpj deve ser uma texto v�lida.'),
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