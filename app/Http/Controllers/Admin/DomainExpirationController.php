<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClientData;
use App\Models\DomainRegister;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DomainExpirationController extends Controller
{
    /**
     * Display client domain expiration monitoring dashboard
     */
    public function index(Request $request)
    {
        $filter = $request->get('filter', 'all'); // all, expired, expiring, safe
        
        $query = ClientData::with('domainRegister');
        
        switch ($filter) {
            case 'expired':
                $query->where('domain_expired', '<', now());
                break;
            case 'expiring':
                $query->where('domain_expired', '>=', now())
                      ->where('domain_expired', '<=', now()->addMonths(3));
                break;
            case 'safe':
                $query->where('domain_expired', '>', now()->addMonths(3));
                break;
            // 'all' shows all client domains
        }
        
        $clientDomains = $query->orderBy('domain_expired', 'asc')->get();
        
        // Calculate statistics
        $stats = [
            'total' => ClientData::count(),
            'expired' => ClientData::where('domain_expired', '<', now())->count(),
            'expiring' => ClientData::where('domain_expired', '>=', now())
                                ->where('domain_expired', '<=', now()->addMonths(3))->count(),
            'safe' => ClientData::where('domain_expired', '>', now()->addMonths(3))->count(),
        ];
        
        // Get upcoming expirations (next 30 days)
        $upcomingExpirations = ClientData::with('domainRegister')
            ->where('domain_expired', '>=', now())
            ->where('domain_expired', '<=', now()->addDays(30))
            ->orderBy('domain_expired', 'asc')
            ->limit(10)
            ->get();
        
        // Critical expirations (next 7 days)
        $criticalExpirations = ClientData::with('domainRegister')
            ->where('domain_expired', '>=', now())
            ->where('domain_expired', '<=', now()->addDays(7))
            ->orderBy('domain_expired', 'asc')
            ->get();
        
        // Recently expired (last 30 days)
        $recentlyExpired = ClientData::with('domainRegister')
            ->where('domain_expired', '<', now())
            ->where('domain_expired', '>=', now()->subDays(30))
            ->orderBy('domain_expired', 'desc')
            ->get();
        
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
     * Export client domain expiration report
     */
    public function export(Request $request)
    {
        $filter = $request->get('filter', 'all');
        
        $query = ClientData::with('domainRegister');
        
        switch ($filter) {
            case 'expired':
                $query->where('domain_expired', '<', now());
                break;
            case 'expiring':
                $query->where('domain_expired', '>=', now())
                      ->where('domain_expired', '<=', now()->addMonths(3));
                break;
            case 'safe':
                $query->where('domain_expired', '>', now()->addMonths(3));
                break;
        }
        
        $domains = $query->orderBy('domain_expired', 'asc')->get();
        
        $csvData = [];
        $csvData[] = ['Client Name', 'Domain Register', 'Domain Expired', 'Days Until Expiry', 'Status', 'WhatsApp', 'Website Expired', 'Hosting Expired'];
        
        foreach ($domains as $client) {
            $daysUntil = $client->domain_expired->diffInDays(now(), false);
            $status = $client->domain_expired->isPast() ? 'Expired' : 
                     ($client->domain_expired->lte(now()->addMonths(3)) ? 'Expiring Soon' : 'Safe');
            
            $csvData[] = [
                $client->name,
                $client->domainRegister->name ?? 'N/A',
                $client->domain_expired->format('Y-m-d'),
                $daysUntil,
                $status,
                $client->whatsapp,
                $client->website_service_expired->format('Y-m-d'),
                $client->hosting_expired->format('Y-m-d')
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
}
