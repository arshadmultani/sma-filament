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
use App\Filament\Actions\SendKofolCouponAction;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;

class ViewKofolEntry extends ViewRecord
{
    protected static string $resource = KofolEntryResource::class;

    public function getTitle(): string
    {
        return 'KSV/POB/' . $this->record->id;
    }

    public function getHeaderActions(): array
    {
        $actions = [];
        if (Gate::allows('updateStatus', $this->record)) {
            $actions[] = UpdateKofolStatusAction::make();

        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if ($user && $user->hasRole(['admin', 'super_admin'])) {
            $actions[] = SendKofolCouponAction::make()->visible(fn($record) => $record->status == 'Approved');
        }
        if (Gate::allows('update', $this->record) && Auth::id() === $this->record->user_id) {
            $actions[] = Action::make('edit')
                ->label('Edit')
                ->url(route('filament.admin.resources.kofol-entries.edit', $this->record))
                ->color('gray');
        }
        // $actions[] = Html2MediaAction::make('print')
        //     ->content(fn($record) => view('filament.kofol-entry-invoice', ['kofolEntry' => $record]))
        //     ->print()
        //     ->margin([10, 10, 10, 10])
        //     ->icon('heroicon-o-printer')
        //     ->label('')
        //     ->filename('KSV-POB-' . $this->record->id)
        //     ->color('gray');


        return [
            ...$actions,
        ];
    }
}
