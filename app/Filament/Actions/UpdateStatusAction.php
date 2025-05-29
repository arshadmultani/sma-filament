<?php

namespace App\Filament\Actions;

use Filament\Forms;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;
use Filament\Forms\Components\Select;
use Filament\Actions\Action;
class UpdateStatusAction{
    public static function make(): Action{
        return Action::make('update_status')
        ->label('Update Status')
                ->form([
                    Select::make('status')
                        ->label('Status')
                        ->options([
                            'Pending' => 'Pending',
                            'Approved' => 'Approved',
                            'Rejected' => 'Rejected',
                        ])
                        ->required(),
                ])
                ->action(function (array $data, $record) {
                    $record->status = $data['status'];
                    $record->save();

                    Notification::make()
                        ->title('Status updated')
                        ->body('Status updated to ' . $data['status'])
                        ->success()
                        ->send();
                })
                ->color('primary')
                ->icon('heroicon-o-arrow-path');
    }

    public static function makeBulk(): BulkAction{
        return BulkAction::make('update_status')
        ->label('Update Status')
        ->form([
            Select::make('status')
                ->label('Status')
                ->options([
                    'Pending' => 'Pending',
                    'Approved' => 'Approved',
                    'Rejected' => 'Rejected',
                ])
                ->required(),
        ])
        ->action(function (array $data, $records) {
            foreach ($records as $record) {
                $record->status = $data['status'];
                $record->save();
            }
            
            Notification::make()
                ->title('Status updated')
                ->body('Status updated to ' . $data['status'] . ' for selected records.')
                ->success()
                ->send();
        })
        ->color('primary')
        ->icon('heroicon-o-arrow-path');
    }
}