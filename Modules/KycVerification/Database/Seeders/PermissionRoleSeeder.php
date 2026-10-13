<?php

namespace Modules\KycVerification\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminPermissions = \App\Models\Permission::whereIn('group', config('kycverification.permission_group'))
        ->where('user_type', 'Admin')
        ->whereNotNull('display_name')
        ->get(['id']);

        if ($adminPermissions->isNotEmpty()) {
            $roleData = $adminPermissions->map(fn($permission) => [
                'role_id' => 1,
                'permission_id' => $permission->id,
            ])->toArray();

            DB::table('permission_role')->insert($roleData);
        }
    }
}
