<?php

namespace Modules\KycVerification\Database\Seeders;

use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [

            ['group' => 'KYC Provider', 'name' => 'view_kyc_provider', 'display_name' => 'View KYC Provider', 'description' => 'View KYC Provider', 'user_type' => 'Admin'],
            ['group' => 'KYC Provider', 'name' => 'add_kyc_provider', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],
            ['group' => 'KYC Provider', 'name' => 'edit_kyc_provider', 'display_name' => 'Edit KYC Provider', 'description' => 'Edit KYC Provider', 'user_type' => 'Admin'],
            ['group' => 'KYC Provider', 'name' => 'delete_kyc_provider', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],

            ['group' => 'KYC Verification', 'name' => 'view_kyc_verification', 'display_name' => 'View KYC Verification', 'description' => 'View KYC Verification', 'user_type' => 'Admin'],
            ['group' => 'KYC Verification', 'name' => 'add_kyc_verification', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],
            ['group' => 'KYC Verification', 'name' => 'edit_kyc_verification', 'display_name' => 'Edit KYC Verification', 'description' => 'Edit KYC Verification', 'user_type' => 'Admin'],
            ['group' => 'KYC Verification', 'name' => 'delete_kyc_verification', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],

            ['group' => 'KYC Setting', 'name' => 'view_kyc_setting', 'display_name' => 'View KYC Setting', 'description' => 'View KYC Setting', 'user_type' => 'Admin'],
            ['group' => 'KYC Setting', 'name' => 'add_kyc_setting', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],
            ['group' => 'KYC Setting', 'name' => 'edit_kyc_setting', 'display_name' => 'Edit KYC Setting', 'description' => 'Edit KYC Setting', 'user_type' => 'Admin'],
            ['group' => 'KYC Setting', 'name' => 'delete_kyc_setting', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],

            ['group' => 'KYC Credential Setting', 'name' => 'view_kyc_credential_setting', 'display_name' => 'View KYC Credential Setting', 'description' => 'View KYC Credential Setting', 'user_type' => 'Admin'],
            ['group' => 'KYC Credential Setting', 'name' => 'add_kyc_credential_setting', 'display_name' => 'Add KYC Credential Setting', 'description' => 'Add KYC Credential Setting', 'user_type' => 'Admin'],
            ['group' => 'KYC Credential Setting', 'name' => 'edit_kyc_credential_setting', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],
            ['group' => 'KYC Credential Setting', 'name' => 'delete_kyc_credential_setting', 'display_name' => null, 'description' => null, 'user_type' => 'Admin']
        ];

        \App\Models\Permission::insert($permissions);
    }
}
