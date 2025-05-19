<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use App\Mail\GenericMail;
use Filament\Tables\Actions\Action;
use Filament\Notifications\Notification;
use App\Filament\Actions\SendMailAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected ?string $plainPassword = null; //Temporary password

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\Select::make('roles')
                            ->relationship('roles', 'name')
                            ->preload()
                            ->required(),
                    ]),
                Section::make()
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')
                            ->required()
                            ->tel(),
                        Forms\Components\Select::make('division_id')
                            ->relationship('division', 'name')
                            ->required(),
                        Forms\Components\TextInput::make('password')
                            ->hidden(fn(string $context): bool => $context === 'edit')
                            ->password()
                            ->revealable()
                            ->default(fn() => Str::random(8))
                            ->placeholder(fn($context) => $context === 'edit' ? 'Enter a new password to change' : null)
                            ->maxLength(255)
                            ->dehydrateStateUsing(function ($state, $livewire) {
                                // Store plain password in temporary variable
                                $livewire->plainPassword = $state;
                                // Return hashed password to be stored in DB
                                return Hash::make($state);
                            })
                            ->dehydrated(fn($state) => filled($state))
                            ->required(fn(string $context): bool => $context === 'create'),
                    ])


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('roles.name')->badge()->label('Desgn.'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('phone_number'),
                Tables\Columns\TextColumn::make('division.name'),
                
            ])
            ->filters([])
            ->actions([
                Tables\Actions\EditAction::make(),
                SendMailAction::make(),
            ])
            ->bulkActions([
                SendMailAction::makeBulk(),
                Tables\Actions\DeleteBulkAction::make(),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
