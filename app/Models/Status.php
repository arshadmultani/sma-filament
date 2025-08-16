<?php

namespace App\Models;

use App\Enums\StatusCategory;
use Illuminate\Database\Eloquent\Model;

class Status extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'category' => StatusCategory::class,

    ];
}
