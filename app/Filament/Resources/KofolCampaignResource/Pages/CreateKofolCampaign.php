<?php

namespace App\Filament\Resources\KofolCampaignResource\Pages;

use App\Filament\Resources\KofolCampaignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateKofolCampaign extends CreateRecord
{
    protected static string $resource = KofolCampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $today = now()->startOfDay();
        $startDate = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($data['end_date'])->endOfDay();

        $data['is_active'] = $today->between($startDate, $endDate);

        return $data;
    }
}
