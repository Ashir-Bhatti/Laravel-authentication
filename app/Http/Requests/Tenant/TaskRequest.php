<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Enums\TaskStatus;
use App\Enums\TaskPriority;

class TaskRequest extends FormRequest
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
            case "status":
                return $this->status();
            default:
                return [];
        }
    }

    private function list()
    {
        return [
            "search" => "nullable|string",
            "user_uuid" => "nullable|exists:users,uuid",
			"position_board_id" => "nullable|array|exists:position_boards,uuid",
        ];
    }

    private function store()
    {
        return [
			'uuid' => 'sometimes|exists:tasks,uuid',
			"title" => "required|max:255",
			"description" => "nullable",
			"status" => ["sometimes", "nullable", Rule::in(TaskStatus::keys())],
            "priority" => ["sometimes", "nullable", Rule::in(TaskPriority::keys())],
			"assignee_id" => "required|exists:users,uuid",
			"reporter_id" => "nullable|exists:users,uuid",
			"start_date" => "required",
            "due_date" => "required|after_or_equal:start_date",
            "completed_at" => "sometimes|nullable|after_or_equal:start_date",
		];
    }

    private function delete()
    {
        return [
            'uuid' => 'required|array',
			'uuid.*' => 'exists:tasks,uuid'
        ];
    }

    private function status()
    {
        return [
            'uuid' => 'required|array',
			'uuid.*' => 'exists:tasks,uuid',
			"read_at" => "required|boolean",
			"important_at" => "required|boolean",
			"completed_at" => "required|boolean",
		];
    }
}
