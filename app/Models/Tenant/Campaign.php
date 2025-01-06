<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Scout\Searchable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Campaign extends Model implements HasMedia
{
    use HasFactory;
    use HasUuid, SoftDeletes, Sluggable, Searchable, InteractsWithMedia;

    protected $guarded = ['id', 'uuid'];

    public function toSearchableArray()
    {
        return [
            'uuid' => $this->uuid,
            'title' => $this->title,
        ];
    }

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'title',
                'separator' => '-'
            ]
        ];
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('campaign_file')->singleFile();
    }

    public function getCampaignFileUrlAttribute()
    {
        return optional($this->getFirstMedia('campaign_file'))->getUrl();
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function owner() :BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function campaignType() :BelongsTo
    {
        return $this->belongsTo(CampaignType::class, 'type_id');
    }

    public function department() :BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function outcome(): BelongsTo
    {
        return $this->belongsTo(Outcome::class, 'outcome_id');
    }

    public function events() :MorphMany
	{
		return $this->morphMany(Event::class, 'eventable');
	}
}
