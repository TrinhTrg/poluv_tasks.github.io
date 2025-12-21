<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    /**
     * Display a listing of categories.
     */
    public function index(Request $request): JsonResponse
    {
        $categories = $this->categoryService->getCategories();

        $response = response()->json($categories);
        
        // Set cache headers for API response (only for GET requests without auth)
        if (!$request->user() && $request->isMethod('GET')) {
            $response->headers->set('Cache-Control', 'public, max-age=60, must-revalidate');
            // Add ETag for cache validation
            $etag = md5($response->getContent());
            $response->setEtag($etag);
        }

        return $response;
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
