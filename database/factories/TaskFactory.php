<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class TaskFactory extends Factory
{
    public function definition(): array
    {
        // Random ngày bắt đầu trong khoảng tuần này
        $startDate = fake()->dateTimeBetween('now', '+1 week');
        // Ngày kết thúc phải sau ngày bắt đầu khoảng 2 ngày
        $dueDate = fake()->dateTimeInInterval($startDate, '+2 days');

        return [
            'user_id' => User::factory(),
            'category_id' => Category::factory(),
            
            'title' => fake()->sentence(4), // Tiêu đề khoảng 4 từ
            'description' => fake()->paragraph(),
            
            'start_at' => $startDate,
            'due_at' => $dueDate,
            
            'color' => fake()->hexColor(), // Color Tag của task
            'priority' => fake()->numberBetween(1, 3), // 1: Low, 2: Med, 3: High
            'has_notify' => fake()->boolean(),
            'is_notified' => false, // Default: chưa được thông báo
            'is_completed' => fake()->boolean(20), // 20% khả năng là đã hoàn thành
        ];
    }
}