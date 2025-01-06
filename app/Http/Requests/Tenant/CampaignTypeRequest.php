<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class CampaignTypeRequest extends FormRequest
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

    private function list()
    {
        return [
            "search" => "nullable|string",
            "uuid" => "nullable|exists:campaign_types,uuid",
            "status" => "nullable|in:0,1",
        ];
    }

    private function store()
    {
        return [
            'uuid' => 'sometimes|exists:campaign_types,uuid',
            'title' => 'required|max:255',
        ];
    }

    private function delete()
    {
        return [
            'uuid' => 'required|array',
            'uuid.*' => 'exists:campaign_types,uuid'
        ];
    }
}
