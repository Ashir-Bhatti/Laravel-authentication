<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrganizationRequest extends FormRequest
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
			case "store":
				return $this->store();
			case "update":
				return $this->update();
			default:
				return $this->updateStatus();
		}
    }

    protected function store()
    {
        return [
            'title'	=> 'required|unique:organizations,title',
			'description' => 'required',	
			'registration_number' => 'required|unique:organizations,registration_number',	
			'address' => 'required',
			'city' => 'required',
			'phone'	=> 'required',
			'state'	=> 'required',
			'zip' => 'required',
			'status' => 'nullable|in:0,1',
			'avatar' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

			//Subscriptions
			'subscription.subscription_id' => 'required|exists:subscriptions,uuid',
			'subscription.billing_start_date' => 'required',
			'subscription.setup_fee' => 'required',
			'subscription.setup_fee_start_date' => 'required',

            //User
			"admin_info" => 'required',
			'admin_info.fname' => 'required',
			'admin_info.lname' => 'required',
			'admin_info.username' => 'required|unique:users,username',
			'admin_info.phone' => 'required',
			'admin_info.email' => 'required|unique:users,email',
        ];
    }

	private function update()
    {
		tenancy()->end();
        return [
			"description" => "required", 
			"address" => "required",
			"city" => "required",
			"phone" => "required",
			"state" => "required",
			"zip" => "required",

			//User
			"admin_info" => 'required',
			'admin_info.fname' => 'required',
			'admin_info.lname' => 'required',
			'admin_info.phone' => 'required',
		];
    }

    protected function updateStatus()
	{
		return [
			'organization_id' => 'required|exists:organizations,uuid',
			'status' => 'required|in:cancel,pause,active,in-processing'
		];
	}
}
