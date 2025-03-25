<?php

namespace App\Http\Requests\Modules\Patrimonial\Licitacoes\Procedimentos\JulgamentoPorLance\FaseDeLances\Julgamento;

use App\Helpers\StringHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class ParametrosLancesRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'l13_precoref' => 'required|numeric',
            'l13_difminlance' => 'required|numeric',
            'l13_clapercent' => 'nullable',
            'l13_avisodeacoestabela' => 'required|numeric',
        ];
    }

    public function messages()
    {
        return [
            'l13_precoref.required' => StringHelper::toUtf8('O precoref � obrigat�rio.'),
            'l13_precoref.numeric' => StringHelper::toUtf8('O precoref deve ser num�rico.'),

            'l13_difminlance.required' => StringHelper::toUtf8('O difminlance � obrigat�rio.'),
            'l13_difminlance.numeric' => StringHelper::toUtf8('O difminlance deve ser num�rico.'),

            'l13_avisodeacoestabela.required' => StringHelper::toUtf8('O avisos � obrigat�rio.'),
            'l13_avisodeacoestabela.numeric' => StringHelper::toUtf8('O avisos deve ser num�rico.'),
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
