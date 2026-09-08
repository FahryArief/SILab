@extends('admin.landing.layout')

@section('landing-content')
<div class="bg-white shadow rounded-lg p-6">
    <form action="{{ route('admin.landing.settings.update') }}" method="POST">
        @csrf

        <div class="space-y-6">
            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Statistik (Angka)</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(isset($settings['statistik']))
                        @foreach($settings['statistik'] as $setting)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $setting->key) }}</label>
                                <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>

            <div>
                <h3 class="text-lg font-medium text-gray-900 border-b pb-2 mb-4">Footer</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(isset($settings['footer']))
                        @foreach($settings['footer'] as $setting)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 capitalize">{{ str_replace('_', ' ', $setting->key) }}</label>
                                @if($setting->key == 'footer_alamat')
                                    <textarea name="{{ $setting->key }}" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">{{ $setting->value }}</textarea>
                                @else
                                    <input type="text" name="{{ $setting->key }}" value="{{ $setting->value }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                @endif
                            </div>
                        @endforeach
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">Simpan Pengaturan</button>
        </div>
    </form>
</div>
@endsection
