<?php

namespace App\Filament\Clusters\Attributes\Resources;

use App\Filament\Clusters\Attributes\Resources\QualificationResource\Pages;
use App\Models\Qualification;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use App\Filament\Clusters\Attributes;

class QualificationResource extends Resource
{
    protected static ?string $model = Qualification::class;
    protected static ?string $navigationIcon = 'heroicon-o-academic-cap';

    protected static ?string $cluster = Attributes::class;
    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                Select::make('category')->required()->native(false)
                    ->options(fn () => Qualification::distinct()->pluck('category', 'category')->toArray())
                    ->createOptionForm([
                        TextInput::make('name')
                            ->label('New Category')
                            ->required(),
                    ])
                    ->createOptionUsing(function (array $data): string {
                        return $data['name'];
                    }),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('category'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
            'index' => Pages\ListQualifications::route('/'),
            // 'create' => Pages\CreateQualification::route('/create'),
            // 'edit' => Pages\EditQualification::route('/{record}/edit'),
        ];
    }
}
