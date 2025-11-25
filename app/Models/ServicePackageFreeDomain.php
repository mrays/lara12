<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServicePackageFreeDomain extends Model
{
    protected $fillable = [
        'service_package_id',
        'domain_extension_id',
        'duration_years',
        'discount_percent',
        'is_free',
        'sort_order',
    ];

    protected $casts = [
        'is_free' => 'boolean',
        'discount_percent' => 'decimal:2',
        'sort_order' => 'integer',
    ];

    /**
     * Get the service package that owns the free domain.
     */
    public function servicePackage(): BelongsTo
    {
        return $this->belongsTo(ServicePackage::class);
    }

    /**
     * Get the domain extension for this free domain.
     */
    public function domainExtension(): BelongsTo
    {
        return $this->belongsTo(DomainExtension::class);
    }

    /**
     * Get the formatted price based on discount.
     */
    public function getFormattedPriceAttribute(): string
    {
        if (!$this->domainExtension) {
            return 'Rp 0';
        }

        $normalPrice = $this->domainExtension->price;
        $discountAmount = $normalPrice * ($this->discount_percent / 100);
        $finalPrice = $normalPrice - $discountAmount;

        return $finalPrice == 0 ? 'FREE' : 'Rp ' . number_format($finalPrice, 0, ',', '.');
    }

    /**
     * Get the final price after discount.
     */
    public function getFinalPriceAttribute(): float
    {
        if (!$this->domainExtension) {
            return 0;
        }

        $normalPrice = $this->domainExtension->price;
        $discountAmount = $normalPrice * ($this->discount_percent / 100);
        return $normalPrice - $discountAmount;
    }
}
