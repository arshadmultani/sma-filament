<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ImportFailedNotification;
use Illuminate\Support\Facades\Notification;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    protected ?string $importRoleName = null;
    protected ?string $importRegion = null;
    protected ?string $importArea = null;
    protected ?string $importHeadquarter = null;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->examples(['John Doe', 'Jane Smith', 'Michael Brown'])
                ->rules(['required', 'max:255']),

            ImportColumn::make('email')
                ->requiredMapping()
                ->examples(['john.doe@example.com', 'jane.smith@example.com', 'michael.brown@example.com'])
                ->rules(['required', 'email', 'max:255']),

            ImportColumn::make('password')
                ->requiredMapping()
                ->examples(['password123', 'secret123', 'admin123'])
                ->rules(['required', 'max:255']),

            ImportColumn::make('phone_number')
                ->examples(['+1234567890', '+0987654321', '+1122334455'])
                ->rules(['max:255']),

            ImportColumn::make('division_id')
                ->label('Division')
                ->relationship('division', resolveUsing: 'name')
                ->examples(['Pharma', 'Phytonova', 'Pharma']),

            ImportColumn::make('roles.name')
                ->label('Role')
                ->rules(['max:255'])
                ->examples(['RSM', 'ASM', 'DSA'])
                ->fillRecordUsing(function () {
                    // Do nothing, handled in afterSave
                }),

            ImportColumn::make('region')
                ->rules(['max:255'])
                ->examples(['Maharashtra', 'Maharashtra', 'Maharashtra'])
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),

            ImportColumn::make('area')
                ->rules(['max:255'])
                ->examples(['', 'Mumbai', 'Mumbai'])
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),

            ImportColumn::make('headquarter')
                ->examples(['', '', 'Vasai'])
                ->rules(['max:255'])
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
        ];
    }

    public function resolveRecord(): ?User
    {
        // Always create a new user, never set the id manually
        $user = new User();
        $user->name = $this->data['name'] ?? null;
        $user->email = $this->data['email'] ?? null;
        $user->password = Hash::make($this->data['password'] ?? null);
        $user->phone_number = $this->data['phone_number'] ?? null;
        
        if (!empty($this->data['division_id'])) {
            $division = \App\Models\Division::whereRaw('LOWER(name) = ?', [strtolower($this->data['division_id'])])->first();
            if ($division) {
                $user->division_id = $division->id;
            }
        }

        // Store for afterSave
        $this->importRoleName = $this->data['roles.name'] ?? null;
        $this->importRegion = $this->data['region'] ?? null;
        $this->importArea = $this->data['area'] ?? null;
        $this->importHeadquarter = $this->data['headquarter'] ?? null;

        return $user;
    }

    protected function afterSave()
    {
        if ($this->importRoleName && $this->record) {
            $this->record->assignRole($this->importRoleName);

            if ($this->importRoleName === 'RSM' && !empty($this->importRegion)) {
                $region = \App\Models\Region::whereRaw('LOWER(name) = ?', [strtolower(trim($this->importRegion))])->first();
                if ($region) {
                    $this->record->location_type = \App\Models\Region::class;
                    $this->record->location_id = $region->id;
                    $this->record->save();
                }
            } elseif ($this->importRoleName === 'ASM' && !empty($this->importArea)) {
                $area = \App\Models\Area::whereRaw('LOWER(name) = ?', [strtolower(trim($this->importArea))])->first();
                if ($area) {
                    $this->record->location_type = \App\Models\Area::class;
                    $this->record->location_id = $area->id;
                    $this->record->save();
                } else {
                    // Log::warning('Area not found for import', ['area' => $this->importArea]);
                }
            } elseif ($this->importRoleName === 'DSA' && !empty($this->importHeadquarter)) {
                $hq = \App\Models\Headquarter::whereRaw('LOWER(name) = ?', [strtolower(trim($this->importHeadquarter))])->first();
                if ($hq) {
                    $this->record->location_type = \App\Models\Headquarter::class;
                    $this->record->location_id = $hq->id;
                    $this->record->save();
                } else {
                    // Log::warning('Headquarter not found for import', ['headquarter' => $this->importHeadquarter]);
                }
            }
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        $userId = $import->options['user_id'] ?? null;
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                Notification::send($user, new ImportFailedNotification($import->getFailedRows()));
            }
        }

        return $body;
    }
    
}
    