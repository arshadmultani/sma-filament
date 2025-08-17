<?php

namespace App\Filament\Resources;

use App\Filament\Resources\POBResource\Pages;
use App\Models\Campaign;
use App\Models\Chemist;
use App\Models\Doctor;
use App\Models\POB;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MorphToSelect;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class POBResource extends Resource
{
    protected static ?string $model = POB::class;

    protected static ?string $navigationGroup = 'Activities';

    protected static ?string $modelLabel = 'POB';

    protected static ?string $pluralLabel = 'POB';

    protected static ?string $slug = 'pob';

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
                            ->modifyOptionsQueryUsing(fn($query) => $query->where('status', 'Approved')),
                        MorphToSelect\Type::make(Chemist::class)
                            ->titleAttribute('name')
                            ->modifyOptionsQueryUsing(fn($query) => $query->where('status', 'Approved')),
                    ])
                    ->native(false)
                    ->searchable()
                    ->optionsLimit(10)
                    ->preload() // this is causing the issue for admin in 5L+ entries are there. fix this later
                    ->required(),
                Repeater::make('pobProducts')
                    ->relationship('pobProducts')
                    ->columnSpanFull()
                    ->addActionLabel('Add Product')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->placeholder('Select Product')
                            ->required()
                            ->preload()
                            ->searchable()
                            ->relationship('product', 'name'),
                        TextInput::make('quantity')
                            ->label('Quantity')
                            ->required()
                            ->numeric()
                            ->minValue(1),
                    ]),
                TextInput::make('invoice_amount')
                    ->label('Invoice Amount')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                FileUpload::make('invoice_image')
                    ->image()
                    ->disk('s3')
                    ->directory('pob-invoices')
                    ->downloadable()
                    // ->maxSize(app(::class)->max_invoice_size)

                    ->required(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('customer.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('invoice_amount')
                    ->label('Invoice Amount')
                    ->money('inr')
                    ->sortable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([

            ])
            ->actions([
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
                TextEntry::make('campaign.name')
                    ->label('Campaign'),
                TextEntry::make('customer.name')
                    ->label('Customer'),
                TextEntry::make('headquarter.name')
                    ->label('Headquarter')
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
