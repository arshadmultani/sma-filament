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
   
    

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('name')
                ->requiredMapping()
                ->rules(['required', 'max:255']),
            ImportColumn::make('email')
                ->requiredMapping()
                ->rules(['required', 'email', 'max:255']),
            ImportColumn::make('phone_number')
                ->rules(['max:255']),
            ImportColumn::make('division_id')
                ->label('Division')
                ->requiredMapping()
                ->rules(['required'])
                ->examples(['Pharma', 'Phytonova'])
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
        $user = new User();
        $user->name = $this->data['name'];
        $user->password = Hash::make('12345678');
        $user->email = $this->data['email'];
        $user->phone_number = $this->data['phone_number'];

        if (!empty($this->data['division_id'])) {
            $division = Division::whereRaw('LOWER(name) = ?', [strtolower($this->data['division_id'])])->first();
            $user->division_id = $division ? $division->id : null;
        }
        $roleConfig = [
            'ZSM' => ['model' => Zone::class, 'id_key' => 'zone_id'],
            'RSM' => ['model' => Region::class, 'id_key' => 'region_id'],
            'ASM' => ['model' => Area::class, 'id_key' => 'area_id'],
            'DSA' => ['model' => Headquarter::class, 'id_key' => 'headquarter_id'],
        ];
        
        $roleName = $this->data['role'];
        if (isset($roleConfig[$roleName])) {
            $config = $roleConfig[$roleName];
            $locationModel = $config['model'];
            $locationIdKey = $config['id_key'];
        
            $user->location_type = $locationModel;
        
            // Check if the corresponding ID is provided in the data
            if (!empty($this->data[$locationIdKey])) {
                // Find the location by its name (case-insensitive)
                $location = $locationModel::whereRaw('LOWER(name) = ?', [strtolower($this->data[$locationIdKey])])->firstOrFail();
                $user->location_id = $location->id;
            }
        }

        // switch ($roleName) {
        //     case 'ZSM':
        //         $user->location_type = Zone::class;
        //         if (!empty($this->data['zone_id'])) {
        //             $zone = Zone::whereRaw('LOWER(name) = ?', [strtolower($this->data['zone_id'])])->firstOrFail();
        //             $user->location_id = $zone->id;
        //         }
        //         break;
        //     case 'RSM':
        //         $user->location_type = Region::class;
        //         if (!empty($this->data['region_id'])) {
        //             $region = Region::whereRaw('LOWER(name) = ?', [strtolower($this->data['region_id'])])->firstOrFail();
        //             $user->location_id = $region->id;
        //         }
        //         break;
        //     case 'ASM':
        //         $user->location_type = Area::class;
        //         if (!empty($this->data['area_id'])) {
        //             $area = Area::whereRaw('LOWER(name) = ?', [strtolower($this->data['area_id'])])->firstOrFail();
        //             $user->location_id = $area->id;
        //         }
        //         break;
        //     case 'DSA':
        //         $user->location_type = Headquarter::class;
        //         if (!empty($this->data['headquarter_id'])) {
        //             $headquarter = Headquarter::whereRaw('LOWER(name) = ?', [strtolower($this->data['headquarter_id'])])->firstOrFail();
        //             $user->location_id = $headquarter->id;
        //         }
        //         break;
        // }
        // $user->save();
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

