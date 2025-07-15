<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ManagerLogEntryResource\Pages;
use App\Models\ManagerLogEntry;
use App\Models\User;
use App\Models\Doctor;
use App\Models\ConversionActivity;
use App\Models\CallInput;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Resources\Resource;
use Filament\Forms\Form;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables;
use App\Models\Campaign;
use Filament\Tables\Actions\Impersonate;
use Filament\Forms\Components\MorphToSelect;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Placeholder;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Support\Enums\FontWeight;
use Filament\Infolists\Components\ImageEntry;
use Icetalker\FilamentTableRepeatableEntry\Infolists\Components\TableRepeatableEntry;
use Illuminate\Support\Facades\Storage;
use App\Models\Tag;
use Filament\Tables\Columns\IconColumn;
use Illuminate\Support\Facades\DB;






class ManagerLogEntryResource extends Resource
{
    protected static ?string $model = ManagerLogEntry::class;

    protected static ?string $navigationGroup = 'Activities';

    protected static ?string $modelLabel = 'Conversion Track';
    // protected static ?string $pluralModelLabel = 'Conversion Tracks';



    public static function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                Select::make('campaign_id')
                    ->label('Campaign')
                    ->placeholder('Select Campaign')
                    ->options(function () {
                        return Campaign::query()
                            ->where('allowed_entry_type', 'manager_log_entry')
                            ->where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->preload()
                    ->searchable()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('customer_id', null);
                    })
                    ->dehydrated(false)
                    ->native(false)
                    ->reactive(),
                /*-------------------------------------TAGGED Cx Allowed --------------------------------*/
                Placeholder::make('campaign_tags')
                    ->label('Customer Tags Allowed')
                    ->content(function ($get) {
                        $campaignId = $get('campaign_id');
                        if (!$campaignId)
                            return '-';
                        $campaign = Campaign::find($campaignId);
                        if (!$campaign)
                            return '-';
                        $tags = $campaign->tags()->pluck('name');
                        if ($tags->isEmpty())
                            return '-';
                        return $tags->implode(', ');
                    })
                    ->visible(function ($get) {
                        $campaignId = $get('campaign_id');
                        if (!$campaignId)
                            return false;
                        $campaign = Campaign::find($campaignId);
                        if (!$campaign && !$campaign->tags()->exists())
                            return false;
                        return $campaign->tags()->exists();
                    })
                    ->reactive(),
                DatePicker::make('date')
                    ->label('Date')
                    ->format('d-m-Y')
                    ->native(false)
                    ->closeOnDateSelection()
                    ->maxDate(now()->toDateString())
                    ->minDate(now()->subDays(15)->toDateString())
                    ->required()
                    ->default(now()->toDateString())
                    ->rules([
                        function ($get, $set, $state, $record) {
                            $recordId = $record?->id ?? null;
                            return Rule::unique('manager_log_entries', 'date')
                                ->where(fn($query) => $query->where('user_id', Auth::id()))
                                ->ignore($recordId);
                        }
                    ])
                    ->validationMessages([
                        'unique' => 'You have already submitted for this date.',
                    ]),
                TextInput::make('doctors_met')
                    ->label('Doctors Met')
                    ->numeric()
                    ->reactive()
                    ->required()
                    ->placeholder('No. of Drs. met on this date')
                    ->minValue(1)
                    ->maxValue(20)
                    ->validationMessages([
                        'max' => 'This figure appears unusually high for one day.',
                    ]),
                Radio::make('worked_with_team')
                    ->label('Working')
                    ->required()
                    ->reactive()
                    ->inline()
                    ->inlineLabel(false)
                    ->options([
                        'true' => 'With Team',
                        'false' => 'Independently',
                    ]),

                /*-------------------------------------TEAM MEMBERS --------------------------------*/
                Repeater::make('colleagues')
                    ->relationship('colleagues')
                    ->label('Team Member')
                    ->visible(fn($get) => $get('worked_with_team') === 'true')
                    ->required(fn($get) => $get('worked_with_team') === 'true')
                    ->columnSpanFull()
                    ->minItems(1)

                    ->defaultItems(1)
                    ->addActionLabel('Add Team Member')
                    ->simple(
                        Select::make('user_id')
                            ->label('Team Member')
                            ->distinct()
                            ->required()
                            ->placeholder('Select Team Member')
                            ->options(function () {
                                $team = Auth::user()->getTeam();
                                return collect($team)->flatten(1)->unique('id')->pluck('name', 'id')->toArray();
                            })

                            ->searchable(),
                    ),
                /*-------------------------------------ACTIVITY DOCTOR(S) MET? --------------------------------*/
                Radio::make('activity_doctor_met')
                    ->label('Activity Doctor(s) Met?')
                    ->options([
                        'true' => 'Yes',
                        'false' => 'No',
                    ])
                    ->columnSpanFull()
                    ->inline()
                    ->reactive()
                    ->required(),
                /*-------------------------------------ACTIVITIES REPEATER --------------------------------*/
                Repeater::make('activities')
                    ->relationship('activities')
                    ->label('Activity')
                    ->columnSpanFull()
                    ->reactive()
                    ->validationMessages([
                        'max' => 'You can only add :max Doctors as mentioned in Doctors met field',
                    ])
                    ->minItems(1)
                    ->maxItems(fn($get) => (int) $get('doctors_met'))
                    ->defaultItems(1)
                    ->required()
                    ->addActionLabel('Add Doctor')
                    ->visible(fn($get) => true)
                    ->schema([
                        Select::make('customer_type')
                            ->label('Customer')
                            ->visible(fn($get) => $get('../../activity_doctor_met') === 'true')
                            ->options(['doctor' => 'Doctor'])
                            ->default('doctor')
                            ->native(false)
                            ->dehydrated(true),
                        Select::make('customer_id')
                            ->distinct()
                            ->label('Doctor')
                            ->visible(fn($get) => $get('../../activity_doctor_met') === 'true')
                            ->required(fn($get) => $get('../../activity_doctor_met') === 'true')
                            ->preload()
                            ->reactive()
                            ->options(function ($get) {
                                $campaignId = $get('../../campaign_id'); // Go up two levels to the root form
                                if (!$campaignId) {
                                    return [];
                                }
                                $campaign = Campaign::find($campaignId);
                                if (!$campaign) {
                                    return [];
                                }
                                $tagIds = $campaign->tags()->pluck('tags.id')->toArray();
                                if (empty($tagIds)) {
                                    return [];
                                }
                                $doctors = Doctor::whereHas('tags', function ($q) use ($tagIds) {
                                    $q->whereIn('tags.id', $tagIds);
                                })
                                    ->pluck('name', 'id')
                                    ->toArray();
                                return $doctors;
                            })
                            ->searchable(),
                        /*-------------------------------------FOCUS PRODUCT --------------------------------*/
                        Placeholder::make('doctor_products')
                            ->label('Focus Product')
                            ->content(function ($get) {
                                $doctorId = $get('customer_id');
                                if (!$doctorId)
                                    return '-';
                                $doctor = Doctor::find($doctorId);
                                if (!$doctor)
                                    return '-';
                                $products = $doctor->products()->pluck('name');
                                if ($products->isEmpty())
                                    return '-';
                                return $products->implode(', ');
                            })
                            ->visible(function ($get) {
                                $doctorId = $get('customer_id');
                                if (!$doctorId)
                                    return false;
                                $doctor = Doctor::find($doctorId);
                                if (!$doctor)
                                    return false;
                                return $doctor->products()->exists();
                            })
                            ->reactive(),
                        /*-------------------------------------DR. CONVERTED --------------------------------*/
                        Radio::make('doctor_converted')
                            ->label('Doctor Converted?')
                            ->options([
                                'true' => 'Yes',
                                'false' => 'No',
                            ])
                            ->columnSpanFull()
                            ->inline()
                            ->reactive()
                            ->visible(fn($get) => $get('../../activity_doctor_met') === 'true')
                            ->required(fn($get) => $get('customer_id')),
                        /*-------------------------------------CONVERSION TYPE --------------------------------*/
                        Radio::make('conversion_type')
                            ->label('Conversion Type')
                            ->visible(fn($get) => $get('doctor_converted') === 'true')
                            ->reactive()
                            ->options([
                                'pob' => 'POB',
                                'prescription' => 'Prescription',
                            ])
                            ->columnSpanFull()
                            ->inline(),
                        /*-------------------------------------PRESCRIPTION --------------------------------*/
                        TextInput::make('no_of_prescriptions')
                            ->label('No. of Prescriptions')
                            ->visible(fn($get) => $get('conversion_type') === 'prescription')
                            ->placeholder('Avg. no. of Rx per week')
                            ->reactive()
                            ->numeric()
                            ->required(fn($get) => $get('conversion_type') === 'prescription')
                            ->minValue(1),
                        FileUpload::make('prescription_image')
                            ->label('Prescription Image')
                            ->visible(fn($get) => $get('conversion_type') === 'prescription')
                            ->disk('s3')
                            ->directory('manager-log-entry-activities/prescriptions')
                            ->reactive()
                            ->image()
                            ->maxSize(2048),
                        /*-------------------------------------POB--------------------------------*/
                        Repeater::make('products')
                            ->relationship('products')
                            ->label('Products')
                            ->columnSpanFull()
                            ->minItems(1)
                            ->defaultItems(1)
                            ->addActionLabel('Add Product')
                            ->visible(fn($get) => $get('conversion_type') === 'pob')
                            ->required(fn($get) => $get('conversion_type') === 'pob')
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->placeholder('Product Name')
                                    ->options(Product::all()->pluck('name', 'id'))
                                    ->searchable()
                                    ->required(),
                                TextInput::make('quantity')
                                    ->label('Quantity')
                                    ->placeholder('Product Qty')
                                    ->numeric()
                                    ->required(),
                            ])->columns(2),
                        TextInput::make('invoice_amount')
                            ->label('POB Value')
                            ->placeholder('POB amount')
                            ->numeric()
                            ->visible(fn($get) => $get('conversion_type') === 'pob')
                            ->prefix('₹')
                            ->required(fn($get) => $get('conversion_type') === 'pob'),
                        FileUpload::make('invoice_image')
                            ->label('POB Image')
                            ->disk('s3')
                            ->visible(fn($get) => $get('conversion_type') === 'pob')
                            ->image()
                            ->directory('manager-log-entry-activities/pob')
                            ->maxSize(2048),

                        /*-------------------------------------CALL INPUTS --------------------------------*/

                        Select::make('call_inputs')
                            ->label('Call Inputs')
                            ->multiple()
                            ->relationship('callInputs', 'name')
                            ->required()
                            ->preload()
                            ->searchable(),
                        // Add other fields as needed
                    ])->columns(2),
                Textarea::make('remark')
                    ->label('Remark (optional)')
                    ->columnSpanFull()
                    ->minLength(3)
                    ->maxLength(255)
                    ->autosize()
                    ->placeholder('Write your feedback/suggestions here...'),

            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('submitWithConfirmation')
                ->label('Submit')
                ->requiresConfirmation()
                ->action(fn() => $this->submitForm()),
            $this->getCancelFormAction(),
        ];
    }

    public function submitForm()
    {
        $this->submit();
    }
    /*-----------------------------------------------------TABLE --------------------------------------------------------*/

    public static function table(\Filament\Tables\Table $table): \Filament\Tables\Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->prefix('CT-')
                    ->sortable()
                    ->visible(fn(): bool => Auth::user()->can('view_user'))
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('date')
                    ->label('Date')
                    ->sortable()
                    ->date('d M y'),
                TextColumn::make('user.name')
                    ->label('Manager')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('user.roles.name')
                    ->badge()
                    ->toggleable()
                    ->visible(fn(): bool => Auth::user()->can('view_user'))
                    ->color(fn(string $state): string => match ($state) {
                        'RSM' => 'danger',
                        'ASM' => 'warning',
                        'DSA' => 'info',
                        'ZSM' => 'success',
                        'NSM' => 'primary',
                        default => 'primary'
                    })
                    ->label('Desgn.')
                    ->sortable()
                    ->searchable(),
                IconColumn::make('activity_doctor_met')
                    ->label('Met Dr.')
                    ->sortable()
                    ->toggleable()
                    ->icon(fn($state) => ($state == true) ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ->color(fn($state) => ($state == true) ? 'success' : 'danger'),
                TextColumn::make('activities.doctor_converted')
                    ->label('Conversion')
                    ->toggleable()
                    ->default(false)
                    ->sortable()
                    ->formatStateUsing(fn($state) => ($state == true) ? 'Yes' : 'No')
                    ->color(fn($state) => ($state == true) ? 'success' : 'danger'),
                TextColumn::make('converted_activities_count')
                    ->label('Conversion Count')
                    ->sortable()
                    ->toggleable(),

                    TextColumn::make('doctors_met')
                    ->toggleable()
                    ->label('Doctors Met')
                    ->sortable()
                    ->numeric(),

            ])
            ->defaultSort('date', 'desc')
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make()
                    ->visible(fn(): bool => Auth::user()->can('delete_user')),
            ]);

    }
    /*-----------------------------------------------------INFOLIST --------------------------------------------------------*/
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Conversion Info.')
                    ->columns(4)
                    ->collapsible()
                    ->compact()
                    ->schema([
                        TextEntry::make('campaignEntry.campaign.name')
                            ->label('Campaign'),
                        TextEntry::make('campaignEntry.campaign.tags.name')
                            ->label('Tags'),
                        TextEntry::make('date')
                            ->label('Conversion Date')
                            ->date('d M Y')
                            ->visible(fn($record) => $record->date),
                        TextEntry::make('created_at')
                            ->label('Submission Date')
                            ->date('d M Y'),
                        TextEntry::make('user.name')
                            ->label('Submitted By'),
                        TextEntry::make('worked_with_team')
                            ->label('Working')
                            ->formatStateUsing(fn($state) => ($state == true) ? 'With Team' : 'Independently'),

                        TextEntry::make('doctors_met')
                            ->label('Doctors Met')
                            ->visible(fn($record) => $record->doctors_met),
                        IconEntry::make('activity_doctor_met')
                            ->label('Met Activity Doctor')
                            ->icon(fn($state) => ($state == true) ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                    ]),

                /*-------------------------------------TEAM MEMBERS --------------------------------*/
                Section::make('Team Members')
                    ->collapsible()
                    ->compact()
                    ->visible(fn($record) => $record->worked_with_team == true && $record->colleagues && $record->colleagues->count())
                    ->schema([
                        TableRepeatableEntry::make('colleagues')
                            ->label('')
                            ->striped()
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name'),
                                TextEntry::make('user.roles.0.name')
                                    ->label('Designation'),
                                TextEntry::make('user.location.name')
                                    ->label('Location'),
                            ])
                            ->extraAttributes(['class' => 'hidden sm:block']) // Hidden on mobile, visible on sm and up
                            ->columns(3),
                        RepeatableEntry::make('colleagues')
                            ->label('')
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Name'),
                                TextEntry::make('user.roles.0.name')
                                    ->label('Designation'),
                                TextEntry::make('user.location.name')
                                    ->label('Location'),
                            ])
                            ->extraAttributes(['class' => 'block sm:hidden']) // Visible only on mobile
                            ->columns(3),

                    ]),
                /*-------------------------------------ACTIVITIES --------------------------------*/
                Section::make('Activities')
                    ->collapsible()
                    ->visible(fn($record) => $record->activity_doctor_met == 'true')
                    ->compact()
                    ->schema([
                        /*------------------------------------- DESKTOP ACTIVITIES TABLE --------------------------------*/
                        TableRepeatableEntry::make('activities')
                            ->label('')
                            ->striped()
                            ->columns(4)
                            ->extraAttributes(['class' => 'hidden sm:block'])
                            ->schema([
                                // COLUMN 1
                                TextEntry::make('customer.name')
                                    ->label('Customer'),
                                // COLUMN 2
                                TextEntry::make('customer.headquarter.name')

                                    ->label('HQ'),
                                // COLUMN 3
                                IconEntry::make('doctor_converted')
                                    ->label('Doctor Converted')
                                    ->icon(fn($state) => ($state == true) ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                    ->color(fn($state) => ($state == true) ? 'success' : 'danger'),
                                // COLUMN 4
                                TextEntry::make('callInputs')
                                    ->label('Call Inputs')
                                    ->formatStateUsing(fn($state, $record) => $record
                                        ->callInputs
                                        ->pluck('name')
                                        ->implode(', ') ?: '-'),
                                // COLUMN 5
                                Section::make('')
                                    ->heading(fn($record) => ($record->conversion_type == 'pob') ? 'POB' : 'Prescription')
                                    ->label('Conversion Details')
                                    ->collapsible()
                                    ->collapsed()
                                    ->compact()
                                    ->columns(3)
                                    ->visible(fn($record) => $record->doctor_converted == true)
                                    ->schema([
                                        TextEntry::make('no_of_prescriptions')
                                            ->visible(fn($record) => $record->conversion_type == 'prescription')
                                            ->label('No. of Rx'),
                                        ImageEntry::make('prescription_image')
                                            ->label('Rx Image')
                                            ->visible(fn($record) => $record->conversion_type == 'prescription' && $record->prescription_image)
                                            ->visibility('private')
                                            ->checkFileExistence(false)
                                            ->url(fn($record) => $record->prescription_image ? Storage::temporaryUrl($record->prescription_image, now()->addMinutes(5)) : '')
                                            ->disk('s3'),
                                        TableRepeatableEntry::make('products')
                                            ->visible(fn($record) => $record->conversion_type == 'pob')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('product.name')
                                                    ->label('Product'),
                                                TextEntry::make('quantity')
                                                    ->label('Quantity'),
                                            ])
                                            ->columns(2),
                                        TextEntry::make('invoice_amount')
                                            ->label('Invoice Amount')
                                            ->visible(fn($record) => $record->conversion_type == 'pob')
                                            ->prefix('₹'),
                                        ImageEntry::make('invoice_image')
                                            ->label('Invoice Image')
                                            ->visible(fn($record) => $record->conversion_type == 'pob' && $record->invoice_image)
                                            ->visibility('private')
                                            ->checkFileExistence(false)
                                            ->url(fn($record) => $record->invoice_image ? Storage::temporaryUrl($record->invoice_image, now()->addMinutes(5)) : '')
                                            ->disk('s3'),
                                    ]),

                            ]),
                        /*------------------------------------- Mobile ACTIVITIES TABLE --------------------------------*/
                        RepeatableEntry::make('activities')
                            ->label('')
                            ->columns(4)
                            ->extraAttributes(['class' => 'block sm:hidden'])
                            ->schema([
                                // COLUMN 1
                                TextEntry::make('customer.name')
                                    ->label('Customer'),
                                // COLUMN 2
                                TextEntry::make('customer.headquarter.name')

                                    ->label('HQ'),
                                // COLUMN 3
                                IconEntry::make('doctor_converted')
                                    ->label('Doctor Converted')
                                    ->icon(fn($state) => ($state == true) ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                                    ->color(fn($state) => ($state == true) ? 'success' : 'danger'),
                                // COLUMN 4
                                TextEntry::make('callInputs')
                                    ->label('Call Inputs')
                                    ->formatStateUsing(fn($state, $record) => $record
                                        ->callInputs
                                        ->pluck('name')
                                        ->implode(', ') ?: '-'),
                                // COLUMN 5
                                Section::make('')
                                    ->heading(fn($record) => ($record->conversion_type == 'pob') ? 'POB' : 'Prescription')
                                    ->label('Conversion Details')
                                    ->collapsible()
                                    ->collapsed()
                                    ->compact()
                                    ->columns(3)
                                    ->visible(fn($record) => $record->doctor_converted == true)
                                    ->schema([
                                        TextEntry::make('no_of_prescriptions')
                                            ->visible(fn($record) => $record->conversion_type == 'prescription')
                                            ->label('No. of Rx'),
                                        ImageEntry::make('prescription_image')
                                            ->label('Rx Image')
                                            ->visible(fn($record) => $record->conversion_type == 'prescription' && $record->prescription_image)
                                            ->visibility('private')
                                            ->checkFileExistence(false)
                                            ->url(fn($record) => $record->prescription_image ? Storage::temporaryUrl($record->prescription_image, now()->addMinutes(5)) : '')
                                            ->disk('s3'),
                                        TableRepeatableEntry::make('products')
                                            ->visible(fn($record) => $record->conversion_type == 'pob')
                                            ->label('')
                                            ->schema([
                                                TextEntry::make('product.name')
                                                    ->label('Product'),
                                                TextEntry::make('quantity')
                                                    ->label('Quantity'),
                                            ])
                                            ->columns(2),
                                        TextEntry::make('invoice_amount')
                                            ->label('Invoice Amount')
                                            ->visible(fn($record) => $record->conversion_type == 'pob')
                                            ->prefix('₹'),
                                        ImageEntry::make('invoice_image')
                                            ->label('Invoice Image')
                                            ->visible(fn($record) => $record->conversion_type == 'pob' && $record->invoice_image)
                                            ->visibility('private')
                                            ->checkFileExistence(false)
                                            ->url(fn($record) => $record->invoice_image ? Storage::temporaryUrl($record->invoice_image, now()->addMinutes(5)) : '')
                                            ->disk('s3'),
                                    ]),

                            ]),

                    ]),
                /*------------------------------------- INPUTS SECTION --------------------------------*/

                Section::make('Misc.')
                    ->schema([
                        TextEntry::make('')
                            ->label('Call Inputs')
                            ->hidden(fn($record) => $record->activity_doctor_met == 'true')
                            ->state(function ($record) {
                                // $record is the ManagerLogEntry
                                if (!$record->activities || $record->activities->isEmpty()) {
                                    return '-';
                                }
                                // Collect all callInputs from all activities, flatten, and get unique names
                                $callInputs = $record->activities
                                    ->flatMap(function ($activity) {
                                    return $activity->callInputs ?? [];
                                })
                                    ->pluck('name')
                                    ->unique()
                                    ->implode(', ');

                                return $callInputs ?: '-';
                            }),
                        TextEntry::make('remark')
                            ->label('Remark')
                            ->visible(fn($record) => $record->remark),
                    ])
                    ->collapsible()
                    ->columns(2),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // ...
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'colleagues.user',
                'colleagues.user.location',
                'colleagues.user.roles',
                'activities.customer',
                'activities.customer.headquarter',
                'activities.callInputs',
                'activities.products',
                'activities.products.product',
                'activities.callInputs',

            ]) ->withCount([
                'activities as converted_activities_count' => function ($query) {
                    $query->where('doctor_converted', true);
                }
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListManagerLogEntries::route('/'),
            'create' => Pages\CreateManagerLogEntry::route('/create'),
            'edit' => Pages\EditManagerLogEntry::route('/{record}/edit'),
            'view' => Pages\ViewManagerLogEntry::route('/{record}/view'),
        ];
    }
}
