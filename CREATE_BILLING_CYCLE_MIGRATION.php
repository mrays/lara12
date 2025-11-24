<?php
// Create this migration file: database/migrations/xxxx_xx_xx_update_billing_cycle_enum.php
// Run: php artisan make:migration update_billing_cycle_enum

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update existing billing cycle data to new format
        DB::statement("
            UPDATE services 
            SET billing_cycle = CASE 
                WHEN billing_cycle IN ('Monthly', 'monthly', '1 month', '1month') THEN '1 Bulan'
                WHEN billing_cycle IN ('Quarterly', 'quarterly', '3 months', '3month') THEN '3 Bulan'
                WHEN billing_cycle IN ('Semi-Annually', 'semi-annually', '6 months', '6month') THEN '6 Bulan'
                WHEN billing_cycle IN ('Annually', 'annually', '1 year', '1year', 'yearly') THEN '1 Tahun'
                WHEN billing_cycle IN ('Biennially', 'biennially', '2 years', '2year') THEN '2 Tahun'
                WHEN billing_cycle IN ('One Time', 'onetime', 'one-time') THEN '1 Bulan'
                ELSE billing_cycle
            END
            WHERE billing_cycle IS NOT NULL
        ");

        // Optional: Add enum constraint (MySQL only)
        // Schema::table('services', function (Blueprint $table) {
        //     $table->enum('billing_cycle', [
        //         '1 Bulan', '2 Bulan', '3 Bulan', '6 Bulan', 
        //         '1 Tahun', '2 Tahun', '3 Tahun', '4 Tahun'
        //     ])->nullable()->change();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to old format if needed
        DB::statement("
            UPDATE services 
            SET billing_cycle = CASE 
                WHEN billing_cycle = '1 Bulan' THEN 'Monthly'
                WHEN billing_cycle = '3 Bulan' THEN 'Quarterly'
                WHEN billing_cycle = '6 Bulan' THEN 'Semi-Annually'
                WHEN billing_cycle = '1 Tahun' THEN 'Annually'
                WHEN billing_cycle = '2 Tahun' THEN 'Biennially'
                ELSE billing_cycle
            END
            WHERE billing_cycle IS NOT NULL
        ");
    }
};
