<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class UpdateUserRequest extends FormRequest
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
                'unique:users,email,' . request()->route('user')->id,
            ],
            'active'    => [
                'boolean',
                'required',
            ],
            'type'    => [
                'string',
                'required',
            ],
            'company'    => [
                'nullable',
            ],
            'password'    => [
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