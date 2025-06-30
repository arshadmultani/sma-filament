<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AreaResource\Pages;
use App\Models\Area;
use App\Models\Division;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;

class AreaResource extends Resource
{
    protected static ?string $model = Area::class;

    protected static ?string $navigationGroup = 'Territory';

    protected static ?int $navigationSort = 2;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Select::make('division_id')
                    ->label('Division')
                    ->native(false)
                    ->options(Division::all()->pluck('name', 'id'))
                    ->reactive()
                    ->required(),
                Select::make('region_id')
                    ->label('Region')
                    ->native(false)
                    ->reactive()
                    ->options(function (Get $get) {
                        return Region::where('division_id', $get('division_id'))->pluck('name', 'id');
                    })
                    ->required(),
                TextInput::make('name')
                    ->label('Area Name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Area::query()->with('region.zone.division'))
            ->defaultSort('name', 'asc')
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
            // 'create' => Pages\CreateArea::route('/create'),
            // 'edit' => Pages\EditArea::route('/{record}/edit'),
        ];
    }
    //     public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
