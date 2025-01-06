<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class OrganizationCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
	{
		return [
			'organizations' => $this->getData(),
			'meta' => [
				'total_record' => $this->total(),
				'per_page_counts' => $this->count(),
				'per_page' => $this->perPage(),
				'current_page' => $this->currentPage(),
				'total_pages' => $this->lastPage(),
				'from' => $this->firstItem(),
				'to' => $this->lastItem(),
            ],
		];
	}
	
	private function getData()
	{
		$data = [];
		foreach ($this->collection as $item) {
			$data[] = [
				'uuid' => $item->uuid,
				'title' => $item->title,
				'description' => $item->description,
				'registration_number' => $item->registration_number,
				'city' => $item->city,
				'state' => $item->state,
				'avatar' => @$item->organizationAvatar[0]->url,
				'subscription_details' => $this->subDetails(@$item->subDetails),
				'user_info' => $this->userInfo(@$item->user),
			];
		}
		return $data;
	}

	private function subDetails($subDetails)
	{
		$data = [];
		foreach ($subDetails as $subDetail) {
			$data[] = [
				'uuid' => $subDetail->uuid,
				'status' => $subDetail->status,
				'subscription' => [
					'uuid' => $subDetail->subscription->uuid,
					'name' => $subDetail->subscription->name,
				]
			];
		}
		return $data;
	}

	private function userInfo($userInfo)
	{
		if($userInfo->organization_id > 0 ){
            return [
                'uuid' => $userInfo->uuid,
                'username' => $userInfo->username,
                'email' => $userInfo->email,
                'phone' => $userInfo->phone,
                'tenant_id' => $userInfo->tenant_id,
                'role' => $userInfo->role
            ];
        }
        return [
            'uuid' => $userInfo->uuid,
			'username' => $userInfo->username,
			'email' => $userInfo->email,
			'phone' => $userInfo->phone,
			'tenant_id' => $userInfo->tenant_id,
			'role' => $userInfo->role,
        ];
	}
}
