<?php

namespace App\Filament\Resources;

use App\Filament\Actions\ViewInfoAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use App\Models\PanelAccessRequest;
use App\Filament\Resources\DoctorResource;
use App\Filament\Resources\UserResource;
use Filament\Infolists\Components\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PanelAccessRequestResource\Pages;
use App\Filament\Resources\PanelAccessRequestResource\RelationManagers;

class PanelAccessRequestResource extends Resource
{
    protected static ?string $model = PanelAccessRequest::class;

    protected static ?string $navigationIcon = 'heroicon-o-key';

    protected static ?string $navigationGroup = 'System';
    protected static ?string $modelLabel = 'Portal Request';



    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('doctor.name')
                    ->label('Dr.')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requester.name')
                    ->label('Requested By')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('requester.roles.name')
                    ->label('Role')
                    ->badge()
                    ->color(fn($record) => $record->requester?->roleColor() ?? 'secondary'),
                TextColumn::make('state.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => $record->state->color)
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reviewer.name')
                    ->label('Reviewed By')
                    ->placeholder('NA')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('reviewed_at')
                    ->placeholder('NA')
                    ->dateTime('d M y @ H:i'),
                // ->default(fn($record) => $record->reviewed_at ?? 'NA'),
                TextColumn::make('created_at')
                    ->label('Submitted On')
                    ->dateTime('d M y @ H:i')

            ])
            ->filters([
                //
            ])
            ->actions([
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

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([

                Section::make('Request Information')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->columnSpanFull()
                    ->grow(true)
                    ->schema([
                        TextEntry::make('doctor.name')
                            ->label('Doctor')
                            ->color('primary')
                            ->prefixAction(ViewInfoAction::for('doctor', DoctorResource::class, 'Doctor')),
                        TextEntry::make('requester.name')
                            ->label('Requested By')
                            ->color('primary')
                            ->prefixAction(ViewInfoAction::for('requester', UserResource::class, 'Requester')),
                        TextEntry::make('created_at')
                            ->label('Requested At')
                            ->dateTime('d-m-y @ H:i'),
                        TextEntry::make('state.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn($record) => $record->state->color)


                    ]),






            ]);
    }


    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPanelAccessRequests::route('/'),
            'create' => Pages\CreatePanelAccessRequest::route('/create'),
            'edit' => Pages\EditPanelAccessRequest::route('/{record}/edit'),
            'view' => Pages\ViewPanelAccessRequest::route('/{record}'),
        ];
    }
}
