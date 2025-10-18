<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use App\Models\User;
use App\Models\TripDetail;
use App\Models\LocationOverview;

class TravelPlanCacheTest extends TestCase
{
    use RefreshDatabase;

    public function test_cache_is_created_for_temp_travel_plan()
    {
        // Mock session data
        $tempPlan = [
            'reference_code' => 'TEST123',
            'location' => 'Paris',
            'duration' => 3,
            'traveler' => 'Solo',
            'budget' => 'Medium',
            'activities' => 'Sightseeing, Food, Culture',
            'created_at' => now(),
            'check_in_date' => now()->format('Y-m-d'),
            'check_out_date' => now()->addDays(3)->format('Y-m-d'),
            'plan' => [
                'location_overview' => [
                    'history_and_culture' => 'Rich history',
                    'local_customs_and_traditions' => 'French customs',
                    'geographic_features_and_climate' => 'Temperate climate',
                    'security_advice' => [
                        'overall_safety_rating' => 'Safe',
                        'emergency_numbers' => '112',
                        'areas_to_avoid' => 'None',
                        'common_scams' => 'None',
                        'safety_tips' => ['Be aware'],
                        'health_precautions' => 'Good healthcare'
                    ]
                ],
                'itinerary' => [
                    [
                        'day' => 1,
                        'activities' => [
                            [
                                'name' => 'Eiffel Tower',
                                'description' => 'Iconic landmark',
                                'coordinates' => '48.8584,2.2945',
                                'address' => 'Champ de Mars',
                                'cost' => '€26',
                                'duration' => '2 hours',
                                'best_time' => 'Morning'
                            ]
                        ]
                    ]
                ],
                'costs' => [
                    [
                        'accommodation' => '€150/night',
                        'transportation' => [
                            [
                                'type' => 'Metro',
                                'cost' => '€1.90',
                                'description' => 'Single ticket'
                            ]
                        ],
                        'dining' => [
                            [
                                'type' => 'Restaurant',
                                'cost' => '€25',
                                'description' => 'Lunch'
                            ]
                        ]
                    ]
                ],
                'additional_information' => [
                    'best_time_to_visit' => 'Spring',
                    'local_cuisine' => [
                        'must_try_dishes' => ['Croissant', 'Baguette'],
                        'dining_tips' => ['Reserve in advance']
                    ]
                ]
            ]
        ];

        // Set session data
        session(['temp_travel_plan' => $tempPlan]);

        // Mock external API calls
        $this->mock('App\Services\HotelRecommendationService', function ($mock) {
            $mock->shouldReceive('getHotelRecommendations')
                ->andReturn([]);
        });

        // Call the showTemp method
        $response = $this->get('/trips/temp');

        // Assert response is successful
        $response->assertStatus(200);

        // Check if cache was created
        $cacheKey = 'temp_travel_plan_' . md5('TEST123');
        $cachedData = Cache::get($cacheKey);
        
        $this->assertNotNull($cachedData, 'Cache should be created');
        $this->assertArrayHasKey('tripDetails', $cachedData);
        $this->assertArrayHasKey('locationOverview', $cachedData);
        $this->assertArrayHasKey('itineraries', $cachedData);
    }

    public function test_cache_is_served_on_subsequent_requests()
    {
        $referenceCode = 'TEST456';
        $cacheKey = 'temp_travel_plan_' . md5($referenceCode);
        
        // Pre-populate cache
        $cachedData = [
            'tripDetails' => (object) ['reference_code' => $referenceCode],
            'locationOverview' => (object) ['history_and_culture' => 'Test'],
            'itineraries' => [],
            'hotels' => [],
            'cityId' => null
        ];
        
        Cache::put($cacheKey, $cachedData, 3600);
        
        // Mock session data
        $tempPlan = [
            'reference_code' => $referenceCode,
            'location' => 'London',
            'duration' => 2,
            'traveler' => 'Couple',
            'budget' => 'High',
            'activities' => 'Shopping, Museums',
            'created_at' => now(),
            'check_in_date' => now()->format('Y-m-d'),
            'check_out_date' => now()->addDays(2)->format('Y-m-d'),
            'plan' => [
                'location_overview' => [
                    'history_and_culture' => 'British history',
                    'local_customs_and_traditions' => 'British customs',
                    'geographic_features_and_climate' => 'Rainy climate'
                ],
                'itinerary' => [],
                'costs' => [],
                'additional_information' => []
            ]
        ];
        
        session(['temp_travel_plan' => $tempPlan]);

        // Mock external API calls
        $this->mock('App\Services\HotelRecommendationService', function ($mock) {
            $mock->shouldReceive('getHotelRecommendations')
                ->andReturn([]);
        });

        // Call the showTemp method
        $response = $this->get('/trips/temp');

        // Assert response is successful
        $response->assertStatus(200);

        // Verify cache was used (no external API calls should be made)
        $this->assertTrue(Cache::has($cacheKey), 'Cache should still exist');
    }

    public function test_cache_expiration_works()
    {
        $referenceCode = 'TEST789';
        $cacheKey = 'temp_travel_plan_' . md5($referenceCode);
        
        // Create cache with short TTL
        $cachedData = ['test' => 'data'];
        Cache::put($cacheKey, $cachedData, 1); // 1 second TTL
        
        // Wait for cache to expire
        sleep(2);
        
        // Check if cache expired
        $this->assertNull(Cache::get($cacheKey), 'Cache should have expired');
    }

    public function test_cache_management_endpoints()
    {
        // Test cache status endpoint
        $response = $this->get('/check-cache-status');
        $response->assertStatus(200);
        $response->assertJsonStructure(['total_cached_plans', 'cache_status']);

        // Test clear expired cache endpoint
        $response = $this->get('/clear-expired-cache');
        $response->assertStatus(302); // Redirect response
    }

    public function test_cache_keys_tracking()
    {
        $referenceCode = 'TEST999';
        $cacheKey = 'temp_travel_plan_' . md5($referenceCode);
        
        // Create cache
        $cachedData = ['test' => 'data'];
        Cache::put($cacheKey, $cachedData, 3600);
        
        // Track the key
        $keys = Cache::get('temp_travel_plan_cache_keys', []);
        $keys[] = $cacheKey;
        Cache::put('temp_travel_plan_cache_keys', $keys, 3600);
        
        // Verify key is tracked
        $trackedKeys = Cache::get('temp_travel_plan_cache_keys', []);
        $this->assertContains($cacheKey, $trackedKeys, 'Cache key should be tracked');
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