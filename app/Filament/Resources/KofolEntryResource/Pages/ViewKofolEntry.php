<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Resources\KofolEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Torgodly\Html2Media\Actions\Html2MediaAction;


class ViewKofolEntry extends ViewRecord
{
    protected static string $resource = KofolEntryResource::class;

    public function getTitle(): string
    {
        return 'KSV/POB/' . $this->record->id;
    }

    public function getHeaderActions(): array
    {
        return [

            Action::make('update_status')
                ->label('Update Status')
                ->form([
                    \Filament\Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
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
                ->icon('heroicon-o-arrow-path'),

            Html2MediaAction::make('print')
                ->content(fn($record)=>view('filament.kofol-entry-invoice', ['kofolEntry' => $record]))
                ->print()
                ->savePdf()
                ->margin([10, 10, 10, 10])
                ->icon('heroicon-o-printer')
                ->label('')
                ->filename('KSV-POB-'.$this->record->id)
                ->color('gray'),
                
                
                Action::make('edit')
                ->label('Edit')
                ->url(route('filament.admin.resources.kofol-entries.edit', $this->record))
                ->color('gray'),
        ];
    }
}
