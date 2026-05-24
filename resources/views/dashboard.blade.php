<x-app-layout>
    @php
        // Hardcoded to 5 since you have 5 core sacramental books
        $bookCount = 5; 

        // If you want to dynamically count entries in your tables directly from Blade without a controller, 
        // you can uncomment these lines below:
        // $massScheduleCount = DB::table('schedules')->count();
        // $pendingCertificatesCount = DB::table('certificates')->count();
        // $appointmentCount = DB::table('appointments')->count();

        // Temporary fallbacks so the other cards don't break:
        $massScheduleCount = $massScheduleCount ?? 0;
        $pendingCertificatesCount = $pendingCertificatesCount ?? 0;
        $appointmentCount = $appointmentCount ?? 0;
    @endphp

    <div class="relative min-h-[calc(100vh-140px)]">
        <div
            class="fixed inset-0 -z-10 bg-cover bg-center"
            style="background-image: url('https://wallpaperaccess.com/full/7322348.jpg');"
        ></div>

        <div class="fixed inset-0 -z-10 bg-white/30"></div>

        <main class="relative">
            <div class="max-w-7xl mx-auto px-10 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 justify-items-center">

                    <a
                        href="{{ Route::has('records.index') ? route('records.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-12 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                    >
                        <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                            Indexed Books
                        </h4>
                        <p class="text-8xl font-black text-blue-600 mb-6 tracking-tighter">
                            {{ $bookCount }}
                        </p>
                        <div class="h-1 w-12 bg-blue-100 mb-6"></div>
                        <span class="text-xs font-black text-blue-500 uppercase tracking-widest flex items-center">
                            Open Records <span class="ml-2">→</span>
                        </span>
                    </a>

                    <a
                        href="{{ Route::has('schedules.index') ? route('schedules.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-12 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                    >
                        <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                            Mass Schedules
                        </h4>
                        <p class="text-8xl font-black text-green-600 mb-6 tracking-tighter">
                            {{ $massScheduleCount }}
                        </p>
                        <div class="h-1 w-12 bg-green-100 mb-6"></div>
                        <span class="text-xs font-black text-green-500 uppercase tracking-widest flex items-center">
                            View Schedules <span class="ml-2">→</span>
                        </span>
                    </a>

                    <a
                        href="{{ Route::has('certificates.index') ? route('certificates.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-12 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                    >
                        <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                            Certificates
                        </h4>
                        <p class="text-8xl font-black text-amber-500 mb-6 tracking-tighter">
                            {{ $pendingCertificatesCount }}
                        </p>
                        <div class="h-1 w-12 bg-amber-100 mb-6"></div>
                        <span class="text-xs font-black text-amber-500 uppercase tracking-widest flex items-center">
                            Pending Requests <span class="ml-2">→</span>
                        </span>
                    </a>

                    <a
                        href="{{ Route::has('appointments.index') ? route('appointments.index') : '#' }}"
                        class="block w-full bg-white rounded-3xl shadow-lg p-12 border border-white/60 hover:shadow-xl hover:-translate-y-1 transition-all duration-300"
                    >
                        <h4 class="text-xs font-black text-gray-500 uppercase tracking-[0.2em] mb-6">
                            Appointments
                        </h4>
                        <p class="text-8xl font-black text-purple-600 mb-6 tracking-tighter">
                            {{ $appointmentCount }}
                        </p>
                        <div class="h-1 w-12 bg-purple-100 mb-6"></div>
                        <span class="text-xs font-black text-purple-500 uppercase tracking-widest flex items-center">
                            Manage Bookings <span class="ml-2">→</span>
                        </span>
                    </a>

                </div>
            </div>
        </main>
    </div>
</x-app-layout>