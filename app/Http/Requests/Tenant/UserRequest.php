<?php

namespace App\Http\Requests\Tenant;

use App\Rules\ValidateBase64;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
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
            case "list":
                return $this->list();
            case "store":
                return $this->store();
            case "update":
                return $this->update();
            case "delete":
                return $this->delete();
            default:
                return [];
        }
    }

    private function store()
    {
        return [
			"fname" => "required", 
			"lname" => "required", 
			"email" => "required|email|unique:users,email", 
			"phone" => "required", 
			"username" => "required|unique:users,username", 
			"address" => "required", 
			"status" => "required|in:0,1",
            "term_start_date" => "required",
            "term_end_date" => "required",
			"role_id" => "required|exists:roles,uuid",
			"position_board_id" => "required|exists:position_boards,uuid",
            "avatar_image" => [new ValidateBase64(), 'nullable']
		];
    }

    private function update()
    {
        return [
            "uuid" => "required|exists:users,uuid",
			"fname" => "required", 
			"lname" => "required", 
			"email" => "required|email|unique:users,email,{$this->uuid},uuid",
			"phone" => "required",
			"username" => "required|unique:users,email,{$this->uuid},uuid",
			"address" => "required",
			"status" => "required|in:0,1",
            "term_start_date" => "required",
            "term_end_date" => "required",
			"role_id" => "required|exists:roles,uuid",
			"position_board_id" => "required|exists:position_boards,uuid"
		];
    }

    private function list()
    {
        return [
            "search" => "nullable|string",
            "uuid" => "nullable|exists:users,uuid",
			"status" => "nullable|in:0,1",
            "role_id" => "nullable|array|exists:roles,uuid",
			"position_board_id" => "nullable|array|exists:position_boards,uuid",
        ];
    }

    private function delete()
    {
        return [
            'uuid' => 'required|array',
			'uuid.*' => 'exists:users,uuid'
        ];
    }
}
