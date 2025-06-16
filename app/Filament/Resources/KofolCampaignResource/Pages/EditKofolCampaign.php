<?php

namespace App\Filament\Resources\KofolCampaignResource\Pages;

use App\Filament\Resources\KofolCampaignResource;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditKofolCampaign extends EditRecord
{
    protected static string $resource = KofolCampaignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->before(function (Actions\DeleteAction $action, $record) {
                    if ($record->kofolEntries()->exists()) {
                        Notification::make()
                            ->danger()
                            ->title('Deletion Failed')
                            ->body('Cannot delete this campaign because it has related entries.')
                            ->persistent()
                            ->send();

                        $action->cancel();
                    }
                }),
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
