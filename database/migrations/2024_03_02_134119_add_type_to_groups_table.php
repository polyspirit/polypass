<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use App\Enums\GroupTypeEnum;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('groups', 'type')) {
            Schema::table('groups', function (Blueprint $table) {
                $table->string('type', 15)->default(GroupTypeEnum::default())->after('name')->index();
            });
        }
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
