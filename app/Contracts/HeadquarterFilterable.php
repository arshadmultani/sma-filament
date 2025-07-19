<?php
namespace App\Contracts;

use Illuminate\Database\Eloquent\Builder;

interface HeadquarterFilterable
{
    /**
     * Scope a query to only include records belonging to the given headquarter IDs.
     *
     * @param Builder $query
     * @param array $headquarterIds
     * @return Builder
     */
    public function scopeWhereHeadquarterIn(Builder $query, array $headquarterIds): Builder;
    
    /**
     * Scope a query to only include records belonging to the given location type and IDs.
     * Each model implements this based on its own relationship structure.
     *
     * @param Builder $query
     * @param string $locationType (headquarter, area, region, zone)
     * @param array $locationIds
     * @return Builder
     */
    public function scopeWhereLocationIn(Builder $query, string $locationType, array $locationIds): Builder;
}