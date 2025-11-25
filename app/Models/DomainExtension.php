<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DomainExtension extends Model
{
    use HasFactory;

    protected $fillable = [
        'extension',
        'duration_years',
        'price',
        'description',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'duration_years' => 'integer'
    ];

    /**
     * Get formatted price with currency
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Get duration display text
     */
    public function getDurationDisplayAttribute()
    {
        return $this->duration_years . ' Tahun' . ($this->duration_years > 1 ? '' : '');
    }

    /**
     * Scope to get only active extensions
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope to filter by extension
     */
    public function scopeByExtension($query, $extension)
    {
        return $query->where('extension', $extension);
    }

    /**
     * Scope to filter by duration
     */
    public function scopeByDuration($query, $years)
    {
        return $query->where('duration_years', $years);
    }
}
