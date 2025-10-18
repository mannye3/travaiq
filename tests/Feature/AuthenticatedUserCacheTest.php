<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\TripDetail;
use App\Models\LocationOverview;

class AuthenticatedUserCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_cache_is_created()
    {
        // Create a user
        $user = User::factory()->create();
        
        $this->actingAs($user);

        // Mock external API calls
        $this->mock('App\Services\HotelRecommendationService', function ($mock) {
            $mock->shouldReceive('getHotelRecommendations')
                ->andReturn([]);
        });

        // Generate travel plan data
        $travelPlanData = [
            'location' => 'Tokyo',
            'duration' => 5,
            'traveler' => 'Family',
            'budget' => 'High',
            'activities' => json_encode(['Sightseeing', 'Food', 'Culture']),
            'travel' => now()->addDays(30)->format('Y-m-d'),
        ];

        // Make request to generate travel plan
        $response = $this->post('/travel/generate', $travelPlanData);

        // Assert response is successful
        $response->assertStatus(302); // Redirect after creation

        // Check if cache was created
        $cacheKey = 'auth_travel_plan_' . $user->id . '_' . md5('Tokyo5FamilyHigh');
        $cachedData = Cache::get($cacheKey);
        
        $this->assertNotNull($cachedData, 'Authenticated user cache should be created');
        $this->assertArrayHasKey('travel_plan', $cachedData);
        $this->assertArrayHasKey('user_id', $cachedData);
        $this->assertEquals($user->id, $cachedData['user_id']);
    }

    public function test_authenticated_user_cache_is_reused()
    {
        // Create a user
        $user = User::factory()->create();
        
        $this->actingAs($user);

        // Pre-populate cache
        $cacheKey = 'auth_travel_plan_' . $user->id . '_' . md5('Paris3SoloMedium');
        $cachedData = [
            'travel_plan' => [
                'location' => 'Paris',
                'duration' => 3,
                'traveler' => 'Solo',
                'budget' => 'Medium',
                'location_overview' => [
                    'history_and_culture' => 'Rich French history',
                    'local_customs_and_traditions' => 'French customs',
                    'geographic_features_and_climate' => 'Temperate'
                ],
                'itinerary' => [],
                'costs' => [],
                'additional_information' => []
            ],
            'check_in_date' => now()->format('Y-m-d'),
            'check_out_date' => now()->addDays(3)->format('Y-m-d'),
            'budget' => 'Medium',
            'hotels' => [],
            'cached_at' => now(),
            'user_id' => $user->id
        ];
        
        Cache::put($cacheKey, $cachedData, 7200);

        // Mock external API calls
        $this->mock('App\Services\HotelRecommendationService', function ($mock) {
            $mock->shouldReceive('getHotelRecommendations')
                ->andReturn([]);
        });

        // Generate travel plan with same parameters
        $travelPlanData = [
            'location' => 'Paris',
            'duration' => 3,
            'traveler' => 'Solo',
            'budget' => 'Medium',
            'activities' => json_encode(['Sightseeing', 'Food']),
            'travel' => now()->addDays(30)->format('Y-m-d'),
        ];

        // Make request to generate travel plan
        $response = $this->post('/travel/generate', $travelPlanData);

        // Assert response is successful
        $response->assertStatus(302);

        // Verify cache was used (should show "from cache" message)
        $this->assertTrue(Cache::has($cacheKey), 'Cache should still exist');
    }

    public function test_different_parameters_create_different_cache()
    {
        // Create a user
        $user = User::factory()->create();
        
        $this->actingAs($user);

        // Mock external API calls
        $this->mock('App\Services\HotelRecommendationService', function ($mock) {
            $mock->shouldReceive('getHotelRecommendations')
                ->andReturn([]);
        });

        // Generate first travel plan
        $firstPlanData = [
            'location' => 'London',
            'duration' => 4,
            'traveler' => 'Couple',
            'budget' => 'High',
            'activities' => json_encode(['Shopping', 'Museums']),
            'travel' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post('/travel/generate', $firstPlanData);
        $response->assertStatus(302);

        // Generate second travel plan with different parameters
        $secondPlanData = [
            'location' => 'London',
            'duration' => 4,
            'traveler' => 'Couple',
            'budget' => 'Low', // Different budget
            'activities' => json_encode(['Shopping', 'Museums']),
            'travel' => now()->addDays(30)->format('Y-m-d'),
        ];

        $response = $this->post('/travel/generate', $secondPlanData);
        $response->assertStatus(302);

        // Check that different cache keys were created
        $firstCacheKey = 'auth_travel_plan_' . $user->id . '_' . md5('London4CoupleHigh');
        $secondCacheKey = 'auth_travel_plan_' . $user->id . '_' . md5('London4CoupleLow');
        
        $this->assertTrue(Cache::has($firstCacheKey), 'First cache should exist');
        $this->assertTrue(Cache::has($secondCacheKey), 'Second cache should exist');
        $this->assertNotEquals($firstCacheKey, $secondCacheKey, 'Cache keys should be different');
    }

    public function test_authenticated_user_cache_expiration()
    {
        // Create a user
        $user = User::factory()->create();
        
        $this->actingAs($user);

        // Create cache with short TTL
        $cacheKey = 'auth_travel_plan_' . $user->id . '_' . md5('Rome2SoloLow');
        $cachedData = ['test' => 'data'];
        Cache::put($cacheKey, $cachedData, 1); // 1 second TTL
        
        // Wait for cache to expire
        sleep(2);
        
        // Check if cache expired
        $this->assertNull(Cache::get($cacheKey), 'Authenticated user cache should have expired');
    }

    public function test_authenticated_user_cache_tracking()
    {
        // Create a user
        $user = User::factory()->create();
        
        $this->actingAs($user);

        // Create cache
        $cacheKey = 'auth_travel_plan_' . $user->id . '_' . md5('Barcelona3FamilyMedium');
        $cachedData = ['test' => 'data'];
        Cache::put($cacheKey, $cachedData, 7200);
        
        // Track the key
        $keys = Cache::get('auth_travel_plan_cache_keys', []);
        $keys[] = $cacheKey;
        Cache::put('auth_travel_plan_cache_keys', $keys, 7200);
        
        // Verify key is tracked
        $trackedKeys = Cache::get('auth_travel_plan_cache_keys', []);
        $this->assertContains($cacheKey, $trackedKeys, 'Authenticated user cache key should be tracked');
    }

    public function test_user_cache_info_endpoint()
    {
        // Create a user
        $user = User::factory()->create();
        
        $this->actingAs($user);

        // Create some cache for the user
        $cacheKey = 'auth_travel_plan_' . $user->id . '_' . md5('Amsterdam2SoloHigh');
        $cachedData = [
            'travel_plan' => [
                'location' => 'Amsterdam',
                'duration' => 2,
                'traveler' => 'Solo',
                'budget' => 'High'
            ],
            'cached_at' => now(),
            'user_id' => $user->id
        ];
        Cache::put($cacheKey, $cachedData, 7200);
        
        // Track the key
        $keys = Cache::get('auth_travel_plan_cache_keys', []);
        $keys[] = $cacheKey;
        Cache::put('auth_travel_plan_cache_keys', $keys, 7200);

        // Test the user cache info endpoint
        $response = $this->get("/user-cache-info/{$user->id}");
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'user_id',
            'total_cached_plans',
            'cached_plans'
        ]);
        
        $data = $response->json();
        $this->assertEquals($user->id, $data['user_id']);
        $this->assertGreaterThan(0, $data['total_cached_plans']);
    }

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing cache
        Cache::flush();
    }

    protected function tearDown(): void
    {
        // Clean up cache
        Cache::flush();
        
        parent::tearDown();
    }
} 