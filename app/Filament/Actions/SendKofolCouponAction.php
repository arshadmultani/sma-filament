<?php

namespace App\Filament\Actions;

use App\Mail\KofolCoupon;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Notifications\KofolCouponNotification;
use Illuminate\Support\Facades\Log;

class SendKofolCouponAction
{
    // Single Record Action
    public static function make(): Action
    {
        return Action::make('sendKofolCoupon')
            ->label('Send Mail')
            ->icon('heroicon-m-envelope')
            ->requiresConfirmation()
            ->modalHeading('Send Kofol Coupon')
            ->modalDescription(function ($record) {
                $to = $record->customer?->email ?? 'N/A';
                $cc = [];
                $owner = $record->customer->user ?? null;
                
                if ($owner) {
                    // Always CC the owner
                    // -------------------------Later remove DSA from CC ---------------------------
                    $cc[] = $owner->email;
                    $managers = $owner->getManagers();
                    // Only add manager CCs if there is at least one manager (i.e., not DSA)
                    if (count($managers) > 0) {
                        foreach (array_slice($managers, 0, 2) as $manager) {
                            $cc[] = $manager->email;
                        }
                    }
                }
                Log::info('CC Emails', ['cc' => $cc]);
                $ccString = $cc ? ("\n    " . implode(",\n    ", $cc)) : 'None';
                return "Are you sure you want to send the coupon email?\n\nTo:    {$to}\nCC:   {$ccString}";
            })
            ->modalSubmitActionLabel('Yes, send it')
            ->action(function ($record) {
                try {
                    $couponCodes = $record->coupons ? $record->coupons->pluck('coupon_code')->toArray() : [];
                    if (! $record->customer) {
                        throw new \Exception('No customer found for this record.');
                    }
                    if (empty($couponCodes)) {
                        throw new \Exception('No coupon codes found for this record.');
                    }
                    Log::info('Sending KofolCouponNotification', ['customer_id' => $record->customer->id]);

                    $record->customer->notify(
                        new KofolCouponNotification(
                            $record->customer,
                            $record->coupons->pluck('coupon_code')->toArray(),
                            $record->id
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
                        ->body('Failed to send coupon email: '.$e->getMessage())
                        ->danger()
                        ->send();
                        Log::error('Failed to send coupon email', [
                            'customer_id' => $record->customer->id ?? null,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
                }
            });
    }

    // Bulk Action
    // This is not working as expected, only the first record is being sent, rest are not being sent. FIX IT LATER
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
                        if (! $record->customer) {
                            throw new \Exception('No customer found for this record.');
                        }

                        $couponCodes = $record->coupons ? $record->coupons->pluck('coupon_code')->toArray() : [];

                        if ($record->status !== 'Approved') {
                            $rejectedCount++;
                            continue;
                        }

                        if (empty($couponCodes)) {
                            $noCouponCount++;
                            continue;
                        }

                        // Determine CC emails based on the record owner role
                        $ccEmails = [];
                        $owner = $record->customer->user ?? null;
                        if ($owner) {
                            if ($owner->hasRole('DSA')) {
                                $managers = $owner->getManagers();
                                if (isset($managers['ASM'])) {
                                    $ccEmails[] = $managers['ASM']->email;
                                }
                                if (isset($managers['RSM'])) {
                                    $ccEmails[] = $managers['RSM']->email;
                                }
                            } elseif ($owner->hasRole('ASM')) {
                                $ccEmails[] = $owner->email;
                                $managers = $owner->getManagers();
                                if (isset($managers['RSM'])) {
                                    $ccEmails[] = $managers['RSM']->email;
                                }
                            } elseif ($owner->hasRole('RSM')) {
                                $ccEmails[] = $owner->email;
                            }
                        }
                        Log::info('Sending KofolCouponNotification (bulk)', ['customer_id' => $record->customer->id, 'cc' => $ccEmails]);
                        $record->customer->notify(
                            new KofolCouponNotification(
                                $record->customer,
                                $record->coupons->pluck('coupon_code')->toArray(),
                                $record->id,
                                $ccEmails
                            )
                        );
                        $successCount++;
                    } catch (\Exception $e) {
                        $errorCount++;
                        Log::error('Failed to send coupon email (bulk)', [
                            'customer_id' => $record->customer->id ?? null,
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                        ]);
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

                if (! empty($errorMessages)) {
                    Notification::make()
                        ->title('Some Emails Failed')
                        ->body(implode(', ', $errorMessages).'.')
                        ->warning()
                        ->send();
                }
            });
    }
}
