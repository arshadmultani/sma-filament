<?php

namespace App\Filament\Resources\MicrositeResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use App\Models\State;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use App\Settings\MicrositeSettings;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use App\Infolists\Components\VideoEntry;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
                    ->helperText(fn() => "Upload a video (max " . (app(MicrositeSettings::class)->max_showcase_video_size / 1024) . "MB) or image (max " . (app(MicrositeSettings::class)->max_showcase_image_size / 1024) . "MB). Supported formats: JPG, PNG, MP4, MOV, AVI"),


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
                    ->label('Patient Name')
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
                    ->modalDescription('Add at least one of the following: review text or upload file. Do not leave both empty.')
                    ->createAnother(false)
                    ->before(function (CreateAction $action, array $data, RelationManager $livewire) {
                        $doctor = $livewire->getOwnerRecord()->doctor;
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
                    ->mutateFormDataUsing(function (array $data, RelationManager $livewire) {
                        $doctor = $livewire->getOwnerRecord()->doctor;

                        $data['doctor_id'] = $doctor->id;
                        $data['submitted_by_name'] = auth()->user()->name;
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
                ViewAction::make(),
                EditAction::make()
                    ->visible(auth()->user()->can('view_user')),
                DeleteAction::make()
                    ->visible(auth()->user()->can('view_user')),

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                TextEntry::make('submitted_by_name')
                    ->label('Submitted By')
                    ->visible(fn($record) => $record->submitted_by_name),
                TextEntry::make('review_text')
                    ->label('Review Text')
                    ->visible(fn($record) => $record->review_text),
                IconEntry::make('is_verified')
                    ->label('Verified')
                    ->boolean(),
                TextEntry::make('verified_at')
                    ->label('Verified At')
                    ->since()
                    ->visible(fn($record) => $record->is_verified),
                TextEntry::make('media_type')
                    ->label('Media Type')
                    ->visible(fn($record) => $record->media_type)
                    ->badge(),
                TextEntry::make('state.name')
                    ->label('Status')
                    ->badge()
                    ->color(fn($record) => $record->state->color),
                TextEntry::make('created_at')
                    ->label('Upload')
                    ->since(),
                // Conditional media display based on type
                ImageEntry::make('media_file_url')
                    ->label('Media')
                    ->visible(fn($record) => $record->media_type === 'image')
                    ->height(200),
                VideoEntry::make('media_file_url')
                    ->label('Media')
                    ->visible(fn($record) => $record->media_type === 'video')
                    ->controls()
                    ->controlsListNoDownload(),

            ]);
    }
}
