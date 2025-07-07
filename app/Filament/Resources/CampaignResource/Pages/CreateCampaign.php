<?php

namespace App\Filament\Resources\CampaignResource\Pages;

use App\Filament\Resources\CampaignResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateCampaign extends CreateRecord
{
    protected static string $resource = CampaignResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $today = now()->startOfDay();
        $startDate = \Carbon\Carbon::parse($data['start_date'])->startOfDay();
        $endDate = \Carbon\Carbon::parse($data['end_date'])->endOfDay();

        $data['is_active'] = $today->between($startDate, $endDate);

        return $data;
    }
    protected function afterCreate(): void
    {
        $record = $this->record;
        $selectedRoles = $this->data['roles'] ?? [];
    
        // Get head office role IDs (should return array of IDs)
        $headOfficeRoleIds = User::headOfficeRoleIds();
    
        // Merge and sync
        $allRoles = array_unique(array_merge($selectedRoles, $headOfficeRoleIds));
        $record->roles()->sync($allRoles);
    }
    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
