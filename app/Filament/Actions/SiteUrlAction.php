<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Tables\Actions\Action as TableAction;
use Filament\Infolists\Components\Actions\Action as InfoAction;


class SiteUrlAction
{
    public static function make(): Action
    {
        return self::baseConfig(Action::make('Site'));
    }
    public static function makeTable(): TableAction
    {
        return self::baseConfig(
            TableAction::make('Site')
                ->label('Site')
                ->icon('heroicon-o-link'),
        );
    }
    public static function makeAction(): InfoAction
    {
        return self::baseConfig(InfoAction::make('Site'));
    }
    private static function baseConfig($action): mixed
    {
        return $action
            ->label('Visit Site')
            ->icon('heroicon-o-globe-alt')
            ->url(fn($record) => route('microsite.show', ['slug' => $record->url]))
            ->openUrlInNewTab();
    }
}
