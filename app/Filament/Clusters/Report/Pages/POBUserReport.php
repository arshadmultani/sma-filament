<?php

namespace App\Filament\Clusters\Report\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Headquarter;
use App\Filament\Clusters\Report;
use function Laravel\Prompts\table;
use Illuminate\Support\Facades\Auth;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Builder;

use Filament\Tables\Concerns\InteractsWithTable;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use App\Filament\Resources\POBResource\Widgets\POBStats;
use App\Filament\Resources\POBResource\Widgets\POBTable;

class POBUserReport extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.clusters.report.pages.p-o-b-user-report';

    protected static ?string $cluster = Report::class;

    protected static ?string $navigationLabel = 'POB User Report';

    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->query(Headquarter::query())
            ->columns([
                TextColumn::make('name'),
            ]);
    }
}
