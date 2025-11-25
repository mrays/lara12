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
        'is_active'
    ];

    protected $casts = [
        'features' => 'array',
        'base_price' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    /**
     * Get services that use this package
     */
    public function services()
    {
        return $this->hasMany(Service::class, 'package_id');
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
}
