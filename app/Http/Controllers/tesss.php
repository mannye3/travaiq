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
                    
                    // Log session state before storing temp plan
                    Log::info('Session state before storing temp plan', [
                        'session_id' => session()->getId(),
                        'all_session_keys' => array_keys(session()->all())
                    ]);

                    // Store in session instead of database
                    $tempPlanData = [
                        'plan' => $travelPlan,
                        'reference_code' => $referenceCode,
                        'location' => $location,
                        'duration' => $totalDays,
                        'traveler' => $traveler,
                        'budget' => $budget,
                        'activities' => $activities,
                        'created_at' => now()
                    ];

                    session(['temp_travel_plan' => $tempPlanData]);

                    // Log session state after storing temp plan
                    Log::info('Session state after storing temp plan', [
                        'session_id' => session()->getId(),
                        'has_temp_plan' => session()->has('temp_travel_plan'),
                        'temp_plan_keys' => array_keys(session('temp_travel_plan')),
                        'all_session_keys' => array_keys(session()->all())
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
                        'activities' => $activities,
                        'session_state' => [
                            'session_id' => session()->getId(),
                            'all_session_keys' => array_keys(session()->all())
                        ]
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
                'request_data' => $request->all(),
                'session_state' => [
                    'session_id' => session()->getId(),
                    'all_session_keys' => array_keys(session()->all())
                ]
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

    private function searchHotels($location)
    {
        $suggestionResponse = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0.4472.124 Safari/537.36',
            'Cookie' => 'agoda.user.03=UserId=cfd674be-8057-4d6f-822b-c7bf6e0f5c81; agoda.prius=PriusID=0&PointsMaxTraffic=Agoda; agoda.price.01=PriceView=1; _fbp=fb.1.1747255400191.110289736142583136; FPID=FPID2.2.3fNFiFLhlwxGUX3tl8ReET22KM9%2B%2FPZu9l%2BsCoVmunM%3D.1747255402; agoda_ptnr_tracking=73c2d7f7-c987-42f7-8fc5-59d832f9517b; ai_user=AZS7E3QxNyALZTyevoYQd6|2025-05-14T20:44:22.442Z; agoda.version.03=CookieId=407a6398-48f3-4068-8eb3-d65cd0b41b8c&DLang=en-us&CurLabel=USD; agoda.familyMode=Mode=0; __RequestVerificationToken=ql40Y0A_J3s9AK5t_dLhoz383AuKI58SQAqnqcpbLIac0XF6Uwsksb0qu0PxN4WNJQ6ROuQnf19sfn4_dq3d0ZxOaoU1; _gid=GA1.2.890101482.1749716159; ul.session=7e7d4ccf-4579-4504-b2b0-9cc7f0e06e9c; ASP.NET_SessionId=gat1js5rtrwpqdsw2tgd43m3; xsrf_token=CfDJ8Dkuqwv-0VhLoFfD8dw7lYzvlw_S31Yy07x5NBPLTUrWCJL9CS4GMyYOrOWCKbZr3m6J9thqkSnXe-fDI49jnSpPUpfbmzrXzASMTMeSzBGAxoj3Vb0gzx7ycnMOr4zLURZoHdHvYp0BHavsZbmB6jk; ul.pc.token=eyJhbGciOiJFUzI1NiJ9.eyJtIjo0MjYzMDk5MjMsInIiOlsiMTI3XzM0ODM4NSJdLCJlIjoiNWRpQCpBSG9AaGQ7L0ZFbkgrMCZSPGAlMTpMKTcyXCJmVWYxMzBOcVpiblxcSS5Ea1pvS2tSTk8wUl1QS0A0QXNVX0s8VzhaalhSXFxybk9FaEoiLCJzcmMiOiJzcmMiLCJzdWIiOiJRaHJZakduZFJpR2lOVnFlbWFOVEl3IiwianRpIjoic1VqNEJ1SV9SNjZZLXVLZFNPSEYyQSIsImlhdCI6MTc0OTcxNjE3OSwiZXhwIjoxNzU3NDkyMTc5LCJ3bHQiOiJmMWE1OTA1Zi05NjIwLTQ1ZTUtOWQ5MS1kMjUxYzA3ZTBiNDIiLCJzIjoyfQ.cB3OvEXFhsFLjsPZ6kbfEQSCG8y65_c8XA2izK5t6q20B-blWP4qcpmn4efYT2YPG2Q7PPUxfm-Rk3E7yoUlYQ; agoda.firstclicks=1942345||||2025-06-12T15:17:37||kb12fm2xbjnnlqatjpf0f5ng||{"IsPaid":true,"gclid":"","Type":""}; ASP.NET_SessionId=kb12fm2xbjnnlqatjpf0f5ng; tealiumEnable=true; FPLC=IBJnEGGdN1WJkouQKElLq7PPzpRy%2BtarlfGJYPosnpPsknjVh8hr2abqND59o9aHe%2FpuqydjAY0mae7g8YWIFT0vuaY4yI3nc4yeOx6bj7hTURvIspP3UUbg3v7%2FMw%3D%3D; _ga_PJFPLJP2TM=GS2.1.s1749716172$o4$g1$t1749716329$j60$l0$h0; agoda.search.01=SHist=4$68211719$9010$1$1$2$1$0$1747257140$17$0|1$20711$9010$1$1$2$1$0$1749716543$17$0|1$1063$9010$1$1$2$1$0$1749716710$17$0&H=8900|0$68211719; agoda.attr.03=ATItems=1922878$05-19-2025 03:29$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1739459$06-12-2025 15:16$|1922878$06-12-2025 16:34$9c6545bc-1666-4ddf-9cd0-c70620f2972d; agoda.landings=1942345|||kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T15:17:37|True|19----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE|kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T16:34:56|True|20----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE|kb12fm2xbjnnlqatjpf0f5ng|2025-06-12T16:34:56|True|99; agoda.lastclicks=1922878||9c6545bc-1666-4ddf-9cd0-c70620f2972d||2025-06-12T16:34:56||kb12fm2xbjnnlqatjpf0f5ng||{"IsPaid":true,"gclid":"CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE","Type":""}; utag_main=v_id:0196d089833a0016e0e418d5b9df0506f008006700838$_sn:4$_se:1$_ss:1$_st:1749722701029$ses_id:1749720901029%3Bexp-session$_pn:1%3Bexp-session; _gcl_aw=GCL.1749720905.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _ga_T408Z268D2=GS2.1.s1749720905$o7$g1$t1749720905$j60$l0$h1935505518; _ga=GA1.2.696457105.1747255402; _gac_UA-6446424-30=1.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _ha_aw=GCL.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _hab_aw=GCL.1749720908.CjwKCAjw9anCBhAWEiwAqBJ-cytRPKCIJe6icK5PE34wi4SKPwQnOlnYrXIuRI5aGmzkQ94uDBzBZBoCoToQAvD_BwE; _uetsid=b7a744c0476511f0a7566d08cb141b53|46mprx|2|fwp|0|1989; _gcl_au=1.2.148026638.1747255400; _ga_C07L4VP9DZ=GS2.2.s1749720911$o4$g0$t1749720911$j60$l0$h0; __gads=ID=c7e1c6cbd973c570:T=1747255396:RT=1749720911:S=ALNI_MaTQseKMeSHylaabZ7b82b2EZqZ7A; __gpi=UID=000010ad0aa2a9b2:T=1747255396:RT=1749720911:S=ALNI_MZmY8tVwapTHo69em5pySZsktpkTg; cto_bundle=1ipviV92c3FMTmtmSUM1OEI1bm9GWmhEUEpBYUpPekVCbHQ2Q1d5alVjRHFiTzFWTWppdlc2NmRtN3B1QjBuUzBzOEV6dHJqeFN5VW5GcXFlTkJPTkx5VjhjV2JjTjFyYUFyZldnT21mUThIZWlyMDclMkZTaHElMkIxVHdRRUMxa0F5bXN0aVVMY3dXWW5LeU84YXpqaEhibmpyTnFRJTNEJTNE; deviceId=eb9c7c5c-d9ae-4515-8df2-8311f746d9f8; agoda.consent=NG||2025-06-12 09:35:21Z; forterToken=583aaa81cbdc45adb264dfbf2283ec04_1749720923140__UDF43_24ck_; _uetvid=1255d1a0310411f0b9e191098651552a|1mv4r9j|1749723808940|1|1|bat.bing.com/p/conversions/c/h; t_pp=wpr2cuqagh4lzxmktebs; agoda.analytics=Id=4242643095643895756&Signature=5886981715704138779&', // Replace with your real cookie from browser
            'Accept' => 'application/json',
        ])->get('https://partners.agoda.com/HotelSuggest/GetSuggestions', [
            'type' => 1,
            'limit' => 10,
            'term' => $location,
        ]);
    
        $suggestions = $suggestionResponse->json();

        if (empty($suggestions) || !is_array($suggestions)) {
            throw new TravelPlanException('No city suggestions found for the location.', [
                'location' => $location
            ]);
        }

        $cityId = $suggestions[0]['Value'] ?? null;

        if (!$cityId) {
            throw new TravelPlanException('City ID not found for the location.', [
                'location' => $location
            ]);
        }

        $hotelResponse = Http::withHeaders([
            'Content-Type' => 'application/json',
            'Accept-Encoding' => 'gzip,deflate',
            'Authorization' => '1942345:87b86d0a-6e0a-4d14-b6f2-d8582e3cb3aa',
        ])->post('https://affiliateapi7643.agoda.com/affiliateservice/lt_v1', [
            'criteria' => [
                'additional' => [
                    'currency' => 'USD',
                    'dailyRate' => [
                        'maximum' => 10000,
                        'minimum' => 1
                    ],
                    'discountOnly' => false,
                    'language' => 'en-us',
                    'maxResult' => 10,
                    'minimumReviewScore' => 0,
                    'minimumStarRating' => 0,
                    'occupancy' => [
                        'numberOfAdult' => 2,
                        'numberOfChildren' => 1
                    ],
                    'sortBy' => 'PriceAsc'
                ],
                'checkInDate' => '2025-06-20',
                'checkOutDate' => '2025-06-25',
                'cityId' => $cityId
            ]
        ]);

        if ($hotelResponse->failed()) {
            throw new TravelPlanException('Failed to retrieve hotel data from Agoda API.', [
                'status' => $hotelResponse->status(),
                'response' => $hotelResponse->body()
            ]);
        }

        return $hotelResponse->json();
    }

    private function createHotels($locationOverview, $travelPlan)
    {
        try {
            // Get hotels from Agoda API
            $hotelData = $this->searchHotels($travelPlan['location']);

            $hotels = collect($hotelData['results'] ?? [])
                ->map(function ($hotelData) use ($locationOverview) {
                    return [
                        'location_overview_id' => $locationOverview->id,
                        'name' => $hotelData['hotelName'] ?? 'Unknown Hotel',
                        'address' => 'Address not available',
                        'price_per_night' => $hotelData['dailyRate'] ?? 'Price not available',
                        'rating' => $hotelData['starRating'] ?? '0',
                        'description' => 'No description available',
                        'coordinates' => json_encode([
                            'latitude' => $hotelData['latitude'] ?? 0,
                            'longitude' => $hotelData['longitude'] ?? 0
                        ]),
                        'image_url' => $hotelData['imageURL'] ?? 'https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900',
                        'review_score' => $hotelData['reviewScore'] ?? 0,
                        'review_count' => $hotelData['reviewCount'] ?? 0,
                        'currency' => $hotelData['currency'] ?? 'USD',
                        'landing_url' => $hotelData['landingURL'] ?? '',
                        'include_breakfast' => $hotelData['includeBreakfast'] ?? false,
                        'free_wifi' => $hotelData['freeWifi'] ?? false
                    ];
                })->toArray();

            if (!empty($hotels)) {
                Hotel::insert($hotels);
            }
        } catch (Exception $e) {
            Log::error('Failed to create hotels', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'location' => $travelPlan['location']
            ]);
            throw new TravelPlanException('Failed to create hotels: ' . $e->getMessage(), [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
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

        // Get Google Places image for the location if not already set
        if (!$tripDetails->google_place_image) {
            $google_place_image = GooglePlacesHelper::getPlacePhotoUrl($tripDetails->location);
            if (!str_starts_with($google_place_image, 'Error:')) {
                $tripDetails->update(['google_place_image' => $google_place_image]);
            }
        }

        return view('pages.travelResult', [
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

        return view('pages.myTrips', [
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

        // Fetch hotels from Agoda API
        try {
            Log::info('Fetching hotels for location: ' . $tempPlan['location']);
            $hotelData = $this->searchHotels($tempPlan['location']);
            Log::info('Hotel API Response:', ['data' => $hotelData]);

            if (empty($hotelData) || !isset($hotelData['results'])) {
                Log::warning('No hotels found in API response', ['response' => $hotelData]);
                $hotels = [];
            } else {
                $hotels = collect($hotelData['results'])->map(function($hotel) {
                    $hotelObject = (object) [
                        'name' => $hotel['hotelName'] ?? 'Unknown Hotel',
                        'address' => 'Address not available',
                        'price_per_night' => $hotel['dailyRate'] ?? 'Price not available',
                        'rating' => $hotel['starRating'] ?? '0',
                        'description' => 'No description available',
                        'coordinates' => [
                            'latitude' => $hotel['latitude'] ?? 0,
                            'longitude' => $hotel['longitude'] ?? 0
                        ],
                        'image_url' => $hotel['imageURL'] ?? 'https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900',
                        
                        'review_score' => $hotel['reviewScore'] ?? 0,
                        'review_count' => $hotel['reviewCount'] ?? 0,
                        'currency' => $hotel['currency'] ?? 'USD',
                        'landing_url' => $hotel['landingURL'] ?? '',
                        'include_breakfast' => $hotel['includeBreakfast'] ?? false,
                        'free_wifi' => $hotel['freeWifi'] ?? false
                    ];
                    Log::info('Processed hotel:', ['hotel' => $hotelObject]);
                    return $hotelObject;
                })->all();
            }
        } catch (Exception $e) {
            Log::error('Failed to fetch hotels for temp plan', [
                'location' => $tempPlan['location'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            $hotels = [];
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

        Log::info('Final hotels array:', ['hotels' => $hotels]);

        return view('pages.tempTravelResult', compact(
            'tripDetails',
            'locationOverview',
            'additionalInfo',
            'securityAdvice',
            'hotels',
            'itineraries'
        ));
    }

    public function testHotelIntegration(Request $request)
    {
        try {
             $location = $request->input('location', 'Cape Town, South Africa');
           return  $hotelData = $this->searchHotels($location);
            
            return response()->json([
                'success' => true,
                'message' => 'Hotel integration test successful',
                'data' => $hotelData
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Hotel integration test failed',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}
