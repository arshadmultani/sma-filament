<?php

namespace App\Filament\Clusters\ActivityStatus\Resources;

use App\Filament\Clusters\ActivityStatus;
use App\Filament\Clusters\ActivityStatus\Resources\StatusResource\Pages;
use App\Filament\Clusters\ActivityStatus\Resources\StatusResource\RelationManagers;
use App\Models\Status;
use App\Enums\StatusCategory;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Illuminate\Support\Str;


class StatusResource extends Resource
{
    protected static ?string $model = Status::class;

    protected static ?string $navigationLabel = 'Activity Status';

    protected static ?string $cluster = ActivityStatus::class;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->placeholder('Status Name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),
                Select::make('color')
                    ->label('Status Colour')
                    ->options([
                        'danger' => 'Red',
                        'success' => 'Green',
                        'info' => 'Blue',
                        'warning' => 'Yellow',
                    ])
                    ->native(false)
                    ->placeholder('Set status colour')
                    ->required(),
                Toggle::make('is_active')
                    ->label(function ($state) {
                        return $state ? 'Active' : 'Inactive';
                    })
                    ->live()
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true),
                Select::make('category')
                    ->options(StatusCategory::class)
                    ->native(false)
                    ->required(),
                TextInput::make('sort_order')
                    ->label('Sort Order')
                    ->numeric()
                    ->minValue(1)
                    ->required(),
                TextInput::make('slug')
                    ->label('Slug')
                    ->disabled()
                    ->dehydrated()
                    ->unique(ignoreRecord: true)

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('color'),
                Tables\Columns\BooleanColumn::make('is_active'),
                Tables\Columns\TextColumn::make('category'),
                Tables\Columns\TextColumn::make('sort_order'),
                Tables\Columns\TextColumn::make('slug'),

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
            'index' => Pages\ListStatuses::route('/'),
            // 'create' => Pages\CreateStatus::route('/create'),
            // 'edit' => Pages\EditStatus::route('/{record}/edit'),
        ];
    }
}
