<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Models\State;
use Filament\Actions;
use App\Enums\StateCategory;
use Filament\Resources\Components\Tab;
use App\Filament\Resources\POBResource;
use Filament\Resources\Pages\ListRecords;

class ListPOBS extends ListRecords
{
    protected static string $resource = POBResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
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
}
