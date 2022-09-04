<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Credential;
use App\Models\Group;

class AssignUserIds extends Command
{
    protected $signature = 'user:assign {id}';
    protected $description = 'Assign user_id to all entities';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $userId = $this->argument('id');
        
        $groups = Group::all();

        foreach ($groups as $group) {
            $group->update(['user_id' => $userId]);
        }

        $credentials = Credential::all();

        foreach ($credentials as $credential) {
            $credential->update(['user_id' => $userId]);
        }
    }
}