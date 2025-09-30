<?php

namespace App\Filament\Doctor\Resources;

use Exception;
use Filament\Forms;
use Filament\Infolists\Components\TextEntry;
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
use Filament\Forms\Components\Radio;
use Filament\Support\Enums\FontWeight;
use App\Filament\Actions\SiteUrlAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Models\Scopes\TeamHierarchyScope;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\Placeholder;
use Filament\Infolists\Components\Section;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\Pages;
use App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers;
use Illuminate\Support\Facades\Crypt;

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
                    // ->dehydrated(false)
                    ->visibility('private')
                    ->disk('s3')
                    ->directory('doctors/profile_photos')
                    ->maxSize(2048)
                    ->helperText('Upload a profile photo for the doctor.'),



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
                        TextEntry::make('reviews_count')
                            ->label('Total Reviews')
                            ->weight(FontWeight::Bold)
                            ->getStateUsing(fn($record) => $record->reviews ?? 0),
                    ])
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListDoctorWebsites::route('/'),
            'create' => Pages\CreateDoctorWebsite::route('/create'),
            'edit' => Pages\EditDoctorWebsite::route('/{record:url}/edit'),
            'view' => Pages\ViewDoctorWebsite::route('/{record}'),
        ];
    }
}
