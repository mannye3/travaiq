@extends('layouts.app')

@section('title', 'Create Travel Plan - Travaiq')

@section('content')

    <style>
        .suggestions-container {
            position: absolute;
            width: 100%;
            max-height: 200px;
            overflow-y: auto;
            background: white;
            border: 1px solid #ddd;
            border-radius: 4px;
            z-index: 1000;
        }

        .suggestion-item {
            padding: 8px 12px;
            cursor: pointer;
        }

        .suggestion-item:hover {
            background-color: #f8f9fa;
        }
    </style>


    <!-- Page Title Section -->
    <section class="bg-gradient-to-r from-primary-dark via-primary to-primary-light text-white py-16 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-full h-full">
            <svg class="absolute right-0 top-0 h-full opacity-10" viewBox="0 0 500 500" width="500" height="500" preserveAspectRatio="xMinYMin meet">
                <path d="M488.6,259.8c0,98.5-80.1,178.3-178.9,178.3s-178.9-79.8-178.9-178.3c0-98.5,80.1-178.3,178.9-178.3S488.6,161.3,488.6,259.8z" fill="white" />
                <circle cx="168" cy="213.8" r="14.8" fill="white" />
                <circle cx="126.3" cy="311.7" r="24.2" fill="white" />
                <circle cx="244.8" cy="348.7" r="10.2" fill="white" />
                <circle cx="291.5" cy="267.1" r="14.2" fill="white" />
                <circle cx="380.4" cy="173.6" r="22.6" fill="white" />
            </svg>
        </div>
        <div class="container mx-auto px-4 text-center relative">
            <h1 class="text-4xl md:text-5xl font-bold mb-4 animate__animated animate__fadeIn">Plan Your Perfect Trip</h1>
            <p class="text-xl mb-3 animate__animated animate__fadeIn animate__delay-1s">Tell us your preferences and let AI do the rest</p>
            <p class="text-lg opacity-90 animate__animated animate__fadeIn animate__delay-2s">Your personalized travel itinerary is just a few clicks away</p>
            <div class="mt-8 flex justify-center gap-4">
                <div class="animate-float delay-100 bg-white bg-opacity-20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="animate-float delay-300 bg-white bg-opacity-20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                    </svg>
                </div>
                <div class="animate-float delay-500 bg-white bg-opacity-20 p-3 rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11.5V14m0-2.5v-6a1.5 1.5 0 113 0m-3 6a1.5 1.5 0 00-3 0v2a7.5 7.5 0 0015 0v-5a1.5 1.5 0 00-3 0m-6-3V11m0-5.5v-1a1.5 1.5 0 013 0v1m0 0V11m0-5.5a1.5 1.5 0 013 0v3m0 0V11"></path>
                    </svg>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Content -->
    <main class="flex-1 min-w-0 max-w-full">
        <div class="max-w-screen-xl mx-auto">
            <!-- Form Section -->
            <section class="mb-12 lg:mb-24 pb-10 pt-10" id="plan-form">
                <div class="container mx-auto px-4">
                    <div class="max-w-5xl mx-auto bg-white rounded-xl shadow-xl p-8 relative">
                        <!-- Decorative elements -->
                        <div class="absolute -top-6 -right-6 w-12 h-12 bg-yellow-400 rounded-full opacity-50"></div>
                        <div class="absolute -bottom-6 -left-6 w-12 h-12 bg-primary rounded-full opacity-50"></div>
                          @if(request('travel_date'))
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 border border-green-200 rounded-lg p-4 mb-6">
                                    <div class="flex items-center">
                                        
                                        <svg class="w-5 h-5 text-green-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        
                                        <span class="text-green-700 font-medium">
                                            Step 2 of 2: Planning your trip to <span class="font-bold">{{ request('location', 'your destination') }}</span>
                                          
                                                on {{ \Carbon\Carbon::parse(request('travel_date'))->format('M d, Y') }}
                                            
                                        </span>
                                        
                                    </div>
                                   
                                </div>
                                 @endif
                            <div class="text-center mb-10">
                            <h2 class="text-2xl font-bold text-gray-800 mb-3">Tell us your travel preferences</h2>
                            <p class="text-gray-600 max-w-3xl mx-auto">Just provide some basic information, and our AI trip planner will generate a customized itinerary based on your preferences, including activities, restaurants, and hidden gems.</p>
                        </div>

                        <form action="{{ route('travel.generate') }}" id="travelForm" method="POST" class="space-y-8"
                            onsubmit="return validateForm()">
                            @csrf
                            <!-- Two column layout for first row -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 md:gap-8">
                                <!-- Destination -->
                                <div class="transition-all duration-300 hover:shadow-md p-3 md:p-4 rounded-lg group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2 group-hover:text-primary transition-colors">What is destination of choice?*</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        
                                        </div>

                                        <div class="form-group position-relative" style="position: relative;">

                                            <input type="text"
                                                class="w-full border border-gray-300 rounded-md shadow-sm py-3 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base"
                                                id="location"
                                                name="location"
                                                value="{{ request('location', '') }}"
                                                placeholder="Type to search locations..."
                                                autocomplete="off">
                                            <div id="suggestions" class="suggestions-container d-none" role="listbox"></div>
                                        </div>

                                        <style>
                                            .suggestions-container {
                                                position: absolute;
                                                top: 100%;
                                                left: 0;
                                                width: 100%;
                                                z-index: 1000;
                                                background: #fff;
                                                border: 1px solid #ccc;
                                                border-top: none;
                                                max-height: 200px;
                                                overflow-y: auto;
                                                display: none;
                                            }

                                            .suggestion-item {
                                                padding: 10px;
                                                cursor: pointer;
                                            }

                                            .suggestion-item:hover {
                                                background-color: #f0f0f0;
                                            }

                                            .suggestions-container.show {
                                                display: block;
                                            }
                                        </style>


                                    </div>
                                </div>

                                <!-- Travel Date -->
                                <div class="transition-all duration-300 hover:shadow-md p-3 md:p-4 rounded-lg group">
                                    <label class="block text-sm font-medium text-gray-700 mb-2 group-hover:text-primary transition-colors">When are you planning to travel?*</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400 group-hover:text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>

                                        <input type="date" name="travel" required placeholder="dd/mm/yyyy"
                                           value="{{ request('travel_date', '') }}"
                                            min="{{ \Carbon\Carbon::now()->format('Y-m-d') }}"
                                            class="w-full border border-gray-300 rounded-md shadow-sm py-3 pl-10 pr-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base" />
                                    </div>
                                </div>
                            </div>

                             @if(request('travel_date'))
                             <!-- Add this after the destination/date row -->
                            <div class="bg-gray-50 rounded-lg p-4">
                                <div class="flex items-center justify-between text-sm text-gray-600 mb-2">
                                    <span>Form completion</span>
                                    <span id="progressPercent">{{ request('location') && request('travel_date') ? '30%' : '0%' }}</span>
                                </div>
                                <div class="w-full bg-gray-300 rounded-full h-2">
                                    <div id="progressBar" class="bg-primary h-2 rounded-full transition-all duration-300" 
                                        style="width: {{ request('location') && request('travel_date') ? '30%' : '0%' }}"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">
                                    {{ request('location') && request('travel_date') ? 'Great start! Just a few more details needed.' : 'Complete all fields to generate your itinerary' }}
                                </p>
                            </div>
                            @endif

                            <!-- Duration -->
                            <div class="transition-all duration-300 hover:shadow-md p-3 md:p-4 rounded-lg">
                                <label class="block text-sm font-medium text-gray-700 mb-2">How many days are you planning to travel?*</label>
                                <div class="flex items-center">
                                    <span class="text-sm font-medium mr-2">Day</span>
                                    <button type="button" onclick="decrementDays()"
                                        class="px-4 py-3 border border-gray-300 rounded-l bg-white hover:bg-gray-50 hover:border-primary transition-colors text-lg">-</button>
                                    <input type="number" value="3" id="daysInput" name="duration" min="1"
                                        max="5" required
                                        class="w-16 text-center border-y border-gray-300 py-3 focus:outline-none focus:ring-2 focus:ring-primary focus:border-primary text-base" />
                                    <button type="button" onclick="incrementDays()"
                                        class="px-4 py-3 border border-gray-300 rounded-r bg-white hover:bg-gray-50 hover:border-primary transition-colors text-lg">+</button>
                                </div>
                            </div>

                            <!-- Budget -->
                            <div class="transition-all duration-300 hover:shadow-md p-3 md:p-4 rounded-lg">
                                <input type="hidden" name="budget" id="budgetInput" required>
                                <label class="block text-sm font-medium text-gray-700 mb-1">What is Your Budget?*</label>
                                <p class="text-xs text-gray-500 mb-3">The budget is exclusively allocated for activities and dining purposes.</p>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                                    <div class="budget-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="setBudget('low')">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <rect x="3" y="6" width="18" height="12" rx="2" stroke-width="2" />
                                                <path d="M7 12h10" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Low</div>
                                        <p class="text-sm text-gray-500 mt-1">0 - 1000 USD</p>
                                    </div>
                                    <div class="budget-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="setBudget('medium')">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <rect x="3" y="6" width="18" height="12" rx="2" stroke-width="2" />
                                                <path d="M12 8v8M8 12h8" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Medium</div>
                                        <p class="text-sm text-gray-500 mt-1">1000 - 2500 USD</p>
                                    </div>
                                    <div class="budget-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="setBudget('high')">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <rect x="3" y="6" width="18" height="12" rx="2" stroke-width="2" />
                                                <path d="M12 8v8M8 12h8M16 10l-8 4" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">High</div>
                                        <p class="text-sm text-gray-500 mt-1">2500+ USD</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Companions -->
                            <div class="transition-all duration-300 hover:shadow-md p-3 md:p-4 rounded-lg">
                                <input type="hidden" name="traveler" id="companionInput" required>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Who do you plan on traveling with on your next adventure?</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                                    <div class="companion-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1"
                                        onclick="setCompanion('Solo')">
                                        <div class="mb-2">
                                            <svg class="w-8 h-8 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <circle cx="12" cy="8" r="4" stroke-width="2" />
                                                <path d="M6 21v-2a4 4 0 014-4h4a4 4 0 014 4v2" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium">Solo</div>
                                    </div>
                                    <div class="companion-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1"
                                        onclick="setCompanion('Couple')">
                                        <div class="mb-2">
                                            <svg class="w-8 h-8 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <circle cx="9" cy="8" r="3" stroke-width="2" />
                                                <circle cx="15" cy="8" r="3" stroke-width="2" />
                                                <path d="M4 21v-2a4 4 0 014-4h8a4 4 0 014 4v2" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium">Couple</div>
                                    </div>
                                    <div class="companion-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1"
                                        onclick="setCompanion('Family')">
                                        <div class="mb-2">
                                            <svg class="w-8 h-8 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <circle cx="12" cy="6" r="3" stroke-width="2" />
                                                <circle cx="7" cy="13" r="2" stroke-width="2" />
                                                <circle cx="17" cy="13" r="2" stroke-width="2" />
                                                <path d="M5 21v-2a3 3 0 013-3h8a3 3 0 013 3v2" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium">Family</div>
                                    </div>
                                    <div class="companion-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1"
                                        onclick="setCompanion('Friends')">
                                        <div class="mb-2">
                                            <svg class="w-8 h-8 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <circle cx="7" cy="8" r="3" stroke-width="2" />
                                                <circle cx="17" cy="8" r="3" stroke-width="2" />
                                                <path d="M3 21v-2a4 4 0 014-4h2m8 0h2a4 4 0 014 4v2" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium">Friends</div>
                                    </div>
                                </div>
                            </div>

                            <!-- Activities -->
                            <div class="transition-all duration-300 hover:shadow-md p-3 md:p-4 rounded-lg">
                                <input type="hidden" name="activities" id="activitiesInput" required>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Which activities are you interested in?</label>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('Beaches')" data-activity="Beaches">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M13 21H3m18-4H3m18-4H3m18-4H3M21 5H3" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Beaches</div>
                                    </div>
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('City sightseeing')" data-activity="City sightseeing">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M3 21h18M3 7v14m18-14v14M6 21V11m4 10V11m4 10V7m4 14V11" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">City sightseeing</div>
                                    </div>
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('Outdoor adventures')" data-activity="Outdoor adventures">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M8 3h8l4 8-10 3L4 11l4-8z" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Outdoor adventures</div>
                                    </div>
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('Festivals/events')" data-activity="Festivals/events">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Festivals/events</div>
                                    </div>
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('Food exploration')" data-activity="Food exploration">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M3 6h18M3 12h18M3 18h18" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Food exploration</div>
                                    </div>
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('Nightlife')" data-activity="Nightlife">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M12 3a6 6 0 016 6c0 7-6 11-6 11s-6-4-6-11a6 6 0 016-6z" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Nightlife</div>
                                    </div>
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('Shopping')" data-activity="Shopping">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Shopping</div>
                                    </div>
                                    <div class="activity-option border border-gray-300 rounded-lg p-4 md:p-5 text-center cursor-pointer hover:border-primary hover:bg-purple-50 transition-all duration-300 transform hover:-translate-y-1 active:scale-95"
                                        onclick="toggleActivity('Spa wellness')" data-activity="Spa wellness">
                                        <div class="mb-2">
                                            <svg class="w-10 h-10 mx-auto text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                                <path d="M12 3c7.2 0 9 1.8 9 9s-1.8 9-9 9-9-1.8-9-9 1.8-9 9-9z" stroke-width="2" />
                                            </svg>
                                        </div>
                                        <div class="font-medium text-base">Spa wellness</div>
                                    </div>
                                </div>
                            </div>
                            <div id="locationDataInputs">
                                <input type="hidden" name="country" id="user_country">
                                <input type="hidden" name="city" id="user_city">
                                <input type="hidden" name="ip" id="user_ip">
                                <input type="hidden" name="longitude" id="user_longitude">
                                <input type="hidden" name="latitude" id="user_latitude">
                            </div>

                            <!-- Submit -->
                            <div class="flex justify-center mt-8">
                                <button type="submit" id="submitBtn"
                                    class="w-full sm:w-auto bg-primary hover:bg-primary-dark text-white px-6 sm:px-10 py-4 rounded-full flex items-center justify-center font-medium transition-all duration-300 transform hover:-translate-y-1 hover:shadow-lg">
                                    <span id="submitText" class="mr-2">Generate My Itinerary</span>
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                    </svg>
                                    <svg id="spinner" class="hidden ml-2 h-5 w-5 animate-spin" viewBox="0 0 24 24" fill="none">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div id="overlay" class="hidden fixed inset-0 bg-black bg-opacity-70 z-50 backdrop-blur-sm flex items-center justify-center">
                    <div class="bg-white p-10 rounded-xl shadow-2xl text-center w-96 animate__animated animate__fadeInUp">
                        <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-10 h-10 text-primary animate-pulse-slow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                            </svg>
                        </div>
                        <p class="text-2xl font-bold text-primary mb-3">Generating your plan</p>
                        <p class="text-sm text-gray-600">We are processing your travel preferences and generating the
                            best possible plan for you.</p>
                        <br>
                        <div class="h-2 w-full bg-gray-200 rounded-full overflow-hidden">
                            <div class="h-full bg-primary rounded-full" id="progressBar" style="width: 0%"></div>
                        </div>
                        <div class="text-sm text-gray-600 mt-2">
                            <span class="font-medium" id="progressStatus">Initializing...</span>
                            <span class="ml-2" id="progressCounter">0%</span>
                        </div>
                        <script>
                            function startProgress() {
                                let progress = 0;
                                const progressCounter = document.getElementById('progressCounter');
                                const progressBar = document.getElementById('progressBar');
                                const progressStatus = document.getElementById('progressStatus');

                                const stages = [
                                    { percent: 10, status: 'Analyzing preferences...' },
                                    { percent: 25, status: 'Researching destinations...' },
                                    { percent: 40, status: 'Finding activities...' },
                                    { percent: 55, status: 'Calculating costs...' },
                                    { percent: 70, status: 'Optimizing itinerary...' },
                                    { percent: 85, status: 'Finalizing details...' },
                                    { percent: 95, status: 'Almost done...' }
                                ];

                                let currentStage = 0;

                                const interval = setInterval(() => {
                                    if (currentStage < stages.length) {
                                        const targetPercent = stages[currentStage].percent;
                                        if (progress < targetPercent) {
                                            progress += 1;
                                            progressCounter.textContent = `${progress}%`;
                                            progressBar.style.width = `${progress}%`;
                                        } else {
                                            progressStatus.textContent = stages[currentStage].status;
                                            currentStage++;
                                        }
                                    } else {
                                        clearInterval(interval);
                                    }
                                }, 100);
                            }

                            // Start progress when form is submitted
                            document.getElementById('travelForm').addEventListener('submit', function() {
                                startProgress();
                            });
                        </script>
                    </div>
                </div>
            </section>
        </div>
    </main>




    <script>
        // Form functionality
        let selectedBudget = null;
        let selectedCompanion = null;
        let selectedActivities = [];

        function decrementDays() {
            const input = document.getElementById('daysInput');
            if (input.value > input.min) {
                input.value = parseInt(input.value) - 1;
            }
        }

        function incrementDays() {
            const input = document.getElementById('daysInput');
            if (input.value < 5) {
                input.value = parseInt(input.value) + 1;
            }
        }

        function setBudget(budget) {
            selectedBudget = budget;
            document.getElementById('budgetInput').value = budget;

            // Highlight selected option
            document.querySelectorAll('.budget-option').forEach(option => {
                option.classList.remove('border-primary', 'bg-purple-50');
                option.querySelector('svg').classList.remove('text-primary');
                option.querySelector('svg').classList.add('text-gray-500');
            });

            const element = event.currentTarget;
            element.classList.add('border-primary', 'bg-purple-50');
            element.querySelector('svg').classList.remove('text-gray-500');
            element.querySelector('svg').classList.add('text-primary');
        }

        function setCompanion(companion) {
            selectedCompanion = companion;
            document.getElementById('companionInput').value = companion;

            // Highlight selected option
            document.querySelectorAll('.companion-option').forEach(option => {
                option.classList.remove('border-primary', 'bg-purple-50');
                option.querySelector('svg').classList.remove('text-primary');
                option.querySelector('svg').classList.add('text-gray-500');
            });

            const element = event.currentTarget;
            element.classList.add('border-primary', 'bg-purple-50');
            element.querySelector('svg').classList.remove('text-gray-500');
            element.querySelector('svg').classList.add('text-primary');
        }

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


        function updateSelectedCounter() {
            const count = selectedActivities.length;
            const counterElement = document.getElementById('selectedActivitiesCount');
            if (counterElement) {
                counterElement.textContent = count;
                if (count > 0) {
                    counterElement.classList.remove('bg-gray-200');
                    counterElement.classList.add('bg-primary', 'text-white');
                } else {
                    counterElement.classList.remove('bg-primary', 'text-white');
                    counterElement.classList.add('bg-gray-200');
                }
            }
        }

        function validateForm() {
            // Check if budget is selected
            if (!selectedBudget) {
                showAlert('Please select your budget');
                return false;
            }

            // Check if companion is selected
            if (!selectedCompanion) {
                showAlert('Please select who you are traveling with');
                return false;
            }

            // Check if at least one activity is selected
            if (selectedActivities.length === 0) {
                showAlert('Please select at least one activity');
                return false;
            }

            // Show loading overlay
            document.getElementById('overlay').classList.remove('hidden');
            document.getElementById('spinner').classList.remove('hidden');
            document.getElementById('submitText').textContent = 'Generating...';

            return true;
        }

        function showAlert(message) {
            const alertBox = document.createElement('div');
            alertBox.className = 'fixed top-20 left-1/2 transform -translate-x-1/2 bg-white p-4 rounded-lg shadow-lg border-l-4 border-primary z-50 animate__animated animate__fadeInDown';
            alertBox.innerHTML = `
                <div class="flex items-center">
                    <div class="text-primary mr-3">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p>${message}</p>
                </div>
            `;
            document.body.appendChild(alertBox);

            setTimeout(() => {
                alertBox.classList.remove('animate__fadeInDown');
                alertBox.classList.add('animate__fadeOutUp');
                setTimeout(() => {
                    document.body.removeChild(alertBox);
                }, 500);
            }, 3000);
        }

        // Add some nice animations on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const options = {
                threshold: 0.1
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
                        observer.unobserve(entry.target);
                    }
                });
            }, options);

            document.querySelectorAll('.feature-card, .budget-option, .companion-option, .activity-option').forEach(card => {
                observer.observe(card);
            });
        });
    </script>




    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            let searchTimeout;
            const $suggestionsContainer = $('#suggestions');
            const $locationInput = $('#location');

            $locationInput.on('input', function() {
                const searchTerm = $(this).val().trim();

                clearTimeout(searchTimeout);

                if (searchTerm.length < 2) {
                    $suggestionsContainer.removeClass('show').addClass('d-none');
                    return;
                }

                $suggestionsContainer
                    .html(`
                        <div class="suggestion-item flex items-center space-x-2 animate-pulse text-gray-600" role="option">
                            <svg class="w-5 h-5 text-primary animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"></path>
                            </svg>
                            <span>Loading suggestions...</span>
                        </div>
                    `)
                    .removeClass('d-none').addClass('show');

                searchTimeout = setTimeout(() => {
                    $.get('/api/location-suggestions', {
                            term: searchTerm
                        })
                        .done(function(response) {
                            if (response && response.length > 0) {
                                let suggestionsHtml = '';
                                response.forEach(function(suggestion) {
                                    suggestionsHtml += `<div class="suggestion-item" role="option">${suggestion.DisplayText}</div>`;
                                });
                                $suggestionsContainer.html(suggestionsHtml).removeClass('d-none').addClass('show');
                            } else {
                                $suggestionsContainer.html('<div class="suggestion-item" role="option">No results found</div>').addClass('show');
                            }
                        })
                        .fail(function() {
                            $suggestionsContainer.html('<div class="suggestion-item" role="option">Error loading suggestions</div>').addClass('show');
                        });
                }, 300);
            });

            // Handle click on suggestion
            $suggestionsContainer.on('click', '.suggestion-item', function() {
                $locationInput.val($(this).text());
                $suggestionsContainer.removeClass('show').addClass('d-none');
            });

            // Hide suggestions on outside click
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.form-group').length) {
                    $suggestionsContainer.removeClass('show').addClass('d-none');
                }
            });
        });
    </script>

    <script>
        class CountryDetector {
            constructor() {
                this.country = null;
                this.countryName = null;
                this.fullData = null; // Store full data if available
            }

            async detectCountry() {
                try {
                    // Try multiple services for reliability
                    const services = [
                        'https://ipapi.co/json/',
                        'https://api.country.is/',
                        'http://ip-api.com/json/'
                    ];

                    for (let service of services) {
                        try {
                            const response = await fetch(service);
                            const data = await response.json();

                            // Log the full data received from the service
                            console.log(`Data from ${service}:`, data);

                            if (service.includes('ipapi.co')) {
                                this.country = data.country_code;
                                this.countryName = data.country_name;
                                this.fullData = data; // Store full data
                            } else if (service.includes('country.is')) {
                                this.country = data.country;
                                this.countryName = data.country;
                                this.fullData = data; // Store full data
                            } else if (service.includes('ip-api.com')) {
                                this.country = data.countryCode;
                                this.countryName = data.country;
                                this.fullData = data; // Store full data
                            }

                            if (this.country) {
                                console.log(`Country detected: ${this.countryName} (${this.country})`);
                                break; // Stop after the first successful service
                            }
                        } catch (error) {
                            console.warn(`Service ${service} failed:`, error);
                            continue;
                        }
                    }

                    if (this.country) {
                        this.updateUI();
                        // this.saveToServer();
                        // this.saveToLocalStorage();
                    } else {
                        throw new Error('All services failed to detect country');
                    }

                } catch (error) {
                    console.error('Country detection failed:', error);
                    // Default fallback
                    this.country = 'US';
                    this.countryName = 'United States';
                    this.fullData = { fallback: true, country: this.country, country_name: this.countryName };
                    console.log(`Using default fallback country: ${this.countryName} (${this.country})`);
                    this.updateUI();
                }
            }

            updateUI() {
                // Get hidden input elements
                const countryInput = document.getElementById('user_country');
                const cityInput = document.getElementById('user_city');
                const ipInput = document.getElementById('user_ip');
                const longitudeInput = document.getElementById('user_longitude');
                const latitudeInput = document.getElementById('user_latitude');

                // Populate country code input
                if (countryInput && this.countryName) {
                    countryInput.value = this.countryName;
                } else if (countryInput) {
                    //  console.warn("Country code not available to populate 'user_country'.");
                }

                // Populate other inputs from fullData if available
                if (this.fullData) {
                    if (cityInput && this.fullData.city) {
                        cityInput.value = this.fullData.city;
                    } else if (cityInput) {
                        //  console.warn("City data not available in fullData to populate 'user_city'.");
                    }

                    if (ipInput && (this.fullData.ip || this.fullData.query)) { // Check for 'ip' or 'query' property
                        ipInput.value = this.fullData.ip || this.fullData.query;
                    } else if (ipInput) {
                        //  console.warn("IP data not available in fullData to populate 'user_ip'.");
                    }

                    if (longitudeInput && (this.fullData.longitude || this.fullData.lon)) { // Check for 'longitude' or 'lon' property
                        longitudeInput.value = this.fullData.longitude || this.fullData.lon;
                    } else if (longitudeInput) {
                        //  console.warn("Longitude data not available in fullData to populate 'user_longitude'.");
                    }

                    if (latitudeInput && (this.fullData.latitude || this.fullData.lat)) { // Check for 'latitude' or 'lat' property
                        latitudeInput.value = this.fullData.latitude || this.fullData.lat;
                    } else if (latitudeInput) {
                        //  console.warn("Latitude data not available in fullData to populate 'user_latitude'.");
                    }
                } else {
                    console.warn("data not available to populate inputs.");
                }

            }

            saveToLocalStorage() {
                localStorage.setItem('user_country', this.country);
                localStorage.setItem('user_country_name', this.countryName);
                if (this.fullData) {
                     localStorage.setItem('user_country_data', JSON.stringify(this.fullData));
                }
            }

          

            getCountry() {
                return this.country || localStorage.getItem('user_country') || 'US';
            }

             getCountryName() {
                return this.countryName || localStorage.getItem('user_country_name') || 'United States';
            }

            getFullData() {
                 const storedData = localStorage.getItem('user_country_data');
                 return this.fullData || (storedData ? JSON.parse(storedData) : null);
            }
        }

        // Initialize and use
        const detector = new CountryDetector();
        detector.detectCountry();

        // Make it globally available
        window.userCountry = detector;
    </script>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const location = document.getElementById('location').value;
    const travelDate = document.querySelector('input[name="travel"]').value;
    
    // If both destination and date are pre-filled, focus on duration
    if (location && travelDate) {
        document.getElementById('daysInput').focus();
        updateProgress(30);
    } else if (location) {
        // Only destination filled, focus on date
        document.querySelector('input[name="travel"]').focus();
        updateProgress(15);
    }
});

function updateProgress(percentage) {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressPercent');
    
    if (progressBar) progressBar.style.width = percentage + '%';
    if (progressText) progressText.textContent = percentage + '%';
}
</script>

    <!-- Footer -->
@endsection


</body>
</html>