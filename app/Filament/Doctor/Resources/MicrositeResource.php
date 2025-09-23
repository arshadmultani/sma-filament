<?php

namespace App\Filament\Doctor\Resources;

use App\Filament\Actions\SiteUrlAction;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Microsite;
use Filament\Tables\Table;
use App\Models\Scopes\TeamScope;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Log;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ImageColumn;
use App\Models\Scopes\TeamHierarchyScope;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Doctor\Resources\MicrositeResource\Pages;
use App\Filament\Doctor\Resources\MicrositeResource\RelationManagers;

class MicrositeResource extends Resource
{
    protected static ?string $model = Microsite::class;

    protected static ?string $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?string $navigationLabel = 'My Website';

    protected static ?string $modelLabel = 'Website';

    protected static ?string $pluralModelLabel = 'Website';






    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->paginated(false)
            ->columns([
                TextColumn::make('doctor.name')
                    ->default('N/A')
                    ->weight(FontWeight::Bold)
                    ->label('Name')
                    ->getStateUsing(fn($record) => $record->doctor?->name),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean(),
            ])

            ->emptyStateHeading('You do not have any website yet.')
            ->emptyStateIcon('heroicon-o-globe-alt')
            ->emptyStateActions([
                CreateAction::make(),
            ])
            ->filters([
                //
            ])
            ->actions([
                SiteUrlAction::makeTable(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $doctor_id = auth()->user()->userable_id;
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([TeamHierarchyScope::class, TeamScope::class])
            ->where('doctor_id', $doctor_id)
            ->with(['doctor' => fn($query) => $query->withoutGlobalScopes()]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMicrosites::route('/'),
            'create' => Pages\CreateMicrosite::route('/create'),
            'edit' => Pages\EditMicrosite::route('/{record}/edit'),
            'view' => Pages\ViewMicrosite::route('/{record}'),
        ];
    }
}
