<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'client_id',
        'service_id', 
        'number',
        'title',
        'description',
        'subtotal',
        'amount',
        'tax_rate',
        'tax_amount', 
        'discount_amount',
        'total_amount',
        'status',
        'issue_date',
        'due_date',
        'paid_date',
        'payment_method',
        'payment_reference',
        'notes',
        'duitku_merchant_code',
        'duitku_reference',
        'duitku_payment_url',
    ];

    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_date' => 'date',
        'subtotal' => 'decimal:2',
        'amount' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    // Accessors & Mutators
    public function getFormattedTotalAttribute()
    {
        return '$' . number_format($this->total_amount, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Draft' => 'secondary',
            'Sent' => 'info',
            'Paid' => 'success',
            'Unpaid' => 'warning',
            'Overdue' => 'danger',
            'Cancelled' => 'dark',
            'gagal' => 'danger',
            default => 'secondary'
        };
    }

    public function getIsOverdueAttribute()
    {
        return $this->status !== 'Paid' && $this->due_date < Carbon::now();
    }

    public function getDaysUntilDueAttribute()
    {
        if ($this->status === 'Paid') return null;
        return Carbon::now()->diffInDays($this->due_date, false);
    }

    // Scopes
    public function scopePaid($query)
    {
        return $query->where('status', 'Paid');
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['Draft', 'Sent', 'Overdue']);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'Paid')
                    ->where('due_date', '<', Carbon::now());
    }

    public function scopeForClient($query, $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    // Static methods
    public static function generateInvoiceNumber()
    {
        $year = date('Y');
        $lastInvoice = self::where('number', 'like', "INV-{$year}-%")->orderBy('id', 'desc')->first();
        
        if ($lastInvoice) {
            $lastNumber = (int) substr($lastInvoice->number, -3);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }
        
        return "INV-{$year}-" . str_pad($newNumber, 3, '0', STR_PAD_LEFT);
    }

    public function markAsPaid($paymentMethod = null, $paymentReference = null)
    {
        $this->update([
            'status' => 'Paid',
            'paid_date' => Carbon::now(),
            'payment_method' => $paymentMethod,
            'payment_reference' => $paymentReference,
        ]);
    }

    // Payment related methods
    public function canBePaid()
    {
        return in_array($this->status, ['Draft', 'Sent', 'Overdue']);
    }

    public function isPaid()
    {
        return $this->status === 'Paid';
    }

    public function isOverdue()
    {
        return $this->status === 'Overdue' || 
               ($this->status === 'Sent' && $this->due_date < Carbon::now());
    }

    public function hasPendingPayment()
    {
        return !empty($this->duitku_merchant_code) && !$this->isPaid();
    }

    public function getPaymentUrl()
    {
        return $this->duitku_payment_url;
    }

    public function getPaymentStatusBadgeClass()
    {
        switch ($this->status) {
            case 'Paid':
                return 'badge bg-success';
            case 'Sent':
                return 'badge bg-primary';
            case 'Overdue':
                return 'badge bg-danger';
            case 'Draft':
                return 'badge bg-secondary';
            case 'Cancelled':
                return 'badge bg-dark';
            default:
                return 'badge bg-light';
        }
    }

    public function getFormattedAmount()
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    public function getDaysOverdue()
    {
        if ($this->status !== 'Overdue' && !$this->isOverdue()) {
            return 0;
        }
        
        return Carbon::now()->diffInDays($this->due_date);
    }
}
