<?php

namespace App\Models;

use App\Enums\StateCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function isActive(): bool
    {
        return $this->is_active;
    }

    /**
     * Get the POBs for this state.
     */
    public function pobs(): HasMany
    {
        return $this->hasMany(POB::class);
    }

    public function isFinalized(): bool
    {
        return $this->category === StateCategory::FINALIZED;
    }


    /**
     * Define which relationships to check before deletion.
     *
     * @return array
     */
    public function getRelationsToCheckForDelete(): array
    {
        return ['pobs'];
    }
}
