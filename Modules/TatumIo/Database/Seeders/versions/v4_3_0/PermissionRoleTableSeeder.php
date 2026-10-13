<?php

namespace Modules\TatumIo\Database\Seeders\versions\v4_3_0;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PermissionRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $permissionGroup = ['Crypto Token', 'Token Transactions', 'Token Send/Receive'];
        
        Model::unguard();

        $adminPermissions = \App\Models\Permission::whereIn('group', $permissionGroup)
            ->where('user_type', 'Admin')
            ->get();

        foreach ($adminPermissions as $value) {
            if ($value->display_name == null) continue;
            $roleData[] = [
                'role_id' => 1,
                'permission_id' => $value->id,
            ];
        }

        $userPermissions = \App\Models\Permission::whereIn('group', $permissionGroup)
            ->where('user_type', 'User')
            ->get();
            
        foreach ($userPermissions as $value) {
            $roleData[] = [
                'role_id' => 2,
                'permission_id' => $value->id,
            ];
            $roleData[] = [
                'role_id' => 3,
                'permission_id' => $value->id,
            ];
        }

        
        DB::table('permission_role')->insert($roleData);
    }
}
