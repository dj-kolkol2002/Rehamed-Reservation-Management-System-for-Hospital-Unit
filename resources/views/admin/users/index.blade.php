
@extends('layouts.app')

@section('styles')
<style>

td .fas, td .fa, .table-actions .fas, .table-actions .fa {
    display: inline-block !important;
    visibility: visible !important;
    opacity: 1 !important;
    font-size: 14px !important;
}

.action-icon, .action-icon * {
    display: inline-flex !important;
    visibility: visible !important;
}


.table-actions * {
    display: inherit !important;
}


button {
    display: inline-flex !important;
}

button .fas, button .fa {
    display: inline-block !important;
}


.inline-flex {
    display: inline-flex !important;
}

.action-icon {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px;
    transition: all 0.2s ease;
    text-decoration: none;
    padding: 6px !important;
}

.action-icon:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}
</style>
@endsection

@section('content')
<div class="flex-1 p-6">

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Zarządzanie użytkownikami</h1>
                <p class="text-gray-600">Przeglądaj i zarządzaj kontami użytkowników systemu.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors duration-150">
                    <i class="fas fa-plus mr-2"></i>
                    Dodaj użytkownika
                </a>
            </div>
        </div>
    </div>


    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-users text-blue-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Wszyscy</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['total'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-check-circle text-green-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Aktywni</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['active'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-times-circle text-red-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Nieaktywni</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['inactive'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-shield text-purple-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Admini</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['admins'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-teal-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user-md text-teal-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Lekarze</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['doctors'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-xl p-4 shadow-lg border border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center">
                    <i class="fas fa-user text-orange-600"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600">Pacjenci</p>
                    <p class="text-xl font-bold text-gray-900">{{ $stats['patients'] }}</p>
                </div>
            </div>
        </div>
    </div>


    <div class="bg-white rounded-2xl p-6 shadow-lg mb-8">
        <form method="GET" action="{{ route('admin.users.index') }}" class="space-y-4">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Wyszukaj</label>
                    <div class="relative">
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ $request->get('search') }}"
                               placeholder="Imię, nazwisko lub email..."
                               class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="fas fa-search text-gray-400"></i>
                        </div>
                    </div>
                </div>


                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Rola</label>
                    <select id="role" name="role" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Wszystkie role</option>
                        <option value="admin" {{ $request->get('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                        <option value="doctor" {{ $request->get('role') === 'doctor' ? 'selected' : '' }}>Fizjoterapeuta</option>
                        <option value="user" {{ $request->get('role') === 'user' ? 'selected' : '' }}>Pacjent</option>
                    </select>
                </div>


                <div>
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                    <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="">Wszystkie statusy</option>
                        <option value="active" {{ $request->get('status') === 'active' ? 'selected' : '' }}>Aktywni</option>
                        <option value="inactive" {{ $request->get('status') === 'inactive' ? 'selected' : '' }}>Nieaktywni</option>
                    </select>
                </div>


                <div>
                    <label for="sort" class="block text-sm font-medium text-gray-700 mb-2">Sortuj według</label>
                    <select id="sort" name="sort" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                        <option value="created_at" {{ $request->get('sort') === 'created_at' ? 'selected' : '' }}>Data utworzenia</option>
                        <option value="firstname" {{ $request->get('sort') === 'firstname' ? 'selected' : '' }}>Imię</option>
                        <option value="lastname" {{ $request->get('sort') === 'lastname' ? 'selected' : '' }}>Nazwisko</option>
                        <option value="email" {{ $request->get('sort') === 'email' ? 'selected' : '' }}>Email</option>
                        <option value="role" {{ $request->get('sort') === 'role' ? 'selected' : '' }}>Rola</option>
                    </select>
                    <input type="hidden" name="direction" value="{{ $request->get('direction', 'desc') }}">
                </div>
            </div>

            <div class="flex flex-col sm:flex-row gap-3">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-filter mr-2"></i>
                    Filtruj
                </button>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-times mr-2"></i>
                    Wyczyść filtry
                </a>
            </div>
        </form>
    </div>


    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'firstname', 'direction' => request('sort') === 'firstname' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center hover:text-gray-700">
                                Użytkownik
                                @if(request('sort') === 'firstname')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'email', 'direction' => request('sort') === 'email' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center hover:text-gray-700">
                                Email
                                @if(request('sort') === 'email')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'role', 'direction' => request('sort') === 'role' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center hover:text-gray-700">
                                Rola
                                @if(request('sort') === 'role')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <a href="{{ route('admin.users.index', array_merge(request()->query(), ['sort' => 'created_at', 'direction' => request('sort') === 'created_at' && request('direction') === 'asc' ? 'desc' : 'asc'])) }}"
                               class="flex items-center hover:text-gray-700">
                                Data utworzenia
                                @if(request('sort') === 'created_at')
                                    <i class="fas fa-sort-{{ request('direction') === 'asc' ? 'up' : 'down' }} ml-1"></i>
                                @else
                                    <i class="fas fa-sort ml-1 opacity-50"></i>
                                @endif
                            </a>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Akcje</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($users as $user)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="shrink-0 h-10 w-10">
                                    <img src="{{ $user->avatar_url }}"
                                         alt="Avatar użytkownika {{ $user->full_name }}"
                                         class="h-10 w-10 rounded-full object-cover border border-gray-200">
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $user->full_name }}
                                    </div>
                                    @if($user->phone)
                                    <div class="text-sm text-gray-500">
                                        {{ $user->phone }}
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $user->email }}</div>
                            <div id="verify-status-{{ $user->id }}" class="text-xs flex items-center gap-1">
                                @if($user->email_verified_at)
                                    <span class="text-green-600 flex items-center">
                                        <i class="fas fa-check-circle mr-1"></i>
                                        Zweryfikowany
                                    </span>
                                @else
                                    <span class="text-red-600 flex items-center">
                                        <i class="fas fa-exclamation-circle mr-1"></i>
                                        Niezweryfikowany
                                    </span>
                                @endif
                                @if($user->id !== Auth::id())
                                    <button onclick="toggleVerifyEmail({{ $user->id }})"
                                            title="{{ $user->email_verified_at ? 'Cofnij weryfikację' : 'Zweryfikuj ręcznie' }}"
                                            style="display: inline-flex !important; align-items: center; justify-content: center; width: 20px; height: 20px; border: none; border-radius: 4px; cursor: pointer; background: {{ $user->email_verified_at ? '#fef2f2' : '#f0fdf4' }}; color: {{ $user->email_verified_at ? '#dc2626' : '#16a34a' }}; font-size: 11px;"
                                            onmouseover="this.style.opacity='0.7'"
                                            onmouseout="this.style.opacity='1'">
                                        <i class="fas {{ $user->email_verified_at ? 'fa-times' : 'fa-check' }}" style="display: inline-block !important; font-size: 11px;"></i>
                                    </button>
                                @endif
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @switch($user->role)
                                @case('admin')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        <i class="fas fa-user-shield mr-1"></i>
                                        Administrator
                                    </span>
                                    @break
                                @case('doctor')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-teal-100 text-teal-800">
                                        <i class="fas fa-user-md mr-1"></i>
                                        Fizjoterapeuta
                                    </span>
                                    @break
                                @case('user')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        <i class="fas fa-user mr-1"></i>
                                        Pacjent
                                    </span>
                                    @break
                            @endswitch
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <button onclick="toggleUserStatus({{ $user->id }}, {{ $user->is_active ? 'false' : 'true' }})"
                                    class="toggle-status-btn"
                                    data-user-id="{{ $user->id }}"
                                    {{ $user->id === Auth::id() && $user->is_active ? 'disabled' : '' }}>
                                @if($user->is_active)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 cursor-pointer hover:bg-green-200 transition-colors">
                                        <i class="fas fa-circle mr-1 text-green-500"></i>
                                        Aktywny
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 cursor-pointer hover:bg-red-200 transition-colors">
                                        <i class="fas fa-circle mr-1 text-red-500"></i>
                                        Nieaktywny
                                    </span>
                                @endif
                            </button>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $user->created_at->format('d.m.Y') }}
                            <div class="text-xs text-gray-500">
                                {{ $user->created_at->format('H:i') }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium table-actions">
                            <div style="display: flex !important; gap: 8px; justify-content: flex-end;">
                                <a href="{{ route('admin.users.show', $user) }}"
                                   style="display: inline-flex !important; width: 32px; height: 32px; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; color: #4f46e5; text-decoration: none;"
                                   title="Zobacz szczegóły"
                                   onmouseover="this.style.background='#e5e7eb'"
                                   onmouseout="this.style.background='#f3f4f6'">
                                    <i class="fas fa-eye" style="display: inline-block !important; font-size: 14px;"></i>
                                </a>
                                <a href="{{ route('admin.users.edit', $user) }}"
                                   style="display: inline-flex !important; width: 32px; height: 32px; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; color: #059669; text-decoration: none;"
                                   title="Edytuj"
                                   onmouseover="this.style.background='#e5e7eb'"
                                   onmouseout="this.style.background='#f3f4f6'">
                                    <i class="fas fa-edit" style="display: inline-block !important; font-size: 14px;"></i>
                                </a>
                                @if($user->id !== Auth::id())
                                <button onclick="deleteUser({{ $user->id }}, '{{ addslashes($user->full_name) }}')"
                                        style="display: inline-flex !important; width: 32px; height: 32px; align-items: center; justify-content: center; background: #f3f4f6; border-radius: 6px; color: #dc2626; border: none; cursor: pointer;"
                                        title="Usuń"
                                        onmouseover="this.style.background='#e5e7eb'"
                                        onmouseout="this.style.background='#f3f4f6'">
                                    <i class="fas fa-trash" style="display: inline-block !important; font-size: 14px;"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <i class="fas fa-users text-4xl text-gray-300 mb-4"></i>
                                <h3 class="text-lg font-medium text-gray-900 mb-2">Brak użytkowników</h3>
                                <p class="text-gray-500 mb-4">Nie znaleziono użytkowników spełniających kryteria wyszukiwania.</p>
                                <a href="{{ route('admin.users.create') }}"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors">
                                    <i class="fas fa-plus mr-2"></i>
                                    Dodaj pierwszego użytkownika
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        @if($users->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            <div class="flex flex-col sm:flex-row items-center justify-between">
                <div class="text-sm text-gray-700 mb-4 sm:mb-0">
                    Pokazano {{ $users->firstItem() }} - {{ $users->lastItem() }} z {{ $users->total() }} wyników
                </div>
                <div>
                    {{ $users->links() }}
                </div>
            </div>
        </div>
        @endif
    </div>
</div>


<div id="deleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <i class="fas fa-exclamation-triangle text-red-600"></i>
            </div>
            <h3 class="text-lg font-medium text-gray-900 mt-2">Potwierdź usunięcie</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500" id="deleteMessage">
                    Czy na pewno chcesz usunąć tego użytkownika? Ta akcja nie może zostać cofnięta.
                </p>
            </div>
            <div class="items-center px-4 py-3">
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                            class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300 mr-2">
                        Usuń
                    </button>
                </form>
                <button onclick="closeDeleteModal()"
                        class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md shadow-sm hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300">
                    Anuluj
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>

    function toggleUserStatus(userId, newStatus) {
        fetch(`/admin/users/${userId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                is_active: newStatus
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Wystąpił błąd podczas zmiany statusu.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Wystąpił błąd podczas zmiany statusu.');
        });
    }


    function toggleVerifyEmail(userId) {
        fetch(`/admin/users/${userId}/verify-email`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const container = document.getElementById(`verify-status-${userId}`);
                const statusSpan = container.querySelector('span');
                const btn = container.querySelector('button');

                if (data.verified) {
                    statusSpan.className = 'text-green-600 flex items-center';
                    statusSpan.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Zweryfikowany';
                    btn.title = 'Cofnij weryfikację';
                    btn.style.background = '#fef2f2';
                    btn.style.color = '#dc2626';
                    btn.querySelector('i').className = 'fas fa-times';
                } else {
                    statusSpan.className = 'text-red-600 flex items-center';
                    statusSpan.innerHTML = '<i class="fas fa-exclamation-circle mr-1"></i> Niezweryfikowany';
                    btn.title = 'Zweryfikuj ręcznie';
                    btn.style.background = '#f0fdf4';
                    btn.style.color = '#16a34a';
                    btn.querySelector('i').className = 'fas fa-check';
                }
            } else {
                Swal.fire({ icon: 'error', title: 'Błąd', text: data.error || 'Wystąpił błąd.', confirmButtonColor: '#d33' });
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Błąd', text: 'Wystąpił błąd podczas zmiany weryfikacji.', confirmButtonColor: '#d33' });
        });
    }

    function deleteUser(userId, userName) {
        document.getElementById('deleteMessage').textContent =
            `Czy na pewno chcesz usunąć użytkownika "${userName}"? Ta akcja nie może zostać cofnięta.`;
        document.getElementById('deleteForm').action = `/admin/users/${userId}`;
        document.getElementById('deleteModal').classList.remove('hidden');
    }

    function closeDeleteModal() {
        document.getElementById('deleteModal').classList.add('hidden');
    }


    document.getElementById('deleteModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeDeleteModal();
        }
    });
</script>
@endsection
