<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_area","view_any_area","create_area","update_area","restore_area","restore_any_area","replicate_area","reorder_area","delete_area","delete_any_area","force_delete_area","force_delete_any_area","view_brand","view_any_brand","create_brand","update_brand","restore_brand","restore_any_brand","replicate_brand","reorder_brand","delete_brand","delete_any_brand","force_delete_brand","force_delete_any_brand","view_chemist","view_any_chemist","create_chemist","update_chemist","delete_chemist","delete_any_chemist","update_status_chemist","view_division","view_any_division","create_division","update_division","restore_division","restore_any_division","replicate_division","reorder_division","delete_division","delete_any_division","force_delete_division","force_delete_any_division","view_doctor","view_any_doctor","create_doctor","update_doctor","delete_doctor","delete_any_doctor","update_status_doctor","view_headquarter","view_any_headquarter","create_headquarter","update_headquarter","restore_headquarter","restore_any_headquarter","replicate_headquarter","reorder_headquarter","delete_headquarter","delete_any_headquarter","force_delete_headquarter","force_delete_any_headquarter","view_kofol::campaign","view_any_kofol::campaign","create_kofol::campaign","update_kofol::campaign","restore_kofol::campaign","restore_any_kofol::campaign","replicate_kofol::campaign","reorder_kofol::campaign","delete_kofol::campaign","delete_any_kofol::campaign","force_delete_kofol::campaign","force_delete_any_kofol::campaign","view_kofol::entry","view_any_kofol::entry","create_kofol::entry","update_kofol::entry","delete_kofol::entry","delete_any_kofol::entry","update_status_kofol::entry","view_product","view_any_product","create_product","update_product","restore_product","restore_any_product","replicate_product","reorder_product","delete_product","delete_any_product","force_delete_product","force_delete_any_product","view_qualification","view_any_qualification","create_qualification","update_qualification","restore_qualification","restore_any_qualification","replicate_qualification","reorder_qualification","delete_qualification","delete_any_qualification","force_delete_qualification","force_delete_any_qualification","view_region","view_any_region","create_region","update_region","restore_region","restore_any_region","replicate_region","reorder_region","delete_region","delete_any_region","force_delete_region","force_delete_any_region","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_specialty","view_any_specialty","create_specialty","update_specialty","restore_specialty","restore_any_specialty","replicate_specialty","reorder_specialty","delete_specialty","delete_any_specialty","force_delete_specialty","force_delete_any_specialty","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","view_zone","view_any_zone","create_zone","update_zone","restore_zone","restore_any_zone","replicate_zone","reorder_zone","delete_zone","delete_any_zone","force_delete_zone","force_delete_any_zone","page_Campaigns","page_Customers","page_Reports","page_HealthCheckResults","page_Products"]},{"name":"admin","guard_name":"web","permissions":["view_area","view_any_area","create_area","update_area","restore_area","restore_any_area","replicate_area","reorder_area","delete_area","delete_any_area","force_delete_area","force_delete_any_area","view_brand","view_any_brand","create_brand","update_brand","restore_brand","restore_any_brand","replicate_brand","reorder_brand","delete_brand","delete_any_brand","force_delete_any_brand","force_delete_brand","view_chemist","view_any_chemist","create_chemist","update_chemist","delete_chemist","delete_any_chemist","update_status_chemist","view_division","view_any_division","create_division","update_division","restore_division","restore_any_division","replicate_division","reorder_division","delete_division","delete_any_division","force_delete_division","force_delete_any_division","view_doctor","view_any_doctor","create_doctor","update_doctor","delete_doctor","delete_any_doctor","update_status_doctor","view_headquarter","view_any_headquarter","create_headquarter","update_headquarter","restore_headquarter","restore_any_headquarter","replicate_headquarter","reorder_headquarter","delete_headquarter","delete_any_headquarter","force_delete_headquarter","force_delete_any_headquarter","view_kofol::campaign","view_any_kofol::campaign","create_kofol::campaign","update_kofol::campaign","restore_kofol::campaign","restore_any_kofol::campaign","replicate_kofol::campaign","reorder_kofol::campaign","delete_kofol::campaign","delete_any_kofol::campaign","force_delete_kofol::campaign","force_delete_any_kofol::campaign","view_kofol::entry","view_any_kofol::entry","create_kofol::entry","update_kofol::entry","delete_kofol::entry","delete_any_kofol::entry","update_status_kofol::entry","view_product","view_any_product","create_product","update_product","restore_product","restore_any_product","replicate_product","reorder_product","delete_product","delete_any_product","force_delete_product","force_delete_any_product","view_qualification","view_any_qualification","create_qualification","update_qualification","restore_qualification","restore_any_qualification","replicate_qualification","reorder_qualification","delete_qualification","delete_any_qualification","force_delete_qualification","force_delete_any_qualification","view_region","view_any_region","create_region","update_region","restore_region","restore_any_region","replicate_region","reorder_region","delete_region","delete_any_region","force_delete_region","force_delete_any_region","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_specialty","view_any_specialty","create_specialty","update_specialty","restore_specialty","restore_any_specialty","replicate_specialty","reorder_specialty","delete_specialty","delete_any_specialty","force_delete_specialty","force_delete_any_specialty","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","view_zone","view_any_zone","create_zone","update_zone","restore_zone","restore_any_zone","replicate_zone","reorder_zone","delete_zone","delete_any_zone","force_delete_zone","force_delete_any_zone","page_Campaigns","page_Customers","page_Reports","page_HealthCheckResults","page_Products"]},{"name":"RSM","guard_name":"web","permissions":["view_chemist","view_any_chemist","create_chemist","update_chemist","update_status_chemist","view_doctor","view_any_doctor","create_doctor","update_doctor","update_status_doctor","view_kofol::campaign","view_any_kofol::campaign","view_kofol::entry","view_any_kofol::entry","create_kofol::entry","update_kofol::entry","update_status_kofol::entry"]},{"name":"ASM","guard_name":"web","permissions":[]},{"name":"DSA","guard_name":"web","permissions":["view_chemist","view_any_chemist","create_chemist","update_chemist","view_doctor","view_any_doctor","create_doctor","update_doctor","view_kofol::campaign","view_any_kofol::campaign","view_kofol::entry","view_any_kofol::entry"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
