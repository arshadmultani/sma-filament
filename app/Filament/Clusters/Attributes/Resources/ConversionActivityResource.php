<?php

namespace App\Filament\Clusters\Attributes\Resources;

use App\Filament\Clusters\Attributes\Resources\ConversionActivityResource\Pages;
use App\Filament\Clusters\Attributes\Resources\ConversionActivityResource\RelationManagers;
use App\Models\ConversionActivity;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Clusters\Attributes;
use App\Traits\HandlesDeleteExceptions;

class ConversionActivityResource extends Resource
{
    use HandlesDeleteExceptions;
    protected static ?string $model = ConversionActivity::class;

    protected static ?string $cluster = Attributes::class;
    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
                Select::make('division_id')
                    ->relationship('division', 'name')
                    ->native(false)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('name', 'asc')
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('division.name'),
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
            'index' => Pages\ListConversionActivities::route('/'),
            // 'create' => Pages\CreateConversionActivity::route('/create'),
            // 'edit' => Pages\EditConversionActivity::route('/{record}/edit'),
        ];
    }
}
