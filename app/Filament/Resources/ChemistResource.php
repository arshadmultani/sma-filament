<?php

namespace App\Filament\Resources;

use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Resources\ChemistResource\Pages;
use App\Models\Chemist;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;

class ChemistResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = Chemist::class;

    protected static ?string $navigationGroup = 'Customer';

    public static function getPermissionPrefixes(): array
    {
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'update_status',
        ];
    }
    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required(),
                TextInput::make('phone')->required()->tel(),
                TextInput::make('email')->email()->unique(),
                TextInput::make('address'),
                TextInput::make('town')
                    ->required(),
                Select::make('type')
                    ->native(false)
                    ->options(['Ayurvedic' => 'Ayurvedic', 'Allopathic' => 'Allopathic'])
                    ->required(),
                Select::make('headquarter_id')
                    ->native(false)
                    ->options(function () {
                        $user = Auth::user();

                        if ($user->hasRole('ASM')) {
                            // ASM: headquarters under their area
                            return \App\Models\Headquarter::where('area_id', $user->location_id)->pluck('name', 'id');
                        } elseif ($user->hasRole('RSM')) {
                            // RSM: headquarters under all areas in their region
                            $areaIds = \App\Models\Area::where('region_id', $user->location_id)->pluck('id');

                            return \App\Models\Headquarter::whereIn('area_id', $areaIds)->pluck('name', 'id');
                        } else {
                            // Default: all headquarters (or adjust as needed)
                            return \App\Models\Headquarter::pluck('name', 'id');
                        }
                    })
                    ->searchable()
                    ->hidden(fn () => Auth::user()->hasRole('DSA'))
                    ->preload()
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                IconColumn::make('status')
                    ->sortable()
                    ->icon(fn (string $state): string => match ($state) {
                        'Pending' => 'heroicon-o-clock',
                        'Approved' => 'heroicon-o-check-circle',
                        'Rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('headquarter.name')
                    ->toggleable()
                    ->label('Location')
                    ->searchable(),
                TextColumn::make('town')->toggleable(),
                TextColumn::make('type')->toggleable(),
                TextColumn::make('address'),
                TextColumn::make('phone')->toggleable(),
                TextColumn::make('email')->toggleable(),
                TextColumn::make('user.name')->label('Created By'),
                TextColumn::make('created_at')->since()->toggleable()->sortable(),
                TextColumn::make('updated_at')->since()->toggleable()->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    UpdateStatusAction::makeBulk(),
                    // Tables\Actions\DeleteBulkAction::make(),
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
                        TextEntry::make('name'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                    ]),
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('address'),
                        TextEntry::make('town')->label('Town'),
                        TextEntry::make('headquarter.name')->label('Headquarter'),
                    ]),
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name'),
                        TextEntry::make('created_at')->since()->label('Created'),
                        TextEntry::make('updated_at')->since()->label('Updated'),
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
            'index' => Pages\ListChemists::route('/'),
            'create' => Pages\CreateChemist::route('/create'),
            'edit' => Pages\EditChemist::route('/{record}/edit'),
            'view' => Pages\ViewChemist::route('/{record}'),
        ];
    }
    //     public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
