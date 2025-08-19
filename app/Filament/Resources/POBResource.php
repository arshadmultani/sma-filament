<?php

namespace App\Filament\Resources;

use App\Models\POB;
use Filament\Tables;
use App\Models\Doctor;
use App\Models\Chemist;
use App\Models\Product;
use App\Models\Campaign;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Settings\POBSettings;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use function Illuminate\Log\log;
use Illuminate\Support\Facades\Log;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Cache;
use Filament\Support\Enums\FontWeight;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\Split;
use Filament\Forms\Components\FileUpload;
use Filament\Infolists\Components\Section;
use Filament\Forms\Components\MorphToSelect;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Resources\POBResource\Pages;
use Filament\Forms\Components\Actions\Action;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Icetalker\FilamentTableRepeatableEntry\Infolists\Components\TableRepeatableEntry;

class POBResource extends Resource
{
    protected static ?string $model = POB::class;

    protected static ?string $navigationGroup = 'Activities';

    protected static ?string $modelLabel = 'POB';

    protected static ?string $pluralLabel = 'POB';

    protected static ?string $slug = 'pob';

    /**
     * Generate URL to a model's resource view page
     */
    public static function getCustomerResourceUrl($customer): ?string
    {
        if (!$customer) {
            return null;
        }

        $resourceClass = 'App\\Filament\\Resources\\' . class_basename($customer) . 'Resource';

        if (class_exists($resourceClass)) {
            return $resourceClass::getUrl('view', ['record' => $customer]);
        }

        return null;
    }
    public static function getS3ImageUrls($imageArray, int $expirationMinutes = 5): array
    {
        if (!$imageArray || !is_array($imageArray)) {
            return [];
        }

        return collect($imageArray)->map(function ($imagePath) use ($expirationMinutes) {
            return Storage::temporaryUrl($imagePath, now()->addMinutes($expirationMinutes));
        })->toArray();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
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
                MorphToSelect::make('customer')
                    ->types([
                        MorphToSelect\Type::make(Doctor::class)
                            ->titleAttribute('name')
                            ->label('Doctor')
                            ->modifyOptionsQueryUsing(fn($query) => $query->approved()->orderBy('name', 'asc')),
                        MorphToSelect\Type::make(Chemist::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn($query) => $query->approved()->orderBy('name', 'asc')),
                    ])
                    ->label('Customer (Type & Name)')
                    ->native(false)
                    ->searchable()
                    ->loadingMessage('Loading customers...')
                    ->noSearchResultsMessage('Customer not found')
                    ->optionsLimit(10)
                    ->preload() // this is causing the issue for admin in 5L+ entries are there. fix this later
                    ->required(),
                Repeater::make('pobProducts')
                    ->label('Products')
                    ->minItems(1)
                    ->columns(2)
                    ->reorderable(false)
                    ->itemLabel(
                        fn(array $state): string => Product::find($state['product_id'])?->name ?? ''
                    )
                    ->collapsible()
                    ->relationship('pobProducts')
                    ->columnSpanFull()
                    ->addActionLabel('Add Product')
                    ->deleteAction(
                        fn(Action $action) => $action->requiresConfirmation()
                    )
                    ->schema([
                        Select::make('product_id')
                            ->label('Product Name')
                            ->placeholder('Select Product')
                            ->required()
                            ->distinct()
                            ->optionsLimit(30)
                            ->reactive()
                            ->searchable()
                            ->options(
                                function () {
                                    return Cache::remember(Product::SELECT_CACHE_KEY, now()->addHours(24), function () {
                                        return Product::pluck('name', 'id')->all();
                                    });
                                }
                            ),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->placeholder('Product Qty.')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ]),
                \Filament\Forms\Components\Section::make()
                    ->columns(2)
                    ->schema([
                        FileUpload::make('invoice_image')
                            ->image()
                            ->label('Invoice Image')
                            // ->multiple()
                            // ->maxFiles(app(POBSettings::class)->max_invoices)
                            // ->helperText('No. of Images allowed: ' . app(POBSettings::class)->max_invoices)
                            ->disk('s3')
                            ->directory('pob-invoices')
                            ->downloadable()
                            ->maxSize(app(POBSettings::class)->max_invoice_size)
                            ->required(),
                        TextInput::make('invoice_amount')
                            ->label('POB Value')
                            ->placeholder('Total value')
                            ->required()
                            ->prefix('₹')
                            ->numeric()
                            ->minValue(1),
                    ]),



            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100, 250])
            ->columns([
                TextColumn::make('id')
                    ->label('POB #')
                    ->toggleable()
                    ->searchable()
                    ->sortable(),
                TextColumn::make('campaignEntry.campaign.name')
                    ->label('Campaign')
                    ->toggleable(),
                TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make(name: 'customer_type')
                    ->label('Cx. Type')
                    ->searchable()
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.name')->label('Submitted By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('user.division.name')->label('Division')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('state.name')->label('Status')
                    ->badge()
                    ->sortable()
                    ->color(fn($record) => $record->state->color),
                TextColumn::make('invoice_amount')
                    ->label('POB Amount')
                    ->money('inr')
                    ->sortable(),
                TextColumn::make('created_at')->label('Submission')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([

            ])
            ->actions([
                // Tables\Actions\ViewAction::make(),
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

                Section::make('Activity Information')
                    ->compact()
                    ->collapsible()
                    ->columns(4)
                    ->columnSpanFull()
                    ->grow(true)
                    ->schema([
                        TextEntry::make('customer.name')
                            ->label('Customer')
                            ->url(fn($record) => static::getCustomerResourceUrl($record->customer))
                            ->color('primary')
                            ->extraAttributes(['class' => 'hover:underline']),
                        TextEntry::make('customer_type')
                            ->formatStateUsing(fn($state) => Str::ucfirst($state))
                            ->label('Customer Type'),
                        TextEntry::make('headquarter.name')
                            ->label('Headquarter'),
                        TextEntry::make('campaignEntry.campaign.name')
                            ->label('Campaign'),
                        TextEntry::make('user.name')
                            ->label('Submitted By'),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('d-m-y @ H:i'),
                        TextEntry::make('state.name')
                            ->label('Status')
                            ->badge()
                            ->color(fn($record) => $record->state->color)

                    ]),
                Section::make('Products')
                    ->compact()
                    ->collapsible()
                    ->schema([
                        TableRepeatableEntry::make('pobProducts')
                            ->label('')
                            ->columns(2)
                            ->extraAttributes(['class' => 'hidden sm:block']) // Visible only on mobile
                            ->schema([
                                TextEntry::make('product.name'),
                                TextEntry::make('quantity')
                            ]),
                        RepeatableEntry::make('pobProducts')
                            ->label('')
                            ->columns(2)
                            ->extraAttributes(['class' => 'block sm:hidden']) // Visible only on mobile
                            ->schema([
                                TextEntry::make('product.name'),
                                TextEntry::make('quantity')
                            ])

                    ]),
                Section::make('Invoice')
                    ->compact()
                    // ->columns(2)
                    ->collapsible()
                    ->schema([
                        ImageEntry::make('invoice_image')
                            ->label('Invoice')
                            ->disk('s3')
                            ->visibility('private')
                            ->url(fn($record) => $record->invoice_image ? Storage::temporaryUrl($record->invoice_image, now()->addMinutes(5)) : '')
                            ->checkFileExistence(false),
                        TextEntry::make('invoice_amount')->label('Total Amount')->money('INR')->weight(FontWeight::SemiBold),

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
            'index' => Pages\ListPOBS::route('/'),
            'create' => Pages\CreatePOB::route('/create'),
            'edit' => Pages\EditPOB::route('/{record}/edit'),
            'view' => Pages\ViewPOB::route('/{record}'),
        ];
    }
}
