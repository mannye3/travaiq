@extends('layouts.app')

@section('title', 'Home - Travaiq')

@section('content')

    <!-- Hero Section -->
    <section class="relative py-16 md:py-24 overflow-hidden bg-gradient-to-br from-white via-indigo-50 to-purple-50">
        <!-- Decorative elements -->
        <div class="absolute top-0 inset-x-0 h-40 bg-hero-pattern opacity-30"></div>
        <div class="absolute -right-12 top-1/4 w-40 h-40 bg-accent/20 rounded-full blur-xl"></div>
        <div class="absolute left-0 bottom-0 w-full h-1/2 bg-gradient-to-t from-white/50 to-transparent"></div>
        
        <div class="container mx-auto px-4 relative">
            <div class="flex flex-wrap items-center">
                <div class="w-full md:w-1/2 mb-10 md:mb-0 md:pr-10 animate__animated animate__fadeInLeft">
                    <div class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                        AI-Powered Travel Planning
                    </div>
                    <h1 class="text-3xl md:text-5xl font-bold mb-6 text-gray-800 leading-tight">Discover Your <span class="text-primary">Perfect Trip</span> with AI Intelligence</h1>
                    <p class="text-lg text-gray-600 mb-8">Create personalized travel itineraries in minutes, tailored to your preferences and style. No more hours of research.</p>
                    
                    <div class="flex flex-wrap items-center gap-4 mb-8">
                        <a href="{{route('createPlan')}}" class="inline-block px-8 py-3 rounded-full bg-gradient-to-r from-primary to-primary-light text-white font-medium shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1 flex items-center">
                            <span>Start Planning Now</span>
                            <svg class="ml-2 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                            </svg>
                        </a>
                        <a href="#how-it-works" class="inline-flex items-center text-primary hover:text-primary-dark transition duration-300">
                            <span class="mr-2">See how it works</span>
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </a>
                    </div>

                    <div class="flex flex-wrap items-center gap-6 text-sm">
                        <div class="flex items-center">
                            <div class="w-12 h-12 flex-shrink-0 bg-white rounded-full shadow-md flex items-center justify-center mr-3">
                                <span class="text-primary-dark text-lg font-bold">10K+</span>
                            </div>
                            <span class="text-gray-600">Trips Planned</span>
                        </div>
                        <div class="flex items-center">
                            <div class="text-yellow-500 mr-2">★★★★★</div>
                            <span class="text-gray-600">4.9/5 User Rating</span>
                        </div>
                    </div>
                </div>
                <div class="w-full md:w-1/2 relative animate__animated animate__fadeInRight">
                    <div class="relative">
                        <!-- Blob background -->
                        <div class="absolute -inset-4 bg-gradient-to-r from-primary/20 to-primary-light/20 blob"></div>

                        <!-- Main image -->
                        <img src="https://lh3.googleusercontent.com/place-photos/AJnk2cwN-6EU02ds0vk0fu4hUjSo5zIoJd_7pzBytaCJ29cvtEjJ8fNTEoZpmdoWAIf2aN2aV_grfPA3hHADCqeEEouWuiWEBHubu9QU0k_AhjD9m9kh3nThmKdI4ETg6N5awlPUQ6C34nQKhyUj=s1600-w800" alt="Tropical beach destination" class="rounded-lg shadow-xl relative z-10 transform -rotate-2">
                        
                        <!-- Floating elements -->
                        <div class="absolute top-1/4 -left-8 z-20 bg-white p-3 rounded-lg shadow-lg animate-float">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                <span class="font-medium">Paris, France</span>
                            </div>
                        </div>
                        <div class="absolute bottom-10 -right-6 z-20 bg-white p-3 rounded-lg shadow-lg animate-float-slow">
                            <div class="flex items-center gap-2">
                                <svg class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"></path>
                                </svg>
                                <span class="font-medium">Personalized for you</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- Features Section -->
    <section class="py-20 md:py-28">
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">Why Choose Travaiq</span>
                <h2 class="text-3xl md:text-4xl font-bold mb-6 text-gray-800">Experience the Future of Travel Planning</h2>
                <p class="text-gray-600 text-lg">Our AI-powered platform makes travel planning effortless, so you can focus on creating memories that last a lifetime.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="group bg-white rounded-xl shadow-md p-8 transition-all duration-500 hover:shadow-xl hover:-translate-y-2 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full transition-all duration-500 group-hover:scale-150"></div>
                    <div class="w-16 h-16 bg-primary/10 group-hover:bg-primary/20 rounded-2xl flex items-center justify-center text-2xl mb-6 relative transition-all duration-300 rotate-3 group-hover:rotate-6">🗺️</div>
                    <h3 class="text-xl font-bold mb-4 relative">AI-Powered Planning</h3>
                    <p class="text-gray-600 mb-6 relative">Our advanced AI analyzes thousands of data points to create personalized itineraries based on your unique preferences and travel style.</p>
                    <!-- <a href="#" class="inline-flex items-center text-primary font-medium relative group-hover:underline">
                        <span>Learn more</span>
                        <svg class="ml-2 w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a> -->
                </div>
                
                <!-- Feature 2 -->
                <div class="group bg-white rounded-xl shadow-md p-8 transition-all duration-500 hover:shadow-xl hover:-translate-y-2 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full transition-all duration-500 group-hover:scale-150"></div>
                    <div class="w-16 h-16 bg-primary/10 group-hover:bg-primary/20 rounded-2xl flex items-center justify-center text-2xl mb-6 relative transition-all duration-300 rotate-3 group-hover:rotate-6">🎯</div>
                    <h3 class="text-xl font-bold mb-4 relative">Smart Recommendations</h3>
                    <p class="text-gray-600 mb-6 relative">Get intelligent suggestions for attractions, restaurants, and activities that match your interests, not just the typical tourist spots.</p>
                    <!-- <a href="#" class="inline-flex items-center text-primary font-medium relative group-hover:underline">
                        <span>Learn more</span>
                        <svg class="ml-2 w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a> -->
                </div>
                
                <!-- Feature 3 -->
                <div class="group bg-white rounded-xl shadow-md p-8 transition-all duration-500 hover:shadow-xl hover:-translate-y-2 relative overflow-hidden">
                    <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full transition-all duration-500 group-hover:scale-150"></div>
                    <div class="w-16 h-16 bg-primary/10 group-hover:bg-primary/20 rounded-2xl flex items-center justify-center text-2xl mb-6 relative transition-all duration-300 rotate-3 group-hover:rotate-6">⏱️</div>
                    <h3 class="text-xl font-bold mb-4 relative">Time-Saving Solutions</h3>
                    <p class="text-gray-600 mb-6 relative">Plan your perfect trip in minutes, not hours. Our AI handles the research while you enjoy the anticipation of your upcoming adventure.</p>
                    <!-- <a href="#" class="inline-flex items-center text-primary font-medium relative group-hover:underline">
                        <span>Learn more</span>
                        <svg class="ml-2 w-4 h-4 transition-transform duration-300 group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a> -->
                </div>
            </div>
            
            <!-- Feature showcase -->
            <div class="mt-24 grid grid-cols-1 lg:grid-cols-2 gap-16 items-center">
                <div class="relative">
                    <div class="absolute -inset-4 bg-gradient-to-r from-accent/20 to-accent-light/20 blob"></div>
                    <img src="{{ asset('tripland2.png') }}" alt="Travaiq app interface showing a personalized itinerary" class="rounded-xl shadow-xl relative z-10">
                    
                    <!-- Floating elements -->
                    <div class="absolute top-8 -right-8 z-20 bg-white p-3 rounded-lg shadow-lg animate-float">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span class="font-medium">Save 5+ hours</span>
                        </div>
                    </div>
                </div>
                
                <div>
                    <span class="inline-block px-3 py-1 bg-accent/10 text-accent text-sm font-medium rounded-full mb-4">The Travaiq Difference</span>
                    <h2 class="text-3xl font-bold mb-6">Travel Planning That Adapts to You</h2>
                    <p class="text-gray-600 mb-8 text-lg">Unlike traditional travel agencies or static guidebooks, our AI learns your preferences over time, creating increasingly personalized recommendations with each trip you plan.</p>
                    
                    <div class="space-y-4">
                        <div class="flex items-start">
                            <div class="bg-green-100 p-1 rounded-full mr-3 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium mb-1">Personalized Recommendations</h3>
                                <p class="text-gray-600">Tailored suggestions based on your interests, not generic tourist traps</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-green-100 p-1 rounded-full mr-3 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium mb-1">Optimal Scheduling</h3>
                                <p class="text-gray-600">Smart algorithms organize your activities to minimize travel time and maximize enjoyment</p>
                            </div>
                        </div>
                        
                        <div class="flex items-start">
                            <div class="bg-green-100 p-1 rounded-full mr-3 mt-1">
                                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div>
                                <h3 class="font-medium mb-1">Local Insights</h3>
                                <p class="text-gray-600">Discover hidden gems and authentic experiences recommended by locals</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- How It Works -->
    <section id="how-it-works" class="py-20 md:py-28 bg-gradient-to-b from-white to-indigo-50 relative">
        <div class="absolute top-0 inset-x-0 h-40 bg-hero-pattern opacity-30 transform rotate-180"></div>
        
        <div class="container mx-auto px-4">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">Simple Process</span>
                <h2 class="text-4xl font-bold mb-6 text-gray-800">How It Works</h2>
                <p class="text-gray-600 text-lg">Plan your perfect trip in four easy steps – tailored just for you. Our intuitive process makes travel planning a breeze.</p>
            </div>
            
            <div class="relative">
                <!-- Desktop Timeline Line -->
                <div class="hidden lg:block absolute top-36 left-0 right-0 h-1 bg-gray-200 z-0"></div>
                
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-8 relative z-10">

                    <!-- Step 1 -->
                    <div class="group">
                        <div class="bg-white rounded-xl shadow-md p-8 mb-6 transition-all duration-500 hover:shadow-xl relative h-64 flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">01</div>
                            <div class="text-3xl mb-4 animate-float delay-150">📍</div>
                            <h3 class="text-xl font-bold mb-2">Choose Destination</h3>
                            <p class="text-gray-600">Pick from top destinations curated to match your interests, or specify your dream location.</p>
                        </div>
                        
                        <!-- Timeline connector - Desktop -->
                        <div class="hidden lg:flex justify-center">
                            <div class="w-6 h-6 bg-primary rounded-full shadow-lg z-20"></div>
                        </div>
                    </div>


                  
                    
                    

                    <!-- Step 2 -->
                    <div class="group">
                        <div class="bg-white rounded-xl shadow-md p-8 mb-6 transition-all duration-500 hover:shadow-xl relative h-64 flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">02</div>
                            <div class="text-3xl mb-4 animate-float delay-300">💼</div>
                            <h3 class="text-xl font-bold mb-2">Set Your Budget</h3>
                            <p class="text-gray-600">Let us know your budget and we'll optimize your experience without stretching your wallet.</p>
                        </div>
                        
                        <!-- Timeline connector - Desktop -->
                        <div class="hidden lg:flex justify-center">
                            <div class="w-6 h-6 bg-primary rounded-full shadow-lg z-20"></div>
                        </div>
                    </div>



                      <!-- Step 3 -->
                    <div class="group">
                        <div class="bg-white rounded-xl shadow-md p-8 mb-6 transition-all duration-500 hover:shadow-xl relative h-64 flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">03</div>
                            <div class="text-3xl mb-4 animate-float">🎯</div>
                            <h3 class="text-xl font-bold mb-2">Set Your Preferences</h3>
                            <p class="text-gray-600">Tell us what you love—beaches, culture, nightlife—and we'll personalize your trip.</p>
                        </div>
                        
                        <!-- Timeline connector - Desktop -->
                        <div class="hidden lg:flex justify-center">
                            <div class="w-6 h-6 bg-primary rounded-full shadow-lg z-20"></div>
                        </div>
                    </div>

                    <!-- Step 4 -->
                    <div class="group">
                        <div class="bg-white rounded-xl shadow-md p-8 mb-6 transition-all duration-500 hover:shadow-xl relative h-64 flex flex-col items-center text-center">
                            <div class="w-16 h-16 bg-primary rounded-full flex items-center justify-center text-white text-xl font-bold mb-6 transform transition-transform duration-500 group-hover:scale-110 group-hover:rotate-3">04</div>
                            <div class="text-3xl mb-4 animate-float delay-500">📅</div>
                            <h3 class="text-xl font-bold mb-2">Get Your Plan</h3>
                            <p class="text-gray-600">Receive a complete, personalized itinerary with daily activities, restaurants, and hidden gems.</p>
                        </div>
                        
                        <!-- Timeline connector - Desktop -->
                        <div class="hidden lg:flex justify-center">
                            <div class="w-6 h-6 bg-primary rounded-full shadow-lg z-20"></div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- CTA Button -->
            <div class="text-center mt-12">
                <a href="{{route('createPlan')}}" class="inline-block px-8 py-4 rounded-full bg-gradient-to-r from-primary to-primary-light text-white font-medium shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1">
                    Start Planning Your Trip
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 md:py-28 bg-gradient-to-b from-indigo-50 to-purple-50 relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute top-1/3 -left-20 w-40 h-40 bg-accent/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 -right-20 w-40 h-40 bg-primary/20 rounded-full blur-3xl"></div>
        
        <div class="container mx-auto px-4 relative">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 bg-accent/10 text-accent text-sm font-medium rounded-full mb-4">Testimonials</span>
                <h2 class="text-4xl font-bold mb-6 text-gray-800">What Our Travelers Say</h2>
                <p class="text-gray-600 text-lg">Join thousands of satisfied travelers who've discovered their perfect trips with Travaiq.</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Testimonial 1 -->
                <div class="bg-white rounded-xl shadow-lg p-8 relative transform transition-all duration-500 hover:-translate-y-2 hover:shadow-xl">
                    <!-- Decorative quote -->
                    <div class="absolute -top-4 -left-2 text-6xl text-primary/10">"</div>
                    
                    <div class="flex items-center mb-6">
                        <img src="https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?t=st=1747494650~exp=1747498250~hmac=a73880ed1c58f7a899a42869c06f309e7542d722aece0b4830c5dd2e660e6a8f&w=1380" alt="Sarah Mitchell" class="w-14 h-14 rounded-full border-2 border-white shadow-md mr-4">
                        <div>
                            <div class="font-bold text-lg">Sarah Mitchell</div>
                            <div class="text-sm text-gray-500 flex items-center">
                                <svg class="w-4 h-4 text-primary mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Paris, France
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 relative z-10">The AI recommendations were spot-on! Made planning my Paris trip so much easier. I discovered hidden gems I would never have found on my own.</p>
                    <div class="text-yellow-500">★★★★★</div>
                </div>
                
                <!-- Testimonial 2 -->
                <div class="bg-white rounded-xl shadow-lg p-8 relative transform transition-all duration-500 hover:-translate-y-2 hover:shadow-xl">
                    <!-- Decorative quote -->
                    <div class="absolute -top-4 -left-2 text-6xl text-primary/10">"</div>
                    
                    <div class="flex items-center mb-6">
                        <img src="https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?t=st=1747494650~exp=1747498250~hmac=a73880ed1c58f7a899a42869c06f309e7542d722aece0b4830c5dd2e660e6a8f&w=1380" alt="David Chen" class="w-14 h-14 rounded-full border-2 border-white shadow-md mr-4">
                        <div>
                            <div class="font-bold text-lg">David Chen</div>
                            <div class="text-sm text-gray-500 flex items-center">
                                <svg class="w-4 h-4 text-primary mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Tokyo, Japan
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 relative z-10">Incredible experience! The itinerary was perfectly balanced with everything I wanted to see. Saved me hours of research and made our Tokyo trip unforgettable.</p>
                    <div class="text-yellow-500">★★★★★</div>
                </div>
                
                <!-- Testimonial 3 -->
                <div class="bg-white rounded-xl shadow-lg p-8 relative transform transition-all duration-500 hover:-translate-y-2 hover:shadow-xl">
                    <!-- Decorative quote -->
                    <div class="absolute -top-4 -left-2 text-6xl text-primary/10">"</div>
                    
                    <div class="flex items-center mb-6">
                        <img src="https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?t=st=1747494650~exp=1747498250~hmac=a73880ed1c58f7a899a42869c06f309e7542d722aece0b4830c5dd2e660e6a8f&w=1380" alt="Emma Thompson" class="w-14 h-14 rounded-full border-2 border-white shadow-md mr-4">
                        <div>
                            <div class="font-bold text-lg">Emma Thompson</div>
                            <div class="text-sm text-gray-500 flex items-center">
                                <svg class="w-4 h-4 text-primary mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                                Barcelona, Spain
                            </div>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-6 relative z-10">Saved me hours of research. The personalized suggestions were exactly what I needed. The local restaurant recommendations were outstanding!</p>
                    <div class="text-yellow-500">★★★★★</div>
                </div>
            </div>
            
            <!-- Review Stats -->
            <!-- <div class="mt-16 bg-white rounded-xl shadow-lg p-8 flex flex-wrap justify-between items-center">
                <div class="w-full md:w-auto text-center md:text-left mb-6 md:mb-0">
                    <h3 class="text-2xl font-bold mb-2">Join thousands of happy travelers</h3>
                    <p class="text-gray-600">Start planning your dream vacation today</p>
                </div>
                
                <div class="flex flex-wrap justify-center md:justify-end gap-8">
                    <div class="text-center">
                        <div class="text-4xl font-bold text-primary mb-1">98%</div>
                        <div class="text-sm text-gray-600">Satisfaction rate</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-primary mb-1">15K+</div>
                        <div class="text-sm text-gray-600">Happy travelers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-bold text-primary mb-1">4.9</div>
                        <div class="text-sm text-gray-600">Average rating</div>
                    </div>
                </div>
            </div> -->
        </div>
    </section>

    <!-- FAQ Section -->
    <section class="py-20 bg-white relative">
        <div class="absolute top-0 left-0 w-full h-20 bg-gradient-to-b from-purple-50 to-white"></div>
        
        <div class="container mx-auto px-4 relative">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">Questions & Answers</span>
                <h2 class="text-4xl font-bold mb-6 text-gray-800">Frequently Asked Questions</h2>
                <p class="text-gray-600 text-lg">Everything you need to know before planning your next adventure with Travaiq.</p>
            </div>

            <div class="max-w-3xl mx-auto space-y-6" x-data="{ selected: null }">
                <!-- FAQ Item 1 -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md">
                    <button
                        @click="selected !== 1 ? selected = 1 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center text-gray-800 font-medium text-lg focus:outline-none"
                    >
                        <span>How does the itinerary planning work?</span>
                        <svg :class="selected === 1 ? 'transform rotate-180 text-primary' : 'text-gray-400'" class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="selected === 1" x-collapse class="px-6 pb-6 text-gray-600">
                        <p>Once you provide your preferences, destination, and budget, our AI system analyzes thousands of data points to create a detailed itinerary tailored just for you. The system considers factors like your interests, travel style, budget constraints, and even seasonal recommendations to deliver a comprehensive plan that maximizes your enjoyment while minimizing hassle.</p>
                    </div>
                </div>

                <!-- FAQ Item 2 -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md">
                    <button
                        @click="selected !== 2 ? selected = 2 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center text-gray-800 font-medium text-lg focus:outline-none"
                    >
                        <span>Is this service free?</span>
                        <svg :class="selected === 2 ? 'transform rotate-180 text-primary' : 'text-gray-400'" class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="selected === 2" x-collapse class="px-6 pb-6 text-gray-600">
                        <p>Yes, our basic trip planning service is completely free. You can create unlimited basic itineraries without any cost. We also offer premium options for travelers who want additional features like offline access, collaborative editing, or concierge booking services.</p>
                    </div>
                </div>

                <!-- FAQ Item 3 -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md">
                    <button
                        @click="selected !== 3 ? selected = 3 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center text-gray-800 font-medium text-lg focus:outline-none"
                    >
                        <span>Can I make changes to the plan?</span>
                        <svg :class="selected === 3 ? 'transform rotate-180 text-primary' : 'text-gray-400'" class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="selected === 3" x-collapse class="px-6 pb-6 text-gray-600">
                        <p>Absolutely! You can adjust preferences or destinations anytime and regenerate your itinerary. Our system is designed to be flexible, allowing you to add, remove, or reschedule activities. You can also manually edit any part of your itinerary to create exactly the trip you want.</p>
                    </div>
                </div>

                <!-- FAQ Item 4 -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md">
                    <button
                        @click="selected !== 4 ? selected = 4 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center text-gray-800 font-medium text-lg focus:outline-none"
                    >
                        <span>Do I need to sign up to use the planner?</span>
                        <svg :class="selected === 4 ? 'transform rotate-180 text-primary' : 'text-gray-400'" class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="selected === 4" x-collapse class="px-6 pb-6 text-gray-600">
                        <p>No signup is needed to explore or create a basic itinerary. However, creating a free account allows you to save and access your plans anytime, from any device. Account holders also receive personalized travel recommendations based on their preferences and past trips.</p>
                    </div>
                </div>
                
                <!-- FAQ Item 5 -->
                <div class="bg-white rounded-xl border border-gray-200 shadow-sm transition-all duration-300 hover:shadow-md">
                    <button
                        @click="selected !== 5 ? selected = 5 : selected = null"
                        class="w-full px-6 py-4 text-left flex justify-between items-center text-gray-800 font-medium text-lg focus:outline-none"
                    >
                        <span>How accurate are the recommendations?</span>
                        <svg :class="selected === 5 ? 'transform rotate-180 text-primary' : 'text-gray-400'" class="w-5 h-5 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div x-show="selected === 5" x-collapse class="px-6 pb-6 text-gray-600">
                        <p>Our recommendations are highly accurate, drawing from up-to-date databases of attractions, restaurants, and activities. We combine official tourism data with real reviews and local insights to ensure quality suggestions. Our AI also takes into account seasonal factors, opening hours, and special events to maximize your experience.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Alpine.js CDN -->
    <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>

    <!-- Call to Action -->
    <section class="py-20 bg-gradient-to-r from-primary to-primary-dark text-white relative overflow-hidden">
        <!-- Decorative elements -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%23FFFFFF\" fill-opacity=\"0.05\" fill-rule=\"evenodd\"%3E%3Cpath d=\"M0 40L40 0H20L0 20M40 40V20L20 40\"/%3E%3C/g%3E%3C/svg%3E')] opacity-50"></div>
        <div class="absolute top-0 right-0 w-1/3 h-full bg-white/5 -skew-x-12 transform -translate-x-20"></div>
        
        <div class="container mx-auto px-4 text-center relative">
            <h2 class="text-3xl md:text-5xl font-bold mb-4 text-shadow">Ready to Plan Your Next Adventure?</h2>
            <p class="text-xl mb-10 max-w-2xl mx-auto">Let AI do the hard work. You enjoy the journey. Join thousands of happy travelers who've discovered their perfect trips with Travaiq.</p>
            
            <div class="flex flex-wrap justify-center gap-4">
                <a href="{{route('createPlan')}}" class="inline-block px-8 py-4 bg-white text-primary font-bold rounded-full hover:bg-gray-100 shadow-lg hover:shadow-xl transition duration-300 transform hover:-translate-y-1">Start for Free</a>
                {{-- <a href="#" class="inline-block px-8 py-4 bg-transparent border-2 border-white text-white font-bold rounded-full hover:bg-white/10 transition duration-300 transform hover:-translate-y-1">Watch Demo</a> --}}
            </div>
            
            <div class="mt-12 flex flex-wrap justify-center items-center gap-8">
                <div class="flex -space-x-4">
                    <img src="https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?t=st=1747494650~exp=1747498250~hmac=a73880ed1c58f7a899a42869c06f309e7542d722aece0b4830c5dd2e660e6a8f&w=1380" alt="User" class="w-12 h-12 rounded-full border-2 border-white">
                    <img src="https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?t=st=1747494650~exp=1747498250~hmac=a73880ed1c58f7a899a42869c06f309e7542d722aece0b4830c5dd2e660e6a8f&w=1380" alt="User" class="w-12 h-12 rounded-full border-2 border-white">
                    <img src="https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?t=st=1747494650~exp=1747498250~hmac=a73880ed1c58f7a899a42869c06f309e7542d722aece0b4830c5dd2e660e6a8f&w=1380" alt="User" class="w-12 h-12 rounded-full border-2 border-white">
                    <img src="https://img.freepik.com/free-vector/user-circles-set_78370-4704.jpg?t=st=1747494650~exp=1747498250~hmac=a73880ed1c58f7a899a42869c06f309e7542d722aece0b4830c5dd2e660e6a8f&w=1380" alt="User" class="w-12 h-12 rounded-full border-2 border-white">
                    <span class="flex items-center justify-center w-12 h-12 rounded-full border-2 border-white bg-primary-dark text-white text-xs font-medium">15k+</span>
                </div>
                <div class="text-lg">Join our community of travelers today!</div>
            </div>
        </div>
    </section>

@endsection

    <script>
        // Mobile menu toggle
        document.getElementById('menu-toggle').addEventListener('click', function() {
            const menu = document.getElementById('menu');
            menu.classList.toggle('hidden');
        });
        
        // Intersection Observer for scroll animations
        document.addEventListener('DOMContentLoaded', function() {
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate__animated', entry.target.dataset.animation || 'animate__fadeIn');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);
            
            document.querySelectorAll('.animate-on-scroll').forEach(element => {
                observer.observe(element);
            });
        });
    </script>

@guest
    <script src="https://accounts.google.com/gsi/client" async defer></script>
    <script>
      window.onload = function () {
        console.log('Google One Tap: onload fired');
        try {
          google.accounts.id.initialize({
            client_id: "{{ config('services.google.client_id') }}",
            callback: handleCredentialResponse
          });
          console.log('Google One Tap: initialize called');
          google.accounts.id.prompt((notification) => {
            console.log('Google One Tap: prompt notification', notification);
            if (notification.isNotDisplayed()) {
              console.log('One Tap was not displayed:', notification.getNotDisplayedReason());
            }
            if (notification.isSkippedMoment()) {
              console.log('One Tap was skipped:', notification.getSkippedReason());
            }
            if (notification.isDismissedMoment()) {
              console.log('One Tap was dismissed:', notification.getDismissedReason());
            }
          });
          console.log('Google One Tap: prompt called');
        } catch (e) {
          console.error('Google One Tap error:', e);
        }
      };

      function handleCredentialResponse(response) {
        console.log('Google One Tap: credential response', response);
        // Send the response.credential (JWT) to your backend for verification and login
        fetch('/google-onetap-login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
          },
          body: JSON.stringify({ credential: response.credential })
        })
        .then(res => res.json())
        .then(data => {
          if (data.success) {
            window.location.reload();
          } else {
            alert('Google login failed');
          }
        });
      }
    </script>
@endguest

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const userMenuButton = document.getElementById('user-menu-button');
        const userDropdown = document.getElementById('user-dropdown');

        if (userMenuButton && userDropdown) {
            // Toggle dropdown on button click
            userMenuButton.addEventListener('click', function (e) {
                e.stopPropagation();
                userDropdown.classList.toggle('hidden');
            });

            // Hide dropdown when clicking outside
            document.addEventListener('click', function (e) {
                // Only hide if the dropdown is open and the click is outside both the button and the dropdown
                if (!userDropdown.classList.contains('hidden') &&
                    !userDropdown.contains(e.target) &&
                    !userMenuButton.contains(e.target)) {
                    userDropdown.classList.add('hidden');
                }
            });
        }
    });
</script>
