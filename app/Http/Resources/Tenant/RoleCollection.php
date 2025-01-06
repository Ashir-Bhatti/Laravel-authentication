<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class RoleCollection extends ResourceCollection
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
			'roles' => $this->getData(),
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
				'name' => $item->name,
				'slug' => $item->slug,
                'permissions' => $this->permissions(@$item->permissions)
			];
		}
		return $data;
	}

    private function permissions($permissions)
    {
        $data = [];
        foreach ($permissions as $permission) {
            $data[] = [
                'uuid' => $permission->uuid,
				'name' => $permission->name,
				'slug' => $permission->slug,
				'module' => $permission->parent->name,
            ];
        }
        return $data;
    }
}
