<?php
namespace Database\Seeder;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admin_permissions')->truncate();
        \App\Models\AdminPermission::create([
            'text'                => '超级管理员',
            'permission_includes' => '',
            'status'              => 1,
        ]);

        for ($i = 1; $i < 10; $i++) {
            $data[] = [
                'text'                => '管理员' . $i,
                'permission_includes' => $i,
                'status'              => 1,
            ];
        }
        \App\Models\AdminPermission::insert($data);
    }
}
