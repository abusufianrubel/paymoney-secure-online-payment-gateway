<?php

namespace Modules\TatumIo\Database\Seeders\versions\v4_3_0;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $permissions = [ 
            // Crypto Token
            ['group' => 'Crypto Token', 'name' => 'view_crypto_token', 'display_name' => 'View Crypto Token', 'description' => 'View Crypto Token', 'user_type' => 'Admin'],
            ['group' => 'Crypto Token', 'name' => 'add_crypto_token', 'display_name' => 'Add Crypto Token', 'description' => 'Add Crypto Token', 'user_type' => 'Admin'],
            ['group' => 'Crypto Token', 'name' => 'edit_crypto_token', 'display_name' => 'Edit Crypto Token', 'description' => 'Edit Crypto Token', 'user_type' => 'Admin'],
            ['group' => 'Crypto Token', 'name' => 'delete_crypto_token', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],
            
            // Token Transactions
            ['group' => 'Token Transactions', 'name' => 'view_token_transactions', 'display_name' => 'View Token Transactions', 'description' => 'View Token Transactions', 'user_type' => 'Admin'],
            ['group' => 'Token Transactions', 'name' => 'add_token_transactions', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],
            ['group' => 'Token Transactions', 'name' => 'edit_token_transactions', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],
            ['group' => 'Token Transactions', 'name' => 'delete_token_transactions', 'display_name' => null, 'description' => null, 'user_type' => 'Admin'],

            // Token Send Receive User Panel
            ['group' => 'Token Send/Receive', 'name' => 'manage_token_send_receive', 'display_name' => 'Manage Token Send/Receive', 'description' => 'Manage Token Send/Receive', 'user_type' => 'User'],
            
        ];

        \App\Models\Permission::insert($permissions);
    }
}
