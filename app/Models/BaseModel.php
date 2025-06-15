<?php

namespace App\Models;

use App\Models\Scopes\TeamScope;
use Illuminate\Database\Eloquent\Model;

abstract class BaseModel extends Model
{
    protected static function booted()
    {
        static::addGlobalScope(new TeamScope);
    }
}
