<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class Store extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'position_id' => 'required',
            'role_id' => 'required',
            'username' => 'required|string|min:1|max:100|unique:users',
            'email' => 'required|email:rfc,dns|min:1|max:100|unique:users',
            'password' => 'required|min:8',
            'first_name' => 'required',
            'last_name' => 'required',
        ];
    }
}
