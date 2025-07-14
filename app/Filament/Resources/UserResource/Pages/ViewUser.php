<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return $this->record->name;
    }
    public function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit')
                ->color('gray')
                ->url(route('filament.admin.resources.users.edit', $this->record))
                ->visible(fn () => Auth::user()->can('update', $this->record))
            ];
    }
}
