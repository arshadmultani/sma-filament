<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Resources\KofolEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
class ViewKofolEntry extends ViewRecord
{
    protected static string $resource = KofolEntryResource::class;

    public function getTitle(): string
    {
        return 'KSV/POB/'.$this->record->id;
    }

    public function getHeaderActions(): array
    {
        return [
            Action::make('edit')
                ->label('Edit')
                ->url(route('filament.admin.resources.kofol-entries.edit', $this->record))
                ->color('gray'),

                Action::make('change_status')
            ->label('Change Status')
            ->form([
                \Filament\Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'Approve' => 'Approve',
                        'Reject' => 'Reject',
                    ])
                    ->required(),
            ])
            ->action(function (array $data) {
                $this->record->status = $data['status'];
                $this->record->save();

                \Filament\Notifications\Notification::make()
                    ->title('Status updated')
                    ->body('Status updated to ' . $data['status'])
                    ->success()
                    ->send();
            })
            ->color('primary')
            ->icon('heroicon-o-arrow-path')
        ];
    }
}
