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
            // Add login/access information columns
            $table->string('username', 255)->nullable()->after('billing_cycle');
            $table->string('password', 255)->nullable()->after('username');
            $table->string('server', 255)->nullable()->after('password');
            $table->string('login_url', 500)->nullable()->after('server');
            
            // Add additional service details
            $table->text('description')->nullable()->after('login_url');
            $table->text('notes')->nullable()->after('description');
            $table->decimal('setup_fee', 15, 2)->nullable()->default(0)->after('price');
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
                'notes', 
                'setup_fee'
            ]);
        });
    }
};
