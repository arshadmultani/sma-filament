<?php

namespace App\Filament\Doctor\Resources;

use Exception;
use Filament\Forms;
use Filament\Tables;
use App\Models\Doctor;
use Filament\Forms\Form;
use App\Models\Microsite;
use Filament\Tables\Table;
use App\Models\DoctorWebsite;
use App\Models\Scopes\TeamScope;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\Log;
use App\Forms\Components\YearSelect;
use Filament\Forms\Components\Radio;
use App\Actions\Review\ApproveReview;
use Illuminate\Support\Facades\Crypt;
use Filament\Support\Enums\FontWeight;
use App\Filament\Actions\SiteUrlAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\Scopes\TeamHierarchyScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Actions\DownloadQrAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Fieldset;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ColorEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Actions\DynamicFieldEditAction;
use App\Filament\Actions\MicrositeDesignSettings;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Actions\SetMicrositeDesignSettings;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\Pages;
use Filament\Infolists\Components\Actions\Action as infoaction;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers\ReviewsRelationManager;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers\ShowcasesRelationManager;

class DoctorWebsiteResource extends Resource
{

    protected static ?string $model = Microsite::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'My Website';

    protected static ?string $modelLabel = 'Website';

    protected static ?string $pluralModelLabel = 'Websites';

    protected static ?Doctor $currentDoctor = null;

    protected static ?string $recordRouteKeyName = 'url';




    public static function form(Form $form): Form
    {
        $doctor = self::currentDoctor();

        return $form
            ->schema([
                \Filament\Forms\Components\Section::make('Current Profile Photo')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->hidden(is_null($doctor?->profile_photo))
                    ->schema([
                        Placeholder::make('profile')
                            ->label('')
                            ->helperText(fn() => $doctor?->profile_photo ? 'Current profile photo' : 'No profile photo set')
                            ->columns(2)
                            ->content(function () use ($doctor): HtmlString {
                                return new HtmlString(("<img src='" . $doctor->profile_photo_url . "'>"));
                            }),
                    ]),
                Radio::make('change_photo')
                    ->label(fn() => $doctor?->profile_photo ? 'Would you like to update the profile photo?' : 'You do not have any profile photo, would you like to add it?')
                    ->helperText(fn() => $doctor?->profile_photo ? 'Select Yes if you want to update the profile photo.' : 'Select Yes to add a profile photo.')
                    ->required()
                    ->reactive()
                    ->dehydrated(false)
                    ->inline()
                    ->inlineLabel(false)
                    ->options([
                        'yes' => 'Yes',
                        'no' => 'No',
                    ]),
                FileUpload::make('profile_photo')
                    ->label('Upload New Profile Photo')
                    ->visible(fn($get, $context) => $get('change_photo') === 'yes' || $context === 'edit')
                    ->visibility('private')
                    ->disk('s3')
                    ->directory('doctors/profile_photos')
                    ->maxSize(2048)
                    ->helperText('Upload a profile photo for the doctor.'),

                YearSelect::make('practice_since')
                    ->label('Practicing Since')
                    ->placeholder('Select Year')
                    ->hidden(filled($doctor?->practice_since))
                    ->required(fn() => empty($doctor?->practice_since)),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('doctor.name')
                    ->default('N/A')
                    ->weight(FontWeight::Bold)
                    ->label('Name')
                    ->getStateUsing(fn($record) => $record->doctor?->name),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])

            ->emptyStateHeading('You do not have any website yet.')
            ->emptyStateIcon('heroicon-o-globe-alt')
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                SiteUrlAction::makeTable(),
                // Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getEloquentQuery(): Builder
    {
        $doctor_id = self::currentDoctor()?->id;
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([TeamHierarchyScope::class, TeamScope::class])
            ->where('doctor_id', $doctor_id)
            ->with(['doctor' => fn($query) => $query->withoutGlobalScopes()]);
    }
    public static function currentDoctor(): ?Doctor
    {
        if (static::$currentDoctor) {
            return static::$currentDoctor;
        }

        $doctorId = auth()->user()?->userable_id;
        if (!$doctorId) {
            return null;
        }

        return static::$currentDoctor = Doctor::query()
            ->withoutGlobalScopes([TeamHierarchyScope::class, TeamScope::class])
            ->find($doctorId);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Website Status')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('is_active')
                            ->label('Site')
                            ->weight(FontWeight::Bold)
                            ->getStateUsing(fn($record) => $record->is_active ? 'Active' : 'Inactive')
                            ->color(fn($record) => $record->is_active ? 'success' : 'danger'),
                        Actions::make([
                            SiteUrlAction::makeAction()
                                ->label('Visit Site')
                                ->outlined(),
                            DownloadQrAction::makeInfolist()
                                ->outlined(),
                        ])
                        // TextEntry::make('reviews_count')
                        //     ->label('Total Reviews')
                        //     ->weight(FontWeight::Bold)
                        //     ->getStateUsing(fn($record) => $record->reviews ?? 0),

                    ]),
                Section::make('Design Settings')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        Actions::make([SetMicrositeDesignSettings::make()])
                    ]),
                Section::make('Personal Information')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('doctor.email')
                            ->label('Email Address'),
                        TextEntry::make('doctor.phone')
                            ->label('Phone Number')
                            ->prefixAction(self::getEditPhoneNumberAction()),
                        TextEntry::make('doctor.practice_since')
                            ->label('Practicing Since')
                            ->default(now())
                            ->date('Y')
                            ->prefixAction(self::getEditPracticeSinceAction()),
                        TextEntry::make('doctor.qualification.name')
                            ->label('Qualification'),
                        ImageEntry::make('doctor.profile_photo')
                            ->label('Profile Photo')
                            ->circular()
                            ->helperText(fn() => self::currentDoctor()?->profile_photo ? 'Current profile photo' : 'No profile photo set')
                            ->disk('s3')
                            ->visibility('private')
                            ->action(self::getEditProfilePhotoAction()),
                        Actions::make([self::getEditProfilePhotoAction()])


                    ])
            ]);
    }
    public static function getRelations(): array
    {
        return [
            ReviewsRelationManager::class,
            ShowcasesRelationManager::class
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctorWebsites::route('/'),
            'create' => Pages\CreateDoctorWebsite::route('/create'),
            'edit' => Pages\EditDoctorWebsite::route('/{record}/edit'),
            'view' => Pages\ViewDoctorWebsite::route('/{record}'),
        ];
    }

    private static function getEditPracticeSinceAction(): Action
    {
        return self::createEditAction(
            'edit-practice-since',
            'practice_since',
            [
                YearSelect::make('practice_since')
                    ->label('Practicing Since')
                    ->placeholder('Select Year')
                    ->required(),
            ],
            fieldLabel: 'Practice Since'
        );
    }

    private static function getEditPhoneNumberAction(): Action
    {
        return self::createEditAction(
            'edit-phone-number',
            'phone',
            [
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->maxLength(10)
                    ->minLength(10)
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->required()
                    ->prefix('+91')
                    ->placeholder('10 digit phone number'),
            ],
            fieldLabel: 'Phone Number'
        )->modalDescription('This phone number will be displayed on your website.');
    }


    private static function getEditProfilePhotoAction(): Action
    {
        return self::createEditAction(
            'edit-profile-photo',
            'profile_photo',
            [
                FileUpload::make('profile_photo')
                    ->label('Profile Photo')
                    ->disk('s3')
                    ->visibility('private')
                    ->image()
                    ->directory('doctors/profile_photos')
                    ->maxSize(2048)
                    ->required(),
            ],
            'Profile Photo'
        )
            ->label('Set Profile Photo')
            ->outlined();
    }

    private static function createEditAction(string $name, string $field, array $formSchema, string $fieldLabel): Action
    {
        return Action::make($name)
            ->color('primary')
            ->icon('heroicon-o-pencil')
            ->modalWidth('sm')
            ->form($formSchema)
            ->action(function (array $data) use ($field, $fieldLabel) {
                try {
                    self::currentDoctor()->update([
                        $field => $data[$field],
                    ]);
                    Notification::make()
                        ->title("{$fieldLabel} updated successfully.")
                        ->success()
                        ->send();

                    redirect(request()->header('Referer'));

                } catch (Exception $e) {
                    Log::error("Failed to update {$fieldLabel}: " . $e->getMessage());

                    Notification::make()
                        ->title('Update failed')
                        ->body('Something went wrong. Please try again.')
                        ->danger()
                        ->send();
                }
            });
    }
}
