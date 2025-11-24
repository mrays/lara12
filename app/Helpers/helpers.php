<?php

use App\Helpers\LogoHelper;

if (!function_exists('company_logo')) {
    /**
     * Get company logo URL
     */
    function company_logo($type = 'main', $fallback = true)
    {
        return LogoHelper::getLogo($type, $fallback);
    }
}

if (!function_exists('company_logo_img')) {
    /**
     * Get company logo HTML img tag
     */
    function company_logo_img($type = 'main', $class = '', $style = '', $alt = null)
    {
        return LogoHelper::getLogoImg($type, $class, $style, $alt);
    }
}

if (!function_exists('has_company_logo')) {
    /**
     * Check if company logo exists
     */
    function has_company_logo($type = 'main')
    {
        return LogoHelper::hasLogo($type);
    }
}

if (!function_exists('calculate_invoice_generation_date')) {
    /**
     * Calculate when to generate invoice based on service expiry and billing cycle
     */
    function calculate_invoice_generation_date($service_expiry_date, $billing_cycle)
    {
        $expiry = \Carbon\Carbon::parse($service_expiry_date);
        
        switch (strtolower($billing_cycle)) {
            case 'yearly':
            case 'annual':
                // 3 bulan sebelum habis untuk yearly
                return $expiry->copy()->subMonths(3);
                
            case 'monthly':
                // 1 minggu sebelum habis untuk monthly
                return $expiry->copy()->subWeeks(1);
                
            case 'quarterly':
                // 3 minggu sebelum habis untuk quarterly
                return $expiry->copy()->subWeeks(3);
                
            case 'semi-annual':
            case 'biannual':
                // 6 minggu sebelum habis untuk semi-annual
                return $expiry->copy()->subWeeks(6);
                
            default:
                // Default: 1 minggu sebelum habis
                return $expiry->copy()->subWeeks(1);
        }
    }
}

if (!function_exists('should_generate_invoice_today')) {
    /**
     * Check if invoice should be generated today for a service
     */
    function should_generate_invoice_today($service)
    {
        if (!$service->next_due_date) {
            return false;
        }
        
        $today = \Carbon\Carbon::today();
        $generation_date = calculate_invoice_generation_date($service->next_due_date, $service->billing_cycle);
        
        return $today->isSameDay($generation_date);
    }
}
