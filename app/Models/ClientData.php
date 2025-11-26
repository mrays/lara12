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
        'domain_id',
        'server_id',
        'domain_register_id',
        'user_id',
        'status',
        'notes',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    /**
     * Get the domain that owns the client data
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class);
    }

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
     * Check if any service is expiring soon (within 60 days / 2 months)
     */
    public function isAnyServiceExpiringSoon()
    {
        if (!$this->domain || !$this->domain->expired_date) {
            return false;
        }

        return $this->domain->expired_date->lte(now()->addDays(60)) && !$this->domain->expired_date->isPast();
    }

    /**
     * Check if any service is expired
     */
    public function isAnyServiceExpired()
    {
        if (!$this->domain || !$this->domain->expired_date) {
            return false;
        }

        return $this->domain->expired_date->isPast();
    }

    /**
     * Get the earliest expiration date
     */
    public function getEarliestExpirationAttribute()
    {
        return $this->domain ? $this->domain->expired_date : null;
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
