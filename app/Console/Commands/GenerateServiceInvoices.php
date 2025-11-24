<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GenerateServiceInvoices extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'invoices:generate-service-renewals {--dry-run : Show what would be generated without actually creating invoices}';

    /**
     * The console command description.
     */
    protected $description = 'Generate invoices for services that are approaching expiry based on billing cycle';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        $this->info('🔍 Checking for services that need invoice generation...');
        
        // Get active services that need invoice generation
        $services = $this->getServicesNeedingInvoices();
        
        if ($services->isEmpty()) {
            $this->info('✅ No services need invoice generation today.');
            return 0;
        }
        
        $this->info("📋 Found {$services->count()} service(s) that need invoice generation:");
        
        $generatedCount = 0;
        
        foreach ($services as $service) {
            $this->line("   • Service #{$service->id} - {$service->product} (Client: {$service->client_name})");
            $this->line("     Billing: {$service->billing_cycle} | Expires: {$service->next_due_date}");
            
            if (!$isDryRun) {
                try {
                    $invoice = $this->generateInvoiceForService($service);
                    $this->info("     ✅ Invoice #{$invoice->number} generated successfully");
                    $generatedCount++;
                } catch (\Exception $e) {
                    $this->error("     ❌ Failed to generate invoice: {$e->getMessage()}");
                }
            } else {
                $this->line("     🔍 [DRY RUN] Would generate invoice for this service");
                $generatedCount++;
            }
            
            $this->line('');
        }
        
        if ($isDryRun) {
            $this->warn("🔍 DRY RUN: {$generatedCount} invoice(s) would be generated");
        } else {
            $this->info("✅ Successfully generated {$generatedCount} invoice(s)");
        }
        
        return 0;
    }
    
    /**
     * Get services that need invoice generation today
     */
    private function getServicesNeedingInvoices()
    {
        $today = Carbon::today();
        
        return DB::table('services')
            ->leftJoin('users', 'services.client_id', '=', 'users.id')
            ->select(
                'services.*',
                'users.name as client_name',
                'users.email as client_email'
            )
            ->where('services.status', 'Active')
            ->whereNotNull('services.next_due_date')
            ->get()
            ->filter(function ($service) use ($today) {
                // Check if invoice should be generated today
                $generationDate = calculate_invoice_generation_date($service->next_due_date, $service->billing_cycle);
                
                // Also check if invoice hasn't been generated yet for this renewal period
                $existingInvoice = DB::table('invoices')
                    ->where('service_id', $service->id)
                    ->where('due_date', $service->next_due_date)
                    ->exists();
                
                return $today->isSameDay($generationDate) && !$existingInvoice;
            });
    }
    
    /**
     * Generate invoice for a specific service
     */
    private function generateInvoiceForService($service)
    {
        $invoiceNumber = $this->generateInvoiceNumber();
        $issueDate = Carbon::today();
        $dueDate = Carbon::parse($service->next_due_date);
        
        // Calculate next renewal date
        $nextRenewalDate = $this->calculateNextRenewalDate($dueDate, $service->billing_cycle);
        
        // Create invoice
        $invoiceId = DB::table('invoices')->insertGetId([
            'number' => $invoiceNumber,
            'title' => "Service Renewal - {$service->product}",
            'client_id' => $service->client_id,
            'service_id' => $service->id,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'amount' => $service->price,
            'tax_amount' => 0,
            'total_amount' => $service->price,
            'status' => 'Unpaid',
            'notes' => "Automatic renewal invoice for {$service->product} service. Billing cycle: {$service->billing_cycle}",
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
        // Update service next due date
        DB::table('services')
            ->where('id', $service->id)
            ->update([
                'next_due_date' => $nextRenewalDate,
                'updated_at' => now(),
            ]);
        
        // Return the created invoice
        return (object) [
            'id' => $invoiceId,
            'number' => $invoiceNumber,
            'amount' => $service->price,
        ];
    }
    
    /**
     * Generate unique invoice number
     */
    private function generateInvoiceNumber()
    {
        $prefix = 'INV';
        $date = Carbon::today()->format('Ymd');
        
        // Get last invoice number for today
        $lastInvoice = DB::table('invoices')
            ->where('number', 'like', "{$prefix}-{$date}-%")
            ->orderBy('number', 'desc')
            ->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->number, -3);
            $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '001';
        }
        
        return "{$prefix}-{$date}-{$newNumber}";
    }
    
    /**
     * Calculate next renewal date based on billing cycle
     */
    private function calculateNextRenewalDate($currentDueDate, $billingCycle)
    {
        $date = Carbon::parse($currentDueDate);
        
        switch (strtolower($billingCycle)) {
            case 'yearly':
            case 'annual':
                return $date->addYear();
                
            case 'monthly':
                return $date->addMonth();
                
            case 'quarterly':
                return $date->addMonths(3);
                
            case 'semi-annual':
            case 'biannual':
                return $date->addMonths(6);
                
            default:
                return $date->addMonth();
        }
    }
}
