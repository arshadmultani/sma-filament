<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CampaignStatusResource\Pages;
use App\Filament\Resources\CampaignStatusResource\RelationManagers;
use App\Models\CampaignStatus;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;

class CampaignStatusResource extends Resource
{
    protected static ?string $model = CampaignStatus::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                TextInput::make('name')->required(),
                Checkbox::make('is_active')
                    ->hint('Select to set campaign as active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\IconColumn::make('is_active')

            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListCampaignStatuses::route('/'),
            // 'create' => Pages\CreateCampaignStatus::route('/create'),
            // 'edit' => Pages\EditCampaignStatus::route('/{record}/edit'),
        ];
    }
}
