<?php

namespace App\Traits;

use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\QueryException;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

trait HandlesDeleteExceptions
{
    /**
     * Check for related records before allowing delete. Halt and notify if found.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $record
     * @param  mixed|null $action  (optional) Filament action instance for table actions
     * @param  array|null $onlyRelationships  (optional) Only check these relationships
     * @param  array|null $ignoreRelationships (optional) Ignore these relationships
     * @return void
     */
    public function tryDeleteRecord($record, $action = null, $onlyRelationships = null, $ignoreRelationships = null)
    {
        try {
            $relationships = method_exists($record, 'getRelationsToCheckForDelete')
                ? $record->getRelationsToCheckForDelete()
                : $this->getAllRelationshipMethods($record);

            Log::info('HandlesDeleteExceptions: Checking relationships for delete', [
                'model' => get_class($record),
                'id' => $record->id ?? null,
                'relationships' => $relationships,
            ]);

            if ($onlyRelationships) {
                $relationships = array_intersect($relationships, $onlyRelationships);
            }
            if ($ignoreRelationships) {
                $relationships = array_diff($relationships, $ignoreRelationships);
            }

            foreach ($relationships as $relation) {
                if (!method_exists($record, $relation)) {
                    Log::warning('HandlesDeleteExceptions: Method does not exist', [
                        'model' => get_class($record),
                        'relation' => $relation,
                    ]);
                    continue;
                }
                $relationObj = $record->{$relation}();
                $exists = false;
                if (
                    $relationObj instanceof \Illuminate\Database\Eloquent\Relations\HasOne ||
                    $relationObj instanceof \Illuminate\Database\Eloquent\Relations\MorphOne
                ) {
                    $exists = $relationObj->exists();
                } elseif (
                    $relationObj instanceof \Illuminate\Database\Eloquent\Relations\HasMany ||
                    $relationObj instanceof \Illuminate\Database\Eloquent\Relations\MorphMany ||
                    $relationObj instanceof \Illuminate\Database\Eloquent\Relations\BelongsToMany ||
                    $relationObj instanceof \Illuminate\Database\Eloquent\Relations\MorphToMany
                ) {
                    $exists = $relationObj->exists();
                }
                Log::info('HandlesDeleteExceptions: Checked relation', [
                    'model' => get_class($record),
                    'id' => $record->id ?? null,
                    'relation' => $relation,
                    'exists' => $exists,
                ]);
                if ($exists) {
                    $this->notifyAndHalt($action, $relation);
                    return;
                }
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title('Error')
                ->body('An error occurred while checking the record.')
                ->danger()
                ->send();
            if ($action) {
                $action->halt();
            } elseif (method_exists($this, 'halt')) {
                $this->halt();
            }
        }
    }

    /**
     * Get all relationship method names for the model.
     */
    protected function getAllRelationshipMethods($model)
    {
        $reflection = new \ReflectionClass($model);
        $methods = $reflection->getMethods(\ReflectionMethod::IS_PUBLIC);
        $relationships = [];
        foreach ($methods as $method) {
            if (
                $method->class === get_class($model) &&
                $method->getNumberOfParameters() === 0 &&
                $method->name !== __FUNCTION__ &&
                !Str::startsWith($method->name, 'get') // skip accessors
            ) {
                try {
                    $return = $method->invoke($model);
                    if ($return instanceof Relation) {
                        $relationships[] = $method->name;
                    }
                } catch (\Throwable $e) {
                    // skip methods that error out
                }
            }
        }
        return $relationships;
    }

    /**
     * Notify and halt the action.
     */
    protected function notifyAndHalt($action, $relation)
    {
        $relationName = Str::headline($relation);
        Notification::make()
            ->title('Cannot delete record')
            ->body("This record is still referenced by related $relationName. Please remove related records first.")
            ->danger()
            ->send();
        if ($action) {
            $action->halt();
        } elseif (method_exists($this, 'halt')) {
            $this->halt();
        }
    }

    /**
     * Catch DB exceptions after delete and show notification if needed.
     *
     * @param  \Throwable $e
     * @return void
     */
    public function afterDeleteRecord($e)
    {
        if ($e instanceof QueryException && $e->getCode() === '23503') {
            Notification::make()
                ->title('Cannot delete record')
                ->body('This record is still referenced elsewhere. Please remove related records first.')
                ->danger()
                ->send();
        } else {
            Notification::make()
                ->title('Error')
                ->body('An error occurred while deleting the record.')
                ->danger()
                ->send();
        }
    }
} 