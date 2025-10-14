<?php

namespace App\Filament\Actions;

use Filament\Forms\Components\ColorPicker;
use Filament\Infolists\Components\Actions\Action;
use Filament\Resources\RelationManagers\RelationManager;

class SetMicrositeDesignSettings
{

    public static function make(): Action
    {
        return Action::make('design_settings')
            ->label('Customize Website')
            ->outlined()
            ->icon('heroicon-o-swatch')
            ->slideOver()
            ->modalIcon('heroicon-o-swatch')
            ->modalHeading('Website Design Settings')
            ->modalWidth('2xl')
            ->form([
                ColorPicker::make('design_settings.bg_color')
                    ->label('Website Background Color')
                    ->placeholder('Select website background color')
                    ->default(fn($record) => $record->bg_color ?? null),
            ])
            ->action(function (array $data, $record) {
                $record->update([
                    'design_settings' => $data['design_settings'],
                ]);
            });
    }
}