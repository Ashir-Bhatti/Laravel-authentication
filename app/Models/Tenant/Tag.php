<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tag extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];

    public function taggables() :HasMany
    {
        return $this->hasMany(Taggable::class, 'tag_id');
    }

    private static function getTaggables()
    {
        return collect(config('camphq.taggables'))
            ->map(fn($item) => strtolower(class_basename($item)))
            ->toArray();
    }

    public static function getTaggable($value)
    {
        if (in_array(strtolower(class_basename($value)), self::getTaggables()))
            return "App\\Models\\Tenant\\".Str::ucfirst($value);

        throw new \Exception("Invalid type: $value");
    }
}
