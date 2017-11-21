<?php
namespace Database\Seeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tags')->truncate();
        $article_category_type = ['技术篇', 'mysql', 'redis', 'php', 'mogodb', 'javascript'];
        $video_category_type = ['php', 'java', '.net', 'golang', 'python', 'jsp'];
        $rand = rand(0, 5);

        for ($i = 1; $i < 100; $i++) {
            $data[] = [
                'admin_id' => 1,
                'category_type'    => ($i % 2 == 0) ? 10 : 20,
                'tag_name'     => ($i % 2 == 0) ? $article_category_type[$rand] : $video_category_type[$rand],
                'status'   => 1,
            ];
        }
        \App\Models\Tag::insert($data);
    }
}
