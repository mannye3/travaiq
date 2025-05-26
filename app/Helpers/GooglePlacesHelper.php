<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

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
            Log::error('Google Places API key not found in environment variables');
            return 'Error: API key not provided.';
        }

        // Helper function using cURL
        $fetchUrl = function ($url) {
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // Add this for development
            $response = curl_exec($ch);
            
            if (curl_errno($ch)) {
                Log::error('cURL error in GooglePlacesHelper', [
                    'error' => curl_error($ch),
                    'url' => $url
                ]);
                curl_close($ch);
                return null;
            }
            
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

        if (!$findResponse || !isset($findResponse['candidates'][0]['place_id'])) {
            Log::warning('No place_id found for location', [
                'location' => $locationName,
                'response' => $findResponse
            ]);
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

        if (!$detailsResponse || !isset($detailsResponse['result']['photos'][0]['photo_reference'])) {
            Log::warning('No photo_reference found for place', [
                'place_id' => $placeId,
                'response' => $detailsResponse
            ]);
            return 'Error: No photo_reference found.';
        }

        $photoReference = $detailsResponse['result']['photos'][0]['photo_reference'];

        // Step 3: Construct photo URL
        $photoUrl = 'https://maps.googleapis.com/maps/api/place/photo?' . http_build_query([
            'maxwidth'       => $maxWidth,
            'photoreference' => $photoReference,
            'key'            => $apiKey,
        ]);

        Log::info('Successfully generated photo URL', [
            'location' => $locationName,
            'photo_url' => $photoUrl
        ]);

        return $photoUrl;
    }
}
