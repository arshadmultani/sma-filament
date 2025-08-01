<?php

namespace App\Filament\Clusters\ActivitySettings\Pages;

use App\Filament\Clusters\ActivitySettings;
use App\Settings\KofolEntrySettings;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;

class ManageKofolEntry extends SettingsPage
{

    protected static string $settings = KofolEntrySettings::class;

    protected static ?string $cluster = ActivitySettings::class;
    protected static ?string $navigationLabel = 'KSV Settings';

    public function getTitle(): string
    {
        return 'KSV Settings';
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('max_coupons')
                    ->label('Max Coupons')
                    ->helperText('Maximum coupons allowed in a single invoice. Current Max Value: ' . app(KofolEntrySettings::class)->max_coupons)
                    ->required()
                    ->numeric()
                    ->suffix('Coupons')
                    ->minValue(1)
                    ->maxValue(200),
                Forms\Components\TextInput::make('max_invoice_size')
                    ->label('Invoice File Limit')
                    ->helperText('Maximum invoice file size in MB. Current Max Value: ' . app(KofolEntrySettings::class)->max_invoice_size/1024 . ' MB')
                    ->required()
                    ->numeric()
                    ->formatStateUsing(function ($state) {
                        return $state ? $state / 1024 : null;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return $state * 1024;
                    })
                    

                    ->minValue(1)
                    ->suffix('MB')
                    ->maxValue(10),
            ]);
    }

}
