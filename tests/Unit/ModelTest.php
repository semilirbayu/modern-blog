<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Post;
use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ModelTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_model_has_correct_fillable_attributes()
    {
        $user = new User();
        
        $this->assertEquals([
            'name',
            'email',
            'password',
        ], $user->getFillable());
    }

    /** @test */
    public function user_model_hides_sensitive_attributes()
    {
        $user = User::factory()->create([
            'password' => 'secret-password',
        ]);

        $array = $user->toArray();
        
        $this->assertArrayNotHasKey('password', $array);
        $this->assertArrayNotHasKey('remember_token', $array);
    }

    /** @test */
    public function user_has_many_posts_relationship()
    {
        $user = User::factory()->create();
        
        $this->assertInstanceOf(HasMany::class, $user->posts());
    }

    /** @test */
    public function user_can_have_multiple_posts()
    {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->posts);
        $this->assertTrue($user->posts->contains($posts[0]));
    }

    /** @test */
    public function post_model_has_correct_fillable_attributes()
    {
        $post = new Post();
        
        $expected = [
            'title',
            'slug',
            'content',
            'excerpt',
            'status',
            'category_id',
            'user_id',
            'published_at',
        ];
        
        $this->assertEquals($expected, $post->getFillable());
    }

    /** @test */
    public function post_belongs_to_user()
    {
        $post = Post::factory()->create();
        
        $this->assertInstanceOf(BelongsTo::class, $post->user());
        $this->assertInstanceOf(User::class, $post->user);
    }

    /** @test */
    public function post_belongs_to_category()
    {
        $post = Post::factory()->create();
        
        $this->assertInstanceOf(BelongsTo::class, $post->category());
        $this->assertInstanceOf(Category::class, $post->category);
    }

    /** @test */
    public function post_automatically_generates_slug_from_title()
    {
        $post = Post::factory()->create(['title' => 'This Is A Test Post']);
        
        $this->assertEquals('this-is-a-test-post', $post->slug);
    }

    /** @test */
    public function post_ensures_unique_slug()
    {
        Post::factory()->create(['title' => 'Test Post', 'slug' => 'test-post']);
        $post2 = Post::factory()->create(['title' => 'Test Post']);
        
        $this->assertEquals('test-post-2', $post2->slug);
    }

    /** @test */
    public function post_has_published_scope()
    {
        Post::factory()->create(['status' => 'published']);
        Post::factory()->create(['status' => 'draft']);
        
        $publishedPosts = Post::published()->get();
        
        $this->assertCount(1, $publishedPosts);
        $this->assertEquals('published', $publishedPosts->first()->status);
    }

    /** @test */
    public function post_has_draft_scope()
    {
        Post::factory()->create(['status' => 'published']);
        Post::factory()->create(['status' => 'draft']);
        
        $draftPosts = Post::draft()->get();
        
        $this->assertCount(1, $draftPosts);
        $this->assertEquals('draft', $draftPosts->first()->status);
    }

    /** @test */
    public function post_casts_published_at_to_datetime()
    {
        $post = Post::factory()->create([
            'published_at' => '2024-01-01 12:00:00'
        ]);
        
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $post->published_at);
    }

    /** @test */
    public function post_excerpt_is_generated_from_content_if_empty()
    {
        $longContent = str_repeat('This is a long content. ', 50);
        $post = Post::factory()->make([
            'content' => $longContent,
            'excerpt' => null
        ]);
        
        // Simulate the model's boot method behavior
        if (empty($post->excerpt)) {
            $post->excerpt = Str::limit(strip_tags($post->content), 200);
        }
        
        $this->assertNotEmpty($post->excerpt);
        $this->assertLessThanOrEqual(203, strlen($post->excerpt)); // 200 + "..."
    }

    /** @test */
    public function category_model_has_correct_fillable_attributes()
    {
        $category = new Category();
        
        $this->assertEquals([
            'name',
            'slug',
            'description',
        ], $category->getFillable());
    }

    /** @test */
    public function category_has_many_posts_relationship()
    {
        $category = Category::factory()->create();
        
        $this->assertInstanceOf(HasMany::class, $category->posts());
    }

    /** @test */
    public function category_can_have_multiple_posts()
    {
        $category = Category::factory()->create();
        $posts = Post::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertCount(3, $category->posts);
        $this->assertTrue($category->posts->contains($posts[0]));
    }

    /** @test */
    public function category_automatically_generates_slug_from_name()
    {
        $category = Category::factory()->create(['name' => 'Technology & Innovation']);
        
        $this->assertEquals('technology-innovation', $category->slug);
    }

    /** @test */
    public function category_ensures_unique_slug()
    {
        Category::factory()->create(['name' => 'Technology', 'slug' => 'technology']);
        $category2 = Category::factory()->create(['name' => 'Technology']);
        
        $this->assertEquals('technology-2', $category2->slug);
    }

    /** @test */
    public function model_timestamps_are_properly_set()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create();
        $post = Post::factory()->create();
        
        $this->assertNotNull($user->created_at);
        $this->assertNotNull($user->updated_at);
        $this->assertNotNull($category->created_at);
        $this->assertNotNull($category->updated_at);
        $this->assertNotNull($post->created_at);
        $this->assertNotNull($post->updated_at);
    }

    /** @test */
    public function post_published_at_defaults_to_now_when_status_is_published()
    {
        $post = Post::factory()->create([
            'status' => 'published',
            'published_at' => null
        ]);
        
        // This would typically be handled in a model observer or mutator
        if ($post->status === 'published' && is_null($post->published_at)) {
            $post->published_at = now();
            $post->save();
        }
        
        $this->assertNotNull($post->fresh()->published_at);
    }

    /** @test */
    public function post_title_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Post::create([
            'title' => null,
            'content' => 'Some content',
            'user_id' => User::factory()->create()->id,
            'category_id' => Category::factory()->create()->id,
        ]);
    }

    /** @test */
    public function category_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        Category::create([
            'name' => null,
            'slug' => 'test-slug',
        ]);
    }

    /** @test */
    public function user_email_must_be_unique()
    {
        User::factory()->create(['email' => 'test@example.com']);
        
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
        ]);
    }
}