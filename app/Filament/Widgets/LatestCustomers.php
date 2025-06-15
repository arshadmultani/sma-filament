<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Doctor;
use App\Models\Chemist;

class LatestCustomers extends BaseWidget
{
    
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Doctor::query()
                ->latest()
                ->limit(1)
            )
            ->heading('Recent Doctors')
            ->columns([
                Tables\Columns\TextColumn::make('name'),                
            ]);
    }
}
