<?php

namespace App\Filament\Resources\KofolEntryResource\Pages;

use App\Filament\Actions\SendKofolCouponAction;
use App\Filament\Actions\UpdateKofolStatusAction;
use App\Filament\Resources\KofolEntryResource;
use App\Models\KofolEntry;
use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Torgodly\Html2Media\Actions\Html2MediaAction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

class ViewKofolEntry extends ViewRecord
{
    protected static string $resource = KofolEntryResource::class;

    public function getTitle(): string
    {
        return 'KSV/POB/'.$this->record->id;
    }
    protected function resolveRecord($key): Model
{
    $record = parent::resolveRecord($key)->load(['customer.headquarter', 'coupons']);
    Log::info('Coupons are loaded:',[$record->coupons]);
    return $record;
}

    public function getHeaderActions(): array
    {
        $actions = [];
        if (Gate::allows('updateStatus', $this->record)) {
            $actions[] = UpdateKofolStatusAction::make();

        }

        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        if (Gate::allows('sendCoupon', $this->record)) {
            $actions[] = SendKofolCouponAction::make()->visible(fn ($record) => $record->status == 'Approved');
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
