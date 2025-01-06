<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;

class Organization extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes;

    use Searchable;

    protected $guarded = ['id', 'uuid'];

    public function toSearchableArray()
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
        ];
    }

    public function user() :HasOne
	{
		return $this->hasOne(User::class, 'organization_id','id');
	}

    public function subDetails() :HasMany
	{
		return $this->hasMany(SubscriptionDetail::class);
	}

    public function files()
	{
		return $this->morphToMany(File::class, 'fileable');
	}
	
	public function organizationAvatar()
	{
		return $this->files()->where('type', 'organization_avatar');
	}
}
