<?php

namespace App\Filament\Clusters\Report\Pages;

use App\Models\User;
use NumberFormatter;
use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Filament\Clusters\Report;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;
use Filament\Tables\Concerns\InteractsWithTable;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use pxlrbt\FilamentExcel\Actions\Tables\ExportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class POBUserReport extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.report.pages.p-o-b-user-report';

    protected static ?string $cluster = Report::class;

    protected static ?string $navigationLabel = 'POB HQ Report';

    public function getTitle(): string
    {
        return 'POB HQ Report';
    }

    public function table(Table $table): Table
    {
        return $table
            ->deferLoading()
            ->heading('POBs')
            ->paginated([10, 25, 50, 100, 200])
            ->query($this->getTableQuery())
            ->columns([
                TextColumn::make('name'),
                TextColumn::make('roles.name'),
                TextColumn::make('division.name')
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Headquarter'),
                TextColumn::make('location.area.name')
                    ->label('Area'),
                TextColumn::make('location.area.region.name')
                    ->label('Region'),
                TextColumn::make('pobs_count')
                    ->label('POB Count')
                    ->sortable(),
                TextColumn::make('pobs_sum_invoice_amount')
                    ->label('Total POB Value')
                    ->default(0)
                    ->sortable()
                    ->formatStateUsing(function ($state) {
                        $formatter = new NumberFormatter('en_IN', NumberFormatter::DECIMAL);
                        return $formatter->format($state);
                    })
            ])
            ->headerActions([
                ExportAction::make('export')
                    ->color('primary')
                    ->outlined()
                    ->exports([
                        ExcelExport::make()
                            ->queue()
                            ->withChunkSize(100)
                            ->fromTable()
                    ])
            ]);
    }

    protected function getTableQuery(): Builder
    {
        return User::query()
            ->where('location_type', 'App\Models\Headquarter')
            ->withCount(['pobs' => function ($query) {
                $query->approved();
            }])
            ->withSum(['pobs' => function ($query) {
                $query->approved();
            }], 'invoice_amount');
    }
}
