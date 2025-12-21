<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

abstract class TestCase extends BaseTestCase
{
    use RefreshDatabase;

    /**
     * Creates the application.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set default timezone for tests
        date_default_timezone_set('UTC');
    }

    /**
     * Helper: Create a test user
     */
    protected function createUser(array $attributes = []): \App\Models\User
    {
        return \App\Models\User::factory()->create($attributes);
    }

    /**
     * Helper: Create a test category
     */
    protected function createCategory(array $attributes = []): \App\Models\Category
    {
        return \App\Models\Category::factory()->create($attributes);
    }

    /**
     * Helper: Create a test task
     */
    protected function createTask(array $attributes = []): \App\Models\Task
    {
        return \App\Models\Task::factory()->create($attributes);
    }

    /**
     * Helper: Act as a user
     */
    protected function actingAsUser(\App\Models\User $user = null): self
    {
        if ($user === null) {
            $user = $this->createUser();
        }
        
        $this->actingAs($user);
        
        return $this;
    }
}
