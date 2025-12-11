<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy User admin
        $user = User::where('email', 'admin@poluv.com')->first();

        // Nếu không tìm thấy user thì dừng (tránh lỗi)
        if (!$user) return;

        // Lấy danh sách category của user đó
        $categories = Category::where('user_id', $user->id)->get();

        // Tạo 10 Task ngẫu nhiên cho user này
        Task::factory(10)->create([
            'user_id' => $user->id,
            // Lấy ngẫu nhiên 1 category ID từ danh sách trên
            'category_id' => $categories->random()->id 
        ]);
    }
}