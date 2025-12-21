<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CategoryControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function authenticated_user_can_get_their_categories()
    {
        $user = $this->createUser();
        $category1 = $this->createCategory(['user_id' => $user->id, 'name' => 'Work']);
        $category2 = $this->createCategory(['user_id' => $user->id, 'name' => 'Personal']);
        $otherUser = $this->createUser();
        $category3 = $this->createCategory(['user_id' => $otherUser->id, 'name' => 'Other']);

        $response = $this->actingAs($user)->getJson('/api/v1/categories');

        $response->assertStatus(200);
        $response->assertJsonCount(2);
        $response->assertJsonFragment(['name' => 'Work']);
        $response->assertJsonFragment(['name' => 'Personal']);
        $response->assertJsonMissing(['name' => 'Other']);
    }

    /** @test */
    public function authenticated_user_can_create_category()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/v1/categories', [
            'name' => 'New Category',
            'color' => '#FF0000',
        ]);

        $response->assertStatus(201);
        $response->assertJsonFragment(['name' => 'New Category']);
        $this->assertDatabaseHas('categories', [
            'user_id' => $user->id,
            'name' => 'New Category',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function user_cannot_create_category_without_name()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/v1/categories', [
            'color' => '#FF0000',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('name');
    }

    /** @test */
    public function user_cannot_create_category_without_color()
    {
        $user = $this->createUser();

        $response = $this->actingAs($user)->postJson('/api/v1/categories', [
            'name' => 'New Category',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors('color');
    }

    /** @test */
    public function authenticated_user_can_update_their_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id, 'name' => 'Old Name']);

        $response = $this->actingAs($user)->putJson("/api/v1/categories/{$category->id}", [
            'name' => 'Updated Name',
            'color' => '#00FF00',
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment(['name' => 'Updated Name']);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'color' => '#00FF00',
        ]);
    }

    /** @test */
    public function user_cannot_update_other_users_category()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->putJson("/api/v1/categories/{$category->id}", [
            'name' => 'Hacked Name',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('categories', [
            'id' => $category->id,
            'name' => 'Hacked Name',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_their_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $response = $this->actingAs($user)->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function user_cannot_delete_other_users_category()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->deleteJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(403);
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function authenticated_user_can_view_their_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $response = $this->actingAs($user)->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(200);
        $response->assertJsonFragment(['id' => $category->id]);
    }

    /** @test */
    public function user_cannot_view_other_users_category()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user2->id]);

        $response = $this->actingAs($user1)->getJson("/api/v1/categories/{$category->id}");

        $response->assertStatus(403);
    }
}

