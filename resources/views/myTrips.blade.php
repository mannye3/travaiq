@extends('layouts.app')

@section('content')

        <!-- Main Content -->
        <main class="flex-1 min-w-0 max-w-full">
            <div class="max-w-screen-xl mx-auto">
                <!-- Hero Section -->
                <section class="mb-12 lg:mb-24 pb-10 pt-10 " id="hero">
                    <!-- Trips Section -->
                    <div class="px-4 sm:px-6 lg:px-8">
                        <h1 class="text-4xl font-bold text-gray-900 mb-8">Trips</h1>

                        <!-- Tabs -->
                        <div class="border-b border-gray-200 mb-8">
                            <div class="flex space-x-4">
                                <button
                                    class="px-6 py-2 text-sm font-medium text-gray-900 border-b-2 border-gray-900 focus:outline-none">
                                    My Trips
                                </button>
                                {{-- <button
                                    class="px-6 py-2 text-sm font-medium text-gray-500 hover:text-gray-700 focus:outline-none">
                                    Collections
                                </button> --}}
                            </div>
                        </div>

                        <!-- Trip Cards Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach ($trips as $trip)
                                <!-- Trip Card 1 -->
                                <a href="{{route('trips.show.reference',  $trip->reference_code)}}">
                                <div class="bg-white rounded-lg overflow-hidden shadow-md">
                                    <div class="relative">
                                         @if($trip->google_place_image)
                                            <img src="{{ $trip->google_place_image }}" alt="{{ $trip->location }}"
                                                class="w-full h-48 object-cover">
                                        @else
                                            <img src="https://img.freepik.com/premium-photo/road-amidst-field-against-sky-sunset_1048944-19856354.jpg?w=1060" alt="{{ $tripDetails['location'] }}"
                                               class="w-full h-48 object-cover">
                                        @endif


                                       
                                    </div>
                                    <div class="p-4">
                                     
                                        <h3 class="text-lg font-semibold text-gray-900 mb-2">{{ $trip->duration }}
                                            days trip in {{ $trip->location }}</h3>
                                    </div>
                                </div>
                                </a>

                                
                            @endforeach
                        </div>
                    </div>
                </section>


            </div>
        </main>

@endsection