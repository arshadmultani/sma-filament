<?php

namespace App\Filament\Resources;

use App\Contracts\IsCampaignEntry;
use App\Filament\Resources\CampaignResource\Pages;
use App\Filament\Resources\CampaignResource\RelationManagers;
use App\Models\Campaign;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Traits\HandlesDeleteExceptions;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\TextEntry;
use App\Models\Tag;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Textarea;

class CampaignResource extends Resource
{
    use HandlesDeleteExceptions;

    protected static ?string $model = Campaign::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-date-range';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make()
                    ->compact()
                    ->columns(2)
                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255),
                        Textarea::make('description')
                            ->required()
                            ->maxLength(255),
                        Select::make('allowed_entry_type')
                            ->label('Activity Type')
                            ->options(function () {
                                $entryableModels = [];
                                foreach (Relation::morphMap() as $alias => $class) {
                                    if (in_array(IsCampaignEntry::class, class_implements($class))) {
                                        $entryableModels[$alias] = class_basename($class);
                                    }
                                }
                                return $entryableModels;
                            })
                            ->native(false)
                            ->searchable()
                            ->required(),
                        Select::make('divisions')
                            ->label('Divisions')
                            ->multiple()
                            ->relationship('divisions', 'name')
                            ->preload()
                            ->required(),
                        Select::make('roles')
                            ->label('Participants')
                            ->multiple()
                            ->relationship('roles', 'name', function ($query) {
                                $query->whereNotIn('roles.id', User::headOfficeRoleIds());
                            })
                            ->preload()
                            ->required(),
                        Select::make('tags')
                            ->label('Tags')
                            ->multiple()
                            ->relationship('tags', 'name')
                            ->preload()
                            ->helperText('Leave blank to allow untagged yet approved customers')

                    ]),
                Forms\Components\Section::make()
                    ->compact()
                    ->columns(2)
                    ->schema([
                        Forms\Components\DatePicker::make('start_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y'),
                        Forms\Components\DatePicker::make('end_date')
                            ->required()
                            ->native(false)
                            ->displayFormat('d F Y')
                            ->minDate(
                                fn(Forms\Get $get) => $get('start_date') ? \Carbon\Carbon::parse($get('start_date'))->addDay() : null
                            ),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('divisions.name')
                    ->visible(fn(): bool => Auth::user()->can('view_user'))
                    ->label('Divisions'),
                TextColumn::make('participants')
                    ->badge()
                    ->color('gray')
                    ->visible(fn(): bool => Auth::user()->can('view_user'))
                    ->label('Participants'),
                TextColumn::make('start_date')
                    ->date('d F Y'),
                TextColumn::make('end_date')
                    ->date('d F Y'),
                TextColumn::make('allowed_entry_type')
                    ->label('Activity')
                    ->visible(fn(): bool => Auth::user()->can('view_user'))
                    ->formatStateUsing(fn(string $state): string => ucfirst(str_replace('_', ' ', $state))),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Active' => 'primary',
                        'Upcoming' => 'warning',
                        'Completed' => 'info',
                        default => 'gray'
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'Active' => 'heroicon-o-arrow-path',
                        'Upcoming' => 'heroicon-o-clock',
                        'Completed' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-x-circle'
                    }),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->before(fn($action, $records) => collect($records)->each(fn($record) => (new static())->tryDeleteRecord($record, $action))),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('description'),
                        TextEntry::make('tags.name')->label('Tags')->badge()->color('primary'),
                        TextEntry::make('allowed_entry_type'),
                    ]),
                Section::make()
                    ->columns(3)
                    ->schema([
                        TextEntry::make('start_date')->formatStateUsing(fn($state) => $state->format('d F Y')),
                        TextEntry::make('end_date')->formatStateUsing(fn($state) => $state->format('d F Y')),
                        TextEntry::make('status')
                            ->badge()
                            ->color(fn($state) => match ($state) {
                                'Active' => 'primary',
                                'Upcoming' => 'warning',
                                'Completed' => 'info',
                                default => 'gray'
                            }),
                    ]),
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('divisions.name')->label('Divisions'),
                        TextEntry::make('participants')->label('Participants')->badge()->color('gray'),
                    ]),
                Section::make()
                    ->columns(2)
                    ->schema([
                        TextEntry::make('created_at')->since()->label('Created'),
                        TextEntry::make('updated_at')->since()->label('Updated'),
                    ]),

            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with('roles');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCampaigns::route('/'),
            'create' => Pages\CreateCampaign::route('/create'),
            'edit' => Pages\EditCampaign::route('/{record}/edit'),
            'view' => Pages\ViewCampaign::route('/{record}'),
        ];
    }
}
