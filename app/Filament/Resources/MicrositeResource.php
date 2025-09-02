<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Campaign;
use Filament\Forms\Form;
use App\Models\Microsite;
use Filament\Tables\Table;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Forms\Components\Select;
use App\Filament\Actions\SiteUrlAction;
use Filament\Forms\Components\Repeater;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Notifications\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\MicrositeResource\Pages;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\MicrositeResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use App\Filament\Resources\MicrositeResource\RelationManagers\ShowcasesRelationManager;
use Filament\Forms\Components\Radio;

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
                    ->noSearchResultsMessage('No Active Campaigns found')
                    ->options(function () {
                        return Campaign::getForEntryType('microsite');
                    }),
                Select::make('doctor_id')
                    ->relationship('doctor', 'name', fn($query) => $query->approved())
                    ->label('Doctor Name')
                    ->placeholder('Select Doctor')
                    ->unique(table: Microsite::class, column: 'doctor_id')
                    ->live()
                    ->noSearchResultsMessage('Doctor not found')
                    ->optionsLimit(50)
                    ->required()
                    ->preload()
                    ->searchable()
                    ->native(false)
                    ->validationMessages([
                        'required' => 'Please select a doctor.',
                        'unique' => 'This doctor is already associated with a microsite.',
                    ])
                    ->afterStateUpdated(function ($state) {
                        if (is_null($state)) {
                            return;
                        }
                        $existingMicrosite = Microsite::where('doctor_id', $state)->first();

                        if ($existingMicrosite) {
                            $doctor = Doctor::find($state);
                            Notification::make()
                                ->warning()
                                ->title('Website Already Exists')
                                ->body("A microsite for {$doctor->name} is already available.")
                                ->actions([
                                    Action::make('view')
                                        ->label('Click here to view')
                                        ->url(MicrositeResource::getUrl('view', ['record' => $existingMicrosite->id]))
                                ])
                                ->send();
                        }
                    }),
                Radio::make('has_any_showcase')
                    ->label('Promotional/Introduction Video')
                    ->helperText('Select Yes if you want to add a video of the doctor promoting themself.')
                    ->required()
                    ->reactive()
                    ->dehydrated()
                    ->inline()
                    ->inlineLabel(false)
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ]),
                Repeater::make('showcases_data')
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('has_any_showcase') === 'yes')
                    ->required(fn($get) => $get('has_any_showcase') === 'yes')
                    ->label('Doctor Showcases')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Title')
                            ->required(),
                        Forms\Components\Textarea::make('description')
                            ->label('Description')
                            ->rows(3),
                        FileUpload::make('media_url')
                            ->disk('s3')
                            ->visibility('private')
                            ->directory('doctor-showcases')
                            ->label('Video Upload')
                            ->acceptedFileTypes(['video/mp4', 'video/x-m4v'])
                            ->helperText('Upload videos for the doctor\'s showcases.')
                            ->required(),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Add Another Showcase')
                    ->deleteAction(
                        fn($action) => $action->requiresConfirmation()
                    ),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->searchable(),
                Tables\Columns\TextColumn::make('campaignEntry.campaign.name')
                    ->searchable()
                    ->toggleable()
                    ->label('Campaign'),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('state.name')
                    ->label('Status')
                    ->toggleable()
                    ->badge()
                    ->color(fn($record) => $record->state->color),
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
                        // TextEntry::make('is_active')->boolean(),
                        TextEntry::make('state.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn($record) => $record->state->color),
                    ]),

            ]);
    }
    public static function getRelations(): array
    {
        return [];
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
