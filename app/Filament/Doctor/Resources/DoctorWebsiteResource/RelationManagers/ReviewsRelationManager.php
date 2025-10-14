<?php

namespace App\Filament\Doctor\Resources\DoctorWebsiteResource\RelationManagers;

use Filament\Forms;
use Filament\Infolists\Components\IconEntry;
use Filament\Tables;
use App\Models\State;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Infolists\Infolist;
use App\Settings\MicrositeSettings;
use Filament\Forms\Components\Radio;
use App\Actions\Review\ApproveReview;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Support\Facades\Storage;
use App\Infolists\Components\VideoEntry;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\FileUpload;
use Filament\Tables\Actions\CreateAction;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Builder;
use Filament\Infolists\Components\Actions;
use Filament\Forms\Components\ToggleButtons;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ImageEntry;
use App\Filament\Actions\Reviews\RejectReview;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Doctor\Resources\DoctorWebsiteResource;
use Filament\Resources\RelationManagers\RelationManager;
use App\Filament\Actions\Reviews\ApproveReview as ApproveReviewAction;

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
                Radio::make('verified_at')
                    ->label('Do you want to show this review on your website?')
                    ->formatStateUsing(fn($state) => (bool) $state)
                    ->dehydrateStateUsing(fn($state) => $state ? now() : null)
                    ->boolean()
                    ->options([
                        1 => 'Yes',
                        0 => 'No',
                    ])
                    ->inline()
                    ->required(),


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
                IconColumn::make('verified_at')
                    ->label('Verification')
                    ->getStateUsing(fn($record) => (bool) $record->verified_at)
                    ->boolean(),
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
                        ->color('primary')
                        ->modalHeading(fn($record) => 'Patient: ' . $record->reviewer_name),
                    ApproveReviewAction::makeTable()
                        ->hidden(fn($record) => $record->verified_at),
                    RejectReview::makeTable()
                        ->visible(fn($record) => $record->verified_at),
                    EditAction::make()
                        ->color('primary')
                        ->modalHeading(fn($record) => 'Patient: ' . $record->reviewer_name),
                    DeleteAction::make()
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
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
                IconEntry::make('verified_at')
                    ->label('Verified')
                    ->getStateUsing(fn($record) => (bool) $record->verified_at)
                    ->boolean(),
                TextEntry::make('verified_at')
                    ->label('Verified At')
                    ->since()
                    ->visible(fn($record) => $record->verified_at),
                TextEntry::make('media_type')
                    ->label('Media Type')
                    ->default('text')
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
                    ->visible(fn($record) => $record->media_type === 'image'),
                VideoEntry::make('media_file_url')
                    ->label('Media')
                    ->visible(fn($record) => $record->media_type === 'video')
                    ->controls()
                    ->controlsListNoDownload(),
            ]);
    }
}
