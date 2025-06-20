@extends('layouts.app')

@section('title', 'Travel Plan Results Guest - Travaiq')

@section('content')


    <!-- Main Content -->
    <main class="flex-1 min-w-0 overflow-x-hidden">
        
        <div class="max-w-screen-xl mx-auto px-4">
        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
        <p class="font-bold">Temporary Travel Plan</p>
        <p>This is a temporary travel plan. To save your plan permanently, please
            <a href="{{ route('google.redirect') }}" class="text-yellow-700 underline">register</a> or
            <a href="{{ route('google.redirect') }}" class="text-yellow-700 underline">login</a>.
        </p>
    </div>
            <!-- Hero Section -->
            <section class="mb-8 lg:mb-16 pb-6 pt-6" id="hero">
                <!-- result  Section -->
                <div class="relative h-[300px] md:h-[400px] w-full rounded-xl overflow-hidden mb-6">
                @if ($tripDetails->google_place_image)
                        <img src="{{ $tripDetails->google_place_image }}" alt="{{ $tripDetails->location }}"
                            class="w-full h-full object-cover">
                    @else
                        <img src="https://img.freepik.com/premium-photo/road-amidst-field-against-sky-sunset_1048944-19856354.jpg?w=1060"
                            alt="{{ $tripDetails->location }}" class="w-full h-full object-cover">
                    @endif
                    <div class="absolute bottom-0 left-0 right-0 p-4 md:p-6 bg-gradient-to-t from-black/70 to-transparent text-white">
                        <h1 class="text-2xl md:text-4xl font-bold mb-2">{{ $tripDetails->duration }} days trip in
                            {{ $tripDetails->location }}</h1>
                        <div class="flex flex-col md:flex-row md:items-center gap-2 text-sm md:text-base">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 shrink-0" fill="none"
                                viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <strong class="flex flex-wrap gap-2">
                                Trip Overview:
                                <span>Budget: {{ $tripDetails->budget }}</span>
                                <span>Travelers: {{ $tripDetails->traveler }}</span>
                                <span>Selected Activities: {{ $tripDetails->activities }}</span>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Navigation Tabs -->
                <div class="flex gap-4 mb-6 border-b overflow-x-auto">
                    <button onclick="switchTab('overview')" id="overview-tab"
                        class="px-4 py-2 font-semibold text-blue-600 border-b-2 border-blue-600 whitespace-nowrap">Overview</button>
                    <button onclick="switchTab('general')" id="general-tab"
                        class="px-4 py-2 text-gray-600 hover:text-blue-600 whitespace-nowrap">General Information</button>
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

                        <div>
                            <div class="flex justify-between items-center mb-6">
                                <div>
                                    <h2 class="text-2xl font-bold">Hotel Recommendation</h2>
                                    {{-- <p class="text-gray-600"><span class="font-semibold">25 Hotels</span>
                                            found based on your interests</p> --}}
                                </div>

                                <div class="flex gap-2">
                                    <button onclick="scrollHotels('prev')" id="scroll-left"
                                        class="p-2 rounded-full bg-white shadow-md hover:bg-gray-50 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 19l-7-7 7-7" />
                                        </svg>
                                    </button>
                                    <button onclick="scrollHotels('next')" id="scroll-right"
                                        class="p-2 rounded-full bg-white shadow-md hover:bg-gray-50 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-600" fill="none"
                                            viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 5l7 7-7 7" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="relative">
                                <div id="hotels-container" class="overflow-hidden">
                                    <div id="hotels-slider" class="flex transition-transform duration-300 ease-in-out"
                                        style="transform: translateX(0px);">
                                        @foreach ($hotels as $hotel)
                                            <!-- Gasthof Pension Alpenblick -->
                                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($hotel->name . ', ' . $hotel->address) }}"
                                                target="_blank">
                                                <div class="w-full md:w-1/3 flex-shrink-0 px-3">
                                                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                                                        @if (isset($hotel->image_url) && !empty($hotel->image_url))
                                                            <img src="{{ $hotel->image_url }}" 
                                                                class="card-img-top" 
                                                                style="height: 180px; object-fit: cover;" 
                                                                alt="{{ $hotel->name }}"
                                                                onerror="this.onerror=null; this.src='https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900';">
                                                        @else
                                                            <img src="https://img.freepik.com/premium-photo/hotel-room_1048944-29197645.jpg?w=900" 
                                                                class="card-img-top" 
                                                                style="height: 180px; object-fit: cover;" 
                                                                alt="{{ $hotel->name }}">
                                                        @endif
                                                       
                                                        <div class="p-4">
                                                            <div class="mb-2">
                                                                <p class="font-semibold text-lg">
                                                                    {{ $hotel->name }}</p>
                                                                <h3 class="text-gray-600 text-sm">
                                                                    📍 {{ $hotel->address }}</h3>
                                                            </div>
                                                            <div class="flex items-center gap-1 mb-3">
                                                                <div class="flex text-yellow-400">
                                                                    @php
                                                                        $rating = floatval($hotel->rating);
                                                                        $fullStars = floor($rating);
                                                                        $hasHalfStar = $rating - $fullStars >= 0.5;
                                                                        $emptyStars =
                                                                            5 - $fullStars - ($hasHalfStar ? 1 : 0);
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
                                                                <span
                                                                    class="text-gray-600 text-sm">{{ $hotel->rating }}</span>
                                                            </div>
                                                            <div class="flex justify-between items-end">
                                                                <div>
                                                                    <p class="text-xl font-bold">
                                                                        {{ $hotel->price_per_night }}/night</p>
                                                                    {{-- <p class="text-gray-600 text-sm"></p> --}}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                            </a>
                                    </div>
                                    @endforeach


                                </div>
                            </div>
                        </div>
                        {{-- <div class="mt-6 text-center">
                                            <p class="text-gray-600">Can't find what you're searching for? Try <a
                                                    href="https://booking.com"
                                                    class="text-blue-600 hover:underline">Booking.com</a></p>
                                        </div> --}}
                    </div>

                    <!-- Itinerary Section -->
                    <div class="mt-12">
                        {{-- <div class="flex justify-between items-center mb-6">
                                            <h2 class="text-2xl font-bold">Itinerary</h2>
                                            <button
                                                class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors">
                                                Best Tour
                                            </button>
                                        </div> --}}
                                        <div class="bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4" role="alert">
                                <p class="font-bold">Temporary Travel Plan</p>
                                <p>This is a temporary travel plan. To save your plan permanently, please
                                    <a href="{{ route('google.redirect') }}" class="text-yellow-700 underline">register</a> or
                                    <a href="{{ route('google.redirect') }}" class="text-yellow-700 underline">login</a>.
                                </p>
                            </div>

                        <!-- Day 1 -->
                        @guest
                            <div class="relative">
                                <div class="" id="blurred-content">
                                    <div>
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
                                                            @if(isset($locationOverview->start_date))
                                                        {{ \Carbon\Carbon::parse($locationOverview->start_date)->addDays($itinerary->day - 1)->format('D, d M') }}
                                                         @endif
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
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            class="h-4 w-4" fill="none"
                                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                        </svg>
                                                                                        <span>{{ $activity->best_time }}</span>
                                                                                    </div>
                                                                                    <div
                                                                                        class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                                                                        <svg xmlns="http://www.w3.org/2000/svg"
                                                                                            class="h-4 w-4" fill="none"
                                                                                            viewBox="0 0 24 24" stroke="currentColor">
                                                                                            <path stroke-linecap="round"
                                                                                                stroke-linejoin="round" stroke-width="2"
                                                                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                                        </svg>
                                                                                        <span>📍 {{ $activity->address }}</span>
                                                                                    </div>
                                                                                </div>
                                                                                <div
                                                                                    class="w-full md:w-32 h-24 rounded-lg overflow-hidden">
                                                                                    @if (isset($activity->image_url) && $activity->image_url)
                                                                <img src="{{ $activity->image_url }}"   alt="{{ $activity->name }}"
                                                                            class="w-full h-full object-cover">
                                                            @else
                                                                <img src="https://img.freepik.com/premium-photo/high-angle-view-smart-phone-table_1048944-29197645.jpg?w=900"
                                                                    class="img-fluid rounded-start" style="height: 100%;  width=100%;  object-fit: cover;" alt="{{ $activity->name }}">
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
                                </div>
                                
                            </div>
                        @else
                            <div>
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
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="h-4 w-4" fill="none"
                                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                                                </svg>
                                                                                <span>{{ $activity->best_time }}</span>
                                                                            </div>
                                                                            <div
                                                                                class="flex items-center gap-2 text-sm text-gray-500 mt-2">
                                                                                <svg xmlns="http://www.w3.org/2000/svg"
                                                                                    class="h-4 w-4" fill="none"
                                                                                    viewBox="0 0 24 24" stroke="currentColor">
                                                                                    <path stroke-linecap="round"
                                                                                        stroke-linejoin="round" stroke-width="2"
                                                                                        d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                                </svg>
                                                                                <span>📍 {{ $activity->address }}</span>
                                                                            </div>
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
                                            </div>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>
                        @endguest
                    </div>

                    <script>
                        function toggleDay(dayId) {
                            const content = document.getElementById(dayId);
                            const arrow = document.getElementById('arrow-' + dayId);

                            if (content.style.display === 'none') {
                                content.style.display = 'block';
                                arrow.style.transform = 'rotate(180deg)';
                            } else {
                                content.style.display = 'none';
                                arrow.style.transform = 'rotate(0deg)';
                            }
                        }

                        // Show the first day by default when page loads
                        document.addEventListener('DOMContentLoaded', function() {
                            const firstDay = document.querySelector('[id^="day"]');
                            if (firstDay) {
                                const firstDayId = firstDay.id;
                                toggleDay(firstDayId);
                            }
                        });
                    </script>
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                
                    <div class="bg-gray-50 rounded-lg p-6 mt-6">
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
                            @if (isset($additionalInfo->dining_costs))
                                  
                                    <div class="mb-4">
                                        <h5 class="font-medium text-gray-700 mb-2">Dining Costs</h5>
                                        <ul class="list-disc list-inside text-gray-600">
                                            @foreach($additionalInfo->dining_costs ?? [] as $cost)
                                                <li>{{ $cost->category ?? '' }}: {{ $cost->cost_range ?? '' }}</li>
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-blue-500" fill="none"
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

                    

                        <!-- Activities Section -->
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
                           
                            <div class="space-y-3 ml-8">
                                @if(isset($securityAdvice->emergency_facilities) && count($securityAdvice->emergency_facilities) > 0)
                                    <div class="space-y-4">
                                        @foreach($securityAdvice->emergency_facilities as $facility)
                                            
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                                        <div class="flex">
                                            <div class="flex-shrink-0">
                                                <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <p class="text-sm text-yellow-700">
                                                    No emergency facilities information available for this location.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Food Section -->
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
                            @if(isset($securityAdvice->emergency_facilities) && count($securityAdvice->emergency_facilities) > 0)
                                  
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
                                                                <a href="tel:{{ $facility->phone }}" class="text-blue-600 hover:text-blue-800">
                                                                    {{ $facility->phone }}
                                                                </a>
                                                            </p>
                                                        @endif
                                        </ul>
                                    </div>
                                @else
                                    <p class="text-gray-500"> No emergency facilities information available for this location.</p>
                                @endif
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
                        <div class="w-8 h-8 bg-blue-50 rounded-full flex items-center justify-center">
                            <span class="text-blue-600 font-semibold"><svg xmlns="http://www.w3.org/2000/svg"
                                    class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24"
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
                        <div class="w-8 h-8 bg-green-50 rounded-full flex items-center justify-center">
                            <span class="text-green-600">$</span>
                        </div>
                        <h3 class="font-medium">Exchange Rate</h3>
                    </div>
                    <p class="text-gray-600">{{ $additionalInfo->exchange_rate }}</p>
                </div>

                <!-- Payment Method -->
                {{-- <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div
                                                class="w-8 h-8 bg-purple-50 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                                </svg>
                                            </div>
                                            <h3 class="font-medium">Payment Method</h3>
                                        </div>
                                        <p class="text-gray-600">Wise</p>
                                    </div> --}}

                <!-- Capital -->
                {{-- <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-8 h-8 bg-red-50 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                </svg>
                                            </div>
                                            <h3 class="font-medium">Capital</h3>
                                        </div>
                                        <p class="text-gray-600">Vienna</p>
                                    </div> --}}

                <!-- Time Zone -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-yellow-50 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-yellow-600" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <h3 class="font-medium">Time Zone</h3>
                    </div>
                    <p class="text-gray-600">{{ $additionalInfo->timezone }}</p>
                </div>

                <!-- Power Socket -->
                {{-- <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div
                                                class="w-8 h-8 bg-indigo-50 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M13 10V3L4 14h7v7l9-11h-7z" />
                                                </svg>
                                            </div>
                                            <h3 class="font-medium">Power Socket</h3>
                                        </div>
                                        <div class="flex gap-2">
                                            <img src="/socket-c.png" alt="Type C" class="w-6 h-6">
                                            <img src="/socket-f.png" alt="Type F" class="w-6 h-6">
                                        </div>
                                    </div> --}}

                <!-- Language -->
                {{-- <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                                        <div class="flex items-center gap-3 mb-2">
                                            <div class="w-8 h-8 bg-pink-50 rounded-full flex items-center justify-center">
                                                <svg class="w-4 h-4 text-pink-600" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M3 5h12M9 3v2m1.048 9.5A18.022 18.022 0 016.412 9m6.088 9h7M11 21l5-10 5 10M12.751 5C11.783 10.77 8.07 15.61 3 18.129" />
                                                </svg>
                                            </div>
                                            <h3 class="font-medium">Language</h3>
                                        </div>
                                        <p class="text-gray-600">German</p>
                                    </div> --}}

                <!-- Weather -->
                <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-2">
                        <div class="w-8 h-8 bg-orange-50 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor"
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
                        <div class="w-8 h-8 bg-teal-50 rounded-full flex items-center justify-center">
                            <svg class="w-4 h-4 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                document.getElementById('overview-tab').classList.remove('text-blue-600', 'border-blue-600');
                document.getElementById('general-tab').classList.remove('text-blue-600', 'border-blue-600');
                document.getElementById('overview-tab').classList.add('text-gray-600');
                document.getElementById('general-tab').classList.add('text-gray-600');

                document.getElementById(tab + '-tab').classList.remove('text-gray-600');
                document.getElementById(tab + '-tab').classList.add('text-blue-600', 'border-blue-600');
            }
        </script>
        </section>


        </div>
    </main>

   

  
      <!-- Simple JavaScript for interactivity -->
    <script>
        // Mobile menu toggle
        document.getElementById('menu-toggle').addEventListener('click', function() {
            const menu = document.getElementById('menu');
            menu.classList.toggle('hidden');
        });
        // FAQ toggles
        const faqToggles = document.querySelectorAll('.faq-toggle');
        faqToggles.forEach(toggle => {
            toggle.addEventListener('click', function() {
                const content = this.nextElementSibling;
                content.classList.toggle('hidden');

                // Toggle icon
                const icon = this.querySelector('.faq-icon');
                if (content.classList.contains('hidden')) {
                    icon.innerHTML = '<path d="m6 9 6 6 6-6"></path>';
                } else {
                    icon.innerHTML = '<path d="m18 15-6-6-6 6"></path>';
                }
            });
        });

        // User dropdown toggle
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        userMenuButton.addEventListener('click', function() {
            userDropdown.classList.toggle('hidden');
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', function(event) {
            if (!userMenuButton.contains(event.target) && !userDropdown.contains(event.target)) {
                userDropdown.classList.add('hidden');
            }
        });
    </script>



    <script>
        // Increment and Decrement for Travel Days
        function incrementDays() {
            const input = document.getElementById("daysInput");
            input.value = parseInt(input.value) + 1;
        }

        function decrementDays() {
            const input = document.getElementById("daysInput");
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
            }
        }

        // Budget selection
        function setBudget(budget) {
            document.querySelectorAll('.budget-option').forEach(option => {
                option.classList.remove('border-indigo-500', 'bg-gray-50');
                option.classList.add('border-gray-300');
            });

            const selectedOption = document.querySelector(`.budget-option[onclick*="'${budget}'"]`);
            if (selectedOption) {
                selectedOption.classList.remove('border-gray-300');
                selectedOption.classList.add('border-indigo-500', 'bg-gray-50');
                document.getElementById('budgetInput').value = budget;
            }
        }

        // Companion selection
        function setCompanion(companion) {
            document.querySelectorAll('.companion-option').forEach(option => {
                option.classList.remove('border-indigo-500', 'bg-gray-50');
                option.classList.add('border-gray-300');
            });

            const selectedOption = document.querySelector(`.companion-option[onclick*="'${companion}'"]`);
            if (selectedOption) {
                selectedOption.classList.remove('border-gray-300');
                selectedOption.classList.add('border-indigo-500', 'bg-gray-50');
                document.getElementById('companionInput').value = companion;
            }
        }

        // Activity selection (multiple)
        let selectedActivities = [];

        function toggleActivity(activity) {
            const option = document.querySelector(`.activity-option[onclick*="'${activity}'"]`);
            if (!option) return;

            const isSelected = option.classList.contains('border-indigo-500');

            if (isSelected) {
                option.classList.remove('border-indigo-500', 'bg-gray-50');
                option.classList.add('border-gray-300');
                selectedActivities = selectedActivities.filter(a => a !== activity);
            } else {
                option.classList.remove('border-gray-300');
                option.classList.add('border-indigo-500', 'bg-gray-50');
                selectedActivities.push(activity);
            }

            document.getElementById('activitiesInput').value = JSON.stringify(selectedActivities);
        }

        // Form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const location = document.querySelector('select[name="location"]').value;
            const travel = document.querySelector('input[name="travel"]').value;
            const duration = document.getElementById('daysInput').value;
            const budget = document.getElementById('budgetInput').value;
            const companion = document.getElementById('companionInput').value;
            const activities = selectedActivities.length;

            if (!location || !travel || !duration || !budget || !companion || !activities) {
                e.preventDefault();
                alert('Please fill in all required fields and select at least one activity');
            }
        });
    </script>

    <script>
        let currentPosition = 0;
        const slider = document.getElementById('hotels-slider');
        const container = document.getElementById('hotels-container');
        const hotelCards = document.querySelectorAll('#hotels-slider > div');
        let cardWidth = 0;
        let maxPosition = 0;

        function updateSliderDimensions() {
            // Calculate card width based on screen size
            if (window.innerWidth >= 768) {
                cardWidth = 300; // Desktop card width
            } else {
                cardWidth = container.offsetWidth; // Mobile card width (full width)
            }

            // Calculate max scroll position
            maxPosition = -(hotelCards.length * (cardWidth + 16) - container.offsetWidth);

            // Reset position if it's beyond the new max position
            if (currentPosition < maxPosition) {
                currentPosition = maxPosition;
            }

            // Update slider position
            slider.style.transform = `translateX(${currentPosition}px)`;

            // Update button states
            updateButtonStates();
        }

        function scrollHotels(direction) {
            if (direction === 'next') {
                currentPosition = Math.max(maxPosition, currentPosition - (cardWidth + 16));
            } else {
                currentPosition = Math.min(0, currentPosition + (cardWidth + 16));
            }

            slider.style.transform = `translateX(${currentPosition}px)`;
            updateButtonStates();
        }

        function updateButtonStates() {
            const scrollLeftBtn = document.getElementById('scroll-left');
            const scrollRightBtn = document.getElementById('scroll-right');

            scrollLeftBtn.disabled = currentPosition >= 0;
            scrollRightBtn.disabled = currentPosition <= maxPosition;
        }

        // Initialize slider
        document.addEventListener('DOMContentLoaded', function() {
            updateSliderDimensions();

            // Add touch event listeners for mobile swipe
            let touchStartX = 0;
            let touchEndX = 0;

            container.addEventListener('touchstart', (e) => {
                touchStartX = e.changedTouches[0].screenX;
            }, false);

            container.addEventListener('touchend', (e) => {
                touchEndX = e.changedTouches[0].screenX;
                handleSwipe();
            }, false);

            function handleSwipe() {
                const swipeDistance = touchEndX - touchStartX;
                if (Math.abs(swipeDistance) > 50) { // Minimum swipe distance
                    if (swipeDistance > 0) {
                        scrollHotels('prev');
                    } else {
                        scrollHotels('next');
                    }
                }
            }
        });

        // Update slider on window resize
        window.addEventListener('resize', updateSliderDimensions);
    </script>

     @endsection
</body>
</html>