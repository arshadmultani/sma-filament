<?php

namespace App\Filament\Clusters\Attributes\Resources;

use App\Filament\Clusters\Attributes\Resources\SpecialtyResource\Pages;
use App\Models\Specialty;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Clusters\Attributes;
use App\Traits\HandlesDeleteExceptions;

class SpecialtyResource extends Resource
{
    use HandlesDeleteExceptions;
    protected static ?string $model = Specialty::class;

    protected static ?string $cluster = Attributes::class;
    protected static ?string $navigationIcon = 'heroicon-o-book-open';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->label('Name'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn($action, $record) => (new static())->tryDeleteRecord($record, $action)),
            ])
            ->bulkActions([
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
            'index' => Pages\ListSpecialties::route('/'),
            // 'create' => Pages\CreateSpecialty::route('/create'),
            // 'edit' => Pages\EditSpecialty::route('/{record}/edit'),
        ];
    }
}
