<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\CategoryService;
use App\Models\Category;
use App\Models\User;
use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;

class CategoryServiceTest extends TestCase
{
    use RefreshDatabase;

    protected CategoryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new CategoryService();
    }

    /** @test */
    public function it_can_get_categories_for_authenticated_user()
    {
        $user = $this->createUser();
        $category1 = $this->createCategory(['user_id' => $user->id, 'name' => 'Work']);
        $category2 = $this->createCategory(['user_id' => $user->id, 'name' => 'Personal']);

        $categories = $this->service->getCategories($user->id);

        $this->assertCount(2, $categories);
        $this->assertTrue($categories->contains($category1));
        $this->assertTrue($categories->contains($category2));
    }

    /** @test */
    public function it_returns_only_user_categories()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        
        $category1 = $this->createCategory(['user_id' => $user1->id]);
        $category2 = $this->createCategory(['user_id' => $user2->id]);

        $categories = $this->service->getCategories($user1->id);

        $this->assertCount(1, $categories);
        $this->assertTrue($categories->contains($category1));
        $this->assertFalse($categories->contains($category2));
    }

    /** @test */
    public function it_can_get_a_single_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $result = $this->service->getCategory($category->id, $user->id);

        $this->assertEquals($category->id, $result->id);
        $this->assertEquals($category->name, $result->name);
    }

    /** @test */
    public function it_throws_exception_when_category_not_found()
    {
        $user = $this->createUser();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Category not found.');

        $this->service->getCategory(999, $user->id);
    }

    /** @test */
    public function it_throws_exception_when_user_does_not_own_category()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user1->id]);

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You do not have permission to access this category.');

        $this->service->getCategory($category->id, $user2->id);
    }

    /** @test */
    public function it_can_create_a_category()
    {
        $user = $this->createUser();

        $categoryData = [
            'name' => 'New Category',
            'color' => '#FF0000',
        ];

        $category = $this->service->createCategory($categoryData, $user->id);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'user_id' => $user->id,
            'name' => 'New Category',
            'color' => '#FF0000',
        ]);
    }

    /** @test */
    public function it_can_update_a_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'color' => '#FF0000',
        ]);

        $updateData = [
            'name' => 'Updated Name',
            'color' => '#00FF00',
        ];

        $updatedCategory = $this->service->updateCategory($category->id, $updateData, $user->id);

        $this->assertEquals('Updated Name', $updatedCategory->name);
        $this->assertEquals('#00FF00', $updatedCategory->color);
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Name',
            'color' => '#00FF00',
        ]);
    }

    /** @test */
    public function it_can_update_category_partially()
    {
        $user = $this->createUser();
        $category = $this->createCategory([
            'user_id' => $user->id,
            'name' => 'Old Name',
            'color' => '#FF0000',
        ]);

        $updateData = [
            'name' => 'Updated Name Only',
        ];

        $updatedCategory = $this->service->updateCategory($category->id, $updateData, $user->id);

        $this->assertEquals('Updated Name Only', $updatedCategory->name);
        $this->assertEquals('#FF0000', $updatedCategory->color); // Color should remain unchanged
    }

    /** @test */
    public function it_throws_exception_when_updating_non_existent_category()
    {
        $user = $this->createUser();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Category not found.');

        $this->service->updateCategory(999, ['name' => 'Updated'], $user->id);
    }

    /** @test */
    public function it_throws_exception_when_user_does_not_own_category_for_update()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user1->id]);

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You do not have permission to access this category.');

        $this->service->updateCategory($category->id, ['name' => 'Updated'], $user2->id);
    }

    /** @test */
    public function it_can_delete_a_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $result = $this->service->deleteCategory($category->id, $user->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function it_throws_exception_when_deleting_non_existent_category()
    {
        $user = $this->createUser();

        $this->expectException(NotFoundException::class);
        $this->expectExceptionMessage('Category not found.');

        $this->service->deleteCategory(999, $user->id);
    }

    /** @test */
    public function it_throws_exception_when_user_does_not_own_category_for_delete()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();
        $category = $this->createCategory(['user_id' => $user1->id]);

        $this->expectException(ForbiddenException::class);
        $this->expectExceptionMessage('You do not have permission to access this category.');

        $this->service->deleteCategory($category->id, $user2->id);
    }

    /** @test */
    public function it_clears_cache_after_creating_category()
    {
        $user = $this->createUser();

        $categoryData = [
            'name' => 'New Category',
            'color' => '#FF0000',
        ];

        // Cache should be cleared after creation
        $category = $this->service->createCategory($categoryData, $user->id);

        // Verify category was created
        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'New Category',
        ]);
    }

    /** @test */
    public function it_clears_cache_after_updating_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $updateData = [
            'name' => 'Updated Name',
        ];

        $updatedCategory = $this->service->updateCategory($category->id, $updateData, $user->id);

        // Verify category was updated
        $this->assertEquals('Updated Name', $updatedCategory->name);
    }

    /** @test */
    public function it_clears_cache_after_deleting_category()
    {
        $user = $this->createUser();
        $category = $this->createCategory(['user_id' => $user->id]);

        $result = $this->service->deleteCategory($category->id, $user->id);

        // Verify category was deleted
        $this->assertTrue($result);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }
}

