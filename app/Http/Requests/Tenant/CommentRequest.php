<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
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
            case "delete":
                return $this->delete();
            default:
                return [];
        }
    }

    private function list()
    {
        return [
            'type' => 'sometimes|string',
        ];
    }

    private function store()
    {
        return [
			'uuid' => 'sometimes|exists:comments,uuid',
			"type" => "required",
			"commentable_id" => "required",
			"body" => "required",
		];
    }

    private function delete()
    {
        return [
            'uuid' => 'required|array',
			'uuid.*' => 'exists:comments,uuid'
        ];
    }
}
