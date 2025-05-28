<?php

namespace App\Filament\Clusters\Products\Resources;

use App\Filament\Clusters\Products\Resources\BrandResource\Pages;
use App\Filament\Clusters\Products;
use App\Filament\Resources\BrandResource\RelationManagers;
use App\Models\Brand;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
class BrandResource extends Resource
{
    protected static ?string $model = Brand::class;
    protected static ?string $cluster = Products::class;
    protected static ?int $navigationSort = 2;




    protected static ?string $navigationIcon = 'heroicon-o-bookmark-square';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->unique(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(['all'])
            ->columns([
                TextColumn::make('name'),
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
            'index' => Pages\ListBrands::route('/'),
            // 'create' => Pages\CreateBrand::route('/create'),
            // 'edit' => Pages\EditBrand::route('/{record}/edit'),
        ];
    }
}
