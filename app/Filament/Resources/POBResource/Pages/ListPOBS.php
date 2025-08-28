<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Models\State;
use Filament\Actions;
use App\Enums\StateCategory;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\Auth;
use App\Filament\Exports\POBExporter;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\POBResource;
use Filament\Resources\Pages\ListRecords;

class ListPOBS extends ListRecords
{
    protected static string $resource = POBResource::class;


    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'pending' => Tab::make('Pending')
                ->query(fn($query) => $query->whereHas('state', fn($q) => $q->where('category', StateCategory::PENDING)))
                ->badgeColor('warning')
                ->icon('heroicon-o-clock'),
            'approved' => Tab::make('Approved')
                ->query(fn($query) => $query->whereHas('state', fn($q) => $q->where('category', StateCategory::FINALIZED)))
                ->badgeColor('success')
                ->icon('heroicon-o-check-circle'),
            'rejected' => Tab::make('Rejected')
                ->query(fn($query) => $query->whereHas('state', fn($q) => $q->where('category', StateCategory::CANCELLED)))
                ->badgeColor('danger')
                ->icon('heroicon-o-x-circle'),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ExportAction::make()
                    ->exporter(POBExporter::class)
                    ->label('Download POB')
                    ->modalDescription('This will download all the POBs in the system. This may take a moment to complete.')
                    ->maxRows(30000)
                    ->modalWidth('2xl')
                    ->color('primary')
                    ->visible(fn(): bool => Auth::user()->can('create_user')),
            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }
}
