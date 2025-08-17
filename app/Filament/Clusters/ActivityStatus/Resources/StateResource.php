<?php

namespace App\Filament\Clusters\ActivityStatus\Resources;

use App\Enums\StateCategory;
use App\Filament\Clusters\ActivityStatus;
use App\Filament\Clusters\ActivityStatus\Resources\StateResource\Pages;
use App\Models\State;
use App\Traits\HandlesDeleteExceptions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StateResource extends Resource
{
    use HandlesDeleteExceptions;
    protected static ?string $model = State::class;

    protected static ?string $navigationLabel = 'Activity State';

    protected static ?string $cluster = ActivityStatus::class;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                TextInput::make('name')
                    ->live(onBlur: true)
                    ->placeholder('State Name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                        if (($get('slug') ?? '') !== Str::slug($old)) {
                            return;
                        }

                        $set('slug', Str::slug($state));
                    }),
                Select::make('color')
                    ->label('State Colour')
                    ->options([
                        'danger' => 'Red',
                        'success' => 'Green',
                        'info' => 'Blue',
                        'warning' => 'Yellow',
                    ])
                    ->native(false)
                    ->placeholder('Set status colour')
                    ->required(),
                Select::make('category')
                    ->options(StateCategory::class)
                    ->native(false)
                    ->required(),

                Toggle::make('is_active')
                    ->label(function ($state) {
                        return $state ? 'Active' : 'Inactive';
                    })
                    ->live()
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true),
                TextInput::make('slug')
                    ->label('')
                    ->helperText('system name')
                    ->disabled()
                    ->readOnly()
                    ->dehydrated()
                    ->unique(ignoreRecord: true),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->badge()
                    ->color(fn($record) => $record->color),
                Tables\Columns\TextColumn::make('is_active'),
                Tables\Columns\TextColumn::make('category'),

            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()
                    ->before(fn($action, $record) => (new static())->tryDeleteRecord($record, $action)),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(fn($action, $records) => collect($records)->each(fn($record) => (new static())->tryDeleteRecord($record, $action))),
                ]),
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
            'index' => Pages\ListState::route('/'),
            'create' => Pages\CreateState::route('/create'),
            'edit' => Pages\EditState::route('/{record}/edit'),
        ];
    }

    public static function getRecordRouteKeyName(): string
    {
        return 'id';
    }

    public static function canEdit(Model $record): bool
    {
        return !$record->getAttribute('is_system');
    }

    public static function canDelete(Model $record): bool
    {
        return !$record->getAttribute('is_system');
    }
}
