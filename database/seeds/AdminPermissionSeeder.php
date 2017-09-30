<?php
namespace Database\Seeder;

use DB;
use Illuminate\Database\Seeder;

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
            'text'               => '超级管理员',
            'permission_include' => '',
            'status'             => 1,
        ]);

        for ($i = 1; $i < 10; $i++) {
            $data = [
                'text'               => '管理员' . $i,
                'permission_include' => $i,
                'status'             => 1,
            ];
        }
        \App\Models\AdminPermission::insert($data);
    }
}
