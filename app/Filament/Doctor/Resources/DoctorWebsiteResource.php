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
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Actions\DynamicFieldEditAction;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\Pages;
use Filament\Infolists\Components\Actions\Action as infoaction;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers\ReviewsRelationManager;

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
                            ->helperText('Current profile photo')
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
                    ->hidden(filled($doctor->practice_since))
                    ->required(fn() => empty($doctor->practice_since)),

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
                Section::make('Website Stats')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('is_active')
                            ->label('Status')
                            ->weight(FontWeight::Bold)
                            ->getStateUsing(fn($record) => $record->is_active ? 'Active' : 'Inactive')
                            ->color(fn($record) => $record->is_active ? 'success' : 'danger'),
                        // TextEntry::make('reviews_count')
                        //     ->label('Total Reviews')
                        //     ->weight(FontWeight::Bold)
                        //     ->getStateUsing(fn($record) => $record->reviews ?? 0),

                    ]),
                Section::make('Your Information')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('doctor.practice_since')
                            ->label('Practicing Since')
                            ->weight(FontWeight::Bold)
                            ->date('Y')
                            ->prefixAction(self::getEditPracticeSinceAction()),
                        TextEntry::make('doctor.phone')
                            ->label('Phone Number')
                            ->prefixAction(self::getEditPhoneNumberAction()),


                    ])
                // Section::make('Reviews')
                //     ->compact()
                //     // ->collapsible()
                //     ->schema([
                //         RepeatableEntry::make('doctor.reviews')
                //             ->label('')
                //             ->columns(3)
                //             ->schema([
                //                 TextEntry::make('reviewer_name')
                //                     ->label('Patient Name')
                //                     ->weight(FontWeight::Bold),
                //                 // TextEntry::make('review_text')
                //                 //     ->label('Review Text'),
                //                 // TextEntry::make('created_at')
                //                 //     ->label('Date')
                //                 //     ->date('M d, Y'),
                //                 IconEntry::make('is_verified')
                //                     ->label('Review Verified')
                //                     ->boolean(),


                //             ]),
                //     ]),
            ]);
    }
    public static function getRelations(): array
    {
        return [
            ReviewsRelationManager::class,
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
        return Action::make('edit-practice-since')
            ->color('primary')
            ->icon('heroicon-o-pencil')
            ->modalWidth('sm')
            ->form([
                YearSelect::make('practice_since')
                    ->label('Practicing Since')
                    ->placeholder('Select Year')
                    ->required(),
            ])
            ->action(function (array $data, Microsite $record) {
                try {
                    DoctorWebsiteResource::currentDoctor()->update([
                        'practice_since' => $data['practice_since'],
                    ]);
                    Notification::make()
                        ->title('Practice Since updated successfully.')
                        ->success()
                        ->send();
                    redirect(request()->header('Referer'));

                } catch (Exception $e) {
                    // Log the exception for debugging
                    Log::error('Failed to update practice since: ' . $e->getMessage());

                    // Notify the user that something went wrong
                    Notification::make()
                        ->title('Update failed')
                        ->body('Something went wrong. Please try again.')
                        ->danger()
                        ->send();
                }
            });
    }

    private static function getEditPhoneNumberAction(): Action
    {
        return Action::make('edit-phone-number')
            ->color('primary')
            ->icon('heroicon-o-pencil')
            ->modalWidth('sm')
            ->form([
                TextInput::make('phone')
                    ->label('Phone Number')
                    ->maxLength(10)
                    ->minLength(10)
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/')
                    ->required()
                    ->prefix('+91')
                    ->placeholder('10 digit phone number'),
            ])
            ->action(function (array $data, Microsite $record) {
                try {
                    DoctorWebsiteResource::currentDoctor()->update([
                        'phone' => $data['phone'],
                    ]);
                    Notification::make()
                        ->title('Phone Number updated successfully.')
                        ->success()
                        ->send();
                    redirect(request()->header('Referer'));

                } catch (Exception $e) {
                    // Log the exception for debugging
                    Log::error('Failed to update phone number: ' . $e->getMessage());

                    // Notify the user that something went wrong
                    Notification::make()
                        ->title('Update failed')
                        ->body('Something went wrong. Please try again.')
                        ->danger()
                        ->send();
                }
            });
    }
}
