<?php

namespace App\Filament\Actions\Reviews;

use App\Models\State;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;

class RejectReview
{

    public static function makeTable(): Action
    {
        return self::baseConfig(Action::make('reject_review'));

    }

    public static function baseConfig($action)
    {
        return $action
            ->label('Reject Review')
            ->color('danger')
            ->outlined()
            ->icon('heroicon-o-x-circle')
            ->requiresConfirmation()
            ->modalHeading('Reject Review')
            ->modalSubheading("Are you sure you want to reject this review? This action cannot be undone.")
            ->modalButton('Reject')
            ->action(function ($record) {
                try {
                    $record->update([
                        'verified_at' => null,
                        'state_id' => State::cancelled()->first()->id,
                    ]);

                    Notification::make()
                        ->title('Review Rejected')
                        ->body("The review has been rejected successfully.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    \Log::error('Error rejecting review: ' . $e->getMessage());

                    Notification::make()
                        ->title('Error')
                        ->body('There was an error rejecting the review. Please try again later.')
                        ->danger()
                        ->send();
                }
            });
    }
}