<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Domain extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'domain_name',
        'client_id',
        'server_id', 
        'domain_register_id',
        'expired_date',
        'status',
        'notes'
    ];

    protected $casts = [
        'expired_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Status options
    const STATUS_ACTIVE = 'active';
    const STATUS_EXPIRED = 'expired';
    const STATUS_PENDING = 'pending';
    const STATUS_SUSPENDED = 'suspended';

    /**
     * Get the client that owns the domain.
     */
    public function client()
    {
        return $this->belongsTo(ClientData::class, 'client_id');
    }

    /**
     * Get the server that hosts the domain.
     */
    public function server()
    {
        return $this->belongsTo(Server::class, 'server_id');
    }

    /**
     * Get the domain register for this domain.
     */
    public function domainRegister()
    {
        return $this->belongsTo(DomainRegister::class, 'domain_register_id');
    }

    /**
     * Get all available status options.
     */
    public static function getStatusOptions()
    {
        return [
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_EXPIRED => 'Expired', 
            self::STATUS_PENDING => 'Pending',
            self::STATUS_SUSPENDED => 'Suspended'
        ];
    }

    /**
     * Get status badge color.
     */
    public function getStatusBadgeColor()
    {
        switch ($this->status) {
            case self::STATUS_ACTIVE:
                return 'success';
            case self::STATUS_EXPIRED:
                return 'danger';
            case self::STATUS_PENDING:
                return 'warning';
            case self::STATUS_SUSPENDED:
                return 'secondary';
            default:
                return 'primary';
        }
    }

    /**
     * Get expiration status.
     */
    public function getExpirationStatus()
    {
        if (!$this->expired_date) {
            return 'not_set';
        }

        $today = now();
        $expiredDate = $this->expired_date;

        if ($expiredDate->isPast()) {
            return 'expired';
        } elseif ($expiredDate->diffInDays($today) <= 7) {
            return 'critical';
        } elseif ($expiredDate->diffInDays($today) <= 30) {
            return 'warning';
        } else {
            return 'safe';
        }
    }

    /**
     * Get expiration status badge color.
     */
    public function getExpirationBadgeColor()
    {
        switch ($this->getExpirationStatus()) {
            case 'expired':
                return 'danger';
            case 'critical':
                return 'danger';
            case 'warning':
                return 'warning';
            case 'safe':
                return 'success';
            default:
                return 'secondary';
        }
    }

    /**
     * Get days until expiration.
     */
    public function getDaysUntilExpiration()
    {
        if (!$this->expired_date) {
            return null;
        }

        return $this->expired_date->diffInDays(now(), false);
    }

    /**
     * Get formatted expiration date.
     */
    public function getFormattedExpirationDate()
    {
        if (!$this->expired_date) {
            return 'Not Set';
        }

        return $this->expired_date->format('M d, Y');
    }

    /**
     * Scope for active domains.
     */
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    /**
     * Scope for expired domains.
     */
    public function scopeExpired($query)
    {
        return $query->where('expired_date', '<', now());
    }

    /**
     * Scope for expiring soon (within 30 days).
     */
    public function scopeExpiringSoon($query)
    {
        return $query->where('expired_date', '>=', now())
                   ->where('expired_date', '<=', now()->addDays(30));
    }

    /**
     * Scope for critical expirations (within 7 days).
     */
    public function scopeCritical($query)
    {
        return $query->where('expired_date', '>=', now())
                   ->where('expired_date', '<=', now()->addDays(7));
    }

    /**
     * Scope for safe domains (more than 30 days).
     */
    public function scopeSafe($query)
    {
        return $query->where('expired_date', '>', now()->addDays(30));
    }

    /**
     * Scope by status.
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope with relationships.
     */
    public function scopeWithRelations($query)
    {
        return $query->with(['client', 'server', 'domainRegister']);
    }

    /**
     * Update expiration status based on date.
     */
    public function updateExpirationStatus()
    {
        if ($this->expired_date && $this->expired_date->isPast() && $this->status === self::STATUS_ACTIVE) {
            $this->status = self::STATUS_EXPIRED;
            $this->save();
        }
    }

    /**
     * Get domain summary for display.
     */
    public function getSummaryAttribute()
    {
        $parts = [];
        
        if ($this->client) {
            $parts[] = $this->client->name;
        }
        
        if ($this->server) {
            $parts[] = $this->server->name;
        }
        
        if ($this->domainRegister) {
            $parts[] = $this->domainRegister->name;
        }
        
        return implode(' • ', $parts);
    }
}
