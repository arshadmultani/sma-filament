<?php

namespace App\Filament\Actions\Reviews;

use App\Models\State;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action as TablesAction;

class ApproveReview
{
    public static function makeTable(): TablesAction
    {
        return TablesAction::make('approve_review')
            ->label('Approve Review')
            ->color('success')
            ->outlined()
            ->icon('heroicon-o-check-circle')
            ->requiresConfirmation()
            ->modalHeading('Approve Review')
            ->modalSubheading("Are you sure you want to approve this review? This will make it visible on the website.")
            ->modalButton('Approve')
            ->action(function ($record) {
                try {
                    $record->update([
                        'is_verified' => true,
                        'verified_at' => now(),
                        'state_id' => State::finalized()->first()->id,
                    ]);
                    Notification::make()
                        ->title('Review Approved')
                        ->body("The review has been approved successfully. It will now be visible on the website.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    \Log::error('Error approving review: ' . $e->getMessage());

                    Notification::make()
                        ->title('Error')
                        ->body('There was an error approving the review. Please try again later.')
                        ->danger()
                        ->send();
                }
            });
    }
}