<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Client extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'company',
        'status',
        'user_id'
    ];

    // Relationships
    public function services()
    {
        return $this->hasMany(Service::class);
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class);
    }

    // Accessors
    public function getActiveServicesCountAttribute()
    {
        return $this->services()->where('status', 'Active')->count();
    }

    public function getUnpaidInvoicesCountAttribute()
    {
        return $this->invoices()->unpaid()->count();
    }

    public function getTotalUnpaidAmountAttribute()
    {
        return $this->invoices()->unpaid()->sum('total_amount');
    }

    public function getFormattedTotalUnpaidAttribute()
    {
        return '$' . number_format($this->total_unpaid_amount, 2);
    }

    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'Active' => 'success',
            'Inactive' => 'warning',
            'Suspended' => 'danger',
            default => 'secondary'
        };
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeWithUnpaidInvoices($query)
    {
        return $query->whereHas('invoices', function($q) {
            $q->unpaid();
        });
    }
}
