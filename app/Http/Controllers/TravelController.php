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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Prompts\TravelPlanPrompt;
use App\Models\RequestLog;
 use Barryvdh\DomPDF\Facade\Pdf;
use Exception;

class TravelPlanException extends Exception
{
    protected $context;

    public function __construct($message, $context = [], $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->context = $context;
    }

    public function getContext()
    {
        return $this->context;
    }
}

class TravelController extends Controller
{
    private function logError($message, $context = [], $exception = null)
    {
        $logContext = array_merge($context, [
            'trace' => $exception ? $exception->getTraceAsString() : null,
            'file' => $exception ? $exception->getFile() : null,
            'line' => $exception ? $exception->getLine() : null,
        ]);

        Log::error($message, $logContext);
    }

    private function handleError($exception, $request)
    {
        $errorMessage = 'An error occurred while generating your travel plan.';
        $errorDetails = null;

        if ($exception instanceof TravelPlanException) {
            $errorMessage = $exception->getMessage();
            $errorDetails = $exception->getContext();
        } elseif ($exception instanceof \JsonException) {
            $errorMessage = 'Failed to process the travel plan data. Please try again.';
        } elseif ($exception instanceof \Illuminate\Database\QueryException) {
            $errorMessage = 'Database error occurred. Please try again later.';
        }

        $this->logError($errorMessage, [
            'error' => $exception->getMessage(),
            'request_data' => $request->all(),
            'error_details' => $errorDetails
        ], $exception);

        return redirect()->back()
            ->with('error', $errorMessage)
            ->with('error_details', $errorDetails)
            ->withInput();
    }

    private function validateTravelPlan($travelPlan, $totalDays)
    {
        $errors = [];

        // Validate total days
        if (count($travelPlan['itinerary']) < $totalDays) {
            $errors[] = "Incomplete itinerary: Expected {$totalDays} days but received " . count($travelPlan['itinerary']) . " days";
        }

        // Validate activities per day
        foreach ($travelPlan['itinerary'] as $dayPlan) {
            $dayNumber = $dayPlan['day'];
            $activityCount = count($dayPlan['activities']);
            
            if ($activityCount < 3) { // Reduced minimum to 3 activities per day
                $errors[] = "Day {$dayNumber} has only {$activityCount} activities. Please try generating the plan again.";
            }
        }

        // Validate required sections
        $requiredSections = [
            'location_overview',
            'hotels',
            'costs',
            'additional_information'
        ];

        foreach ($requiredSections as $section) {
            if (!isset($travelPlan[$section])) {
                $errors[] = "Missing required section: {$section}";
            }
        }

        if (!empty($errors)) {
            throw new TravelPlanException('Travel plan validation failed', [
                'errors' => $errors,
                'total_days' => $totalDays,
                'received_days' => count($travelPlan['itinerary']),
                'plan_summary' => [
                    'days_with_activities' => array_map(function($day) {
                        return [
                            'day' => $day['day'],
                            'activity_count' => count($day['activities'])
                        ];
                    }, $travelPlan['itinerary'])
                ]
            ]);
        }
    }

    public function generateTravelPlan(Request $request)
    {
        set_time_limit(300);
        
        try {
            // Validate required fields
            $requiredFields = ['location', 'duration', 'traveler', 'budget', 'activities'];
            foreach ($requiredFields as $field) {
                if (!$request->has($field)) {
                    throw new TravelPlanException("Missing required field: {$field}", [
                        'field' => $field,
                        'request_data' => $request->all()
                    ]);
                }
            }

            // Step 1: Validate and prepare input data
            $location = $request->input('location');
            $totalDays = $request->input('duration');
            $traveler = $request->input('traveler');
            $budget = $request->input('budget');
            
            try {
                $activities = implode(', ', json_decode($request->input('activities'), true));
            } catch (\JsonException $e) {
                throw new TravelPlanException('Invalid activities format', [
                    'activities' => $request->input('activities')
                ]);
            }

            // Generate reference code
            $referenceCode = strtoupper(Str::random(8));

            // Check if user is authenticated
            if (Auth::check()) {
                // Use DB transaction for authenticated users
                return \DB::transaction(function () use ($request, $location, $totalDays, $traveler, $budget, $activities, $referenceCode) {
                    try {
                        // Create request log
                        RequestLog::create([
                            'location' => $location,
                            'duration' => $totalDays,
                            'traveler' => $traveler,
                            'budget' => $budget,
                            'activities' => $request->input('activities'),
                        ]);

                        // Create trip detail
                        $tripDetail = TripDetail::create([
                            'reference_code' => $referenceCode,
                            'location' => $location,
                            'duration' => $totalDays,
                            'traveler' => $traveler,
                            'budget' => $budget,
                            'activities' => $activities,
                            'user_id' => Auth::id(),
                        ]);

                        // Generate and process travel plan
                        $travelPlan = $this->generateAndProcessTravelPlan($location, $totalDays, $traveler, $budget, $activities);
                        
                        // Save to database for authenticated users
                        $locationOverview = $this->saveTravelPlanToDatabase($travelPlan, $tripDetail);

                        return redirect()->route('trips.show', $locationOverview->id)
                            ->with('reference_code', $referenceCode)
                            ->with('success', 'Travel plan generated successfully!');
                    } catch (Exception $e) {
                        Log::error('Error in transaction closure', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString(),
                            'location' => $location,
                            'totalDays' => $totalDays,
                            'traveler' => $traveler,
                            'budget' => $budget,
                            'activities' => $activities
                        ]);
                        throw new TravelPlanException('Error during travel plan generation', [
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ], 0, $e);
                    }
                });
            } else {
                // For unauthenticated users, generate plan without saving to database
                try {
                    $travelPlan = $this->generateAndProcessTravelPlan($location, $totalDays, $traveler, $budget, $activities);
                    
                    // Store in session instead of database
                    session([
                        'temp_travel_plan' => [
                            'plan' => $travelPlan,
                            'reference_code' => $referenceCode,
                            'location' => $location,
                            'duration' => $totalDays,
                            'traveler' => $traveler,
                            'budget' => $budget,
                            'activities' => $activities,
                            'created_at' => now()
                        ]
                    ]);
                    
                    return redirect()->route('trips.show.temp')
                        ->with('reference_code', $referenceCode)
                        ->with('success', 'Travel plan generated successfully!');
                } catch (Exception $e) {
                    Log::error('Error in unauthenticated flow', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                        'location' => $location,
                        'totalDays' => $totalDays,
                        'traveler' => $traveler,
                        'budget' => $budget,
                        'activities' => $activities
                    ]);
                    throw new TravelPlanException('Error during travel plan generation', [
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString()
                    ], 0, $e);
                }
            }
        } catch (Exception $e) {
            Log::error('Error in generateTravelPlan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            return $this->handleError($e, $request);
        }
    }

    private function generateAndProcessTravelPlan($location, $totalDays, $traveler, $budget, $activities)
    {
        try {
            $prompt = TravelPlanPrompt::generate($location, $totalDays, $traveler, $budget, $activities);
            $apiKey = env('GOOGLE_GEN_AI_API_KEY');

            if (empty($apiKey)) {
                throw new TravelPlanException('AI service configuration error', [
                    'service' => 'Gemini AI'
                ]);
            }

            $response = Http::timeout(60)
                ->retry(3, 5000)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=$apiKey", [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                throw new TravelPlanException('Failed to generate travel plan from AI service', [
                    'status' => $response->status(),
                    'response' => $response->body()
                ]);
            }

            // Process AI response
            $responseBody = $response->json();
            $generatedText = $responseBody['candidates'][0]['content']['parts'][0]['text'];
            $generatedText = preg_replace('/```json\s*|\s*```/', '', $generatedText);
            $generatedText = trim($generatedText);

            try {
                $travelPlan = json_decode($generatedText, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new TravelPlanException('Invalid AI response format', [
                    'response' => $generatedText
                ]);
            }

            // Validate the travel plan
            $this->validateTravelPlan($travelPlan, $totalDays);

            return $travelPlan;
        } catch (Exception $e) {
            Log::error('Error in generateAndProcessTravelPlan', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'location' => $location,
                'totalDays' => $totalDays,
                'traveler' => $traveler,
                'budget' => $budget,
                'activities' => $activities
            ]);
            throw $e;
        }
    }

    private function saveTravelPlanToDatabase($travelPlan, $tripDetail)
    {
        // Step 5: Create location overview and related data
        $locationOverview = LocationOverview::create([
            'history_and_culture' => $travelPlan['location_overview']['history_and_culture'],
            'local_customs_and_traditions' => $travelPlan['location_overview']['local_customs_and_traditions'],
            'geographic_features_and_climate' => $travelPlan['location_overview']['geographic_features_and_climate'],
        ]);

        // Step 6: Batch create related data
        $this->createSecurityAdvice($locationOverview, $travelPlan);
        $this->createHistoricalLandmarks($locationOverview, $travelPlan);
        $this->createCulturalHighlights($locationOverview, $travelPlan);
        $this->createHotels($locationOverview, $travelPlan);
        $this->createItinerary($locationOverview, $travelPlan);
        $this->createCosts($locationOverview, $travelPlan);
        $this->createAdditionalInfo($locationOverview, $travelPlan);
        $this->createFlightRecommendations($locationOverview, $travelPlan);

        // Update trip detail with location overview
        $tripDetail->update(['location_overview_id' => $locationOverview->id]);

        // Get Google Places image in parallel
        $this->updateGooglePlaceImage($tripDetail, $tripDetail->location);

        return $locationOverview;
    }

    private function createSecurityAdvice($locationOverview, $travelPlan)
    {
        $securityAdvice = SecurityAdvice::create([
            'location_overview_id' => $locationOverview->id,
            'overall_safety_rating' => data_get($travelPlan, 'location_overview.security_advice.overall_safety_rating'),
            'emergency_numbers' => data_get($travelPlan, 'location_overview.security_advice.emergency_numbers'),
            'areas_to_avoid' => data_get($travelPlan, 'location_overview.security_advice.areas_to_avoid'),
            'common_scams' => data_get($travelPlan, 'location_overview.security_advice.common_scams'),
            'safety_tips' => data_get($travelPlan, 'location_overview.security_advice.safety_tips', []),
            'health_precautions' => data_get($travelPlan, 'location_overview.security_advice.health_precautions')
        ]);

        // Batch create emergency facilities
        $emergencyFacilities = collect(data_get($travelPlan, 'location_overview.security_advice.local_emergency_facilities', []))
            ->map(function ($facility) use ($securityAdvice) {
                return [
                    'name' => data_get($facility, 'name'),
                    'address' => data_get($facility, 'address'),
                    'phone' => data_get($facility, 'phone'),
                    'security_advice_id' => $securityAdvice->id
                ];
            })->toArray();

        if (!empty($emergencyFacilities)) {
            $securityAdvice->emergencyFacilities()->createMany($emergencyFacilities);
        }
    }

    private function createHistoricalLandmarks($locationOverview, $travelPlan)
    {
        $landmarks = collect(data_get($travelPlan, 'location_overview.historical_events_and_landmarks', []))
            ->map(function ($landmark) use ($locationOverview) {
                return [
                    'name' => data_get($landmark, 'name', 'Unknown Landmark'),
                    'description' => data_get($landmark, 'description', 'No description available'),
                    'location_overview_id' => $locationOverview->id
                ];
            })->toArray();

        if (!empty($landmarks)) {
            $locationOverview->historicalEventsAndLandmarks()->createMany($landmarks);
        }
    }

    private function createCulturalHighlights($locationOverview, $travelPlan)
    {
        $highlights = collect($travelPlan['location_overview']['cultural_highlights'])
            ->map(function ($highlight) use ($locationOverview) {
                $highlight['location_overview_id'] = $locationOverview->id;
                return $highlight;
            })->toArray();

        if (!empty($highlights)) {
            $locationOverview->culturalHighlights()->createMany($highlights);
        }
    }

    private function createHotels($locationOverview, $travelPlan)
    {
        $hotels = collect($travelPlan['hotels'])
            ->map(function ($hotelData) use ($locationOverview) {
                $hotelData['location_overview_id'] = $locationOverview->id;
                
                // Get image URL for hotel
                if (!empty($hotelData['name'])) {
                    try {
                        $imageUrl = GooglePlacesHelper::getPlacePhotoUrl($hotelData['name']);
                        if (!str_starts_with($imageUrl, 'Error:')) {
                            $hotelData['image_url'] = $imageUrl;
                            Log::info('Successfully fetched hotel image', [
                                'hotel_name' => $hotelData['name'],
                                'image_url' => $imageUrl
                            ]);
                        } else {
                            Log::warning('Failed to fetch hotel image - API error', [
                                'hotel_name' => $hotelData['name'],
                                'error' => $imageUrl
                            ]);
                            // Set a default image URL
                            $hotelData['image_url'] = 'https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900';
                        }
                    } catch (Exception $e) {
                        Log::error('Failed to fetch hotel image - Exception', [
                            'hotel_name' => $hotelData['name'],
                            'error' => $e->getMessage(),
                            'trace' => $e->getTraceAsString()
                        ]);
                        // Set a default image URL
                        $hotelData['image_url'] = 'https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900';
                    }
                } else {
                    // Set a default image URL if hotel name is empty
                    $hotelData['image_url'] = 'https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900';
                }
                
                return $hotelData;
            })->toArray();

        if (!empty($hotels)) {
            Hotel::insert($hotels);
        }
    }

    private function createItinerary($locationOverview, $travelPlan)
    {
        foreach ($travelPlan['itinerary'] as $dayPlan) {
            $itinerary = Itinerary::create([
                'day' => $dayPlan['day'],
                'location_overview_id' => $locationOverview->id,
            ]);
    
            $activities = collect($dayPlan['activities'])
                ->map(function ($activityData) use ($itinerary, $locationOverview) {
                    // Get image URL for activity
                    $imageUrl = null;
                    if (!empty($activityData['name'])) {
                        try {
                            $imageUrl = GooglePlacesHelper::getPlacePhotoUrl($activityData['name']);
                            if (str_starts_with($imageUrl, 'Error:')) {
                                Log::warning('Failed to fetch activity image - API error', [
                                    'activity_name' => $activityData['name'],
                                    'error' => $imageUrl
                                ]);
                                $imageUrl = null;
                            }
                        } catch (Exception $e) {
                            Log::warning('Failed to fetch activity image - Exception', [
                                'activity_name' => $activityData['name'],
                                'error' => $e->getMessage()
                            ]);
                            $imageUrl = null;
                        }
                    }

                    return [
                        'itinerary_id' => $itinerary->id,
                        'location_overview_id' => $locationOverview->id,
                        'name' => $activityData['name'] ?? '',
                        'description' => $activityData['description'] ?? '',
                        'coordinates' => $activityData['coordinates'] ?? '',
                        'address' => $activityData['address'] ?? '',
                        'cost' => $activityData['cost'] ?? '',
                        'duration' => $activityData['duration'] ?? '',
                        'best_time' => $activityData['best_time'] ?? '',
                        'image_url' => $imageUrl
                    ];
                })->toArray();

            if (!empty($activities)) {
                Activity::insert($activities);
            }
        }
    }

    private function createCosts($locationOverview, $travelPlan)
    {
        foreach ($travelPlan['costs'] as $costData) {
            $costData['location_overview_id'] = $locationOverview->id;
            $cost = Cost::create($costData);

            $transportationCosts = collect($costData['transportation'])
                ->map(function ($transportData) use ($cost, $locationOverview) {
                    $transportData['cost_id'] = $cost->id;
                    $transportData['location_overview_id'] = $locationOverview->id;
                    return $transportData;
                })->toArray();

            $diningCosts = collect($costData['dining'])
                ->map(function ($diningData) use ($cost, $locationOverview) {
                    $diningData['cost_id'] = $cost->id;
                    $diningData['location_overview_id'] = $locationOverview->id;
                    return $diningData;
                })->toArray();

            if (!empty($transportationCosts)) {
                TransportationCost::insert($transportationCosts);
            }

            if (!empty($diningCosts)) {
                DiningCost::insert($diningCosts);
            }
        }
    }

    private function createAdditionalInfo($locationOverview, $travelPlan)
    {
        $additionalInfoData = $travelPlan['additional_information'];
        $additionalInfoData['location_overview_id'] = $locationOverview->id;
        AdditionalInformation::create($additionalInfoData);
    }

    private function createFlightRecommendations($locationOverview, $travelPlan)
    {
        if (isset($travelPlan['flight_recommendations'])) {
            $flightRecommendations = $locationOverview->flightRecommendations()->create([
                'best_booking_time' => $travelPlan['flight_recommendations']['best_booking_time'],
                'travel_tips' => $travelPlan['flight_recommendations']['travel_tips'],
            ]);

            $airports = collect($travelPlan['flight_recommendations']['recommended_airports'])
                ->map(function ($airport) use ($flightRecommendations) {
                    $airport['flight_recommendation_id'] = $flightRecommendations->id;
                    return $airport;
                })->toArray();

            $airlines = collect($travelPlan['flight_recommendations']['airlines'])
                ->map(function ($airline) use ($flightRecommendations) {
                    $airline['flight_recommendation_id'] = $flightRecommendations->id;
                    return $airline;
                })->toArray();

            if (!empty($airports)) {
                $flightRecommendations->airports()->createMany($airports);
            }

            if (!empty($airlines)) {
                $flightRecommendations->airlines()->createMany($airlines);
            }
        }
    }

    private function updateGooglePlaceImage($tripDetail, $location)
    {
        $google_place_image = GooglePlacesHelper::getPlacePhotoUrl($location);
        if (!str_starts_with($google_place_image, 'Error:')) {
            $tripDetail->update(['google_place_image' => $google_place_image]);
        }
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
            'tripId'                => $tripId,
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
            ->get();

        return view('myTrips', [
            'trips' => $trips,
        ]);
    }

    public function downloadTrip($tripId)
    {
        $locationOverview = LocationOverview::with([
            'securityAdvice.emergencyFacilities',
            'historicalEventsAndLandmarks',
            'culturalHighlights',
        ])->findOrFail($tripId);

        $tripDetails = TripDetail::where('location_overview_id', $tripId)->firstOrFail();

        $pdf = PDF::loadView('pdf.itinerary', [
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
        
        // return $pdf->download('itinerary.pdf');
        return $pdf->stream('itinerary.pdf');
    }
    
    // Add this new method to handle image updates
    private function updateImagesForExistingRecords()
    {
        // Update hotel images
        $hotels = Hotel::whereNull('image_url')->get();
        foreach ($hotels as $hotel) {
            if (!empty($hotel->name)) {
                try {
                    $imageUrl = GooglePlacesHelper::getPlacePhotoUrl($hotel->name);
                    if (!str_starts_with($imageUrl, 'Error:')) {
                        $hotel->update(['image_url' => $imageUrl]);
                    }
                } catch (Exception $e) {
                    Log::warning('Failed to update hotel image', [
                        'hotel_id' => $hotel->id,
                        'hotel_name' => $hotel->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }

        // Update activity images
        $activities = Activity::whereNull('image_url')->get();
        foreach ($activities as $activity) {
            if (!empty($activity->name)) {
                try {
                    $imageUrl = GooglePlacesHelper::getPlacePhotoUrl($activity->name);
                    if (!str_starts_with($imageUrl, 'Error:')) {
                        $activity->update(['image_url' => $imageUrl]);
                    }
                } catch (Exception $e) {
                    Log::warning('Failed to update activity image', [
                        'activity_id' => $activity->id,
                        'activity_name' => $activity->name,
                        'error' => $e->getMessage()
                    ]);
                }
            }
        }
    }

    // Add this method to your controller
    public function updateMissingImages()
    {
        try {
            $this->updateImagesForExistingRecords();
            return redirect()->back()->with('success', 'Images updated successfully');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to update images: ' . $e->getMessage());
        }
    }

    // Add this new method to show temporary travel plan
    public function showTemp()
    {
        $tempPlan = session('temp_travel_plan');
        if (!$tempPlan) {
            return redirect()->to('/')->with('error', 'Temporary travel plan not found.');
        }

        // Create trip details object
        $tripDetails = (object) [
            'reference_code' => $tempPlan['reference_code'],
            'location' => $tempPlan['location'],
            'duration' => $tempPlan['duration'],
            'traveler' => $tempPlan['traveler'],
            'budget' => $tempPlan['budget'],
            'activities' => $tempPlan['activities'],
            'google_place_image' => null
        ];

        // Get Google Places image for the location
        try {
             $google_place_image = GooglePlacesHelper::getPlacePhotoUrl($tempPlan['location']);
            if (!str_starts_with($google_place_image, 'Error:')) {
                $tripDetails->google_place_image = $google_place_image;
            }
        } catch (Exception $e) {
            Log::warning('Failed to fetch location image', [
                'location' => $tempPlan['location'],
                'error' => $e->getMessage()
            ]);
        }

        // Convert arrays to objects for consistent property access
        $locationOverview = (object) $tempPlan['plan']['location_overview'];
        $additionalInfo = (object) $tempPlan['plan']['additional_information'];
        $securityAdvice = (object) $tempPlan['plan']['location_overview']['security_advice'];
        
        // Handle emergency facilities
        if (isset($tempPlan['plan']['location_overview']['security_advice']['local_emergency_facilities'])) {
            $securityAdvice->emergency_facilities = collect($tempPlan['plan']['location_overview']['security_advice']['local_emergency_facilities'])
                ->map(function($facility) {
                    return (object) $facility;
                })->all();
        }

        // Handle transportation and dining costs
        if (isset($tempPlan['plan']['costs'][0])) {
            $costs = $tempPlan['plan']['costs'][0];
            $additionalInfo->transportation_costs = collect($costs['transportation'] ?? [])
                ->map(function($transport) {
                    return (object) $transport;
                })->all();
            
            $additionalInfo->dining_costs = collect($costs['dining'] ?? [])
                ->map(function($dining) {
                    return (object) $dining;
                })->all();
        }

        // Handle local cuisine
        if (isset($tempPlan['plan']['location_overview']['local_cuisine'])) {
            $additionalInfo->local_cuisine = (object) $tempPlan['plan']['location_overview']['local_cuisine'];
        }
        
        // Convert hotels to objects
        $hotels = collect($tempPlan['plan']['hotels'])->map(function($hotel) {
            return (object) $hotel;
        })->all();

          // Get Google Places image for the hotels
          foreach ($hotels as $hotel) {
            if (!empty($hotel->name)) {
                $hotel->image_url = GooglePlacesHelper::getPlacePhotoUrl($hotel->name);
            }
          } 

        // Convert itineraries to objects
        $itineraries = collect($tempPlan['plan']['itinerary'])->map(function($day) {
            $dayObj = (object) $day;
            $dayObj->activities = collect($day['activities'])->map(function($activity) {
                return (object) $activity;
            })->all();
            return $dayObj;
        })->all();

          // Get Google Places image for the itineraries
          foreach ($itineraries as $itinerary) {
            foreach ($itinerary->activities as $activity) {
                if (!empty($activity->name)) {
                    $activity->image_url = GooglePlacesHelper::getPlacePhotoUrl($activity->name);
                }
            }
          }

          

        return view('tempTravelResult', compact(
            'tripDetails',
            'locationOverview',
            'additionalInfo',
            'securityAdvice',
            'hotels',
            'itineraries'
        ));
    }
}
