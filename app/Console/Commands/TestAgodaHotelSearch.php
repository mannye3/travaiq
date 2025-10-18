<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TestAgodaHotelSearch extends Command
{
    protected $signature = 'test:agoda-hotel-search {location?}';
    protected $description = 'Test Agoda hotel search with direct city ID';

    public function handle()
    {
        $location = $this->argument('location') ?? 'Paris';
        $this->info("Testing Agoda hotel search for: {$location}");

        $partnerId = env('AGODA_PARTNER_ID');
        $apiKey = env('AGODA_API_KEY');
        
        // Extract just the API key part (after the colon)
        if ($apiKey && strpos($apiKey, ':') !== false) {
            $parts = explode(':', $apiKey);
            $apiKey = end($parts);
        }

        $this->info("Using Partner ID: $partnerId");
        $this->info("Using API Key: $apiKey");

        // Map common locations to their city IDs
        $cityIds = [
            'Paris' => 1063,
            'London' => 20711,
            'New York' => 1234,
            'Tokyo' => 23412,
            'Dubai' => 9010,
            'Singapore' => 1202,
            'Bangkok' => 9016,
            'Rome' => 3135,
            'Barcelona' => 9637,
            'Istanbul' => 9593
        ];

        $cityId = $cityIds[$location] ?? 1063; // Default to Paris
        $this->info("Using city ID: $cityId for $location");

        $checkInDate = date('Y-m-d', strtotime('+7 days'));
        $checkOutDate = date('Y-m-d', strtotime('+12 days'));

        $this->info("Check-in date: $checkInDate");
        $this->info("Check-out date: $checkOutDate");

        try {
            $this->info("\nSending hotel search request...");

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
                        'cityId' => $cityId
                    ]
                ]);

            $this->info("Response Status: " . $hotelResponse->status());
            
            if ($hotelResponse->successful()) {
                $this->info("SUCCESS: Hotel search was successful!");
                
                $responseData = $hotelResponse->json();
                
                if (!empty($responseData) && isset($responseData['results'])) {
                    $this->info("Found " . count($responseData['results']) . " hotels");
                    
                    if (!empty($responseData['results'])) {
                        $this->info("\nSample hotels:");
                        foreach (array_slice($responseData['results'], 0, 3) as $index => $hotel) {
                            $this->info(($index + 1) . ". " . 
                                ($hotel['hotelName'] ?? 'Unknown Hotel') . 
                                " - " . ($hotel['dailyRate'] ?? 'N/A') . 
                                " " . ($hotel['currency'] ?? 'USD') .
                                " - Rating: " . ($hotel['starRating'] ?? 'N/A'));
                        }
                    }
                    
                    return 0; // Success
                } else {
                    $this->error("No results found in response");
                    $this->error("Response: " . json_encode($responseData));
                }
            } else {
                $this->error("Hotel search failed!");
                $this->error("Response Body: " . $hotelResponse->body());
                
                // Log the full response for debugging
                Log::error('Agoda Hotel Search Failed', [
                    'status' => $hotelResponse->status(),
                    'body' => $hotelResponse->body(),
                    'headers' => $hotelResponse->headers()
                ]);
            }
        } catch (\Exception $e) {
            $this->error("Exception occurred: " . $e->getMessage());
            $this->error("Trace: " . $e->getTraceAsString());
            
            Log::error('Agoda Hotel Search Exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }

        return 1; // Failure
    }
}