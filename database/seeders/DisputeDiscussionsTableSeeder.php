<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class DisputeDiscussionsTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('dispute_discussions')->delete();
        
        DB::table('dispute_discussions')->insert([
            [
                'dispute_id' => getDisputeId('DIS-656MEZ'),
                'user_id' => getAdmin(),
                'type' => 'Admin',
                'message' => 'Hello, Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut luctus. Cras sed molestie nulla, nec placerat nibh. Donec placerat interdum libero eu blandit. Quisque at nulla ut mi porttitor eleifend nec nec erat. Thank you',
                'file' => null,
                'created_at' => Carbon::now()->subDays(50),
                'updated_at' => Carbon::now()->subDays(50),
            ],
            [
                'dispute_id' => getDisputeId('DIS-656MEZ'),
                'user_id' => getUserByEmail('kyla@gmail.com'),
                'type' => 'User',
                'message' => 'Hello admin, Duis in bibendum nisl. Praesent vel vestibulum enim. Sed ultrices pellentesque massa non sodales. Vestibulum ut magna in risus dignissim hendrerit. Aenean aliquet, massa et rutrum varius, nunc nisi ullamcorper ante, varius auctor sem nisl vel nisl. Cras gravida lectus at tempus sodales. Vivamus molestie dui nec bibendum rutrum. Nulla id purus a nibh fringilla dapibus at eu enim. Sed nunc leo, mattis vitae tempor nec, lobortis in diam. Cras nunc erat, aliquam vel sodales nec, scelerisque eget sem. Nulla dignissim facilisis feugiat. Nullam quis enim id libero fringilla accumsan ut ac eros. Nulla id interdum velit. Donec dictum nunc augue, vitae porta enim pharetra ut. Thank you',
                'file' => null,
                'created_at' => Carbon::now()->subDays(47),
                'updated_at' => Carbon::now()->subDays(47),
            ],
        ]);
    }
}
