<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class UserCollection extends ResourceCollection
{
    public function toArray($request)
	{
		return [
			'users' => 	$this->getData(),
			'meta' => [
				'total_record' => $this->total() ?? null,
				'per_page_counts' => $this->count() ?? null,
				'per_page' => $this->perPage() ?? null,
				'current_page' => $this->currentPage() ?? null ,
				'total_pages' => $this->lastPage() ?? null,
				'from' => $this->firstItem() ?? null,
				'to' => $this->lastItem() ?? null,
            ],
		];
	}
	
	private function getData()
	{   
		$data = [];
		foreach ($this->collection as $user) {
			$data[] = [
				'uuid' => $user->uuid,
				'full_name' => $user->full_name,
				'fname' => $user->fname,
				'lname' => $user->lname,
				'username' => $user->username,
				'phone' => $user->phone,
				'email' => $user->email,
				'address' => $user->address,
				'status' => $user->status,
				'term_start_date' => $user->term_start_date,
				'term_end_date' => $user->term_end_date,
				"avatar_url" => $user->avatarUrl,
                'role' => [
                    'uuid' => @$user->roles[0]->uuid,
                    'name' => @$user->roles[0]->name
                ],
				'position_board' => [
                    'uuid' => @$user->positionBoard->uuid,
                    'title' => @$user->positionBoard->title
                ]
			];
		}
		return $data;
	}
}
