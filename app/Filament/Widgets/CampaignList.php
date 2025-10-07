<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Campaign;
use Filament\Tables\Table;
use Filament\Facades\Filament;
use Illuminate\Support\Carbon;
use Filament\Tables\Actions\Action;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\Layout\Split;
use Filament\Tables\Columns\Layout\Stack;
use App\Filament\Resources\CampaignResource;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Relations\Relation;

class CampaignList extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->defaultSort('name', 'asc')
            ->query(Campaign::active())
            ->heading('')
            ->emptyStateHeading('No Campaigns active at the moment')
            ->recordUrl(fn($record) => Filament::getResourceUrl(Relation::getMorphedModel($record->allowed_entry_type), 'index'))
            ->columns([
                Split::make([
                    Stack::make([
                        TextColumn::make('name')
                            ->label('Name')
                            ->weight(FontWeight::SemiBold)
                            ->color('primary'),

                        TextColumn::make('end_date')
                            ->label('End Date')
                            ->prefix('Ends in: ')
                            ->formatStateUsing(fn(?string $state) => Carbon::parse($state)->diffForHumans(null, true)),
                    ]),

                ])

            ])
            ->contentGrid([
                'sm' => 2,
                'md' => 2,
                'xl' => 3,
            ]);


    }
}
