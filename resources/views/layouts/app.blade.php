<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Travaiq - Best AI Trip Planner, Free AI Travel Planner</title>
    <link rel="shortcut icon" href="{{ asset('logo.png') }}">
    <meta name="description"
        content="Streamline your travels using Travaiq's Trip Planner AI. Quickly search for hotels, and get personalized trip advice, all powered by AI.">
    <meta name="keywords"
        content="build ai trip planner, travel planner ai, trip planner ai, ai road trip planner, plan you journey, plan my trip, travel planning ai">

    <!-- Open Graph / Facebook -->
    <meta property="og:site_name" content="Travaiq">
    <meta property="og:locale" content="en_US">
    <meta property="og:url" content="https://Travaiq.ai">
    <meta property="og:type" content="website">
    <meta property="og:title" content="Travaiq - Best AI Trip Planner, Free AI Travel Planner">
    <meta property="og:description"
        content="Streamline your travels using Travaiq's Trip Planner AI. Quickly search for hotels, and get personalized trip advice, all powered by AI.">

    <!-- Twitter -->
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="Travaiq - Best AI Trip Planner, Free AI Travel Planner">
    <meta name="twitter:description"
        content="Streamline your travels using Travaiq's Trip Planner AI. Quickly search for hotels, and get personalized trip advice, all powered by AI.">
    <meta name="twitter:creator" content="@Travaiqai">
    <meta name="twitter:site" content="@Travaiqai">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        /* Custom styles */
        /* Base styles */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Responsive typography */
        h1 {
            font-size: clamp(2rem, 5vw, 4rem);
            line-height: 1.2;
        }

        h2 {
            font-size: clamp(1.5rem, 4vw, 3rem);
            line-height: 1.3;
        }

        p {
            font-size: clamp(1rem, 2vw, 1.125rem);
            line-height: 1.6;
        }

        /* Feature sections */
        .feature {
            background-color: #f9fafb;
            border-radius: 12px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            margin-bottom: 1.5rem;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .feature:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1);
        }

        @media (min-width: 768px) {
            .feature {
                flex-direction: row;
                align-items: center;
                gap: 3rem;
                padding: 2rem;
            }
        }

        .feature-title {
            font-size: clamp(1.25rem, 3vw, 1.5rem);
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .feature-description {
            color: #4b5563;
            margin-bottom: 1.5rem;
            font-size: clamp(0.875rem, 2vw, 1rem);
        }

        .feature img {
            border-radius: 8px;
            width: 100%;
            max-width: 500px;
            height: auto;
            object-fit: cover;
        }

        .feature-content {
            flex: 1;
        }

        @media (max-width: 767px) {
            .feature img {
                margin-top: 1rem;
                max-height: 250px;
            }
        }

        /* Buttons */
        .start-button {
            background-color: #0b9fcd;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 0.5rem;
            font-weight: 600;
            display: inline-block;
            transition: all 0.3s;
            text-align: center;
            width: fit-content;
            min-width: 180px;
            box-shadow: 0 4px 6px rgba(245, 101, 81, 0.25);
        }

        .start-button:hover {
            opacity: 0.9;
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(245, 101, 81, 0.3);
        }

        @media (max-width: 640px) {
            .start-button {
                padding: 0.625rem 1.25rem;
                min-width: 160px;
                font-size: 0.9375rem;
            }
        }

        /* CTA section */
        #try-it {
            background-color: #0b9fcd;
            padding: 2rem 1rem;
            border-radius: 12px;
            color: white;
        }

        @media (min-width: 640px) {
            #try-it {
                padding: 3rem 2rem;
            }
        }

        /* Footer styles */
        .footer .title {
            font-weight: 600;
            margin-bottom: 0.75rem;
            font-size: 1.125rem;
        }

        .footer .link {
            color: #6b7280;
            transition: color 0.2s, transform 0.2s;
            display: inline-block;
            margin-bottom: 0.5rem;
            font-size: 0.9375rem;
        }

        .footer .link:hover {
            color: #111827;
            transform: translateX(3px);
        }

        /* Navigation */
        .nav-button {
            padding: 0.5rem 1rem;
            color: #111827;
            font-weight: 500;
            transition: color 0.2s, transform 0.2s;
            position: relative;
        }

        .nav-button:hover {
            color: #0b9fcd;
        }

        .nav-button::after {
            content: '';
            position: absolute;
            width: 0;
            height: 2px;
            bottom: 0;
            left: 50%;
            background-color: #0b9fcd;
            transition: width 0.3s ease, left 0.3s ease;
        }

        .nav-button:hover::after {
            width: 70%;
            left: 15%;
        }

        /* Mobile menu styles */
        #mobile-menu {
            transition: all 0.3s ease;
        }

        /* FAQs */
        .faq-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
        }

        .faq-content.active {
            max-height: 500px;
            padding-top: 0.75rem;
        }

        /* Hero section */
        @media (max-width: 639px) {
            #hero {
                padding-left: 1rem;
                padding-right: 1rem;
            }
        }
    </style>
</head>

<body class="bg-white">
    <div class="flex flex-col min-h-screen">
        <!-- Header/Navigation -->
        <header
            class="relative flex flex-wrap sm:justify-start sm:flex-nowrap z-50 py-4 w-full border-b border-b-gray-200 bg-white">
            <nav class="container w-full max-w-full px-4 sm:flex sm:items-center sm:justify-between"
                aria-label="Global">
                <div class="flex items-center justify-between">
                    <a href="{{ url('/') }}" aria-label="Travaiq Logo"
                        class="flex items-center text-xl font-semibold">
                        <!-- Logo icon -->
                        <img src="{{ asset('logo.png') }}" alt="Travaiq logo" class="h-9 w-6 mr-[6px]" />

                        <!-- SVG text -->
                        <svg class="w-24 sm:w-28" width="160" height="50" viewBox="0 0 160 50" fill="none"
                            xmlns="http://www.w3.org/2000/svg">
                            <text x="0" y="35" font-family="Arial, sans-serif" font-size="35"
                                fill="#27272a">Travaiq</text>
                        </svg>
                    </a>


                    <div class="sm:hidden">
                        <button type="button"
                            class="hover:scale-110 transition-all duration-300 p-2 rounded-md focus:outline-none focus:ring-2 focus:ring-gray-300"
                            data-toggle="mobile-menu" aria-label="Toggle navigation">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"
                                xmlns="http://www.w3.org/2000/svg" class="w-6 h-6">
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.5 5C3.23478 5 2.98043 5.10536 2.79289 5.29289C2.60536 5.48043 2.5 5.73478 2.5 6C2.5 6.26522 2.60536 6.51957 2.79289 6.70711C2.98043 6.89464 3.23478 7 3.5 7H20.5C20.7652 7 21.0196 6.89464 21.2071 6.70711C21.3946 6.51957 21.5 6.26522 21.5 6C21.5 5.73478 21.3946 5.48043 21.2071 5.29289C21.0196 5.10536 20.7652 5 20.5 5H3.5Z"
                                    fill="#1f2937"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.5 11C3.23478 11 2.98043 11.1054 2.79289 11.2929C2.60536 11.4804 2.5 11.7348 2.5 12C2.5 12.2652 2.60536 12.5196 2.79289 12.7071C2.98043 12.8946 3.23478 13 3.5 13H20.5C20.7652 13 21.0196 12.8946 21.2071 12.7071C21.3946 12.5196 21.5 12.2652 21.5 12C21.5 11.7348 21.3946 11.4804 21.2071 11.2929C21.0196 11.1054 20.7652 11 20.5 11H3.5Z"
                                    fill="#1f2937"></path>
                                <path fill-rule="evenodd" clip-rule="evenodd"
                                    d="M3.5 17C3.23478 17 2.98043 17.1054 2.79289 17.2929C2.60536 17.4804 2.5 17.7348 2.5 18C2.5 18.2652 2.60536 18.5196 2.79289 18.7071C2.98043 18.8946 3.23478 19 3.5 19H20.5C20.7652 19 21.0196 18.8946 21.2071 18.7071C21.3946 18.5196 21.5 18.2652 21.5 18C21.5 17.7348 21.3946 17.4804 21.2071 17.2929C21.0196 17.1054 20.7652 17 20.5 17H3.5Z"
                                    fill="#1f2937"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="hidden sm:flex justify-end sm:flex-row sm:items-center mt-0 text-zinc-900 w-full">
                    <div class="flex-1 flex justify-center">
                        <a class="nav-button" href="#">Blog</a>
                        <a class="nav-button" href="{{ route('createPlan') }}">Trip Planner</a>
                        @if (Auth::check())
                            <a class="nav-button" href="{{ route('my.trips') }}">My Trips</a>
                        @endif
                        {{-- <a class="nav-button hidden md:block" href="deals.html">Deals</a> --}}
                    </div>
                    @if (Auth::check())
                        <div class="relative">
                            <!-- User Profile Dropdown -->
                            <div class="flex items-center">
                                <button id="user-menu-button" class="flex items-center focus:outline-none">
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200"
                                        src="{{ Auth::user()->picture ?? asset('images/default-avatar.png') }}"
                                        alt="User profile">

                                    <svg class="ml-1 h-5 w-5 text-gray-500" xmlns="http://www.w3.org/2000/svg"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                            clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </div>

                            <!-- Dropdown menu -->
                            <div id="user-dropdown"
                                class="hidden absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50">
                                <a href="{{ route('my.trips') }}"
                                    class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">My
                                    Trips</a>


                                <form action="{{ route('logout') }}" method="POST" class="w-full">
                                    @csrf
                                    <button type="submit"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        Logout
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('google.redirect') }}"
                            class="py-3 px-4 text-base font-medium bg-zinc-900 text-white rounded-md hover:bg-zinc-800 transition-colors">
                            Sign In
                        </a>
                    @endif

                </div>

                <!-- Mobile Menu (hidden by default) -->
                <div id="mobile-menu" class="hidden sm:hidden mt-4 w-full">
                    <div class="flex flex-col space-y-2 mt-2 px-2 pb-4 bg-white rounded-lg shadow-lg">
                        <a href="#"
                            class="py-3 px-4 text-base font-medium text-gray-900 hover:bg-gray-100 hover:text-F56551 rounded-md transition-colors">Blog</a>
                        <a href="{{ route('createPlan') }}"
                            class="py-3 px-4 text-base font-medium text-gray-900 hover:bg-gray-100 hover:text-F56551 rounded-md transition-colors">Trip
                            Planner</a>
                        {{-- <a href="deals.html"
                            class="py-3 px-4 text-base font-medium text-gray-900 hover:bg-gray-100 hover:text-F56551 rounded-md transition-colors">Deals</a> --}}
                        <div class="pt-2 pb-1 px-4">
                            @if (Auth::check())
                                <div class="flex items-center py-3 px-4">
                                    <img class="h-10 w-10 rounded-full object-cover border-2 border-gray-200"
                                        src="{{ Auth::user()->picture ?? asset('images/default-avatar.png') }}"
                                        alt="User profile">
                                    <div class="ml-3">
                                        <a href="{{ route('my.trips') }}"
                                            class="block text-base font-medium text-gray-900 hover:text-F56551">My
                                            Trips</a>
                                        {{-- <a href="{{route('createPlan')}}"
                            class="py-3 px-4 text-base font-medium text-gray-900 hover:bg-gray-100 hover:text-F56551 rounded-md transition-colors">Trip
                            Planner</a> --}}
                                        <form action="{{ route('logout') }}" method="POST" class="mt-1">
                                            @csrf
                                            <button type="submit"
                                                class="text-base font-medium text-gray-900 hover:text-F56551">
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('google.redirect') }}"
                                    class="w-full py-3 px-4 text-base font-medium bg-zinc-900 text-white rounded-md hover:bg-zinc-800 transition-colors">
                                    Sign In
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </nav>
        </header>



        @yield('content')










        <!-- Footer -->
        <hr class="border-gray-200 w-full">
        <footer class="container footer mx-auto py-4 sm:py-12 flex flex-col sm:flex-row max-w-7xl px-4">
            <div class="flex-[40%] mb-8 sm:mb-0">
                <a href="" aria-label="Travaiq Logo" class="flex items-center text-xl font-semibold">
                    <!-- Logo icon -->
                    <img src="{{ asset('logo.png') }}" alt="Travaiq logo" class="h-9 w-6 mr-[6px]" />

                    <!-- SVG text -->
                    <svg class="w-24 sm:w-28" width="160" height="50" viewBox="0 0 160 50" fill="none"
                        xmlns="http://www.w3.org/2000/svg">
                        <text x="0" y="35" font-family="Arial, sans-serif" font-size="35"
                            fill="#27272a">Travaiq</text>
                    </svg>
                </a>

            </div>

            <div class="flex-[60%] flex flex-col sm:flex-row gap-4">
                <div class="flex-[50%] text-sm flex flex-col gap-y-3 mb-8 lg:mb-0">
                    <div class="title">Get started</div>
                    <a class="link" href="#" rel="noreferrer">Planning a trip to Portugal</a>
                    <a class="link" href="#" rel="noreferrer">Planning a trip to Japan</a>
                    <a class="link" href="#" rel="noreferrer">Planning a trip to Korea</a>
                    <a class="link" href="#" rel="noreferrer">Planning a trip to Maldives</a>
                </div>

                <div class="flex-[50%] text-sm mb-8 sm:mb-0 flex flex-col gap-y-3">
                    <div class="title">Resources</div>
                    <a href="mailto:support@Travaiq.com" class="link">Contact</a>
                    <a href="#" class="link">Blog</a>
                    <a href="https://instagram.com/Travaiq" target="_blank" rel="noreferrer noopener"
                        class="link">Instagram</a>
                    <a class="link" href="privacy-policy.html" rel="noreferrer">Privacy Policy</a>
                </div>
            </div>
        </footer>

        <div class="text-center my-8">
            <p class="text-xs text-gray-500 mb-2">© 2025 Travaiq.</p>
        </div>
    </div>

    <!-- Simple JavaScript for interactivity -->
    <script>
        // Mobile menu toggle
        document.querySelector('[data-toggle="mobile-menu"]').addEventListener('click', function() {
            const mobileMenu = document.getElementById('mobile-menu');
            mobileMenu.classList.toggle('hidden');
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
            const currentValue = parseInt(input.value);
            if (currentValue < 5) {
                input.value = currentValue + 1;
            }
        }

        function decrementDays() {
            const input = document.getElementById("daysInput");
            const currentValue = parseInt(input.value);
            if (currentValue > 1) {
                input.value = currentValue - 1;
            }
        }

        // Add input validation to prevent manual entry of numbers > 5
        document.getElementById("daysInput").addEventListener("input", function(e) {
            const value = parseInt(e.target.value);
            if (value > 5) {
                e.target.value = 5;
            } else if (value < 1) {
                e.target.value = 1;
            }
        });

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
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form'); // adjust selector if needed
            const submitBtn = document.getElementById('submitBtn');
            const spinner = document.getElementById('spinner');
            const submitText = document.getElementById('submitText');
            const overlay = document.getElementById('overlay');
            const budgetInput = document.getElementById('budgetInput');
            const companionInput = document.getElementById('companionInput');
            const activitiesInput = document.getElementById('activitiesInput');

            // Enable submit button initially
            submitBtn.disabled = false;

            form.addEventListener('submit', function(e) {
                // Validate the form fields
                if (!validateForm()) {
                    e.preventDefault(); // Prevent form submission if validation fails
                    return false;
                }

                // If form is valid, show spinner and change button text
                submitBtn.disabled = true;
                submitText.textContent = 'Submitting...';
                spinner.classList.remove('hidden');
                overlay.classList.remove('hidden');
            });

            function validateForm() {
                const budget = budgetInput.value;
                const companion = companionInput.value;
                const activities = activitiesInput.value;

                if (!budget || !companion || !activities) {
                    alert("Please select your budget, travel companion, and at least one activity.");
                    return false;
                }
                return true;
            }
        });
    </script>





    <script>
        // Initialize Google Autocomplete
        function initAutocomplete() {
            var input = document.getElementById('location');
            var autocomplete = new google.maps.places.Autocomplete(input);

            // When a place is selected, update the hidden fields with location details
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                document.getElementById('place_id').value = place.place_id;
                document.getElementById('formatted_address').value = place.formatted_address;
                document.getElementById('latitude').value = place.geometry.location.lat();
                document.getElementById('longitude').value = place.geometry.location.lng();
            });
        }
    </script>





    <script>
        let currentPosition = 0;
        const slider = document.getElementById('hotels-slider');
        const container = document.getElementById('hotels-container');
        const slideWidth = 100; // Percentage width of each slide
        const totalSlides = slider.children.length;
        const visibleSlides = 3;
        const maxPosition = Math.max(0, totalSlides - visibleSlides);

        function scrollHotels(direction) {
            if (direction === 'next' && currentPosition < maxPosition) {
                currentPosition++;
            } else if (direction === 'prev' && currentPosition > 0) {
                currentPosition--;
            }

            // Update slider position
            const translateX = -(currentPosition * (100 / visibleSlides));
            slider.style.transform = `translateX(${translateX}%)`;

            // Update button states
            document.getElementById('scroll-left').disabled = currentPosition === 0;
            document.getElementById('scroll-right').disabled = currentPosition >= maxPosition;
        }

        // Initialize button states
        document.getElementById('scroll-left').disabled = true;
        document.getElementById('scroll-right').disabled = maxPosition <= 0;
    </script>
    
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-KBCRTSETD9"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-KBCRTSETD9');
</script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA0Oy90FUKjIHNwASRdh1Nv-1v8Sqr1Bf4&libraries=places&callback=initAutocomplete"
        async defer></script>
