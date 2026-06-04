<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ArCreativeResource\Pages;
use App\Models\ArCreative;
use App\Support\Qr;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\View;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ArCreativeResource extends Resource
{
    protected static ?string $model = ArCreative::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'AR Creatives';

    protected static ?string $modelLabel = 'AR creative';

    protected static ?string $pluralModelLabel = 'AR creatives';

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Creative')
                ->description('Name this AR creative and decide whether doctors can see it yet.')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),

                    Select::make('status')
                        ->options([
                            'draft' => 'Draft (hidden)',
                            'published' => 'Published (live)',
                        ])
                        ->default('draft')
                        ->native(false)
                        ->required(),

                    Select::make('play_mode')
                        ->label('Video playback')
                        ->options([
                            'loop' => 'Loop while on the image',
                            'once' => 'Play once',
                        ])
                        ->default('loop')
                        ->native(false)
                        ->required(),
                ]),

            Section::make('Marker image & video')
                ->description('Upload the printed creative image (what the camera locks onto) and the video that plays on top of it.')
                ->columns(2)
                ->schema([
                    FileUpload::make('marker_image_path')
                        ->label('Marker image')
                        ->helperText('The printed picture the doctor points the camera at. Use a detailed, high-contrast image.')
                        ->image()
                        ->disk(ArCreative::DISK)
                        ->visibility('private')
                        ->directory('ar/markers')
                        ->maxSize(8 * 1024)
                        ->required(),

                    FileUpload::make('video_path')
                        ->label('Video')
                        ->helperText('The .mp4 that plays on the image. Keep it small (compressed) so it loads fast on mobile data.')
                        ->disk(ArCreative::DISK)
                        ->visibility('private')
                        ->directory('ar/videos')
                        ->acceptedFileTypes(['video/mp4'])
                        ->maxSize(50 * 1024)
                        ->previewable(false)
                        ->required(),
                ]),

            Section::make('AR tracking file')
                ->description('Turn the marker image into a tracking file. This runs in your browser — nothing is sent to any outside service.')
                ->visibleOn('edit')
                ->schema([
                    View::make('filament.ar-compiler'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('marker_image_path')
                    ->label('Image')
                    ->disk(ArCreative::DISK)
                    ->visibility('private')
                    ->hidden(fn () => ! auth()->user()?->hasRole('super_admin'))
                    ->height(48),

                Tables\Columns\TextColumn::make('name')
                    // ->searchable()
                    ->sortable(),
                Tables\Columns\IconColumn::make('ready')
                    ->label('Ready')
                    ->tooltip('Has image, video and a compiled tracking file')
                    ->state(fn (ArCreative $record) => $record->isReady())
                    ->boolean(),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->hidden(fn () => ! auth()->user()?->hasRole('super_admin'))
                    ->colors([
                        'gray' => 'draft',
                        'success' => 'published',
                    ]),

                Tables\Columns\TextColumn::make('tracking_score')
                    ->label('Trackability')
                    ->badge()
                    ->hidden(fn () => ! auth()->user()?->hasRole('super_admin'))
                    ->formatStateUsing(fn ($state, ArCreative $record) => $record->trackabilityLabel())
                    ->color(fn ($state, ArCreative $record) => $record->trackabilityColor()),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->actions([
                Tables\Actions\Action::make('openAr')
                    ->label(fn (ArCreative $record) => $record->isPublished() ? 'Open AR' : 'Preview AR')
                    ->icon('heroicon-o-qr-code')
                    ->color('gray')
                    ->url(fn (ArCreative $record) => $record->arUrl())
                    ->openUrlInNewTab()
                    ->visible(fn (ArCreative $record) => $record->isReady()),

                Tables\Actions\Action::make('downloadQr')
                    ->label('QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->hidden(fn () => ! auth()->user()?->hasRole('super_admin'))
                    ->action(function (ArCreative $record): StreamedResponse {
                        $result = Qr::forCreative($record);

                        return response()->streamDownload(
                            fn () => print ($result->getString()),
                            "qr-{$record->slug}.png",
                            ['Content-Type' => $result->getMimeType()],
                        );
                    }),

                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListArCreatives::route('/'),
            'create' => Pages\CreateArCreative::route('/create'),
            'edit' => Pages\EditArCreative::route('/{record}/edit'),
        ];
    }
}
