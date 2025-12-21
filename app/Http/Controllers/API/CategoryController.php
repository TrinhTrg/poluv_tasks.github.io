<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getCategories();

        return response()->json($categories);
    }

    /**
     * Store a newly created category.
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->createCategory($request->validated());

        return response()->json($category, 201);
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category): JsonResponse
    {
        try {
            $category = $this->categoryService->getCategory($category->id);

            return response()->json($category);
        } catch (\App\Exceptions\ForbiddenException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\App\Exceptions\NotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified category.
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        try {
            $category = $this->categoryService->updateCategory($category->id, $request->validated());

            return response()->json($category);
        } catch (\App\Exceptions\ForbiddenException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\App\Exceptions\NotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified category.
     */
    public function destroy(Category $category): JsonResponse
    {
        try {
            $this->categoryService->deleteCategory($category->id);

            return response()->json([
                'message' => 'Category deleted successfully.',
            ]);
        } catch (\App\Exceptions\ForbiddenException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 403);
        } catch (\App\Exceptions\NotFoundException $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
