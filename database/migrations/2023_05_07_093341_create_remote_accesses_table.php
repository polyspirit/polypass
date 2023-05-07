<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('remote_accesses', function (Blueprint $table) {
            $table->id();
            $table->string('ip', '31')->nullable();
            $table->string('path')->default('');
            $table->boolean('blocked')->default(false);
            $table->timestamps();

            $table->index('ip');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('remote_accesses');
    }
};
