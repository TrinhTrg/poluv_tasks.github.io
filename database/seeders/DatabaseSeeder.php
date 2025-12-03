<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
// Import 2 Seeder con vào để tránh lỗi không tìm thấy class
use Database\Seeders\CategorySeeder;
use Database\Seeders\TaskSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            CategorySeeder::class,
            TaskSeeder::class,
        ]);
    }
}