<?php

namespace App\Models;

use App\Enums\StateCategory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active' => 'boolean',
        'is_system' => 'boolean',
        'sort_order' => 'integer',
        'category' => StateCategory::class,

    ];

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
