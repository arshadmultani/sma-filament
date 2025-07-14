<?php

namespace App\Filament\Resources\ManagerLogEntryResource\Pages;

use App\Filament\Resources\ManagerLogEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewManagerLogEntry extends ViewRecord
{
    protected static string $resource = ManagerLogEntryResource::class;

    public function getTitle(): string
    {
        return 'CT-'.$this->record->id.'/'.$this->record->date->format('dmy');
    }
}
