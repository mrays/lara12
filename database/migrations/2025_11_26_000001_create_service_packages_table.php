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
        Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description');
            $table->decimal('base_price', 15, 2);
            $table->json('features')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('domain_extension_id')->nullable();
            $table->integer('domain_duration_years')->nullable();
            $table->boolean('is_domain_free')->default(false);
            $table->decimal('domain_discount_percent', 5, 2)->default(0);
            $table->timestamps();

            $table->foreign('domain_extension_id')->references('id')->on('domain_extensions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('service_packages');
    }
};
