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
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use App\Filament\Actions\SiteUrlAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use App\Infolists\Components\VideoEntry;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Section;
use Filament\Notifications\Actions\Action;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use App\Filament\Resources\MicrositeResource\Pages;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Resources\MicrositeResource\RelationManagers;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Rmsramos\Activitylog\Actions\ActivityLogTimelineTableAction;
use App\Filament\Resources\MicrositeResource\RelationManagers\ReviewsRelationManager;
use App\Filament\Resources\MicrositeResource\RelationManagers\ShowcasesRelationManager;


/**
 * 
 *
 *
 */
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
            'active_status',
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
    public static function canAccess(): bool
    {
        return !auth()->user()->hasRole('doctor');

    }
    public static function getRelations(): array
    {
        return [
            ReviewsRelationManager::class,
            ShowcasesRelationManager::class
        ];
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
                    ->unique(ignoreRecord: true)
                    ->reactive()
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
                Radio::make('has_profile_photo')
                    ->label('Profile photo not uploaded for this doctor. Would you like to upload?')
                    ->reactive()
                    ->live()
                    ->visible(fn($get) => $get('doctor_id') && !Doctor::find($get('doctor_id'))?->has_profile_photo)
                    ->required()
                    ->dehydrated(false)
                    ->inline()
                    ->inlineLabel(false)
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ]),
                FileUpload::make('profile_photo')
                    ->dehydrated(false)
                    ->hint('max 2MB')
                    ->label('Profile Photo')
                    ->disk('s3')
                    ->visibility('private')
                    ->maxSize(2048)
                    ->directory('doctors/profile_photos')
                    ->maxFiles(1)
                    ->image()
                    ->required(fn($get) => $get('has_profile_photo') === 'yes')
                    ->visible(fn($get) => $get('has_profile_photo') === 'yes')
                    ->helperText('Upload a profile photo for the doctor.'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->sortable()
                    ->toggleable()
                    ->prefix('DW-'),
                TextColumn::make('doctor.name')
                    ->label('Doctor')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('campaignEntry.campaign.name')
                    ->searchable()
                    ->toggleable()
                    ->label('Campaign'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                // TextColumn::make('state.name')
                //     ->label('Status')
                //     ->toggleable()
                //     ->badge()
                //     ->color(fn($record) => $record->state->color),
                TextColumn::make('user.name')->label('Submitted By')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created On')
                    ->dateTime('M d, Y')
            ])
            ->filters([
                //
            ])
            ->actions([
                SiteUrlAction::makeTable(),
                // ActivityLogTimelineTableAction::make()->label('')
                //     ->visible(auth()->user()->can('view_user')),

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
                    ->compact()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('doctor.name'),
                        TextEntry::make('campaignEntry.campaign.name')->label('Campaign'),
                        // TextEntry::make('is_active')->boolean(),
                        TextEntry::make('is_active')
                            ->label('Website Status')
                            ->badge()
                            ->color(fn($record) => $record->is_active ? 'success' : 'danger')
                            ->getStateUsing(fn($record) => $record->is_active ? 'Active' : 'Inactive'),
                        // TextEntry::make('state.name')
                        //     ->label('Status')
                        //     ->badge()
                        //     ->color(fn($record) => $record->state->color),
                        TextEntry::make('user.name')->label('Submitted By')
                            ->hint(fn($record) => $record->user->roles->first()?->name),
                        TextEntry::make('created_at')->label('Created On')
                            ->dateTime('M d, Y')
                    ]),
                // Section::make('Doctor Video')
                //     ->compact()
                //     ->collapsible()
                //     ->visible(fn($record) => $record->doctor->showcases->isNotEmpty())
                //     ->schema([
                //         VideoEntry::make('doctor.showcases')
                //             ->label('Doctor Videos')
                //             ->muted()
                //             ->disablePictureInPicture()
                //             ->controlsListNoDownload()
                //             ->getStateUsing(function ($record) {
                //                 return $record->doctor->showcases->pluck('media_file_url')->filter()->toArray();
                //             })
                //     ]),
            ]);
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
