<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'status' => ['sometimes', 'string', 'in:draft,published'],
            'category_id' => ['sometimes', 'integer', 'exists:categories,id'],
            'search' => ['sometimes', 'string', 'max:255'],
            'per_page' => ['sometimes', 'integer', 'min:1', 'max:100'],
        ]);

        $query = Post::with(['user:id,name', 'category:id,name,slug']);

        // Filter by status if provided
        if (isset($validated['status'])) {
            $query->where('status', $validated['status']);
        }

        // Filter by category if provided
        if (isset($validated['category_id'])) {
            $query->where('category_id', $validated['category_id']);
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

        // Order by latest first
        $query->latest();

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

    public function store(PostRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Generate slug from title if not provided
        if (!isset($data['slug']) || empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
            
            // Ensure slug is unique
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Post::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Set published_at if status is published
        if ($data['status'] === 'published' && !isset($data['published_at'])) {
            $data['published_at'] = now();
        }

        // Set user_id to authenticated user
        $data['user_id'] = auth()->id();

        // Retry mechanism for slug race conditions
        $maxAttempts = 3;
        $attempt = 0;
        
        while ($attempt < $maxAttempts) {
            try {
                $post = Post::create($data);
                break;
            } catch (QueryException $e) {
                $attempt++;
                
                // Check if it's a unique constraint violation for slug
                if ($e->getCode() === '23000' && $attempt < $maxAttempts) {
                    // Generate a new unique slug
                    $baseSlug = Str::slug($data['title']);
                    $data['slug'] = $baseSlug . '-' . time() . '-' . $attempt;
                    continue;
                }
                
                throw $e;
            }
        }

        // Load relationships for response
        $post->load(['user:id,name', 'category:id,name,slug']);

        return response()->json([
            'message' => 'Post created successfully',
            'data' => $post,
        ], 201);
    }

    public function show(Post $post): JsonResponse
    {
        $post->load(['user:id,name', 'category:id,name,slug']);

        return response()->json([
            'data' => $post,
        ]);
    }

    public function update(PostRequest $request, Post $post): JsonResponse
    {
        $data = $request->validated();

        // Generate slug from title if title is being updated and slug is not provided
        if (isset($data['title']) && (!isset($data['slug']) || empty($data['slug']))) {
            $data['slug'] = Str::slug($data['title']);
            
            // Ensure slug is unique (excluding current post)
            $originalSlug = $data['slug'];
            $counter = 1;
            while (Post::where('slug', $data['slug'])->where('id', '!=', $post->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }

        // Handle status changes
        if (isset($data['status'])) {
            if ($data['status'] === 'published' && $post->status !== 'published') {
                // Post is being published for the first time
                if (!isset($data['published_at'])) {
                    $data['published_at'] = now();
                }
            } elseif ($data['status'] === 'draft' && $post->status === 'published') {
                // Post is being unpublished
                $data['published_at'] = null;
            }
        }

        // Retry mechanism for slug race conditions (only if slug is being updated)
        if (isset($data['slug'])) {
            $maxAttempts = 3;
            $attempt = 0;
            
            while ($attempt < $maxAttempts) {
                try {
                    $post->update($data);
                    break;
                } catch (QueryException $e) {
                    $attempt++;
                    
                    // Check if it's a unique constraint violation for slug
                    if ($e->getCode() === '23000' && $attempt < $maxAttempts) {
                        // Generate a new unique slug
                        $baseSlug = isset($data['title']) ? Str::slug($data['title']) : Str::slug($post->title);
                        $data['slug'] = $baseSlug . '-' . time() . '-' . $attempt;
                        continue;
                    }
                    
                    throw $e;
                }
            }
        } else {
            $post->update($data);
        }

        // Load relationships for response
        $post->load(['user:id,name', 'category:id,name,slug']);

        return response()->json([
            'message' => 'Post updated successfully',
            'data' => $post,
        ]);
    }

    public function destroy(Post $post): JsonResponse
    {
        $post->delete();

        return response()->json([
            'message' => 'Post deleted successfully',
        ]);
    }
}