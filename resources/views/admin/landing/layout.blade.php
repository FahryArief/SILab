<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Pengaturan Landing Page') }}
        </h2>
    </x-slot>

    <div class="mb-6">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                <a href="{{ route('admin.landing.settings.index') }}" 
                   class="{{ request()->routeIs('admin.landing.settings.*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Statistik & Footer
                </a>
                
                <a href="{{ route('admin.landing.prestasi.index') }}" 
                   class="{{ request()->routeIs('admin.landing.prestasi.*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Prestasi
                </a>

                <a href="{{ route('admin.landing.fasilitas.index') }}" 
                   class="{{ request()->routeIs('admin.landing.fasilitas.*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Fasilitas
                </a>

                <a href="{{ route('admin.landing.dokumentasi.index') }}" 
                   class="{{ request()->routeIs('admin.landing.dokumentasi.*') ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Dokumentasi
                </a>
            </nav>
        </div>
    </div>

    @if (session('success'))
        <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
            {{ session('success') }}
        </div>
    @endif

    @yield('landing-content')

</x-app-layout>
