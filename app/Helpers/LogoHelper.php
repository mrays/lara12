<?php

namespace App\Helpers;

class LogoHelper
{
    /**
     * Get logo URL with fallback
     */
    public static function getLogo($type = 'main', $fallback = true)
    {
        $logoPath = config("company.logo.{$type}");
        $fullPath = public_path($logoPath);
        
        // Check if logo file exists
        if (file_exists($fullPath)) {
            return asset($logoPath);
        }
        
        // Fallback options
        if ($fallback) {
            switch ($type) {
                case 'white':
                    // Try main logo if white doesn't exist
                    return self::getLogo('main', false) ?: self::getDefaultLogo();
                case 'small':
                    // Try main logo if small doesn't exist
                    return self::getLogo('main', false) ?: self::getDefaultLogo();
                case 'favicon':
                    // Default favicon
                    return asset('favicon.ico');
                default:
                    return self::getDefaultLogo();
            }
        }
        
        return null;
    }
    
    /**
     * Get default logo (SVG or text)
     */
    public static function getDefaultLogo()
    {
        // Return default SVG logo or null to show text
        return null;
    }
    
    /**
     * Check if logo exists
     */
    public static function hasLogo($type = 'main')
    {
        $logoPath = config("company.logo.{$type}");
        return $logoPath && file_exists(public_path($logoPath));
    }
    
    /**
     * Get logo HTML img tag
     */
    public static function getLogoImg($type = 'main', $class = '', $style = '', $alt = null)
    {
        $logoUrl = self::getLogo($type);
        $altText = $alt ?: config('company.logo.alt_text');
        
        if ($logoUrl) {
            return "<img src=\"{$logoUrl}\" alt=\"{$altText}\" class=\"{$class}\" style=\"{$style}\">";
        }
        
        // Fallback to company name
        return "<span class=\"{$class}\" style=\"{$style}\">" . config('company.name') . "</span>";
    }
}
