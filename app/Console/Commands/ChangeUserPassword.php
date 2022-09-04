<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

use App\Models\User;

class ChangeUserPassword extends Command
{
    protected $signature = 'user:password {id} {password}';
    protected $description = 'Change user password';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $user = User::find($this->argument('id'));
        $user->update(['password' => Hash::make($this->argument('password'))]);
        
        $this->info('done');
    }
}
