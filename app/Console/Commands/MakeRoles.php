<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class MakeRoles extends Command
{
    protected $signature = 'roles:make';
    protected $description = 'Make all roles and peremissions';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $roles = config('roles.roles');

        foreach ($roles as $roleName => $permissions) {
            $role = Role::findByName($roleName);
            if (is_null($role)) {
                $role = Role::create(['name' => $roleName]);
            }

            foreach ($permissions as $permissionName) {
                try {
                    $permission = Permission::create(['name' => $permissionName]);
                } catch (\Throwable $th) {
                    $permission = Permission::findByName($permissionName);
                }
                $permission->assignRole($role);
            }
        }
    }
}
