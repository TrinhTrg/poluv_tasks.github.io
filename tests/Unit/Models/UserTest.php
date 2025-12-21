<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\User;
use App\Models\Task;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_have_many_tasks()
    {
        $user = $this->createUser();
        $task1 = $this->createTask(['user_id' => $user->id]);
        $task2 = $this->createTask(['user_id' => $user->id]);

        $this->assertCount(2, $user->tasks);
        $this->assertTrue($user->tasks->contains($task1));
        $this->assertTrue($user->tasks->contains($task2));
    }

    /** @test */
    public function user_can_have_many_categories()
    {
        $user = $this->createUser();
        $category1 = $this->createCategory(['user_id' => $user->id]);
        $category2 = $this->createCategory(['user_id' => $user->id]);

        $this->assertCount(2, $user->categories);
        $this->assertTrue($user->categories->contains($category1));
        $this->assertTrue($user->categories->contains($category2));
    }

    /** @test */
    public function user_password_is_hashed()
    {
        $user = User::factory()->create([
            'password' => 'plaintext-password'
        ]);

        $this->assertNotEquals('plaintext-password', $user->password);
        $this->assertTrue(\Hash::check('plaintext-password', $user->password));
    }

    /** @test */
    public function user_password_is_not_visible_in_array()
    {
        $user = $this->createUser();
        $userArray = $user->toArray();

        $this->assertArrayNotHasKey('password', $userArray);
        $this->assertArrayNotHasKey('remember_token', $userArray);
    }

    /** @test */
    public function user_can_be_created_with_required_fields()
    {
        $user = User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
        ]);
    }
}

