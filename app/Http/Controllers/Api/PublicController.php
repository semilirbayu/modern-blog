<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function posts(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'category' => ['sometimes', 'string', 'max:255'],
            'search' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Post::published()
            ->select(['id', 'title', 'slug', 'excerpt', 'status', 'category_id', 'user_id', 'published_at', 'created_at', 'updated_at'])
            ->with(['user:id,name', 'category:id,name,slug'])
            ->latest('published_at');

        // Filter by category if provided
        if (isset($validated['category'])) {
            $query->whereHas('category', function ($q) use ($validated) {
                $q->where('slug', $validated['category']);
            });
        }

        // Filter by search term if provided
        if (isset($validated['search'])) {
            $searchTerm = $validated['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'LIKE', "%{$searchTerm}%");
            });
        }

        $perPage = min($validated['per_page'] ?? 10, 100);
        $posts = $query->paginate($perPage);

        return response()->json([
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }

    public function postBySlug(string $slug): JsonResponse
    {
        $post = Post::published()
            ->with(['user:id,name', 'category:id,name,slug'])
            ->where('slug', $slug)
            ->first();

        if (!$post) {
            return response()->json([
                'message' => 'Post not found',
            ], 404);
        }

        return response()->json([
            'data' => $post,
        ]);
    }

    public function categories(): JsonResponse
    {
        $categories = Category::withCount(['posts' => function ($query) {
            $query->where('status', 'published');
        }])
        ->whereHas('posts', function ($query) {
            $query->where('status', 'published');
        })
        ->orderBy('name')
        ->get();

        return response()->json([
            'data' => $categories,
        ]);
    }

    public function categoryBySlug(string $slug): JsonResponse
    {
        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        // Get published posts for this category
        $posts = Post::published()
            ->select(['id', 'title', 'slug', 'excerpt', 'published_at'])
            ->where('category_id', $category->id)
            ->latest('published_at')
            ->paginate(10);

        return response()->json([
            'id' => $category->id,
            'name' => $category->name,
            'slug' => $category->slug,
            'description' => $category->description,
            'posts' => $posts,
        ]);
    }

    public function postsByCategory(Request $request, string $slug): JsonResponse
    {
        $validated = $request->validate([
            'search' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $category = Category::where('slug', $slug)->first();

        if (!$category) {
            return response()->json([
                'message' => 'Category not found',
            ], 404);
        }

        $query = Post::published()
            ->select(['id', 'title', 'slug', 'excerpt', 'status', 'category_id', 'user_id', 'published_at', 'created_at', 'updated_at'])
            ->with(['user:id,name', 'category:id,name,slug'])
            ->where('category_id', $category->id)
            ->latest('published_at');

        // Filter by search term if provided
        if (isset($validated['search'])) {
            $searchTerm = $validated['search'];
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('content', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('excerpt', 'LIKE', "%{$searchTerm}%");
            });
        }

        $perPage = min($validated['per_page'] ?? 10, 100);
        $posts = $query->paginate($perPage);

        return response()->json([
            'category' => $category,
            'data' => $posts->items(),
            'meta' => [
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'per_page' => $posts->perPage(),
                'total' => $posts->total(),
            ],
        ]);
    }
}