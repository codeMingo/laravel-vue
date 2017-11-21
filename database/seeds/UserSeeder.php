<?php
namespace Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        for ($i = 1; $i < 100; $i++) {
            $data[] = [
                'username' => 'user' . $i,
                'email'    => 'userEmail' . $i . '@qq.com',
                'face'     => 'http://owmb1f0qu.bkt.clouddn.com/test/beauty.jpg',
                'password' => Hash::make('123456'),
                'active'   => 1,
                'status'   => 1,
            ];
        }
        \App\Models\User::insert($data);
    }
}
