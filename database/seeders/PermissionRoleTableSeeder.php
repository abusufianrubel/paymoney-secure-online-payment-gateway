<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('permission_role')->delete();

        $adminPermissions = \App\Models\Permission::where('user_type', 'Admin')->get(['id', 'display_name']);

        foreach ($adminPermissions as $value) {
            if ($value->display_name == null) {
                continue;
            }

            $roleData[] = [
                'role_id' => 1,
                'permission_id' => $value->id,
            ];
        }

        $investmentUserPermissions = \App\Models\Permission::where('user_type', 'User')->get(['id', 'name']);

        foreach ($investmentUserPermissions as $value) {

            if (!in_array($value->name, ['manage_merchant', 'manage_merchant_payment'])) {
                
                $roleData[] = [
                    'role_id' => 2,
                    'permission_id' => $value->id,
                ];
            }

            $roleData[] = [
                'role_id' => 3,
                'permission_id' => $value->id,
            ];
        }

        DB::table('permission_role')->insert($roleData);
    }
}


