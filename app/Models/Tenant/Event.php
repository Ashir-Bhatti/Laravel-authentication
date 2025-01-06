<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Event extends Model implements HasMedia
{
    use HasFactory;
    use HasUuid, SoftDeletes, InteractsWithMedia;

    protected $guarded = ['id', 'uuid'];

    public function users() : BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function attendees() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'event_attendees');
    }

    public function eventable()
    {
        return $this->morphTo();
    }

    private static function getEventables()
    {
        return collect(config('camphq.eventables'))
            ->map(fn($item) => strtolower(class_basename($item)))
            ->toArray();
    }

    public static function getEventable($value)
    {
        if (in_array(strtolower(class_basename($value)), self::getEventables()))
            return "App\\Models\\Tenant\\".Str::ucfirst($value);

        throw new \Exception("Invalid type: $value");
    }

    public function registerMediaCollections(): void
    {
        $this
            ->addMediaCollection('event_cover_img')
            ->singleFile();
    }

    public function getEventCoverImgUrlAttribute()
    {
        return optional($this->getFirstMedia('event_cover_img'))->getUrl();
    }
}
