<?php

namespace App\Filament\Resources\KofolCampaignResource\Pages;

use App\Filament\Resources\KofolCampaignResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditKofolCampaign extends EditRecord
{
    protected static string $resource = KofolCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    protected function mutateFormDataBeforeSave(array $data): array
{
    $today = now()->startOfDay();
    $startDate = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
    $endDate = \Carbon\Carbon::parse($data['end_date'])->endOfDay();

    $data['is_active'] = $today->between($startDate, $endDate);

    return $data;
}
}
