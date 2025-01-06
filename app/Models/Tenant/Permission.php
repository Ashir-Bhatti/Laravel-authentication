<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use HasFactory, Sluggable;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];
    protected $hidden = [
		'id',
		'created_at',
		'updated_at',
		'parent_id',
		'deleted_at'
	];

	public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name',
                'separator' => '-',
            ],
        ];
    }

	public function getParentSlugAttribute()
	{
		return $this->parent ? $this->parent->slug : null;
	}

	public function getModuleNameAttribute(): string
    {
        return $this->parent->slug ?? '';
    }

	public function getActionAttribute(): string
    {
        return strtolower($this->name);
    }

    //Relations
	public function roles() :BelongsToMany
	{
		return $this->belongsToMany(Role::class,'roles_permissions');
	}

	public function users() :BelongsToMany
	{
		return $this->belongsToMany(User::class,'users_permissions');
	}

	public function children() :HasMany
	{
		return $this->hasMany(Permission::class, 'parent_id', 'id');
	}

	public function parent() :BelongsTo
	{
		return $this->belongsTo(Permission::class, 'parent_id', 'id');
	}
}
