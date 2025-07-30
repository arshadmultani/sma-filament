<?php

namespace App\Filament\Resources\KofolEntryResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\KofolEntryCoupon;
use App\Models\Region;
use Filament\Tables\Columns\TextColumn;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;


class KofolCoupon extends BaseWidget
{
    protected static ?string $heading = '';
    protected static ?string $description = 'All Regions by Coupon Count';

    public function table(Table $table): Table
    {
        return $table
            ->paginated([15,25,50,100])
            ->query(
                KofolEntryCoupon::query()
                    ->selectRaw('CAST(regions.id AS CHAR) as id, divisions.name as division_name, zones.name as zone_name, regions.name as region_name, COUNT(DISTINCT kofol_entry_coupons.id) as coupon_count, (SELECT COUNT(*) FROM kofol_entries ke INNER JOIN headquarters h ON h.id = ke.headquarter_id INNER JOIN areas a ON a.id = h.area_id INNER JOIN regions r ON r.id = a.region_id WHERE r.id = regions.id AND ke.status = \'Approved\') as approved_entries_count')
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
                TextColumn::make('approved_entries_count')
                    ->label('Bookings')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('region_name')
                    ->label('Region'),
                TextColumn::make('division_name')
                    ->label('Division'),
            ])
            ->defaultSort('coupon_count', 'desc')
            ->headerActions([
                ExportAction::make('export')
                    ->exports([
                        ExcelExport::make()
                            ->fromTable()
                    ])
                    ->label('Export')
                    ->outlined(),
            ]);
    }
}
