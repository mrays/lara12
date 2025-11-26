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
        Schema::table('services', function (Blueprint $table) {
            $table->string('username')->nullable()->after('notes');
            $table->string('password')->nullable()->after('username');
            $table->string('server')->nullable()->after('password');
            $table->string('login_url')->nullable()->after('server');
            $table->text('description')->nullable()->after('login_url');
            $table->decimal('setup_fee', 10, 2)->nullable()->default(0)->after('description');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->dropColumn([
                'username',
                'password', 
                'server',
                'login_url',
                'description',
                'setup_fee'
            ]);
        });
    }
};
