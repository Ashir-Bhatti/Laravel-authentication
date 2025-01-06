<?php

namespace App\Http\Resources\Tenant;

use Illuminate\Http\Resources\Json\ResourceCollection;

class PermissionCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return $this->collection
            ->groupBy('module_name')
            ->map(function ($permissions, $module) {
                return [
                    'module' => $module,
                    'create' => $permissions->pluck('action')->contains('create'),
                    'view'   => $permissions->pluck('action')->contains('view'),
                    'edit'   => $permissions->pluck('action')->contains('edit'),
                    'delete'   => $permissions->pluck('action')->contains('delete'),
                ];
            })
            ->values();
    }
}
