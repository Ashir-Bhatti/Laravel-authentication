<?php

namespace App\Http\Requests\Tenant;

use Illuminate\Foundation\Http\FormRequest;

class ActivityLogRequest extends FormRequest
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
            case "logs":
                return $this->logs();
            default:
                return [];
        }
    }

    private function logs()
    {
        return [
            'type' => 'sometimes|string',
            'uuid' => 'sometimes|exists:tasks,uuid',
        ];
    }
}
