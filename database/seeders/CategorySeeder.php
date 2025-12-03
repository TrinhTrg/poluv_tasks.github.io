<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo 1 User cố định để test đăng nhập
        // Email: admin@poluv.com | Pass: 123456
        $user = User::firstOrCreate(
            ['email' => 'admin@poluv.com'],
            [
                'name' => 'Trinh Truong',
                'password' => Hash::make('123456')
            ]
        );

        // 2. Tạo các Category mẫu cho User này
        $categories = [
            ['name' => 'Work', 'color' => '#3B82F6'], // Blue
            ['name' => 'Personal', 'color' => '#10B981'], // Green
            ['name' => 'Health', 'color' => '#EF4444'], // Red
            ['name' => 'Study', 'color' => '#F59E0B'], // Yellow
        ];

        foreach ($categories as $cat) {
            Category::create([
                'user_id' => $user->id,
                'name' => $cat['name'],
                'color' => $cat['color']
            ]);
        }
    }
}