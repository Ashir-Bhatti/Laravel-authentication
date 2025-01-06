<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ProfileRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        $route = last(request()->segments());
        switch ($route) {
            case "update":
                return $this->update();
            case "change-password":
                return $this->password();
            case "email-verify":
                return $this->emailVerify();
            case "reset-email":
                return $this->resetEmail();
            default:
                return [];
        }
    }

    private function update()
    {
        return [
			"fname" => "required", 
			"lname" => "required", 
			"phone" => "required",
			"address" => "required",
		];
    }

    private function password()
    {
        return [
            'current_password' => 'required|string|min:6|current_password',
			'password' => 'required|string|min:6|confirmed|different:current_password'
		];
    }

    private function emailVerify()
    {
        return [
            'email' => 'required|unique:users,email',
			'url' => 'required|url'
		];
    }

    private function resetEmail()
    {
        return [
            'email' => 'required|unique:users,email',
			'token' => 'required'
		];
    }

    public function messages()
    {
        return [
            'email.unique' => 'The provided email address is already registered.',
            'email.required' => 'The email field is required.',
            'url.required' => 'The URL field is required.',
            'url.url' => 'The URL must be a valid URL.',
        ];
    }
}
