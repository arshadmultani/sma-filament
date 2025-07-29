<?php

namespace App\Filament\Resources\KofolEntryResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\KofolEntryCoupon;
use App\Models\Region;
use Filament\Tables\Columns\TextColumn;

class KofolCoupon extends BaseWidget
{
    protected static ?string $heading = '';
    protected static ?string $description = 'All Regions by Coupon Count';

    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->query(
                KofolEntryCoupon::query()
                    ->selectRaw('CAST(regions.id AS CHAR) as id, divisions.name as division_name, zones.name as zone_name, regions.name as region_name, COUNT(kofol_entry_coupons.id) as coupon_count')
                    ->join('kofol_entries', 'kofol_entries.id', '=', 'kofol_entry_coupons.kofol_entry_id')
                    ->join('headquarters', 'headquarters.id', '=', 'kofol_entries.headquarter_id')
                    ->join('areas', 'areas.id', '=', 'headquarters.area_id')
                    ->join('regions', 'regions.id', '=', 'areas.region_id')
                    ->join('zones', 'zones.id', '=', 'regions.zone_id')
                    ->join('divisions', 'divisions.id', '=', 'regions.division_id')
                    ->groupBy('regions.id', 'regions.name', 'zones.name', 'divisions.name')
                    ->orderByDesc('coupon_count')
                    ->orderBy('regions.name')
            )
            ->description('Regions by Coupon Count')
            ->columns([
                TextColumn::make('coupon_count')
                    ->label('Coupons'),
                TextColumn::make('region_name')
                    ->label('Region'),
                // TextColumn::make('zone_name')
                //     ->label('Zone')
                //     ->sortable(),
                TextColumn::make('division_name')
                    ->label('Division'),
            ]);
    }
}
