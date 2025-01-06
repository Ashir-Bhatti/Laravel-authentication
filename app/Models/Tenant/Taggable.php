<?php

namespace App\Models\Tenant;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphPivot;

class Taggable extends MorphPivot
{
    use HasFactory;

    protected $guarded = ['id', 'uuid'];

    protected $table = 'taggables';

    public function tag() :BelongsTo
    {
        return $this->belongsTo(Tag::class, 'tag_id');
    }

    public function taggable()
    {
        return $this->morphTo();
    }
}
