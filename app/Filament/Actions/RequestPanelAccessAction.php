<?php

namespace App\Filament\Actions;

use App\Models\State;
use App\Enums\StateCategory;
use Filament\Actions\Action;
use App\Models\PanelAccessRequest;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\MaxWidth;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Support\View\Components\Modal;



class RequestPanelAccessAction
{

    public static function make(): Action
    {
        return Action::make('request_panel_access')
            ->modalWidth(MaxWidth::ExtraLarge)
            ->label('Request Portal Access')
            ->icon('heroicon-s-key')
            ->color('primary')
            ->outlined()
            ->form([
                Select::make('request_reason')
                    ->label('Reason for Request')
                    ->required()
                    ->native(false)
                    ->placeholder('Select Reason')
                    ->options(([
                        'interest_shown' => 'Doctor has shown interest',
                        'high_volume_patients' => 'Doctor has high patient volume',
                        'speicalist_doctor' => 'Specialist Doctor',
                        'other' => 'Other',

                    ])),
                Textarea::make('justification')
                    ->label('Remark(optional)')
                    ->autosize()
                    ->maxLength(100)
                    ->placeholder('Additional remarks'),
            ])
            ->requiresConfirmation()
            ->modalHeading('Confirm Portal Access Request')
            ->modalSubheading('Are you sure you want to request portal access to this doctor? Press Confirm if you want to send request')
            ->action(function ($record, $data) {
                $user = auth()->user();
                if ($record && $user) {
                    // Check if a request already exists and is pending
                    $existingRequest = PanelAccessRequest::where('doctor_id', $record->id)
                        ->where('state_id', State::pending()->value('id'))
                        ->first();

                    if ($existingRequest) {
                        // Notify user that a request is already pending
                        Notification::make()
                            ->title('Request Pending')
                            ->body('A request for portal access is already pending approval.')
                            ->warning()
                            ->send();
                    } else {
                        // Create a new panel access request
                        PanelAccessRequest::create([
                            'doctor_id' => $record->id,
                            'requested_by' => $user->id,
                            'state_id' => State::pending()->value('id'),
                            'request_reason' => $data['request_reason'],
                            'justification' => $data['justification'],
                        ]);

                        // Notify user of successful request
                        Notification::make()
                            ->title('Request Submitted')
                            ->body('Your request for Dr. portal access has been submitted successfully.')
                            ->success()
                            ->send();
                    }
                } else {
                    // Notify user of an error
                    Notification::make()
                        ->title('Error')
                        ->body('Unable to process your request at this time. Please try again later.')
                        ->danger()
                        ->send();
                }
            });
    }
}