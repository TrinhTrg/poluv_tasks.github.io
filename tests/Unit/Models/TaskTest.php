<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Task;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TaskTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function task_belongs_to_user()
    {
        $user = $this->createUser();
        $task = $this->createTask(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $task->user);
        $this->assertEquals($user->id, $task->user->id);
    }

    /** @test */
    public function task_belongs_to_category()
    {
        $category = $this->createCategory();
        $task = $this->createTask(['category_id' => $category->id]);

        $this->assertInstanceOf(Category::class, $task->category);
        $this->assertEquals($category->id, $task->category->id);
    }

    /** @test */
    public function task_can_be_created_with_all_fields()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $task = Task::factory()->create([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'description' => 'Test Description',
            'priority' => 3,
            'is_completed' => false,
        ]);

        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Test Task',
            'priority' => 3,
            'is_completed' => false,
        ]);
    }

    /** @test */
    public function task_dates_are_cast_to_datetime()
    {
        $task = $this->createTask([
            'start_at' => '2024-01-01 10:00:00',
            'due_at' => '2024-01-02 18:00:00',
        ]);

        $this->assertInstanceOf(\Carbon\Carbon::class, $task->start_at);
        $this->assertInstanceOf(\Carbon\Carbon::class, $task->due_at);
    }

    /** @test */
    public function task_booleans_are_cast_correctly()
    {
        $task = $this->createTask([
            'is_completed' => true,
            'has_notify' => false,
            'is_notified' => true,
        ]);

        $this->assertIsBool($task->is_completed);
        $this->assertIsBool($task->has_notify);
        $this->assertIsBool($task->is_notified);
        $this->assertTrue($task->is_completed);
        $this->assertFalse($task->has_notify);
        $this->assertTrue($task->is_notified);
    }

    /** @test */
    public function task_priority_is_cast_to_integer()
    {
        $task = $this->createTask(['priority' => '2']);

        $this->assertIsInt($task->priority);
        $this->assertEquals(2, $task->priority);
    }

    /** @test */
    public function task_can_toggle_completion_status()
    {
        $task = $this->createTask(['is_completed' => false]);

        $task->is_completed = true;
        $task->save();

        $this->assertTrue($task->fresh()->is_completed);

        $task->is_completed = false;
        $task->save();

        $this->assertFalse($task->fresh()->is_completed);
    }
}

