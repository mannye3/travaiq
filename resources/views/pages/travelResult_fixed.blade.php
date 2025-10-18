@extends('layouts.app')

@section('title', 'Travel Plan Results - Travaiq')

@section('content')
<style>
    .custom-purple-50 { background: #f3f0ff; }
    .custom-purple-100 { background: #e0d7fa; }
    .custom-purple-200 { border-color: #d0c4f5; }
    .custom-purple-400 { color: #9575cd; }
    .custom-purple-500 { color: #9575cd; background: #9575cd; }
    .custom-purple-600 { background: #7e5bc4; }
    .custom-purple-700 { background: #6d4cae; color: #9575cd; border-color: #9575cd; }
    .custom-purple-800 { color: #5a3d94; }
    .hover\:custom-purple-700:hover { background: #7e5bc4; }
    .hover\:custom-purple-600:hover { color: #9575cd; }
    .from-custom-purple-50 { background: linear-gradient(to right, #f3f0ff, #e0d7fa); }
    .from-custom-purple-600 { background: linear-gradient(to bottom right, #7e5bc4, #6d4cae); }
    .to-custom-purple-100 { background: linear-gradient(to right, #f3f0ff, #e0d7fa); }
    .to-custom-purple-700 { background: linear-gradient(to bottom right, #7e5bc4, #6d4cae); }
    .border-custom-purple-200 { border-color: #d0c4f5; }
    .border-custom-purple-600 { border-color: #9575cd; }
    .text-custom-purple-400 { color: #9575cd; }
    .text-custom-purple-500 { color: #9575cd; }
    .text-custom-purple-600 { color: #9575cd; }
    .text-custom-purple-700 { color: #6d4cae; }
    .text-custom-purple-800 { color: #5a3d94; }
    .bg-custom-purple-50 { background: #f3f0ff; }
    .bg-custom-purple-100 { background: #e0d7fa; }
    .bg-custom-purple-600 { background: #9575cd; }
    .hover\:bg-custom-purple-700:hover { background: #7e5bc4; }
    .hover\:text-custom-purple-800:hover { color: #5a3d94; }
</style>

    <!-- Main Content -->
    <main class="flex-1 min-w-0 overflow-x-hidden">
        <div class="max-w-screen-xl mx-auto px-4">
            <!-- Hero Section -->
            <section class="mb-8 lg:mb-16 pb-6 pt-6" id="hero">
                <!-- Hero Image -->
                <div class="relative h-[300px] md:h-[400px] w-full rounded-xl overflow-hidden mb-6">
                    @if ($tripDetails && $tripDetails->google_place_image)
                        <img src="{{ $tripDetails->google_place_image }}" alt="{{ $tripDetails->location }}"
                            class="w-full h-full object-cover">
                    @else
                        <img src="https://img.freepik.com/premium-photo/road-amidst-field-against-sky-sunset_1048944-19856354.jpg?w=1060"
                            alt="{{ $tripDetails->location ?? 'Travel destination' }}" class="w-full h-full object-cover">
                    @endif
                    <div
                        class="absolute bottom-0 left-0 right-0 p-4 md:p-6 bg-gradient-to-t from-black/70 to-transparent text-white">
                        <h1 class="text-2xl md:text-4xl font-bold mb-2">{{ $tripDetails->duration ?? '' }} days trip in
                            {{ $tripDetails->location ?? '' }}</h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 text-sm md:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <strong class="flex flex-wrap gap-2">
                                Trip Overview:
                                <span>Budget: {{ $budget }}</span>
                                <span>Travelers: {{ $traveler }}</span>
                                <span>Selected Activities: {{ $activities }}</span>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex flex-wrap gap-4 mb-6">
                    <button onclick="saveTrip()" class="flex items-center px-6 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save This Trip
                    </button>
                    <button onclick="shareTrip()" class="flex items-center px-6 py-3 bg-custom-purple-600 text-white rounded-lg hover:bg-custom-purple-700 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"></path>
                        </svg>
                        Share Trip
                    </button>
                    <a href="{{ route('createPlan') }}?destination={{ $tripDetails->location ?? '' }}&customize=true" class="flex items-center px-6 py-3 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors font-medium">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Customize Trip
                    </a>
                </div>

                <!-- Navigation Tabs -->
                <div class="flex gap-4 mb-6 border-b overflow-x-auto">
                    <button onclick="switchTab('overview')" id="overview-tab"
                        class="px-4 py-2 font-semibold text-custom-purple-600 border-b-2 border-custom-purple-600 whitespace-nowrap">Overview</button>
                    <button onclick="switchTab('general')" id="general-tab"
                        class="px-4 py-2 text-gray-600 hover:text-custom-purple-600 whitespace-nowrap">General Information</button>
                </div>

                <!-- Overview Content -->
                <div id="overview-content" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4">Description</h2>
                            <p class="text-gray-700 leading-relaxed">
                                {{ $locationOverview->history_and_culture }}
                            </p>
                        </div>

                        <div class="mb-8">
                            <h2 class="text-2xl font-bold mb-4">History</h2>
                            <p class="text-gray-700 leading-relaxed">
                                {{ $locationOverview->geographic_features_and_climate }}
                            </p>
                        </div>

                        <!-- Enhanced Hotel Recommendations Section -->
                        <div class="mb-12">
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold">Hotel Recommendations</h2>
                                    <p class="text-gray-600 mt-1">Find the perfect place to stay during your trip</p>
                                </div>
                                <div class="flex gap-2">
                                    <button onclick="scrollHotels('prev')" id="scroll-left"
                                        class="p-2 rounded-full bg-white shadow-md hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button onclick="scrollHotels('next')" id="scroll-right"
                                        class="p-2 rounded-full bg-white shadow-md hover:bg-gray-50 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <div class="relative">
                                <div id="hotels-container" class="overflow-hidden">
                                    <div id="hotels-slider" class="flex transition-transform duration-300 ease-in-out">
                                        @foreach($hotels as $hotel)
                                        <div class="w-full md:w-1/3 flex-shrink-0 px-3">
                                            <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow duration-300 h-full flex flex-col">
                                                <div class="relative">
                                                    @if (isset($hotel->image_url) && !empty($hotel->image_url))
                                                        <img src="{{ $hotel->image_url }}" alt="{{ $hotel->name }}" 
                                                            class="w-full h-48 object-cover"
                                                            onerror="this.onerror=null; this.src='https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900';">
                                                    @else
                                                        <img src="https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900" 
                                                            class="w-full h-48 object-cover" 
                                                            alt="{{ $hotel->name }}">
                                                    @endif
                                                    @if(isset($hotel->rating) && $hotel->rating > 0)
                                                        <div class="absolute top-2 right-2 bg-yellow-400 text-black px-2 py-1 rounded-full text-sm font-semibold">
                                                            {{ number_format((float)$hotel->rating, 1) }} ★
                                                        </div>
                                                    @endif
                                                    <div class="absolute top-2 left-2 bg-red-100 text-red-600 px-2 py-1 rounded text-xs font-medium">
                                                        Save 15%
                                                    </div>
                                                </div>
                                                
                                                <div class="p-4 flex flex-col flex-1">
                                                    <div>
                                                        <h3 class="font-semibold text-lg mb-2 line-clamp-1">{{ $hotel->name }}</h3>
                                                        @if(isset($hotel->description))
                                                            <p class="text-gray-600 text-sm mb-3 line-clamp-2">{{ $hotel->description }}</p>
                                                        @endif
                                                        
                                                        <div class="flex items-center gap-1 mb-3">
                                                            @if(isset($hotel->rating) && $hotel->rating > 0)
                                                                <div class="flex text-yellow-400">
                                                                    @php
                                                                        $rating = floatval($hotel->rating);
                                                                        $fullStars = floor($rating);
                                                                        $hasHalfStar = $rating - $fullStars >= 0.5;
                                                                        $emptyStars = 5 - $fullStars - ($hasHalfStar ? 1 : 0);
                                                                    @endphp
                                                                    @for ($i = 0; $i < $fullStars; $i++)
                                                                        <span>★</span>
                                                                    @endfor
                                                                    @if ($hasHalfStar)
                                                                        <span>½</span>
                                                                    @endif
                                                                    @for ($i = 0; $i < $emptyStars; $i++)
                                                                        <span class="text-gray-300">★</span>
                                                                    @endfor
                                                                </div>
                                                                <span class="text-gray-600 text-sm">{{ number_format((float)$hotel->rating, 1) }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="mt-auto">
                                                        <div class="flex justify-between items-center mb-3">
                                                            <div>
                                                                <p class="text-xl font-bold text-green-600">
                                                                    {{ $hotel->currency }} {{ number_format((float)$hotel->price, 2) }}
                                                                    <span class="text-sm text-gray-500">/night</span>
                                                                </p>
                                                                @if(isset($hotel->review_count) && $hotel->review_count > 0)
                                                                    <p class="text-sm text-gray-600">{{ $hotel->review_count }} reviews</p>
                                                                @endif
                                                            </div>
                                                        </div>

                                                        <!-- Enhanced booking buttons -->
                                                        <div class="space-y-2">
                                                            <a href="{{ $hotel->booking_url ?? '#' }}" target="_blank" 
                                                               class="block w-full bg-custom-purple-600 text-white text-center py-2 rounded-lg hover:bg-custom-purple-700 transition-colors font-medium">
                                                                Book Now - Save 15%
                                                            </a>
                                                            <div class="flex gap-2">
                                                               
                                                            </div>
                                                        </div>

                                                        @if(!empty($hotel->amenities))
                                                            <div class="mt-3 flex flex-wrap gap-2">
                                                                @if($hotel->amenities['free_wifi'] ?? false)
                                                                    <span class="bg-custom-purple-100 text-custom-purple-800 text-xs px-2 py-1 rounded">Free WiFi</span>
                                                                @endif
                                                                @if($hotel->amenities['breakfast_included'] ?? false)
                                                                    <span class="bg-custom-purple-100 text-custom-purple-800 text-xs px-2 py-1 rounded">Breakfast Included</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 text-center">
                            <p class="text-gray-600">Can't find what you're searching for? Try <a
                                    href="https://www.agoda.com/partners/partnersearch.aspx?pcs=1&cid=1942345&city={{ $cityId ?? '20711' }}" target="_blank"
                                    class="text-custom-purple-600 hover:underline">Agoda.com</a></p>
                        </div>

                    <!-- Flight Booking Widget - Add after action buttons -->
                    <div class="bg-gradient-to-r from-custom-purple-50 to-custom-purple-100 rounded-xl p-6 mb-6 border border-green-200">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Complete Your Trip</h3>
                                <p class="text-gray-600">Find flights to {{ $tripDetails->location }}</p>
                            </div>
                            <svg class="w-8 h-8 text-custom-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </div>
                        
                        <div class="flex flex-wrap gap-4">
                            <a href="https://www.skyscanner.com/transport/flights-from/{{ $userLocation ?? 'us' }}/{{ strtolower($tripDetails->location) }}/?adults=1&adultsv2=1&cabinclass=economy&children=0&childrenv2=&inboundaltsenabled=false&infants=0&outboundaltsenabled=false&preferdirects=false&ref=home&rtn=1" 
                               target="_blank" 
                               class="flex-1 bg-custom-purple-600 hover:bg-custom-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-colors text-center">
                                Find Flights from $299
                            </a>
                            <button class="flex-1 border border-gray-300 text-gray-700 hover:bg-gray-50 px-6 py-3 rounded-lg font-medium transition-colors text-center">
                                Get Flight Alerts
                            </button>
                        </div>
                        
                        <div class="flex items-center justify-center mt-3 text-sm text-gray-500">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Best prices guaranteed • No booking fees
                        </div>
                    </div>
                   
                    <!-- Itinerary Section -->
                     
                    <div class="mt-12">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold">Itinerary</h2>
                            <a target="_blank" href="{{ route('download.itinerary', ['tripId' => $tripId]) }}" 
                               class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download PDF
                            </a>
                        </div>
                       
                        <!-- Day 1 -->
                        @foreach ($itineraries as $itinerary)
                            <div class="bg-white rounded-lg shadow-sm mb-4 overflow-hidden">
                                <div class="flex items-center justify-between p-4 bg-white border-b cursor-pointer"
                                    onclick="toggleDay('day{{ $itinerary->day }}')">
                                    <div class="flex items-center gap-4">
                                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                                            <span class="text-blue-600 font-semibold">{{ $itinerary->day }}</span>
                                        </div>
                                        <div>
                                            <h3 class="font-semibold text-gray-900">Day {{ $itinerary->day }}</h3>
                                            <p class="text-sm text-gray-500">
                                                {{ \Carbon\Carbon::parse($locationOverview->start_date)->addDays($itinerary->day - 1)->format('D, d M') }}
                                            </p>
                                        </div>
                                    </div>
                                    <svg xmlns="http://www.w3.org/2000/svg"
                                        class="h-5 w-5 text-gray-400 transform transition-transform duration-200"
                                        id="arrow-day{{ $itinerary->day }}" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M19 9l-7 7-7-7" />
                                    </svg>
                                </div>
                                        
                                <div id="day{{ $itinerary->day }}" class="p-4" style="display: none;">
                                    @foreach ($itinerary->activities as $activity)
                                        <div class="mb-6 last:mb-0">
                                            <div class="flex items-start gap-4">
                                                <div class="flex-shrink-0">
                                                    <div
                                                        class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm">
                                                        {{ $loop->iteration }}
                                                    </div>
                                                </div>
                                                <div class="flex-1">
                                                    <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($activity->name . ', ' . $activity->address) }}"
                                                        target="_blank" class="block">
                                                        <div class="bg-gray-50 rounded-lg p-4">
                                                            <div class="flex flex-col md:flex-row gap-4">
                                                                <div class="flex-1">
                                                                    <h3 class="font-semibold text-gray-900 mb-2">
                                                                        {{ $activity->name }}</h3>
                                                                    <p class="text-gray-600 text-sm mb-3">
                                                                        {{ $activity->description }}</p>
                                                                    <div
                                                                        class="flex items-center gap-2 text-sm text-gray-500">
                                                                        🕛
                                                                        <span>{{ $activity->best_time }}</span>
                                                                    </div>
                                                                    <div
                                                                        class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                                                        
                                                                        <span>📍 {{ $activity->address }}</span>
                                                                    </div>
                                                                    
                                                                    <!-- Phone Number -->
                                                                    @if($activity->phone_number)
                                                                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                                                        
                                                                        <span>📞 {{ $activity->phone_number }}</span>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    <!-- Website -->
                                                                    @if($activity->website)
                                                                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                                                        
                                                                    <a href="{{ $activity->website }}" target="_blank" class="text-blue-600 hover:underline">🌐 Visit Website</a>
                                                                    </div>
                                                                    @endif
                                                                    
                                                                    <!-- Fee -->
                                                                    @if($activity->fee)
                                                                    <div class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                                                       
                                                                        <span>💰 Additional Fee: {{ $activity->fee }}</span>
                                                                    </div>
                                                                    @endif
                                                                </div>
                                                                <div
                                                                    class="w-full md:w-32 h-24 rounded-lg overflow-hidden">
                                                                    @if ($activity->image_url)
                                                                        <img src="{{ $activity->image_url }}"
                                                                            alt="{{ $activity->name }}"
                                                                            class="w-full h-full object-cover">
                                                                    @else
                                                                        <img src="https://img.freepik.com/premium-photo/high-angle-view-smart-phone-table_1048944-29197645.jpg?w=900"
                                                                        alt="{{ $activity->name }}"
                                                                            class="w-full h-full object-cover">
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <script>
                        function switchTab(tab) {
                            // Hide all content
                            document.getElementById('overview-content').classList.add('hidden');
                            document.getElementById('general-content').classList.add('hidden');

                            // Show selected content
                            document.getElementById(tab + '-content').classList.remove('hidden');

                            // Update tab styles
                            document.getElementById('overview-tab').classList.remove('text-custom-purple-600', 'border-custom-purple-600');
                            document.getElementById('general-tab').classList.remove('text-custom-purple-600', 'border-custom-purple-600');
                            document.getElementById('overview-tab').classList.add('text-gray-600');
                            document.getElementById('general-tab').classList.add('text-gray-600');

                            document.getElementById(tab + '-tab').classList.remove('text-gray-600');
                            document.getElementById(tab + '-tab').classList.add('text-custom-purple-600', 'border-custom-purple-600');
                        }

                        function toggleDay(dayId) {
                            const content = document.getElementById(dayId);
                            const arrow = document.getElementById('arrow-' + dayId);

                            if (content.style.display === 'none' || !content.style.display) {
                                content.style.display = 'block';
                                arrow.style.transform = 'rotate(180deg)';
                            } else {
                                content.style.display = 'none';
                                arrow.style.transform = 'rotate(0deg)';
                            }
                        }

                        // Show the first day by default when page loads
                        document.addEventListener('DOMContentLoaded', function() {
                            // Find all day elements
                            const dayElements = document.querySelectorAll('[id^="day"]');
                            if (dayElements.length > 0) {
                                // Show the first day
                                const firstDayId = dayElements[0].id;
                                toggleDay(firstDayId);
                            }
                        });

                        // Hotel slider functionality
                        let currentPosition = 0;
                        const slider = document.getElementById('hotels-slider');
                        const container = document.getElementById('hotels-container');
                        const cardWidth = 33.33;

                        const totalCards = {{ isset($hotels) ? count($hotels) : 0 }};
                        const maxPosition = -(totalCards - 3) * cardWidth;

                        function updateScrollButtons() {
                            const prevButton = document.getElementById('scroll-left');
                            const nextButton = document.getElementById('scroll-right');
                            
                            prevButton.disabled = currentPosition >= 0;
                            nextButton.disabled = currentPosition <= maxPosition;
                        }

                        function scrollHotels(direction) {
                            if (direction === 'next' && currentPosition > maxPosition) {
                                currentPosition -= cardWidth;
                            } else if (direction === 'prev' && currentPosition < 0) {
                                currentPosition += cardWidth;
                            }
                            
                            slider.style.transform = `translateX(${currentPosition}%)`;
                            updateScrollButtons();
                        }

                        // Action functions
                        function saveTrip() {
                            // Implement trip saving functionality
                            alert('Trip saved successfully!');
                        }

                        function downloadPDF() {
                            // Implement PDF download functionality
                            alert('PDF download feature coming soon!');
                        }

                        function shareTrip() {
                            if (navigator.share) {
                                navigator.share({
                                    title: '{{ $tripDetails->duration }} days trip in {{ $tripDetails->location }}',
                                    url: window.location.href
                                });
                            } else {
                                // Fallback - copy to clipboard
                                navigator.clipboard.writeText(window.location.href);
                                alert('Trip URL copied to clipboard!');
                            }
                        }

                        function getMobileApp() {
                            // Redirect to app store or show app download modal
                            alert('Mobile app coming soon!');
                        }

                        function bookEverything() {
                            // Implement bulk booking functionality
                            alert('Bulk booking feature coming soon!');
                        }

                        function bookCompletePackage() {
                            // Implement complete package booking
                            alert('Complete package booking coming soon!');
                        }

                        function saveHotel(hotelName) {
                            // Implement hotel saving functionality
                            alert(`Hotel "${hotelName}" saved to your wishlist!`);
                        }

                        function closeUpgrade() {
                            document.getElementById('upgrade-prompt').style.display = 'none';
                        }

                        function toggleMapView() {
                            // Implement map view toggle functionality
                            alert('Map view feature coming soon! This will show an interactive map with all locations.');
                        }

                        function saveActivity(activityName) {
                            // Implement activity saving functionality
                            alert(`Activity "${activityName}" saved to your wishlist!`);
                        }

                        // Show save trip prompt after 30 seconds
                        setTimeout(() => {
                            document.getElementById('upgrade-prompt').style.display = 'block';
                        }, 30000);

                        // Initialize scroll buttons
                        updateScrollButtons();
                    </script>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    
                    <!-- Enhanced Cost Summary -->
                    {{-- <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border border-gray-100">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-xl font-bold">Trip Investment</h3>
                            <div class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm font-medium">
                                {{ ucwords($tripDetails->budget) }} Budget
                            </div>
                        </div>
                        
                        <!-- Visual cost breakdown -->
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">🏨 Accommodation</span>
                                <span class="font-semibold">$50-150/night</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">🍽️ Dining</span>
                                <span class="font-semibold">$40-80/day</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-600">🚌 Transport</span>
                                <span class="font-semibold">$20-40/day</span>
                            </div>
                            <div class="border-t pt-2 flex justify-between items-center font-bold">
                                <span>Estimated Total</span>
                                <span class="text-green-600">$110-270/day</span>
                            </div>
                        </div>

                        <!-- Book everything CTA -->
                        <button onclick="bookCompletePackage()" class="w-full mt-4 bg-gradient-to-r from-custom-purple-600 to-custom-purple-700 text-white py-3 rounded-lg hover:shadow-lg transition-all font-medium">
                            Book Complete Package - Save 20%
                        </button>
                    </div> --}}
                
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border border-gray-100">
                        <h3 class="text-xl font-bold mb-4">Estimated Cost</h3>

                        <div class="mt-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-orange-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                <h4 class="font-semibold text-gray-800">Local Cuisine & Food</h4>
                            </div>
                            <div class="space-y-3 ml-8">
                                @if ($cost && $cost->diningCosts)
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">Dining Costs</h5>
                                        <ul class="list-disc list-inside text-gray-600">
                                            @foreach ($cost->diningCosts as $dining)
                                                <li>{{ $dining->category ?? '' }}: {{ $dining->cost_range ?? '' }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-gray-500">No food information available</p>
                                @endif
                            </div>
                        </div>

                        <!-- Transportation Section -->
                        <div class="mt-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-custom-purple-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                                <h4 class="font-semibold text-gray-800">Transportation</h4>
                            </div>
                            <div class="space-y-3 ml-8">
                                @if(isset($additionalInfo->transportation_options))
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">Available Options</h5>
                                        <p class="text-gray-600">{{ $additionalInfo->transportation_options }}</p>
                                    </div>
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">Transportation Costs</h5>
                                        <ul class="list-disc list-inside text-gray-600">
                                            @foreach($additionalInfo->transportation_costs ?? [] as $transport)
                                                <li>{{ $transport->type ?? '' }}: {{ $transport->cost ?? '' }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-gray-500">No transportation information available</p>
                                @endif
                            </div>
                        </div>

                        <!-- Book everything CTA -->
                        <button onclick="bookCompletePackage()" class="w-full mt-4 bg-gradient-to-r from-custom-purple-600 to-custom-purple-700 text-white py-3 rounded-lg hover:shadow-lg transition-all font-medium">
                            Book Complete Package - Save 20%
                        </button>
                    </div>

                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6 border border-gray-100">
                        <h3 class="text-xl font-bold mb-4">Security Advice</h3>

                        <!-- Security Advice Section -->
                        <div class="mt-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h4 class="font-semibold text-gray-800">Security Advice</h4>
                            </div>
                            <div class="space-y-3 ml-8">
                                <span class="text-gray-600">Safety Rating:
                                    <strong>{{ $securityAdvice->overall_safety_rating }}</strong> </span>
                                <br>
                                <span class="text-gray-600">Emergency Numbers:
                                    <strong>{{ $securityAdvice->emergency_numbers }}</strong> </span>
                                <br>
                                <br>

                                <span class="text-gray-600">Common scams:
                                    {{ $securityAdvice->common_scams }} </span>
                                <br>
                                <br>
                                <span class="text-gray-600">Health Precautions:
                                    {{ $securityAdvice->health_precautions }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                                <h4 class="font-semibold text-gray-800">Safety Tips:</h4>
                            </div>
                            <div class="space-y-3 ml-8">
                                <ul>
                                    @foreach ($securityAdvice->safety_tips as $tip)
                                        <li><span class="text-gray-600">{{ $tip }}</span></li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>

                        <!-- Emergency Facilities Section -->
                        <div class="mt-6">
                            <div class="flex items-center gap-3 mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 14l-7 7m0 0l-7-7m7 7V3" />
                                </svg>
                                <h4 class="font-semibold text-gray-800">Emergency Facilities</h4>
                            </div>
                            <div class="space-y-3 ml-8">
                                @foreach ($securityAdvice->emergencyFacilities as $facility)
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">{{ $facility->name ?? 'Emergency Facility' }}</h5>
                                        <ul class="list-disc list-inside text-gray-600">
                                            @if(isset($facility->address))
                                                <p class="text-sm text-gray-600 mb-1">
                                                    <span class="font-medium">Address:</span> {{ $facility->address }}
                                                </p>
                                            @endif
                                            @if(isset($facility->phone))
                                                <p class="text-sm text-gray-600">
                                                    <span class="font-medium">Phone:</span> 
                                                    <a href="tel:{{ $facility->phone }}" class="text-custom-purple-600 hover:text-custom-purple-800">
                                                        {{ $facility->phone }}
                                                    </a>
                                                </p>
                                            @endif
                                        </ul>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
        </div>

        <!-- General Information Content -->
        <div id="general-content" class="hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Currency -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-50 rounded-full flex items-center justify-center">
                            <span class="text-purple-600 font-semibold"><svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg></span>
                        </div>
                        <h3 class="font-medium">Currency</h3>
                    </div>
                    <p class="text-gray-600">{{ $additionalInfo->local_currency }}</p>
                </div>

                <!-- Exchange Rate -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-50 rounded-full flex items-center justify-center">
                            <span class="text-green-600">$</span>
                        </div>
                        <h3 class="font-medium">Exchange Rate</h3>
                    </div>
                    <p class="text-gray-600">{{ $additionalInfo->exchange_rate }}</p>
                </div>

                <!-- Time Zone -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-50 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="font-medium">Time Zone</h3>
                    </div>
                    <p class="text-gray-600">{{ $additionalInfo->timezone }}</p>
                </div>

                <!-- Weather -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-50 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </div>
                        <h3 class="font-medium">Weather</h3>
                    </div>
                    <p class="text-gray-600">{{ $additionalInfo->weather_forecast }}</p>
                </div>

                <!-- Transportation -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-purple-50 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </div>
                        <h3 class="font-medium">Transportation</h3>
                    </div>
                    <p class="text-gray-600">{{ $additionalInfo->transportation_options }}</p>
                </div>
            </div>
        </div>
        </div>

        <script>
            function switchTab(tab) {
                // Hide all content
                document.getElementById('overview-content').classList.add('hidden');
                document.getElementById('general-content').classList.add('hidden');

                // Show selected content
                document.getElementById(tab + '-content').classList.remove('hidden');

                // Update tab styles
                document.getElementById('overview-tab').classList.remove('text-custom-purple-600', 'border-custom-purple-600');
                document.getElementById('general-tab').classList.remove('text-custom-purple-600', 'border-custom-purple-600');
                document.getElementById('overview-tab').classList.add('text-gray-600');
                document.getElementById('general-tab').classList.add('text-gray-600');

                document.getElementById(tab + '-tab').classList.remove('text-gray-600');
                document.getElementById(tab + '-tab').classList.add('text-custom-purple-600', 'border-custom-purple-600');
            }
        </script>
        </section>


        </div>
    </main>

   

  
      <!-- Simple JavaScript for interactivity -->
   

     @endsection