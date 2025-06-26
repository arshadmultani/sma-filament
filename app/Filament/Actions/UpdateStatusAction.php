<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateStatusAction
{
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
                        'Approved' => 'Approve',
                        'Rejected' => 'Reject',
                    ])->except($record?->status)->toArray())
                    ->required(),
            ])
            ->action(function (array $data, $record) {
                try {
                    DB::beginTransaction();

                    $record->status = $data['status'];
                    $record->save();
                    DB::commit();

                    Notification::make()
                        ->title('Status updated successfully')
                        ->body('Status updated to '.$data['status'])
                        ->success()
                        ->send();

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
            ->hidden(fn () => Auth::user()->hasRole(['DSA', 'ASM']))
            ->modalWidth('sm')
            ->label('Update Status')
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

                $failedUpdates = 0;

                try {
                    DB::beginTransaction();

                    foreach ($recordsCollection as $record) {
                        try {
                            $record->status = $data['status'];
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

                // Build success message
                $message = "Status updated to {$data['status']} for ".($totalRecords - $failedUpdates)." of {$totalRecords} records.";

                if ($failedUpdates > 0) {
                    $message .= " {$failedUpdates} records failed to update.";
                }

                $notificationType = $failedUpdates > 0 ? 'warning' : 'success';
                $notificationTitle = $failedUpdates > 0 ? 'Bulk update completed with issues' : 'Bulk update successful';

                Notification::make()
                    ->title($notificationTitle)
                    ->body($message)
                    ->{$notificationType}()
                    ->send();
            })
            ->color('primary')
            ->icon('heroicon-o-arrow-path');
    }
}
