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
        Schema::table('service_packages', function (Blueprint $table) {
            $table->boolean('is_visible')->default(true)->after('is_active')
                ->comment('Whether package is visible on client order page');
            $table->boolean('is_custom')->default(false)->after('is_visible')
                ->comment('Whether package is a custom/special package for specific clients');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('service_packages', function (Blueprint $table) {
            $table->dropColumn(['is_visible', 'is_custom']);
        });
    }
};
