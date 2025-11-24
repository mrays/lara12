<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CheckServiceRenewals extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'services:check-renewals {--days=30 : Show services expiring within X days}';

    /**
     * The console command description.
     */
    protected $description = 'Check which services are approaching renewal and when invoices should be generated';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = (int) $this->option('days');
        $today = Carbon::today();
        $futureDate = $today->copy()->addDays($days);
        
        $this->info("🔍 Checking services expiring between {$today->format('Y-m-d')} and {$futureDate->format('Y-m-d')}");
        $this->line('');
        
        // Get services expiring within the specified period
        $services = DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->select(
                'services.*',
                'users.name as client_name',
                'users.email as client_email'
            )
            ->where('services.status', 'Active')
            ->whereNotNull('services.next_due_date')
            ->whereBetween('services.next_due_date', [$today, $futureDate])
            ->orderBy('services.next_due_date')
            ->get();
        
        if ($services->isEmpty()) {
            $this->info('✅ No services expiring in the specified period.');
            return 0;
        }
        
        $this->info("📋 Found {$services->count()} service(s) expiring soon:");
        $this->line('');
        
        $headers = ['Service ID', 'Product', 'Client', 'Billing Cycle', 'Expires', 'Invoice Date', 'Days Until Invoice', 'Status'];
        $rows = [];
        
        foreach ($services as $service) {
            $expiryDate = Carbon::parse($service->next_due_date);
            $invoiceDate = calculate_invoice_generation_date($service->next_due_date, $service->billing_cycle);
            $daysUntilInvoice = $today->diffInDays($invoiceDate, false);
            
            // Check if invoice already exists
            $existingInvoice = DB::table('invoices')
                ->where('service_id', $service->id)
                ->where('due_date', $service->next_due_date)
                ->exists();
            
            $status = '';
            if ($existingInvoice) {
                $status = '✅ Invoice exists';
            } elseif ($daysUntilInvoice <= 0) {
                $status = '🔥 Generate today!';
            } elseif ($daysUntilInvoice <= 7) {
                $status = '⚠️ Generate soon';
            } else {
                $status = '⏳ Waiting';
            }
            
            $rows[] = [
                $service->id,
                $service->product,
                $service->client_name,
                ucfirst($service->billing_cycle),
                $expiryDate->format('Y-m-d'),
                $invoiceDate->format('Y-m-d'),
                $daysUntilInvoice > 0 ? $daysUntilInvoice : 0,
                $status
            ];
        }
        
        $this->table($headers, $rows);
        
        // Show summary
        $needsInvoiceToday = collect($rows)->where(6, '<=', 0)->count();
        $needsInvoiceSoon = collect($rows)->where(6, '>', 0)->where(6, '<=', 7)->count();
        
        $this->line('');
        $this->info("📊 Summary:");
        $this->line("   • Services needing invoice today: {$needsInvoiceToday}");
        $this->line("   • Services needing invoice within 7 days: {$needsInvoiceSoon}");
        
        if ($needsInvoiceToday > 0) {
            $this->line('');
            $this->warn("💡 Run 'php artisan invoices:generate-service-renewals --dry-run' to see what would be generated");
            $this->warn("💡 Run 'php artisan invoices:generate-service-renewals' to actually generate invoices");
        }
        
        return 0;
    }
}
