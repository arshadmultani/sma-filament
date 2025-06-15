<?php

namespace App\Filament\Resources\DoctorResource\Pages;

use App\Filament\Resources\DoctorResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
class CreateDoctor extends CreateRecord
{
    protected static string $resource = DoctorResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array{
        $data['user_id'] = Auth::id();
        if(Auth::user()->hasRole('DSA')){
            $data['headquarter_id'] = Auth::user()->location_id;
        }
        return $data;
    }
}
