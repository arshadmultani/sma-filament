<?php

namespace App\Filament\Clusters\Attributes\Resources;

use App\Filament\Clusters\Attributes;
use App\Filament\Clusters\Attributes\Resources\TagResource\Pages;
use App\Filament\Clusters\Attributes\Resources\TagResource\RelationManagers;
use App\Models\Tag;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Traits\HandlesDeleteExceptions;

class TagResource extends Resource
{
    use HandlesDeleteExceptions;
    protected static ?string $model = Tag::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';

    protected static ?string $cluster = Attributes::class;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->helperText('Set short,unique name for the tag')
                    ->unique(ignoreRecord: true),
                Forms\Components\Select::make('attached_to')
                    ->label('Attach to Customer')
                    ->native(false)
                    ->helperText('Makes tags attachable to this customer only')
                    ->options([
                        'doctor' => 'Doctor',
                        'chemist' => 'Chemist',
                    ])
                    ->preload()
                    ->required(),
                Forms\Components\Select::make('divisions')
                    ->label('Attach to Division')
                    ->native(false)
                    ->multiple()
                    ->relationship('divisions', 'name')
                    ->preload()
                    ->helperText('NOTE: 1. Makes tags visible to this division users only. 2. Avoid editing this after creation to prevent data corruption')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color('warning')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('attached_to')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('divisions.name')
                    ->label('Division')
                    ->searchable()
                    ->sortable(),
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
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListTags::route('/'),
            // 'create' => Pages\CreateTag::route('/create'),
            // 'edit' => Pages\EditTag::route('/{record}/edit'),
        ];
    }
}
