<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PositionBoard extends Model
{
    use HasFactory, Sluggable;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];
    protected $hidden = ['id', 'created_at', 'updated_at', 'deleted_at'];

    /**
     * Return the sluggable configuration array for this model.
     *
     * @return array
     */
    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'separator' => '-'
            ]
        ];
    }
}
