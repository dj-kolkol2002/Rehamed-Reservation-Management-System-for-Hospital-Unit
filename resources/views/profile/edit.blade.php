{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Edytuj profil</h1>
                <p class="text-gray-600">Zaktualizuj swoje dane osobowe i ustawienia konta.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('profile.show') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Powrót do profilu
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Avatar Section -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-camera mr-2"></i>
                        Zdjęcie profilowe
                    </h3>
                </div>

                <div class="p-6 text-center">
                    <!-- Current Avatar -->
                    <div class="mb-6">
                        <img id="current-avatar"
                             src="{{ $user->avatar_url }}"
                             alt="Avatar użytkownika"
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-200 shadow-lg">
                    </div>

                    <!-- Upload Form -->
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

                    <!-- Messages -->
                    <div id="avatar-messages" class="mt-4"></div>
                </div>
            </div>
        </div>

        <!-- Profile Form -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Personal Information -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        Dane osobowe
                    </h3>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- First Name -->
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

                        <!-- Last Name -->
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

                        <!-- Email -->
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

                        <!-- Phone -->
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

                        <!-- Date of Birth -->
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

                        <!-- Gender -->
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

                        <!-- Address -->
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

                        <!-- Emergency Contact -->
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

                    <!-- Medical History (for patients only) -->
                    @if($user->role === 'user')
                    <div class="border-t pt-6">
                        <label for="medical_history" class="block text-sm font-medium text-gray-700 mb-2">
                            Historia medyczna
                        </label>
                        <textarea id="medical_history"
                                  name="medical_history"
                                  rows="5"
                                  placeholder="Wprowadź każdą informację w nowej linii, np.:&#10;Cukrzyca typu 2&#10;Alergia na penicylinę&#10;Operacja kolana (2020)"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('medical_history') @enderror">{{ old('medical_history', is_array($user->medical_history) ? implode("\n", $user->medical_history) : $user->medical_history) }}</textarea>
                        <p class="mt-1 text-sm text-gray-500">
                            Wprowadź każdą informację w osobnej linii.
                        </p>
                        @error('medical_history')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    <!-- Submit Button -->
                    <div class="border-t pt-6">
                        <button type="submit"
                                class="inline-flex justify-center items-center px-6 py-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-medium rounded-lg hover:from-indigo-700 hover:to-purple-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-save mr-2"></i>
                            Zapisz zmiany
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Change -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="bg-gradient-to-r from-red-500 to-pink-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <i class="fas fa-lock mr-2"></i>
                        Zmiana hasła
                    </h3>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Aktualne hasło <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="current_password"
                                       name="current_password"
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('current_password', 'password') @enderror"
                                       required>
                                <button type="button"
                                        onclick="togglePassword('current_password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="current_password-icon"></i>
                                </button>
                            </div>
                            @error('current_password', 'password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- New Password -->
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Nowe hasło <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password"
                                       name="password"
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password', 'password') @enderror"
                                       required>
                                <button type="button"
                                        onclick="togglePassword('password')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password-icon"></i>
                                </button>
                            </div>
                            @error('password', 'password')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Confirm Password -->
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-2">
                                Potwierdź hasło <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <input type="password"
                                       id="password_confirmation"
                                       name="password_confirmation"
                                       class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent"
                                       required>
                                <button type="button"
                                        onclick="togglePassword('password_confirmation')"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <i class="fas fa-eye text-gray-400 hover:text-gray-600" id="password_confirmation-icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="border-t pt-6">
                        <button type="submit"
                                class="inline-flex justify-center items-center px-6 py-2 bg-gradient-to-r from-red-600 to-pink-600 text-white font-medium rounded-lg hover:from-red-700 hover:to-pink-700 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:-translate-y-1">
                            <i class="fas fa-key mr-2"></i>
                            Zmień hasło
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Toggle password visibility
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

    // Avatar upload functionality
    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            uploadAvatar(file);
        }
    });

    // Delete avatar functionality
    document.getElementById('delete-avatar-btn')?.addEventListener('click', function() {
        if (confirm('Czy na pewno chcesz usunąć zdjęcie profilowe?')) {
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

        fetch('{{ route("profile.avatar.upload") }}', {
            method: 'POST',
            body: formData,
            onUploadProgress: function(progressEvent) {
                const percentCompleted = Math.round((progressEvent.loaded * 100) / progressEvent.total);
                progressBar.style.width = percentCompleted + '%';
            }
        })
        .then(response => response.json())
        .then(data => {
            progressDiv.classList.add('hidden');

            if (data.success) {
                document.getElementById('current-avatar').src = data.avatar_url + '?' + new Date().getTime();
                showMessage('success', data.message);

                // Refresh page to show delete button if needed
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
        });
    }

    function deleteAvatar() {
        fetch('{{ route("profile.avatar.delete") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.getElementById('current-avatar').src = data.avatar_url + '?' + new Date().getTime();
                showMessage('success', data.message);

                // Hide delete button
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

        // Auto-hide after 3 seconds
        setTimeout(() => {
            messagesDiv.innerHTML = '';
        }, 3000);
    }
</script>
@endsection
