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
        // First update existing data from 'Sent' to 'gagal'
        DB::statement("UPDATE invoices SET status = 'gagal' WHERE status = 'Sent'");
        
        // Then modify the column to remove 'Sent' and add 'gagal' if not already done
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['Draft', 'gagal', 'Paid', 'Overdue', 'Cancelled'])->default('Draft')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse the data change
        DB::statement("UPDATE invoices SET status = 'Sent' WHERE status = 'gagal'");
        
        // Reverse the column change
        Schema::table('invoices', function (Blueprint $table) {
            $table->enum('status', ['Draft', 'Sent', 'Paid', 'Overdue', 'Cancelled'])->default('Draft')->change();
        });
    }
};
