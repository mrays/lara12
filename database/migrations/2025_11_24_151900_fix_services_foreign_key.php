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
            // Drop existing foreign key constraint
            $table->dropForeign(['client_id']);
            
            // Add new foreign key constraint pointing to users table
            $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('services', function (Blueprint $table) {
            // Drop the users foreign key
            $table->dropForeign(['client_id']);
            
            // Restore original foreign key to clients table (if clients table exists)
            // $table->foreign('client_id')->references('id')->on('clients')->onDelete('cascade');
        });
    }
};
