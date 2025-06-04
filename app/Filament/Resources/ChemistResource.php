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
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;


class ChemistResource extends Resource
{
    protected static ?string $model = Chemist::class;
    protected static ?string $navigationGroup = 'Customer';

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('phone')->required()->tel(),
                TextInput::make('email')->email()->unique(),
                TextInput::make('address'),
                TextInput::make('town')
                    ->required(),
                Select::make('type')
                    ->native(false)
                    ->options(['Ayurvedic' => 'Ayurvedic', 'Allopathic' => 'Allopathic'])
                    ->required(),
                Select::make('headquarter_id')
                    ->relationship('headquarter', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('headquarter.name')
                    ->toggleable()
                    ->label('Location')
                    ->searchable(),
                TextColumn::make('town')->toggleable(),
                TextColumn::make('type')->toggleable(),
                TextColumn::make('address'),
                TextColumn::make('phone')->toggleable(),
                TextColumn::make('email')->toggleable(),
                TextColumn::make('user.name')->label('Created By'),
                TextColumn::make('created_at')->since()->toggleable(),
                TextColumn::make('updated_at')->since()->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                    ]),
                    Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('address'),
                        TextEntry::make('town')->label('Area'),
                        TextEntry::make('headquarter.name')->label('Region'),
                    ]),
                    Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name'),
                        TextEntry::make('created_at')->since()->label('Created'),
                        TextEntry::make('updated_at')->since()->label('Updated'),
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
            'view' => Pages\ViewChemist::route('/{record}')
        ];
    }
    //     public static function getNavigationBadge(): ?string
// {
//     return static::getModel()::count();
// }
}
