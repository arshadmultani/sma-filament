<?php

namespace App\Filament\Clusters\ActivitySettings\Pages;

use Filament\Forms;
use App\Models\State;
use Filament\Forms\Form;
use App\Enums\StateCategory;
use App\Settings\POBSettings;
use Filament\Pages\SettingsPage;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\ActivitySettings;
use EightyNine\FilamentAdvancedWidget\AdvancedStatsOverviewWidget\Stat;

class ManagePOB extends SettingsPage
{
    // protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = POBSettings::class;

    protected static ?string $cluster = ActivitySettings::class;

    protected static ?string $navigationLabel = 'POB Settings';

    public function getTitle(): string
    {
        return 'POB Settings';
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('max_invoice_size')
                    ->label('Invoice File Size Limit')
                    ->helperText('Maximum invoice file size in MB. Current Max Value: ' . app(POBSettings::class)->max_invoice_size / 1024 . ' MB')
                    ->required()
                    ->numeric()
                    ->minValue(1)
                    ->suffix('MB')
                    ->maxValue(10)
                    ->formatStateUsing(function ($state) {
                        return $state ? $state / 1024 : null;
                    })
                    ->dehydrateStateUsing(function ($state) {
                        return $state * 1024;
                    }),

                TextInput::make('max_invoices')
                    ->label('Maximum Invoices')
                    ->placeholder('No. of image files can be uploaded')
                    ->helperText('Current Value: ' . app(POBSettings::class)->max_invoices)
                    ->required()
                    ->numeric()
                    ->suffix('No. of Images')
                    ->minValue(1)
                    ->maxValue(4),
                Select::make('start_state')
                    ->helperText('The default status of Document when it is sumbitted')
                    ->placeholder('Select a status')
                    ->native(false)
                    ->options(State::query()
                        ->where('category', StateCategory::PENDING)
                        ->pluck('name', 'id'))
            ]);
    }
}
