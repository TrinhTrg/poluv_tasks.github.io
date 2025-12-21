<?php

namespace App\Services;

use App\Exceptions\ForbiddenException;
use App\Exceptions\NotFoundException;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CategoryService
{
    /**
     * Get all categories for current user
     */
    public function getCategories(?int $userId = null): Collection
    {
        $userId = $userId ?? $this->getUserId();

        $cacheKey = 'categories:user:' . ($userId ?? 'guest');
        $cacheTtl = $userId ? 60 : 120; // Cache for 60s (authenticated) or 120s (guest)

        return Cache::remember($cacheKey, $cacheTtl, function () use ($userId) {
            return Category::query()
                ->when($userId !== null, function ($q) use ($userId) {
                    return $q->where('user_id', $userId);
                }, function ($q) {
                    // Guest mode: categories with null user_id or user_id = 1
                    return $q->where(function ($query) {
                        $query->whereNull('user_id')
                            ->orWhere('user_id', 1);
                    });
                })
                ->orderBy('id', 'desc')
                ->get();
        });
    }

    /**
     * Get a single category by ID
     *
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function getCategory(int $categoryId, ?int $userId = null): Category
    {
        $userId = $userId ?? $this->getUserId();
        
        $category = Category::find($categoryId);
        
        if (!$category) {
            throw new NotFoundException('Category not found.');
        }

        $this->checkOwnership($category, $userId);

        return $category;
    }

    /**
     * Create a new category
     */
    public function createCategory(array $data, ?int $userId = null): Category
    {
        $userId = $userId ?? $this->getUserId();

        $category = Category::create([
            'user_id' => $userId,
            'name' => $data['name'],
            'color' => $data['color'],
        ]);

        // Clear cache for this user's categories
        $this->clearUserCategoriesCache($userId);

        return $category;
    }

    /**
     * Update an existing category
     *
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function updateCategory(int $categoryId, array $data, ?int $userId = null): Category
    {
        $userId = $userId ?? $this->getUserId();
        
        $category = Category::find($categoryId);
        
        if (!$category) {
            throw new NotFoundException('Category not found.');
        }

        $this->checkOwnership($category, $userId);

        if (isset($data['name'])) {
            $category->name = $data['name'];
        }
        if (isset($data['color'])) {
            $category->color = $data['color'];
        }

        $category->save();

        // Clear cache for this user's categories
        $this->clearUserCategoriesCache($userId);

        return $category;
    }

    /**
     * Delete a category
     *
     * @throws ForbiddenException
     * @throws NotFoundException
     */
    public function deleteCategory(int $categoryId, ?int $userId = null): bool
    {
        $userId = $userId ?? $this->getUserId();
        
        $category = Category::find($categoryId);
        
        if (!$category) {
            throw new NotFoundException('Category not found.');
        }

        $this->checkOwnership($category, $userId);

        $userId = $category->user_id;
        $deleted = $category->delete();

        // Clear cache for this user's categories
        if ($deleted) {
            $this->clearUserCategoriesCache($userId);
        }

        return $deleted;
    }

    /**
     * Check if user owns the category
     *
     * @throws ForbiddenException
     */
    protected function checkOwnership(Category $category, ?int $userId): void
    {
        if ($userId !== null && $category->user_id !== $userId) {
            throw new ForbiddenException('You do not have permission to access this category.');
        }
        
        if ($userId === null && $category->user_id !== null && $category->user_id !== 1) {
            throw new ForbiddenException('You do not have permission to access this category.');
        }
    }

    /**
     * Get current user ID or null
     */
    protected function getUserId(): ?int
    {
        return Auth::check() ? Auth::id() : null;
    }

    /**
     * Clear cache for user's categories
     */
    protected function clearUserCategoriesCache(?int $userId): void
    {
        $cacheKey = 'categories:user:' . ($userId ?? 'guest');
        Cache::forget($cacheKey);
    }
}

