<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class DomainRegister extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'username',
        'password',
        'login_link',
        'expired_date',
        'status',
        'notes',
    ];

    protected $casts = [
        'expired_date' => 'date',
    ];

    /**
     * Get the decrypted password
     */
    public function getDecryptedPasswordAttribute()
    {
        return $this->password ? Crypt::decryptString($this->password) : null;
    }

    /**
     * Set the encrypted password
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = $value ? Crypt::encryptString($value) : null;
    }

    /**
     * Get domains registered with this domain register
     */
    public function domains()
    {
        return $this->hasMany(Domain::class);
    }

    /**
     * Get clients associated with this domain register (through domains)
     */
    public function clients()
    {
        return $this->hasManyThrough(
            ClientData::class,
            Domain::class,
            'domain_register_id',  // Foreign key on domains table
            'id',                  // Foreign key on client_data table
            'id',                  // Local key on domain_registers table
            'client_id'            // Local key on domains table
        );
    }

    /**
     * Check if register is expiring soon (within 30 days)
     */
    public function isExpiringSoon()
    {
        return $this->expired_date && $this->expired_date->lte(now()->addDays(30));
    }

    /**
     * Check if register is expired
     */
    public function isExpired()
    {
        return $this->expired_date && $this->expired_date->isPast();
    }

    /**
     * Get status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return match($this->status) {
            'active' => 'bg-success',
            'expired' => 'bg-danger',
            'suspended' => 'bg-warning',
            default => 'bg-secondary'
        };
    }
}
