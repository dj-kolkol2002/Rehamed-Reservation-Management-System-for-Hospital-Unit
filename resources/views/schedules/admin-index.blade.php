@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">
                <i class="fas fa-calendar-alt mr-3"></i>Harmonogramy Fizjoterapeutów
            </h1>
            <p class="text-gray-600 dark:text-gray-400">Zarządzaj harmonogramami pracy wszystkich fizjoterapeutów</p>
        </div>

        @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900/20 border border-green-400 dark:border-green-700 text-green-700 dark:text-green-400 px-4 py-3 rounded-lg mb-6">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 dark:bg-red-900/20 border border-red-400 dark:border-red-700 text-red-700 dark:text-red-400 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($doctors as $doctor)
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                <div class="p-6">
                    <div class="flex items-center mb-4">
                        <img src="{{ $doctor->avatar_url ?? 'https://via.placeholder.com/48' }}" alt="{{ $doctor->full_name }}" class="h-12 w-12 rounded-full mr-4 object-cover">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $doctor->full_name }}</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">{{ $doctor->email }}</p>
                        </div>
                    </div>

                    <div class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                        <p>Status: <span class="font-semibold text-green-600 dark:text-green-400">Aktywny</span></p>
                    </div>

                    <a href="{{ route('schedules.show', $doctor->id) }}" class="block w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2 px-4 rounded-lg transition-colors text-center">
                        <i class="fas fa-edit mr-2"></i>Edytuj Harmonogram
                    </a>
                </div>
            </div>
            @empty
            <div class="col-span-full text-center py-12">
                <i class="fas fa-user-md text-gray-400 text-4xl mb-4"></i>
                <p class="text-gray-600 dark:text-gray-400">Brak fizjoterapeutów w systemie</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
