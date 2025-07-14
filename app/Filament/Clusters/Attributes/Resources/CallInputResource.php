<?php

namespace App\Filament\Clusters\Attributes\Resources;

use App\Filament\Clusters\Attributes;
use App\Filament\Clusters\Attributes\Resources\CallInputResource\Pages;
use App\Filament\Clusters\Attributes\Resources\CallInputResource\RelationManagers;
use App\Models\CallInput;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\HandlesDeleteExceptions;

class CallInputResource extends Resource
{
    use HandlesDeleteExceptions;
    protected static ?string $model = CallInput::class;

    protected static ?string $navigationIcon = 'heroicon-o-square-3-stack-3d';

    protected static ?string $cluster = Attributes::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([
                // TextFilter::make('name'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn($action, $record) => (new static())->tryDeleteRecord($record, $action)),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     // Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListCallInputs::route('/'),
            // 'create' => Pages\CreateCallInput::route('/create'),
            // 'edit' => Pages\EditCallInput::route('/{record}/edit'),
        ];
    }
}
