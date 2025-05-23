<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\ServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceProviderControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_can_list_all_providers()
    {
        // Arrange
        ServiceProvider::factory()->count(5)->create();

        // Act
        $response = $this->getJson('/api/providers');

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'slug',
                        'short_description',
                        'logo',
                        'category_id'
                    ]
                ],
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ]);

        $this->assertEquals(5, count($response->json('data')));
        $this->assertEquals('success', $response->json('status'));
        $this->assertEquals('Service providers retrieved successfully.', $response->json('message'));
        $this->assertEquals(5, $response->json('meta.total'));
    }

    public function test_it_can_filter_providers_by_category_name()
    {
        // Arrange
        $category1 = Category::factory()->create(['name' => 'Technology']);
        $category2 = Category::factory()->create(['name' => 'Healthcare']);

        ServiceProvider::factory()->count(3)->create(['category_id' => $category1->id]);
        ServiceProvider::factory()->count(2)->create(['category_id' => $category2->id]);

        // Act
        $response = $this->getJson("/api/providers?category=Technology");

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_it_can_filter_providers_by_category_id()
    {
        // Arrange
        $category1 = Category::factory()->create();
        $category2 = Category::factory()->create();

        ServiceProvider::factory()->count(3)->create(['category_id' => $category1->id]);
        ServiceProvider::factory()->count(2)->create(['category_id' => $category2->id]);

        // Act
        $response = $this->getJson("/api/providers?category_id={$category1->id}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function test_it_returns_empty_results_for_non_existent_category()
    {
        // Arrange
        ServiceProvider::factory()->count(5)->create();

        // Act
        $response = $this->getJson("/api/providers?category=non-existent-category");

        // Assert
        $response->assertStatus(404)
            ->assertJsonStructure([
                'message'
            ])
            ->assertJson([
                'message' => 'No service providers found.'
            ]);
    }

    public function test_it_loads_category_relation_when_filter_is_applied()
    {
        // Arrange
        $category = Category::factory()->create(['name' => 'Technology']);
        ServiceProvider::factory()->create(['category_id' => $category->id]);

        // Act
        $response = $this->getJson("/api/providers?category=Technology");

        // Assert
        $response->assertStatus(200);

        // Check if 'category' key exists in the first item of data
        $this->assertArrayHasKey('category', $response->json('data.0'));
    }

    public function test_it_can_customize_pagination_with_per_page_parameter()
    {
        // Arrange
        ServiceProvider::factory()->count(20)->create();

        // Act
        $response = $this->getJson("/api/providers?per_page=5");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data',
                'meta' => [
                    'current_page',
                    'from',
                    'last_page',
                    'links',
                    'path',
                    'per_page',
                    'to',
                    'total'
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Service providers retrieved successfully.'
            ]);

        $this->assertEquals(5, count($response->json('data')));
        $this->assertEquals(5, $response->json('meta.per_page'));
        $this->assertEquals(20, $response->json('meta.total'));
        $this->assertEquals(4, $response->json('meta.last_page'));
    }

    public function test_it_can_show_a_provider_detail_by_slug()
    {
        // Arrange
        $provider = ServiceProvider::factory()->create([
            'slug' => 'test-provider'
        ]);

        // Act
        $response = $this->getJson("/api/providers/{$provider->slug}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'short_description',
                    'logo',
                    'category_id',
                    'category' => [
                        'id',
                        'name',
                        'slug'
                    ]
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Service provider retrieved successfully.',
                'data' => [
                    'id' => $provider->id,
                    'name' => $provider->name,
                    'slug' => $provider->slug
                ]
            ]);

        $this->assertEquals($provider->id, $response->json('data.id'));
    }

    public function test_provider_detail_includes_category_information()
    {
        // Arrange
        $category = Category::factory()->create();
        $provider = ServiceProvider::factory()->create([
            'category_id' => $category->id,
            'slug' => 'test-provider'
        ]);

        // Act
        $response = $this->getJson("/api/providers/{$provider->slug}");

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'message',
                'data' => [
                    'id',
                    'name',
                    'slug',
                    'category' => [
                        'id',
                        'name',
                        'slug'
                    ]
                ]
            ])
            ->assertJson([
                'status' => 'success',
                'message' => 'Service provider retrieved successfully.',
                'data' => [
                    'category' => [
                        'id' => $category->id,
                        'name' => $category->name
                    ]
                ]
            ]);

        $this->assertEquals($category->id, $response->json('data.category.id'));
        $this->assertEquals($category->name, $response->json('data.category.name'));
    }

    public function test_it_returns_404_for_non_existent_provider_slug()
    {
        // Act
        $response = $this->getJson("/api/providers/non-existent-slug");

        // Assert
        $response->assertStatus(404);
    }

    public function test_it_prevents_n_plus_one_problem()
    {
        // Arrange - Create multiple categories and providers
        $categories = Category::factory()->count(3)->create();
        $providers = [];

        foreach ($categories as $category) {
            $providers[] = ServiceProvider::factory()->create([
                'category_id' => $category->id
            ]);
        }

        // Clear query log
        \DB::enableQueryLog();
        \DB::flushQueryLog();

        // Act - Get the first provider
        $this->getJson("/api/providers/{$providers[0]->slug}");
        $firstQueryCount = count(\DB::getQueryLog());

        // Clear query log again
        \DB::flushQueryLog();

        // Act - Get the second provider
        $this->getJson("/api/providers/{$providers[1]->slug}");
        $secondQueryCount = count(\DB::getQueryLog());

        // Assert - The number of queries should be the same
        $this->assertEquals(
            $firstQueryCount,
            $secondQueryCount,
            "N+1 problem detected: fetching additional providers increases query count"
        );

        \DB::disableQueryLog();
    }
}
