<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubscriptionDetail extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];

    public function subscription() :BelongsTo
	{
		return $this->belongsTo(Subscription::class, 'subscription_id');
	}
}
