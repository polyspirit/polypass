<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

use App\Models\Group;
use App\Models\User;
use App\Enums\GroupTypeEnum;

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

        if (!User::all()->count()) {
            Artisan::call('user:create admin admin@example.com qwe123 superadmin');
        }

        $rootGroup = Group::where('type', GroupTypeEnum::Root->value)
            ->orWhere('name', GroupTypeEnum::Root->value)
            ->orWhere('name', __('groups.' . GroupTypeEnum::Root->value))
            ->first();

        if (is_null($rootGroup)) {
            $rootGroup = new Group;
        }

        $rootGroup->name = __('groups.' . GroupTypeEnum::Root->value);
        $rootGroup->type = GroupTypeEnum::Root->value;
        $rootGroup->save();
    }
}
