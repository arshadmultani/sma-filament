<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HeadquarterResource\Pages;
use App\Filament\Resources\HeadquarterResource\RelationManagers;
use App\Models\Headquarter;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            'index' => Pages\ListHeadquarters::route('/'),
            'create' => Pages\CreateHeadquarter::route('/create'),
            'edit' => Pages\EditHeadquarter::route('/{record}/edit'),
        ];
    }
}
