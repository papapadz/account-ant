<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        
        $permissions = config('bee.permissions', []);
        $roles = config('bee.roles', []);
        
        $permissionModels = collect($permissions)->map(function ($permission) {
            return Permission::findOrCreate($permission, 'web');
        });
        
        foreach ($roles as $role) {
            $roleItem = Role::findOrCreate($role, 'web');

            switch ($role) {
                case 'super_admin':
                    $roleItem->syncPermissions($permissionModels);
                    break;
                case 'admin':
                    $adminPerms = $permissionModels->filter(function ($p) {
                        return $p->name === 'view settings';
                    });
                    $roleItem->syncPermissions($adminPerms);
                    break;
            }
        }
    }
}
