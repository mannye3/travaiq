@extends('layouts.app')

@section('content')

        <!-- Main Content -->
        <main class="flex-1 min-w-0 max-w-full">
            <div class="max-w-screen-xl mx-auto">
                <!-- Hero Section -->
                <section class="mb-12 lg:mb-24 pb-10 pt-10 sm:pt-16 bg-zinc-950 sm:bg-white" id="hero">
                    <div class="container mx-auto text-center px-4">
                       <h1
    class="max-w-[60rem] mx-auto text-[42px] sm:text-[64px] font-bold mb-4 sm:mb-6 flex justify-center flex-wrap leading-[1.3] text-white sm:text-zinc-900">
    Plan Smart. Travel Better with <span class="text-[#0b9fcd]">Travaiq</span>
</h1>

                        {{-- <p
                            class="max-w-[70rem] mx-auto sm:leading-8 text-base sm:text-xl mb-8 sm:mb-12 whitespace-break-spaces text-white sm:text-gray-600 sm:px-4 md:px-36 flex justify-center flex-wrap">
                            Your personal trip planner and travel curator, creating custom itineraries tailored to your
                            interests and budget.
                        </p> --}}
                        <a href="{{route('createPlan')}}" class="start-button" aria-label="Start planning your trip for free">Start Planning for Free</a>

                    </div>
                    <div class="mt-12 w-full hidden sm:block mb-0 sm:mb-[-4rem] sm:px-8">
                        {{-- <img src="https://wonderplan.ai/_app/immutable/assets/hero.BdTITtUy.webp" alt="planner"
                            class="mx-auto w-full max-w-screen-xl shadow-[0_0_20px_0_rbga(0_0_0_4%)] border border-gray-200 rounded-xl"
                            width="100%" height="auto"> --}}

                            <img src="{{ asset('tripLand.png') }}" alt="planner"
                            class="mx-auto w-full max-w-screen-xl shadow-[0_0_20px_0_rbga(0_0_0_4%)] border border-gray-200 rounded-xl"
                            width="100%" height="auto">
                    </div>
                </section>

                <!-- Features Section -->
                <section class="mt-20 mb-12 sm:mb-20">
                    <div class="text-start px-4 lg:px-8">
                        <h2 class="text-4xl lg:text-5xl font-bold mb-12">Everything you need for planning your trip
                        </h2>

                        <div class="feature feature-1">
                            <div class="content">
                                <h4 class="feature-title">Adjust your itinerary as needed</h4>
                                <p class="feature-description">Seamlessly manage your itinerary all in one page with
                                    Travaiq - from reconfiguring the order of your plans, introducing new
                                    destinations to your itinerary, or even discarding plans as needed.</p>
                            </div>
                            <img src="{{ asset('tripland2.png') }}"
                                alt="planner" loading="lazy" width="auto" height="auto">
                        </div>

                        <!-- <div class="flex flex-col lg:flex-row lg:justify-between gap-8 my-8">
                            <div class="w-full feature feature-2">
                                <div class="content">
                                    <h4 class="feature-title">AI Travel</h4>
                                    <p class="feature-description">Generate richly personalized accommodation recommendations. Driven by your unique preferences and tastes, we curate an array of lodging options which not only meet, but exceed your needs, providing you with the comfort and convenience you deserve on your journey.</p>
                                </div>
                                <img src="https://Travaiq.ai/_app/immutable/assets/feature-2.vxD_86LP.webp" alt="ai" loading="lazy" width="auto" height="auto">
                            </div>
                            
                            <div class="w-full feature feature-3">
                                <div class="content">
                                    <h4 class="feature-title">Offline Access</h4>
                                    <p class="feature-description">Enjoy the convenience of offline accessibility - with the option to download and save your plans as a PDF, you can always have your information at hand, no matter where you are.</p>
                                </div>
                                <img src="https://placehold.co/500x300/e4e4e7/a3a3a3?text=Offline+Access" alt="offline" loading="lazy" width="auto" height="auto">
                            </div>
                        </div> -->

                        <div class="feature feature-4">
                            <div class="content">
                               <h4 class="feature-title">All Your Plans, One Place</h4>
<p class="feature-description">From personalized trips to saved itineraries, everything is neatly organized on a single, easy-to-access page.</p>

                            </div>
                            <img src="{{ asset('tripland3.png') }}"
                                alt="one place" loading="lazy" width="auto" height="auto">
                        </div>
                    </div>
                </section>

                <!-- Call to Action Section -->
                <section class="px-4 sm:px-8 mb-12 sm:mb-20">
                    <div id="try-it">
                        <div
                            class="text-center max-w-screen-sm mx-auto flex flex-col gap-y-8 justify-center items-center">
                            <h2 class="text-2xl sm:text-4xl font-semibold sm:leading-[3rem] px-4 sm:px-0">
    Say goodbye to manual planning — let Travaiq craft your journey for free.
</h2>
<a href="{{route('createPlan')}}" class="start-button !bg-white !text-zinc-900" aria-label="Start Planning">
    Start Planning
</a>

                        </div>
                    </div>
                </section>

                <!-- FAQs Section -->
               <section class="px-4 sm:px-8 mb-12 sm:mb-20">
    <div class="mx-auto">
        <div class="grid md:grid-cols-5 gap-10">
            <div class="md:col-span-2">
                <div class="max-w-xs">
                    <h2 class="text-2xl font-bold md:text-5xl md:leading-tight">FAQs</h2>
                </div>
            </div>

            <div class="md:col-span-3">
                <div class="divide-y divide-gray-200">

                    <!-- FAQ Item 1 -->
                    <div class="pt-6 pb-3">
                        <details class="group">
                            <summary class="text-lg font-semibold cursor-pointer p-4 border-b hover:bg-gray-50">What is Travaiq?</summary>
                            <p class="px-4 py-2 text-gray-600">
                                Travaiq is your smart travel companion. It helps you effortlessly plan personalized trips using AI—anytime, anywhere. Think of it as having a virtual travel assistant that crafts your itinerary in seconds.
                            </p>
                        </details>
                    </div>

                    <!-- FAQ Item 2 -->
                    <div class="pt-6 pb-3">
                        <details class="group">
                            <summary class="text-lg font-semibold cursor-pointer p-4 border-b hover:bg-gray-50">Is Travaiq free to use?</summary>
                            <p class="px-4 py-2 text-gray-600">
                                Yes, Travaiq is completely free (for now 😄). We want everyone to enjoy seamless travel planning without worrying about costs.
                            </p>
                        </details>
                    </div>

                    <!-- FAQ Item 3 -->
                    <div class="pt-6 pb-3">
                        <details class="group">
                            <summary class="text-lg font-semibold cursor-pointer p-4 border-b hover:bg-gray-50">Where can I get support?</summary>
                            <p class="px-4 py-2 text-gray-600">
                                Need help? Reach out to us at <a href="mailto:support@Travaiq.com" class="underline font-medium text-zinc-900">support@Travaiq.com</a> — we’re happy to assist with your travel planning needs.
                            </p>
                        </details>
                    </div>

                    <!-- FAQ Item 4 -->
                    <div class="pt-6 pb-3">
                        <details class="group">
                            <summary class="text-lg font-semibold cursor-pointer p-4 border-b hover:bg-gray-50">Do I need to create an account?</summary>
                            <p class="px-4 py-2 text-gray-600">
                                No account is required to start planning. Just visit the site and begin your journey. However, creating an account lets you save and manage your itineraries more easily.
                            </p>
                        </details>
                    </div>

                    <!-- FAQ Item 5 -->
                    <div class="pt-6 pb-3">
                        <details class="group">
                            <summary class="text-lg font-semibold cursor-pointer p-4 border-b hover:bg-gray-50">Can I share my itinerary?</summary>
                            <p class="px-4 py-2 text-gray-600">
                                Absolutely! You can easily share your generated itinerary with friends or travel companions via a shareable link.
                            </p>
                        </details>
                    </div>

                    <!-- FAQ Item 6 -->
                    <div class="pt-6 pb-3">
                        <details class="group">
                            <summary class="text-lg font-semibold cursor-pointer p-4 border-b hover:bg-gray-50">Does Travaiq support international travel?</summary>
                            <p class="px-4 py-2 text-gray-600">
                                Yes, Travaiq supports planning for destinations all around the globe. Just enter where you want to go, and our AI will handle the rest.
                            </p>
                        </details>
                    </div>

                </div>
            </div>
        </div>
    </div>
</section>

            </div>
        </main>
@endsection