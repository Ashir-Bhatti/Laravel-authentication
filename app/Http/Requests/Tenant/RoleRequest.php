<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends FormRequest
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
        $uuid = $this->input('uuid');
        return [
            "name" => [
                'required',
                'unique:roles,name,' . ($uuid ?? 'NULL') . ',uuid'
            ],
            "slug" => "nullable",
            "permission_ids.*" => "nullable|exists:permissions,uuid",
        ];
    }
}
