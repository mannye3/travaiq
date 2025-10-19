<?php

namespace App\Services;

use App\Helpers\GooglePlacesHelper;
use App\Http\Controllers\TravelPlanException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HotelRecommendationService
{
    private $agodaApiKey;
    private $agodaPartnerId;

    public function __construct()
    {
        $this->agodaApiKey = env('AGODA_API_KEY');
        $this->agodaPartnerId = env('AGODA_PARTNER_ID');
    }

    public function getHotelRecommendations($location, $checkInDate , $checkOutDate , $budget = null)
    {
        try {
            Log::info('Getting hotel recommendations', [
                'location' => $location,
                'checkInDate' => $checkInDate,
                'checkOutDate' => $checkOutDate,
                'budget' => $budget
            ]);

            // Normalize and determine budget range
            $budget = strtolower(trim($budget));
            if ($budget === 'low') {
                $min = 30; $max = 1000;
            } elseif ($budget === 'medium') {
                $min = 100; $max = 3000;
            } elseif ($budget === 'high') {
                $min = 200; $max = 10000;
            } else {
                $min = 30; $max = 10000;
            }

            // Get hotels from Agoda
            $agodaHotels = $this->getAgodaHotels($location, $checkInDate, $checkOutDate, $min, $max);
           
            if ($agodaHotels === null) {
                Log::warning('No hotels found from Agoda', [
                    'location' => $location,
                    'budget' => $budget
                ]);
                return [];
            }

            // Format the hotels
            $formattedHotels = $this->formatAgodaHotels($agodaHotels);
            
            if (empty($formattedHotels)) {
                Log::warning('No formatted hotels available', [
                    'raw_data' => $agodaHotels
                ]);
                return [];
            }

            Log::info('Successfully retrieved hotel recommendations', [
                'count' => count($formattedHotels),
                'sample' => array_slice($formattedHotels, 0, 2)
            ]);

            return $formattedHotels;
        } catch (\Exception $e) {
            Log::error('Error getting hotel recommendations', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'location' => $location,
                'budget' => $budget
            ]);
            return [];
        }
    }

    private function getAgodaHotels($location, $checkInDate, $checkOutDate, $min = 20, $max = 10000)
    {
        try {
            Log::info('Getting city suggestions from Agoda', [
                'location' => $location,
                'api_key' => $this->agodaApiKey ? 'present' : 'missing',
                'partner_id' => $this->agodaPartnerId ? 'present' : 'missing'
            ]);

            // Get city suggestions first with timeout
      

                  $suggestionResponse = Http::withHeaders([
            'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/91.0.4472.124 Safari/537.36',
            'Cookie' => 'agoda.user.03=UserId=cfd674be-8057-4d6f-822b-c7bf6e0f5c81; FPID=FPID2.2.3fNFiFLhlwxGUX3tl8ReET22KM9%2B%2FPZu9l%2BsCoVmunM%3D.1747255402; agoda_ptnr_tracking=73c2d7f7-c987-42f7-8fc5-59d832f9517b; ai_user=AZS7E3QxNyALZTyevoYQd6|2025-05-14T20:44:22.442Z; agoda.version.03=CookieId=407a6398-48f3-4068-8eb3-d65cd0b41b8c&DLang=en-us&CurLabel=USD; agoda.familyMode=Mode=0; deviceId=eb9c7c5c-d9ae-4515-8df2-8311f746d9f8; partnerLocale=en-us; agoda.search.01=SHist=4$68211719$9010$1$1$2$1$0$1747257140$17$0|1$1063$9010$1$1$2$1$0$1749716710$17$0|1$20711$9010$1$1$2$1$0$1749752817$17$0|4$141897$9007$3$1$2$1$0$1755330347$17$0&H=8993|93$68211719|65$5215052|64$28349439$61776270$47036141$5215052$71632249$5215052$2537731|63$45899118|60$5215052|55$73157830|0$141897; agoda.firstclicks=1922878||9c6545bc-1666-4ddf-9cd0-c70620f2972d||2025-09-23T15:21:14||zvjhvanoeqkyblb4hui4ikfv||{"IsPaid":true,"gclid":"","Type":""}; ASP.NET_SessionId=zvjhvanoeqkyblb4hui4ikfv; agoda.prius=PriusID=0&PointsMaxTraffic=Agoda; agoda.price.01=PriceView=1; tealiumEnable=true; _fbp=fb.1.1758615676926.680178922326575928; FPLC=AO1qbTK2dF6vdm5mL0wx2S1jUwxuecz2iBNpkC4suAhPZs%2BoE9kutj17Y33U8JOKx%2FAJAXOmGG6j42MUTEIZCNAQMZGxfU1O4G4hr7smXDIYno4P0q%2BsE1Q%2FxSmo%2FQ%3D%3D; _gid=GA1.2.42167763.1758615680; __RequestVerificationToken=stLjIHaILLKyOWQ63gPUWGTIJ4nH-GoWk5U7-G9JZYzITsJlH9sivcnJ-NOOXWFUkcG_xc8Yo706gvFIjpCCFVz86j01; xsrf_token=CfDJ8Dkuqwv-0VhLoFfD8dw7lYww3h1I8_IswgJsHT3sGRtd93QERzIM3m5AavBruhZoBqV2tugLmdO6eOHgIOSf4Ih8hXmPXJiG-8rS8oQU4W9Sv9Q48HSHhJAPWh88MsuydCEyBgPF83AhWn5mZm47jiU; t_pp=BpgbGqA9X4OcgNb4:0sDlN8n1n1XRo+LN/NsYuQ==:as3i4H/pwXA6FuVOrDmk28hYaFONGG4S1d6hFMR9M7J1EgaEDhDcJ9ZrT9l/4DiEeuiO6ydfdLx/tMd6AC+5nbE0IYIzyWMeNGd8mznKy9uB0v0tffx9Qg==; agoda-ptnr-sn=gp1hkgm0jthczoheesy4ch03; session_cache_pc={"Cache":"hkdata1","Time":"638942366378317471","SessionID":"gp1hkgm0jthczoheesy4ch03","CheckID":"2eaac234bfc3dbd617aaf4f40c52e7df7883665a","CType":"N"}; _ga_PJFPLJP2TM=GS2.1.s1758638195$o2$g1$t1758639838$j60$l0$h0; ul.session=1e183d41-acb5-4138-b1ef-a9c2dea41bbc; ul.pc.token=eyJhbGciOiJFUzI1NiJ9.eyJtIjo0MjYzMDk5MjMsInIiOlsiMTI3XzM0ODM4NSJdLCJlIjoiX1omWzleXS5PYEU1OjdoPTUvX0xCJz5uSVBxIVVTMlwibDUsTCQ2VTVKPDpscV86UWU_SzQyNnJRXFw4b2RAJT0mQGk2MFQlbkhGJCtjXFw0SWsiLCJzcmMiOiJzcmMiLCJzdWIiOiJRaHJZakduZFJpR2lOVnFlbWFOVEl3IiwianRpIjoiU1NKbkNGUFNTMnlsblJwWHhoWHcwUSIsImlhdCI6MTc1ODY0MDAyMCwiZXhwIjoxNzY2NDE2MDIwLCJ3bHQiOiJmMWE1OTA1Zi05NjIwLTQ1ZTUtOWQ5MS1kMjUxYzA3ZTBiNDIiLCJzIjoyfQ.DoxMcG1Csr84tG50Cxi2eJKhmGe9MuweAN4pkkCrkEVs_vX9tmgkNsk7C3SFBu-0Nym3eY3uyC6LUMQeJKnrQA; ai_session=z1NEYb5xqzyS62ne4o8FE4|1758639844169|1758640020432; agoda.attr.03=ATItems=1922878$05-19-2025 03:29$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1739459$06-12-2025 15:16$|1922878$06-12-2025 16:34$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1942345$06-14-2025 01:02$|1922878$06-17-2025 03:37$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1942345$06-17-2025 21:00$|1922878$06-18-2025 17:53$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1942345$06-22-2025 07:34$|1922878$07-19-2025 20:43$9c6545bc-1666-4ddf-9cd0-c70620f2972d|1942345$08-16-2025 14:45$|1922878$09-23-2025 22:17$9c6545bc-1666-4ddf-9cd0-c70620f2972d; agoda.landings=1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d||zvjhvanoeqkyblb4hui4ikfv|2025-09-23T15:21:14|True|19----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjwisnGBhAXEiwA0zEOR-JF3erwpbq5OT3LfO5TUzM8SNc1vps2NPXuWJBXLLgTauCiDFX4bxoCi6wQAvD_BwE|zvjhvanoeqkyblb4hui4ikfv|2025-09-23T22:17:43|True|20----1922878|9c6545bc-1666-4ddf-9cd0-c70620f2972d|CjwKCAjwisnGBhAXEiwA0zEOR-JF3erwpbq5OT3LfO5TUzM8SNc1vps2NPXuWJBXLLgTauCiDFX4bxoCi6wQAvD_BwE|zvjhvanoeqkyblb4hui4ikfv|2025-09-23T22:17:43|True|99; agoda.lastclicks=1922878||9c6545bc-1666-4ddf-9cd0-c70620f2972d||2025-09-23T22:17:43||zvjhvanoeqkyblb4hui4ikfv||{"IsPaid":true,"gclid":"CjwKCAjwisnGBhAXEiwA0zEOR-JF3erwpbq5OT3LfO5TUzM8SNc1vps2NPXuWJBXLLgTauCiDFX4bxoCi6wQAvD_BwE","Type":""}; utag_main=v_id:019975a9a38b0013c2ea24b474080506f01ac06700bd0$_sn:2$_se:1$_ss:1$_st:1758642475982$ses_id:1758640675982%3Bexp-session$_pn:1%3Bexp-session; agoda.consent=NG||2025-09-23 15:17:58Z; _gcl_aw=GCL.1758640688.CjwKCAjwisnGBhAXEiwA0zEOR-JF3erwpbq5OT3LfO5TUzM8SNc1vps2NPXuWJBXLLgTauCiDFX4bxoCi6wQAvD_BwE; _ga_T408Z268D2=GS2.1.s1758640687$o2$g1$t1758640688$j59$l0$h712201921; _ga=GA1.2.2076035954.1758615677; _gac_UA-6446424-30=1.1758640691.CjwKCAjwisnGBhAXEiwA0zEOR-JF3erwpbq5OT3LfO5TUzM8SNc1vps2NPXuWJBXLLgTauCiDFX4bxoCi6wQAvD_BwE; cto_bundle=jmD9nF92c3FMTmtmSUM1OEI1bm9GWmhEUEpOTk12ZjhqVzFPNUgyamxYSVVRalVGSzZ4RVAlMkJHME5FdU56U2xka3Bsb0d2SE5NZDFoanpoZGNiTEQxZThNNXpwcDNKQUlwbTJSenhraWdpM2hCUGdaMlhoN0tDbmtkeSUyRnVOakhIUUJoU1FvWCUyRmpLY2VLRDElMkJrOTNDYkVWMnk3dyUzRCUzRA; _gat_t3=1; _uetsid=487eb3b0985611f0bbaedb0a28b85283|byu7d6|2|fzk|0|2092; _uetvid=1255d1a0310411f0b9e191098651552a|1phh2ft|1758619027096|1|1|bat.bing.com/p/conversions/c/a; _gcl_au=1.2.1876300320.1758615677; _ga_C07L4VP9DZ=GS2.2.s1758640722$o2$g0$t1758640722$j60$l0$h0; __gads=ID=c7e1c6cbd973c570:T=1747255396:RT=1758640726:S=ALNI_MaTQseKMeSHylaabZ7b82b2EZqZ7A; __gpi=UID=000010ad0aa2a9b2:T=1747255396:RT=1758640726:S=ALNI_MZmY8tVwapTHo69em5pySZsktpkTg; t_rc=t=88&8ETD+1m6XA3NQYm7DVQvpQ=2; agoda.analytics=Id=252385164726278423&Signature=8508749872945367235&Expiry=1758644335382',
            'Accept' => 'application/json',
        ])->timeout(10) // Prevent long hangs
          ->retry(2, 200) // Retry on transient errors
          ->withOptions([
              'verify' => false, // use with caution, only for dev
              'curl' => [
                  CURLOPT_FOLLOWLOCATION => true,
              ],
          ])
          ->get('https://partners.agoda.com/HotelSuggest/GetSuggestions', [
              'type' => 1,
              'limit' => 10,
              'term' => $location,
          ]);

              

            Log::info('Agoda city suggestions response', [
                'status' => $suggestionResponse->status(),
                'body' => $suggestionResponse->body()
            ]);

            if (!$suggestionResponse->successful()) {
                Log::error('Failed to get city suggestions from Agoda', [
                    'status' => $suggestionResponse->status(),
                    'response' => $suggestionResponse->body()
                ]);
                return null;
            }

            $suggestions = $suggestionResponse->json();
             // dd($suggestionResponse);

            if (empty($suggestions) || !is_array($suggestions)) {
                Log::warning('No city suggestions found for location', [
                    'location' => $location,
                    'response' => $suggestions
                ]);
                return null;
            }

            $cityId = $suggestions[0]['Value'] ?? null;
          // dd($cityId);
            if (!$cityId) {
                Log::warning('City ID not found for location', [
                    'location' => $location,
                    'suggestions' => $suggestions
                ]);
                return null;
            }

            Log::info('Found city ID', ['cityId' => $cityId]);

            // Set default dates if not provided
            $checkInDate = $checkInDate ?? date('Y-m-d', strtotime('+7 days'));
            $checkOutDate = $checkOutDate ?? date('Y-m-d', strtotime('+12 days'));

            
            Log::info('Using budget range', [
                'min' => $min,
                'max' => $max
            ]);

            $hotelResponse = Http::timeout(30)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept-Encoding' => 'gzip,deflate',
                    'Authorization' => $this->agodaPartnerId . ':' . $this->agodaApiKey,
                ])->post('http://affiliateapi7643.agoda.com/affiliateservice/lt_v1', [
                    'criteria' => [
                        'additional' => [
                            'currency' => 'USD',
                            'dailyRate' => [
                                'maximum' => $max,
                                'minimum' => $min
                            ],
                            'discountOnly' => false,
                            'language' => 'en-us',
                            'maxResult' => 20,
                            'minimumReviewScore' => 0,
                            'minimumStarRating' => 0,
                            'occupancy' => [
                                'numberOfAdult' => 2,
                                'numberOfChildren' => 1
                            ],
                            'sortBy' => 'PriceAsc'
                        ],
                        'checkInDate' => $checkInDate,
                        'checkOutDate' => $checkOutDate,
                        'cityId' => $cityId
                    ]
                ]);

           


            Log::info('Agoda hotels response', [
                'status' => $hotelResponse->status(),
                'body' => $hotelResponse->body()
            ]);

            if (!$hotelResponse->successful()) {
                Log::error('Failed to get hotels from Agoda', [
                    'status' => $hotelResponse->status(),
                    'response' => $hotelResponse->body()
                ]);

                
                return null;
            }

            $responseData = $hotelResponse->json();



           
        
            
            if (empty($responseData) || !isset($responseData['results'])) {
                Log::warning('No hotels found in Agoda response', [
                    'response' => $responseData
                ]);
                return null;
            }

            Log::info('Successfully retrieved hotels from Agoda', [
                'count' => count($responseData['results']),
                'sample' => array_slice($responseData['results'], 0, 2)
            ]);

            return $responseData;
        } catch (\Exception $e) {
            Log::error('Error in getAgodaHotels', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'location' => $location
            ]);
            return null;
        }
    }

    private function formatAgodaHotels($agodaHotels)
    {
        if ($agodaHotels === null) {
            Log::warning('Agoda hotels data is null');
            return [];
        }

        if (!is_array($agodaHotels) || !isset($agodaHotels['results'])) {
            Log::warning('Invalid Agoda hotels data format', [
                'data' => $agodaHotels
            ]);
            return [];
        }

        if (empty($agodaHotels['results'])) {
            Log::warning('No hotels found in Agoda response');
            return [];
        }

        $formattedHotels = [];
        $defaultImageUrl = 'https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900';
        
        foreach ($agodaHotels['results'] as $hotel) {
            try {
                $imageUrl = $hotel['imageURL'] ?? null;
                if (!$imageUrl) {
                    // Use default image instead of making API call
                    $imageUrl = $defaultImageUrl;
                }

                $formattedHotels[] = [
                    'name' => $hotel['hotelName'] ?? 'Unknown Hotel',
                    'description' => 'Hotel in ' . $hotel['hotelName'],
                    'address' => 'Address not available',
                    'rating' => $hotel['starRating'] ?? 0,
                    'price' => $hotel['dailyRate'] ?? 0,
                    'currency' => $hotel['currency'] ?? 'USD',
                    'image_url' => $imageUrl,
                    'amenities' => [
                        'free_wifi' => $hotel['freeWifi'] ?? false,
                        'breakfast_included' => $hotel['includeBreakfast'] ?? false
                    ],
                    'location' => [
                        'latitude' => $hotel['latitude'] ?? 0,
                        'longitude' => $hotel['longitude'] ?? 0
                    ],
                    'review_score' => $hotel['reviewScore'] ?? 0,
                    'review_count' => $hotel['reviewCount'] ?? 0,
                    'booking_url' => $hotel['landingURL'] ?? null
                ];
            } catch (\Exception $e) {
                Log::error('Error formatting hotel data', [
                    'error' => $e->getMessage(),
                    'hotel' => $hotel
                ]);
                continue;
            }
        }

        return $formattedHotels;
    }
} 