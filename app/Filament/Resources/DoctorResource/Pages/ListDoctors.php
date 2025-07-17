<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Exports\DoctorExporter;
use App\Filament\Resources\DoctorResource;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListDoctors extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = DoctorResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Actions\ExportAction::make()
            //     ->exporter(DoctorExporter::class)
            //     ->visible(Auth::user()->can('force_delete_any_user'))
            //     ->label('Download All Doctors')
            //     ->color('primary'),
        ];
    }
}
