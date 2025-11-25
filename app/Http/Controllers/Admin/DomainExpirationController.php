<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientData;
use App\Models\DomainRegister;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DomainExpirationController extends Controller
{
    /**
     * Display client domain expiration monitoring dashboard
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, expired, expiring, safe
        
        // Build base query with MySQL
        $baseQuery = 'SELECT cd.*, dr.name as domain_register_name, dr.login_link 
                     FROM client_data cd 
                     LEFT JOIN domain_registers dr ON cd.domain_register_id = dr.id';
        
        $whereClause = '';
        $bindings = [];
        
        switch ($filter) {
            case 'expired':
                $whereClause = ' WHERE cd.domain_expired < ?';
                $bindings[] = now();
                break;
            case 'expiring':
                $whereClause = ' WHERE cd.domain_expired >= ? AND cd.domain_expired <= ?';
                $bindings[] = now();
                $bindings[] = now()->addMonths(3);
                break;
            case 'safe':
                $whereClause = ' WHERE cd.domain_expired > ?';
                $bindings[] = now()->addMonths(3);
                break;
            // 'all' shows all client domains
        }
        
        $fullQuery = $baseQuery . $whereClause . ' ORDER BY cd.domain_expired ASC';
        $clientDomains = DB::select($fullQuery, $bindings);
        
        // Calculate statistics using MySQL queries
        $stats = [
            'total' => DB::select('SELECT COUNT(*) as count FROM client_data')[0]->count,
            'expired' => DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired < ?', [now()])[0]->count,
            'expiring' => DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired >= ? AND domain_expired <= ?', [now(), now()->addMonths(3)])[0]->count,
            'safe' => DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired > ?', [now()->addMonths(3)])[0]->count,
        ];
        
        // Get upcoming expirations (next 30 days) using MySQL
        $upcomingQuery = 'SELECT cd.*, dr.name as domain_register_name, dr.login_link 
                          FROM client_data cd 
                          LEFT JOIN domain_registers dr ON cd.domain_register_id = dr.id
                          WHERE cd.domain_expired >= ? AND cd.domain_expired <= ? 
                          ORDER BY cd.domain_expired ASC LIMIT 10';
        $upcomingExpirations = DB::select($upcomingQuery, [now(), now()->addDays(30)]);
        
        // Critical expirations (next 7 days) using MySQL
        $criticalQuery = 'SELECT cd.*, dr.name as domain_register_name, dr.login_link 
                         FROM client_data cd 
                         LEFT JOIN domain_registers dr ON cd.domain_register_id = dr.id
                         WHERE cd.domain_expired >= ? AND cd.domain_expired <= ? 
                         ORDER BY cd.domain_expired ASC';
        $criticalExpirations = DB::select($criticalQuery, [now(), now()->addDays(7)]);
        
        // Recently expired (last 30 days) using MySQL
        $recentQuery = 'SELECT cd.*, dr.name as domain_register_name, dr.login_link 
                       FROM client_data cd 
                       LEFT JOIN domain_registers dr ON cd.domain_register_id = dr.id
                       WHERE cd.domain_expired < ? AND cd.domain_expired >= ? 
                       ORDER BY cd.domain_expired DESC';
        $recentlyExpired = DB::select($recentQuery, [now(), now()->subDays(30)]);
        
        // Convert stdClass objects to arrays for view compatibility
        $clientDomains = $this->convertStdClassToArray($clientDomains);
        $upcomingExpirations = $this->convertStdClassToArray($upcomingExpirations);
        $criticalExpirations = $this->convertStdClassToArray($criticalExpirations);
        $recentlyExpired = $this->convertStdClassToArray($recentlyExpired);
        
        return view('admin.domain-expiration.index', compact(
            'clientDomains', 
            'stats', 
            'filter',
            'upcomingExpirations',
            'criticalExpirations',
            'recentlyExpired'
        ));
    }
    
    /**
     * Convert stdClass objects to arrays with proper date objects
     */
    private function convertStdClassToArray($items)
    {
        return collect($items)->map(function($item) {
            $itemArray = (array) $item;
            
            // Convert date strings to Carbon objects
            $dateFields = ['domain_expired', 'website_service_expired', 'hosting_expired', 'created_at', 'updated_at'];
            foreach ($dateFields as $field) {
                if (isset($itemArray[$field]) && $itemArray[$field]) {
                    $itemArray[$field] = new Carbon($itemArray[$field]);
                }
            }
            
            // Create domain_register relationship
            if (isset($itemArray['domain_register_name'])) {
                $itemArray['domainRegister'] = (object) [
                    'name' => $itemArray['domain_register_name'] ?? null,
                    'login_link' => $itemArray['login_link'] ?? null
                ];
                unset($itemArray['domain_register_name'], $itemArray['login_link']);
            }
            
            return (object) $itemArray;
        })->all();
    }
    
    /**
     * Send renewal reminder for client domain
     */
    public function sendReminder(ClientData $client)
    {
        // Create WhatsApp message for renewal reminder
        $message = "Halo {$client->name},\n\n" .
                  "Ini adalah pengingat bahwa domain Anda akan segera expired:\n" .
                  "📅 Tanggal Expired: {$client->domain_expired->format('d F Y')}\n" .
                  "⏰ {$client->domain_expired->diffInDays(now())} hari lagi\n\n" .
                  "Silakan lakukan perpanjangan sebelum tanggal kadaluarsa untuk menghindari downtime.\n\n" .
                  "Terima kasih.";
        
        $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $client->whatsapp) . "?text=" . urlencode($message);
        
        return redirect()->away($whatsappUrl);
    }
    
    /**
     * Export client domain expiration report using MySQL
     */
    public function export(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        // Build MySQL query for export
        $baseQuery = 'SELECT cd.name, dr.name as domain_register_name, cd.domain_expired, 
                      cd.website_service_expired, cd.hosting_expired, cd.whatsapp, cd.status
                      FROM client_data cd 
                      LEFT JOIN domain_registers dr ON cd.domain_register_id = dr.id';
        
        $whereClause = '';
        $bindings = [];
        
        switch ($filter) {
            case 'expired':
                $whereClause = ' WHERE cd.domain_expired < ?';
                $bindings[] = now();
                break;
            case 'expiring':
                $whereClause = ' WHERE cd.domain_expired >= ? AND cd.domain_expired <= ?';
                $bindings[] = now();
                $bindings[] = now()->addMonths(3);
                break;
            case 'safe':
                $whereClause = ' WHERE cd.domain_expired > ?';
                $bindings[] = now()->addMonths(3);
                break;
        }
        
        $fullQuery = $baseQuery . $whereClause . ' ORDER BY cd.domain_expired ASC';
        $domains = DB::select($fullQuery, $bindings);
        
        $csvData = [];
        $csvData[] = ['Client Name', 'Domain Register', 'Domain Expired', 'Days Until Expiry', 'Status', 'WhatsApp', 'Website Expired', 'Hosting Expired'];
        
        foreach ($domains as $domain) {
            $domainExpired = new Carbon($domain->domain_expired);
            $daysUntil = $domainExpired->diffInDays(now(), false);
            $status = $domainExpired->isPast() ? 'Expired' : 
                     ($domainExpired->lte(now()->addMonths(3)) ? 'Expiring Soon' : 'Safe');
            
            $csvData[] = [
                $domain->name,
                $domain->domain_register_name ?? 'N/A',
                $domainExpired->format('Y-m-d'),
                $daysUntil,
                $status,
                $domain->whatsapp,
                (new Carbon($domain->website_service_expired))->format('Y-m-d'),
                (new Carbon($domain->hosting_expired))->format('Y-m-d')
            ];
        }
        
        $filename = "client_domain_expiration_report_" . now()->format('Y-m-d') . ".csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];
        
        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
    
    /**
     * Load sample client data for demonstration
     */
    public function loadSampleData()
    {
        try {
            // Run the seeder
            \Artisan::call('db:seed', ['--class' => 'ClientDataSeeder']);
            
            return response()->json([
                'success' => true,
                'message' => 'Sample client data loaded successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading sample data: ' . $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Show domain assignment guide
     */
    public function guide()
    {
        // Get statistics using MySQL queries for the guide
        $stats = [
            'total' => DB::select('SELECT COUNT(*) as count FROM client_data')[0]->count,
            'expired' => DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired < ?', [now()])[0]->count,
            'expiring' => DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired >= ? AND domain_expired <= ?', [now(), now()->addMonths(3)])[0]->count,
            'safe' => DB::select('SELECT COUNT(*) as count FROM client_data WHERE domain_expired > ?', [now()->addMonths(3)])[0]->count,
        ];
        
        return view('admin.domain-expiration.guide', compact('stats'));
    }
}
