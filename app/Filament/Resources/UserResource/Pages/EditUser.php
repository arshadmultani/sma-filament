<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use App\Mail\SendUserCredentials;
use Filament\Actions;
use Filament\Actions\ActionGroup;
use Filament\Forms;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    public function getTitle(): string
    {
        return ($this->record->roles->first()?->name ?? ' ').' - '.$this->record->name;
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
                                    ->default(fn (EditUser $livewire) => $livewire->record->email),

                                Forms\Components\TextInput::make('password')
                                    ->label('New Password')
                                    ->placeholder('Enter a new password')
                                    ->password()
                                    ->required()
                                    ->revealable()
                                    ->minLength(6),
                            ]),
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
        $passwordChanged = ! empty($data['password']);
        $plainPassword = $passwordChanged ? $data['password'] : null;

        // Set location_type and location_id based on role
        $roleID = $data['roles'] ?? null;
        $roleName = $roleID ? \Spatie\Permission\Models\Role::find($roleID)?->name : null;
        if ($roleName === 'RSM') {
            $data['location_type'] = \App\Models\Region::class;
            $data['location_id'] = $data['region_id'] ?? null;
        } elseif ($roleName === 'ASM') {
            $data['location_type'] = \App\Models\Area::class;
            $data['location_id'] = $data['area_id'] ?? null;
        } elseif ($roleName === 'DSA') {
            $data['location_type'] = \App\Models\Headquarter::class;
            $data['location_id'] = $data['headquarter_id'] ?? null;
        } elseif ($roleName === 'ZSM') {
            $data['location_type'] = \App\Models\Zone::class;
            $data['location_id'] = $data['zone_id'] ?? null;
        } else {
            $data['location_type'] = null;
            $data['location_id'] = null;
        }
        unset($data['zone_id'], $data['region_id'], $data['area_id'], $data['headquarter_id']);

        // Assign roles if present
        if (isset($data['roles'])) {
            $record->roles()->sync($data['roles']);
            unset($data['roles']); // Remove roles before update
        }
        $record->update($data);
        // Send notification if email or password changed
        if ($emailChanged || $passwordChanged) {
            Mail::to($record->email)->send(new SendUserCredentials($record->email, $plainPassword ?? 'Your password was not changed.'));
        }

        return $record;
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Only set 'roles' for edit, not create
        if ($this->record && $this->record->exists) {
            $data['roles'] = $this->record->roles()->pluck('id')->first();

            // Hierarchical location prefill (as before)
            $data['division_id'] = $this->record->division_id;
            $data['zone_id'] = null;
            $data['region_id'] = null;
            $data['area_id'] = null;
            $data['headquarter_id'] = null;

            if ($this->record->location_type === \App\Models\Zone::class) {
                $data['zone_id'] = $this->record->location_id;
            } elseif ($this->record->location_type === \App\Models\Region::class) {
                $region = \App\Models\Region::find($this->record->location_id);
                $data['region_id'] = $region?->id;
                $data['zone_id'] = $region?->zone_id;
            } elseif ($this->record->location_type === \App\Models\Area::class) {
                $area = \App\Models\Area::find($this->record->location_id);
                $data['area_id'] = $area?->id;
                $data['region_id'] = $area?->region_id;
                $data['zone_id'] = $area?->region?->zone_id;
            } elseif ($this->record->location_type === \App\Models\Headquarter::class) {
                $hq = \App\Models\Headquarter::find($this->record->location_id);
                $data['headquarter_id'] = $hq?->id;
                $data['area_id'] = $hq?->area_id;
                $data['region_id'] = $hq?->area?->region_id;
                $data['zone_id'] = $hq?->area?->region?->zone_id;
            }
        }
        return $data;
    }

    public function saved()
    {
        if ($this->data['roles']) {
            $this->record->syncRoles([$this->data['roles']]);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
