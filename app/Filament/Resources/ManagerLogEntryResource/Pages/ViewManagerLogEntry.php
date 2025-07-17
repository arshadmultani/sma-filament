<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Pages;

use App\Filament\Resources\ManagerLogEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Pages\Concerns\CanPaginateViewRecord;
use App\Filament\Actions\PreviousAction;
use App\Filament\Actions\NextAction;

class ViewManagerLogEntry extends ViewRecord
{
    use CanPaginateViewRecord;
    protected static string $resource = ManagerLogEntryResource::class;

    public function getTitle(): string
    {
        return 'CT-' . $this->record->id . '/' . $this->record->date->format('dmy');
    }
    public function getHeaderActions(): array
    {
        $actions = [];
        $actions[] = PreviousAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);
        $actions[] = NextAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);
        return $actions;
    }
}
