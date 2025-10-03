<?php

namespace App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\ToggleButtons;
use Filament\Tables;
use App\Models\State;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Settings\MicrositeSettings;
use Filament\Forms\Components\Textarea;
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
use App\Filament\Doctor\Resources\DoctorWebsiteResource;
use Filament\Resources\RelationManagers\RelationManager;

class ReviewsRelationManager extends RelationManager
{
    protected static string $relationship = 'reviews';
    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('reviewer_name')
                    ->label('Patient Name')
                    ->required()
                    ->placeholder('John Doe')
                    ->maxLength(25)
                    ->minLength(3),
                Textarea::make('review_text')
                    ->label('Review Text(optional)')
                    ->placeholder('Patient\'s review goes here...')
                    ->maxLength(500)
                    ->minLength(10),
                FileUpload::make('media_url')
                    ->label('Upload Review Photo/Video')
                    ->disk('s3')
                    ->visibility('private')
                    ->directory('microsite/reviews')
                    ->maxFiles(1)
                    ->acceptedFileTypes([
                        'image/jpeg',
                        'image/png',
                        'image/gif',
                        'video/mp4',
                        'video/quicktime',
                        'video/x-msvideo'
                    ])
                    ->rules([
                        function () {
                            return function ($attribute, $value, $fail) {
                                $file = is_array($value) ? $value[0] : $value;
                                if (!$file)
                                    return;

                                $mimeType = $file->getMimeType();
                                $size = $file->getSize() / 1024;

                                $videoSize = app(MicrositeSettings::class)->max_review_video_size;
                                $imageSize = app(MicrositeSettings::class)->max_review_image_size;

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
                ToggleButtons::make('is_verified')
                    ->label('Do you want to show this review on your website?')
                    ->boolean()
                    ->inline()
                    ->required()
                    ->grouped(),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->paginated(false)
            ->recordTitleAttribute('title')
            ->description('Patient reviews')
            ->emptyStateIcon('heroicon-o-chat-bubble-bottom-center-text')
            ->emptyStateDescription('No reviews found. Start by adding a new review.')
            ->recordTitleAttribute('reviewer_name')
            ->columns([
                TextColumn::make('reviewer_name')
                    ->label('Reviewer')
                    ->limit(15),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                CreateAction::make()
                    ->outlined()
                    ->icon('heroicon-o-plus')
                    ->label('New Review')
                    ->createAnother(false)
                    ->before(function (CreateAction $action, array $data) {
                        $doctor = DoctorWebsiteResource::currentDoctor();
                        if ($doctor->reviews()->count() >= app(MicrositeSettings::class)->review_count) {
                            Notification::make()
                                ->title('You can only add up to ' . app(MicrositeSettings::class)->review_count . ' reviews.')
                                ->danger()
                                ->send();
                            $action->cancel();
                        }
                        if (empty($data['review_text']) && empty($data['media_url'])) {
                            Notification::make()
                                ->title('Please provide either review text or upload a media file.')
                                ->danger()
                                ->send();
                            $action->halt();
                        }
                    })
                    ->mutateFormDataUsing(function (array $data) {
                        $data['doctor_id'] = auth()->user()->userable_id;
                        $data['is_verified'] = false;
                        $data['state_id'] = State::pending()->first()->id;


                        if (isset($data['media_url'])) {
                            $mimeType = Storage::mimeType($data['media_url']);
                            $data['media_type'] = Str::startsWith($mimeType, 'video')
                                ? 'video'
                                : 'image';
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
                ]),
            ]);
    }
}
