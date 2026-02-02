{{-- resources/views/admin/users/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edytuj użytkownika</h1>
                <p class="text-gray-600">Zaktualizuj informacje o użytkowniku {{ $user->full_name }}.</p>
            </div>
            <div class="mt-4 sm:mt-0 flex space-x-3">
                <a href="{{ route('admin.users.show', $user) }}"
                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 transition-colors">
                    <i class="fas fa-eye mr-2"></i>
                    Zobacz profil
                </a>
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Powrót do listy
                </a>
            </div>
        </div>
    </div>


    <div class="bg-linear-to-r from-indigo-500 to-purple-600 rounded-2xl p-6 mb-8 text-white">
        <div class="flex items-center">
            <div class="h-16 w-16 rounded-full overflow-hidden bg-white bg-opacity-20 border-4 border-white border-opacity-30">
                <img id="header-avatar"
                     src="{{ $user->avatar_url }}"
                     alt="Avatar użytkownika {{ $user->full_name }}"
                     class="h-full w-full object-cover">
            </div>
            <div class="ml-4">
                <h2 class="text-xl font-bold">{{ $user->full_name }}</h2>
                <p class="text-indigo-100">{{ $user->email }}</p>
                <div class="flex items-center mt-2">
                    @switch($user->role)
                        @case('admin')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20">
                                <i class="fas fa-user-shield mr-1"></i>
                                Administrator
                            </span>
                            @break
                        @case('doctor')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20">
                                <i class="fas fa-user-md mr-1"></i>
                                Fizjoterapeuta
                            </span>
                            @break
                        @case('user')
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20">
                                <i class="fas fa-user mr-1"></i>
                                Pacjent
                            </span>
                            @break
                    @endswitch

                    <span class="ml-3 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-white bg-opacity-20">
                        <i class="fas fa-circle mr-1 {{ $user->is_active ? 'text-green-300' : 'text-red-300' }}"></i>
                        {{ $user->is_active ? 'Aktywny' : 'Nieaktywny' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-linear-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-camera mr-2"></i>
                        Zdjęcie profilowe
                    </h3>
                </div>

                <div class="p-6 text-center">

                    <div class="mb-6">
                        <img id="current-avatar"
                             src="{{ $user->avatar_url }}"
                             alt="Avatar użytkownika {{ $user->full_name }}"
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-200 shadow-lg">
                    </div>


                    <form id="avatar-form" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <input type="file"
                                   id="avatar-input"
                                   name="avatar"
                                   accept="image/*"
                                   class="hidden">
                            <label for="avatar-input"
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors">
                                <i class="fas fa-upload mr-2"></i>
                                Wybierz zdjęcie
                            </label>
                        </div>

                        <div id="upload-progress" class="hidden">
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div class="bg-indigo-600 h-2 rounded-full transition-all duration-300" style="width: 0%"></div>
                            </div>
                            <p class="text-sm text-gray-600 mt-2">Przesyłanie...</p>
                        </div>

                        @if($user->hasCustomAvatar())
                        <button type="button"
                                id="delete-avatar-btn"
                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                            <i class="fas fa-trash mr-2"></i>
                            Usuń zdjęcie
                        </button>
                        @endif
                    </form>

                    <div class="mt-4 text-xs text-gray-500">
                        <p>Dozwolone formaty: JPEG, PNG, JPG, GIF</p>
                        <p>Maksymalny rozmiar: 2MB</p>
                    </div>


                    <div id="avatar-messages" class="mt-4"></div>
                </div>
            </div>
        </div>


        <div class="lg:col-span-2">

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-6">
                    @csrf
                    @method('PUT')


                    <div class="bg-linear-to-r from-indigo-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <i class="fas fa-user mr-2"></i>
                            Dane osobowe
                        </h3>
                    </div>

                    <div class="px-6 pb-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                            <div>
                                <label for="firstname" class="block text-sm font-medium text-gray-700 mb-2">
                                    Imię <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="firstname"
                                       name="firstname"
                                       value="{{ old('firstname', $user->firstname) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('firstname') @enderror"
                                       required>
                                @error('firstname')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="lastname" class="block text-sm font-medium text-gray-700 mb-2">
                                    Nazwisko <span class="text-red-500">*</span>
                                </label>
                                <input type="text"
                                       id="lastname"
                                       name="lastname"
                                       value="{{ old('lastname', $user->lastname) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('lastname') @enderror"
                                       required>
                                @error('lastname')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adres email <span class="text-red-500">*</span>
                                </label>
                                <input type="email"
                                       id="email"
                                       name="email"
                                       value="{{ old('email', $user->email) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('email') @enderror"
                                       required>
                                @error('email')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
                                    Telefon
                                </label>
                                <input type="text"
                                       id="phone"
                                       name="phone"
                                       value="{{ old('phone', $user->phone) }}"
                                       placeholder="+48 123 456 789"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('phone') @enderror">
                                @error('phone')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="date_of_birth" class="block text-sm font-medium text-gray-700 mb-2">
                                    Data urodzenia
                                </label>
                                <input type="date"
                                       id="date_of_birth"
                                       name="date_of_birth"
                                       value="{{ old('date_of_birth', $user->date_of_birth?->format('Y-m-d')) }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('date_of_birth') @enderror">
                                @error('date_of_birth')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div>
                                <label for="gender" class="block text-sm font-medium text-gray-700 mb-2">
                                    Płeć
                                </label>
                                <select id="gender"
                                        name="gender"
                                        class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('gender') @enderror">
                                    <option value="">Wybierz płeć</option>
                                    <option value="male" {{ old('gender', $user->gender) === 'male' ? 'selected' : '' }}>Mężczyzna</option>
                                    <option value="female" {{ old('gender', $user->gender) === 'female' ? 'selected' : '' }}>Kobieta</option>
                                    <option value="other" {{ old('gender', $user->gender) === 'other' ? 'selected' : '' }}>Inna</option>
                                </select>
                                @error('gender')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="md:col-span-2">
                                <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                                    Adres
                                </label>
                                <textarea id="address"
                                          name="address"
                                          rows="3"
                                          placeholder="Ulica, numer domu/mieszkania, kod pocztowy, miasto"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('address') @enderror">{{ old('address', $user->address) }}</textarea>
                                @error('address')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>


                            <div class="md:col-span-2">
                                <label for="emergency_contact" class="block text-sm font-medium text-gray-700 mb-2">
                                    Kontakt awaryjny
                                </label>
                                <input type="text"
                                       id="emergency_contact"
                                       name="emergency_contact"
                                       value="{{ old('emergency_contact', $user->emergency_contact) }}"
                                       placeholder="Imię, nazwisko i numer telefonu osoby kontaktowej"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('emergency_contact') @enderror">
                                @error('emergency_contact')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="border-t border-gray-200">
                        <div class="bg-linear-to-r from-indigo-500 to-purple-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-key mr-2"></i>
                                Informacje o koncie
                            </h3>
                        </div>

                        <div class="px-6 pb-6 pt-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                                <div>
                                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">
                                        Rola <span class="text-red-500">*</span>
                                    </label>
                                    <select id="role"
                                            name="role"
                                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('role') @enderror"
                                            required
                                            onchange="toggleMedicalHistory()"
                                            {{ $user->id === Auth::id() ? 'disabled' : '' }}>
                                        <option value="">Wybierz rolę</option>
                                        <option value="admin" {{ old('role', $user->role) === 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="doctor" {{ old('role', $user->role) === 'doctor' ? 'selected' : '' }}>Fizjoterapeuta</option>
                                        <option value="user" {{ old('role', $user->role) === 'user' ? 'selected' : '' }}>Pacjent</option>
                                    </select>
                                    @if($user->id === Auth::id())
                                        <input type="hidden" name="role" value="{{ $user->role }}">
                                        <p class="mt-1 text-sm text-gray-500">Nie możesz zmienić swojej własnej roli.</p>
                                    @endif
                                    @error('role')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div>
                                    <label for="is_active" class="block text-sm font-medium text-gray-700 mb-2">
                                        Status konta
                                    </label>
                                    <div class="flex items-center">
                                        <input type="hidden" name="is_active" value="0">
                                        <input type="checkbox"
                                               id="is_active"
                                               name="is_active"
                                               value="1"
                                               {{ old('is_active', $user->is_active) ? 'checked' : '' }}
                                               {{ $user->id === Auth::id() && $user->is_active ? 'disabled' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                                            Konto aktywne
                                        </label>
                                    </div>
                                    @if($user->id === Auth::id() && $user->is_active)
                                        <input type="hidden" name="is_active" value="1">
                                        <p class="mt-1 text-sm text-gray-500">Nie możesz dezaktywować swojego własnego konta.</p>
                                    @endif
                                </div>


                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nowe hasło
                                        <span class="text-gray-500 font-normal">(pozostaw puste, aby nie zmieniać)</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') @enderror">
                                        <button type="button"
                                                onclick="togglePassword('password')"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password-icon"></i>
                                        </button>
                                    </div>
                                    @error('password')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>


                                <div>
                                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                        Potwierdź nowe hasło
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               id="password_confirmation"
                                               name="password_confirmation"
                                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                                        <button type="button"
                                                onclick="togglePassword('password_confirmation')"
                                                class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                            <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password_confirmation-icon"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div id="medical-history-section" class="border-t border-gray-200 {{ old('role', $user->role) === 'user' ? '' : 'hidden' }}">
                        <div class="bg-linear-to-r from-green-500 to-teal-600 px-6 py-4">
                            <h3 class="text-lg font-semibold text-white flex items-center">
                                <i class="fas fa-file-medical mr-2"></i>
                                Historia medyczna
                            </h3>
                        </div>

                        <div class="px-6 pb-6 pt-6">
                            <div>
                                <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                                    Historia chorób i ważne informacje medyczne
                                </label>
                                <textarea id="medical_history"
                                          name="medical_history"
                                          rows="5"
                                          placeholder="Wprowadź każdą informację w nowej linii, np.:&#10;Cukrzyca typu 2&#10;Alergia na penicylinę&#10;Operacja kolana (2020)"
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('medical_history') @enderror">{{ old('medical_history', is_array($user->medical_history) ? implode("\n", $user->medical_history) : $user->medical_history) }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">
                                    Wprowadź każdą informację w osobnej linii. Zostanie to zapisane jako lista.
                                </p>
                                @error('medical_history')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>


                    <div class="border-t border-gray-200 bg-gray-50 px-6 py-4">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
                            <div>
                                <strong>Utworzono:</strong> {{ $user->created_at->format('d.m.Y H:i') }}
                            </div>
                            <div>
                                <strong>Ostatnia aktualizacja:</strong> {{ $user->updated_at->format('d.m.Y H:i') }}
                            </div>
                            <div class="flex items-center gap-2">
                                <strong>Email zweryfikowany:</strong>
                                <span id="verify-edit-status">
                                    @if($user->email_verified_at)
                                        <span class="text-green-600">{{ $user->email_verified_at->format('d.m.Y') }}</span>
                                    @else
                                        <span class="text-red-600">Nie</span>
                                    @endif
                                </span>
                                @if($user->id !== Auth::id())
                                    <button type="button" id="verify-edit-btn" onclick="toggleVerifyEmail({{ $user->id }})"
                                            class="inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors {{ $user->email_verified_at ? 'bg-red-100 text-red-700 hover:bg-red-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }}">
                                        <i class="fas {{ $user->email_verified_at ? 'fa-times-circle' : 'fa-check-circle' }} mr-1"></i>
                                        {{ $user->email_verified_at ? 'Cofnij weryfikację' : 'Zweryfikuj ręcznie' }}
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>


                    <div class="border-t border-gray-200 px-6 py-4 bg-gray-50">
                        <div class="flex flex-col sm:flex-row sm:justify-end space-y-3 sm:space-y-0 sm:space-x-3">
                            <a href="{{ route('admin.users.index') }}"
                               class="inline-flex justify-center items-center px-6 py-2 border border-gray-300 text-gray-700 bg-white rounded-lg hover:bg-gray-50 transition-colors">
                                <i class="fas fa-times mr-2"></i>
                                Anuluj
                            </a>
                            <button type="submit"
                                    class="inline-flex justify-center items-center px-6 py-2 bg-linear-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                                <i class="fas fa-save mr-2"></i>
                                Zapisz zmiany
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>

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
                const statusEl = document.getElementById('verify-edit-status');
                const btn = document.getElementById('verify-edit-btn');

                if (data.verified) {
                    const now = new Date();
                    const dateStr = now.toLocaleDateString('pl-PL', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    statusEl.innerHTML = `<span class="text-green-600">${dateStr}</span>`;
                    btn.className = 'inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors bg-red-100 text-red-700 hover:bg-red-200';
                    btn.innerHTML = '<i class="fas fa-times-circle mr-1"></i> Cofnij weryfikację';
                } else {
                    statusEl.innerHTML = '<span class="text-red-600">Nie</span>';
                    btn.className = 'inline-flex items-center px-3 py-1 text-xs font-medium rounded-md transition-colors bg-green-100 text-green-700 hover:bg-green-200';
                    btn.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Zweryfikuj ręcznie';
                }
            } else {
                Swal.fire({ icon: 'error', title: 'Błąd', text: data.error || 'Wystąpił błąd.', confirmButtonColor: '#d33' });
            }
        })
        .catch(() => {
            Swal.fire({ icon: 'error', title: 'Błąd', text: 'Wystąpił błąd podczas zmiany weryfikacji.', confirmButtonColor: '#d33' });
        });
    }

    function togglePassword(fieldId) {
        const field = document.getElementById(fieldId);
        const icon = document.getElementById(fieldId + '-icon');

        if (field.type === 'password') {
            field.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            field.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }


    function toggleMedicalHistory() {
        const roleSelect = document.getElementById('role');
        const medicalHistorySection = document.getElementById('medical-history-section');

        if (roleSelect.value === 'user') {
            medicalHistorySection.classList.remove('hidden');
        } else {
            medicalHistorySection.classList.add('hidden');

        }
    }


    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            uploadAvatar(file);
        }
    });


    document.getElementById('delete-avatar-btn')?.addEventListener('click', function() {
        if (confirm('Czy na pewno chcesz usunąć zdjęcie profilowe tego użytkownika?')) {
            deleteAvatar();
        }
    });

    function uploadAvatar(file) {
        const formData = new FormData();
        formData.append('avatar', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        const progressDiv = document.getElementById('upload-progress');
        const progressBar = progressDiv.querySelector('.bg-indigo-600');

        progressDiv.classList.remove('hidden');

        fetch('{{ route("admin.users.avatar.upload", $user) }}', {
            method: 'POST',
            body: formData,
            onUploadProgress: function(progressEvent) {
                if (progressEvent.lengthComputable) {
                    const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                    progressBar.style.width = percentCompleted + '%';
                }
            }
        })
        .then(response => response.json())
        .then(data => {
            progressDiv.classList.add('hidden');

            if (data.success) {

                const currentAvatar = document.getElementById('current-avatar');
                const headerAvatar = document.getElementById('header-avatar');
                const newUrl = data.avatar_url + '?' + new Date().getTime();

                currentAvatar.src = newUrl;
                headerAvatar.src = newUrl;

                showMessage('success', data.message);


                setTimeout(() => {
                    location.reload();
                }, 1000);
            } else {
                showMessage('error', data.message || 'Wystąpił błąd podczas przesyłania zdjęcia.');
            }
        })
        .catch(error => {
            progressDiv.classList.add('hidden');
            showMessage('error', 'Wystąpił błąd podczas przesyłania zdjęcia.');
            console.error('Error:', error);
        });
    }

    function deleteAvatar() {
        fetch('{{ route("admin.users.avatar.delete", $user) }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {

                const currentAvatar = document.getElementById('current-avatar');
                const headerAvatar = document.getElementById('header-avatar');
                const newUrl = data.avatar_url + '?' + new Date().getTime();

                currentAvatar.src = newUrl;
                headerAvatar.src = newUrl;

                showMessage('success', data.message);


                const deleteBtn = document.getElementById('delete-avatar-btn');
                if (deleteBtn) {
                    deleteBtn.style.display = 'none';
                }
            } else {
                showMessage('error', data.message || 'Wystąpił błąd podczas usuwania zdjęcia.');
            }
        })
        .catch(error => {
            showMessage('error', 'Wystąpił błąd podczas usuwania zdjęcia.');
            console.error('Error:', error);
        });
    }

    function showMessage(type, message) {
        const messagesDiv = document.getElementById('avatar-messages');
        const alertClass = type === 'success' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-red-100 text-red-800 border-red-200';

        messagesDiv.innerHTML = `
            <div class="p-3 rounded-lg border ${alertClass}">
                <p class="text-sm">${message}</p>
            </div>
        `;


        setTimeout(() => {
            messagesDiv.innerHTML = '';
        }, 3000);
    }


    document.addEventListener('DOMContentLoaded', function() {
        toggleMedicalHistory();
    });
</script>
@endsection
