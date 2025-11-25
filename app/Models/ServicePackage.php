<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServicePackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'base_price',
        'features',
        'is_active',
        'domain_extension_id',
        'domain_duration_years',
        'is_domain_free',
        'domain_discount_percent'
    ];

    protected $casts = [
        'features' => 'array',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_domain_free' => 'boolean',
        'domain_discount_percent' => 'decimal:2'
    ];

    /**
     * Get services that use this package
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'package_id');
    }

    /**
     * Get the domain extension associated with this package
     */
    public function domainExtension()
    {
        return $this->belongsTo(DomainExtension::class, 'domain_extension_id');
    }

    /**
     * Get the free domains associated with this package
     */
    public function freeDomains()
    {
        return $this->hasMany(ServicePackageFreeDomain::class)->orderBy('sort_order');
    }

    /**
     * Scope for active packages
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }

    /**
     * Get features as formatted string
     */
    public function getFeaturesStringAttribute()
    {
        if (!$this->features) {
            return '';
        }

        $features = [];
        foreach ($this->features as $key => $value) {
            if (is_bool($value)) {
                if ($value) {
                    $features[] = ucfirst(str_replace('_', ' ', $key));
                }
            } else {
                $features[] = $value . ' ' . str_replace('_', ' ', $key);
            }
        }

        return implode(' • ', $features);
    }

    /**
     * Get price based on billing cycle
     */
    public function getPrice($billingCycle = 'monthly')
    {
        if ($billingCycle === 'annually') {
            return $this->base_price * 12 * 0.9; // 10% discount for annual
        }
        
        return $this->base_price;
    }

    /**
     * Get formatted price for specific billing cycle
     */
    public function getFormattedPrice($billingCycle = 'monthly')
    {
        $price = $this->getPrice($billingCycle);
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    /**
     * Get annual price with discount
     */
    public function getAnnualPriceAttribute()
    {
        return $this->getPrice('annually');
    }

    /**
     * Get formatted annual price
     */
    public function getFormattedAnnualPriceAttribute()
    {
        return $this->getFormattedPrice('annually');
    }

    /**
     * Get domain price with discount applied
     */
    public function getDomainPrice()
    {
        if (!$this->domainExtension || !$this->domain_duration_years) {
            return 0;
        }

        $baseDomainPrice = $this->domainExtension->price;
        
        if ($this->is_domain_free) {
            return 0;
        }

        if ($this->domain_discount_percent > 0) {
            $discount = $baseDomainPrice * ($this->domain_discount_percent / 100);
            return $baseDomainPrice - $discount;
        }

        return $baseDomainPrice;
    }

    /**
     * Get formatted domain price
     */
    public function getFormattedDomainPriceAttribute()
    {
        $price = $this->getDomainPrice();
        
        if ($price == 0 && $this->domainExtension) {
            return 'FREE';
        }
        
        return 'Rp ' . number_format($price, 0, ',', '.');
    }

    /**
     * Get domain display text
     */
    public function getDomainDisplayAttribute()
    {
        if (!$this->domainExtension || !$this->domain_duration_years) {
            return 'Tidak ada domain';
        }

        $extension = '.' . $this->domainExtension->extension;
        $duration = $this->domain_duration_years . ' tahun';
        $price = $this->formatted_domain_price;

        return "{$extension} ({$duration}) - {$price}";
    }

    /**
     * Get total price including domain
     */
    public function getTotalPrice($billingCycle = 'monthly')
    {
        $packagePrice = $this->getPrice($billingCycle);
        $domainPrice = $this->getDomainPrice();
        
        return $packagePrice + $domainPrice;
    }

    /**
     * Get formatted total price including domain
     */
    public function getFormattedTotalPrice($billingCycle = 'monthly')
    {
        $price = $this->getTotalPrice($billingCycle);
        return 'Rp ' . number_format($price, 0, ',', '.');
    }
}
