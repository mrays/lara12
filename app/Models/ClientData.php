<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientData extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'whatsapp',
        'user_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get domains owned by this client (1 client can have many domains)
     */
    public function domains()
    {
        return $this->hasMany(Domain::class, 'client_id');
    }

    /**
     * Get the user associated with the client data
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if any service is expiring soon (within 60 days / 2 months)
     */
    public function isAnyServiceExpiringSoon()
    {
        return $this->domains()
            ->whereNotNull('expired_date')
            ->where('expired_date', '<=', now()->addDays(60))
            ->where('expired_date', '>', now())
            ->exists();
    }

    /**
     * Check if any service is expired
     */
    public function isAnyServiceExpired()
    {
        return $this->domains()
            ->whereNotNull('expired_date')
            ->where('expired_date', '<', now())
            ->exists();
    }

    /**
     * Get the earliest expiration date from all domains
     */
    public function getEarliestExpirationAttribute()
    {
        $domain = $this->domains()
            ->whereNotNull('expired_date')
            ->orderBy('expired_date')
            ->first();
            
        return $domain ? $domain->expired_date : null;
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        if ($this->isAnyServiceExpired()) {
            return 'bg-danger';
        } elseif ($this->isAnyServiceExpiringSoon()) {
            return 'bg-warning';
        }
        
        return match($this->status) {
            'active' => 'bg-success',
            'expired' => 'bg-danger',
            'warning' => 'bg-warning',
            default => 'bg-secondary'
        };
    }

    /**
     * Get formatted whatsapp number
     */
    public function getFormattedWhatsappAttribute()
    {
        return preg_replace('/[^0-9+]/', '', $this->whatsapp);
    }

    /**
     * Get whatsapp link
     */
    public function getWhatsappLinkAttribute()
    {
        return 'https://wa.me/' . $this->formatted_whatsapp;
    }
}
