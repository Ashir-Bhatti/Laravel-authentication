<?php

namespace App\Models\Tenant;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventReminder extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];

}
