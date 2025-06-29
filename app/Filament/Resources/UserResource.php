<?php

namespace App\Filament\Resources;

use App\Filament\Actions\SendMailAction;
use App\Filament\Exports\UserExporter;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\UserFilters;
use App\Models\Area;
use App\Models\Headquarter;
use App\Models\Region;
use App\Models\Zone;
use App\Models\User;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Spatie\Permission\Models\Role;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Filters\SelectFilter;
use App\Models\Division;


class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 2;

    protected ?string $plainPassword = null; // Temporary password

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
                        Select::make('division_id')
                            ->native(false)
                            ->relationship('division', 'name')
                            // ->helperText(fn(Get $get) => static::needsDivision($get)
                            //     ? __('Leave empty if the HO user has access to all divisions.') : null)
                            ->required(fn(Get $get) => static::needsDivision($get)),

                        Select::make('zone_id')
                            ->label('Zone')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(fn(Get $get) => Zone::where('division_id', $get('division_id'))->pluck('name', 'id'))
                            ->afterStateUpdated(fn(Set $set) => $set('area_id', null))
                            ->reactive()
                            ->required(fn(Get $get) => static::getRole($get) === 'ZSM')
                            ->visible(fn(Get $get) => in_array(static::getRole($get), ['ASM', 'RSM', 'DSA', 'ZSM']))
                            ->hidden(fn(Get $get) => in_array(static::getRole($get), ['admin', 'super_admin'])),

                        Select::make('region_id')
                            ->label('Region')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(function (Get $get) {
                                $divisionId = $get('division_id');
                                if ($divisionId) {
                                    return \App\Models\Region::where('division_id', $divisionId)->pluck('name', 'id');
                                }
                                return [];
                            })
                            ->afterStateUpdated(fn(Set $set) => $set('area_id', null))
                            ->reactive()
                            ->required(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? \Spatie\Permission\Models\Role::find($roleId)?->name : null;
                                return $roleName === 'RSM';
                            })
                            ->hidden(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? \Spatie\Permission\Models\Role::find($roleId)?->name : null;
                                return !in_array($roleName, ['ASM', 'RSM', 'DSA']) || in_array($roleName, ['admin', 'super_admin']);
                            }),

                        Select::make('area_id')
                            ->label('Area')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(function (Get $get) {
                                $regionId = $get('region_id');
                                $divisionId = $get('division_id');
                                $query = \App\Models\Area::query();
                                if ($regionId) {
                                    $query->where('region_id', (int) $regionId);
                                }
                                if ($divisionId) {
                                    $query->whereHas('region', function ($q) use ($divisionId) {
                                        $q->where('division_id', $divisionId);
                                    });
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->required(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? \Spatie\Permission\Models\Role::find($roleId)?->name : null;
                                return $roleName === 'ASM';
                            })
                            ->hidden(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? \Spatie\Permission\Models\Role::find($roleId)?->name : null;
                                return !in_array($roleName, ['ASM', 'DSA']) || in_array($roleName, ['admin', 'super_admin']);
                            })
                            ->reactive(),

                        Select::make('headquarter_id')
                            ->label('Headquarter')
                            ->native(false)
                            ->preload()
                            ->searchable()
                            ->options(function (Get $get) {
                                $areaId = $get('area_id');
                                $divisionId = $get('division_id');
                                $query = \App\Models\Headquarter::query();
                                if ($areaId) {
                                    $query->where('area_id', (int) $areaId);
                                }
                                if ($divisionId) {
                                    $query->whereHas('area.region', function ($q) use ($divisionId) {
                                        $q->where('division_id', $divisionId);
                                    });
                                }
                                return $query->pluck('name', 'id');
                            })
                            ->required(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? \Spatie\Permission\Models\Role::find($roleId)?->name : null;
                                return $roleName === 'DSA';
                            })
                            ->hidden(function (Get $get) {
                                $roleId = $get('roles');
                                $roleName = $roleId ? \Spatie\Permission\Models\Role::find($roleId)?->name : null;
                                return $roleName !== 'DSA' || in_array($roleName, ['admin', 'super_admin']);
                            })
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
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('phone_number')
                            ->required()
                            ->tel(),

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
                    ]),

            ]);
    }
    protected static function getRole(Get $get): ?string
    {
        return Role::find($get('roles'))?->name;
    }

    protected static function needsDivision(Get $get): bool
    {
        $roleName = Role::find($get('roles'))?->name;
        if (!$roleName) {
            return true;
        }

        return !Role::where('name', $roleName)->first()?->hasPermissionTo('view_user');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->query(static::getEloquentQuery())
            ->defaultSort('name', 'asc')
            ->deferLoading()
            ->searchPlaceholder('Search User Name')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('roles.name')
                    ->badge()
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
                // TextColumn::make('email'),

                // Add computed columns for Region, Area, Headquarter
                TextColumn::make('zone_name')
                    ->label('Zone')
                    ->toggleable(),
                TextColumn::make('region_name')
                    ->label('Region')
                    ->toggleable()
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
                    ->toggleable()
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
                    ->toggleable(),
                    // ->getStateUsing(function ($record) {
                    //     if ($record->location instanceof \App\Models\Headquarter) {
                    //         return $record->location->name;
                    //     }

                    //     return '-';
                    // }),
                // TextColumn::make('phone_number'),
                TextColumn::make('division.name')->sortable(),

            ])
            ->filtersFormColumns(2)
            // ->filters(
                // UserFilters::all()
            // )
            ->actions([
                Tables\Actions\ViewAction::make(),
                Impersonate::make(),
                // Tables\Actions\RestoreAction::make(),
                // SendMailAction::make(),
            ])
            ->bulkActions([
                SendMailAction::makeBulk(),
                Tables\Actions\DeleteBulkAction::make(),
                ForceDeleteBulkAction::make(),
                Tables\Actions\ExportBulkAction::make()->exporter(UserExporter::class),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Components\Section::make()
                    ->columns(3)
                    ->schema([

                        TextEntry::make('roles.name')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'RSM' => 'danger',
                                'ASM' => 'warning',
                                'DSA' => 'info',
                                'ZSM' => 'success',
                                'NSM' => 'primary',
                                default => 'primary'
                            })
                            ->label('Desgn.'),
                        TextEntry::make('email')->copyable(),
                        TextEntry::make('phone_number')->copyable(),
                        TextEntry::make('division.name'),
                        TextEntry::make('zone_name')->label('Zone'),
                        TextEntry::make('region_name')->label('Region'),
                        TextEntry::make('area_name')->label('Area'),
                        TextEntry::make('headquarter_name')->label('Headquarter'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
    //     public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'roles',
                'division',
                'location' => function ($morphTo) {
                    $morphTo->morphWith([
                        \App\Models\Zone::class => [],
                        \App\Models\Region::class => [],
                        \App\Models\Area::class => ['region'],
                        \App\Models\Headquarter::class => ['area.region'],
                    ]);
                },
            ])
            ->whereDoesntHave('roles', function ($query) {
                $query->whereIn('name', ['super_admin', 'admin']);
            });
    }
}
