<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Service extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'package_id',
        'product',
        'domain',
        'price',
        'billing_cycle',
        'registration_date',
        'due_date',
        'ip',
        'status',
        'notes',
    ];

    protected $casts = [
        'registration_date' => 'date',
        'due_date' => 'date',
        'price' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public function package()
    {
        return $this->belongsTo(ServicePackage::class, 'package_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Accessors
    public function getFormattedPriceAttribute()
    {
        return '$' . number_format($this->price, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Active' => 'success',
            'Suspended' => 'warning',
            'Terminated' => 'danger',
            'Pending' => 'info',
            default => 'secondary'
        };
    }

    public function getDaysUntilDueAttribute()
    {
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    public function getIsExpiringSoonAttribute()
    {
        return $this->days_until_due <= 30 && $this->days_until_due > 0;
    }

    public function getIsExpiredAttribute()
    {
        return $this->due_date < Carbon::now() && $this->status === 'Active';
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeExpiringSoon($query, $days = 30)
    {
        return $query->where('due_date', '<=', Carbon::now()->addDays($days))
                    ->where('due_date', '>', Carbon::now())
                    ->where('status', 'Active');
    }

    public function scopeExpired($query)
    {
        return $query->where('due_date', '<', Carbon::now())
                    ->where('status', 'Active');
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Methods
    public function getTranslatedBillingCycleAttribute()
    {
        $cycleMap = [
            '1D' => '1 Hari',
            '1W' => '1 Minggu',
            '2W' => '2 Minggu',
            '3W' => '3 Minggu',
            '1M' => '1 Bulan',
            '2M' => '2 Bulan',
            '3M' => '3 Bulan',
            '6M' => '6 Bulan',
            '1Y' => '1 Tahun',
            '2Y' => '2 Tahun',
            '3Y' => '3 Tahun',
            'Monthly' => 'Bulanan',
            'Quarterly' => 'Triwulan',
            'Semi-Annually' => 'Semester',
            'Annually' => 'Tahunan',
            'Biennially' => '2 Tahunan',
        ];

        return $cycleMap[$this->billing_cycle] ?? $this->billing_cycle;
    }

    public function generateRenewalInvoice()
    {
        $invoice = Invoice::create([
            'client_id' => $this->client_id,
            'service_id' => $this->id,
            'number' => Invoice::generateInvoiceNumber(),
            'title' => "Service Renewal - {$this->product}",
            'description' => "Renewal for {$this->product}" . ($this->domain ? " ({$this->domain})" : ''),
            'subtotal' => $this->price,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $this->price,
            'status' => 'Draft',
            'issue_date' => Carbon::now(),
            'due_date' => Carbon::now()->addDays(30),
        ]);

        // Create invoice item
        $invoice->items()->create([
            'description' => "{$this->product} - {$this->billing_cycle} Renewal",
            'quantity' => 1,
            'unit_price' => $this->price,
            'total_price' => $this->price,
        ]);

        return $invoice;
    }
}
