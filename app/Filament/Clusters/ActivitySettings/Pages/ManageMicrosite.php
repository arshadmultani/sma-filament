<?php

namespace App\Filament\Clusters\ActivitySettings\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use App\Settings\MicrositeSettings;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use App\Filament\Clusters\ActivitySettings;

class ManageMicrosite extends SettingsPage
{
    // protected static ?string $navigationIcon = 'heroicon-o-cog-6-tooth';

    protected static string $settings = MicrositeSettings::class;

    protected static ?string $cluster = ActivitySettings::class;

    protected static ?string $navigationLabel = 'Microsite Settings';


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Showcase Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('max_showcase_video_size')
                            ->label('Showcase Video Size Limit')
                            ->helperText('Maximum showcase video size in MB. Current Max Value: ' . app(MicrositeSettings::class)->max_showcase_video_size / 1024 . ' MB')
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
                        TextInput::make('max_showcase_image_size')
                            ->label('Showcase Image Size Limit')
                            ->helperText('Maximum showcase image size in MB. Current Max Value: ' . app(MicrositeSettings::class)->max_showcase_image_size / 1024 . ' MB')
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
                        TextInput::make('showcase_count')
                            ->label('No. of Showcases allowed')
                            ->placeholder('No. of showcases can be uploaded')
                            ->helperText('Current Value: ' . app(MicrositeSettings::class)->showcase_count)
                            ->required()
                            ->numeric()
                            ->prefix('Max')
                            ->suffix('Showcases')
                            ->minValue(1)
                            ->maxValue(50),
                    ]),
                Section::make('Review Settings')
                    ->columns(2)
                    ->schema([
                        TextInput::make('max_review_video_size')
                            ->label('Review Video Size Limit')
                            ->helperText('Maximum review video size in MB. Current Max Value: ' . app(MicrositeSettings::class)->max_review_video_size / 1024 . ' MB')
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
                        TextInput::make('max_review_image_size')
                            ->label('Review Image Size Limit')
                            ->helperText('Maximum review image size in MB. Current Max Value: ' . app(MicrositeSettings::class)->max_review_image_size / 1024 . ' MB')
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
                        TextInput::make('review_count')
                            ->label('No. of Reviews allowed')
                            ->placeholder('No. of reviews can be uploaded')
                            ->helperText('Current Value: ' . app(MicrositeSettings::class)->review_count)
                            ->required()
                            ->numeric()
                            ->prefix('Max')
                            ->suffix('Reviews')
                            ->minValue(1)
                            ->maxValue(50),

                    ]),
                Section::make('Portal Request Settings')
                    ->columns(1)
                    ->schema([
                        KeyValue::make('panel_access_reasons')
                            ->label('Portal Request Reasons')
                            ->addActionLabel('Add Reason')
                            ->keyLabel('Reason Key')
                            ->valueLabel('Reason Description')
                            ->helperText('Define reasons for panel access. E.g: interest_shown => Doctor has shown interest')
                            ->disableAddingRows(false)
                            ->disableEditingKeys(false)
                            ->disableDeletingRows(true)
                            ->rules(['array', 'min:1', 'max:10'])
                    ])

            ]);
    }
}
