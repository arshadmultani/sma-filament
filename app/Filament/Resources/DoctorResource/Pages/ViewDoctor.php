<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;

class ViewDoctor extends ViewRecord
{
    protected static string $resource = DoctorResource::class;

    public function getTitle(): string
    {
        return 'Dr. ' . $this->record->name;
    }

    public function getHeaderActions(): array
    {
        return [
                Action::make('edit')
                ->label('Edit')
                ->url(route('filament.admin.resources.doctors.edit', $this->record))
                ->color('gray'),
        ];
    }
    
}
