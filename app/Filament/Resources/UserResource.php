<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Filament\Actions\SendMailAction;
use Filament\Forms\Set;
use Filament\Forms\Components\Select;
use App\Models\Region;
use App\Models\Area;
use App\Models\Headquarter;
use Filament\Forms\Get;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\UserFilters;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected ?string $plainPassword = null; //Temporary password

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Select::make('roles')
                            ->options(function () {
                                $query = Role::query();
                                /** @var \App\Models\User|null $user */
                                $user = Auth::user();
                                if (!$user?->hasRole('super_admin')) {
                                    $query->where('name', '!=', 'super_admin');
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->preload()
                            ->live()
                            ->native(false)
                            ->reactive()
                            ->required()
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),

                        Select::make('region_id')
                            ->label('Region')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(Region::all()->pluck('name', 'id'))
                            ->afterStateUpdated(fn(Set $set) => $set('area_id', null))
                            ->reactive()
                            ->required(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? Role::find($roleId)?->name : null;
                                return $roleName === 'RSM';
                            })
                            ->hidden(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? Role::find($roleId)?->name : null;
                                return !in_array($roleName, ['ASM', 'RSM', 'DSA']) || in_array($roleName, ['admin', 'super_admin']);
                            }),

                        Select::make('area_id')
                            ->label('Area')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(function (Get $get) {
                                $regionId = $get('region_id');
                                if ($regionId) {
                                    return Area::where('region_id', (int)$regionId)->pluck('name', 'id');
                                }
                                return [];
                            })
                            ->required(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? Role::find($roleId)?->name : null;
                                return $roleName === 'ASM';
                            })
                            ->hidden(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? Role::find($roleId)?->name : null;
                                return !in_array($roleName, ['ASM', 'DSA']) || in_array($roleName, ['admin', 'super_admin']);
                            })
                            // ->afterStateUpdated(fn(Set $set) => $set('area_id', null))
                            ->reactive(),

                        Select::make('headquarter_id')
                            ->label('Headquarter')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(function (Get $get) {
                                $areaId = $get('area_id');
                                if ($areaId) {
                                    return Headquarter::where('area_id', (int)$areaId)->pluck('name', 'id');
                                }
                                return [];
                            })
                            ->required(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? Role::find($roleId)?->name : null;
                                return $roleName === 'DSA';
                            })
                            ->hidden(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? Role::find($roleId)?->name : null;
                                return $roleName !== 'DSA' || in_array($roleName, ['admin', 'super_admin']);
                            })
                            // ->afterStateUpdated(fn(Set $set) => $set('headquarter_id', null))
                            ->reactive(),
                    ]),



                Section::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->required()
                            ->tel(),
                        Select::make('division_id')
                            ->native(false)
                            ->relationship('division', 'name')
                            ->required(),
                        TextInput::make('password')
                            ->visibleOn('create')
                            ->password()
                            ->revealable()
                            ->default(fn() => Str::random(8))
                            ->placeholder(fn($context) => $context === 'edit' ? 'Enter a new password to change' : null)
                            ->maxLength(255)
                            ->dehydrateStateUsing(function ($state, $livewire) {
                                // Store plain password in temporary variable
                                $livewire->plainPassword = $state;
                                // Return hashed password to be stored in DB
                                return Hash::make($state);
                            })
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->striped()
            ->columns([
                TextColumn::make('name')->searchable(),
                TextColumn::make('roles.name')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'RSM' => 'danger',
                        'ASM' => 'warning',
                        'DSA' => 'info',
                        default => 'primary'
                    })
                    ->label('Desgn.')
                    ->sortable()
                    ->searchable(),
                // TextColumn::make('email'),

                // Add computed columns for Region, Area, Headquarter
                TextColumn::make('region_name')
                    ->label('Region')
                    ->getStateUsing(function ($record) {
                        if ($record->location instanceof \App\Models\Region) {
                            return $record->location->name;
                        } elseif ($record->location instanceof \App\Models\Area) {
                            return $record->location->region?->name;
                        } elseif ($record->location instanceof \App\Models\Headquarter) {
                            return $record->location->area?->region?->name;
                        }
                        return '-';
                    }),
                TextColumn::make('area_name')
                    ->label('Area')
                    ->getStateUsing(function ($record) {
                        if ($record->location instanceof \App\Models\Area) {
                            return $record->location->name;
                        } elseif ($record->location instanceof \App\Models\Headquarter) {
                            return $record->location->area?->name;
                        }
                        return '-';
                    }),
                TextColumn::make('headquarter_name')
                    ->label('Headquarter')
                    ->getStateUsing(function ($record) {
                        if ($record->location instanceof \App\Models\Headquarter) {
                            return $record->location->name;
                        }
                        return '-';
                    }),
                // TextColumn::make('phone_number'),
                // TextColumn::make('division.name'),

            ])
            ->filtersFormColumns(2)
            ->filters(
                UserFilters::all()
            )
            ->actions([
                Tables\Actions\EditAction::make(),
                SendMailAction::make(),
            ])
            ->bulkActions([
                SendMailAction::makeBulk(),
                Tables\Actions\DeleteBulkAction::make(),
                Tables\Actions\ExportBulkAction::make()->exporter(UserExporter::class),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
    //     public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['roles', 'division', 'location']);
    }
}
