<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;

class TaskControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_get_their_tasks()
    {
        $user = $this->createUser();
        $task1 = $this->createTask(['user_id' => $user->id, 'title' => 'Task 1']);
        $task2 = $this->createTask(['user_id' => $user->id, 'title' => 'Task 2']);
        $otherUser = $this->createUser();
        $task3 = $this->createTask(['user_id' => $otherUser->id, 'title' => 'Other Task']);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks');

        $response->assertStatus(200);
        $response->assertJsonCount(2, 'data');
        $response->assertJsonFragment(['title' => 'Task 1']);
        $response->assertJsonFragment(['title' => 'Task 2']);
        $response->assertJsonMissing(['title' => 'Other Task']);
    }

    /** @test */
    public function user_can_search_tasks_by_title()
    {
        $user = $this->createUser();
        $task1 = $this->createTask(['user_id' => $user->id, 'title' => 'Find this task']);
        $task2 = $this->createTask(['user_id' => $user->id, 'title' => 'Another task']);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?search=Find this');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Find this task']);
        $response->assertJsonMissing(['title' => 'Another task']);
    }

    /** @test */
    public function user_can_search_tasks_by_description()
    {
        $user = $this->createUser();
        $task1 = $this->createTask([
            'user_id' => $user->id,
            'title' => 'Task 1',
            'description' => 'This is a test description'
        ]);
        $task2 = $this->createTask([
            'user_id' => $user->id,
            'title' => 'Task 2',
            'description' => 'Different description'
        ]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?search=test description');

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Task 1']);
        $response->assertJsonMissing(['title' => 'Task 2']);
    }

    /** @test */
    public function user_can_filter_tasks_by_category()
    {
        $user = $this->createUser();
        $category1 = $this->createCategory(['user_id' => $user->id, 'name' => 'Work']);
        $category2 = $this->createCategory(['user_id' => $user->id, 'name' => 'Personal']);
        
        $task1 = $this->createTask(['user_id' => $user->id, 'category_id' => $category1->id]);
        $task2 = $this->createTask(['user_id' => $user->id, 'category_id' => $category2->id]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?category_id=' . $category1->id);

        $response->assertStatus(200);
        $response->assertJsonFragment(['category_id' => $category1->id]);
        $response->assertJsonMissing(['category_id' => $category2->id]);
    }

    /** @test */
    public function user_can_filter_tasks_by_status_completed()
    {
        $user = $this->createUser();
        $task1 = $this->createTask(['user_id' => $user->id, 'is_completed' => true]);
        $task2 = $this->createTask(['user_id' => $user->id, 'is_completed' => false]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?status=completed');

        $response->assertStatus(200);
        $response->assertJsonFragment(['is_completed' => true]);
        $response->assertJsonMissing(['is_completed' => false]);
    }

    /** @test */
    public function user_can_filter_tasks_by_status_pending()
    {
        $user = $this->createUser();
        $task1 = $this->createTask(['user_id' => $user->id, 'is_completed' => true]);
        $task2 = $this->createTask(['user_id' => $user->id, 'is_completed' => false]);

        $response = $this->actingAs($user)->getJson('/api/v1/tasks?status=pending');

        $response->assertStatus(200);
        $response->assertJsonFragment(['is_completed' => false]);
        $response->assertJsonMissing(['is_completed' => true]);
    }

    /** @test */
    public function authenticated_user_can_create_task()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id, 'name' => 'Work']);

        $response = $this->actingAs($user)->postJson('/api/v1/tasks', [
            'title' => 'New Task',
            'description' => 'Task description',
            'category' => 'Work',
            'priority' => 'high',
            'due_date' => '2024-12-31',
            'due_time' => '18:00',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['title' => 'New Task']);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'New Task',
            'priority' => 3, // high = 3
        ]);
    }

    /** @test */
    public function guest_cannot_create_task()
    {
        $response = $this->postJson('/api/v1/tasks', [
            'title' => 'New Task',
        ]);

        $response->assertStatus(401);
        $response->assertJson(['message' => 'Unauthorized. Please sign in to create tasks.']);
    }

    /** @test */
    public function user_cannot_create_task_without_title()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/v1/tasks', [
            'description' => 'Task without title',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /** @test */
    public function authenticated_user_can_update_their_task()
    {
        $user = $this->createUser();
        $task = $this->createTask(['user_id' => $user->id, 'title' => 'Old Title']);

        $response = $this->actingAs($user)->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated Title',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Updated Title']);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_task()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $task = $this->createTask(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Hacked Title',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
            'title' => 'Hacked Title',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_their_task()
    {
        $user = $this->createUser();
        $task = $this->createTask(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_task()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $task = $this->createTask(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->deleteJson("/api/v1/tasks/{$task->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('tasks', ['id' => $task->id]);
    }

    /** @test */
    public function authenticated_user_can_toggle_task_completion()
    {
        $user = $this->createUser();
        $task = $this->createTask(['user_id' => $user->id, 'is_completed' => false]);

        $response = $this->actingAs($user)->postJson("/api/v1/tasks/{$task->id}/toggle");

        $response->assertStatus(200);
        $this->assertTrue($task->fresh()->is_completed);

        // Toggle again
        $response = $this->actingAs($user)->postJson("/api/v1/tasks/{$task->id}/toggle");
        $this->assertFalse($task->fresh()->is_completed);
    }

    /** @test */
    public function user_cannot_toggle_other_users_task()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $task = $this->createTask(['user_id' => $user2->id, 'is_completed' => false]);

        $response = $this->actingAs($user1)->postJson("/api/v1/tasks/{$task->id}/toggle");

        $response->assertStatus(403);
        $this->assertFalse($task->fresh()->is_completed);
    }
}

