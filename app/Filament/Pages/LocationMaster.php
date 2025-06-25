<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Division;
use App\Models\Zone;
use App\Models\Region;
use App\Models\Area;
use App\Models\Headquarter;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Group;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\CheckboxList;
use Filament\Notifications\Notification;
use Filament\Forms\Get;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;


class LocationMaster extends Page implements HasTable, HasForms
{
    use InteractsWithForms, InteractsWithTable, HasPageShield;

    protected static ?string $navigationGroup = 'Territory';
    protected static ?string $navigationLabel = 'Location Master';
    protected static string $view = 'filament.pages.location-master';
    protected static ?int $navigationSort = 0;

    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            CheckboxList::make('division_ids')
                ->label('Division(s)')
                ->columns(2)
                ->helperText('Select the divisions first to create locations')
                ->options(Division::all()->pluck('name', 'id'))
                ->required(),
            // ZONE
            Select::make('zone_id')
                ->label('Zone')
                ->options(fn ($get) =>
                    $get('division_ids') && count($get('division_ids')) > 0
                        ? Zone::whereIn('division_id', $get('division_ids'))->pluck('name', 'id')
                        : []
                )
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')->label('Zone Name')->required(),
                ])
                ->createOptionUsing(function (array $data, $get) {
                    $divisionId = $get('division_ids')[0] ?? null;
                    if (!$divisionId) {
                        Notification::make()
                            ->title('Please select a division before creating a zone.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    $existing = Zone::where('name', $data['name'])
                        ->where('division_id', $divisionId)
                        ->first();
                    if ($existing) {
                        Notification::make()
                            ->title('A zone with this name already exists in the selected division.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    return Zone::create([
                        'name' => $data['name'],
                        'division_id' => $divisionId,
                    ])->id;
                })
                ->required(),
            // REGION
            Select::make('region_id')
                ->label('Region')
                ->options(fn ($get) =>
                    $get('zone_id')
                        ? Region::where('zone_id', $get('zone_id'))->pluck('name', 'id')
                        : []
                )
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')->label('Region Name')->required(),
                ])
                ->createOptionUsing(function (array $data, $get) {
                    $zoneId = $get('zone_id');
                    $divisionId = $get('division_ids')[0] ?? null;
                    if (!$zoneId) {
                        Notification::make()
                            ->title('Please select a zone before creating a region.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    if (!$divisionId) {
                        Notification::make()
                            ->title('Please select a division before creating a region.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    $existing = Region::where('name', $data['name'])
                        ->where('zone_id', $zoneId)
                        ->where('division_id', $divisionId)
                        ->first();
                    if ($existing) {
                        Notification::make()
                            ->title('A region with this name already exists in the selected zone and division.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    return Region::create([
                        'name' => $data['name'],
                        'zone_id' => $zoneId,
                        'division_id' => $divisionId,
                    ])->id;
                })
                ->required(),
            // AREA
            Select::make('area_id')
                ->label('Area')
                ->options(fn ($get) =>
                    $get('region_id')
                        ? Area::where('region_id', $get('region_id'))->pluck('name', 'id')
                        : []
                )
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')->label('Area Name')->required(),
                ])
                ->createOptionUsing(function (array $data, $get) {
                    $regionId = $get('region_id');
                    $divisionId = $get('division_ids')[0] ?? null;
                    if (!$regionId) {
                        Notification::make()
                            ->title('Please select a region before creating an area.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    if (!$divisionId) {
                        Notification::make()
                            ->title('Please select a division before creating an area.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    $existing = Area::where('name', $data['name'])
                        ->where('region_id', $regionId)
                        ->where('division_id', $divisionId)
                        ->first();
                    if ($existing) {
                        Notification::make()
                            ->title('An area with this name already exists in the selected region and division.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    return Area::create([
                        'name' => $data['name'],
                        'region_id' => $regionId,
                        'division_id' => $divisionId,
                    ])->id;
                })
                ->required(),
            // HEADQUARTER
            Select::make('headquarter_id')
                ->label('Headquarter')
                ->options(fn ($get) =>
                    $get('area_id')
                        ? Headquarter::where('area_id', $get('area_id'))->pluck('name', 'id')
                        : []
                )
                ->searchable()
                ->createOptionForm([
                    TextInput::make('name')->label('Headquarter Name')->required(),
                ])
                ->createOptionUsing(function (array $data, $get) {
                    $areaId = $get('area_id');
                    $divisionId = $get('division_ids')[0] ?? null;
                    if (!$areaId) {
                        Notification::make()
                            ->title('Please select an area before creating a headquarter.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    if (!$divisionId) {
                        Notification::make()
                            ->title('Please select a division before creating a headquarter.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    $existing = Headquarter::where('name', $data['name'])
                        ->where('area_id', $areaId)
                        ->where('division_id', $divisionId)
                        ->first();
                    if ($existing) {
                        Notification::make()
                            ->title('A headquarter with this name already exists in the selected area and division.')
                            ->danger()
                            ->send();
                        return null;
                    }
                    return Headquarter::create([
                        'name' => $data['name'],
                        'area_id' => $areaId,
                        'division_id' => $divisionId,
                    ])->id;
                })
                ->required(),
        ])->columns(5)->statePath('data');
    }

    public function create(): void
    {
        // At this point, all entities are already created via the dropdowns.
        // You can perform any additional logic here if needed, using the selected IDs.
        // For now, just show a success notification.

        Notification::make()
            ->title('Location hierarchy selected successfully!')
            ->success()
            ->send();

        $this->form->fill();
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Headquarter::query()->with(['area.region.zone', 'division']))
            ->columns([
                TextColumn::make('division.name')->label('Division')->sortable()->searchable(),
                TextColumn::make('area.region.zone.name')->label('Zone')->sortable()->searchable(),
                TextColumn::make('area.region.name')->label('Region')->sortable()->searchable(),
                TextColumn::make('area.name')->label('Area')->sortable()->searchable(),
                TextColumn::make('name')->label('Headquarter')->sortable()->searchable(),
            ])
            ->defaultPaginationPageOption(10);
    }
}
