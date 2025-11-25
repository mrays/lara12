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
        'website_service_expired',
        'domain_expired',
        'hosting_expired',
        'server_id',
        'domain_register_id',
        'user_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'website_service_expired' => 'date',
        'domain_expired' => 'date',
        'hosting_expired' => 'date',
    ];

    /**
     * Get the server that owns the client data
     */
    public function server()
    {
        return $this->belongsTo(Server::class);
    }

    /**
     * Get the domain register that owns the client data
     */
    public function domainRegister()
    {
        return $this->belongsTo(DomainRegister::class);
    }

    /**
     * Get the user associated with the client data
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if any service is expiring soon (within 30 days)
     */
    public function isAnyServiceExpiringSoon()
    {
        $dates = [
            $this->website_service_expired,
            $this->domain_expired,
            $this->hosting_expired
        ];

        foreach ($dates as $date) {
            if ($date && $date->lte(now()->addDays(30))) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if any service is expired
     */
    public function isAnyServiceExpired()
    {
        $dates = [
            $this->website_service_expired,
            $this->domain_expired,
            $this->hosting_expired
        ];

        foreach ($dates as $date) {
            if ($date && $date->isPast()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the earliest expiration date
     */
    public function getEarliestExpirationAttribute()
    {
        $dates = array_filter([
            $this->website_service_expired,
            $this->domain_expired,
            $this->hosting_expired
        ]);

        return empty($dates) ? null : min($dates);
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
