<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\TaskService;
use App\Models\Task;
use App\Models\Category;
use App\Models\User;
use App\Exceptions\UnauthorizedException;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class TaskServiceTest extends TestCase
{
    use RefreshDatabase;

    protected TaskService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new TaskService();
    }

    /** @test */
    public function it_can_get_tasks_for_authenticated_user()
    {
        $user = $this->createUser();
        $this->actingAs($user);

        $category = $this->createCategory(['user_id' => $user->id]);
        $task1 = $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);
        $task2 = $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);

        $tasks = $this->service->getTasks([], $user->id);

        $this->assertCount(2, $tasks);
        $this->assertTrue($tasks->contains($task1));
        $this->assertTrue($tasks->contains($task2));
    }

    /** @test */
    public function it_can_filter_tasks_by_search()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        
        $task1 = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Buy groceries',
        ]);
        
        $task2 = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Finish report',
        ]);

        $tasks = $this->service->getTasks(['search' => 'groceries'], $user->id);

        $this->assertCount(1, $tasks);
        $this->assertTrue($tasks->contains($task1));
        $this->assertFalse($tasks->contains($task2));
    }

    /** @test */
    public function it_can_filter_tasks_by_category()
    {
        $user = $this->createUser();
        $category1 = $this->createCategory(['user_id' => $user->id]);
        $category2 = $this->createCategory(['user_id' => $user->id]);
        
        $task1 = $this->createTask(['user_id' => $user->id, 'category_id' => $category1->id]);
        $task2 = $this->createTask(['user_id' => $user->id, 'category_id' => $category2->id]);

        $tasks = $this->service->getTasks(['category_id' => $category1->id], $user->id);

        $this->assertCount(1, $tasks);
        $this->assertTrue($tasks->contains($task1));
        $this->assertFalse($tasks->contains($task2));
    }

    /** @test */
    public function it_can_filter_tasks_by_status_completed()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        
        $task1 = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => true,
        ]);
        
        $task2 = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => false,
        ]);

        $tasks = $this->service->getTasks(['status' => 'completed'], $user->id);

        $this->assertCount(1, $tasks);
        $this->assertTrue($tasks->contains($task1));
        $this->assertFalse($tasks->contains($task2));
    }

    /** @test */
    public function it_can_filter_tasks_by_status_pending()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        
        $task1 = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => true,
        ]);
        
        $task2 = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => false,
        ]);

        $tasks = $this->service->getTasks(['status' => 'pending'], $user->id);

        $this->assertCount(1, $tasks);
        $this->assertFalse($tasks->contains($task1));
        $this->assertTrue($tasks->contains($task2));
    }

    /** @test */
    public function it_can_get_a_single_task()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);

        $result = $this->service->getTask($task->id, $user->id);

        $this->assertEquals($task->id, $result->id);
        $this->assertTrue($result->relationLoaded('category'));
    }

    /** @test */
    public function it_throws_exception_when_task_not_found()
    {
        $user = $this->createUser();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Task not found.');

        $this->service->getTask(999, $user->id);
    }

    /** @test */
    public function it_throws_exception_when_user_does_not_own_task()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user1->id]);
        $task = $this->createTask(['user_id' => $user1->id, 'category_id' => $category->id]);

        $this->expectException(ForbiddenException::class);

        $this->service->getTask($task->id, $user2->id);
    }

    /** @test */
    public function it_can_create_a_task()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $taskData = [
            'title' => 'New Task',
            'description' => 'Task description',
            'category' => $category->name,
            'priority' => 'high',
            'notify' => true,
        ];

        $task = $this->service->createTask($taskData, $user->id);

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'New Task',
            'description' => 'Task description',
            'priority' => 3,
            'has_notify' => true,
            'is_completed' => false,
        ]);
        $this->assertTrue($task->relationLoaded('category'));
    }

    /** @test */
    public function it_throws_exception_when_creating_task_without_authentication()
    {
        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Please sign in to create tasks.');

        $this->service->createTask(['title' => 'New Task'], null);
    }

    /** @test */
    public function it_can_update_a_task()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'title' => 'Old Title',
        ]);

        $updateData = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'priority' => 'high',
        ];

        $updatedTask = $this->service->updateTask($task->id, $updateData, $user->id);

        $this->assertEquals('Updated Title', $updatedTask->title);
        $this->assertEquals('Updated description', $updatedTask->description);
        $this->assertEquals(3, $updatedTask->priority);
    }

    /** @test */
    public function it_throws_exception_when_updating_task_without_authentication()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Please sign in to update tasks.');

        $this->service->updateTask($task->id, ['title' => 'Updated'], null);
    }

    /** @test */
    public function it_throws_exception_when_updating_non_existent_task()
    {
        $user = $this->createUser();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Task not found.');

        $this->service->updateTask(999, ['title' => 'Updated'], $user->id);
    }

    /** @test */
    public function it_throws_exception_when_user_does_not_own_task_for_update()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user1->id]);
        $task = $this->createTask(['user_id' => $user1->id, 'category_id' => $category->id]);

        $this->expectException(ForbiddenException::class);

        $this->service->updateTask($task->id, ['title' => 'Updated'], $user2->id);
    }

    /** @test */
    public function it_can_delete_a_task()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);

        $result = $this->service->deleteTask($task->id, $user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function it_throws_exception_when_deleting_task_without_authentication()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Please sign in to delete tasks.');

        $this->service->deleteTask($task->id, null);
    }

    /** @test */
    public function it_throws_exception_when_deleting_non_existent_task()
    {
        $user = $this->createUser();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Task not found.');

        $this->service->deleteTask(999, $user->id);
    }

    /** @test */
    public function it_throws_exception_when_user_does_not_own_task_for_delete()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user1->id]);
        $task = $this->createTask(['user_id' => $user1->id, 'category_id' => $category->id]);

        $this->expectException(ForbiddenException::class);

        $this->service->deleteTask($task->id, $user2->id);
    }

    /** @test */
    public function it_can_toggle_task_completion()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => false,
        ]);

        $toggledTask = $this->service->toggleTask($task->id, $user->id);

        $this->assertTrue($toggledTask->is_completed);
        $this->assertTrue($toggledTask->fresh()->is_completed);
    }

    /** @test */
    public function it_can_toggle_task_completion_from_completed_to_pending()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask([
            'user_id' => $user->id,
            'category_id' => $category->id,
            'is_completed' => true,
        ]);

        $toggledTask = $this->service->toggleTask($task->id, $user->id);

        $this->assertFalse($toggledTask->is_completed);
        $this->assertFalse($toggledTask->fresh()->is_completed);
    }

    /** @test */
    public function it_throws_exception_when_toggling_task_without_authentication()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        $task = $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);

        $this->expectException(UnauthorizedException::class);
        $this->expectExceptionMessage('Please sign in to complete tasks.');

        $this->service->toggleTask($task->id, null);
    }

    /** @test */
    public function it_throws_exception_when_toggling_non_existent_task()
    {
        $user = $this->createUser();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Task not found.');

        $this->service->toggleTask(999, $user->id);
    }

    /** @test */
    public function it_throws_exception_when_user_does_not_own_task_for_toggle()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user1->id]);
        $task = $this->createTask(['user_id' => $user1->id, 'category_id' => $category->id]);

        $this->expectException(ForbiddenException::class);

        $this->service->toggleTask($task->id, $user2->id);
    }

    /** @test */
    public function it_converts_priority_string_to_integer()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $taskData = [
            'title' => 'High Priority Task',
            'priority' => 'high',
        ];

        $task = $this->service->createTask($taskData, $user->id);
        $this->assertEquals(3, $task->priority);

        $taskData['priority'] = 'medium';
        $task = $this->service->createTask($taskData, $user->id);
        $this->assertEquals(2, $task->priority);

        $taskData['priority'] = 'low';
        $task = $this->service->createTask($taskData, $user->id);
        $this->assertEquals(1, $task->priority);
    }

    /** @test */
    public function it_combines_date_and_time_for_start_at()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $taskData = [
            'title' => 'Task with date',
            'start_date' => '2024-01-15',
            'start_time' => '10:30',
        ];

        $task = $this->service->createTask($taskData, $user->id);

        $this->assertNotNull($task->start_at);
        $this->assertEquals('2024-01-15 10:30:00', $task->start_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_combines_date_and_time_for_due_at()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $taskData = [
            'title' => 'Task with due date',
            'due_date' => '2024-01-20',
            'due_time' => '18:00',
        ];

        $task = $this->service->createTask($taskData, $user->id);

        $this->assertNotNull($task->due_at);
        $this->assertEquals('2024-01-20 18:00:00', $task->due_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_uses_default_time_for_due_at_when_time_not_provided()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $taskData = [
            'title' => 'Task with due date only',
            'due_date' => '2024-01-20',
        ];

        $task = $this->service->createTask($taskData, $user->id);

        $this->assertNotNull($task->due_at);
        $this->assertEquals('2024-01-20 23:59:00', $task->due_at->format('Y-m-d H:i:s'));
    }

    /** @test */
    public function it_resolves_category_id_from_category_name()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id, 'name' => 'Work']);

        $taskData = [
            'title' => 'Task with category name',
            'category' => 'Work',
        ];

        $task = $this->service->createTask($taskData, $user->id);

        $this->assertEquals($category->id, $task->category_id);
    }

}

