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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('clients')->onDelete('cascade');
            $table->string('product');
            $table->string('domain')->nullable();
            $table->decimal('price', 10, 2);
            $table->enum('billing_cycle', ['Monthly', 'Quarterly', 'Semi-Annually', 'Annually', 'Biennially']);
            $table->date('registration_date');
            $table->date('due_date');
            $table->string('ip')->nullable();
            $table->enum('status', ['Active', 'Suspended', 'Terminated', 'Pending'])->default('Pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
