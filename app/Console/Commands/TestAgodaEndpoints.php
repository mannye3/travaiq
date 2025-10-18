<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestAgodaEndpoints extends Command
{
    protected $signature = 'test:agoda-endpoints';
    protected $description = 'Test different Agoda API endpoints';

    public function handle()
    {
        $this->info("Testing different Agoda API endpoints");

        $partnerId = env('AGODA_PARTNER_ID');
        $apiKey = env('AGODA_API_KEY');

        // Test 1: Try the hotel search endpoint directly with a known city ID
        $this->info("\n1. Testing hotel search with known city ID (Paris = 1063):");
        
        $checkInDate = date('Y-m-d', strtotime('+7 days'));
        $checkOutDate = date('Y-m-d', strtotime('+12 days'));
        
        try {
            $hotelResponse = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept-Encoding' => 'gzip,deflate',
                    'Authorization' => $partnerId . ':' . $apiKey,
                ])->post('https://affiliateapi7643.agoda.com/affiliateservice/lt_v1', [
                    'criteria' => [
                        'additional' => [
                            'currency' => 'USD',
                            'dailyRate' => [
                                'maximum' => 3000,
                                'minimum' => 100
                            ],
                            'discountOnly' => false,
                            'language' => 'en-us',
                            'maxResult' => 5,
                            'minimumReviewScore' => 0,
                            'minimumStarRating' => 0,
                            'occupancy' => [
                                'numberOfAdult' => 2,
                                'numberOfChildren' => 1
                            ],
                            'sortBy' => 'PriceAsc'
                        ],
                        'checkInDate' => $checkInDate,
                        'checkOutDate' => $checkOutDate,
                        'cityId' => 1063  // Paris city ID
                    ]
                ]);

            $this->info("Hotel search response status: " . $hotelResponse->status());
            $this->info("Hotel search response body: " . substr($hotelResponse->body(), 0, 500) . "...");
            
            if ($hotelResponse->successful()) {
                $responseData = $hotelResponse->json();
                if (!empty($responseData) && isset($responseData['results'])) {
                    $this->info("Found " . count($responseData['results']) . " hotels in Paris");
                    if (!empty($responseData['results'])) {
                        $hotel = $responseData['results'][0];
                        $this->info("Sample hotel: " . ($hotel['hotelName'] ?? 'Unknown') . 
                                  " - Price: " . ($hotel['dailyRate'] ?? 'N/A') . " " . ($hotel['currency'] ?? 'USD'));
                    }
                } else {
                    $this->error("No hotels found in response");
                }
            } else {
                $this->error("Hotel search failed");
            }
        } catch (\Exception $e) {
            $this->error("Error in hotel search: " . $e->getMessage());
        }

        // Test 2: Try a different city
        $this->info("\n2. Testing hotel search with London (city ID 20711):");
        
        try {
            $hotelResponse = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept-Encoding' => 'gzip,deflate',
                    'Authorization' => $partnerId . ':' . $apiKey,
                ])->post('https://affiliateapi7643.agoda.com/affiliateservice/lt_v1', [
                    'criteria' => [
                        'additional' => [
                            'currency' => 'USD',
                            'dailyRate' => [
                                'maximum' => 3000,
                                'minimum' => 100
                            ],
                            'discountOnly' => false,
                            'language' => 'en-us',
                            'maxResult' => 5,
                            'minimumReviewScore' => 0,
                            'minimumStarRating' => 0,
                            'occupancy' => [
                                'numberOfAdult' => 2,
                                'numberOfChildren' => 1
                            ],
                            'sortBy' => 'PriceAsc'
                        ],
                        'checkInDate' => $checkInDate,
                        'checkOutDate' => $checkOutDate,
                        'cityId' => 20711  // London city ID
                    ]
                ]);

            $this->info("London hotel search response status: " . $hotelResponse->status());
            
            if ($hotelResponse->successful()) {
                $responseData = $hotelResponse->json();
                if (!empty($responseData) && isset($responseData['results'])) {
                    $this->info("Found " . count($responseData['results']) . " hotels in London");
                } else {
                    $this->error("No hotels found in London response");
                }
            } else {
                $this->error("London hotel search failed");
            }
        } catch (\Exception $e) {
            $this->error("Error in London hotel search: " . $e->getMessage());
        }
    }
}