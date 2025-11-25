<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClientData;
use App\Models\DomainRegister;
use App\Models\Server;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ClientDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get existing data using MySQL queries
        $domainRegisters = DB::select('SELECT id, name FROM domain_registers ORDER BY name ASC');
        $servers = DB::select('SELECT id, name FROM servers ORDER BY name ASC');
        $users = DB::select('SELECT id, name FROM users WHERE role = ? ORDER BY name ASC', ['client']);
        
        if (empty($domainRegisters)) {
            $this->command->error('No domain registers found. Please add domain registers first.');
            return;
        }
        
        if (empty($servers)) {
            $this->command->error('No servers found. Please add servers first.');
            return;
        }
        
        // Sample client data with various expiration scenarios
        $sampleClients = [
            [
                'name' => 'PT. Teknologi Maju',
                'address' => 'Jl. Sudirman No. 123, Jakarta Selatan',
                'whatsapp' => '+6281234567890',
                'website_service_expired' => Carbon::now()->addDays(15), // Expiring soon
                'domain_expired' => Carbon::now()->addDays(15), // Expiring soon
                'hosting_expired' => Carbon::now()->addMonths(2),
                'server_id' => $servers[0]->id,
                'domain_register_id' => $domainRegisters[0]->id,
                'user_id' => !empty($users) ? $users[0]->id : null,
                'status' => 'warning',
                'notes' => 'Client penting, perlu diperhatikan renewalnya'
            ],
            [
                'name' => 'CV. Karya Digital',
                'address' => 'Jl. Gatot Subroto No. 456, Jakarta Pusat',
                'whatsapp' => '+6282345678901',
                'website_service_expired' => Carbon::now()->subDays(5), // Expired
                'domain_expired' => Carbon::now()->subDays(5), // Expired
                'hosting_expired' => Carbon::now()->addDays(10),
                'server_id' => $servers[1]->id ?? $servers[0]->id,
                'domain_register_id' => $domainRegisters[1]->id ?? $domainRegisters[0]->id,
                'user_id' => !empty($users) && isset($users[1]) ? $users[1]->id : null,
                'status' => 'expired',
                'notes' => 'Domain expired, perlu segera renewal'
            ],
            [
                'name' => 'UD. Jaya Abadi',
                'address' => 'Jl. Thamrin No. 789, Jakarta Barat',
                'whatsapp' => '+6283456789012',
                'website_service_expired' => Carbon::now()->addMonths(6), // Safe
                'domain_expired' => Carbon::now()->addMonths(6), // Safe
                'hosting_expired' => Carbon::now()->addMonths(8),
                'server_id' => $servers[2]->id ?? $servers[0]->id,
                'domain_register_id' => $domainRegisters[2]->id ?? $domainRegisters[0]->id,
                'user_id' => !empty($users) && isset($users[2]) ? $users[2]->id : null,
                'status' => 'active',
                'notes' => 'Client regular, semua aman'
            ],
            [
                'name' => 'PT. Solusi Bisnis',
                'address' => 'Jl. MH Thamrin No. 321, Jakarta',
                'whatsapp' => '+6284567890123',
                'website_service_expired' => Carbon::now()->addDays(3), // Critical
                'domain_expired' => Carbon::now()->addDays(3), // Critical
                'hosting_expired' => Carbon::now()->addDays(30),
                'server_id' => $servers[0]->id,
                'domain_register_id' => $domainRegisters[0]->id,
                'user_id' => !empty($users) && isset($users[3]) ? $users[3]->id : null,
                'status' => 'warning',
                'notes' => 'Critical: akan expired dalam 3 hari!'
            ],
            [
                'name' => 'CV. Media Kreatif',
                'address' => 'Jl. Kemang No. 567, Jakarta Selatan',
                'whatsapp' => '+6285678901234',
                'website_service_expired' => Carbon::now()->addMonths(4), // Safe
                'domain_expired' => Carbon::now()->addMonths(4), // Safe
                'hosting_expired' => Carbon::now()->addMonths(3),
                'server_id' => $servers[1]->id ?? $servers[0]->id,
                'domain_register_id' => $domainRegisters[1]->id ?? $domainRegisters[0]->id,
                'user_id' => !empty($users) && isset($users[4]) ? $users[4]->id : null,
                'status' => 'active',
                'notes' => 'Client baru, semua layanan aktif'
            ],
            [
                'name' => 'PT. Inovasi Teknologi',
                'address' => 'Jl. SCBD No. 890, Jakarta',
                'whatsapp' => '+6286789012345',
                'website_service_expired' => Carbon::now()->subDays(15), // Expired
                'domain_expired' => Carbon::now()->subDays(15), // Expired
                'hosting_expired' => Carbon::now()->subDays(10), // Expired
                'server_id' => $servers[2]->id ?? $servers[0]->id,
                'domain_register_id' => $domainRegisters[2]->id ?? $domainRegisters[0]->id,
                'user_id' => !empty($users) && isset($users[5]) ? $users[5]->id : null,
                'status' => 'expired',
                'notes' => 'Semua layanan expired, urgent!'
            ]
        ];

        // Use MySQL INSERT with multiple rows for better performance
        $insertData = [];
        foreach ($sampleClients as $client) {
            $insertData[] = [
                'name' => $client['name'],
                'address' => $client['address'],
                'whatsapp' => $client['whatsapp'],
                'website_service_expired' => $client['website_service_expired'],
                'domain_expired' => $client['domain_expired'],
                'hosting_expired' => $client['hosting_expired'],
                'server_id' => $client['server_id'],
                'domain_register_id' => $client['domain_register_id'],
                'user_id' => $client['user_id'],
                'status' => $client['status'],
                'notes' => $client['notes'],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Bulk insert using MySQL
        DB::table('client_data')->insert($insertData);

        // Get statistics using MySQL queries
        $totalClients = DB::select('SELECT COUNT(*) as count FROM client_data')[0]->count;
        $expiredCount = DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired < ?', [now()])[0]->count;
        $expiringCount = DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired >= ? AND domain_expired <= ?', [now(), now()->addMonths(3)])[0]->count;
        $safeCount = DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired > ?', [now()->addMonths(3)])[0]->count;

        $this->command->info('✅ Sample client data created successfully!');
        $this->command->info('📊 Statistics:');
        $this->command->info("   • Total Clients: {$totalClients}");
        $this->command->info("   • Expired Domains: {$expiredCount} (❌)");
        $this->command->info("   • Expiring Soon: {$expiringCount} (⚠️)");
        $this->command->info("   • Safe Domains: {$safeCount} (✅)");
        $this->command->info('');
        $this->command->info('🔗 Access the domain expiration monitoring at: /admin/domain-expiration');
    }
}
