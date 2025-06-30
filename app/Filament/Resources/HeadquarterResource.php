<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeadquarterResource\Pages;
use App\Models\Area;
use App\Models\Headquarter;
use App\Models\Region;
use App\Models\Division;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Get;

class HeadquarterResource extends Resource
{
    protected static ?string $model = Headquarter::class;

    protected static ?string $navigationGroup = 'Territory';

    protected static ?int $navigationSort = 1;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Select::make('division_id')
                    ->label('Division')
                    ->native(false)
                    ->reactive()
                    ->options(Division::all()->pluck('name', 'id'))
                    ->required(),
                Select::make('area_id')
                    ->label('Area')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->options(function (Get $get) {
                        return Area::where('division_id', $get('division_id'))->pluck('name', 'id');
                    })
                    ->required()
                    ->reactive(),
                TextInput::make('name')
                    ->label('Headquarter Name')
                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(Headquarter::query()->with('area.region.zone.division'))
            ->defaultSort('name', 'asc')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('area.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('area.region.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('area.region.zone.name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('division.name')->searchable()->sortable(),
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
            ->deferLoading()
            // ->striped()
            ->paginated([25, 50, 100, 250, 'all'])

            ->defaultPaginationPageOption(5)
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
            // 'create' => Pages\CreateHeadquarter::route('/create'),
            // 'edit' => Pages\EditHeadquarter::route('/{record}/edit'),
        ];
    }
    //     public static function getNavigationBadge(): ?string
    //     {
    //         return static::getModel()::count();
    // }
}
