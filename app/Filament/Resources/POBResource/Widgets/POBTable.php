<?php

namespace App\Filament\Resources\POBResource\Widgets;

use App\Models\Headquarter;
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
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'POB Summary';


    public function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->extremePaginationLinks()
            ->defaultSort('name', 'asc')
            ->paginated([10, 25, 50])
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
                // Action::make('groupBy')
                //     ->label('Group:' . ucfirst($this->groupBy))
                //     ->icon('heroicon-s-chevron-down')
                //     ->iconPosition('after')
                //     ->outlined()
                //     ->form([
                //         \Filament\Forms\Components\Select::make('groupBy')
                //             ->label('Group By')
                //             ->options([
                //                 'headquarter' => 'Headquarter',
                //                 'area' => 'Area',
                //                 'region' => 'Region',
                //             ])
                //             ->native(false)
                //             ->default($this->groupBy)
                //             ->required(),
                //     ])
                //     ->action(function (array $data) {
                //         $this->groupBy = $data['groupBy'];
                //     })
                //     ->modalHeading('Change Grouping')
                //     ->modalSubmitActionLabel('Apply'),
            ]);
        // ->defaultSort('group_name');
    }

    public function getTableRecordKey(Model $record): string
    {
        return $record->id;
    }

    protected function getTableQuery(): Builder
    {
        return Headquarter::query()->with('division')
            ->withCount('pobs as pob_count')
            ->withSum('pobs as pob_total_value', 'invoice_amount');
    }

    protected function getTableColumns(): array
    {
        $label = ucfirst($this->groupBy ?? 'headquarter');

        return [
            TextColumn::make('name')
                ->label('Headquarter')
                ->searchable(),
            TextColumn::make('division.name')
                ->label('Division')
                ->sortable(),
            TextColumn::make('pob_count')
                ->label('POB Count')
                ->sortable()
                ->default(0),
            TextColumn::make('pob_total_value')
                ->label('POB Total Value')
                ->toggleable()
                ->money('INR', locale: 'en_IN')
                ->default(0)
                ->sortable(),
        ];
    }
}
