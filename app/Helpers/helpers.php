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
