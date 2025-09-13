<?php

namespace App\Filament\Pages;

use App\Models\Doctor;
use App\Models\User;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Database\Eloquent\Relations\Relation;

class ListDoctorUsers extends Page implements HasTable
{
    use InteractsWithTable;
    protected static string $view = 'filament.pages.list-doctor-users';

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
            ->query(User::query()->where('userable_type', Relation::getMorphAlias(Doctor::class)))
            ->paginated([10, 25, 50, 100])
            ->columns([
                TextColumn::make('name')->label('Name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->label('Email')
                    ->toggleable()
                    ->searchable(),
                TextColumn::make('created_at')
                    ->label('Created At')
                    ->toggleable()
                    ->dateTime('d-M-Y H:i')
                    ->sortable(),
                TextColumn::make('division.name')
                    ->label('Division')
                    ->searchable()
                    ->toggleable()
                    ->sortable(),
                TextColumn::make('location.name')
                    ->label('Headquarter')
                    ->searchable()
                    ->toggleable(),
            ]);
    }
}
