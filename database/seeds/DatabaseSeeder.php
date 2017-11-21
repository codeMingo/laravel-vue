<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call('Database\Seeder\AdminSeeder');
        $this->call('Database\Seeder\UserSeeder');
        $this->call('Database\Seeder\AdminPermissionSeeder');
        $this->call('Database\Seeder\DictSeeder');
        $this->call('Database\Seeder\CategorySeeder');
        $this->call('Database\Seeder\TagSeeder');
        $this->call('Database\Seeder\ArticleSeeder');
    }
}
