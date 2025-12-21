<?php

namespace Tests\Unit\Models;

use Tests\TestCase;
use App\Models\Category;
use App\Models\User;
use App\Models\Task;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function category_belongs_to_user()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $category->user);
        $this->assertEquals($user->id, $category->user->id);
    }

    /** @test */
    public function category_can_have_many_tasks()
    {
        $category = $this->createCategory();
        $task1 = $this->createTask(['category_id' => $category->id]);
        $task2 = $this->createTask(['category_id' => $category->id]);

        $this->assertCount(2, $category->tasks);
        $this->assertTrue($category->tasks->contains($task1));
        $this->assertTrue($category->tasks->contains($task2));
    }

    /** @test */
    public function category_does_not_have_timestamps()
    {
        $category = $this->createCategory();

        $this->assertFalse($category->timestamps);
    }

    /** @test */
    public function category_can_be_created_with_required_fields()
    {
        $user = $this->createUser();
        
        $category = Category::factory()->create([
            'user_id' => $user->id,
            'name' => 'Work',
            'color' => '#FF0000',
        ]);

        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'Work',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function category_can_be_updated()
    {
        $category = $this->createCategory(['name' => 'Old Name']);

        $category->name = 'New Name';
        $category->color = '#00FF00';
        $category->save();

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Name',
            'color' => '#00FF00',
        ]);
    }
}

