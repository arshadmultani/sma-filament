<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Microsite;
use Filament\Tables\Table;
use App\Models\DoctorWebsite;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\DoctorWebsiteResource\Pages;
use App\Filament\Resources\DoctorWebsiteResource\RelationManagers;

class DoctorWebsiteResource extends Resource
{
    protected static ?string $model = Microsite::class;

    protected static ?string $navigationLabel = 'My Website';

    protected static ?string $slug = 'doctor-website';

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $modelLabel = 'Website';

    public static function canAccess(): bool
    {
        return auth()->user()->hasRole('doctor');

    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Select::make('practice_since')
                //     ->label('Practice Since')
                //     ->visible(fn() => auth()->user()->userable?->practice_since === null)
                //     ->placeholder('e.g 2001')
                //     // ->options(collect(range(now()->year, 1900))->mapWithKeys(fn($year) => [$year => $year]))
                //     ->options(function () {
                //         static $yearOptions = null;

                //         if ($yearOptions === null) {
                //             $years = range(now()->year, 1900);
                //             $yearOptions = array_combine($years, $years);
                //         }

                //         return $yearOptions;
                //     })
                //     ->searchable()
                //     ->native(false)
                //     ->mutateDehydratedStateUsing(fn($state) => "{$state}-01-01")
                //     ->afterStateHydrated(function (Select $component, $state) {
                //         if ($state) {
                //             $component->state(date('Y', strtotime($state)));
                //         }
                //     }),

                TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->disabled()
                    ->default(fn() => auth()->user()->name)
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
            'index' => Pages\ListDoctorWebsites::route('/'),
            'create' => Pages\CreateDoctorWebsite::route('/create'),
            'edit' => Pages\EditDoctorWebsite::route('/{record}/edit'),
        ];
    }
}
