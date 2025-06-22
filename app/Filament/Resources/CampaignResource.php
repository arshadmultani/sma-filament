<?php

namespace App\Filament\Resources;

use App\Contracts\IsCampaignEntry;
use App\Filament\Resources\CampaignResource\Pages;
use App\Filament\Resources\CampaignResource\RelationManagers;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Relations\Relation;

class CampaignResource extends Resource
{
    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->compact()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('allowed_entry_type')
                            ->options(function () {
                                $entryableModels = [];
                                foreach (Relation::morphMap() as $alias => $class) {
                                    if (in_array(IsCampaignEntry::class, class_implements($class))) {
                                        $entryableModels[$alias] = class_basename($class);
                                    }
                                }
                                return $entryableModels;
                            })
                            ->native(false)
                            ->searchable()
                            ->helperText('Optional. If selected, this campaign will only accept entries of this type.'),

                    ]),
                Section::make()
                ->compact()
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->minDate(fn (Forms\Get $get) => $get('start_date') ? \Carbon\Carbon::parse($get('start_date'))->addDay() : null
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('start_date')
                    ->date('d F Y'),
                TextColumn::make('end_date')
                    ->date('d F Y'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'primary',
                        'Upcoming' => 'warning',
                        'Completed' => 'info',
                        default => 'gray'
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Active' => 'heroicon-o-arrow-path',
                        'Upcoming' => 'heroicon-o-clock',
                        'Completed' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle'
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
        ];
    }
}
