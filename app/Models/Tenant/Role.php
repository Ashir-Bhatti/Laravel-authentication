<?php

namespace App\Models\Tenant;

use App\Traits\HasPermission;
use App\Traits\HasUuid;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Role extends Model
{
    use HasFactory, HasPermission;
    use HasUuid, SoftDeletes;
    use Searchable;
    use Sluggable;

    protected $guarded = ['id', 'uuid'];

    public function toSearchableArray()
    {
        return [
            'uuid' => $this->uuid,
            'name' => $this->name,
        ];
    }

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'separator' => '-'
            ]
        ];
    }
}
