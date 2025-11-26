<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ServicePackage;
use App\Models\DomainExtension;

class ServicePackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create domain extensions first
        $domains = [
            ['extension' => 'com', 'price' => 150000, 'duration_years' => 1],
            ['extension' => 'id', 'price' => 200000, 'duration_years' => 1],
            ['extension' => 'net', 'price' => 180000, 'duration_years' => 1],
            ['extension' => 'org', 'price' => 170000, 'duration_years' => 1],
        ];

        foreach ($domains as $domain) {
            DomainExtension::create($domain);
        }

        // Create service packages
        $packages = [
            [
                'name' => 'Business Website Exclusive Type S',
                'description' => '2 GB storage • 5 GB monthly traffic • 1 situs web • 1 email account GRATIS • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 2 revisi • Login cPanel tersedia.',
                'base_price' => 4500000,
                'features' => [
                    'storage' => '2 GB',
                    'monthly_traffic' => '5 GB',
                    'websites' => '1',
                    'email_accounts' => '1',
                    'revisions' => '2',
                    'cpanel' => true,
                    'ssl' => true,
                    'domain' => true
                ],
                'is_active' => true
            ],
            [
                'name' => 'Business Website Professional Type M',
                'description' => '3,5 GB storage • Unlimited monthly traffic • "No Limit Sub Features" • 1 situs web • 2 "Email Account Pro" • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Maksimal 3 revisi • Login cPanel tersedia.',
                'base_price' => 7580000,
                'features' => [
                    'storage' => '3.5 GB',
                    'monthly_traffic' => 'Unlimited',
                    'websites' => '1',
                    'email_accounts' => '2',
                    'revisions' => '3',
                    'cpanel' => true,
                    'ssl' => true,
                    'domain' => true,
                    'sub_features' => 'No Limit'
                ],
                'is_active' => true
            ],
            [
                'name' => 'Business Website Enterprise Type L',
                'description' => '5 GB storage • Unlimited monthly traffic • Advanced features • Multiple websites • Premium support • Free domain • Free SSL & monitoring • Free akses login • Free biaya instalasi • Unlimited revisi • Login cPanel tersedia.',
                'base_price' => 12000000,
                'features' => [
                    'storage' => '5 GB',
                    'monthly_traffic' => 'Unlimited',
                    'websites' => 'Multiple',
                    'email_accounts' => '5',
                    'revisions' => 'Unlimited',
                    'cpanel' => true,
                    'ssl' => true,
                    'domain' => true,
                    'premium_support' => true
                ],
                'is_active' => true
            ]
        ];

        foreach ($packages as $package) {
            ServicePackage::create($package);
        }
    }
}
