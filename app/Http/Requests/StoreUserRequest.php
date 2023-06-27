<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class StoreUserRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name'    => [
                'string',
                'required',
            ],
            'email'   => [
                'required',
                'unique:users,email'
            ],
            'type'    => [
                'string',
                'required',
            ],
            'company'    => [
                'required',
            ],
            'password'    => [
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