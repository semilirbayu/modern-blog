<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index(): JsonResponse
    {
        $categories = Category::withCount('posts')->orderBy('name')->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function store(CategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Generate slug from name if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            
            // Ensure slug is unique
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Category::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Retry mechanism for slug race conditions
        $maxAttempts = 3;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            try {
                $category = Category::create($data);
                break;
            } catch (QueryException $e) {
                $attempt++;
                
                // Check if it's a unique constraint violation for slug
                if ($e->getCode() === '23000' && $attempt < $maxAttempts) {
                    // Generate a new unique slug
                    $baseSlug = Str::slug($data['name']);
                    $data['slug'] = $baseSlug . '-' . time() . '-' . $attempt;
                    continue;
                }
                
                throw $e;
            }
        }

        return response()->json([
            'message' => 'Category created successfully',
            'data' => $category,
        ], 201);
    }

    public function show(Category $category): JsonResponse
    {
        $category->loadCount('posts');

        return response()->json([
            'data' => $category,
        ]);
    }

    public function update(CategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        // Generate slug from name if name is being updated and slug is not provided
        if (isset($data['name']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['name']);
            
            // Ensure slug is unique (excluding current category)
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Category::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        $category->update($data);

        return response()->json([
            'message' => 'Category updated successfully',
            'data' => $category,
        ]);
    }

    public function destroy(Category $category): JsonResponse
    {
        // Check if category has posts
        $postsCount = $category->posts()->count();
        
        if ($postsCount > 0) {
            return response()->json([
                'message' => 'Cannot delete category that has posts. Please reassign or delete the posts first.',
                'posts_count' => $postsCount,
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Category deleted successfully',
        ]);
    }
}