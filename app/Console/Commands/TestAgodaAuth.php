<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class TestAgodaAuth extends Command
{
    protected $signature = 'test:agoda-auth';
    protected $description = 'Test Agoda API authentication';

    public function handle()
    {
        $this->info("Testing Agoda API authentication");

        $partnerId = env('AGODA_PARTNER_ID');
        $apiKey = env('AGODA_API_KEY');

        $this->info("Partner ID: $partnerId");
        $this->info("API Key: $apiKey");

        if (empty($partnerId) || empty($apiKey)) {
            $this->error("Agoda API credentials not found in .env file");
            return;
        }

        // Test a simple authenticated request to see if credentials work
        $this->info("\nTesting authentication with a simple request...");

        try {
            // Try a simple endpoint that should respond with authentication info
            $response = Http::withHeaders([
                'Authorization' => $partnerId . ':' . $apiKey,
                'Content-Type' => 'application/json',
            ])->get('https://affiliateapi7643.agoda.com/affiliateservice/ping');

            $this->info("Response Status: " . $response->status());
            $this->info("Response Body: " . $response->body());

            if ($response->successful()) {
                $this->info("Authentication successful!");
            } else {
                $this->error("Authentication failed!");
            }
        } catch (\Exception $e) {
            $this->error("Error: " . $e->getMessage());
        }
    }
}