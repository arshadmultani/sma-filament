<?php

namespace App\Filament\Resources\PanelAccessRequestResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PanelAccessRequestResource;
use App\Models\State;

class ListPanelAccessRequests extends ListRecords
{
    protected static string $resource = PanelAccessRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make(),
            'Pending' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    return $query->whereHas('state', function ($q) {
                        $q->pending();
                    });
                }),
            'Approved' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    return $query->whereHas('state', function ($q) {
                        $q->finalized();
                    });
                }),
            'Rejected' => Tab::make()
                ->modifyQueryUsing(function ($query) {
                    return $query->whereHas('state', function ($q) {
                        $q->cancelled();
                    });
                })
        ];
    }
}
