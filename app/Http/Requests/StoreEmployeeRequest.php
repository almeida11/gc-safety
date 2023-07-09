<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreEmployeeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'    => [
                'required',
            ],
            'cpf'   => [
                'required',
            ],
            'admission'    => [
                'date',
            ],
            'responsibility'    => [
                'required',
            ],
            'sector'    => [
                'required',
            ],
            'id_company'    => [
                'nullable',
            ],
            'active'    => [
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