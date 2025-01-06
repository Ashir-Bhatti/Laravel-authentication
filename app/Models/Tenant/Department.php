<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Department extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes, Sluggable, Searchable;

    protected $guarded = ['id', 'uuid'];

	public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'separator' => '-',
            ],
        ];
    }

    public function toSearchableArray()
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
        ];
    }

    public function createdBy() :BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
