<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class StoreResponsibilityRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => [
                'required',
            ],
            'id_company' => [
                'required',
            ],
            'documents' => [
                'nullable'
            ],
        ];
    }

    public function authorize()
    {
        return true;
        return Gate::allows('user_access');
    }
}