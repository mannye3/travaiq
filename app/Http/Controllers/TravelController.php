<?php
namespace App\Http\Controllers;

use App\Helpers\GooglePlacesHelper;
use App\Models\Activity;
use App\Models\AdditionalInformation;
use App\Models\Cost;
use App\Models\DiningCost;
use App\Models\Hotel;
use App\Models\Itinerary;
use App\Models\LocationOverview;
use App\Models\SecurityAdvice;
use App\Models\TransportationCost;
use App\Models\TripDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class TravelController extends Controller
{
    public function generateTravelPlan(Request $request)
    {
        set_time_limit(300);


        // Step 2: Prepare the prompt for the Gemini AI API
        $location   = $request->input('location');
        $totalDays  = $request->input('duration');
        $traveler   = $request->input('traveler');
        $budget     = $request->input('budget');
        $activities = json_decode($request->input('activities'), true); // Decode JSON array
        $activities = implode(', ', $activities);

      
        $referenceCode = strtoupper(Str::random(8));
        $tripDetail    = TripDetail::create([
            'reference_code' => $referenceCode,
            'location'       => $location,
            'duration'       => $totalDays,
            'traveler'       => $traveler,
            'budget'         => $budget,
            'activities'     => $activities,
            'user_id'        => Auth::id(), // Add the authenticated user's ID
        ]);

         
        $google_place_image = GooglePlacesHelper::getPlacePhotoUrl($location);

        if (! str_starts_with($google_place_image, 'Error:')) {
            $tripDetail->update([
                'google_place_image' => $google_place_image,
            ]);
        }

        $prompt = "
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
        - Include **image URLs** for landmarks and cultural highlights under `location_overview`.
        - Prices are in the local currency.
        - Include comprehensive security advice specific to the location.
        - Include recommended flight options with airlines and typical price ranges.

Return a JSON object with these exact keys:
{
    \"location_overview\": {
        \"history_and_culture\": \"string\",
        \"local_customs_and_traditions\": \"string\",
        \"geographic_features_and_climate\": \"string\",
        \"historical_events_and_landmarks\": [
            {\"name\": \"string\", \"description\": \"string\", \"image_url\": \"string\"}
        ],
        \"cultural_highlights\": [
            {\"name\": \"string\", \"description\": \"string\", \"image_url\": \"string\"}
        ],
        \"security_advice\": {
            \"overall_safety_rating\": \"string\",
            \"emergency_numbers\": \"string\",
            \"areas_to_avoid\": \"string\",
            \"common_scams\": \"string\",
            \"safety_tips\": [\"string\"],
            \"health_precautions\": \"string\",
            \"local_emergency_facilities\": [
                {\"name\": \"string\", \"address\": \"string\", \"phone\": \"string\"}
            ]
        }
    },
    \"hotels\": [
        {
            \"name\": \"string\",
            \"address\": \"string\",
            \"price_per_night\": \"string\",
            \"rating\": \"string\",
            \"description\": \"string\",
            \"coordinates\": \"string\",
           
        }
    ],
    \"itinerary\": [
        {
            \"day\": \"integer\",
            \"activities\": [
                {
                    \"name\": \"string\",
                    \"description\": \"string\",
                    \"coordinates\": \"string\",
                    \"address\": \"string\",
                    \"cost\": \"string\",
                    \"duration\": \"string\",
                    \"best_time\": \"string\"
                }
            ]
        }
    ],
    \"costs\": [
        {
            \"transportation\": [
                {\"type\": \"string\", \"cost\": \"string\"}
            ],
            \"dining\": [
                {\"category\": \"string\", \"cost_range\": \"string\"}
            ]
        }
    ],
    \"additional_information\": {
        \"local_currency\": \"string\",
        \"exchange_rate\": \"string\",
        \"timezone\": \"string\",
        \"weather_forecast\": \"string\",
        \"transportation_options\": \"string\"
    },
    \"flight_recommendations\": {
        \"recommended_airports\": [
            {\"name\": \"string\", \"code\": \"string\", \"distance_to_city\": \"string\"}
        ],
        \"airlines\": [
            {\"name\": \"string\", \"typical_price_range\": \"string\", \"flight_duration\": \"string\", \"notes\": \"string\"}
        ],
        \"best_booking_time\": \"string\",
        \"travel_tips\": [\"string\"]
    }
}
";

        $apiKey = env('GOOGLE_GEN_AI_API_KEY');

     

        $response = Http::timeout(60) // ⏳ increase timeout
            ->retry(3, 5000)              // 🔁 optional retry: 3 attempts, 5s delay
            ->withHeaders([
                'Content-Type' => 'application/json',
            ])->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-2.0-flash:generateContent?key=$apiKey", [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $prompt],
                    ],
                ],
            ],
        ]);

        // Step 3: Decode the API response
        $responseBody = $response->json();

        // Get the generated text and clean it
        $generatedText = $responseBody['candidates'][0]['content']['parts'][0]['text'];

        // Clean the response - remove any markdown formatting or extra text
        $generatedText = preg_replace('/```json\s*|\s*```/', '', $generatedText);
        $generatedText = trim($generatedText);

        try {
            $travelPlan = json_decode($generatedText, true, 512, JSON_THROW_ON_ERROR);

            // Validate that the itinerary includes all days
            if (count($travelPlan['itinerary']) < $totalDays) {
                throw new \Exception("Incomplete itinerary: Expected {$totalDays} days but received " . count($travelPlan['itinerary']) . " days");
            }

            // Validate that each day has the required number of activities
            foreach ($travelPlan['itinerary'] as $dayPlan) {
                if (count($dayPlan['activities']) < 4) {
                    throw new \Exception("Day {$dayPlan['day']} has fewer than 4 activities");
                }
            }
        } catch (\JsonException $e) {
            \Log::error('AI Response JSON parsing failed', [
                'error'    => $e->getMessage(),
                'response' => $generatedText,
            ]);
            throw new \Exception('Failed to generate valid travel plan. Please try again.');
        }

        // Step 4: Save data to the database
        // Save Location Overview
        $locationOverview = LocationOverview::create([
            'history_and_culture'             => $travelPlan['location_overview']['history_and_culture'],
            'local_customs_and_traditions'    => $travelPlan['location_overview']['local_customs_and_traditions'],
            'geographic_features_and_climate' => $travelPlan['location_overview']['geographic_features_and_climate'],
        ]);

        // Save Security Advice
        $securityAdvice = SecurityAdvice::create([
            'location_overview_id'  => $locationOverview->id,
            'overall_safety_rating' => data_get($travelPlan, 'location_overview.security_advice.overall_safety_rating'),
            'emergency_numbers'     => data_get($travelPlan, 'location_overview.security_advice.emergency_numbers'),
            'areas_to_avoid'        => data_get($travelPlan, 'location_overview.security_advice.areas_to_avoid'),
            'common_scams'          => data_get($travelPlan, 'location_overview.security_advice.common_scams'),
            'safety_tips'           => data_get($travelPlan, 'location_overview.security_advice.safety_tips', []),
            'health_precautions'    => data_get($travelPlan, 'location_overview.security_advice.health_precautions')
        ]);

        // Save Emergency Facilities
        $emergencyFacilities = data_get($travelPlan, 'location_overview.security_advice.local_emergency_facilities', []);
        foreach ($emergencyFacilities as $facility) {
            $securityAdvice->emergencyFacilities()->create([
                'name'    => data_get($facility, 'name'),
                'address' => data_get($facility, 'address'),
                'phone'   => data_get($facility, 'phone'),
            ]);
        }

        $historicalLandmarks = data_get($travelPlan, 'location_overview.historical_events_and_landmarks', []);

        foreach ($historicalLandmarks as $landmark) {
            $locationOverview->historicalEventsAndLandmarks()->create([
                'name'                 => data_get($landmark, 'name', 'Unknown Landmark'),                // Default if name is missing
                'description'          => data_get($landmark, 'description', 'No description available'), // Default if description is missing
                'location_overview_id' => $locationOverview->id,                                          // Ensure this relates to the correct location overview
            ]);
        }

        foreach ($travelPlan['location_overview']['cultural_highlights'] as $highlight) {
            $locationOverview->culturalHighlights()->create($highlight);
        }

       // Save Hotels
       foreach ($travelPlan['hotels'] as $hotelData) {
        $hotelData['location_overview_id'] = $locationOverview->id;
        $hotel = Hotel::create($hotelData);
    
        if (!empty($hotelData['name'])) {
            $imageUrl = GooglePlacesHelper::getPlacePhotoUrl($hotelData['name']); // or pass photo_reference
    
            if (!str_starts_with($imageUrl, 'Error:')) {
                $hotel->update([
                    'image_url' => $imageUrl,
                ]);
            }
        }
    }
    

    foreach ($travelPlan['itinerary'] as $dayPlan) {
        $itinerary = Itinerary::create([
            'day'                  => $dayPlan['day'],
            'location_overview_id' => $locationOverview->id,
        ]);
    
        foreach ($dayPlan['activities'] as $activityData) {
            $activityData['itinerary_id']         = $itinerary->id;
            $activityData['location_overview_id'] = $locationOverview->id;
    
            // If place_id is available, fetch photo
            if (!empty($activityData['name'])) {
                $activityImage = GooglePlacesHelper::getPlacePhotoUrl($activityData['name']);
    
                if (!str_starts_with($activityImage, 'Error:')) {
                    $activityData['image_url'] = $activityImage;
                }
            }
    
            Activity::create($activityData);
        }
    }
    

        // Save Costs
        foreach ($travelPlan['costs'] as $costData) {
            $costData['location_overview_id'] = $locationOverview->id;
            $cost                             = Cost::create($costData);

            foreach ($costData['transportation'] as $transportData) {
                $transportData['cost_id']              = $cost->id;
                $transportData['location_overview_id'] = $locationOverview->id;
                TransportationCost::create($transportData);
            }

            foreach ($costData['dining'] as $diningData) {
                $diningData['cost_id']              = $cost->id;
                $diningData['location_overview_id'] = $locationOverview->id;
                DiningCost::create($diningData);
            }
        }

        // Save Additional Information
        $additionalInfoData                         = $travelPlan['additional_information'];
        $additionalInfoData['location_overview_id'] = $locationOverview->id;
        AdditionalInformation::create($additionalInfoData);

        // After creating LocationOverview, add this:
        $tripDetail->update(['location_overview_id' => $locationOverview->id]);

        // Add flight recommendations
        if (isset($travelPlan['flight_recommendations'])) {
            $flightRecommendations = $locationOverview->flightRecommendations()->create([
                'best_booking_time' => $travelPlan['flight_recommendations']['best_booking_time'],
                'travel_tips'       => $travelPlan['flight_recommendations']['travel_tips'],
            ]);

            foreach ($travelPlan['flight_recommendations']['recommended_airports'] as $airport) {
                $flightRecommendations->airports()->create($airport);
            }

            foreach ($travelPlan['flight_recommendations']['airlines'] as $airline) {
                $flightRecommendations->airlines()->create($airline);
            }
        }

        // At the end of the method, before the redirect
        return redirect()->route('trips.show', $locationOverview->id)
            ->with('reference_code', $referenceCode)
            ->with('success', 'Travel plan generated successfully!');
    }

    public function show($tripId)
    {

        $locationOverview = LocationOverview::with([
            'securityAdvice.emergencyFacilities',
            'historicalEventsAndLandmarks',
            'culturalHighlights',
        ])->findOrFail($tripId);

        $tripDetails = TripDetail::where('location_overview_id', $tripId)->firstOrFail();

        return view('travelResult', [
            'locationOverview'      => $locationOverview,
            'securityAdvice'        => $locationOverview->securityAdvice,
            'hotels'                => Hotel::where('location_overview_id', $tripId)->get(),
            'itineraries'           => Itinerary::with('activities')
                ->where('location_overview_id', $tripId)
                ->orderBy('day')
                ->get(),
            'cost'                  => Cost::with(['transportationCosts', 'diningCosts'])
                ->where('location_overview_id', $tripId)
                ->firstOrFail(),
            'additionalInfo'        => AdditionalInformation::where('location_overview_id', $tripId)->first(),
            'tripDetails'           => $tripDetails,
            'referenceCode'         => $tripDetails->reference_code,
            'location'              => $tripDetails->location,
            'duration'              => $tripDetails->duration,
            'traveler'              => $tripDetails->traveler,
            'budget'                => $tripDetails->budget,
            'activities'            => $tripDetails->activities,
            'flightRecommendations' => $locationOverview->flightRecommendations()->with(['airports', 'airlines'])->first(),
        ]);
    }

    public function showByReference($referenceCode)
    {
        $tripDetails = TripDetail::where('reference_code', $referenceCode)->firstOrFail();
        return redirect()->route('trips.show', $tripDetails->location_overview_id);
    }

    /**
     * Display all trips for the authenticated user.
     *
     * @return \Illuminate\View\View
     */
    public function myTrips()
    {
        $trips = TripDetail::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(6); // Show 6 trips per page

        return view('myTrips', [
            'trips' => $trips,
        ]);
    }
}
