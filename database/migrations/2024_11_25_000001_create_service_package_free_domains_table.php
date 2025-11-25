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
        Schema::create('service_package_free_domains', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_package_id')->constrained()->onDelete('cascade');
            $table->foreignId('domain_extension_id')->constrained()->onDelete('cascade');
            $table->integer('duration_years')->comment('Domain duration in years (1-10)');
            $table->decimal('discount_percent', 5, 2)->default(0.00)->comment('Domain discount percentage (0-100)');
            $table->boolean('is_free')->default(false)->comment('1 = domain is free, 0 = normal price');
            $table->integer('sort_order')->default(0)->comment('Order in which domain appears in UI');
            $table->timestamps();

            // Prevent duplicate domain assignments for same package
            $table->unique(['service_package_id', 'domain_extension_id'], 'unique_package_domain');
            
            // Indexes for better performance
            $table->index(['service_package_id', 'sort_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_package_free_domains');
    }
};
