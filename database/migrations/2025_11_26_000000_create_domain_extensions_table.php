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
            $table->string('extension', 10); // e.g., 'com', 'id', 'net'
            $table->decimal('price', 10, 2);
            $table->integer('duration_years')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['extension', 'duration_years']);
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
