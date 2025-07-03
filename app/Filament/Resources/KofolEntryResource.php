<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendKofolCouponAction;
use App\Filament\Actions\UpdateKofolStatusAction;
use App\Filament\Resources\KofolEntryResource\Pages;
use App\Models\Chemist;
use App\Models\Doctor;
use App\Models\KofolEntry;
use App\Models\Product;
use App\Models\Campaign;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Support\Enums\FontWeight;
use Filament\Tables;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Icetalker\FilamentTableRepeatableEntry\Infolists\Components\TableRepeatableEntry;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;


class KofolEntryResource extends Resource implements HasShieldPermissions
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
            'send_coupon',
        ];
    }

    protected static ?string $model = KofolEntry::class;

    protected static ?string $navigationGroup = 'Activities';

    protected static ?string $modelLabel = 'KSV Booking';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                // campaign name
                Select::make('campaign_id')
                    ->label('Campaign')
                    ->options(function () {
                        return Campaign::query()
                            ->where('allowed_entry_type', 'kofol_entry')
                            ->where('is_active', true)
                            ->pluck('name', 'id');
                    })
                    ->required()
                    ->preload()
                    ->searchable()
                    ->dehydrated(false)
                    ->native(false),
                // customer details
                MorphToSelect::make('customer')
                    ->types([
                        MorphToSelect\Type::make(Doctor::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn($query) => $query->where('status', 'Approved')),
                        MorphToSelect\Type::make(Chemist::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn($query) => $query->where('status', 'Approved')),
                    ])
                    ->native(false)
                    ->searchable()
                    ->optionsLimit(10)
                    // ->preload() // this is causing the issue for admin in 5L+ entries are there.. fix this later
                    ->required(),

                // products
                Repeater::make('products')
                    ->collapsible()
                    ->columns(2)
                    ->addActionLabel('Add Product')
                    ->reorderable(false)
                    ->itemLabel(
                        fn(array $state): string => Product::find($state['product_id'])?->name ?? ''
                    )
                    ->minItems(1)
                    ->deleteAction(fn(Action $action) => $action->requiresConfirmation())
                    // ->afterStateUpdated(fn($state, callable $set) => static::updateInvoiceTotal($state, $set))
                    // ->afterStateHydrated(fn($state, callable $set) => static::updateInvoiceTotal($state, $set))
                    ->schema([
                        Select::make('product_id')
                            ->label('Kofol Products')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(static::getKofolProductOptions())
                            ->required()
                            ->reactive(),
                        // ->afterStateUpdated(
                        //     fn($state, callable $set, callable $get) =>
                        //     static::updateProductPrice($state, $set, $get)
                        // ),

                        TextInput::make('quantity')
                            ->numeric()
                            ->minValue(1)
                            ->type('number')
                            ->default(1)
                            ->required()
                            ->reactive(),
                        // ->afterStateUpdated(
                        //     fn($state, callable $set, callable $get) =>
                        //     static::updateQuantityPrice($state, $set, $get)
                        // ),
                    ]),

                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('invoice_amount')
                            ->label('Invoice Amount')
                            ->prefix('₹')
                            ->type('number')
                            ->required()
                            ->reactive(),
                        FileUpload::make('invoice_image')
                            ->image()
                            ->disk('s3')
                            ->visibility('public')
                            ->directory('kofol-invoices')
                            ->downloadable()
                            ->maxSize(5120)
                            ->required(),

                    ]),
            ]);
    }

    private static function getKofolProductOptions(): Collection
    {
        return Product::query()
            ->whereHas('brand', fn($query) => $query->where('name', 'Kofol'))
            ->pluck('name', 'id');
    }


    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginated([25, 50, 100, 250])
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->sortable()
                    ->prefix('KSV/POB/')
                    ->label('Invoice #')
                    ->toggleable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('campaignEntry.campaign.name')
                    ->label('Campaign')
                    ->toggleable(),
                TextColumn::make('customer.name')
                    ->label('Cx. Name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make(name: 'customer_type')
                    ->label('Cx. Type')
                    ->searchable()
                    ->formatStateUsing(fn($state) => class_basename($state))
                    ->toggleable(),
                ImageColumn::make('invoice_image')
                    ->label('Invoice')
                    // ->visibility('public')
                    // ->disk('s3')
                    ->circular()
                    ->simpleLightbox()
                    ->toggleable(),
                TextColumn::make('user.name')->label('Submitted By')
                    ->searchable()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'primary',
                        'Rejected' => 'danger',
                        default => 'secondary'
                    }),
                TextColumn::make('invoice_amount')
                    ->label('Amount')
                    ->sortable()
                    ->money('INR'),
                TextColumn::make('coupon_codes_list')
                    ->label('Coupon Codes')
                    ->formatStateUsing(fn($state, $record) => ($record && isset($record->coupons) && $record->coupons ? $record->coupons->pluck('coupon_code')->implode(', ') : ''))
                    ->visible(fn($record) => $record && isset($record->coupons) && $record->coupons && $record->coupons->isNotEmpty()),
                TextColumn::make('coupon_count')
                    ->label('Coupons')
                    ->state(fn($record) => $record && $record->coupons ? $record->coupons->count() : '0'),
                TextColumn::make('created_at')->label('Submission')
                    ->since()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')->label('Last Update')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                // Removed coupon_code filter
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    // UpdateKofolStatusAction::makeBulk(), // Removed as bulk actions are commented out
                    SendKofolCouponAction::makeBulk(),
                ]),
            ]);
    }
    // // infolist on view page

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make('Customer Details')
                ->collapsible()
                ->compact()
                    ->columns(4)
                    ->schema([
                        TextEntry::make('customer.name'),
                        TextEntry::make('customer_type')->formatStateUsing(fn($state) => class_basename($state)),
                        TextEntry::make('customer.headquarter.name')
                            ->label('Headquarter'),
                            TextEntry::make('user.name')->label('Submitted By'),
                    ]),
                Components\Section::make('Status')
                ->collapsible()
                ->compact()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('campaignEntry.campaign.name'),
                        TextEntry::make('created_at')->label(label: 'Submission')->dateTime('d-m-y @ H:i'),
                        TextEntry::make('status')->label('Status')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'Pending' => 'warning',
                                'Approved' => 'primary',
                                'Rejected' => 'danger',
                                default => 'secondary'
                            }),
                    ]),
                Components\Section::make('Coupons')
                ->collapsed()
                ->compact()
                    ->visible(fn($record) => $record && $record->coupons && $record->coupons->isNotEmpty())
                    ->columns(2)
                    ->schema([
                        TextEntry::make('coupon_count')
                            ->label('Coupon Count')
                            ->state(fn($record) => $record && $record->coupons ? $record->coupons->count() : '0'),
                        Components\RepeatableEntry::make('coupons')
                            ->label('Coupons')
                            ->contained(false)
                            ->grid(2)
                            ->schema([
                                TextEntry::make('coupon_code')->badge()->label(''),
                            ])
                            ->visible(fn($record) => $record && $record->coupons && $record->coupons->isNotEmpty()),
                    ]),
                Components\Section::make('Products')
                ->collapsible()
                ->compact()
                    ->schema([
                        TableRepeatableEntry::make('products') // repeater for desktop
                            ->columnSpan(2)
                            ->striped()
                            ->extraAttributes(['class' => 'hidden sm:block']) // Hidden on mobile, visible on sm and up
                            ->schema([
                                TextEntry::make('product_id')
                                    ->columnSpan(2)
                                    ->label('Product')
                                    ->formatStateUsing(fn($state) => Product::find($state)?->name ?? ''),
                                TextEntry::make('quantity')->columnSpan(1),
                            ]),
                        RepeatableEntry::make('products')  // repeater for mobile
                            ->extraAttributes(['class' => 'block sm:hidden']) // Visible only on mobile
                            ->schema([
                                TextEntry::make('product_id')
                                    ->columnSpan(2)
                                    ->label('Product')
                                    ->formatStateUsing(fn($state) => Product::find($state)?->name ?? ''),
                                TextEntry::make('quantity'),
                            ]),
                    ]),
                Components\Section::make()
                    ->schema([
                        ImageEntry::make('invoice_image')->label('Invoice')
                            // ->visibility('public')
                            // ->disk('s3')
                            ->square()
                            ->simpleLightbox()
                            ->columnSpan(2),
                        TextEntry::make('invoice_amount')->label('Total Amount')->money('INR')->weight(FontWeight::SemiBold),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListKofolEntries::route('/'),
            'create' => Pages\CreateKofolEntry::route('/create'),
            'edit' => Pages\EditKofolEntry::route('/{record}/edit'),
            'view' => Pages\ViewKofolEntry::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'coupons',
                'campaignEntry.campaign',
                'user.division',
            ])
            ->with([
                'customer' => function ($morphTo) {
                    $morphTo->morphWith([
                        \App\Models\Doctor::class => ['headquarter'],
                        \App\Models\Chemist::class => ['headquarter'],
                    ]);
                }
            ]);
    }
}
