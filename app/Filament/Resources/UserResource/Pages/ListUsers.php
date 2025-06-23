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
                ImportAction::make()
                    ->importer(UserImporter::class)
                    ->label('Import Users')
                    // ->icon('heroicon-m-arrow-down-on-square')
                    ->color('primary'),
                Actions\ExportAction::make()
                    ->exporter(UserExporter::class)
                    ->label('Download All Users')
                    // ->icon('heroicon-m-arrow-long-up')
                    ->maxRows(2000)
                    ->color('primary'),

            ])->icon('heroicon-m-bars-3-bottom-right'),
        ];
    }

    public function getTabs(): array
    {
        $tabs = [];
        $user = Auth::user();
        /** @var \App\Models\User $user */
        if ($user->can('view_user')) {
            $tabs['all'] = Tab::make('All')->badge(User::count());
            $tabs['archived'] = Tab::make('Archived')->badge(User::onlyTrashed()->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super_admin');
            })->count())->modifyQueryUsing(function ($query) {
                return $query->onlyTrashed();
            });
        }

        return $tabs;
    }
}
