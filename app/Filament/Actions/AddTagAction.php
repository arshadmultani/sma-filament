<?php

namespace App\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use App\Models\Tag;
use Filament\Forms\Components\CheckboxList;

class AddTagAction extends Action
{
    public static function make(
        string $name = null,
        string $tagType = 'doctor', // e.g., 'doctor', 'chemist'
        string $relationship = 'tags', // relationship name on the model
        string $label = 'Add Tag',
        string $modalHeading = 'Add Tag'
    ): static {
        return parent::make($name ?? 'addTag')
            ->icon('heroicon-m-tag')
            ->color('primary')
            ->modalWidth('lg')
            ->label($label)
            ->modalHeading($modalHeading)
            ->form(function (Model $record) use ($tagType, $relationship) {
                static $notified = false;
                $existingTagIds = $record->{$relationship}->pluck('id')->toArray();
                $options = Tag::where('attached_to', $tagType)
                    ->whereNotIn('id', $existingTagIds)
                    ->pluck('name', 'id');
                if ($options->isEmpty() && !$notified) {
                    Notification::make()
                        ->title('All applicable tags have been successfully added. You may now close this popup.')
                        ->info()
                        ->send();
                    $notified = true;
                    return [];
                }
                $fields = [
                    Select::make('tags')
                        ->label('Tags')
                        ->options($options)
                        ->searchable(),
                ];
                // Show existing tags as removable badges if user can('view_user')
                if (Auth::user()?->can('view_user') && !empty($existingTagIds)) {
                    $fields[] = CheckboxList::make('remove_tags')
                        ->label('Tick the boxes to remove tags')
                        ->options(\App\Models\Tag::whereIn('id', $existingTagIds)->pluck('name', 'id'))
                        ->columns(2);
                }
                return $fields;
            })
            ->action(function (array $data, Model $record) use ($relationship) {
                $userId = Auth::id();

                if (!empty($data['tags'])) {
                    $syncData = [];
                    foreach ((array) $data['tags'] as $tagId) {
                        $syncData[$tagId] = ['user_id' => $userId];
                    }
                    $record->{$relationship}()->syncWithoutDetaching($syncData);
                }
                if (!empty($data['remove_tags'])) {
                    $record->{$relationship}()->detach($data['remove_tags']);
                }
                Notification::make()
                    ->title('Tag updated successfully!')
                    ->success()
                    ->send();
            });
    }
}
