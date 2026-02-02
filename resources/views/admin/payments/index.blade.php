@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
                <i class="fas fa-credit-card mr-3 text-indigo-600 dark:text-indigo-400"></i>
                Wszystkie płatności
            </h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">
                Zarządzanie wszystkimi płatnościami w systemie
            </p>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 rounded-lg">
                <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-100 dark:bg-red-900 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-200 rounded-lg">
                <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
            </div>
        @endif


        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <form method="GET" action="{{ route('payments.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status płatności</label>
                    <select name="status" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Wszystkie</option>
                        <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Opłacone</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Oczekujące</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Nieudane</option>
                    </select>
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Metoda płatności</label>
                    <select name="method" onchange="this.form.submit()" class="w-full px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-indigo-500">
                        <option value="">Wszystkie</option>
                        <option value="stripe" {{ request('method') === 'stripe' ? 'selected' : '' }}>Online (Stripe)</option>
                        <option value="cash" {{ request('method') === 'cash' ? 'selected' : '' }}>Gotówka</option>
                    </select>
                </div>


                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Szukaj użytkownika</label>
                    <div class="flex gap-2">
                        <input type="text" name="search" placeholder="Imię lub nazwisko..." value="{{ request('search') }}" class="flex-1 px-4 py-2 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-indigo-500">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>


                @if(request('status') || request('method') || request('search'))
                    <div class="flex items-end gap-2">
                        <a href="{{ route('payments.index') }}" class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg font-medium hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors text-center">
                            <i class="fas fa-redo mr-1"></i> Wyczyść filtry
                        </a>
                    </div>
                @endif
            </form>
        </div>


        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
            @if($payments->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Wizyta
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Pacjent
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Fizjoterapeuta
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Data wizyty
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Kwota
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Metoda
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 dark:text-gray-300 uppercase tracking-wider">
                                    Akcje
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($payments as $payment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $payment->appointment->title ?? 'Brak tytułu' }}
                                        </div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $payment->appointment->type_display ?? '' }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $payment->user ? $payment->user->firstname . ' ' . $payment->user->lastname : 'Brak danych' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $payment->appointment->doctor ? 'Dr ' . $payment->appointment->doctor->firstname . ' ' . $payment->appointment->doctor->lastname : 'Brak danych' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                        {{ $payment->appointment ? $payment->appointment->start_time->timezone('Europe/Warsaw')->format('d.m.Y H:i') : 'Brak danych' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-semibold text-gray-900 dark:text-white">
                                            {{ number_format($payment->amount, 2, ',', ' ') }} PLN
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->payment_method === 'stripe')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                                <i class="fas fa-credit-card mr-1"></i> Online
                                            </span>
                                        @elseif($payment->payment_method === 'cash')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                <i class="fas fa-money-bill-wave mr-1"></i> Gotówka
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                {{ ucfirst($payment->payment_method) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($payment->status === 'completed')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                <i class="fas fa-check-circle mr-1"></i> Opłacone
                                            </span>
                                        @elseif($payment->status === 'pending')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200">
                                                <i class="fas fa-clock mr-1"></i> Oczekuje
                                            </span>
                                        @elseif($payment->status === 'failed')
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200">
                                                <i class="fas fa-times-circle mr-1"></i> Nieudane
                                            </span>
                                        @else
                                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-300">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <a href="{{ route('payments.show', $payment) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                            <i class="fas fa-eye mr-1"></i> Zobacz
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>


                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $payments->links() }}
                </div>
            @else
                <div class="text-center py-12">
                    <i class="fas fa-receipt text-6xl text-gray-300 dark:text-gray-600 mb-4"></i>
                    <p class="text-gray-500 dark:text-gray-400 text-lg">Brak płatności</p>
                    <p class="text-gray-400 dark:text-gray-500 text-sm mt-2">Nie masz jeszcze żadnych płatności</p>
                    <a href="{{ route('calendar.index') }}" class="mt-6 inline-block px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors">
                        <i class="fas fa-calendar mr-2"></i>Przejdź do kalendarza
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
