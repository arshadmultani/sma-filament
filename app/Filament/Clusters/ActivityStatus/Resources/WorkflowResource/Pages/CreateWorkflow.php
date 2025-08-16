<?php

namespace App\Filament\Clusters\ActivityStatus\Resources\WorkflowResource\Pages;

use App\Filament\Clusters\ActivityStatus\Resources\WorkflowResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateWorkflow extends CreateRecord
{
    protected static string $resource = WorkflowResource::class;
}
