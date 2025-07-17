<?php

namespace App\Filament\Resources;

use App\Filament\Actions\UpdateStatusAction;
use App\Filament\Resources\DoctorResource\Pages;
use App\Models\Doctor;
use App\Models\Qualification;
use App\Models\Specialty;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Traits\HandlesDeleteExceptions;
use Filament\Infolists\Components\IconEntry;
use Njxqlus\Filament\Components\Infolists\LightboxImageEntry;
use Illuminate\Support\Facades\Storage;
use Filament\Infolists\Components\RepeatableEntry;
use Illuminate\Database\Eloquent\Builder;
use Icetalker\FilamentTableRepeatableEntry\Infolists\Components\TableRepeatableEntry;
use App\Models\Tag;
use Filament\Tables\Grouping\Group;
use App\Filament\Exports\DoctorExporter;
use Filament\Actions\ExportAction;


class DoctorResource extends Resource implements HasShieldPermissions
{
    use HandlesDeleteExceptions;

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

    protected static ?string $model = Doctor::class;

    protected static ?string $navigationGroup = 'Customer';

    // protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->prefix('Dr. ')
                    ->label('Name'),
                Select::make('type')
                    ->native(false)
                    ->options(['Allopathic' => 'Allopathic', 'Ayurvedic' => 'Ayurvedic'])
                    ->required(),
                Select::make('qualification_id')
                    ->native(false)
                    ->label('Qualification')
                    ->options(Qualification::where('category', 'Doctor')->orderBy('name', 'asc')->pluck('name', 'id'))
                    ->required(),
                Select::make('specialty_id')
                    ->label('Specialty')
                    ->native(false)
                    ->label('Specialty')
                    ->options(Specialty::orderBy('name', 'asc')->pluck('name', 'id'))
                    ->required(),
                Select::make('support_type')
                    ->native(false)
                    ->options(['Dispensing' => 'Dispensing', 'Prescribing' => 'Prescribing'])
                    ->required(),
                TextInput::make('email')
                    ->email()
                    ->required(),
                TextInput::make('phone')->required(),
                TextInput::make('address'),
                TextInput::make('town'),

                Select::make('headquarter_id')
                    ->label('Headquarter')
                    ->native(false)
                    ->options(function () {
                        $user = Auth::user();

                        if ($user->hasRole('ASM')) {
                            // ASM: headquarters under their area
                            return \App\Models\Headquarter::where('area_id', $user->location_id)->orderBy('name', 'asc')->pluck('name', 'id');
                        } elseif ($user->hasRole('RSM')) {
                            // RSM: headquarters under all areas in their region
                            $areaIds = \App\Models\Area::where('region_id', $user->location_id)->orderBy('name', 'asc')->pluck('id');

                            return \App\Models\Headquarter::whereIn('area_id', $areaIds)->orderBy('name', 'asc')->pluck('name', 'id');
                        } else {
                            // Default: all headquarters (or adjust as needed)
                            return \App\Models\Headquarter::orderBy('name', 'asc')->pluck('name', 'id');
                        }
                    })
                    ->searchable()
                    ->hidden(fn() => Auth::user()->hasRole('DSA'))
                    ->preload()
                    ->required(),
                Select::make('products')
                    ->label('Focus Product')
                    ->relationship('products', 'name', fn($query) => $query->orderBy('name', 'asc'))
                    ->preload()
                    ->searchable()
                    // ->required()
                    ->getOptionLabelFromRecordUsing(fn($record) => $record->name)
                    ->multiple(),
                // ->minItems(1)
                // ->maxItems(1),
                Select::make('tags')
                    ->label('Tags')
                    ->multiple()
                    ->visible(fn(string $context): bool => $context === 'create')
                    ->preload()
                    ->relationship(
                        name: 'tags',
                        titleAttribute: 'name',
                        modifyQueryUsing: fn(Builder $query) => $query->where('attached_to', 'doctor')
                    )
                    ->searchable(),

                FileUpload::make('attachment')
                    ->directory('doctors/attachments')
                    ->placeholder('Upload Both or Any One')
                    ->image()
                    ->multiple()
                    ->maxFiles(2)
                    ->panelLayout('grid')
                    ->maxSize(2048)
                    ->label('Visiting Card/Rx. Pad'),
                FileUpload::make('profile_photo')
                    ->image()
                    ->directory('doctors/profile_photos')
                    ->maxFiles(1)
                    ->image()
                    ->maxSize(2048),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            // ->groups([
            //    'tags.name'
            // ])
            ->paginated([25, 50, 100, 250])
            ->defaultSort('name', 'asc')
            ->columns([
                // ImageColumn::make('profile_photo')
                //     ->circular()
                //     ->toggleable()
                //     ->label('Photo'),

                TextColumn::make('name')->weight(FontWeight::Bold)->label('Dr.')->searchable(),

                IconColumn::make('status')
                    ->icon(fn(string $state): string => match ($state) {
                        'Pending' => 'heroicon-o-clock',
                        'Approved' => 'heroicon-o-check-circle',
                        'Rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'secondary',
                    }),
                // TextColumn::make('town')->toggleable(),
                TextColumn::make('headquarter.name')
                    ->toggleable()
                    ->label('HQ')
                    ->searchable(),

                TextColumn::make('headquarter.area.name')
                    ->toggleable()
                    ->label('Area')
                    ->searchable(),
                // TextColumn::make('headquarter.area.region.name')
                //     ->toggleable()
                //     ->label('Region')
                //     ->searchable(),

                // TextColumn::make('type')->toggleable(),
                // TextColumn::make('support_type')->toggleable()->label('Support'),
                // TextColumn::make('email')->toggleable(),
                // TextColumn::make('qualification.name')->toggleable(),
                // TextColumn::make('phone'),
                TextColumn::make('user.name')->label('Created By'),
                // TextColumn::make('created_at')->since()->toggleable()->sortable(),
                TextColumn::make('updated_at')->since()->toggleable()->sortable(),
                TextColumn::make('tags.name')
                    ->label('Tags')
                    ->toggleable()
                    ->badge(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
                SelectFilter::make('tags')
                    ->relationship('tags', 'name')
                    ->preload()
                    ->multiple(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    UpdateStatusAction::makeBulk(),
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(fn($action, $records) => collect($records)->each(fn($record) => (new static())->tryDeleteRecord($record, $action))),
                    Tables\Actions\ExportBulkAction::make()->exporter(DoctorExporter::class)
                        ->label('Download selected')
                        ->visible(fn() => Auth::user()->can('view_user')),

                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist

            ->schema([

                Section::make()
                    ->compact()
                    ->columns(3)
                    ->schema([
                        LightboxImageEntry::make('profile_photo')
                            ->href(fn($state) => $state ? Storage::temporaryUrl($state, now()->addMinutes(5)) : '')
                            ->visible(fn($state) => !is_null($state))
                            ->label('Photo')->circular(),

                        Section::make()
                            ->columns(2)
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('type'),
                                TextEntry::make('support_type'),
                                TextEntry::make('qualification.name'),
                                TextEntry::make('town'),
                            ]),

                    ]),

                Section::make('')
                    ->compact()
                    ->columns(3)
                    ->schema([

                        TextEntry::make('address'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                        TextEntry::make('headquarter.name')->label('HQ'),
                        TextEntry::make('headquarter.area.name')->label('Area'),
                        TextEntry::make('headquarter.area.region.name')->label('Region'),

                        // TextEntry::make('created_at')->since()->label('Created'),
                        // TextEntry::make('user.name'),
                    ]),
                Section::make('')

                    ->columns(4)
                    ->schema([
                        IconEntry::make('status')
                            ->icon(fn(string $state): string => match ($state) {
                                'Pending' => 'heroicon-o-clock',
                                'Approved' => 'heroicon-o-check-circle',
                                'Rejected' => 'heroicon-o-x-circle',
                                default => 'heroicon-o-question-mark-circle',
                            })
                            ->color(fn(string $state): string => match ($state) {
                                'Pending' => 'warning',
                                'Approved' => 'success',
                                'Rejected' => 'danger',
                                default => 'secondary',
                            }),
                        TextEntry::make('products.name')
                            ->hidden(fn($record) => $record->products->isEmpty())
                            ->label('Focus Product'),
                        TextEntry::make('tags.name')
                            ->label('Tags')
                            ->hidden(fn($record) => $record->tags->isEmpty())
                            ->badge(),
                        TextEntry::make('updated_at')->label('Updated')->since(),
                        TextEntry::make('user.name')->label('Created By'),
                    ]),
                Section::make()
                    ->hidden(fn($record) => $record->attachment == null)
                    ->columns(3)
                    ->schema([
                        RepeatableEntry::make('attachment')
                            ->label('Visiting Card/Rx. Pad')
                            ->schema([
                                LightboxImageEntry::make('') // no key, since each item is a string
                                    ->href(fn($state) => $state ? Storage::temporaryUrl($state, now()->addMinutes(5)) : '')
                            ])
                            ->visible(fn($record) => !empty($record->attachment)),

                    ]),
                Section::make()
                    ->compact()
                    ->hidden(fn($record) => $record->tags->isEmpty())
                    ->visible(fn() => Auth::user()->can('view_user'))
                    ->schema([
                        TableRepeatableEntry::make('tags')
                            ->contained(false)
                            ->label('')
                            ->columnSpan(2)
                            ->extraAttributes(['class' => 'hidden sm:block']) // Hidden on mobile, visible on sm and up
                            ->schema([
                                TextEntry::make('name')
                                    ->badge()
                                    ->label('Tag'),
                                TextEntry::make('pivot.user_id')
                                    ->label('Tagged By')
                                    ->formatStateUsing(function ($state) {
                                        return \App\Models\User::find($state)?->name ?? 'Unknown';
                                    }),
                                TextEntry::make('pivot.created_at')
                                    ->label('Tagged On')
                                    ->formatStateUsing(fn($state) => $state->format('d-m-Y')),
                            ]),
                        RepeatableEntry::make('tags')  // repeater for mobile
                            ->label('')
                            ->extraAttributes(['class' => 'block sm:hidden']) // Visible only on mobile
                            ->schema([
                                TextEntry::make('name')
                                    ->columnSpan(2)
                                    ->label('')
                                    ->badge(),
                                TextEntry::make('pivot.user_id')
                                    ->label('Tagged By')
                                    ->formatStateUsing(function ($state) {
                                        return \App\Models\User::find($state)?->name ?? 'Unknown';
                                    }),
                                TextEntry::make('pivot.created_at')
                                    ->label('Tagged On')
                                    ->formatStateUsing(fn($state) => $state->format('d-m-Y')),
                            ]),
                    ])
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
            'view' => Pages\ViewDoctor::route('/{record}'),
        ];
    }
    // public function throwValidationException(array $errors): void
    // {
    //     throw new ValidationException($errors);
    // }
    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
