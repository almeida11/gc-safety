<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class UpdateEmployeeRequest extends FormRequest
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
                'required',
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