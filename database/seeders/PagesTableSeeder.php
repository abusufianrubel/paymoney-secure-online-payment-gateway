<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PagesTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('pages')->delete();
        
        DB::table('pages')->insert([
            [
                'name' => 'About us',
                'url' => 'about-us',
                'content' => '<p><b><span style="font-size: 18px;">About Us</span></b><p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut luctus. Cras sed molestie nulla, nec placerat nibh. Donec placerat interdum libero eu blandit. Quisque at nulla ut mi porttitor eleifend nec nec erat.<br></p><p><br></p><p></p><p>Duis in bibendum nisl. Praesent vel vestibulum enim. Sed ultrices pellentesque massa non sodales. Vestibulum ut magna in risus dignissim hendrerit. Aenean aliquet, massa et rutrum varius, nunc nisi ullamcorper ante, varius auctor sem nisl vel nisl. Cras gravida lectus at tempus sodales. Vivamus molestie dui nec bibendum rutrum. Nulla id purus a nibh fringilla dapibus at eu enim. Sed nunc leo, mattis vitae tempor nec, lobortis in diam. Cras nunc erat, aliquam vel sodales nec, scelerisque eget sem. Nulla dignissim facilisis feugiat. Nullam quis enim id libero fringilla accumsan ut ac eros. Nulla id interdum velit. Donec dictum nunc augue, vitae porta enim pharetra ut.</p><p><br></p><p></p><p></p><p> Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin a justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor. Vivamus maximus lacus metus, eu vulputate magna facilisis commodo. Cras porta molestie accumsan. Nunc at mollis est. Aliquam eleifend varius metus, et facilisis risus sagittis ut. Etiam in ligula a risus semper porttitor nec et magna. Sed sed ipsum ultricies, tincidunt magna quis, facilisis quam. Morbi dapibus tincidunt quam sed feugiat.</p><p><br></p><p>&nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut luctus. Cras sed molestie nulla, nec placerat nibh. Donec placerat interdum libero eu blandit.Quisque at nulla ut mi porttitor eleifend nec nec erat.</p><p><br></p><p></p><p> Duis in bibendum nisl. Praesent vel vestibulum enim. Sed ultrices pellentesque massa non sodales. Vestibulum ut magna in risus dignissim hendrerit. Aenean aliquet, massa et rutrum varius, nunc nisi ullamcorper ante, varius auctor sem nisl vel nisl. Cras gravida lectus at tempus sodales. Vivamus molestie dui nec bibendum rutrum. Nulla id purus a nibh fringilla dapibus at eu enim. Sed nunc leo, mattis vitae tempor nec, lobortis in diam. Cras nunc erat, aliquam vel sodales nec, scelerisque eget sem. Nulla dignissim facilisis feugiat. Nullam quis enim id libero fringilla accumsan ut ac eros. Nulla id interdum velit. Donec dictum nunc augue, vitae porta enim pharetra ut.</p><p><br></p><p></p><p>Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin a justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor. Vivamus maximus lacus metus, eu vulputate magna facilisis commodo. Cras porta molestie accumsan. Nunc at mollis est. Aliquam eleifend varius metus, et facilisis risus sagittis ut. Etiam in ligula a risus semper porttitor nec et magna. Sed sed ipsum ultricies, tincidunt magna quis, facilisis quam. Morbi dapibus tincidunt quam sed feugiat.&nbsp;</p><p></p><p></p><p></p></p>',
                'position' => '["footer"]',
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(500),
                'updated_at' => Carbon::now()->subDays(500),
            ],
            [
                'name' => 'Portfoilo',
                'url' => 'portfoilo',
                'content' => '<p><b><span style="font-size: 18px;">Portfolio</span></b><p>&nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut luctus. Cras sed molestie nulla, nec placerat nibh. Donec placerat interdum libero eu blandit. Quisque at nulla ut mi porttitor eleifend nec nec erat.</p><p><br></p><p></p><p> Duis in bibendum nisl. Praesent vel vestibulum enim. Sed ultrices pellentesque massa non sodales. Vestibulum ut magna in risus dignissim hendrerit. Aenean aliquet, massa et rutrum varius, nunc nisi ullamcorper ante, varius auctor sem nisl vel nisl. Cras gravida lectus at tempus sodales. Vivamus molestie dui nec bibendum rutrum. Nulla id purus a nibh fringilla dapibus at eu enim. Sed nunc leo, mattis vitae tempor nec, lobortis in diam. Cras nunc erat, aliquam vel sodales nec, scelerisque eget sem. Nulla dignissim facilisis feugiat. Nullam quis enim id libero fringilla accumsan ut ac eros. Nulla id interdum velit. Donec dictum nunc augue, vitae porta enim pharetra ut.</p><p><br></p><p><br></p><p></p><p></p><p> Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin a justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor. Vivamus maximus lacus metus, eu vulputate magna facilisis commodo. Cras porta molestie accumsan. Nunc at mollis est. Aliquam eleifend varius metus, et facilisis risus sagittis ut. Etiam in ligula a risus semper porttitor nec et magna. Sed sed ipsum ultricies, tincidunt magna quis, facilisis quam. Morbi dapibus tincidunt quam sed feugiat.&nbsp;</p><p><br></p><p><br></p></p>',
                'position' => '["footer"]',
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(490),
                'updated_at' => Carbon::now()->subDays(490),
            ],
            [
                'name' => 'Contact Us',
                'url' => 'contact-us',
                'content' => '<p><b><span style="font-size: 18px;">Contact Us</span></b><p>&nbsp;Lorem ipsum dolor sit amet, consectetur adipiscing elit. Cras in purus sem. Phasellus auctor facilisis velit at rhoncus. Maecenas sed enim eu orci scelerisque lobortis porttitor id erat. Quisque congue porttitor placerat. Fusce malesuada hendrerit est ut luctus. Cras sed molestie nulla, nec placerat nibh. Donec placerat interdum libero eu blandit. Quisque at nulla ut mi porttitor eleifend nec nec erat.</p><p><br></p><p></p><p> Duis in bibendum nisl. Praesent vel vestibulum enim. Sed ultrices pellentesque massa non sodales. Vestibulum ut magna in risus dignissim hendrerit. Aenean aliquet, massa et rutrum varius, nunc nisi ullamcorperante, varius auctor sem nisl vel nisl. Cras gravida lectus at tempus sodales. Vivamus molestie dui nec bibendum rutrum. Nulla id purus a nibh fringilla dapibus at eu enim. Sed nunc leo, mattis vitae tempor nec, lobortis in diam. Cras nunc erat, aliquam vel sodales nec, scelerisque eget sem. Nulla dignissim facilisis feugiat. Nullam quis enim id libero fringilla accumsan ut ac eros. Nulla id interdum velit. Donec dictum nunc augue, vitae porta enim pharetra ut.</p><p><br></p><p></p><p></p><p> Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proina justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor. Vivamus maximus lacus metus, eu vulputate magna facilisiscommodo. Cras porta molestie accumsan. Nunc at mollis est. Aliquam eleifend varius metus, et facilisis risus sagittis ut. Etiam in ligula arisus semper porttitor nec et magna. Sed sed ipsum ultricies, tincidunt magna quis, facilisis quam. Morbi dapibus tincidunt quam sed feugiat.&nbsp;</p><p><br></p><p>Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin a justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor. Vivamus maximus lacus metus, eu vulputate magna facilisis commodo. Cras porta molestie accumsan. Nunc at mollis est. Aliquam eleifend varius metus, et facilisis risus sagittis ut. Etiam in ligula a risus semper porttitor nec et magna. Sed sed ipsum ultricies, tincidunt magna quis, facilisis quam. Morbi dapibus tincidunt quam sed feugiat.</p><p><br></p><p>Aliquam elementum blandit risus vel facilisis. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Proin a justo vitae libero facilisis scelerisque. Duis sed ornare nibh, id gravida dolor. Vivamus maximus lacus metus, eu vulputate magna facilisis commodo. Cras porta molestie accumsan. Nunc at mollis est. Aliquam eleifend varius metus, et facilisis risus sagittis ut. Etiam in ligula a risus semper porttitor nec et magna. Sed sed ipsum ultricies, tincidunt magna quis, facilisis quam. Morbi dapibus tincidunt quam sed feugiat.<br></p></p>',
                'position' => '["footer"]',
                'status' => 'active',
                'created_at' => Carbon::now()->subDays(480),
                'updated_at' => Carbon::now()->subDays(480),
            ],
        ]);
    }
}
