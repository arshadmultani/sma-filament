<?php

namespace App\Filament\Imports;

use App\Models\User;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;


class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    protected ?string $importRoleName = null;

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
                ->relationship('division', 'name')
                ->examples(['Pharma', 'Phytonova', 'Pharma']),

            ImportColumn::make('roles.name')
                ->label('Role')
                ->rules(['max:255'])
                ->examples(['RSM', 'ASM', 'DSA'])
                ->fillRecordUsing(function () {
                    // Do nothing, handled in afterSave
                }),

            // ImportColumn::make('region')
            //     ->rules(['max:255'])
            //     ->examples(['Maharashtra', 'Gujarat', 'Karnataka']),

            // ImportColumn::make('area')
            //     ->rules(['max:255'])
            //     ->examples(['', 'Ahmedabad', 'Banglore']),

            // ImportColumn::make('headquarter')
            //     ->examples(['', '', 'Whitefield'])
            //     ->rules(['max:255']),
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
        // // Map location_type and location_id based on headquarter, area, region (priority: headquarter > area > region)
        // if (!empty($this->data['headquarter'])) {
        //     $headquarter = \App\Models\Headquarter::where('name', $this->data['headquarter'])->first();
        //     if ($headquarter) {
        //         $user->location_type = \App\Models\Headquarter::class;
        //         $user->location_id = $headquarter->id;
        //     }
        // } elseif (!empty($this->data['area'])) {
        //     $area = \App\Models\Area::where('name', $this->data['area'])->first();
        //     if ($area) {
        //         $user->location_type = \App\Models\Area::class;
        //         $user->location_id = $area->id;
        //     }
        // } elseif (!empty($this->data['region'])) {
        //     $region = \App\Models\Region::where('name', $this->data['region'])->first();
        //     if ($region) {
        //         $user->location_type = \App\Models\Region::class;
        //         $user->location_id = $region->id;
        //     }
        // }
        // Map role name to role id (store for later assignment)
        // $user->_import_role_id = null;
        if (!empty($this->data['roles.name'])) {
            $role = Role::where('name', $this->data['roles.name'])->first();
            Log::info('ROOOOLE DATA WILL COME HERE....');
            Log::info($role);
            if ($role) {
                $this->importRoleName = $role->name;
            }
        }
        return $user;
    }

    protected function afterSave()
    {
        if ($this->importRoleName && $this->record) {
            $this->record->assignRole($this->importRoleName);
        }
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Your user import has completed and ' . number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows) . ' imported.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to import.';
        }

        return $body;
    }
    
}
    