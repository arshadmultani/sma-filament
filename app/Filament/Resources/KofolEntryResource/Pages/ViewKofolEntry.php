<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Resources\KofolEntryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Actions\Action;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use Illuminate\Support\Str;
use App\Models\KofolEntry;
use App\Filament\Actions\UpdateKofolStatusAction;

class ViewKofolEntry extends ViewRecord
{
    protected static string $resource = KofolEntryResource::class;

    public function getTitle(): string
    {
        return 'KSV/POB/' . $this->record->id;
    }

    public function getHeaderActions(): array
    {
        return [

            UpdateKofolStatusAction::make(),
            Html2MediaAction::make('print')
                ->content(fn($record)=>view('filament.kofol-entry-invoice', ['kofolEntry' => $record]))
                ->print()
                ->margin([10, 10, 10, 10])
                ->icon('heroicon-o-printer')
                ->label('')
                ->filename('KSV-POB-'.$this->record->id)
                ->color('gray'),
                
                
                Action::make('edit')
                ->label('Edit')
                ->url(route('filament.admin.resources.kofol-entries.edit', $this->record))
                ->color('gray'),
        ];
    }
}
