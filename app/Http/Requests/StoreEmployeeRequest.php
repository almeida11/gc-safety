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
                'regex:/^[0-9]{3}.[0-9]{3}.[0-9]{3}-[0-9]{2}$/'
            ],
            'admission'    => [
                'date',
            ],
            'id_responsibility'    => [
                'required',
            ],
            'id_sector'    => [
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