<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Get;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UpdateKofolStatusAction
{
    // Maximum number of attempts to generate a unique coupon code
    private static int $maxCouponAttempts = 1000;

    // Range for 7-digit coupon codes
    private static int $minCouponCode = 1000000;
    private static int $maxCouponCode = 9999999;

    private static int $bulkProcessLimit = 500; // Prevent memory issues

    public static function make(): Action
    {
        return Action::make('update_status')
            ->modalWidth('sm')
            ->label('Update Status')
            ->form([
                Select::make('status')
                    ->native(false)
                    ->label('Status')
                    ->options(fn ($record) => collect([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ])->except($record?->status)->toArray())
                    ->required()
                    ->reactive(),
                \Filament\Forms\Components\TextInput::make('num_coupons')
                    ->label('No. of Coupons')
                    ->numeric()
                    ->minValue(1)
                    ->required(fn ($get) => $get('status') === 'Approved')
                    ->visible(fn ($get) => $get('status') === 'Approved')
                    ->rule(function () {
                        return function (string $attribute, $value, \Closure $fail) {
                            if ($value > 50) {
                                $fail('Yikes! You are trying to generate too many coupons.');
                            }
                        };
                    }),
                \Filament\Forms\Components\TextInput::make('rejection_reason')
                    ->label('Reason for Rejection')
                    ->required(fn ($get) => $get('status') === 'Rejected')
                    ->visible(fn ($get) => $get('status') === 'Rejected'),
            ])
            ->action(function (array $data, $record) {
                try {
                    DB::beginTransaction();

                    $record->status = $data['status'];

                    if ($data['status'] === 'Approved') {
                        $numCoupons = (int) ($data['num_coupons'] ?? 1);
                        $generatedCodes = [];
                        for ($i = 0; $i < $numCoupons; $i++) {
                            try {
                                $couponCode = self::generateUniqueCouponCode();
                            } catch (\RuntimeException $e) {
                                DB::rollBack();
                                Notification::make()
                                    ->title('Error')
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                                return;
                            }
                            $record->coupons()->create(['coupon_code' => $couponCode]);
                            $generatedCodes[] = $couponCode;
                        }
                    }

                    // If status is set to Rejected, delete all coupons for this entry
                    if ($data['status'] === 'Rejected') {
                        $record->coupons()->delete();
                    }

                    $record->save();
                    DB::commit();

                    $message = 'Status updated to ' . $data['status'];
                    if (!empty($generatedCodes)) {
                        $message .= '. Coupon codes generated: ' . implode(', ', $generatedCodes);
                    }

                    Notification::make()
                        ->title('Status updated successfully')
                        ->body($message)
                        ->success()
                        ->send();

                    // Notify the user who created the record
                    if ($record->user) {
                        $notificationBody = 'Your Booking with amount ' . $record->invoice_amount . ' has been ' . $data['status'];
                        if ($data['status'] === 'Rejected' && !empty($data['rejection_reason'])) {
                            $notificationBody .= ". Reason: " . $data['rejection_reason'];
                        }
                        Notification::make()
                            ->title('Kofol Booking Status')
                            ->body($notificationBody)
                            ->when(
                                $data['status'] === 'Approved',
                                fn ($notification) => $notification->success(),
                                fn ($notification) => $notification->danger()
                            )
                            ->sendToDatabase($record->user);
                    }

                } catch (QueryException $e) {
                    DB::rollBack();
                    Notification::make()
                        ->title('Database Error')
                        ->body('Failed to update record. Please try again.')
                        ->danger()
                        ->send();
                } catch (\Exception $e) {
                    DB::rollBack();
                    Notification::make()
                        ->title('Unexpected Error')
                        ->body('An error occurred while updating the record.')
                        ->danger()
                        ->send();
                    Log::error('UpdateStatusAction error: ' . $e->getMessage(), [
                        'record_id' => $record->id ?? 'unknown',
                        'status' => $data['status'] ?? 'unknown',
                    ]);
                }
            })
            ->color('primary')
            ->icon('heroicon-o-arrow-path');
    }

    // Comment out bulk actions for now
    // public static function makeBulk(): BulkAction
    // {
    //     // ...
    // }

    /**
     * Generate a unique 7-digit coupon code with guaranteed uniqueness
     */
    private static function generateUniqueCouponCode(): int
    {
        // Check if we have exhausted the coupon space
        $totalPossibleCoupons = self::$maxCouponCode - self::$minCouponCode + 1;
        $existingCoupons = DB::table('kofol_entry_coupons')->count();
        
        if ($existingCoupons >= $totalPossibleCoupons) {
            throw new \RuntimeException('All possible coupon codes have been exhausted. Please contact system administrator.');
        }

        $attempts = 0;
        $maxAttempts = self::$maxCouponAttempts;

        while ($attempts < $maxAttempts) {
            try {
                $couponCode = random_int(self::$minCouponCode, self::$maxCouponCode);
                
                // Try to insert the record with a unique constraint
                $exists = DB::table('kofol_entry_coupons')
                    ->where('coupon_code', $couponCode)
                    ->lockForUpdate()
                    ->exists();
                
                if (!$exists) {
                    return $couponCode;
                }
                
                $attempts++;
            } catch (\Exception $e) {
                Log::error('Error generating coupon code: ' . $e->getMessage());
                $attempts++;
            }
        }

        throw new \RuntimeException('Failed to generate a unique coupon code after ' . $maxAttempts . ' attempts. Please try again.');
    }
}
