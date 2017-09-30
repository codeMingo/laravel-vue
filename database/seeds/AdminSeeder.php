<?php
namespace Database\Seeder;

use DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('admins')->truncate();
        for ($i = 1; $i < 100; $i++) {
            $data[] = [
                'username'           => 'admin' . $i,
                'email'              => 'admin' . $i . '@qq.com',
                'password'           => Hash::make('123456'),
                'permission_id'      => $i,
                'permission_include' => $i,
                'status'             => 1,
            ];
        }
        \App\Models\Admin::insert($data);
    }
}
