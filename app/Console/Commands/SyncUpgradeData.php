<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ServiceUpgradeRequest;
use App\Models\Service;
use App\Models\User;

class SyncUpgradeData extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'sync:upgrade-data {--create-sample : Create sample data for testing}';

    /**
     * The console command description.
     */
    protected $description = 'Sync upgrade request data and ensure proper relationships';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Syncing Upgrade Request Data...');
        $this->line('');

        // Step 1: Check existing data
        $this->info('1. Checking existing data...');
        
        $userCount = User::count();
        $serviceCount = Service::count();
        $upgradeCount = ServiceUpgradeRequest::count();
        
        $this->line("   📊 Users: {$userCount}");
        $this->line("   📊 Services: {$serviceCount}");
        $this->line("   📊 Upgrade Requests: {$upgradeCount}");

        // Step 2: Validate relationships
        $this->line('');
        $this->info('2. Validating relationships...');
        
        $invalidRequests = ServiceUpgradeRequest::whereDoesntHave('client')->count();
        $invalidServices = ServiceUpgradeRequest::whereDoesntHave('service')->count();
        
        if ($invalidRequests > 0) {
            $this->error("   ❌ {$invalidRequests} upgrade requests have invalid client_id");
        } else {
            $this->line("   ✅ All upgrade requests have valid client relationships");
        }
        
        if ($invalidServices > 0) {
            $this->error("   ❌ {$invalidServices} upgrade requests have invalid service_id");
        } else {
            $this->line("   ✅ All upgrade requests have valid service relationships");
        }

        // Step 3: Create sample data if requested
        if ($this->option('create-sample')) {
            $this->line('');
            $this->info('3. Creating sample data...');
            $this->createSampleData();
        }

        // Step 4: Show current upgrade requests
        $this->line('');
        $this->info('4. Current upgrade requests:');
        
        $requests = ServiceUpgradeRequest::with(['client', 'service'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        if ($requests->count() > 0) {
            $headers = ['ID', 'Client', 'Service', 'Current → Requested', 'Status', 'Created'];
            $rows = [];
            
            foreach ($requests as $request) {
                $rows[] = [
                    $request->id,
                    $request->client ? $request->client->name : 'N/A',
                    $request->service ? $request->service->product : 'N/A',
                    $request->current_plan . ' → ' . $request->requested_plan,
                    $request->status,
                    $request->created_at->format('Y-m-d H:i')
                ];
            }
            
            $this->table($headers, $rows);
        } else {
            $this->warn('   No upgrade requests found.');
        }

        // Step 5: Admin access info
        $this->line('');
        $this->info('5. Admin access:');
        
        $adminCount = User::where('role', 'admin')->count();
        if ($adminCount > 0) {
            $this->line("   ✅ {$adminCount} admin user(s) available");
            $this->line("   🌐 Access: /admin/upgrade-requests");
        } else {
            $this->warn("   ⚠️  No admin users found. Run: php artisan admin:create");
        }

        $this->line('');
        $this->info('✅ Sync completed!');
        
        return 0;
    }

    /**
     * Create sample data for testing
     */
    private function createSampleData()
    {
        // Ensure we have at least one client user
        $client = User::where('role', 'client')->first();
        if (!$client) {
            $client = User::create([
                'name' => 'Test Client',
                'email' => 'client@test.com',
                'password' => bcrypt('password'),
                'role' => 'client',
                'email_verified_at' => now(),
            ]);
            $this->line("   ✅ Created test client: {$client->email}");
        }

        // Ensure we have at least one service
        $service = Service::where('client_id', $client->id)->first();
        if (!$service) {
            $service = Service::create([
                'client_id' => $client->id,
                'product' => 'Business Website Basic',
                'domain' => 'testclient.com',
                'price' => 150000,
                'billing_cycle' => 'monthly',
                'registration_date' => now(),
                'due_date' => now()->addMonth(),
                'status' => 'Active',
                'notes' => 'Sample service for testing upgrade system',
            ]);
            $this->line("   ✅ Created test service: {$service->product}");
        }

        // Create sample upgrade request if none exists
        $existingRequest = ServiceUpgradeRequest::where('service_id', $service->id)
            ->where('status', 'pending')
            ->first();
            
        if (!$existingRequest) {
            $upgradeRequest = ServiceUpgradeRequest::create([
                'service_id' => $service->id,
                'client_id' => $client->id,
                'current_plan' => 'Business Website Basic',
                'requested_plan' => 'Business Website Premium',
                'current_price' => 150000,
                'requested_price' => 300000,
                'upgrade_reason' => 'business_growth',
                'additional_notes' => 'Need more storage and bandwidth for growing business',
                'status' => 'pending',
            ]);
            $this->line("   ✅ Created sample upgrade request: #{$upgradeRequest->id}");
        }

        // Ensure we have an admin user
        $admin = User::where('role', 'admin')->first();
        if (!$admin) {
            $admin = User::create([
                'name' => 'Admin User',
                'email' => 'admin@test.com',
                'password' => bcrypt('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ]);
            $this->line("   ✅ Created test admin: {$admin->email}");
        }
    }
}
