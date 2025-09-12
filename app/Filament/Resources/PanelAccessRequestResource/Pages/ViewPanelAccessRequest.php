<?php

namespace App\Filament\Resources\PanelAccessRequestResource\Pages;

use App\Filament\Actions\ApproveAction;
use App\Filament\Actions\CreateDoctorUser;
use App\Filament\Actions\NextAction;
use App\Filament\Actions\PreviousAction;
use App\Filament\Actions\RejectAction;
use App\Filament\Pages\Concerns\CanPaginateViewRecord;
use App\Filament\Resources\PanelAccessRequestResource;
use Filament\Resources\Pages\ViewRecord;

class ViewPanelAccessRequest extends ViewRecord
{
    use CanPaginateViewRecord;

    protected static string $resource = PanelAccessRequestResource::class;

    public function getTitle(): string
    {
        return 'PR-' . $this->record->id;
    }

    protected function getHeaderActions(): array
    {
        $actions = [];

        $actions[] = PreviousAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);
        $actions[] = NextAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);

        $actions[] = CreateDoctorUser::make()
            ->hidden($this->record->doctor->hasLoginAccount())
            ->visible(fn() => $this->record->state->isFinalized);

        $actions[] = ApproveAction::make(
            'Portal Access Approved for ' . $this->record->doctor->name,
            fn() => $this->record->requester
        )
            ->hidden($this->record->state->isFinalized);

        $actions[] = RejectAction::make(
            'Portal Access Request Rejected for ' . $this->record->doctor->name,
            fn() => $this->record->requester
        )
            ->hidden($this->record->state->isCancelled);

        return $actions;
    }
}
