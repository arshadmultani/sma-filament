<?php

namespace App\Filament\Resources;

use App\Filament\Resources\DoctorResource\Pages;
use App\Filament\Resources\DoctorResource\RelationManagers;
use App\Models\Doctor;
use Filament\Forms;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Qualification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
class DoctorResource extends Resource
{
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationGroup = 'Customer';

    // protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->prefix('Dr. '),
                Select::make('qualification_id')
                    ->native(false)
                    ->label('Qualification')
                    ->options(Qualification::where('category', 'Doctor')->pluck('name', 'id'))
                    ->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('phone')->required(),
                TextInput::make('address')->required(),
                Select::make('headquarter_id')
                    ->native(false)
                    ->relationship('headquarter', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                FileUpload::make('profile_photo')
                    ->image()->directory('doctors/profile_photos'),
                FileUpload::make('attachment')
                    ->directory('doctors/attachments')
                    ->placeholder('Upload Both or Any One')
                    ->image()
                    ->multiple()
                    ->maxFiles(2)
                    ->panelLayout('grid')
                    ->maxSize(1024)
                    ->label('Visiting Card/Rx. Pad'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo')
                    ->circular()
                    ->toggleable()
                    ->label('Photo')
                    ->defaultImageUrl('https://www.charak.com/wp-content/uploads/2021/03/charak-logo.svg'),

                TextColumn::make('name')->weight(FontWeight::Bold),
                TextColumn::make('headquarter.area.name')
                    ->toggleable()
                    ->label('Location')
                    ->searchable(),
                TextColumn::make('email')->toggleable(),
                TextColumn::make('qualification.name')->toggleable(),
                TextColumn::make('phone'),
                TextColumn::make('user.name')->label('Created By'),
                TextColumn::make('created_at')->since()->toggleable()->sortable(),
                TextColumn::make('updated_at')->since()->toggleable()->sortable(),
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
            'index' => Pages\ListDoctors::route('/'),
            'create' => Pages\CreateDoctor::route('/create'),
            'edit' => Pages\EditDoctor::route('/{record}/edit'),
        ];
    }
    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
