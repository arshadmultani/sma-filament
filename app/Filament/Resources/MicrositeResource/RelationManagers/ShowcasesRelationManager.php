<?php

namespace App\Filament\Resources\MicrositeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Settings\MicrositeSettings;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;

class ShowcasesRelationManager extends RelationManager
{
    protected static string $relationship = 'showcases';

    public function isReadOnly(): bool
    {
        return false;
    }
    public function form(Form $form): Form
    {
        return $form
            ->columns(1)
            ->schema([
                TextInput::make('title')
                    ->label('Title of Photo/Video')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Dr. Video, Clinic Tour')
                    ->helperText('Provide a brief title for your photo or video.'),
                FileUpload::make('media_url')
                    ->label('Upload Media')
                    ->disk('s3')
                    ->visibility('private')
                    ->directory('microsite/showcases')
                    ->maxFiles(1)
                    ->required()
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/gif', // Images
                        'video/mp4',
                        'video/quicktime',
                        'video/x-msvideo' // Videos (mp4, mov, avi)
                    ])
                    ->rules([
                        function () {
                            return function ($attribute, $value, $fail) {
                                $file = is_array($value) ? $value[0] : $value;
                                if (!$file)
                                    return;

                                $mimeType = $file->getMimeType();
                                $size = $file->getSize() / 1024; // Convert to KB
                
                                $videoSize = app(MicrositeSettings::class)->max_showcase_video_size;
                                $imageSize = app(MicrositeSettings::class)->max_showcase_image_size;

                                $maxSize = str_contains($mimeType, 'video/') ? $videoSize : $imageSize;

                                if ($size > $maxSize) {
                                    $maxSizeMB = $maxSize / 1024;
                                    $fail("For " .
                                        (str_contains($mimeType, 'video/') ? 'videos' : 'images') .
                                        ", maximum allowed size is {$maxSizeMB}MB.");
                                }
                            };
                        }
                    ])
                    ->helperText(fn() => "Upload a video (max " . (app(MicrositeSettings::class)->max_showcase_video_size / 1024) . "MB) or image (max " . (app(MicrositeSettings::class)->max_showcase_image_size / 1024) . "MB). Supported formats: JPG, PNG, GIF, MP4, MOV, AVI"),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->recordTitleAttribute('title')
            ->description('Add doctor introduction videos or clinic images/videos to showcase their practice.')
            ->emptyStateIcon('heroicon-o-video-camera')
            ->columns([
                TextColumn::make('title')
                    ->placeholder('doctor video title')
                    ->limit(15)
                    ->label('Title'),
                TextColumn::make('media_type')
                    ->badge(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->modalHeading('Add New Showcase')
                    // ->modalDescription('')
                    ->outlined()
                    ->icon('heroicon-o-plus')
                    ->createAnother(false)
                    ->before(function (CreateAction $action, RelationManager $livewire) {
                        $doctor = $livewire->getOwnerRecord()->doctor;
                        if ($doctor->showcases()->count() >= app(MicrositeSettings::class)->showcase_count) {
                            Notification::make()
                                ->title('You can only add up to ' . app(MicrositeSettings::class)->showcase_count . ' showcases.')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                    })
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire): array {
                        $doctor = $livewire->getOwnerRecord()->doctor;
                        $data['doctor_id'] = $doctor->id;

                        // Determine media type from uploaded file
                        if (isset($data['media_url'])) {
                            $mimeType = Storage::mimeType($data['media_url']);
                            $data['media_type'] = str_contains($mimeType, 'video') ? 'video' : 'image';
                        }

                        return $data;
                    }),
            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make()
                        ->modalHeading(fn($record) => $record->title),
                    EditAction::make()
                        ->modalHeading(fn($record) => $record->title),
                    DeleteAction::make()
                ]),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

}
