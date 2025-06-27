<?php

namespace App\Filament\Resources\UserResource;

use App\Models\Area;
use App\Models\Headquarter;
use App\Models\Region;
use App\Models\Division;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Schema;

class UserFilters
{
    public static function all(): array
    {
        return [
            SelectFilter::make('division_id')
                ->options(Division::all()->pluck('name', 'id'))
                ->label('Division')
                ->placeholder('Select Division'),
            SelectFilter::make('roles')
                ->multiple()
                ->relationship('roles', 'name', function ($query) {
                    $query->whereNotIn('name', ['admin', 'super_admin']);
                })
                ->preload(),

            SelectFilter::make('region_location_hierarchical')
                ->label('Region')
                ->multiple()
                ->options(function () {
                    if (class_exists(Region::class) && Schema::hasTable((new Region)->getTable())) {
                        return Region::pluck('name', 'id')->all();
                    }

                    return [];
                })
                ->query(function (Builder $query, array $data): Builder {
                    $values = $data['values'] ?? ($data['value'] ? [$data['value']] : []);
                    if (empty($values)) {
                        return $query;
                    }

                    // Group the OR conditions
                    return $query->where(function (Builder $query) use ($values) {
                        // 1. Users whose 'location' IS one of the selected RegionModels
                        $query->whereHasMorph(
                            'location',
                            Region::class,
                            function (Builder $subQuery) use ($values) {
                                $subQuery->whereIn((new Region)->getKeyName(), $values);
                            }
                        );

                        // 2. OR Users whose 'location' IS an AreaModel that belongs to one of the selected Regions
                        // Assuming AreaModel has a 'region_id' foreign key or a 'region' belongsTo relationship
                        $query->orWhereHasMorph(
                            'location',
                            Area::class,
                            function (Builder $subQuery) use ($values) {
                                // Adjust 'region_id' if your foreign key is named differently in AreaModel
                                // Or use whereHas('region', ...) if you have a relationship defined
                                $subQuery->whereIn('region_id', $values);
                            }
                        );

                        // 3. OR Users whose 'location' IS a HeadquarterModel,
                        // and its parent AreaModel belongs to one of the selected Regions.
                        // Assuming HeadquarterModel has an 'area_id' or 'area' relationship,
                        // and AreaModel has a 'region_id' or 'region' relationship.
                        $query->orWhereHasMorph(
                            'location',
                            Headquarter::class,
                            function (Builder $subQuery) use ($values) {
                                $subQuery->whereHas('area', function (Builder $areaQuery) use ($values) {
                                    // Adjust 'region_id' if your foreign key is named differently in AreaModel
                                    $areaQuery->whereIn('region_id', $values);
                                });
                            }
                        );
                    });
                }),
            SelectFilter::make('area_location_hierarchical') // Changed key
                ->label('Area') // Changed label
                ->multiple()
                ->options(function () {
                    if (class_exists(Area::class) && Schema::hasTable((new Area)->getTable())) {
                        return Area::pluck('name', 'id')->all();
                    }

                    return [];
                })
                ->query(function (Builder $query, array $data): Builder {
                    $values = $data['values'] ?? ($data['value'] ? [$data['value']] : []);
                    if (empty($values)) {
                        return $query;
                    }

                    // Group the OR conditions
                    return $query->where(function (Builder $query) use ($values) {
                        // 1. Users whose 'location' IS one of the selected AreaModels
                        $query->whereHasMorph(
                            'location',
                            Area::class,
                            function (Builder $subQuery) use ($values) {
                                $subQuery->whereIn((new Area)->getKeyName(), $values);
                            }
                        );

                        // 2. OR Users whose 'location' IS a HeadquarterModel that belongs to one of the selected Areas
                        // Assuming HeadquarterModel has an 'area_id' foreign key or an 'area' belongsTo relationship
                        $query->orWhereHasMorph(
                            'location',
                            Headquarter::class,
                            function (Builder $subQuery) use ($values) {
                                // Adjust 'area_id' if your foreign key is named differently in HeadquarterModel
                                // Or use whereHas('area', ...) if you have a relationship defined and prefer that
                                $subQuery->whereIn('area_id', $values);
                            }
                        );
                    });
                }),
            // Filter for Users whose 'location' is a specific HeadquarterModel (Direct)
            SelectFilter::make('headquarter_location_direct')
                ->label('Headquarter')
                ->multiple()
                ->options(function () {
                    if (class_exists(Headquarter::class) && Schema::hasTable((new Headquarter)->getTable())) {
                        return Headquarter::pluck('name', 'id')->all();
                    }

                    return [];
                })
                ->query(function (Builder $query, array $data): Builder {
                    $values = $data['values'] ?? ($data['value'] ? [$data['value']] : []);
                    if (empty($values)) {
                        return $query;
                    }

                    return $query->whereHasMorph(
                        'location',
                        Headquarter::class,
                        function (Builder $subQuery) use ($values) {
                            $subQuery->whereIn((new Headquarter)->getKeyName(), $values);
                        }
                    );
                }),

        ];
    }
}
