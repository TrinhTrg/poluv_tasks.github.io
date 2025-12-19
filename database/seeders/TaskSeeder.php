<?php

namespace Database\Seeders;

use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TaskSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy User admin
        $user = User::where('email', 'admin@poluv.com')->first();

        // Nếu không tìm thấy user thì dừng (tránh lỗi)
        if (!$user) return;

        // Lấy danh sách category của user đó
        $workCategory = Category::where('user_id', $user->id)->where('name', 'Work')->first();
        $personalCategory = Category::where('user_id', $user->id)->where('name', 'Personal')->first();
        $homeworkCategory = Category::where('user_id', $user->id)->where('name', 'Homework')->first();
        $meetingCategory = Category::where('user_id', $user->id)->where('name', 'Meeting')->first();
        $otherCategory = Category::where('user_id', $user->id)->where('name', 'Other')->first();

        // Nếu không có categories thì dừng
        if (!$workCategory || !$personalCategory || !$homeworkCategory || !$meetingCategory || !$otherCategory) {
            return;
        }

        // Tạo 5 tasks cụ thể với màu pastel nhẹ nhàng (không trùng với category colors)
        $tasks = [
            [
                'user_id' => $user->id,
                'category_id' => $workCategory->id,
                'title' => 'Complete Project Proposal',
                'description' => 'Finish writing the project proposal document and submit to client',
                'start_at' => Carbon::now()->addDays(1)->setTime(9, 0),
                'due_at' => Carbon::now()->addDays(3)->setTime(17, 0),
                'color' => '#FFB6C1', // Pastel Pink
                'priority' => 3, // High
                'has_notify' => true,
                'is_notified' => false,
                'is_completed' => false,
            ],
            [
                'user_id' => $user->id,
                'category_id' => $homeworkCategory->id,
                'title' => 'Study for Math Exam',
                'description' => 'Review chapters 5-8 and complete practice problems',
                'start_at' => Carbon::now()->addDays(2)->setTime(14, 0),
                'due_at' => Carbon::now()->addDays(5)->setTime(16, 0),
                'color' => '#B0E0E6', // Pastel Blue
                'priority' => 3, // High
                'has_notify' => true,
                'is_notified' => false,
                'is_completed' => false,
            ],
            [
                'user_id' => $user->id,
                'category_id' => $meetingCategory->id,
                'title' => 'Team Standup Meeting',
                'description' => 'Weekly team sync to discuss progress and blockers',
                'start_at' => Carbon::now()->addDays(1)->setTime(10, 30),
                'due_at' => Carbon::now()->addDays(1)->setTime(11, 0),
                'color' => '#FFFACD', // Pastel Yellow
                'priority' => 2, // Medium
                'has_notify' => true,
                'is_notified' => false,
                'is_completed' => false,
            ],
            [
                'user_id' => $user->id,
                'category_id' => $personalCategory->id,
                'title' => 'Grocery Shopping',
                'description' => 'Buy ingredients for weekend cooking session',
                'start_at' => Carbon::now()->addDays(3)->setTime(15, 0),
                'due_at' => Carbon::now()->addDays(3)->setTime(18, 0),
                'color' => '#DDA0DD', // Pastel Purple
                'priority' => 1, // Low
                'has_notify' => false,
                'is_notified' => false,
                'is_completed' => false,
            ],
            [
            'user_id' => $user->id,
                'category_id' => $otherCategory->id,
                'title' => 'Update Portfolio Website',
                'description' => 'Add new projects and update resume section',
                'start_at' => Carbon::now()->addDays(4)->setTime(13, 0),
                'due_at' => Carbon::now()->addDays(7)->setTime(17, 0),
                'color' => '#FFDAB9', // Pastel Peach
                'priority' => 2, // Medium
                'has_notify' => true,
                'is_notified' => false,
                'is_completed' => false,
            ],
        ];

        // Tạo từng task
        foreach ($tasks as $taskData) {
            Task::create($taskData);
        }
    }
}