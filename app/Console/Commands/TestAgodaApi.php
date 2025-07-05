<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\HotelRecommendationService;
use Illuminate\Support\Facades\Log;

class TestAgodaApi extends Command
{
    protected $signature = 'test:agoda-api {location?}';
    protected $description = 'Test the Agoda API integration';

    public function handle()
    {
        $location = $this->argument('location') ?? 'Lagos, Nigeria';
        $this->info("Testing Agoda API for location: {$location}");

        // Check environment variables
        $this->info("\nChecking environment variables:");
        $this->info("AGODA_API_KEY: " . (env('AGODA_API_KEY') ? 'Set' : 'Not set'));
        $this->info("AGODA_PARTNER_ID: " . (env('AGODA_PARTNER_ID') ? 'Set' : 'Not set'));

        // Test the API
        $this->info("\nTesting API calls:");
        $service = new HotelRecommendationService();
        
        try {
            $hotels = $service->getHotelRecommendations($location, null, null, 'low');
            
            $this->info("\nResults:");
            $this->info("Number of hotels found: " . count($hotels));
            
            if (!empty($hotels)) {
                $this->info("\nSample hotel data:");
                $this->table(
                    ['Name', 'Price', 'Rating'],
                    array_map(function($hotel) {
                        return [
                            $hotel['name'],
                            $hotel['price'] . ' ' . $hotel['currency'],
                            $hotel['rating']
                        ];
                    }, array_slice($hotels, 0, 3))
                );
            }
        } catch (\Exception $e) {
            $this->error("Error testing API: " . $e->getMessage());
            Log::error('Agoda API test failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
} 