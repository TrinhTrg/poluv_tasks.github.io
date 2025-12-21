<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * E2E Tests for Modal Interactions
 * Tests the API endpoints that modals use for task and category operations
 */
class ModalInteractionTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function modal_can_create_task_via_api()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id, 'name' => 'Work']);

        $response = $this->actingAs($user)->postJson('/api/v1/tasks', [
            'title' => 'Modal Test Task',
            'description' => 'Created from modal',
            'category' => 'Work',
            'priority' => 'high',
            'color' => '#FFB6C1',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['title' => 'Modal Test Task']);
        $this->assertDatabaseHas('tasks', [
            'user_id' => $user->id,
            'title' => 'Modal Test Task',
        ]);
    }

    /** @test */
    public function modal_can_update_task_via_api()
    {
        $user = $this->createUser();
        $task = $this->createTask(['user_id' => $user->id, 'title' => 'Original Title']);

        $response = $this->actingAs($user)->putJson("/api/v1/tasks/{$task->id}", [
            'title' => 'Updated from Modal',
            'priority' => 'medium',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['title' => 'Updated from Modal']);
        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => 'Updated from Modal',
        ]);
    }

    /** @test */
    public function modal_can_create_category_via_api()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/v1/categories', [
            'name' => 'Modal Category',
            'color' => '#FF0000',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'Modal Category']);
        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Modal Category',
        ]);
    }

    /** @test */
    public function modal_can_update_category_via_api()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id, 'name' => 'Old Name']);

        $response = $this->actingAs($user)->putJson("/api/v1/categories/{$category->id}", [
            'name' => 'Updated from Modal',
            'color' => '#00FF00',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated from Modal']);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated from Modal',
        ]);
    }

    /** @test */
    public function modal_shows_validation_errors_for_invalid_task_data()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/v1/tasks', [
            'description' => 'Task without title',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('title');
    }

    /** @test */
    public function modal_shows_validation_errors_for_invalid_category_data()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/v1/categories', [
            'name' => 'Category without color',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('color');
    }
}

