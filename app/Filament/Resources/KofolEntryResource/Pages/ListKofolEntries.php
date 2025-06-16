<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Resources\KofolEntryResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListKofolEntries extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = KofolEntryResource::class;

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
                ->query(fn (Builder $query) => $query->where('status', 'Pending'))
                ->badgeColor('warning')
                ->icon('heroicon-o-clock'),
            'approved' => Tab::make('Approved')
                ->badgeColor('success')
                ->query(fn (Builder $query) => $query->where('status', 'Approved'))
                ->icon('heroicon-o-check-circle'),
            'rejected' => Tab::make('Rejected')
                ->badgeColor('danger')
                ->icon('heroicon-o-x-circle')
                ->query(fn (Builder $query) => $query->where('status', 'Rejected')),
        ];
    }
}
