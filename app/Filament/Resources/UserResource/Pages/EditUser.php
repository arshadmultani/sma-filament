<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\Facades\Hash;
use Filament\Actions\ActionGroup;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendUserCredentials;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;
   
    public function getTitle(): string
{
    return $this->record->name . ' - ' . ($this->record->roles->first()?->name ?? ' ');
}
    protected function getHeaderActions(): array
    {
        return [
            ActionGroup::make([
                Actions\Action::make('sendCredentials')
                    ->label('Update Password')
                    ->icon('heroicon-m-lock-closed')
                    ->color('primary')
                    ->closeModalByEscaping()
                    ->closeModalByClickingAway()
                    ->form([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('email')
                                    ->label('User Email')
                                    ->disabled()
                                    ->default(fn(EditUser $livewire) => $livewire->record->email),

                                Forms\Components\TextInput::make('password')
                                    ->label('New Password')
                                    ->placeholder('Enter a new password')
                                    ->password()
                                    ->required()
                                    ->revealable()
                                    ->minLength(6),
                            ])
                    ])
                    ->modalHeading('Update Password')
                    ->modalDescription('Enter a new password. The user will receive these credentials by email.')
                    ->modalSubmitActionLabel('Send')
                    ->modalCancelActionLabel('Cancel')
                    ->action(function (array $data, EditUser $livewire) {
                        try {
                            $user = $livewire->record;

                            $user->update([
                                'password' => Hash::make($data['password']),
                            ]);

                            Mail::to($user->email)->send(mailable: new SendUserCredentials($user->email, $data['password']));

                            \Filament\Notifications\Notification::make()
                                ->success()
                                ->title('Password Updated')
                                ->body('The user\'s password has been updated and sent to their email.')
                                ->send();
                        } catch (\Exception $e) {
                            \Filament\Notifications\Notification::make()
                                ->danger()
                                ->title('Error')
                                ->body('Failed to update password. Please try again.')
                                ->send();
                        }
                    }),
                Actions\DeleteAction::make()
                    ->color('danger')
                    ->label('Delete User Permanently'),
                
            ])->icon('heroicon-m-cog-6-tooth')
                ->label('Settings')
                ->color('black'),


        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        $originalEmail = $record->email;
        $emailChanged = isset($data['email']) && $data['email'] !== $originalEmail;
        $passwordChanged = !empty($data['password']);
        $plainPassword = $passwordChanged ? $data['password'] : null;

        $record->update($data);
        // Assign roles if present
        if (isset($data['roles'])) {
            $record->roles()->sync($data['roles']);
        }
        // Send notification if email or password changed
        if ($emailChanged || $passwordChanged) {
            Mail::to($record->email)->send(new SendUserCredentials($record->email, $plainPassword ?? 'Your password was not changed.'));
        }
        return $record;
    }
    public function saved()
{
    if ($this->data['roles']) {
        $this->record->syncRoles([$this->data['roles']]);
    }
}
}
