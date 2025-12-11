<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    public function definition(): array
    {
        return [
            // Tạo sẵn User nếu chưa có (để tránh lỗi khóa ngoại)
            'user_id' => User::factory(), 
            // Tên danh mục giả (Work, Personal...)
            'name' => fake()->word(), 
            // Màu sắc ngẫu nhiên dạng Hex (#RRGGBB)
            'color' => fake()->hexColor(), 
        ];
    }
}