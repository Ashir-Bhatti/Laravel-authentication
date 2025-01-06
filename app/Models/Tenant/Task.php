<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Task extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes, LogsActivity;

    protected $guarded = ['id', 'uuid'];

    public function assignee() : BelongsTo
    {
        return $this->belongsTo(User::class, 'assignee_id');
    }

    public function reporter() : BelongsTo
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function comments() :MorphMany
	{
		return $this->morphMany(Comment::class, 'commentable');
	}

    public function tags() :MorphToMany
	{
		return $this->morphToMany(Tag::class, 'taggable');
	}

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->setDescriptionForEvent(fn(string $eventName) => "Task has been {$eventName}");
    }
}
