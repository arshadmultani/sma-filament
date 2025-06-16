<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendKofolCouponAction;
use App\Filament\Actions\UpdateKofolStatusAction;
use App\Filament\Resources\KofolEntryResource\Pages;
use App\Models\Chemist;
use App\Models\Doctor;
use App\Models\KofolEntry;
use App\Models\Product;
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
use Icetalker\FilamentTableRepeatableEntry\Infolists\Components\TableRepeatableEntry;
use Illuminate\Support\Collection;

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
        ];
    }

    protected static ?string $model = KofolEntry::class;

    protected static ?string $navigationGroup = 'Kofol Swarna Varsha';

    protected static ?string $modelLabel = 'Campaign Entries';

    public static function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                // campaign name
                Select::make('kofol_campaign_id')
                    ->relationship('kofolCampaign', 'name', fn ($query) => $query->where('is_active', true))
                    ->native(false)
                    ->required(),

                // customer details
                MorphToSelect::make('customer')
                    ->types([
                        MorphToSelect\Type::make(Doctor::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn ($query) => $query->where('status', 'Approved')),
                        MorphToSelect\Type::make(Chemist::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn ($query) => $query->where('status', 'Approved')),
                    ])
                    ->native(false)
                    ->preload()
                    ->searchable()
                    ->required(),

                // products
                Repeater::make('products')
                    ->collapsible()
                    ->columns(2)
                    ->addActionLabel('Add Product')
                    ->reorderable(false)
                    ->itemLabel(
                        fn (array $state): string => Product::find($state['product_id'])?->name ?? ''
                    )
                    ->minItems(1)
                    ->deleteAction(fn (Action $action) => $action->requiresConfirmation())
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
                            ->required()
                            ->reactive(),
                        FileUpload::make('invoice_image')
                            ->image()
                            ->downloadable()
                            ->maxSize(2048)
                            ->required(),

                    ]),
            ]);
    }

    private static function getKofolProductOptions(): Collection
    {
        return Product::query()
            ->whereHas('brand', fn ($query) => $query->where('name', 'Kofol'))
            ->pluck('name', 'id');
    }

    // private static function updateInvoiceTotal($state, callable $set): void
    // {
    //     $total = collect($state)
    //         ->pluck('price')
    //         ->map(fn($price) => (float) $price)
    //         ->sum();

    //     $set('invoice_amount', $total);
    // }

    // private static function updateProductPrice($state, callable $set, callable $get): void
    // {
    //     $quantity = $get('quantity') ?: 1;
    //     $product = $state ? Product::find($state) : null;
    //     $set('price', $product ? $product->price * $quantity : '');

    //     static::recalculateTotal($set, $get);
    // }

    // private static function updateQuantityPrice($state, callable $set, callable $get): void
    // {
    //     $productId = $get('product_id');
    //     $product = $productId ? Product::find($productId) : null;
    //     $set('price', $product ? $product->price * ($state ?: 1) : '');

    //     static::recalculateTotal($set, $get);
    // }

    // private static function recalculateTotal(callable $set, callable $get): void
    // {
    //     $products = $get('../../products');
    //     $total = collect($products)
    //         ->pluck('price')
    //         ->map(fn($price) => (float) $price)
    //         ->sum();

    //     $set('../../invoice_amount', $total);
    // }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated([25, 50, 100, 250])
            ->columns([
                TextColumn::make('id')->label('ID')
                    ->sortable()
                    ->prefix('KSV/POB/')
                    ->label('Invoice #')
                    ->toggleable()
                    ->weight(FontWeight::Bold),
                TextColumn::make('kofolCampaign.name')
                    ->toggleable(),
                TextColumn::make('customer.name')
                    ->label('Cx. Name')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make(name: 'customer_type')
                    ->label('Cx. Type')
                    ->searchable()
                    ->formatStateUsing(fn ($state) => class_basename($state))
                    ->toggleable(),
                ImageColumn::make('invoice_image')
                    ->label('Invoice')
                    ->circular()
                    ->simpleLightbox()
                    ->toggleable(),
                TextColumn::make('user.name')->label('Submitted By')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('status')->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'primary',
                        'Rejected' => 'danger',
                        default => 'secondary'
                    }),
                TextColumn::make('invoice_amount')
                    ->label('Amount')
                    ->sortable()
                    ->money('INR'),
                TextColumn::make('created_at')->label('Submission')
                    ->since()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('updated_at')->label('Last Update')
                    ->since()
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    UpdateKofolStatusAction::makeBulk(),
                    SendKofolCouponAction::makeBulk(),
                ]),
            ]);
    }

    // // infolist on view page

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->columns(4)
            ->schema([
                Components\Section::make()
                    ->columns([
                        'sm' => 2,
                        'md' => 4,
                    ])
                    ->columnSpan(4)
                    ->schema([
                        TextEntry::make('kofolCampaign.name'),
                        TextEntry::make('created_at')->label(label: 'Submission')->dateTime('d-m-y @ H:i'),
                        TextEntry::make('status')->label('Status')
                            ->badge()
                            ->color(fn (string $state): string => match ($state) {
                                'Pending' => 'warning',
                                'Approved' => 'primary',
                                'Rejected' => 'danger',
                                default => 'secondary'
                            }),
                        TextEntry::make('coupon_code')->label('Coupon Code')
                            ->visible(fn ($state, $record) => ! is_null($state) && $record->status === 'Approved')
                            ->badge()
                            ->color('gray'),

                    ]),
                Components\Section::make()
                    ->columns([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->columnSpan([
                        'sm' => 1,
                        'md' => 3,
                    ])
                    ->schema([
                        TextEntry::make('customer.name'),
                        TextEntry::make('customer_type')->formatStateUsing(fn ($state) => class_basename($state)),
                        // Have to fix HQ ......
                        TextEntry::make('user.headquarter.name')->label('Headquarter'),

                    ]),
                Components\Section::make()
                    ->columns(1)
                    ->columnSpan([
                        'sm' => 4,
                        'md' => 1,
                    ])
                    ->schema([
                        TextEntry::make('user.name'),

                    ]),

                Components\Section::make()
                    ->columnSpan(3)
                    ->schema([
                        TableRepeatableEntry::make('products') // repeater for desktop
                            ->columnSpan(2)
                            ->striped()
                            ->extraAttributes(['class' => 'hidden sm:block']) // Hidden on mobile, visible on sm and up
                            ->schema([
                                TextEntry::make('product_id')
                                    ->columnSpan(2)
                                    ->label('Product')
                                    ->formatStateUsing(fn ($state) => Product::find($state)?->name ?? ''),
                                TextEntry::make('quantity')->columnSpan(1),
                                TextEntry::make('price')->money('INR')->columnSpan(1),
                            ]),
                        RepeatableEntry::make('products')  // repeater for mobile
                            ->columns(2)
                            ->extraAttributes(['class' => 'block sm:hidden']) // Visible only on mobile
                            ->schema([
                                TextEntry::make('product_id')
                                    ->columnSpan(2)
                                    ->label('Product')
                                    ->formatStateUsing(fn ($state) => Product::find($state)?->name ?? ''),
                                TextEntry::make('quantity'),
                                TextEntry::make('price')->money('INR'),
                            ]),
                    ]),
                Components\Section::make()
                    ->columnSpan(1)
                    ->schema([
                        ImageEntry::make('invoice_image')->label('Invoice')->square()->simpleLightbox()->columnSpan(2),
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
}
