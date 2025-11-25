<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DomainRegister;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DomainExpirationController extends Controller
{
    /**
     * Display all domain registers listing
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, expired, expiring, safe
        
        // Build base query with MySQL for domain registers
        $baseQuery = 'SELECT dr.*, COUNT(cd.id) as client_count 
                     FROM domain_registers dr 
                     LEFT JOIN client_data cd ON dr.id = cd.domain_register_id';
        
        $groupBy = ' GROUP BY dr.id';
        $whereClause = '';
        $bindings = [];
        
        switch ($filter) {
            case 'expired':
                $whereClause = ' WHERE dr.expired_date < ?';
                $bindings[] = now();
                break;
            case 'expiring':
                $whereClause = ' WHERE dr.expired_date >= ? AND dr.expired_date <= ?';
                $bindings[] = now();
                $bindings[] = now()->addMonths(3);
                break;
            case 'safe':
                $whereClause = ' WHERE dr.expired_date > ?';
                $bindings[] = now()->addMonths(3);
                break;
            // 'all' shows all domain registers
        }
        
        $orderBy = ' ORDER BY dr.expired_date ASC';
        $fullQuery = $baseQuery . $whereClause . $groupBy . $orderBy;
        $domainRegisters = DB::select($fullQuery, $bindings);
        
        // Calculate statistics using MySQL queries
        $stats = [
            'total' => DB::select('SELECT COUNT(*) as count FROM domain_registers')[0]->count,
            'expired' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date < ?', [now()])[0]->count,
            'expiring' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date >= ? AND expired_date <= ?', [now(), now()->addMonths(3)])[0]->count,
            'safe' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date > ?', [now()->addMonths(3)])[0]->count,
            'total_clients' => DB::select('SELECT COUNT(*) as count FROM client_data')[0]->count,
        ];
        
        // Get upcoming expirations (next 30 days) using MySQL
        $upcomingQuery = 'SELECT dr.*, COUNT(cd.id) as client_count 
                          FROM domain_registers dr 
                          LEFT JOIN client_data cd ON dr.id = cd.domain_register_id
                          WHERE dr.expired_date >= ? AND dr.expired_date <= ? 
                          GROUP BY dr.id
                          ORDER BY dr.expired_date ASC LIMIT 10';
        $upcomingExpirations = DB::select($upcomingQuery, [now(), now()->addDays(30)]);
        
        // Critical expirations (next 7 days) using MySQL
        $criticalQuery = 'SELECT dr.*, COUNT(cd.id) as client_count 
                         FROM domain_registers dr 
                         LEFT JOIN client_data cd ON dr.id = cd.domain_register_id
                         WHERE dr.expired_date >= ? AND dr.expired_date <= ? 
                         GROUP BY dr.id
                         ORDER BY dr.expired_date ASC';
        $criticalExpirations = DB::select($criticalQuery, [now(), now()->addDays(7)]);
        
        // Recently expired (last 30 days) using MySQL
        $recentQuery = 'SELECT dr.*, COUNT(cd.id) as client_count 
                       FROM domain_registers dr 
                       LEFT JOIN client_data cd ON dr.id = cd.domain_register_id
                       WHERE dr.expired_date < ? AND dr.expired_date >= ? 
                       GROUP BY dr.id
                       ORDER BY dr.expired_date DESC';
        $recentlyExpired = DB::select($recentQuery, [now(), now()->subDays(30)]);
        
        // Convert stdClass objects to arrays for view compatibility
        $domainRegisters = $this->convertStdClassToArray($domainRegisters);
        $upcomingExpirations = $this->convertStdClassToArray($upcomingExpirations);
        $criticalExpirations = $this->convertStdClassToArray($criticalExpirations);
        $recentlyExpired = $this->convertStdClassToArray($recentlyExpired);
        
        return view('admin.domain-expiration.index', compact(
            'domainRegisters', // Changed from clientDomains
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
        if (empty($items)) {
            return [];
        }
        
        return collect($items)->map(function($item) {
            $itemArray = (array) $item;
            
            // Convert date strings to Carbon objects
            $dateFields = ['expired_date', 'created_at', 'updated_at'];
            foreach ($dateFields as $field) {
                if (isset($itemArray[$field]) && $itemArray[$field]) {
                    $itemArray[$field] = new Carbon($itemArray[$field]);
                }
            }
            
            return (object) $itemArray;
        })->toArray();
    }
    
    /**
     * Send renewal reminder for domain register
     */
    public function sendReminder($domainRegisterId)
    {
        // Get domain register data using MySQL query
        $domainRegister = DB::select('SELECT * FROM domain_registers WHERE id = ?', [$domainRegisterId]);
        
        if (empty($domainRegister)) {
            return redirect()->route('admin.domain-expiration.index')
                ->with('error', 'Domain register not found');
        }
        
        $domainRegister = $domainRegister[0]; // Get first result
        
        // Convert date strings to Carbon objects
        $expiredDate = new Carbon($domainRegister->expired_date);
        
        // Create WhatsApp message for renewal reminder
        $message = "Halo Admin,\n\n" .
                  "Ini adalah pengingat bahwa domain register akan segera expired:\n" .
                  "� Nama Register: {$domainRegister->name}\n" .
                  "�📅 Tanggal Expired: {$expiredDate->format('d F Y')}\n" .
                  "⏰ {$expiredDate->diffInDays(now())} hari lagi\n" .
                  "🔗 Login: {$domainRegister->login_link}\n\n" .
                  "Silakan lakukan perpanjangan sebelum tanggal kadaluarsa.\n\n" .
                  "Terima kasih.";
        
        // Send to admin WhatsApp (you might want to configure this)
        $adminWhatsApp = '+6281234567890'; // Replace with actual admin WhatsApp
        $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $adminWhatsApp) . "?text=" . urlencode($message);
        
        return redirect()->away($whatsappUrl);
    }
    
    /**
     * Export domain registers report using MySQL
     */
    public function export(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        // Build MySQL query for export
        $baseQuery = 'SELECT dr.name, dr.login_link, dr.expired_date, dr.username, dr.notes, COUNT(cd.id) as client_count
                      FROM domain_registers dr 
                      LEFT JOIN client_data cd ON dr.id = cd.domain_register_id';
        
        $groupBy = ' GROUP BY dr.id';
        $whereClause = '';
        $bindings = [];
        
        switch ($filter) {
            case 'expired':
                $whereClause = ' WHERE dr.expired_date < ?';
                $bindings[] = now();
                break;
            case 'expiring':
                $whereClause = ' WHERE dr.expired_date >= ? AND dr.expired_date <= ?';
                $bindings[] = now();
                $bindings[] = now()->addMonths(3);
                break;
            case 'safe':
                $whereClause = ' WHERE dr.expired_date > ?';
                $bindings[] = now()->addMonths(3);
                break;
        }
        
        $fullQuery = $baseQuery . $whereClause . $groupBy . ' ORDER BY dr.expired_date ASC';
        $domains = DB::select($fullQuery, $bindings);
        
        $csvData = [];
        $csvData[] = ['Domain Register', 'Login Link', 'Expired Date', 'Days Until Expiry', 'Status', 'Username', 'Client Count', 'Notes'];
        
        foreach ($domains as $domain) {
            $expiredDate = new Carbon($domain->expired_date);
            $daysUntil = $expiredDate->diffInDays(now(), false);
            $status = $expiredDate->isPast() ? 'Expired' : 
                     ($expiredDate->lte(now()->addMonths(3)) ? 'Expiring Soon' : 'Safe');
            
            $csvData[] = [
                $domain->name,
                $domain->login_link,
                $expiredDate->format('Y-m-d'),
                $daysUntil,
                $status,
                $domain->username,
                $domain->client_count,
                $domain->notes
            ];
        }
        
        $filename = "domain_registers_report_" . now()->format('Y-m-d') . ".csv";
        
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
     * Load sample domain register data using SQL file
     */
    public function loadSampleData()
    {
        try {
            // Read SQL file
            $sqlFile = database_path('sql/sample_domain_registers.sql');
            if (!file_exists($sqlFile)) {
                return response()->json([
                    'success' => false,
                    'message' => 'SQL file not found: ' . $sqlFile
                ], 500);
            }
            
            $sql = file_get_contents($sqlFile);
            
            // Split SQL by semicolons and execute each statement
            $statements = array_filter(array_map('trim', explode(';', $sql)));
            
            DB::beginTransaction();
            
            foreach ($statements as $statement) {
                if (!empty($statement) && !preg_match('/^--/', $statement)) {
                    DB::statement($statement);
                }
            }
            
            DB::commit();
            
            // Get statistics after insertion
            $stats = [
                'total' => DB::select('SELECT COUNT(*) as count FROM domain_registers')[0]->count,
                'expired' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date < ?', [now()])[0]->count,
                'expiring' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date >= ? AND expired_date <= ?', [now(), now()->addMonths(3)])[0]->count,
                'safe' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date > ?', [now()->addMonths(3)])[0]->count,
            ];
            
            return response()->json([
                'success' => true,
                'message' => 'Sample domain register data loaded successfully!',
                'stats' => $stats
            ]);
            
        } catch (\Exception $e) {
            DB::rollBack();
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
            'total' => DB::select('SELECT COUNT(*) as count FROM domain_registers')[0]->count,
            'expired' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date < ?', [now()])[0]->count,
            'expiring' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date >= ? AND expired_date <= ?', [now(), now()->addMonths(3)])[0]->count,
            'safe' => DB::select('SELECT COUNT(*) as count FROM domain_registers WHERE expired_date > ?', [now()->addMonths(3)])[0]->count,
        ];
        
        return view('admin.domain-expiration.guide', compact('stats'));
    }
}
