<?php

namespace App\Filament\Actions;

use Filament\Actions\Action as InfolistAction;
use Filament\Tables\Actions\Action as TableAction;

class SiteUrlAction
{
    public static function makeInfolist(): InfolistAction
    {
        return InfolistAction::make('Site')
            ->label('Visit Site')
            ->icon('heroicon-o-globe-alt')
            ->url(fn($record) => route('microsite.show', ['slug' => $record->url]))
            ->openUrlInNewTab();
    }

    public static function makeTable(): TableAction
    {
        return TableAction::make('Site')
            ->label('Site')
            ->icon('heroicon-o-link')
            ->visible(fn($record) => $record->is_active)
            ->url(fn($record) => route('microsite.show', ['slug' => $record->url]))
            ->openUrlInNewTab();
    }
}
