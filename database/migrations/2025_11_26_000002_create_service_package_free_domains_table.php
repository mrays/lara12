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
            $table->unsignedBigInteger('service_package_id');
            $table->unsignedBigInteger('domain_extension_id');
            $table->integer('duration_years');
            $table->decimal('discount_percent', 5, 2)->default(0);
            $table->boolean('is_free')->default(false);
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->foreign('service_package_id')->references('id')->on('service_packages')->onDelete('cascade');
            $table->foreign('domain_extension_id')->references('id')->on('domain_extensions')->onDelete('cascade');
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
