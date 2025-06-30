<?php

namespace App\Filament\Resources;

use App\Filament\Resources\RegionResource\Pages;
use App\Models\Region;
use App\Models\Zone;
use App\Models\Division;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Get;
use Illuminate\Support\Facades\Log;

class RegionResource extends Resource
{
    protected static ?string $model = Region::class;

    protected static ?string $navigationGroup = 'Territory';

    protected static ?int $navigationSort = 3;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Select::make('division_id')
                    ->label('Division')
                    ->options(Division::all()->pluck('name', 'id'))
                    ->native(false)
                    ->reactive()
                    ->required(),
                Select::make('zone_id')
                    ->label('Zone')
                    ->native(false)
                    ->options(function (Get $get) {
                        return Zone::where('division_id', $get('division_id'))->pluck('name', 'id');
                    })
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Region::query()->with('zone.division'))
            ->defaultSort('name', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('zone.name')->searchable()->sortable(),
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
            'index' => Pages\ListRegions::route('/'),
            // 'create' => Pages\CreateRegion::route('/create'),
            // 'edit' => Pages\EditRegion::route('/{record}/edit'),
        ];
    }
    //     public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
