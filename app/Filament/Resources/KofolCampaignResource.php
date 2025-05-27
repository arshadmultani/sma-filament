<?php

namespace App\Filament\Resources;

use App\Filament\Resources\KofolCampaignResource\Pages;
use App\Filament\Resources\KofolCampaignResource\RelationManagers;
use App\Models\KofolCampaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\Section;

class KofolCampaignResource extends Resource
{
    protected static ?string $model = KofolCampaign::class;
    protected static ?string $navigationGroup = 'Kofol Swarna Varsha';

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('description')
                            ->required()
                            ->maxLength(255),

                    ]),
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('start_date')
                    ->date('d F Y'),
                TextColumn::make('end_date')
                    ->date('d F Y'),
                TextColumn::make('status')
                    ->state(function ($record) {
                        if ($record->is_active) {
                            return 'Active';
                        }
                        
                        $today = now()->startOfDay();
                        $startDate = \Carbon\Carbon::parse($record->start_date)->startOfDay();
                        $endDate = \Carbon\Carbon::parse($record->end_date)->endOfDay();
                        
                        if (!$record->is_active && $startDate->greaterThan($today)) {
                            return 'Upcoming';
                        }
                        
                        if (!$record->is_active && $today->greaterThan($endDate)) {
                            return 'Completed';
                        }
                        
                        return 'Unknown';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Active' => 'primary',
                        'Upcoming' => 'warning',
                        'Completed' => 'info',
                        default => 'gray'
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'Active' => 'heroicon-o-arrow-path',
                        'Upcoming' => 'heroicon-o-clock',
                        'Completed' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle'
                    }),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
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
            'index' => Pages\ListKofolCampaigns::route('/'),
            'create' => Pages\CreateKofolCampaign::route('/create'),
            'edit' => Pages\EditKofolCampaign::route('/{record}/edit'),
        ];
    }
}
