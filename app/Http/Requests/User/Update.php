<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class Update extends FormRequest
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
      'role_id' => 'required',
      'username' => 'required|string|min:1|max:100|unique:users,username,'.$this->user,
      'email' => 'required|email:rfc,dns|min:1|max:100|unique:users,email,'.$this->user,
      'first_name' => 'required|string',
      'last_name' => 'required|string'
    ];
  }
}
