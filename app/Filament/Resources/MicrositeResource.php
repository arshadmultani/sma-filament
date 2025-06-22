<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MicrositeResource\Pages;
use App\Filament\Resources\MicrositeResource\RelationManagers;
use App\Models\Campaign;
use App\Models\Microsite;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

use Illuminate\Database\Eloquent\Model;

class MicrositeResource extends Resource
{
    protected static ?string $model = Microsite::class;

    protected static ?string $navigationIcon = '';
    protected static ?string $navigationGroup = 'Activities';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('campaign_id')
                    ->label('Campaign')
                    ->options(function () {
                        return Campaign::query()
                            ->where('allowed_entry_type', 'microsite')
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->preload()
                    ->searchable()
                    ->native(false),
                Forms\Components\Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->native(false),
                Repeater::make('doctor.reviews')
                    ->schema([
                        Forms\Components\TextInput::make('reviewer_name')
                            ->required(),
                        Forms\Components\FileUpload::make('video')
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name'),
                Tables\Columns\TextColumn::make('campaignEntry.campaign.name')->label('Campaign'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('status'),
                Tables\Columns\TextColumn::make('url'),
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
                        TextEntry::make('doctor.name'),
                        TextEntry::make('campaignEntry.campaign.name')->label('Campaign'),
                        TextEntry::make('url')->copyable(true),
                        // TextEntry::make('is_active')->boolean(),
                        // TextEntry::make('status'),
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
            'index' => Pages\ListMicrosites::route('/'),
            'create' => Pages\CreateMicrosite::route('/create'),
            'edit' => Pages\EditMicrosite::route('/{record}/edit'),
            'view' => Pages\ViewMicrosite::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['campaignEntry.campaign']);
    }
}
