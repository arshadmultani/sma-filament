<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Resources\KofolEntryResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Filament\Actions\ActionGroup;
use App\Filament\Exports\KofolEntryExporter;
use App\Filament\Exports\KofolEntryCouponExporter;
use Filament\Actions\ExportAction;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Auth;

class ListKofolEntries extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = KofolEntryResource::class;


    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ExportAction::make()
                    ->exporter(KofolEntryExporter::class)
                    ->label('Download KSV Bookings')
                    ->modalDescription('This will download all the KSV Bookings in the system. This may take a moment to complete.')
                    ->maxRows(30000)
                    ->modalWidth('2xl')
                    ->color('primary')
                    ->visible(fn(): bool => Auth::user()->can('create_user')),
                // Actions\ExportAction::make()
                //     ->exporter(KofolEntryCouponExporter::class)
                //     ->label('Download Coupons')
                //     ->modalDescription('This will download all the Coupons in the system. This may take a moment to complete.')
                //     ->maxRows(30000)
                //     ->modalWidth('2xl')
                //     ->color('primary')
                //     ->visible(fn(): bool => Auth::user()->can('create_user')),

            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending' => Tab::make('Pending')
                ->query(fn(Builder $query) => $query->where('status', 'Pending'))
                ->badgeColor('warning')
                ->icon('heroicon-o-clock'),
            'approved' => Tab::make('Approved')
                ->badgeColor('success')
                ->query(fn(Builder $query) => $query->where('status', 'Approved'))
                ->icon('heroicon-o-check-circle'),
            'rejected' => Tab::make('Rejected')
                ->badgeColor('danger')
                ->icon('heroicon-o-x-circle')
                ->query(fn(Builder $query) => $query->where('status', 'Rejected')),
        ];
    }

}
