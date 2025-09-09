<?php

namespace App\Filament\Actions;

use App\Models\State;
use App\Enums\StateCategory;
use Filament\Actions\Action;
use App\Models\PanelAccessRequest;
use Filament\Notifications\Notification;


class RequestPanelAccessAction
{

    public static function make(): Action
    {
        return Action::make('request_panel_access')
            ->label('Request Portal Access')
            ->icon('heroicon-s-key')
            ->color('primary')
            ->outlined()
            ->requiresConfirmation()
            ->modalHeading('Confirm Panel Access Request')
            ->modalSubheading('Are you sure you want to request access to this doctor\'s panel? This action cannot be undone.')
            ->action(function ($record) {
                $user = auth()->user();
                if ($record && $user) {
                    // Check if a request already exists
                    $existingRequest = PanelAccessRequest::where('doctor_id', $record->id)->first();

                    if ($existingRequest) {
                        // Notify user that a request is already pending
                        Notification::make()
                            ->title('Requested Already')
                            ->body('Already requested for access. Please wait for approval.')
                            ->warning()
                            ->send();
                    } else {
                        // Create a new panel access request
                        PanelAccessRequest::create([
                            'doctor_id' => $record->id,
                            'requested_by' => $user->id,
                            'state_id' => State::where('category', StateCategory::PENDING)->value('id'),
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