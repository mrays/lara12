<?php

namespace App\Helpers;

class BillingCycleHelper
{
    /**
     * Get billing cycle mapping
     */
    public static function getBillingCycles()
    {
        return [
            '1M' => '1 Bulan',
            '2M' => '2 Bulan', 
            '3M' => '3 Bulan',
            '6M' => '6 Bulan',
            '1Y' => '1 Tahun',
            '2Y' => '2 Tahun',
            '3Y' => '3 Tahun',
            '4Y' => '4 Tahun',
        ];
    }

    /**
     * Get display name for billing cycle code
     */
    public static function getDisplayName($code)
    {
        $cycles = self::getBillingCycles();
        return $cycles[$code] ?? $code;
    }

    /**
     * Get code from display name
     */
    public static function getCode($displayName)
    {
        $cycles = array_flip(self::getBillingCycles());
        return $cycles[$displayName] ?? $displayName;
    }

    /**
     * Convert old format to new format
     */
    public static function convertOldFormat($oldValue)
    {
        $mapping = [
            'Monthly' => '1M',
            'Quarterly' => '3M', 
            'Semi-Annually' => '6M',
            'Annually' => '1Y',
            'Biennially' => '2Y',
            'One Time' => '1M',
        ];

        return $mapping[$oldValue] ?? $oldValue;
    }
}
