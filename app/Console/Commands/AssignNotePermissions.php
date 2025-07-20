<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AssignNotePermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:assign-notes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign note permissions to roles';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Assigning note permissions to roles...');

        // Get or create permissions
        $permissions = [
            'notes-create' => Permission::firstOrCreate(['name' => 'notes-create']),
            'notes-modify-any' => Permission::firstOrCreate(['name' => 'notes-modify-any']),
            'notes-modify' => Permission::firstOrCreate(['name' => 'notes-modify']),
        ];

        foreach ($permissions as $name => $permission) {
            $this->info("Permission: {$name}");
        }

        // Get roles
        $adminRole = Role::where('name', 'admin')->first();
        $userRole = Role::where('name', 'user')->first();
        $superadminRole = Role::where('name', 'superadmin')->first();

        if ($superadminRole) {
            $this->info('Assigning all permissions to superadmin role...');
            foreach ($permissions as $permission) {
                $superadminRole->givePermissionTo($permission);
                $this->info("Assigned {$permission->name} to superadmin");
            }
        }

        if ($adminRole) {
            $this->info('Assigning all permissions to admin role...');
            foreach ($permissions as $permission) {
                $adminRole->givePermissionTo($permission);
                $this->info("Assigned {$permission->name} to admin");
            }
        }

        if ($userRole) {
            $this->info('Assigning basic permissions to user role...');
            $userPermissions = [$permissions['notes-create'], $permissions['notes-modify']];
            foreach ($userPermissions as $permission) {
                $userRole->givePermissionTo($permission);
                $this->info("Assigned {$permission->name} to user");
            }
        }

        $this->info('Note permissions assigned successfully!');

        return Command::SUCCESS;
    }
}
