<?php

namespace App\Filament\Actions;

use Filament\Forms;
use Filament\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Mail;
use App\Mail\KofolCoupon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class SendKofolCouponAction
{
    // Single Record Action
    public static function make(): Action
    {
        return Action::make('sendKofolCoupon')
            ->label('Send Mail')
            ->icon('heroicon-m-envelope')
            ->visible(function () {
                /** @var \App\Models\User|null $user */
                $user = Auth::user();
                return $user && $user->hasRole(['admin', 'super_admin']);
            })
            ->requiresConfirmation()
            ->modalHeading('Send Kofol Coupon')
            ->modalDescription('Are you sure you want to send the coupon email?')
            ->modalSubmitActionLabel('Yes, send it')
            ->action(function ($record) {   
                try {
                    if (!$record->customer) {
                        throw new \Exception('No customer found for this record.');
                    }

                    Mail::to($record->customer->email)->queue(
                        new KofolCoupon(
                            $record->customer,
                            $record->coupon_code
                        )
                    );

                    Notification::make()
                        ->title('Coupon Email Queued')
                        ->body("Coupon email has been queued for {$record->customer->email}.")
                        ->success()
                        ->send();
                } catch (\Exception $e) {
                    Notification::make()
                        ->title('Error Sending Email')
                        ->body("Failed to send coupon email: " . $e->getMessage())
                        ->danger()
                        ->send();
                }
            });
    }

    // Bulk Action
    public static function makeBulk(): BulkAction
    {
        return BulkAction::make('sendKofolCoupon')
            ->label('Send Coupon to Selected')
            ->icon('heroicon-m-envelope')
            ->visible(function () {
                /** @var \App\Models\User|null $user */
                $user = Auth::user();
                return $user && $user->hasRole(['admin', 'super_admin']);
            })
            ->requiresConfirmation()
            ->modalHeading('Send Kofol Coupon')
            ->modalDescription('Are you sure you want to send coupon emails to the selected users?')
            ->modalSubmitActionLabel('Yes, send them')
            ->action(function ($records) {
                $successCount = 0;
                $errorCount = 0;
                $rejectedCount = 0;
                $noCouponCount = 0;

                foreach ($records as $record) {
                    try {
                        if (!$record->customer) {
                            throw new \Exception('No customer found for this record.');
                        }

                        if ($record->status !== 'Approved') {
                            $rejectedCount++;
                            continue;
                        }

                        if (is_null($record->coupon_code)) {
                            $noCouponCount++;
                            continue;
                        }

                        Mail::to($record->customer->email)->queue(
                            new KofolCoupon(
                                $record->customer,
                                $record->coupon_code
                            )
                        );
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                    }
                }

                if ($successCount > 0) {
                    Notification::make()
                        ->title('Coupon Emails Queued')
                        ->body("Successfully queued coupon emails for {$successCount} users.")
                        ->success()
                        ->send();
                }

                $errorMessages = [];
                if ($rejectedCount > 0) {
                    $errorMessages[] = "{$rejectedCount} records were not approved";
                }
                if ($noCouponCount > 0) {
                    $errorMessages[] = "{$noCouponCount} records had no coupon code";
                }
                if ($errorCount > 0) {
                    $errorMessages[] = "{$errorCount} records failed due to other errors";
                }

                if (!empty($errorMessages)) {
                    Notification::make()
                        ->title('Some Emails Failed')
                        ->body(implode(', ', $errorMessages) . '.')
                        ->warning()
                        ->send();
                }
            });
    }
}
