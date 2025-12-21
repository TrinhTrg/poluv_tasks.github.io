<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Performance Benchmark Tests
 * Simple tests to verify API response times and query efficiency
 */
class PerformanceBenchmarkTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function tasks_list_endpoint_responds_quickly()
    {
        $user = $this->createUser();
        
        // Create some test data
        $this->createTask(['user_id' => $user->id]);
        $this->createTask(['user_id' => $user->id]);
        $this->createTask(['user_id' => $user->id]);

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->getJson('/api/v1/tasks');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000; // Convert to milliseconds

        $response->assertStatus(200);
        $this->assertLessThan(500, $responseTime, 'Tasks endpoint should respond in less than 500ms');
    }

    /** @test */
    public function categories_list_endpoint_responds_quickly()
    {
        $user = $this->createUser();
        
        $this->createCategory(['user_id' => $user->id]);
        $this->createCategory(['user_id' => $user->id]);

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->getJson('/api/v1/categories');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(500, $responseTime, 'Categories endpoint should respond in less than 500ms');
    }

    /** @test */
    public function task_creation_endpoint_responds_quickly()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->postJson('/api/v1/tasks', [
            'title' => 'Performance Test Task',
            'category' => $category->name,
            'priority' => 'high',
        ]);
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(201);
        $this->assertLessThan(400, $responseTime, 'Task creation should complete in less than 400ms');
    }

    /** @test */
    public function search_endpoint_handles_queries_efficiently()
    {
        $user = $this->createUser();
        
        // Create multiple tasks
        for ($i = 0; $i < 10; $i++) {
            $this->createTask([
                'user_id' => $user->id,
                'title' => "Task {$i}",
            ]);
        }

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->getJson('/api/v1/tasks?search=Task');
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(500, $responseTime, 'Search should complete in less than 500ms');
    }

    /** @test */
    public function filtering_by_category_is_efficient()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);
        
        // Create tasks with and without category
        $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);
        $this->createTask(['user_id' => $user->id, 'category_id' => $category->id]);
        $this->createTask(['user_id' => $user->id]); // No category

        $startTime = microtime(true);
        
        $response = $this->actingAs($user)->getJson("/api/v1/tasks?category_id={$category->id}");
        
        $endTime = microtime(true);
        $responseTime = ($endTime - $startTime) * 1000;

        $response->assertStatus(200);
        $this->assertLessThan(400, $responseTime, 'Category filter should complete in less than 400ms');
    }

    /** @test */
    public function bulk_operations_are_efficient()
    {
        $user = $this->createUser();
        $tasks = [];
        
        // Create multiple tasks
        for ($i = 0; $i < 5; $i++) {
            $tasks[] = $this->createTask(['user_id' => $user->id]);
        }

        $startTime = microtime(true);
        
        // Toggle multiple tasks
        foreach ($tasks as $task) {
            $this->actingAs($user)->postJson("/api/v1/tasks/{$task->id}/toggle");
        }
        
        $endTime = microtime(true);
        $totalTime = ($endTime - $startTime) * 1000;

        $this->assertLessThan(2000, $totalTime, 'Bulk operations should complete in less than 2 seconds');
    }
}

