<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Models\Area;
use App\Models\Division;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationGroup = 'Territory';

    protected static ?int $navigationSort = 2;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                \Filament\Forms\Components\Select::make('division_id')
                    ->label('Division')
                    ->options(Division::all()->pluck('name', 'id'))
                    ->required(),
                Forms\Components\Select::make('region_id')
                    ->label('Region')
                    ->options(\App\Models\Region::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->required(),
                Forms\Components\TextInput::make('name')
                    ->label('Area Name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
        ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('region.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('region.zone.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('division.name')->searchable()->sortable(),

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
            'index' => Pages\ListAreas::route('/'),
            'create' => Pages\CreateArea::route('/create'),
            'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
    //     public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
