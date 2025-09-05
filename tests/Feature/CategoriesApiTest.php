<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class CategoriesApiTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
    }

    /** @test */
    public function authenticated_user_can_get_all_categories()
    {
        Sanctum::actingAs($this->user);
        
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'slug', 'description', 'created_at']
            ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_admin_categories()
    {
        $response = $this->getJson('/api/categories');

        $response->assertStatus(401);
    }

    /** @test */
    public function authenticated_user_can_create_category()
    {
        Sanctum::actingAs($this->user);

        $categoryData = [
            'name' => 'Technology',
            'description' => 'Technology related posts',
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
            ->assertJson([
                'name' => 'Technology',
                'slug' => 'technology',
                'description' => 'Technology related posts',
            ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Technology',
            'slug' => 'technology',
        ]);
    }

    /** @test */
    public function category_creation_requires_valid_data()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/categories', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function category_name_must_be_unique()
    {
        Sanctum::actingAs($this->user);
        
        Category::factory()->create(['name' => 'Technology']);

        $response = $this->postJson('/api/categories', [
            'name' => 'Technology',
            'description' => 'Another tech category',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    /** @test */
    public function category_creation_generates_unique_slug()
    {
        Sanctum::actingAs($this->user);
        
        // Create first category
        Category::factory()->create(['name' => 'Technology', 'slug' => 'technology']);

        $response = $this->postJson('/api/categories', [
            'name' => 'Technology Blog',
        ]);

        $response->assertStatus(201);
        $this->assertEquals('technology-blog', $response->json('slug'));
    }

    /** @test */
    public function authenticated_user_can_get_single_category()
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create();

        $response = $this->getJson('/api/categories/' . $category->id);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ]);
    }

    /** @test */
    public function authenticated_user_can_update_category()
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create();

        $updateData = [
            'name' => 'Updated Category',
            'description' => 'Updated description',
        ];

        $response = $this->putJson('/api/categories/' . $category->id, $updateData);

        $response->assertStatus(200)
            ->assertJson([
                'name' => 'Updated Category',
                'slug' => 'updated-category',
                'description' => 'Updated description',
            ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Updated Category',
        ]);
    }

    /** @test */
    public function authenticated_user_can_delete_category()
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create();

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(204);
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    /** @test */
    public function cannot_delete_category_with_posts()
    {
        Sanctum::actingAs($this->user);
        
        $category = Category::factory()->create();
        Post::factory()->create(['category_id' => $category->id]);

        $response = $this->deleteJson('/api/categories/' . $category->id);

        $response->assertStatus(409)
            ->assertJson([
                'message' => 'Cannot delete category with existing posts'
            ]);

        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    /** @test */
    public function public_can_get_all_categories()
    {
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/public/categories');

        $response->assertStatus(200)
            ->assertJsonCount(3)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'slug', 'description']
            ]);
    }

    /** @test */
    public function public_can_get_category_with_published_posts()
    {
        $category = Category::factory()->create();
        
        // Create published and draft posts
        Post::factory()->create(['category_id' => $category->id, 'status' => 'published']);
        Post::factory()->create(['category_id' => $category->id, 'status' => 'draft']);

        $response = $this->getJson('/api/public/categories/' . $category->slug);

        $response->assertStatus(200)
            ->assertJson([
                'id' => $category->id,
                'name' => $category->name,
                'slug' => $category->slug,
            ])
            ->assertJsonCount(1, 'posts.data') // Only published posts
            ->assertJsonStructure([
                'posts' => [
                    'data' => [
                        '*' => ['id', 'title', 'slug', 'excerpt', 'published_at']
                    ]
                ]
            ]);
    }

    /** @test */
    public function public_category_posts_are_paginated()
    {
        $category = Category::factory()->create();
        
        Post::factory()->count(25)->create([
            'category_id' => $category->id,
            'status' => 'published'
        ]);

        $response = $this->getJson('/api/public/categories/' . $category->slug);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'posts' => [
                    'data',
                    'links',
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]
            ]);
    }

    /** @test */
    public function nonexistent_category_returns_404()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/categories/999');

        $response->assertStatus(404);
    }

    /** @test */
    public function public_nonexistent_category_returns_404()
    {
        $response = $this->getJson('/api/public/categories/nonexistent-slug');

        $response->assertStatus(404);
    }

    /** @test */
    public function categories_with_post_count()
    {
        Sanctum::actingAs($this->user);
        
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();
        
        Post::factory()->count(3)->create(['category_id' => $category1->id]);
        Post::factory()->count(1)->create(['category_id' => $category2->id]);

        $response = $this->getJson('/api/categories?with_counts=true');

        $response->assertStatus(200)
            ->assertJsonStructure([
                '*' => ['id', 'name', 'posts_count']
            ]);
    }

    /** @test */
    public function categories_can_be_searched()
    {
        Sanctum::actingAs($this->user);
        
        Category::factory()->create(['name' => 'Technology']);
        Category::factory()->create(['name' => 'Science']);
        Category::factory()->create(['name' => 'Arts']);

        $response = $this->getJson('/api/categories?search=tech');

        $response->assertStatus(200)
            ->assertJsonCount(1);
    }

    /** @test */
    public function categories_can_be_sorted()
    {
        Sanctum::actingAs($this->user);
        
        Category::factory()->create(['name' => 'Zebra']);
        Category::factory()->create(['name' => 'Apple']);

        $response = $this->getJson('/api/categories?sort=name');

        $response->assertStatus(200);
        
        $categories = $response->json();
        $this->assertEquals('Apple', $categories[0]['name']);
        $this->assertEquals('Zebra', $categories[1]['name']);
    }
}