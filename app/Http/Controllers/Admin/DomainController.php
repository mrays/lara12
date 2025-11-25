<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Domain;
use App\Models\ClientData;
use App\Models\Server;
use App\Models\DomainRegister;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DomainController extends Controller
{
    /**
     * Display all domains listing
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, active, expired, expiring, safe, pending, suspended
        
        // Build base query with MySQL for better performance
        $baseQuery = 'SELECT d.*, cd.name as client_name, s.name as server_name, dr.name as domain_register_name 
                     FROM domains d 
                     LEFT JOIN client_data cd ON d.client_id = cd.id
                     LEFT JOIN servers s ON d.server_id = s.id
                     LEFT JOIN domain_registers dr ON d.domain_register_id = dr.id';
        
        $whereClause = '';
        $bindings = [];
        
        switch ($filter) {
            case 'active':
                $whereClause = ' WHERE d.status = ?';
                $bindings[] = Domain::STATUS_ACTIVE;
                break;
            case 'expired':
                $whereClause = ' WHERE d.expired_date < ?';
                $bindings[] = now();
                break;
            case 'expiring':
                $whereClause = ' WHERE d.expired_date >= ? AND d.expired_date <= ?';
                $bindings[] = now();
                $bindings[] = now()->addDays(30);
                break;
            case 'critical':
                $whereClause = ' WHERE d.expired_date >= ? AND d.expired_date <= ?';
                $bindings[] = now();
                $bindings[] = now()->addDays(7);
                break;
            case 'safe':
                $whereClause = ' WHERE d.expired_date > ?';
                $bindings[] = now()->addDays(30);
                break;
            case 'pending':
                $whereClause = ' WHERE d.status = ?';
                $bindings[] = Domain::STATUS_PENDING;
                break;
            case 'suspended':
                $whereClause = ' WHERE d.status = ?';
                $bindings[] = Domain::STATUS_SUSPENDED;
                break;
            // 'all' shows all domains
        }
        
        $orderBy = ' ORDER BY d.expired_date ASC, d.domain_name ASC';
        $fullQuery = $baseQuery . $whereClause . $orderBy;
        $domains = DB::select($fullQuery, $bindings);
        
        // Convert stdClass objects to arrays with proper date objects
        $domains = $this->convertStdClassToArray($domains);
        
        // Calculate statistics using MySQL queries
        $stats = [
            'total' => DB::select('SELECT COUNT(*) as count FROM domains')[0]->count,
            'active' => DB::select('SELECT COUNT(*) as count FROM domains WHERE status = ?', [Domain::STATUS_ACTIVE])[0]->count,
            'expired' => DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date < ?', [now()])[0]->count,
            'expiring' => DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date >= ? AND expired_date <= ?', [now(), now()->addDays(30)])[0]->count,
            'critical' => DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date >= ? AND expired_date <= ?', [now(), now()->addDays(7)])[0]->count,
            'safe' => DB::select('SELECT COUNT(*) as count FROM domains WHERE expired_date > ?', [now()->addDays(30)])[0]->count,
            'pending' => DB::select('SELECT COUNT(*) as count FROM domains WHERE status = ?', [Domain::STATUS_PENDING])[0]->count,
            'suspended' => DB::select('SELECT COUNT(*) as count FROM domains WHERE status = ?', [Domain::STATUS_SUSPENDED])[0]->count,
        ];
        
        // Get critical expirations (next 7 days) using MySQL
        $criticalQuery = 'SELECT d.*, cd.name as client_name, s.name as server_name, dr.name as domain_register_name 
                         FROM domains d 
                         LEFT JOIN client_data cd ON d.client_id = cd.id
                         LEFT JOIN servers s ON d.server_id = s.id
                         LEFT JOIN domain_registers dr ON d.domain_register_id = dr.id
                         WHERE d.expired_date >= ? AND d.expired_date <= ? 
                         ORDER BY d.expired_date ASC';
        $criticalExpirations = DB::select($criticalQuery, [now(), now()->addDays(7)]);
        $criticalExpirations = $this->convertStdClassToArray($criticalExpirations);
        
        // Get upcoming expirations (next 30 days) using MySQL
        $upcomingQuery = 'SELECT d.*, cd.name as client_name, s.name as server_name, dr.name as domain_register_name 
                          FROM domains d 
                          LEFT JOIN client_data cd ON d.client_id = cd.id
                          LEFT JOIN servers s ON d.server_id = s.id
                          LEFT JOIN domain_registers dr ON d.domain_register_id = dr.id
                          WHERE d.expired_date >= ? AND d.expired_date <= ? 
                          ORDER BY d.expired_date ASC LIMIT 10';
        $upcomingExpirations = DB::select($upcomingQuery, [now(), now()->addDays(30)]);
        $upcomingExpirations = $this->convertStdClassToArray($upcomingExpirations);
        
        return view('admin.domains.index', compact(
            'domains',
            'stats', 
            'filter',
            'criticalExpirations',
            'upcomingExpirations'
        ));
    }
    
    /**
     * Show the form for creating a new domain.
     */
    public function create()
    {
        $clients = ClientData::orderBy('name')->get();
        $servers = Server::orderBy('name')->get();
        $domainRegisters = DomainRegister::orderBy('name')->get();
        $statusOptions = Domain::getStatusOptions();
        
        return view('admin.domains.create', compact(
            'clients',
            'servers', 
            'domainRegisters',
            'statusOptions'
        ));
    }
    
    /**
     * Store a newly created domain.
     */
    public function store(Request $request)
    {
        $request->validate([
            'domain_name' => 'required|string|max:255|unique:domains,domain_name',
            'client_id' => 'nullable|exists:client_data,id',
            'server_id' => 'nullable|exists:servers,id',
            'domain_register_id' => 'nullable|exists:domain_registers,id',
            'expired_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:' . implode(',', array_keys(Domain::getStatusOptions())),
            'notes' => 'nullable|string|max:1000'
        ]);
        
        // Use MySQL insert for better performance
        $insertData = [
            'domain_name' => $request->domain_name,
            'client_id' => $request->client_id ?: null,
            'server_id' => $request->server_id ?: null,
            'domain_register_id' => $request->domain_register_id ?: null,
            'expired_date' => $request->expired_date ?: null,
            'status' => $request->status,
            'notes' => $request->notes ?: null,
            'created_at' => now(),
            'updated_at' => now()
        ];
        
        $domainId = DB::table('domains')->insertGetId($insertData);
        
        return redirect()
            ->route('admin.domains.index')
            ->with('success', 'Domain created successfully!');
    }
    
    /**
     * Show the form for editing the specified domain.
     */
    public function edit($domainId)
    {
        // Get domain using MySQL query
        $domainQuery = 'SELECT d.*, cd.name as client_name, s.name as server_name, dr.name as domain_register_name 
                       FROM domains d 
                       LEFT JOIN client_data cd ON d.client_id = cd.id
                       LEFT JOIN servers s ON d.server_id = s.id
                       LEFT JOIN domain_registers dr ON d.domain_register_id = dr.id
                       WHERE d.id = ?';
        $domainResults = DB::select($domainQuery, [$domainId]);
        
        if (empty($domainResults)) {
            return redirect()
                ->route('admin.domains.index')
                ->with('error', 'Domain not found');
        }
        
        $domain = $domainResults[0];
        
        // Convert date to proper format if needed
        if ($domain->expired_date) {
            $domain->expired_date = new Carbon($domain->expired_date);
        }
        
        $clients = ClientData::orderBy('name')->get();
        $servers = Server::orderBy('name')->get();
        $domainRegisters = DomainRegister::orderBy('name')->get();
        $statusOptions = Domain::getStatusOptions();
        
        return view('admin.domains.edit', compact(
            'domain',
            'clients',
            'servers', 
            'domainRegisters',
            'statusOptions'
        ));
    }
    
    /**
     * Update the specified domain.
     */
    public function update(Request $request, $domainId)
    {
        $request->validate([
            'domain_name' => 'required|string|max:255|unique:domains,domain_name,' . $domainId,
            'client_id' => 'nullable|exists:client_data,id',
            'server_id' => 'nullable|exists:servers,id',
            'domain_register_id' => 'nullable|exists:domain_registers,id',
            'expired_date' => 'nullable|date|after_or_equal:today',
            'status' => 'required|in:' . implode(',', array_keys(Domain::getStatusOptions())),
            'notes' => 'nullable|string|max:1000'
        ]);
        
        // Check if domain exists
        $exists = DB::select('SELECT id FROM domains WHERE id = ?', [$domainId]);
        if (empty($exists)) {
            return redirect()
                ->route('admin.domains.index')
                ->with('error', 'Domain not found');
        }
        
        // Use MySQL update for better performance
        $updateData = [
            'domain_name' => $request->domain_name,
            'client_id' => $request->client_id ?: null,
            'server_id' => $request->server_id ?: null,
            'domain_register_id' => $request->domain_register_id ?: null,
            'expired_date' => $request->expired_date ?: null,
            'status' => $request->status,
            'notes' => $request->notes ?: null,
            'updated_at' => now()
        ];
        
        DB::table('domains')->where('id', $domainId)->update($updateData);
        
        return redirect()
            ->route('admin.domains.index')
            ->with('success', 'Domain updated successfully!');
    }
    
    /**
     * Remove the specified domain.
     */
    public function destroy($domainId)
    {
        // Check if domain exists
        $exists = DB::select('SELECT id FROM domains WHERE id = ?', [$domainId]);
        if (empty($exists)) {
            return redirect()
                ->route('admin.domains.index')
                ->with('error', 'Domain not found');
        }
        
        DB::table('domains')->where('id', $domainId)->delete();
        
        return redirect()
            ->route('admin.domains.index')
            ->with('success', 'Domain deleted successfully!');
    }
    
    /**
     * Send renewal reminder for domain
     */
    public function sendReminder($domainId)
    {
        // Get domain data using MySQL query
        $domainQuery = 'SELECT d.*, cd.name as client_name, cd.whatsapp as client_whatsapp, s.name as server_name, dr.name as domain_register_name, dr.login_link 
                       FROM domains d 
                       LEFT JOIN client_data cd ON d.client_id = cd.id
                       LEFT JOIN servers s ON d.server_id = s.id
                       LEFT JOIN domain_registers dr ON d.domain_register_id = dr.id
                       WHERE d.id = ?';
        $domainResults = DB::select($domainQuery, [$domainId]);
        
        if (empty($domainResults)) {
            return redirect()->route('admin.domains.index')
                ->with('error', 'Domain not found');
        }
        
        $domain = $domainResults[0];
        
        // Convert date strings to Carbon objects
        $expiredDate = new Carbon($domain->expired_date);
        
        // Create WhatsApp message for renewal reminder
        $message = "Halo" . ($domain->client_name ? " {$domain->client_name}," : ",") . "\n\n" .
                  "Ini adalah pengingat bahwa domain Anda akan segera expired:\n" .
                  "🌐 Domain: {$domain->domain_name}\n" .
                  "📅 Tanggal Expired: {$expiredDate->format('d F Y')}\n" .
                  "⏰ {$expiredDate->diffInDays(now())} hari lagi\n";
        
        if ($domain->domain_register_name) {
            $message .= "📛 Register: {$domain->domain_register_name}\n";
        }
        
        if ($domain->server_name) {
            $message .= "🖥️ Server: {$domain->server_name}\n";
        }
        
        $message .= "\nSilakan lakukan perpanjangan sebelum tanggal kadaluarsa.\n\n" .
                   "Terima kasih.";
        
        // Send to client WhatsApp if available, otherwise admin
        $whatsappNumber = $domain->client_whatsapp ?: '+6281234567890'; // Replace with actual admin WhatsApp
        $whatsappUrl = "https://wa.me/" . preg_replace('/[^0-9]/', '', $whatsappNumber) . "?text=" . urlencode($message);
        
        return redirect()->away($whatsappUrl);
    }
    
    /**
     * Export domains report using MySQL
     */
    public function export(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        // Build MySQL query for export
        $baseQuery = 'SELECT d.domain_name, cd.name as client_name, s.name as server_name, dr.name as domain_register_name, 
                     d.expired_date, d.status, d.notes
                     FROM domains d 
                     LEFT JOIN client_data cd ON d.client_id = cd.id
                     LEFT JOIN servers s ON d.server_id = s.id
                     LEFT JOIN domain_registers dr ON d.domain_register_id = dr.id';
        
        $whereClause = '';
        $bindings = [];
        
        switch ($filter) {
            case 'active':
                $whereClause = ' WHERE d.status = ?';
                $bindings[] = Domain::STATUS_ACTIVE;
                break;
            case 'expired':
                $whereClause = ' WHERE d.expired_date < ?';
                $bindings[] = now();
                break;
            case 'expiring':
                $whereClause = ' WHERE d.expired_date >= ? AND d.expired_date <= ?';
                $bindings[] = now();
                $bindings[] = now()->addDays(30);
                break;
            case 'critical':
                $whereClause = ' WHERE d.expired_date >= ? AND d.expired_date <= ?';
                $bindings[] = now();
                $bindings[] = now()->addDays(7);
                break;
            case 'safe':
                $whereClause = ' WHERE d.expired_date > ?';
                $bindings[] = now()->addDays(30);
                break;
            case 'pending':
                $whereClause = ' WHERE d.status = ?';
                $bindings[] = Domain::STATUS_PENDING;
                break;
            case 'suspended':
                $whereClause = ' WHERE d.status = ?';
                $bindings[] = Domain::STATUS_SUSPENDED;
                break;
        }
        
        $fullQuery = $baseQuery . $whereClause . ' ORDER BY d.expired_date ASC, d.domain_name ASC';
        $domains = DB::select($fullQuery, $bindings);
        
        $csvData = [];
        $csvData[] = ['Domain Name', 'Client', 'Server', 'Domain Register', 'Expired Date', 'Days Until Expiry', 'Status', 'Notes'];
        
        foreach ($domains as $domain) {
            $daysUntil = $domain->expired_date ? (new Carbon($domain->expired_date))->diffInDays(now(), false) : 'Not Set';
            $csvData[] = [
                $domain->domain_name,
                $domain->client_name ?? 'N/A',
                $domain->server_name ?? 'N/A',
                $domain->domain_register_name ?? 'N/A',
                $domain->expired_date ?? 'Not Set',
                $daysUntil,
                ucfirst($domain->status),
                $domain->notes ?? ''
            ];
        }
        
        $filename = "domains_report_" . now()->format('Y-m-d') . ".csv";
        
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
            if (isset($itemArray['expired_date']) && $itemArray['expired_date']) {
                $itemArray['expired_date'] = new Carbon($itemArray['expired_date']);
            }
            
            if (isset($itemArray['created_at']) && $itemArray['created_at']) {
                $itemArray['created_at'] = new Carbon($itemArray['created_at']);
            }
            
            if (isset($itemArray['updated_at']) && $itemArray['updated_at']) {
                $itemArray['updated_at'] = new Carbon($itemArray['updated_at']);
            }
            
            return (object) $itemArray;
        })->toArray();
    }
}
