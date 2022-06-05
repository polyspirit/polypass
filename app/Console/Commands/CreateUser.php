<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;

use App\Models\User;

class CreateUser extends Command
{
    protected $signature = 'user:create {name} {email} {password} {role}';
    protected $description = 'Create User';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $validator = Validator::make($this->arguments(), [
            'email' => 'required|string|email|unique:users,email|max:255',
            'password' => 'required|string|min:6',
            'name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            $this->info($validator->errors());
        } else {
            $user = User::create([
                'name' => $this->argument('name'),
                'password' => bcrypt($this->argument('password')),
                'email' => $this->argument('email')
            ]);

            $role = $this->argument('role') ?? 'user';

            $user->assignRole($role);

            $this->info('done');
        }

    }
}
