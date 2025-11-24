<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\ServiceUpgradeRequest;
use App\Models\Service;
use App\Models\User;

class TestUpgradeSystem extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'test:upgrade-system';

    /**
     * The console command description.
     */
    protected $description = 'Test the upgrade system components';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Testing Upgrade System Components...');
        $this->line('');

        // Test 1: Check required tables
        $this->info('1. Checking required tables...');
        $tables = [
            'users' => 'Users table',
            'services' => 'Services table', 
            'service_upgrade_requests' => 'Service upgrade requests table',
            'password_reset_tokens' => 'Password reset tokens table'
        ];

        foreach ($tables as $table => $description) {
            if (Schema::hasTable($table)) {
                $this->line("   ✅ {$description}: EXISTS");
            } else {
                $this->error("   ❌ {$description}: MISSING");
            }
        }

        // Test 2: Check role column in users
        $this->line('');
        $this->info('2. Checking users table structure...');
        if (Schema::hasColumn('users', 'role')) {
            $this->line('   ✅ Role column: EXISTS');
            
            // Check admin users
            $adminCount = DB::table('users')->where('role', 'admin')->count();
            $this->line("   📊 Admin users: {$adminCount}");
        } else {
            $this->error('   ❌ Role column: MISSING');
        }

        // Test 3: Check models
        $this->line('');
        $this->info('3. Testing models...');
        
        try {
            $userCount = User::count();
            $this->line("   ✅ User model: OK ({$userCount} users)");
        } catch (\Exception $e) {
            $this->error("   ❌ User model: ERROR - {$e->getMessage()}");
        }

        try {
            $serviceCount = Service::count();
            $this->line("   ✅ Service model: OK ({$serviceCount} services)");
        } catch (\Exception $e) {
            $this->error("   ❌ Service model: ERROR - {$e->getMessage()}");
        }

        try {
            $upgradeCount = ServiceUpgradeRequest::count();
            $this->line("   ✅ ServiceUpgradeRequest model: OK ({$upgradeCount} requests)");
        } catch (\Exception $e) {
            $this->error("   ❌ ServiceUpgradeRequest model: ERROR - {$e->getMessage()}");
        }

        // Test 4: Check routes
        $this->line('');
        $this->info('4. Checking routes...');
        
        $routes = [
            'services.upgrade.request' => 'POST /services/{service}/upgrade-request',
            'client.upgrade-requests.index' => 'GET /upgrade-requests',
            'admin.upgrade-requests.index' => 'GET /admin/upgrade-requests'
        ];

        foreach ($routes as $routeName => $routePath) {
            try {
                $route = route($routeName, ['service' => 1, 'request' => 1, 'upgradeRequest' => 1]);
                $this->line("   ✅ {$routeName}: OK");
            } catch (\Exception $e) {
                $this->error("   ❌ {$routeName}: ERROR");
            }
        }

        // Test 5: Sample data check
        $this->line('');
        $this->info('5. Checking sample data...');
        
        $sampleService = Service::first();
        if ($sampleService) {
            $this->line("   ✅ Sample service found: #{$sampleService->id} - {$sampleService->product}");
            
            // Check if service has client_id
            if ($sampleService->client_id) {
                $this->line("   ✅ Service has client_id: {$sampleService->client_id}");
            } else {
                $this->error("   ❌ Service missing client_id");
            }
        } else {
            $this->error("   ❌ No services found in database");
        }

        // Summary
        $this->line('');
        $this->info('📋 Summary & Next Steps:');
        $this->line('');
        
        if (!Schema::hasTable('service_upgrade_requests')) {
            $this->warn('⚠️  Run: quick_fix_auth.sql to create missing tables');
        }
        
        if (!Schema::hasColumn('users', 'role')) {
            $this->warn('⚠️  Run: ALTER TABLE users ADD COLUMN role ENUM(\'admin\', \'client\', \'staff\') DEFAULT \'client\';');
        }
        
        $adminCount = DB::table('users')->where('role', 'admin')->count();
        if ($adminCount === 0) {
            $this->warn('⚠️  Run: php artisan admin:create');
        }

        $this->line('');
        $this->info('🚀 Test completed! Check the results above.');
        
        return 0;
    }
}
