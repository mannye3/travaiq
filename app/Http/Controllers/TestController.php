<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\HotelRecommendationService;

class TestController extends Controller
{
    private $hotelRecommendationService;

    public function __construct(HotelRecommendationService $hotelRecommendationService)
    {
        $this->hotelRecommendationService = $hotelRecommendationService;
    }

    public function search(Request $request)
    {
        $location = $request->input('location', 'Paris');
        $checkInDate = $request->input('check_in', date('Y-m-d', strtotime('+7 days')));
        $checkOutDate = $request->input('check_out', date('Y-m-d', strtotime('+12 days')));
        $budget = $request->input('budget', 'medium');

        try {
            $hotels = $this->hotelRecommendationService->getHotelRecommendations(
                $location,
                $checkInDate,
                $checkOutDate,
                $budget
            );

            return response()->json([
                'success' => true,
                'location' => $location,
                'check_in' => $checkInDate,
                'check_out' => $checkOutDate,
                'budget' => $budget,
                'hotels' => $hotels,
                'count' => count($hotels)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ], 500);
        }
    }
}