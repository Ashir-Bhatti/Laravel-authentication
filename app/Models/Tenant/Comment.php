<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Comment extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];

    public function user() : BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function commentable()
    {
        return $this->morphTo();
    }

    private static function getCommentables()
    {
        return collect(config('camphq.commentables'))
            ->map(fn($item) => strtolower(class_basename($item)))
            ->toArray();
    }

    public static function getCommentable($value)
    {
        if (in_array(strtolower(class_basename($value)), self::getCommentables()))
            return "App\\Models\\Tenant\\".Str::ucfirst($value);

        throw new \Exception("Invalid type: $value");
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'commentable_id')
            ->where('commentable_type', Comment::class);
    }
}
