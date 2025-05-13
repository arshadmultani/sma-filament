<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeadquarterResource\Pages;
use App\Filament\Resources\HeadquarterResource\RelationManagers;
use App\Models\Headquarter;
use App\Models\Area;
use App\Models\Region;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HeadquarterResource extends Resource
{
    protected static ?string $model = Headquarter::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('region_id')
                    ->label('Region')
                    ->options(Region::all()->pluck('name', 'id')->toArray())
                    ->reactive()
                    ->required(),
                Forms\Components\Select::make('area_id')
                    ->label('Area')
                    ->options(function (callable $get) {
                        $regionId = $get('region_id');
                        if (!$regionId) return [];
                        return Area::where('region_id', $regionId)->pluck('name', 'id')->toArray();
                    })
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(fn ($set) => $set('headquarter_id', null)),
                Forms\Components\TextInput::make('name')
                    ->label('Headquarter Name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('area.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('area.region.name')->searchable()->sortable(),
            ])
            ->filters([
                SelectFilter::make('area.region')
                    ->relationship('area.region', 'name')
                    ->label('Region'),
                // SelectFilter::make('area')
                //     ->relationship('area', 'name')
                //     ->query(function (Builder $query, array $data): Builder {
                //         return isset($data['area.region']) && $data['area.region']
                //             ? $query->where('region_id', $data['area.region'])
                //             : $query;
                //     }),
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListHeadquarters::route('/'),
            'create' => Pages\CreateHeadquarter::route('/create'),
            'edit' => Pages\EditHeadquarter::route('/{record}/edit'),
        ];
    }
}
