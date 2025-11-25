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
        Schema::create('client_data', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('address');
            $table->string('whatsapp');
            $table->date('website_service_expired');
            $table->date('domain_expired');
            $table->date('hosting_expired');
            
            // Foreign keys
            $table->foreignId('server_id')->nullable()->constrained('servers')->onDelete('set null');
            $table->foreignId('domain_register_id')->nullable()->constrained('domain_registers')->onDelete('set null');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null'); // link ke user jika ada
            
            $table->enum('status', ['active', 'expired', 'warning'])->default('active');
            $table->text('notes')->nullable();
            $table->timestamps();
            
            $table->index(['status', 'website_service_expired', 'domain_expired', 'hosting_expired']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('client_data');
    }
};
