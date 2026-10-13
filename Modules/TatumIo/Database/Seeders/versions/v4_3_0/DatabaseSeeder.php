<?php

namespace Modules\TatumIo\Database\Seeders\versions\v4_3_0;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(PermissionTableSeeder::class);
        $this->call(TatumIoMetasTableSeeder::class);
        $this->call(PermissionRoleTableSeeder::class);
    }
}
