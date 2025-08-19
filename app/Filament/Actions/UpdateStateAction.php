<?php

namespace App\Filament\Actions;

use App\Enums\StateCategory;
use App\Models\State;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateStateAction
{
    private static int $bulkProcessLimit = 500; // Prevent memory issues

    public static function make(): Action
    {
        return Action::make('update_state')
            ->modalWidth('sm')
            ->label('Update Status')
            ->form([
                Select::make('state_id')
                    ->native(false)
                    ->label('Status')
                    ->options(fn($record) => State::query()
                        ->where('is_active', true)
                        ->where('id', '!=', $record?->state_id)
                        ->whereNot('category', StateCategory::DRAFT)
                        ->pluck('name', 'id')
                        ->toArray())
                    ->required(),
            ])
            ->action(function (array $data, $record) {
                try {
                    DB::beginTransaction();

                    $record->state_id = $data['state_id'];
                    $record->save();
                    DB::commit();

                    $newState = State::find($data['state_id']);

                    Notification::make()
                        ->title('Status updated successfully')
                        ->body('Status updated to ' . $newState->name)
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
                    Log::error('UpdateStateAction error: ' . $e->getMessage(), [
                        'record_id' => $record->id ?? 'unknown',
                        'state_id' => $data['state_id'] ?? 'unknown',
                    ]);
                }
            })
            ->color('primary')
            ->icon('heroicon-o-arrow-path');
    }

    public static function makeBulk(): BulkAction
    {
        return BulkAction::make('update_state')
            ->modalWidth('sm')
            ->label('Update Status')
            ->form([
                Select::make('state_id')
                    ->label('Status')
                    ->options(State::query()
                        ->where('is_active', true)
                        ->whereNot('category', StateCategory::DRAFT)
                        ->pluck('name', 'id')
                        ->toArray())
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
                        ->body('Please select fewer than ' . self::$bulkProcessLimit . ' records at a time.')
                        ->warning()
                        ->send();

                    return;
                }

                $failedUpdates = 0;
                $newState = State::find($data['state_id']);

                try {
                    DB::beginTransaction();

                    foreach ($recordsCollection as $record) {
                        try {
                            $record->state_id = $data['state_id'];
                            $record->save();

                        } catch (\Exception $e) {
                            $failedUpdates++;
                            Log::error('Bulk update failed for record: ' . ($record->id ?? 'unknown'), [
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
                $message = "Status updated to {$newState->name} for " . ($totalRecords - $failedUpdates) . " of {$totalRecords} records.";

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
