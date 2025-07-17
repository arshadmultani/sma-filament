<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Actions\AddTagAction;
use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Filament\Pages\Concerns\CanPaginateViewRecord;
use App\Filament\Actions\PreviousAction;
use App\Filament\Actions\NextAction;

class ViewDoctor extends ViewRecord
{
    use CanPaginateViewRecord;

    protected static string $resource = DoctorResource::class;

    // public function getHeader(): ?View{
    //     return view('filament.doctor-resource.pages.view-doctor-header',[
    //         'record'=>$this->record,
    //         'actions'=>$this->getHeaderActions(),
    //     ]);
    // }

    public function getTitle(): string
    {
        return 'Dr. ' . $this->record->name;
    }

    public function getHeaderActions(): array
    {
        $actions = [];
        $actions[] = PreviousAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);
        $actions[] = NextAction::make()
            ->extraAttributes(['class' => 'hidden sm:block']);
        if (Gate::allows('updateStatus', $this->getRecord())) {
            $actions[] = UpdateStatusAction::make();
        }
        $actions[] = ActionGroup::make([
            AddTagAction::make(null, 'doctor', 'tags', 'Add Tag', 'Add Tag to Doctor')
                ->visible(fn() => $this->record->status == 'Approved'),
            Action::make('edit')
                ->icon('heroicon-m-pencil')
                ->hidden(fn() => !(Auth::user()->hasRole(['admin', 'super_admin']) || Auth::user()->id === $this->record->user_id))
                ->label('Edit')
                ->url(route('filament.admin.resources.doctors.edit', $this->record))
                ->color('primary'),
        ])->icon('heroicon-m-bars-3-bottom-right');

        return $actions;
    }
}
