<?php

namespace App\Filament\Resources\POBResource\Widgets;

use App\Models\POB;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class POBTable extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->paginated([10, 25, 50, 100])
            ->query(
                POB::query()
            )
            ->columns([
                TextColumn::make('headquarter.name')->label('Headquarter'),

            ]);
    }
}
