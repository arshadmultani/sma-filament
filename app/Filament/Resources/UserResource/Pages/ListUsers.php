<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\ActionGroup;
use Filament\Actions\Action;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
           ActionGroup::make([
                Action::make('export')
                    ->label('Export')
                    ->icon('heroicon-m-arrow-down-on-square')
                    ->color('success'),
                    // ->action(function () {
                        // Add your export logic here
           ])->icon('heroicon-m-bars-3-bottom-right'),                
        ];
    }
}
