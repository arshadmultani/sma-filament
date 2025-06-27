<?php

namespace App\Filament\Imports;

use App\Models\Division;
use App\Models\Region;
use App\Models\Area;
use App\Models\User;
use App\Models\Headquarter;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Facades\Hash;
use App\Models\Zone;
use Illuminate\Validation\ValidationException;

class UserImporter extends Importer
{
    protected static ?string $model = User::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required']),
            ImportColumn::make('phone_number')
                ->rules(['max:255']),
            ImportColumn::make('division_id')
                ->label('Division')
                ->requiredMapping()
                ->rules(['required'])
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
            ImportColumn::make('role')
                ->label('Role')
                ->requiredMapping()
                ->rules(['required'])
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
            ImportColumn::make('zone_id')
                ->label('Zone')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
            ImportColumn::make('region_id')
                ->label('Region')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
            ImportColumn::make('area_id')
                ->label('Area')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
            ImportColumn::make('headquarter_id')
                ->label('Headquarter')
                ->requiredMapping()
                ->fillRecordUsing(function () {
                    // handled in resolveRecord
                }),
        ];
    }

    public function resolveRecord(): ?User
    {
        // Check if user already exists by email (production safe)
        $existingUser = User::where('email', $this->data['email'])->first();
        if ($existingUser) {
            // Skip import if user already exists
            return null;
        }

        $user = new User();
        $user->name = $this->data['name'];
        $user->password = $this->data['phone_number'];
        $user->email = $this->data['email'];
        $user->phone_number = $this->data['phone_number'];

        if (empty($this->data['division_id'])) {
            throw ValidationException::withMessages([
                'division_id' => ['Division is required.'],
            ]);
        }
        $division = Division::whereRaw('LOWER(name) = ?', [strtolower($this->data['division_id'])])->first();
        if (!$division) {
            throw ValidationException::withMessages([
                'division_id' => ['Division not found: ' . $this->data['division_id']],
            ]);
        }
        $user->division_id = $division->id;

        $roleConfig = [
            'ZSM' => ['model' => Zone::class, 'id_key' => 'zone_id', 'division_column' => 'division_id'],
            'RSM' => ['model' => Region::class, 'id_key' => 'region_id', 'division_column' => 'division_id'],
            'ASM' => ['model' => Area::class, 'id_key' => 'area_id', 'division_column' => 'division_id'],
            'DSA' => ['model' => Headquarter::class, 'id_key' => 'headquarter_id', 'division_column' => 'division_id'],
        ];
        
        $roleName = $this->data['role'];
        if (!isset($roleConfig[$roleName])) {
            throw ValidationException::withMessages([
                'role' => ['Invalid or missing role: ' . $roleName],
            ]);
        }
        $config = $roleConfig[$roleName];
        $locationModel = $config['model'];
        $locationIdKey = $config['id_key'];
        $divisionColumn = $config['division_column'];
        $user->location_type = $locationModel;
        if (empty($this->data[$locationIdKey])) {
            throw ValidationException::withMessages([
                $locationIdKey => [ucfirst(str_replace('_', ' ', $locationIdKey)) . ' is required for role ' . $roleName . '.'],
            ]);
        }
        $location = $locationModel::whereRaw('LOWER(name) = ?', [strtolower($this->data[$locationIdKey])])
            ->where($divisionColumn, $division->id)
            ->first();
        if (!$location) {
            throw ValidationException::withMessages([
                $locationIdKey => [ucfirst(str_replace('_', ' ', $locationIdKey)) . ' not found in division: ' . $this->data[$locationIdKey]],
            ]);
        }
        $user->location_id = $location->id;

        return $user;
    }
    protected function afterSave()
    {
        $user = $this->record;
        $roleName = $this->data['role'];
        switch ($roleName) {
            case 'ZSM':
                $user->assignRole('ZSM');
                break;
            case 'RSM':
                $user->assignRole('RSM');
                break;
            case 'ASM':
                $user->assignRole('ASM');
                break;
            case 'DSA':
                $user->assignRole('DSA');
                break;
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



