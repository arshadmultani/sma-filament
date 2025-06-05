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
use Filament\Infolists\Components\Fieldset;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\Section;
use Filament\Tables\Columns\IconColumn;
use App\Filament\Actions\UpdateStatusAction;
use Filament\Tables\Filters\SelectFilter;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class DoctorResource extends Resource implements HasShieldPermissions
{

    public static function getPermissionPrefixes(): array{
        return [
            'view',
            'view_any',
            'create',
            'update',
            'delete',
            'delete_any',
            'update_status'
        ];
    }
    protected static ?string $model = Doctor::class;

    protected static ?string $navigationGroup = 'Customer';

    // protected static ?string $navigationIcon = 'heroicon-o-user';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')->required()->prefix('Dr. '),
                Select::make('type')
                    ->native(false)
                    ->options(['Ayurvedic' => 'Ayurvedic', 'Allopathic' => 'Allopathic'])
                    ->required(),
                Select::make('qualification_id')
                    ->native(false)
                    ->label('Qualification')
                    ->options(Qualification::where('category', 'Doctor')->pluck('name', 'id'))
                    ->required(),
                Select::make('support_type')
                    ->native(false)
                    ->options(['Prescribing' => 'Prescribing', 'Dispensing' => 'Dispensing'])
                    ->required(),
                TextInput::make('email')->email()->required(),
                TextInput::make('phone')->required(),
                TextInput::make('address'),
                TextInput::make('town'),
                

                Select::make('headquarter_id')
                    ->native(false)
                    ->relationship('headquarter', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false)
                    ->required(),
                FileUpload::make('attachment')
                    ->directory('doctors/attachments')
                    ->placeholder('Upload Both or Any One')
                    ->image()
                    ->multiple()
                    ->maxFiles(2)
                    ->panelLayout('grid')
                    ->maxSize(1024)
                    ->label('Visiting Card/Rx. Pad'),
                FileUpload::make('profile_photo')
                    ->image()->directory('doctors/profile_photos'),


            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo')
                    ->circular()
                    ->toggleable()
                    ->label('Photo'),

                TextColumn::make('name')->weight(FontWeight::Bold)->label('Dr.')->searchable(),

                IconColumn::make('status')
                    ->icon(fn (string $state): string => match ($state) {
                        'Pending' => 'heroicon-o-clock',
                        'Approved' => 'heroicon-o-check-circle',
                        'Rejected' => 'heroicon-o-x-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'Pending' => 'warning',
                        'Approved' => 'success',
                        'Rejected' => 'danger',
                        default => 'secondary',
                    }),
                TextColumn::make('town')->toggleable(),
                TextColumn::make('headquarter.name')
                    ->toggleable()
                    ->label('HQ')
                    ->searchable(),

                TextColumn::make('headquarter.area.name')
                    ->toggleable()
                    ->label('Area')
                    ->searchable(),
                TextColumn::make('headquarter.area.region.name')
                    ->toggleable()
                    ->label('Region')
                    ->searchable(),


                TextColumn::make('type')->toggleable(),
                TextColumn::make('support_type')->toggleable()->label('Support'),
                TextColumn::make('email')->toggleable(),
                TextColumn::make('qualification.name')->toggleable(),
                TextColumn::make('phone'),
                TextColumn::make('user.name')->label('Created By'),
                TextColumn::make('created_at')->since()->toggleable()->sortable(),
                TextColumn::make('updated_at')->since()->toggleable()->sortable(),

            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'Pending' => 'Pending',
                        'Approved' => 'Approved',
                        'Rejected' => 'Rejected',
                    ]),
            ])
            ->actions([

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    UpdateStatusAction::makeBulk(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist

            ->schema([

                Section::make()
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('profile_photo')
                            ->simpleLightbox()

                            ->visible(fn($state) => !is_null($state))
                            ->label('Photo')->circular(),

                        Section::make()
                            ->columns(2)
                            ->columnSpan(2)
                            ->schema([
                                TextEntry::make('type'),
                                TextEntry::make('support_type'),
                                TextEntry::make('qualification.name'),
                                TextEntry::make('town'),
                            ]),

                    ]),

                Section::make('')
                    ->columns(3)
                    ->schema([

                        TextEntry::make('address'),
                        TextEntry::make('email'),
                        TextEntry::make('phone'),
                        TextEntry::make('headquarter.name'),
                        TextEntry::make('headquarter.area.name'),
                        TextEntry::make('headquarter.area.region.name'),



                        // TextEntry::make('created_at')->since()->label('Created'),
                        // TextEntry::make('user.name'),
                    ]),
                Section::make()
                    ->columns(3)
                    ->schema([
                        ImageEntry::make('attachment')
                            ->simpleLightbox()
                            ->label('Visiting Card/Rx. Pad'),
                        
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
            'view' => Pages\ViewDoctor::route('/{record}'),
        ];
    }
    // public static function getNavigationBadge(): ?string
    // {
    //     return static::getModel()::count();
    // }
}
