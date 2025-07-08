<?php

namespace App\Filament\Clusters\Attributes\Resources\CallInputResource\Pages;

use App\Filament\Clusters\Attributes\Resources\CallInputResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateCallInput extends CreateRecord
{
    protected static string $resource = CallInputResource::class;
}
