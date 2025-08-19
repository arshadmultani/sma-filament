<?php

namespace App\Filament\Resources\POBResource\Pages;

use App\Models\POB;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Illuminate\Support\Facades\Log;
use App\Filament\Actions\NextAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Filament\Resources\POBResource;
use App\Filament\Actions\PreviousAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Actions\UpdateStateAction;
use Filament\Tables\Concerns\CanPaginateRecords;
use App\Filament\Pages\Concerns\CanPaginateViewRecord;

class ViewPOB extends ViewRecord
{
    use CanPaginateViewRecord;
    protected static string $resource = POBResource::class;

    public function getTitle(): string
    {
        return 'POB/' . $this->record->id;
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        $actions[] = PreviousAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);
        $actions[] = NextAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);



        if (Gate::allows('updateState', POB::class)) {
            $actions[] = UpdateStateAction::make();
        }
        if (Auth::user()->can('update', $this->record) && !$this->record->state->isFinalized()) {
            $actions[] = EditAction::make();

        }

        return $actions;
    }
}
