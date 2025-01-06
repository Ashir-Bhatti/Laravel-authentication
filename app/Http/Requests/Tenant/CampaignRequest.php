<?php

namespace App\Http\Requests\Tenant;

use App\Rules\ValidateBase64;
use Illuminate\Foundation\Http\FormRequest;

class CampaignRequest extends FormRequest
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
            "uuid" => "nullable|exists:campaigns,uuid",
            "status" => "nullable|in:0,1",
        ];
    }

    private function store()
    {
        return [
            "uuid" => "sometimes|exists:campaigns,uuid",
            "owner_id" => "required|exists:users,uuid",
            "start_date" => "required|date|before:end_date",
            "end_date" => "required|date|after:start_date",
            "title" => "required|max:255",
            "type_id" => "required|exists:campaign_types,uuid",
            "category_id" => "required|exists:categories,uuid",
            "file" => [new ValidateBase64(), 'nullable']
        ];
    }

    private function delete()
    {
        return [
            "uuid" => "required|array",
            "uuid.*" => "exists:campaigns,uuid"
        ];
    }
}
