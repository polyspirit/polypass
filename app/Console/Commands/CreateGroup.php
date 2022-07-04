<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use Illuminate\Support\Facades\DB;

class CreateGroup extends Command
{
    protected $signature = 'group:create {name}';
    protected $description = 'Create Group';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        DB::insert('insert into groups (name) values ("?")', [1, $this->argument('name')]);
        $this->info('done');
    }
}
