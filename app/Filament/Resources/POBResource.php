<?php

namespace App\Filament\Resources;

use App\Filament\Resources\POBResource\Pages;
use App\Filament\Resources\POBResource\RelationManagers;
use App\Models\POB;
use App\Models\Campaign;
use App\Models\Doctor;
use App\Models\Chemist;
use Filament\Forms;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Select;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class POBResource extends Resource
{
    protected static ?string $model = POB::class;

    protected static ?string $navigationGroup = 'Activities';

    protected static ?string $modelLabel = 'POB';

    protected static ?string $pluralLabel = 'POB';

    protected static ?string $slug = 'pob';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('campaign_id')
                    ->label('Campaign')
                    ->placeholder('Select Campaign')
                    ->dehydrated()
                    ->reactive()
                    ->native(false)
                    ->preload()
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return Campaign::getForEntryType('pob');
                    }),
                MorphToSelect::make('customer')
                    ->types([
                        MorphToSelect\Type::make(Doctor::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn($query) => $query->where('status', 'Approved')),
                        MorphToSelect\Type::make(Chemist::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn($query) => $query->where('status', 'Approved')),
                    ])
                    ->native(false)
                    ->searchable()
                    ->optionsLimit(10)
                    ->preload() // this is causing the issue for admin in 5L+ entries are there.. fix this later
                    ->required(),
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
            'index' => Pages\ListPOBS::route('/'),
            'create' => Pages\CreatePOB::route('/create'),
            'edit' => Pages\EditPOB::route('/{record}/edit'),
        ];
    }
}
