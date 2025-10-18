<?php
// Test script for Agoda location search API

// Read .env file manually
function parseEnvFile($path) {
    $env = [];
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos($line, '=') !== false && strpos($line, '#') !== 0) {
            list($key, $value) = explode('=', $line, 2);
            $env[trim($key)] = trim($value, "'\"");
        }
    }
    return $env;
}

$env = parseEnvFile('.env');

$agodaPartnerId = $env['AGODA_PARTNER_ID'] ?? null;
$agodaApiKey = $env['AGODA_API_KEY'] ?? null;

echo "Testing Agoda API credentials:\n";
echo "Partner ID: " . ($agodaPartnerId ? "Present" : "Missing") . "\n";
echo "API Key: " . ($agodaApiKey ? "Present" : "Missing") . "\n";

// Extract just the API key part (after the colon) if it contains a colon
if ($agodaApiKey && strpos($agodaApiKey, ':') !== false) {
    $parts = explode(':', $agodaApiKey);
    $agodaApiKey = end($parts);
    echo "Extracted API Key: " . $agodaApiKey . "\n";
}

// Test the location search API to get correct city IDs
$locations = ['London', 'Barcelona', 'Paris', 'New York'];

foreach ($locations as $location) {
    echo "\nSearching for '$location'...\n";
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://partners.api.agoda.com/locations/search?term=' . urlencode($location) . '&limit=5');
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Accept: application/json',
        'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_ENCODING, ''); // Accept all encodings

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response !== false) {
        $decoded = json_decode($response, true);
        if (is_array($decoded) && count($decoded) > 0) {
            echo "  Found " . count($decoded) . " results:\n";
            foreach ($decoded as $result) {
                echo "    - " . ($result['name'] ?? $result['displayName'] ?? 'Unknown') . 
                     " (ID: " . ($result['id'] ?? $result['cityId'] ?? 'Unknown') . ")\n";
            }
        } else {
            echo "  No results found\n";
        }
    } else {
        echo "  cURL Error: " . curl_error($ch) . "\n";
    }

    curl_close($ch);
}