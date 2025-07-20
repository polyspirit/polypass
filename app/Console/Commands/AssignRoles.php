<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AssignRoles extends Command
{
    protected $signature = 'roles:assign {id} {role}';
    protected $description = 'Assign role to user';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $user = User::find($this->argument('id'));
        $user->syncRoles($this->argument('role'));
    }
}
