<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUuid
{
    protected $uuidPattern = 'short';

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $uuid = "";
            $existing = false;
            $isShort = (new self())->uuidPattern === 'short';

            do {
                if ($isShort) {
                    $uuid =  Str::uuid();
                    $existing = self::findByUUID($uuid);
                } else {
                    $uuid = str_replace('-', '', (string) Str::uuid());
                }
            } while($existing);

            $model->uuid = $uuid;
        });
    }

    public function scopeWhereUUID($query, $uuid)
    {
        return $query->where('uuid', $uuid);
    }

    public static function findByUUID($uuid, array $columns = ['*'])
    {
        return static::whereUUID($uuid)->first($columns);
    }

    public static function getByUUID($uuid)
	{
		return static::whereInUUID($uuid)->get();
	}

    public function scopeWhereInUUID($query, $uuid)
	{
		return $query->whereIn('uuid', $uuid);
	}
    
    public static function findByUUIDOrFail($uuid, array $columns = ['*'])
    {
        return static::whereUUID($uuid)->firstOrFail($columns);
    }
}
