<?php

namespace App\Models;

use App\Enums\StateCategory;
use Illuminate\Database\Eloquent\Casts\Attribute;
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

    public function isFinalized(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category === StateCategory::FINALIZED,
        );
    }

    public function isPending(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category === StateCategory::PENDING,
        );
    }

    public function isCancelled(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->category === StateCategory::CANCELLED,
        );
    }

    public function scopePending($query)
    {
        return $query->where('category', StateCategory::PENDING);
    }

    public function scopeFinalized($query)
    {
        return $query->where('category', StateCategory::FINALIZED);
    }

    public function scopeCancelled($query)
    {
        return $query->where('category', StateCategory::CANCELLED);
    }

    /**
     * Define which relationships to check before deletion.
     */
    public function getRelationsToCheckForDelete(): array
    {
        return ['pobs'];
    }
}
