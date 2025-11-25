<?php

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
        // Migrate existing domain data from service_packages to service_package_free_domains
        $existingPackages = DB::table('service_packages')
            ->whereNotNull('domain_extension_id')
            ->whereNotNull('domain_duration_years')
            ->get();

        foreach ($existingPackages as $package) {
            DB::table('service_package_free_domains')->insert([
                'service_package_id' => $package->id,
                'domain_extension_id' => $package->domain_extension_id,
                'duration_years' => $package->domain_duration_years,
                'discount_percent' => $package->domain_discount_percent ?? 0,
                'is_free' => $package->is_domain_free ?? 0,
                'sort_order' => 0,
                'created_at' => $package->created_at,
                'updated_at' => $package->updated_at,
            ]);
        }

        // Optional: Remove old domain columns after migration is verified
        // Uncomment these lines after confirming migration worked correctly
        /*
        Schema::table('service_packages', function (Blueprint $table) {
            $table->dropForeign(['domain_extension_id']);
            $table->dropColumn('domain_extension_id');
            $table->dropColumn('domain_duration_years');
            $table->dropColumn('is_domain_free');
            $table->dropColumn('domain_discount_percent');
            $table->dropIndex('idx_domain_extension');
        });
        */
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Clear migrated data
        DB::table('service_package_free_domains')->truncate();
        
        // If you dropped the old columns in up(), you would need to recreate them here
        /*
        Schema::table('service_packages', function (Blueprint $table) {
            $table->foreignId('domain_extension_id')->nullable()->after('is_active')->comment('Foreign key to domain_extensions table for promo domain');
            $table->integer('domain_duration_years')->nullable()->after('domain_extension_id')->comment('Domain duration in years for promo (1-10)');
            $table->boolean('is_domain_free')->default(false)->after('domain_duration_years')->comment('1 = domain is free, 0 = normal price');
            $table->decimal('domain_discount_percent', 5, 2)->default(0.00)->after('is_domain_free')->comment('Domain discount percentage (0-100)');
            $table->index('domain_extension_id', 'idx_domain_extension');
        });

        Schema::table('service_packages', function (Blueprint $table) {
            $table->foreign('domain_extension_id')->references('id')->on('domain_extensions')->onDelete('set null')->onUpdate('cascade');
        });
        */
    }
};
