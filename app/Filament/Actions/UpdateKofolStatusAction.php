<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class UpdateKofolStatusAction
{
    private static int $maxCouponAttempts = 100;

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
                    ->native(false)
                    ->options(fn ($record) => collect([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ])->except($record?->status)->toArray())
                    ->required(),
            ])
            ->action(function (array $data, $record) {
                try {
                    DB::beginTransaction();

                    $record->status = $data['status'];
                    $couponGenerated = false;
                    $generatedCouponCode = null;

                    if ($data['status'] === 'Approved' && empty($record->coupon_code)) {
                        $couponCode = self::generateUniqueCouponCode(get_class($record));

                        if ($couponCode === null) {
                            DB::rollBack();
                            Notification::make()
                                ->title('Error')
                                ->body('Unable to generate unique coupon code after multiple attempts')
                                ->danger()
                                ->send();

                            return;
                        }

                        $record->coupon_code = $couponCode;
                        $couponGenerated = true;
                        $generatedCouponCode = $couponCode;
                    }

                    $record->save();
                    DB::commit();

                    // Dynamic success message
                    $message = 'Status updated to '.$data['status'];
                    if ($couponGenerated) {
                        $message .= '. Coupon code generated: '.$generatedCouponCode;
                    }

                    Notification::make()
                        ->title('Status updated successfully')
                        ->body($message)
                        ->success()
                        ->send();

                } catch (QueryException $e) {
                    DB::rollBack();

                    // Handle duplicate coupon code constraint violation
                    if (str_contains($e->getMessage(), 'coupon_code')) {
                        Notification::make()
                            ->title('Error')
                            ->body('Coupon code conflict detected. Please try again.')
                            ->danger()
                            ->send();
                    } else {
                        Notification::make()
                            ->title('Database Error')
                            ->body('Failed to update record. Please try again.')
                            ->danger()
                            ->send();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();

                    Notification::make()
                        ->title('Unexpected Error')
                        ->body('An error occurred while updating the record.')
                        ->danger()
                        ->send();

                    // Log the error for debugging
                    Log::error('UpdateStatusAction error: '.$e->getMessage(), [
                        'record_id' => $record->id ?? 'unknown',
                        'status' => $data['status'] ?? 'unknown',
                    ]);
                }
            })
            ->color('primary')
            ->icon('heroicon-o-arrow-path');
    }

    public static function makeBulk(): BulkAction
    {
        return BulkAction::make('update_status')
            ->modalWidth('sm')
            ->label('Update Status')
            ->visible(fn () => Gate::allows('update_status_kofol::entry'))
            ->form([
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ])
                    ->required()
                    ->native(false),
            ])
            ->action(function (array $data, $records) {
                $recordsCollection = $records instanceof \Illuminate\Database\Eloquent\Collection
                    ? $records
                    : collect($records);

                $totalRecords = $recordsCollection->count();

                // Prevent memory issues with large datasets
                if ($totalRecords > self::$bulkProcessLimit) {
                    Notification::make()
                        ->title('Bulk Limit Exceeded')
                        ->body('Please select fewer than '.self::$bulkProcessLimit.' records at a time.')
                        ->warning()
                        ->send();

                    return;
                }

                $couponsGenerated = 0;
                $failedCoupons = 0;
                $failedUpdates = 0;
                $modelClass = $recordsCollection->first() ? get_class($recordsCollection->first()) : null;

                try {
                    DB::beginTransaction();

                    foreach ($recordsCollection as $record) {
                        try {
                            $record->status = $data['status'];

                            if ($data['status'] === 'Approved' && empty($record->coupon_code)) {
                                $couponCode = self::generateUniqueCouponCode($modelClass);

                                if ($couponCode !== null) {
                                    $record->coupon_code = $couponCode;
                                    $couponsGenerated++;
                                } else {
                                    $failedCoupons++;
                                }
                            }

                            $record->save();

                        } catch (\Exception $e) {
                            $failedUpdates++;
                            Log::error('Bulk update failed for record: '.($record->id ?? 'unknown'), [
                                'error' => $e->getMessage(),
                            ]);
                        }
                    }

                    DB::commit();

                } catch (\Exception $e) {
                    DB::rollBack();

                    Notification::make()
                        ->title('Bulk Update Failed')
                        ->body('Failed to update records. Please try again.')
                        ->danger()
                        ->send();

                    return;
                }

                // Build comprehensive success message
                $message = "Status updated to {$data['status']} for ".($totalRecords - $failedUpdates)." of {$totalRecords} records.";

                if ($couponsGenerated > 0) {
                    $message .= " {$couponsGenerated} coupon codes generated.";
                }

                if ($failedCoupons > 0) {
                    $message .= " {$failedCoupons} coupon codes failed to generate.";
                }

                if ($failedUpdates > 0) {
                    $message .= " {$failedUpdates} records failed to update.";
                }

                $notificationType = ($failedCoupons > 0 || $failedUpdates > 0) ? 'warning' : 'success';
                $notificationTitle = ($failedCoupons > 0 || $failedUpdates > 0) ? 'Bulk update completed with issues' : 'Bulk update successful';

                Notification::make()
                    ->title($notificationTitle)
                    ->body($message)
                    ->{$notificationType}()
                    ->send();
            })
            ->color('primary')
            ->icon('heroicon-o-arrow-path');
    }

    /**
     * Generate a unique coupon code with proper model handling
     */
    private static function generateUniqueCouponCode(?string $modelClass = null): ?int
    {
        if (! $modelClass) {
            return null;
        }

        $attempts = 0;

        do {
            $couponCode = random_int(100000, 999999);
            $attempts++;

            try {
                $exists = $modelClass::where('coupon_code', $couponCode)->exists();
            } catch (\Exception $e) {
                // If query fails, assume it exists to be safe
                $exists = true;
            }

        } while ($exists && $attempts < self::$maxCouponAttempts);

        return $attempts >= self::$maxCouponAttempts ? null : $couponCode;
    }
}
