<?php

namespace App\Observers;

use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Category Model Observer
 * 
 * Handles automatic cache invalidation when categories are modified.
 * This ensures cache consistency across the application.
 */
class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        $this->clearCategoryCache($category);
        Log::debug('CategoryObserver: Cache cleared on create', ['category_id' => $category->id]);
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        $this->clearCategoryCache($category);
        Log::debug('CategoryObserver: Cache cleared on update', ['category_id' => $category->id]);
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        $this->clearCategoryCache($category);
        Log::debug('CategoryObserver: Cache cleared on delete', ['category_id' => $category->id]);
    }

    /**
     * Clear all category-related caches for the user.
     */
    protected function clearCategoryCache(Category $category): void
    {
        $userId = $category->user_id ?? 'guest';
        
        // Clear categories cache for this user
        Cache::forget('categories:user:' . $userId);
        
        // Clear homepage tasks cache (since tasks display category info)
        Cache::forget('homepage:tasks:user:' . $userId);
        
        // Try to use cache tags if supported (Redis, Memcached)
        try {
            if (method_exists(Cache::getStore(), 'tags')) {
                Cache::tags(['categories', 'user:' . $userId])->flush();
            }
        } catch (\Exception $e) {
            // Tags not supported, manual clearing above handles it
        }
    }
}
