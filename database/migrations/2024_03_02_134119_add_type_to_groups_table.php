<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\GroupTypeEnum;
use App\Models\Group;

use Database\Seeders\DatabaseSeeder;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('groups', 'type')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->string('type', 15)->default(GroupTypeEnum::default())->after('name')->index();
            });
        }

        resolve(DatabaseSeeder::class)->run();
    }

    public function down(): void
    {
        if (Schema::hasColumn('groups', 'type')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->dropColumn('type');
            });
        }
    }
};
