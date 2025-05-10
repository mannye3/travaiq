<?php

namespace App\Helpers;

class GooglePlacesHelper
{
    /**
     * Get a photo URL for a location using Google Places API
     *
     * @param string $locationName The name of the location
     * @param string|null $apiKey Google Places API key (optional, will use env variable if not provided)
     * @param int $maxWidth Maximum width of the image
     * @return string The URL of the image or an error message
     */
   public static function getPlacePhotoUrl($locationName, $apiKey = null, $maxWidth = 800)
{
    // Use provided API key or get from environment
    $apiKey = $apiKey ?? env('GOOGLE_MAPS_API_KEY');

    if (empty($apiKey)) {
        return 'Error: API key not provided.';
    }

    // Helper function using cURL
    $fetchUrl = function ($url) {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    };

    // Step 1: Get place_id from place name
    $findPlaceUrl = 'https://maps.googleapis.com/maps/api/place/findplacefromtext/json?' . http_build_query([
        'input'     => $locationName,
        'inputtype' => 'textquery',
        'fields'    => 'place_id',
        'key'       => $apiKey,
    ]);

    $findResponse = json_decode($fetchUrl($findPlaceUrl), true);

    if (
        !isset($findResponse['candidates'][0]['place_id']) ||
        empty($findResponse['candidates'][0]['place_id'])
    ) {
        return 'Error: No place_id found.';
    }

    $placeId = $findResponse['candidates'][0]['place_id'];

    // Step 2: Use place_id to get photo_reference
    $detailsUrl = 'https://maps.googleapis.com/maps/api/place/details/json?' . http_build_query([
        'place_id' => $placeId,
        'fields'   => 'photos',
        'key'      => $apiKey,
    ]);

    $detailsResponse = json_decode($fetchUrl($detailsUrl), true);

    if (
        !isset($detailsResponse['result']['photos'][0]['photo_reference']) ||
        empty($detailsResponse['result']['photos'][0]['photo_reference'])
    ) {
        return 'Error: No photo_reference found.';
    }

    $photoReference = $detailsResponse['result']['photos'][0]['photo_reference'];

    // Step 3: Construct photo URL
    $photoUrl = 'https://maps.googleapis.com/maps/api/place/photo?' . http_build_query([
        'maxwidth'       => $maxWidth,
        'photoreference' => $photoReference,
        'key'            => $apiKey,
    ]);

    return $photoUrl;
}

}
