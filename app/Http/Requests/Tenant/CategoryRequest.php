<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        switch (last(request()->segments())) {
            case "list":
                return $this->list();
            case "store":
                return $this->store();
            case "delete":
                return $this->delete();
            default:
                return [];
        }
    }

    public function list()
    {
        return [
            "uuid" => "sometimes|exists:categories,uuid",
        ];
    }

    public function store()
    {
        return [
            "uuid" => "sometimes|exists:categories,uuid",
            "title" => "required|max:255",
        ];
    }

    public function delete()
    {
        return [
            "uuid" => "required|array",
            "uuid.*" => "exists:categories,uuid",
        ];
    }
}
