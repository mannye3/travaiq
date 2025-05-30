<?php

namespace App\Prompts;

class TravelPlanPrompt
{
    /**
     * Generate the travel plan prompt with the given parameters
     *
     * @param string $location
     * @param int $totalDays
     * @param string $traveler
     * @param string $budget
     * @param string $activities
     * @return string
     */
    public static function generate(string $location, int $totalDays, string $traveler, string $budget, string $activities): string
    {
        $prompt = <<<PROMPT
        You are a travel planning assistant. Generate a travel plan based on the following specifications and return it ONLY as a valid JSON object. Do not include any other text or explanations.

        Location: {$location}
        Duration: {$totalDays} days
        Travelers: {$traveler}
        Budget Level: {$budget}
        Preferred Activities: {$activities}

        Please ensure:
        - Generate a complete itinerary for ALL {$totalDays} days of the trip
        - At least **4 hotels** are suggested with detailed information.
        - Each day in the itinerary includes at least **4 activities** with descriptions, cost, duration, best times, coordinates, addresses.
        - Include  landmarks and cultural highlights under `location_overview`.
        - Prices are in the local currency.
        - Include comprehensive security advice specific to the location.
        - Include recommended flight options with airlines and typical price ranges.

Return a JSON object with these exact keys:
{
    "location_overview": {
        "history_and_culture": "string",
        "local_customs_and_traditions": "string",
        "geographic_features_and_climate": "string",
        "historical_events_and_landmarks": [
            {"name": "string", "description": "string", "image_url": "string"}
        ],
        "cultural_highlights": [
            {"name": "string", "description": "string", "image_url": "string"}
        ],
        "security_advice": {
            "overall_safety_rating": "string",
            "emergency_numbers": "string",
            "areas_to_avoid": "string",
            "common_scams": "string",
            "safety_tips": ["string"],
            "health_precautions": "string",
            "local_emergency_facilities": [
                {"name": "string", "address": "string", "phone": "string"}
            ]
        }
    },
    "hotels": [
        {
            "name": "string",
            "address": "string",
            "price_per_night": "string",
            "rating": "string",
            "description": "string",
            "coordinates": "string"
        }
    ],
    "itinerary": [
        {
            "day": "integer",
            "activities": [
                {
                    "name": "string",
                    "description": "string",
                    "coordinates": "string",
                    "address": "string",
                    "cost": "string",
                    "duration": "string",
                    "best_time": "string"
                }
            ]
        }
    ],
    "costs": [
        {
            "transportation": [
                {"type": "string", "cost": "string"}
            ],
            "dining": [
                {"category": "string", "cost_range": "string"}
            ]
        }
    ],
    "additional_information": {
        "local_currency": "string",
        "exchange_rate": "string",
        "timezone": "string",
        "weather_forecast": "string",
        "transportation_options": "string"
    },
    "flight_recommendations": {
        "recommended_airports": [
            {"name": "string", "code": "string", "distance_to_city": "string"}
        ],
        "airlines": [
            {"name": "string", "typical_price_range": "string", "flight_duration": "string", "notes": "string"}
        ],
        "best_booking_time": "string",
        "travel_tips": ["string"]
    }
}
PROMPT;

        return $prompt;
    }
} 