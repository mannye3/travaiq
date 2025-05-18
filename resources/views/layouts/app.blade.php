<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Travaiq - AI-Powered Travel Planning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: {
                            light: '#9575cd',
                            DEFAULT: '#673AB7',
                            dark: '#5e35b1',
                        },
                        accent: {
                            light: '#FFECB3',
                            DEFAULT: '#FFC107',
                            dark: '#FFA000',
                        }
                    },
                    animation: {
                        'float': 'float 6s ease-in-out infinite',
                        'float-slow': 'float 8s ease-in-out infinite',
                        'pulse-slow': 'pulse 4s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'spin-slow': 'spin 8s linear infinite',
                    },
                    keyframes: {
                        float: {
                          '0%, 100%': { transform: 'translateY(0)' },
                          '50%': { transform: 'translateY(-10px)' },
                        }
                    },
                    backgroundImage: {
                        'hero-pattern': "url('data:image/svg+xml,%3Csvg width=\"40\" height=\"40\" viewBox=\"0 0 40 40\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cg fill=\"%239C92AC\" fill-opacity=\"0.05\" fill-rule=\"evenodd\"%3E%3Cpath d=\"M0 40L40 0H20L0 20M40 40V20L20 40\"/%3E%3C/g%3E%3C/svg%3E')",
                    }
                }
            }
        }
    </script>
    <style>
        .text-shadow {
            text-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        
        .blob {
            animation: blob 7s infinite;
            border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%;
        }
        
        @keyframes blob {
            0% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; }
            25% { border-radius: 45% 55% 65% 35% / 40% 60% 40% 60%; }
            50% { border-radius: 50% 50% 40% 60% / 35% 65% 35% 65%; }
            75% { border-radius: 60% 40% 50% 50% / 55% 45% 55% 45%; }
            100% { border-radius: 42% 58% 70% 30% / 45% 45% 55% 55%; }
        }

        .marquee {
            animation: marquee 35s linear infinite;
        }
        
        @keyframes marquee {
            0% { transform: translateX(0); }
            100% { transform: translateX(-50%); }
        }
    </style>
</head>
<body class="font-sans text-gray-800 bg-gradient-to-b from-white to-indigo-50 overflow-x-hidden">
    <!-- Background Elements -->
    <div class="fixed top-1/4 -left-32 w-64 h-64 bg-primary rounded-full opacity-10 blur-3xl"></div>
    <div class="fixed bottom-1/4 -right-32 w-64 h-64 bg-accent rounded-full opacity-10 blur-3xl"></div>
    
    <!-- Header -->
    <header class="sticky top-0 z-50 bg-white backdrop-blur-lg bg-opacity-90 shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <nav class="flex flex-wrap items-center justify-between">
                <a href="{{url('/')}}" class="flex items-center text-primary font-bold text-xl">
                    {{-- <span class="mr-2 inline-block animate-float">✈</span> --}}
                    <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary to-primary-light">Travaiq</span>
                </a>
                
                <!-- Mobile menu button -->
                <div class="block lg:hidden">
                    <button id="menu-toggle" class="flex items-center px-3 py-2 border rounded text-primary border-primary hover:text-primary-dark hover:border-primary-dark transition duration-300">
                        <svg class="fill-current h-5 w-5" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M0 3h20v2H0V3zm0 6h20v2H0V9zm0 6h20v2H0v-2z"/>
                        </svg>
                    </button>
                </div>
                
                <div id="menu" class="hidden w-full lg:flex lg:items-center lg:w-auto">
                    <div class="text-sm lg:flex-grow lg:flex lg:items-center">
                        {{-- <a href="#" class="block mt-4 lg:inline-block lg:mt-0 text-gray-700 hover:text-primary mr-8 transition-all duration-300 hover:scale-105">
                            Destinations
                        </a> --}}
                        <a href="#" class="block mt-4 lg:inline-block lg:mt-0 text-gray-700 hover:text-primary mr-8 transition-all duration-300 hover:scale-105">
                           Blogs
                        </a>
                        <a href="{{ route('createPlan') }}" class="block mt-4 lg:inline-block lg:mt-0 text-gray-700 hover:text-primary mr-8 transition-all duration-300 hover:scale-105">
                            Trip Planner
                        </a>
                         @if (Auth::check())
                        {{-- <a href="{{ route('my.trips') }}" class="block mt-4 lg:inline-block lg:mt-0 text-gray-700 hover:text-primary mr-8 transition-all duration-300 hover:scale-105">
                            My Trips
                        </a> --}}
                        @endif
                    </div>
                    <div class="flex flex-col lg:flex-row items-start lg:items-center mt-4 lg:mt-0">
                      
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
                                class="hidden w-full lg:w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 z-50 lg:absolute lg:right-0 lg:mt-2 mt-2 static lg:static">
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
                            class="block px-6 py-2 rounded-full bg-gradient-to-r from-primary to-primary-light hover:from-primary-dark hover:to-primary text-white font-medium shadow-md hover:shadow-lg transform transition duration-300 hover:-translate-y-1">
                            Sign In
                        </a>
                    @endif
                    </div>
                </div>
            </nav>
        </div>
    </header>



        @yield('content')










    <!-- Footer -->
    <footer class="bg-gray-900 text-white relative">
        <div class="absolute top-0 inset-x-0 h-1 bg-gradient-to-r from-primary via-accent to-primary-light"></div>
        
        <div class="container mx-auto px-4 pt-16 pb-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-12">
                <div>
                    <div class="flex items-center text-xl font-bold mb-6">
                        <span class="mr-2 animate-float">✈</span>
                        <span class="bg-clip-text text-transparent bg-gradient-to-r from-primary-light to-white">Travaiq</span>
                    </div>
                    <p class="mb-6 text-gray-400 leading-relaxed">Making travel planning smarter and easier with AI-powered recommendations. We help you discover the perfect destinations and experiences tailored to your preferences.</p>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-full flex items-center justify-center transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M16 8.049c0-4.446-3.582-8.05-8-8.05C3.58 0-.002 3.603-.002 8.05c0 4.017 2.926 7.347 6.75 7.951v-5.625h-2.03V8.05H6.75V6.275c0-2.017 1.195-3.131 3.022-3.131.876 0 1.791.157 1.791.157v1.98h-1.009c-.993 0-1.303.621-1.303 1.258v1.51h2.218l-.354 2.326H9.25V16c3.824-.604 6.75-3.934 6.75-7.951z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-full flex items-center justify-center transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M5.026 15c6.038 0 9.341-5.003 9.341-9.334 0-.14 0-.282-.006-.422A6.685 6.685 0 0 0 16 3.542a6.658 6.658 0 0 1-1.889.518 3.301 3.301 0 0 0 1.447-1.817 6.533 6.533 0 0 1-2.087.793A3.286 3.286 0 0 0 7.875 6.03a9.325 9.325 0 0 1-6.767-3.429 3.289 3.289 0 0 0 1.018 4.382A3.323 3.323 0 0 1 .64 6.575v.045a3.288 3.288 0 0 0 2.632 3.218 3.203 3.203 0 0 1-.865.115 3.23 3.23 0 0 1-.614-.057 3.283 3.283 0 0 0 3.067 2.277A6.588 6.588 0 0 1 .78 13.58a6.32 6.32 0 0 1-.78-.045A9.344 9.344 0 0 0 5.026 15z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-full flex items-center justify-center transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M8 0C5.829 0 5.556.01 4.703.048 3.85.088 3.269.222 2.76.42a3.917 3.917 0 0 0-1.417.923A3.927 3.927 0 0 0 .42 2.76C.222 3.268.087 3.85.048 4.7.01 5.555 0 5.827 0 8.001c0 2.172.01 2.444.048 3.297.04.852.174 1.433.372 1.942.205.526.478.972.923 1.417.444.445.89.719 1.416.923.51.198 1.09.333 1.942.372C5.555 15.99 5.827 16 8 16s2.444-.01 3.298-.048c.851-.04 1.434-.174 1.943-.372a3.916 3.916 0 0 0 1.416-.923c.445-.445.718-.891.923-1.417.197-.509.332-1.09.372-1.942C15.99 10.445 16 10.173 16 8s-.01-2.445-.048-3.299c-.04-.851-.175-1.433-.372-1.941a3.926 3.926 0 0 0-.923-1.417A3.911 3.911 0 0 0 13.24.42c-.51-.198-1.092-.333-1.943-.372C10.443.01 10.172 0 7.998 0h.003zm-.717 1.442h.718c2.136 0 2.389.007 3.232.046.78.035 1.204.166 1.486.275.373.145.64.319.92.599.28.28.453.546.598.92.11.281.24.705.275 1.485.039.843.047 1.096.047 3.231s-.008 2.389-.047 3.232c-.035.78-.166 1.203-.275 1.485a2.47 2.47 0 0 1-.599.919c-.28.28-.546.453-.92.598-.28.11-.704.24-1.485.276-.843.038-1.096.047-3.232.047s-2.39-.009-3.233-.047c-.78-.036-1.203-.166-1.485-.276a2.478 2.478 0 0 1-.92-.598 2.48 2.48 0 0 1-.6-.92c-.109-.281-.24-.705-.275-1.485-.038-.843-.046-1.096-.046-3.233 0-2.136.008-2.388.046-3.231.036-.78.166-1.204.276-1.486.145-.373.319-.64.599-.92.28-.28.546-.453.92-.598.282-.11.705-.24 1.485-.276.738-.034 1.024-.044 2.515-.045v.002zm4.988 1.328a.96.96 0 1 0 0 1.92.96.96 0 0 0 0-1.92zm-4.27 1.122a4.109 4.109 0 1 0 0 8.217 4.109 4.109 0 0 0 0-8.217zm0 1.441a2.667 2.667 0 1 1 0 5.334 2.667 2.667 0 0 1 0-5.334z"/>
                            </svg>
                        </a>
                        <a href="#" class="w-10 h-10 bg-gray-800 hover:bg-primary rounded-full flex items-center justify-center transition duration-300 hover:-translate-y-1 hover:shadow-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                                <path d="M0 1.146C0 .513.526 0 1.175 0h13.65C15.474 0 16 .513 16 1.146v13.708c0 .633-.526 1.146-1.175 1.146H1.175C.526 16 0 15.487 0 14.854V1.146zm4.943 12.248V6.169H2.542v7.225h2.401zm-1.2-8.212c.837 0 1.358-.554 1.358-1.248-.015-.709-.52-1.248-1.342-1.248-.822 0-1.359.54-1.359 1.248 0 .694.521 1.248 1.327 1.248h.016zm4.908 8.212V9.359c0-.216.016-.432.08-.586.173-.431.568-.878 1.232-.878.869 0 1.216.662 1.216 1.634v3.865h2.401V9.25c0-2.22-1.184-3.252-2.764-3.252-1.274 0-1.845.7-2.165 1.193v.025h-.016a5.54 5.54 0 0 1 .016-.025V6.169h-2.4c.03.678 0 7.225 0 7.225h2.4z"/>
                            </svg>
                        </a>
                    </div>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6 relative inline-block">
                        Company
                        <div class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-primary"></div>
                    </h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            About Us
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Careers
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Press
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Blog
                        </a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6 relative inline-block">
                        Resources
                        <div class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-primary"></div>
                    </h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Travel Guide
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            FAQs
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Support
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Contact
                        </a></li>
                    </ul>
                </div>
                
                <div>
                    <h3 class="text-lg font-semibold mb-6 relative inline-block">
                        Legal
                        <div class="absolute bottom-0 left-0 w-1/2 h-0.5 bg-primary"></div>
                    </h3>
                    <ul class="space-y-4">
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Privacy Policy
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Terms of Service
                        </a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition duration-300 flex items-center hover:translate-x-1">
                            <svg class="w-4 h-4 mr-2 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            Cookie Policy
                        </a></li>
                    </ul>
                    
                   
                </div>
            </div>
            
            <div class="border-t border-gray-800 pt-8 flex flex-wrap items-center justify-between">
                <p class="text-gray-500 text-sm mb-4 md:mb-0">&copy; 2024 Travaiq. All rights reserved.</p>
                
                <div class="flex items-center space-x-4">
                    <a href="#" class="text-gray-500 hover:text-white transition duration-300 text-sm">Sitemap</a>
                    <a href="#" class="text-gray-500 hover:text-white transition duration-300 text-sm">Accessibility</a>
                    <a href="#" class="text-gray-500 hover:text-white transition duration-300 text-sm">Affiliates</a>
                </div>
            </div>
        </div>
    </footer>

    </body>
</html>
    
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-KBCRTSETD9"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-KBCRTSETD9');
</script>

    <script
        src="https://maps.googleapis.com/maps/api/js?key={{ env('GOOGLE_MAPS_API_KEY') }}&libraries=places&callback=initAutocomplete"
        async defer></script>

        {{-- https://maps.googleapis.com/maps/api/js?
        key=AIzaSyA0Oy90FUKjIHNwASRdh1Nv-1v8Sqr1Bf4&libraries=places&
        callback=initAutocomplete

        https://maps.googleapis.com/maps/api/js?key=AIzaSyDNQQItpPeJl6xu2BeKPk_-YVMTS0Daoqs&v=weekly&loading=async&callback=initMap --}}
        
