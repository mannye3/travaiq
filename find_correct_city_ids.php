<?php
// Script to find correct city IDs by testing a range of IDs

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

$env = parseEnvFile('c:\xampp\htdocs\travaiq\.env');

$agodaPartnerId = $env['AGODA_PARTNER_ID'] ?? null;
$agodaApiKey = $env['AGODA_API_KEY'] ?? null;

// Extract just the API key part (after the colon) if it contains a colon
if ($agodaApiKey && strpos($agodaApiKey, ':') !== false) {
    $parts = explode(':', $agodaApiKey);
    $agodaApiKey = end($parts);
}

// Test a range of city IDs for Paris
$testCityIds = [1063, 1067, 298391, 298525, 298524, 298526, 298527, 298528];
$checkInDate = date('Y-m-d', strtotime('+14 days'));
$checkOutDate = date('Y-m-d', strtotime('+17 days'));

echo "Testing city IDs for Paris...\n\n";

foreach ($testCityIds as $cityId) {
    echo "Testing city ID: $cityId\n";
    
    $data = [
        'criteria' => [
            'additional' => [
                'currency' => 'USD',
                'dailyRate' => [
                    'maximum' => 500,
                    'minimum' => 50
                ],
                'discountOnly' => false,
                'language' => 'en-us',
                'maxResult' => 1,
                'minimumReviewScore' => 0,
                'minimumStarRating' => 0,
                'occupancy' => [
                    'numberOfAdult' => 2,
                    'numberOfChildren' => 0
                ],
                'sortBy' => 'PriceAsc'
            ],
            'checkInDate' => $checkInDate,
            'checkOutDate' => $checkOutDate,
            'cityId' => $cityId
        ]
    ];

    $jsonData = json_encode($data);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, 'https://affiliateapi7643.agoda.com/affiliateservice/lt_v1');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Authorization: ' . $agodaPartnerId . ':' . $agodaApiKey
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 30);
    curl_setopt($ch, CURLOPT_ENCODING, ''); // Accept all encodings

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response !== false) {
        $decoded = json_decode($response, true);
        if (isset($decoded['results']) && count($decoded['results']) > 0) {
            $firstHotel = $decoded['results'][0];
            echo "  ✓ Success - Found hotel: " . $firstHotel['hotelName'] . "\n";
            echo "    Location: " . $firstHotel['latitude'] . ", " . $firstHotel['longitude'] . "\n";
        } elseif (isset($decoded['error'])) {
            echo "  ✗ Error: " . $decoded['error']['message'] . " (ID: " . ($decoded['error']['id'] ?? 'N/A') . ")\n";
        } else {
            echo "  ✗ Unexpected response format\n";
        }
    } else {
        echo "  ✗ cURL Error: " . curl_error($ch) . "\n";
    }

    curl_close($ch);
    echo "\n";
}