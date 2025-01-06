<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class TagRequest extends FormRequest
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
            case "sync-tags":
                return $this->syncTags();
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
			'uuid' => 'sometimes|exists:tags,uuid',
			"name" => "required|max:255",
			"color" => "sometimes|nullable|string",
		];
    }

    private function syncTags()
    {
        return [
            'tag_uuid' => 'nullable|array',
            'tag_uuid.*' => 'exists:tags,uuid',
            'task_uuid' => 'required|array',
            'task_uuid.*' => 'exists:tasks,uuid',
            'type' => 'required|string',
        ];
    }

    private function delete()
    {
        return [
			"uuid" => "required|array",
			'uuid.*' => 'exists:tags,uuid'
		];
    }
}
