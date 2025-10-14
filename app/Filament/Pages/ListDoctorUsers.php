<?php

namespace App\Filament\Pages;

use App\Models\User;
use App\Models\Doctor;
use Filament\Pages\Page;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Table;
use Filament\Resources\Components\Tab;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Resources\Concerns\HasTabs;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\RestoreAction;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Illuminate\Database\Eloquent\Relations\Relation;
use STS\FilamentImpersonate\Tables\Actions\Impersonate;


class ListDoctorUsers extends Page implements HasTable
{
    use InteractsWithTable;


    protected static string $view = 'filament.pages.list-doctor-users';

    protected static ?string $slug = 'users-doctor';

    protected static ?string $navigationGroup = 'Users';

    protected static ?string $navigationLabel = 'Dr. Users';

    protected static ?string $title = 'Dr. Users';

    protected static ?int $navigationSort = 3;



    public static function shouldRegisterNavigation(): bool
    {
        return true;
    }

    public static function canAccess(): bool
    {
        return auth()->user()->can('view_user');

    }

    public function table(Table $table): Table
    {
        return $table
            ->query(User::withTrashed()->where('userable_type', Relation::getMorphAlias(Doctor::class)))
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('name')->label('Name')
                    ->searchable()
                    ->weight(FontWeight::Bold)
                    ->extraAttributes(fn($record) => $record->trashed() ? ['style' => 'text-decoration: line-through;'] : [])
                    ->tooltip(fn($record) => $record->trashed() ? 'This user is archived. You can restore' : 'This user is active')
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('division.name')
                    ->label('Division')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Headquarter')
                    ->toggleable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('is_active')
                    ->label('Status')
                    ->badge()
                    ->toggleable()
                    ->formatStateUsing(fn($record) => $record->is_active ? 'Active' : 'Inactive')
                    ->color(fn($state) => $state ? 'success' : 'danger')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->toggleable()
                    ->since()
                    ->sortable(),
            ])
            ->actions([
                Impersonate::make()
                    ->visible(fn() => auth()->user()->can('create_user'))
                    ->requiresConfirmation(),
                ActionGroup::make([
                    DeleteAction::make()
                        ->label('Delete User')
                        ->before(function ($record) {
                            $record->is_active = false;
                            $record->save();
                        }),
                    RestoreAction::make()
                        ->label('Restore')
                        ->visible(fn($record) => $record->trashed()),
                ])
            ])
            ->bulkActions([
                ForceDeleteBulkAction::make()
                    ->label('Permanently Delete')
                    ->requiresConfirmation(),
            ]);
    }

    //TODO: Fix Tabs live switching issue

    // public function getTabs(): array
    // {

    //     return [
    //         'all' => Tab::make('All')
    //             ->badge(User::where('userable_type', Relation::getMorphAlias(Doctor::class))->count())
    //             ->modifyQueryUsing(fn($query) => $query->where('userable_type', Relation::getMorphAlias(Doctor::class))),
    //         'archived' => Tab::make('Archived')
    //             ->modifyQueryUsing(
    //                 function ($query) {
    //                     return $query->onlyTrashed()->where('userable_type', Relation::getMorphAlias(Doctor::class));
    //                 }
    //             )
    //             ->badge(User::onlyTrashed()->where('userable_type', Relation::getMorphAlias(Doctor::class))->count()),


    //     ];
    // }
}
