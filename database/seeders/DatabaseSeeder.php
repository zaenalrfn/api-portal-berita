<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seeder utama
        $this->call([
            RoleSeeder::class,
            UserSeeder::class,
            NewsSeeder::class,
            CommentSeeder::class,
        ]);
    }
}
