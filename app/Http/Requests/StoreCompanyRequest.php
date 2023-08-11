<?php

namespace App\Http\Requests;
use App\Http\Requests\Request;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreCompanyRequest extends FormRequest
{
    public function rules()
    {
        return [
            'razao_social'    => [
                'unique:companies',
                'required',
            ],
            'name'   => [
                'required',
            ],
            'atividade_principal'    => [
                'required',
            ],
            'cnae'    => [
                'required',
            ],
            'endereco'    => [
                'required',
            ],
            'bairro'    => [
                'required',
            ],
            'cep'    => [
                'required',
                'regex:/^[0-9]{2}.[0-9]{3}-[0-9]{3}$/'
            ],
            'cidade'    => [
                'required',
            ],
            'telefone'    => [
                'required',
                'regex:/^[(][0-9]{2}[)][0-9]{4}-[0-9]{4}$|[(][0-9]{2}[)][0-9]{4}-[0-9]{5}$/'
            ],
            'cnpj'    => [
                'required',
                'regex:/^[0-9]{2}\.?[0-9]{3}\.?[0-9]{3}\/?[0-9]{4}\-?[0-9]{2}$/'
            ],
            'id_manager'    => [
                'nullable',
            ],
            'tipo'    => [
                'nullable',
            ],
            'ativo'    => [
                'nullable',
            ],
        ];
    }

    public function authorize()
    {
        return true;
        return Gate::allows('user_access');
    }
}