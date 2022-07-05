<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\Artisan;
use App\Models\Group;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('roles:make');
        Artisan::call('user:create admin zverskiy@yandex.ru qwe123 superadmin');

        $group = new Group;
        $group->name = 'root';
        $group->save();
    }
}