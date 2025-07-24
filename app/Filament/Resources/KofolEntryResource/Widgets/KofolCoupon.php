<?php

namespace App\Filament\Resources\KofolEntryResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\KofolEntryCoupon;
use Filament\Tables\Columns\TextColumn;

class KofolCoupon extends BaseWidget
{
    protected static ?string $heading = '';
    protected static ?string $description = 'Top 10 Areas by Coupon Count';
    public function table(Table $table): Table
    {
        // Aggregate coupon counts by area
        $topAreas = KofolEntryCoupon::query()
            ->selectRaw('CAST(areas.id AS CHAR) as id, divisions.name as division_name, areas.name as area_name, COUNT(kofol_entry_coupons.id) as coupon_count')
            ->join('kofol_entries', 'kofol_entries.id', '=', 'kofol_entry_coupons.kofol_entry_id')
            ->join('headquarters', 'headquarters.id', '=', 'kofol_entries.headquarter_id')
            ->join('areas', 'areas.id', '=', 'headquarters.area_id')
            ->join('divisions', 'divisions.id', '=', 'areas.division_id')
            ->groupBy('areas.id', 'areas.name', 'divisions.name')
            ->orderByDesc('coupon_count')
            ->limit(10);

        return $table
            ->paginated(false)
            ->query($topAreas)
            ->description('Top 10 Areas by Coupon Count')
            ->columns([
                TextColumn::make('coupon_count')->label('Coupons'),
                TextColumn::make('area_name')->label('Area'),
                TextColumn::make('division_name')->label('Division'),
            ]);
    }
}
