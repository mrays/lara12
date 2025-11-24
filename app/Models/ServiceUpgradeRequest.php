<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ServiceUpgradeRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'client_id',
        'current_plan',
        'requested_plan',
        'current_price',
        'requested_price',
        'upgrade_reason',
        'additional_notes',
        'status',
        'admin_notes',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'current_price' => 'decimal:2',
        'requested_price' => 'decimal:2',
        'processed_at' => 'datetime',
    ];

    /**
     * Get the service that this upgrade request belongs to
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * Get the client who made this upgrade request
     */
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    /**
     * Get the admin who processed this request
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Scope for pending requests
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope for approved requests
     */
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    /**
     * Scope for rejected requests
     */
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'pending' => 'bg-warning',
            'approved' => 'bg-success',
            'rejected' => 'bg-danger',
            'processing' => 'bg-info',
            default => 'bg-secondary'
        };
    }

    /**
     * Get status text
     */
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending' => 'Menunggu Persetujuan',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            'processing' => 'Sedang Diproses',
            default => 'Unknown'
        };
    }

    /**
     * Get price difference
     */
    public function getPriceDifferenceAttribute()
    {
        return $this->requested_price - $this->current_price;
    }

    /**
     * Get formatted price difference
     */
    public function getFormattedPriceDifferenceAttribute()
    {
        $diff = $this->price_difference;
        $sign = $diff >= 0 ? '+' : '';
        return $sign . 'Rp ' . number_format(abs($diff), 0, ',', '.');
    }

    /**
     * Check if request can be processed
     */
    public function canBeProcessed()
    {
        return $this->status === 'pending';
    }

    /**
     * Mark as approved
     */
    public function approve($adminId, $notes = null)
    {
        $this->update([
            'status' => 'approved',
            'admin_notes' => $notes,
            'processed_by' => $adminId,
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark as rejected
     */
    public function reject($adminId, $notes = null)
    {
        $this->update([
            'status' => 'rejected',
            'admin_notes' => $notes,
            'processed_by' => $adminId,
            'processed_at' => now(),
        ]);
    }
}
