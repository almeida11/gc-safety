<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCompanyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'razao_social'    => [
                'string',
                'unique:companies',
            ],
            'nome_fantasia'   => [
                'string',
                'required',
            ],
            'atividade_principal'    => [
                'string',
                'required',
            ],
            'cnae'    => [
                'string',
                'required',
            ],
            'endereco'    => [
                'string',
                'required',
            ],
            'bairro'    => [
                'string',
                'required',
            ],
            'cep'    => [
                'string',
                'required',
            ],
            'cidade'    => [
                'string',
                'required',
            ],
            'telefone'    => [
                'string',
                'required',
            ],
            'cnpj'    => [
                'string',
                'required',
            ],
            'id_manager'    => [
                'string',
                'required',
            ],
        ];
    }

    public function authorize()
    {
        return true;
        return Gate::allows('user_access');
    }
}