<?php

namespace App\Filament\Actions;

use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Infolist;

class ViewInfoAction
{
    /**
     * Build a reusable "View" action for infolists.
     *
     * @param  string  $name          Action name (e.g. "viewDoctor")
     * @param  string  $relation      The relation or attribute to load
     * @param  string  $resourceClass Filament resource class (e.g. DoctorResource::class)
     */
    public static function make(string $name, string $relation, string $resourceClass, ?string $label = null): Action
    {
        return Action::make($name)
            ->icon('heroicon-o-eye')
            ->label($label ?? 'View')
            ->modalSubmitAction(false)
            ->modalCancelAction(false)
            ->modalContent(
                fn($record) =>
                $resourceClass::infolist(new Infolist())
                    ->record($record->{$relation})
            );
    }

    /**
     * Shortcut for when action name should default to "view{Relation}".
     */
    public static function for(string $relation, string $resourceClass, ?string $label = null): Action
    {
        $name = 'view' . ucfirst($relation);

        return self::make($name, $relation, $resourceClass, $label);
    }
}
