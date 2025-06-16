<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Exports\UserExporter;
use App\Filament\Imports\UserImporter;
use App\Filament\Resources\UserResource;
use App\Models\User;
use Asmit\ResizedColumn\HasResizableColumn;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Actions\ImportAction;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

class ListUsers extends ListRecords
{
    use HasResizableColumn;

    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            ActionGroup::make([
                Actions\ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->label('Download All Users')
                    // ->icon('heroicon-m-arrow-long-up')
                    ->maxRows(2000)
                    ->color('primary'),
                ImportAction::make()
                    ->importer(UserImporter::class)
                    ->label('Import Users')
                    // ->icon('heroicon-m-arrow-down-on-square')
                    ->color('primary'),

            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        if (Auth::user()->hasRole('super_admin')) {
            $tabs['all'] = Tab::make('All')->badge(User::count());
            $tabs['archived'] = Tab::make('Archived')->badge(User::onlyTrashed()->count())->modifyQueryUsing(function ($query) {
                return $query->onlyTrashed();
            });
        }

        return $tabs;
    }
}
