<?php
namespace Database\Seeder;

use DB;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('categories')->truncate();
        $data = [
            ['category_type' => 10, 'category_name' => 'PHP后端技术', 'instruction' => '菜单说明', 'sort' => 1, 'status' => 1],
            ['category_type' => 10, 'category_name' => '前端技术', 'instruction' => '菜单说明', 'sort' => 2, 'status' => 1],
            ['category_type' => 10, 'category_name' => '服务端技术', 'instruction' => '菜单说明', 'sort' => 3, 'status' => 1],
            ['category_type' => 10, 'category_name' => 'java基础讲解', 'instruction' => '菜单说明', 'sort' => 4, 'status' => 1],
            ['category_type' => 20, 'category_name' => 'PHP教学视频', 'instruction' => '菜单说明', 'sort' => 1, 'status' => 1],
            ['category_type' => 20, 'category_name' => 'JAVA教学视频', 'instruction' => '菜单说明', 'sort' => 2, 'status' => 1],
            ['category_type' => 20, 'category_name' => 'Python教学视频', 'instruction' => '菜单说明', 'sort' => 3, 'status' => 1],
        ];
        \App\Models\Category::insert($data);
    }
}
