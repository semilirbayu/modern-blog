<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PostsApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_get_all_posts()
    {
        Sanctum::actingAs($this->user);
        
        Post::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonCount(3, 'data')
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'content', 'excerpt', 'status', 'created_at', 'user', 'category']
                ],
                'links',
                'meta'
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_admin_posts()
    {
        $response = $this->getJson('/api/posts');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_create_post()
    {
        Sanctum::actingAs($this->user);

        $postData = [
            'title' => 'Test Post',
            'content' => 'This is test content',
            'excerpt' => 'This is excerpt',
            'status' => 'draft',
            'category_id' => $this->category->id,
        ];

        $response = $this->postJson('/api/posts', $postData);

        $response->assertStatus(201)
            ->assertJson([
                'title' => 'Test Post',
                'slug' => 'test-post',
                'content' => 'This is test content',
                'status' => 'draft',
                'user_id' => $this->user->id,
            ]);

        $this->assertDatabaseHas('posts', [
            'title' => 'Test Post',
            'slug' => 'test-post',
            'user_id' => $this->user->id,
        ]);
    }

    /** @test */
    public function post_creation_requires_valid_data()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/posts', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title', 'content']);
    }

    /** @test */
    public function post_creation_generates_unique_slug()
    {
        Sanctum::actingAs($this->user);
        
        // Create first post
        Post::factory()->create(['title' => 'Test Post', 'slug' => 'test-post']);

        $response = $this->postJson('/api/posts', [
            'title' => 'Test Post',
            'content' => 'Different content',
            'category_id' => $this->category->id,
        ]);

        $response->assertStatus(201);
        $this->assertEquals('test-post-2', $response->json('slug'));
    }

    /** @test */
    public function authenticated_user_can_get_single_post()
    {
        Sanctum::actingAs($this->user);
        
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts/' . $post->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'title' => $post->title,
                'slug' => $post->slug,
            ]);
    }

    /** @test */
    public function authenticated_user_can_update_own_post()
    {
        Sanctum::actingAs($this->user);
        
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'title' => 'Updated Title',
            'content' => 'Updated content',
            'status' => 'published',
        ];

        $response = $this->putJson('/api/posts/' . $post->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'title' => 'Updated Title',
                'content' => 'Updated content',
                'status' => 'published',
            ]);

        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_own_post()
    {
        Sanctum::actingAs($this->user);
        
        $post = Post::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson('/api/posts/' . $post->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('posts', ['id' => $post->id]);
    }

    /** @test */
    public function posts_can_be_filtered_by_status()
    {
        Sanctum::actingAs($this->user);
        
        Post::factory()->create(['status' => 'published', 'user_id' => $this->user->id]);
        Post::factory()->create(['status' => 'draft', 'user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts?status=published');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function posts_can_be_filtered_by_category()
    {
        Sanctum::actingAs($this->user);
        
        $category2 = Category::factory()->create();
        
        Post::factory()->create(['category_id' => $this->category->id, 'user_id' => $this->user->id]);
        Post::factory()->create(['category_id' => $category2->id, 'user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts?category_id=' . $this->category->id);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function posts_can_be_searched()
    {
        Sanctum::actingAs($this->user);
        
        Post::factory()->create(['title' => 'Laravel Tutorial', 'user_id' => $this->user->id]);
        Post::factory()->create(['title' => 'Vue.js Guide', 'user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts?search=Laravel');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function posts_are_paginated()
    {
        Sanctum::actingAs($this->user);
        
        Post::factory()->count(25)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/posts');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'links' => ['first', 'last', 'prev', 'next'],
                'meta' => ['current_page', 'from', 'last_page', 'per_page', 'to', 'total']
            ]);
    }

    /** @test */
    public function public_can_get_published_posts()
    {
        Post::factory()->create(['status' => 'published']);
        Post::factory()->create(['status' => 'draft']);

        $response = $this->getJson('/api/public/posts');

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    /** @test */
    public function public_can_get_published_post_by_slug()
    {
        $post = Post::factory()->create(['status' => 'published', 'slug' => 'test-post']);

        $response = $this->getJson('/api/public/posts/test-post');

        $response->assertStatus(200)
            ->assertJson([
                'id' => $post->id,
                'title' => $post->title,
                'slug' => 'test-post',
            ]);
    }

    /** @test */
    public function public_cannot_access_draft_posts()
    {
        $post = Post::factory()->create(['status' => 'draft', 'slug' => 'draft-post']);

        $response = $this->getJson('/api/public/posts/draft-post');

        $response->assertStatus(404);
    }

    /** @test */
    public function published_posts_increment_view_count()
    {
        $post = Post::factory()->create(['status' => 'published']);

        $this->getJson('/api/public/posts/' . $post->slug);
        $this->getJson('/api/public/posts/' . $post->slug);

        $post->refresh();
        $this->assertEquals(2, $post->views ?? 0);
    }

    /** @test */
    public function post_shows_related_posts()
    {
        $category = Category::factory()->create();
        $mainPost = Post::factory()->create(['status' => 'published', 'category_id' => $category->id]);
        Post::factory()->count(3)->create(['status' => 'published', 'category_id' => $category->id]);

        $response = $this->getJson('/api/public/posts/' . $mainPost->slug);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'related_posts' => [
                    '*' => ['id', 'title', 'slug', 'excerpt']
                ]
            ]);
    }
}