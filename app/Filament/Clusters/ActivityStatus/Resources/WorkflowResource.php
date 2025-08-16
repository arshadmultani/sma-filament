<?php

namespace App\Filament\Clusters\ActivityStatus\Resources;

use App\Filament\Clusters\ActivityStatus;
use App\Filament\Clusters\ActivityStatus\Resources\WorkflowResource\Pages;
use App\Filament\Clusters\ActivityStatus\Resources\WorkflowResource\RelationManagers;
use App\Models\Workflow;
use App\Models\Status;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use App\Contracts\IsCampaignEntry;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Relations\Relation;

class WorkflowResource extends Resource
{
    protected static ?string $model = Workflow::class;


    protected static ?string $cluster = ActivityStatus::class;

    public static function form(Form $form): Form
    {
        return $form
            ->columns(3)
            ->schema([
                TextInput::make('name')
                    ->label('Workflow Name')
                    ->required(),
                Select::make('model_type')
                    ->options(function () {
                        $entryableModels = [];
                        foreach (Relation::morphMap() as $alias => $class) {
                            if (in_array(IsCampaignEntry::class, class_implements($class))) {
                                $entryableModels[$alias] = class_basename($class);
                            }
                        }
                        return $entryableModels;
                    })
                    ->native(false)
                    ->required(),
                Toggle::make('is_active')
                    ->label(function ($state) {
                        return $state ? 'Active' : 'Inactive';
                    })
                    ->live()
                    ->onIcon('heroicon-s-check')
                    ->offIcon('heroicon-s-x-mark')
                    ->inline(false)
                    ->onColor('success')
                    ->offColor('danger')
                    ->default(true),
                Repeater::make('transitions')
                    ->columnSpanFull()
                    ->columns(4)
                    ->addActionLabel('Add Transition')
                    ->relationship()
                    ->schema([
                        Select::make('from_status_id')
                            ->label('From')
                            ->preload()
                            ->native(false)
                            ->required()
                            ->options(Status::pluck('name', 'id')),
                        Select::make('to_status_id')
                            ->label('To')
                            ->preload()
                            ->native(false)
                            ->required()
                            ->options(Status::pluck('name', 'id')),
                        TextInput::make('action')
                            ->label('Action')
                            ->required(),
                        Select::make('roles')
                            ->relationship('roles', 'name')
                            ->multiple()
                            ->preload()
                            ->required(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Workflow Name'),
                TextColumn::make('model_type')
                    ->label('Model Type'),
                TextColumn::make('is_active')
                    ->label('Status'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListWorkflows::route('/'),
            'create' => Pages\CreateWorkflow::route('/create'),
            'edit' => Pages\EditWorkflow::route('/{record}/edit'),
        ];
    }
}
