<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Campaign;
use Filament\Forms\Form;
use App\Models\Microsite;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use App\Filament\Actions\SiteUrlAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\MicrositeResource\Pages;
use App\Filament\Resources\MicrositeResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class MicrositeResource extends Resource implements HasShieldPermissions
{
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
    protected static ?string $model = Microsite::class;

    protected static ?string $navigationIcon = '';
    protected static ?string $navigationGroup = 'Activities';
    protected static ?string $modelLabel = 'Doctor Website';

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('campaign_id')
                    ->label('Campaign')
                    ->placeholder('Select Campaign')
                    ->dehydrated(false)
                    ->reactive()
                    ->native(false)
                    ->preload()
                    ->required()
                    ->searchable()
                    ->options(function () {
                        return Campaign::getForEntryType('pob');
                    }),
                Select::make('doctor_id')
                    ->relationship('doctor', 'name')
                    ->required()
                    ->preload()
                    ->searchable()
                    ->native(false),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name')->searchable(),
                Tables\Columns\TextColumn::make('campaignEntry.campaign.name')
                    ->searchable()
                    ->toggleable()
                    ->label('Campaign'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')->label('Status')
                    ->toggleable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'primary',
                        'Rejected' => 'danger',
                        default => 'secondary'
                    }),
                Tables\Columns\TextColumn::make('user.name')->label('Submitted By')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                SiteUrlAction::makeTable(),
                Tables\Actions\ViewAction::make(),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
                        TextEntry::make('doctor.name'),
                        TextEntry::make('campaignEntry.campaign.name')->label('Campaign'),
                        // TextEntry::make('is_active')->boolean(),
                        TextEntry::make('status'),
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
