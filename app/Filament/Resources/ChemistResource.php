<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ChemistResource\Pages;
use App\Filament\Resources\ChemistResource\RelationManagers;
use App\Models\Chemist;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ChemistResource extends Resource
{
    protected static ?string $model = Chemist::class;
    protected static ?string $navigationGroup = 'Customer';

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('phone')->required()->tel(),
                Forms\Components\TextInput::make('email')->email()->unique(),
                Forms\Components\TextInput::make('address'),
                Forms\Components\Select::make('headquarter_id')
                    ->relationship('headquarter', 'name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('address'),
                Tables\Columns\TextColumn::make('headquarter.name'),
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
            'index' => Pages\ListChemists::route('/'),
            'create' => Pages\CreateChemist::route('/create'),
            'edit' => Pages\EditChemist::route('/{record}/edit'),
        ];
    }
    public static function getNavigationBadge(): ?string
{
    return static::getModel()::count();
}
}
