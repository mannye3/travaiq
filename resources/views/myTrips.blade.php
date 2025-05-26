@extends('layouts.app')

@section('content')

    <!-- Main Content -->
    <main class="flex-1 py-12 px-4 sm:px-6 lg:px-8 max-w-screen-xl mx-auto">
        <!-- Hero Section with Page Title -->
        <section class="mb-8 relative">
            <div class="absolute top-0 inset-x-0 h-40 bg-hero-pattern opacity-30"></div>
            <div class="relative">
                <span class="inline-block px-3 py-1 bg-primary/10 text-primary text-sm font-medium rounded-full mb-4">
                    My Travel Plans
                </span>
                <h1 class="text-3xl md:text-4xl font-bold text-gray-800 mb-4">My Trips</h1>
                <p class="text-lg text-gray-600 mb-8">Manage and explore your personalized travel itineraries created with Travaiq.</p>
            </div>
        </section>

        <!-- Tabs -->
        <div class="border-b border-gray-200 mb-8">
            <div class="flex space-x-4">
                <button class="px-6 py-3 text-sm font-medium text-primary border-b-2 border-primary focus:outline-none">
                    My Trips
                </button>
                {{-- <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-primary hover:border-primary-light border-b-2 border-transparent focus:outline-none transition duration-300">
                    Saved Destinations
                </button>
                <button class="px-6 py-3 text-sm font-medium text-gray-500 hover:text-primary hover:border-primary-light border-b-2 border-transparent focus:outline-none transition duration-300">
                    Recently Viewed
                </button> --}}
            </div>
        </div>

        <!-- Create New Trip Button -->
        <div class="mb-8 flex justify-between items-center">
            <div class="text-gray-600">Showing 3 trips</div>
            <a href="{{route('createPlan')}}" class="inline-flex items-center px-6 py-3 rounded-full bg-gradient-to-r from-primary to-primary-light text-white font-medium shadow-md hover:shadow-lg transition duration-300 transform hover:-translate-y-1">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create New Trip
            </a>
        </div>

        <!-- Trip Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @foreach ($trips as $trip)
            <!-- Trip Card 1 -->
            <div class="group bg-white rounded-xl overflow-hidden shadow-md transition-all duration-500 hover:shadow-xl hover:-translate-y-2">
                <a href="{{route('trips.show.reference',  $trip->reference_code)}}">
                <div class="relative">
                      @if($trip->google_place_image)
                                            <img src="{{ $trip->google_place_image }}" alt="{{ $trip->location }}"
                                                class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105">
                                        @else
                                            <img src="https://img.freepik.com/premium-photo/road-amidst-field-against-sky-sunset_1048944-19856354.jpg?w=1060" alt="{{ $tripDetails['location'] }}"
                                                class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-105">
                                        @endif

                   
                    {{-- <div class="absolute top-4 right-4 bg-white rounded-full p-2 shadow-md">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h.01M12 12h.01M19 12h.01M6 12a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0zm7 0a1 1 0 11-2 0 1 1 0 012 0z"></path>
                        </svg>
                    </div> --}}
                </div>
                </a>
                <div class="p-5">
                    <div class="flex items-center mb-3">
                        <div class="bg-primary/10 rounded-full p-1.5">
                            <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm text-gray-600">{{ $trip->duration }} days</span>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $trip->duration }} days trip in {{ $trip->location }}</h3>
                    <div class="flex items-center text-sm text-gray-600 mb-4">
                        <svg class="w-4 h-4 text-primary mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                       {{ $trip->location }}
                    </div>
                    <div class="flex justify-between">
                        <div class="flex items-center text-gray-500 text-sm">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $trip->created_at }}
                        </div>
                        <a href="{{route('trips.show.reference',  $trip->reference_code)}}" class="text-primary font-medium text-sm hover:underline">View details</a>
                    </div>
                </div>
            </div>
            @endforeach
            

            <!-- "Create New Trip" Card -->
            <div class="bg-white rounded-xl overflow-hidden shadow-md border-2 border-dashed border-gray-200 flex items-center justify-center h-72 transition-all duration-300 hover:border-primary/30 hover:shadow-md group">
                <a href="{{route('createPlan')}}" class="flex flex-col items-center text-gray-400 group-hover:text-primary transition-colors duration-300 p-6 text-center">
                    <div class="w-16 h-16 rounded-full bg-gray-100 group-hover:bg-primary/10 flex items-center justify-center mb-4 transition-colors duration-300">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                    </div>
                    <span class="font-medium text-lg">Create New Trip</span>
                    <p class="mt-2 text-sm">Let AI plan your next adventure</p>
                </a>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-12 flex justify-center">
            <nav class="flex items-center space-x-2">
                {{-- Previous Page Link --}}
                @if ($trips->onFirstPage())
                    <span class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 cursor-not-allowed">
                        <span class="sr-only">Previous</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </span>
                @else
                    <a href="{{ $trips->previousPageUrl() }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-primary transition-colors duration-300">
                        <span class="sr-only">Previous</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                    </a>
                @endif

                {{-- Page Number Links --}}
                @for ($page = 1; $page <= $trips->lastPage(); $page++)
                    @if ($page == $trips->currentPage())
                        <span class="px-3 py-2 rounded-md text-sm font-medium bg-primary text-white">{{ $page }}</span>
                    @elseif ($page == 1 || $page == $trips->lastPage() || ($page >= $trips->currentPage() - 1 && $page <= $trips->currentPage() + 1))
                        <a href="{{ $trips->url($page) }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-700 hover:bg-primary/10 transition-colors duration-300">{{ $page }}</a>
                    @elseif ($page == 2 && $trips->currentPage() > 4)
                        <span class="px-3 py-2 text-gray-600">...</span>
                    @elseif ($page == $trips->lastPage() - 1 && $trips->currentPage() < $trips->lastPage() - 3)
                        <span class="px-3 py-2 text-gray-600">...</span>
                    @endif
                @endfor

                {{-- Next Page Link --}}
                @if ($trips->hasMorePages())
                    <a href="{{ $trips->nextPageUrl() }}" class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 hover:text-primary transition-colors duration-300">
                        <span class="sr-only">Next</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </a>
                @else
                    <span class="px-3 py-2 rounded-md text-sm font-medium text-gray-500 cursor-not-allowed">
                        <span class="sr-only">Next</span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </span>
                @endif
            </nav>
        </div>
    </main>

    <!-- Footer -->
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
</body>
</html>