<?php

namespace App\Models;

use App\Traits\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subscription extends Model
{
    use HasFactory;
    use HasUuid, SoftDeletes;

    protected $guarded = ['id', 'uuid'];

    protected $hidden = ['id', 'updated_at', 'deleted_at'];
}
