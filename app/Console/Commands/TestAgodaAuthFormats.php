<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestAgodaAuthFormats extends Command
{
    protected $signature = 'test:agoda-auth-formats';
    protected $description = 'Test different Agoda API authentication formats';

    public function handle()
    {
        $this->info("Testing different Agoda API authentication formats");

        $partnerId = env('AGODA_PARTNER_ID');
        $apiKey = env('AGODA_API_KEY');

        $this->info("Partner ID: $partnerId");
        $this->info("API Key: $apiKey");

        // Extract just the API key part (after the colon)
        $apiKeyOnly = substr($apiKey, strpos($apiKey, ':') + 1);
        $this->info("Extracted API Key: $apiKeyOnly");

        $checkInDate = date('Y-m-d', strtotime('+7 days'));
        $checkOutDate = date('Y-m-d', strtotime('+12 days'));

        // Test Format 1: Current format (PartnerID:APIKey)
        $this->info("\n1. Testing current format (PartnerID:APIKey):");
        $this->testHotelSearch($partnerId . ':' . $apiKeyOnly, $partnerId, $apiKeyOnly);

        // Test Format 2: Just the API key
        $this->info("\n2. Testing just API key:");
        $this->testHotelSearch($apiKeyOnly, $partnerId, $apiKeyOnly);

        // Test Format 3: Basic Auth style
        $this->info("\n3. Testing Basic Auth style:");
        $credentials = base64_encode($partnerId . ':' . $apiKeyOnly);
        $this->testHotelSearch("Basic $credentials", $partnerId, $apiKeyOnly);
    }

    private function testHotelSearch($authHeader, $partnerId, $apiKey)
    {
        try {
            $response = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept-Encoding' => 'gzip,deflate',
                    'Authorization' => $authHeader,
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
                            'maxResult' => 2,
                            'minimumReviewScore' => 0,
                            'minimumStarRating' => 0,
                            'occupancy' => [
                                'numberOfAdult' => 2,
                                'numberOfChildren' => 1
                            ],
                            'sortBy' => 'PriceAsc'
                        ],
                        'checkInDate' => date('Y-m-d', strtotime('+7 days')),
                        'checkOutDate' => date('Y-m-d', strtotime('+12 days')),
                        'cityId' => 1063  // Paris
                    ]
                ]);

            $this->info("  Status: " . $response->status());
            
            if ($response->successful()) {
                $this->info("  SUCCESS! Authentication worked");
                $data = $response->json();
                if (!empty($data['results'])) {
                    $this->info("  Found " . count($data['results']) . " hotels");
                }
            } else {
                $this->info("  FAILED: " . substr($response->body(), 0, 100));
            }
        } catch (\Exception $e) {
            $this->info("  ERROR: " . $e->getMessage());
        }
    }
}