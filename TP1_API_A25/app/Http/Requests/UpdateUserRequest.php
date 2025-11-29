<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $id = $this->route('id');
        return [
            'login'      => "required|string|max:50|unique:users,login,{$id}",
            'password' => 'nullable|string|min:6|max:255',
            'email'      => "required|email|max:50|unique:users,email,{$id}",
            'first_name' => 'required|string|max:50',
            'last_name'  => 'required|string|max:50',
        ];
    }
}
