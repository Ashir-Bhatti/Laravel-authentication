<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outcome extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes, Sluggable;

    protected $guarded = ['id', 'uuid'];

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
