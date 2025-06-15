<?php

namespace App\Filament\Resources\ChemistResource\Pages;

use App\Filament\Resources\ChemistResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateChemist extends CreateRecord
{
    protected static string $resource = ChemistResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['user_id'] = Auth::id();
        if(Auth::user()->hasRole('DSA')){
            $data['headquarter_id'] = Auth::user()->location_id;
        }
        return $data;
    }
}
