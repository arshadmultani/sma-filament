<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class ViewDoctor extends ViewRecord
{
    protected static string $resource = DoctorResource::class;

    // public function getHeader(): ?View{
    //     return view('filament.doctor-resource.pages.view-doctor-header',[
    //         'record'=>$this->record,
    //         'actions'=>$this->getHeaderActions(),
    //     ]);
    // }

    public function getTitle(): string
    {
        return 'Dr. '.$this->record->name;
    }

    public function getHeaderActions(): array
    {
        $actions = [];
        if (Gate::allows('updateStatus', $this->getRecord())) {
            $actions[] = UpdateStatusAction::make();
        }
        $actions[] = Action::make('edit')
            ->hidden(fn () => ! (Auth::user()->hasRole(['admin', 'super_admin']) || Auth::user()->id === $this->record->user_id))
            ->label('Edit')
            ->url(route('filament.admin.resources.doctors.edit', $this->record))
            ->color('gray');

        return $actions;
    }
}
