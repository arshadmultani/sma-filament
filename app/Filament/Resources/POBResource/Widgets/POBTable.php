<?php

namespace App\Filament\Resources\POBResource\Widgets;

use App\Models\POB;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Url;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class POBTable extends BaseWidget
{
    protected int | string | array $columnSpan = 'full';
    protected static ?string $heading = 'POB Summary';

    #[Url]
    public ?string $groupBy = 'headquarter';

    public function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->extremePaginationLinks()
            // ->paginated([10, 25, 50])
            ->query(
                $this->getTableQuery()
            )
            ->columns(
                $this->getTableColumns()
            )
            ->headerActions([
                ExportAction::make('export')
                    ->color('primary')
                    ->outlined()
                    ->exports([
                        ExcelExport::make()
                            ->queue()
                            ->withChunkSize(100)
                            ->fromTable()
                    ]),
                Action::make('groupBy')
                    ->label('Group:' . ucfirst($this->groupBy))
                    ->icon('heroicon-s-chevron-down')
                    ->iconPosition('after')
                    ->outlined()
                    ->form([
                        \Filament\Forms\Components\Select::make('groupBy')
                            ->label('Group By')
                            ->options([
                                'headquarter' => 'Headquarter',
                                'area' => 'Area',
                                'region' => 'Region',
                            ])
                            ->native(false)
                            ->default($this->groupBy)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $this->groupBy = $data['groupBy'];
                    })
                    ->modalHeading('Change Grouping')
                    ->modalSubmitActionLabel('Apply'),
            ])
            ->defaultSort('group_name');
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->group_name;
    }

    protected function getTableQuery(): Builder
    {
        switch ($this->groupBy) {
            case 'area':
                // Get all areas with their POB count and total value
                $subQuery = DB::table('areas')
                    ->leftJoin('divisions', 'areas.division_id', '=', 'divisions.id')
                    ->leftJoin('headquarters', 'areas.id', '=', 'headquarters.area_id')
                    ->leftJoin('p_o_b_s', 'headquarters.id', '=', 'p_o_b_s.headquarter_id')
                    ->select(
                        'areas.name as group_name',
                        'divisions.name as division_name',
                        DB::raw('count(p_o_b_s.id) as pob_count'),
                        DB::raw('coalesce(sum(p_o_b_s.invoice_amount), 0) as pob_total_value')
                    )
                    ->groupBy('areas.id', 'areas.name', 'divisions.id', 'divisions.name')
                    ->orderBy('areas.name');

                return POB::query()->fromSub($subQuery, 'area_counts');

            case 'region':
                // Get all regions with their POB count and total value
                $subQuery = DB::table('regions')
                    ->leftJoin('divisions', 'regions.division_id', '=', 'divisions.id')
                    ->leftJoin('areas', 'regions.id', '=', 'areas.region_id')
                    ->leftJoin('headquarters', 'areas.id', '=', 'headquarters.area_id')
                    ->leftJoin('p_o_b_s', 'headquarters.id', '=', 'p_o_b_s.headquarter_id')
                    ->select(
                        'regions.name as group_name',
                        'divisions.name as division_name',
                        DB::raw('count(p_o_b_s.id) as pob_count'),
                        DB::raw('coalesce(sum(p_o_b_s.invoice_amount), 0) as pob_total_value')
                    )
                    ->groupBy('regions.id', 'regions.name', 'divisions.id', 'divisions.name')
                    ->orderBy('regions.name');

                return POB::query()->fromSub($subQuery, 'region_counts');

            case 'headquarter':
            default:
                // Get all headquarters with their POB count and total value
                $subQuery = DB::table('headquarters')
                    ->leftJoin('divisions', 'headquarters.division_id', '=', 'divisions.id')
                    ->leftJoin('p_o_b_s', 'headquarters.id', '=', 'p_o_b_s.headquarter_id')
                    ->select(
                        'headquarters.name as group_name',
                        'divisions.name as division_name',
                        DB::raw('count(p_o_b_s.id) as pob_count'),
                        DB::raw('coalesce(sum(p_o_b_s.invoice_amount), 0) as pob_total_value')
                    )
                    ->groupBy('headquarters.id', 'headquarters.name', 'divisions.id', 'divisions.name')
                    ->orderBy('headquarters.name');

                return POB::query()->fromSub($subQuery, 'headquarters_counts');
        }
    }

    protected function getTableColumns(): array
    {
        $label = ucfirst($this->groupBy ?? 'headquarter');

        return [
            TextColumn::make('group_name')->label($label)
                ->searchable(),
            TextColumn::make('division_name')->label('Division')
                ->sortable(),
            TextColumn::make('pob_count')->label('POB Count')
                ->sortable(),
            TextColumn::make('pob_total_value')
                ->label('POB Total Value')
                ->toggleable()
                ->money('INR', locale: 'en_IN')
                ->sortable(),
        ];
    }
}
