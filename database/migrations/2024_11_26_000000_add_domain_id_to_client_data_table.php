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
        Schema::table('client_data', function (Blueprint $table) {
            $table->foreignId('domain_id')->nullable()->after('whatsapp')->constrained('domains')->onDelete('set null');
            
            // Drop old expiration columns
            $table->dropColumn(['website_service_expired', 'domain_expired', 'hosting_expired']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('client_data', function (Blueprint $table) {
            $table->dropForeign(['domain_id']);
            $table->dropColumn('domain_id');
            
            // Add back old expiration columns
            $table->date('website_service_expired')->nullable()->after('whatsapp');
            $table->date('domain_expired')->nullable()->after('website_service_expired');
            $table->date('hosting_expired')->nullable()->after('domain_expired');
        });
    }
};
