<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use App\Traits\HasPermission;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class User extends Model implements HasMedia
{
    use HasFactory, HasPermission;
    use HasUuid, SoftDeletes;
    use InteractsWithMedia;

    protected $guarded = ['id', 'uuid'];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'users_roles');
    }

    public function positionBoard(): BelongsTo
    {
        return $this->belongsTo(PositionBoard::class);
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('avatar')
            ->singleFile();
    }

    public function getAvatarUrlAttribute()
    {
        return optional($this->getFirstMedia('avatar'))->getUrl();
    }

    public function getFullNameAttribute(): string
    {
        return $this->fname. ' ' .$this->lname;
    }

    public function scopeRoleString($query, $search)
	{
		return $query->whereHas('roles', function ($query) use ($search) {
			$query->whereIn('uuid', $search);
		});
	}

    public function scopePositionBoardString($query, $search)
	{
		return $query->whereHas('positionBoard', function ($query) use ($search) {
			$query->whereIn('uuid', $search);
		});
	}

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_attendees');
    }
}
