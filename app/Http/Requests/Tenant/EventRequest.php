<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Rules\ValidateBase64;
use App\Enums\EventGenre;
use App\Enums\EventPrivacy;
use App\Enums\EventType;

class EventRequest extends FormRequest
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
            case "sync-users":
                return $this->syncUsers();
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
			'uuid' => 'sometimes|exists:events,uuid',
            'title' => "required|max:255",
            'genre' => ["sometimes", "nullable", Rule::in(EventGenre::keys())],
            'privacy' => ["sometimes", "nullable", Rule::in(EventPrivacy::keys())],
            'event_type' => ["sometimes", "nullable", Rule::in(EventType::keys())],
			"type" => "required",
			"eventable_id" => "required|exists:campaigns,uuid",
			"address" => "sometimes|max:255",
			"open_registration" => "required",
            "close_registration" => "required|after_or_equal:open_registration",
            "start_date" => "required",
            "end_date" => "required|after_or_equal:start_date",
            "description" => "sometimes",
            "event_cover_img" => [new ValidateBase64(), 'nullable']
		];
    }

    private function delete()
    {
        return [
			"uuid" => "required|array",
			'uuid.*' => 'exists:events,uuid'
		];
    }

    private function syncUsers()
    {
        return [
            'event_uuid' => 'required|array',
            'event_uuid.*' => 'exists:events,uuid',
            'user_uuid' => 'required|array',
            'user_uuid.*' => 'exists:users,uuid'
        ];
    }
}
