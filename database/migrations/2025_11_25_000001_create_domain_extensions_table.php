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
        Schema::create('domain_extensions', function (Blueprint $table) {
            $table->id();
            $table->string('extension'); // .com, .id, .org, etc.
            $table->integer('duration_years'); // 1, 2, 3, etc.
            $table->decimal('price', 10, 2); // Price in IDR or other currency
            $table->text('description')->nullable(); // Optional description
            $table->boolean('is_active')->default(true); // Enable/disable extension
            $table->timestamps();
            
            // Unique constraint to prevent duplicate extension+duration combinations
            $table->unique(['extension', 'duration_years']);
            
            // Indexes for better performance
            $table->index('extension');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('domain_extensions');
    }
};
