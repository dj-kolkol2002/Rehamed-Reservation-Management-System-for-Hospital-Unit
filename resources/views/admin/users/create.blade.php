
@extends('layouts.app')

@section('content')
<div class="flex-1 p-6">

    <div class="mb-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Dodaj nowego użytkownika</h1>
                <p class="text-gray-600">Utwórz nowe konto użytkownika w systemie.</p>
            </div>
            <div class="mt-4 sm:mt-0">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-500 text-white font-medium rounded-lg hover:bg-gray-600 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>
                    Powrót do listy
                </a>
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
                        <img id="avatar-preview"
                             src="https://ui-avatars.com/api/?name=N+U&size=200&background=667eea&color=ffffff&bold=true&rounded=true"
                             alt="Podgląd awatara"
                             class="w-32 h-32 rounded-full mx-auto object-cover border-4 border-gray-200 shadow-lg">
                    </div>


                    <div class="mb-4">
                        <input type="file"
                               id="avatar-input"
                               name="avatar"
                               accept="image/*"
                               class="hidden"
                               form="user-form">
                        <label for="avatar-input"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 cursor-pointer transition-colors">
                            <i class="fas fa-upload mr-2"></i>
                            Wybierz zdjęcie
                        </label>
                    </div>

                    <div class="text-xs text-gray-500">
                        <p>Dozwolone formaty: JPEG, PNG, JPG, GIF</p>
                        <p>Maksymalny rozmiar: 2MB</p>
                        <p class="mt-2 text-gray-400">Jeśli nie wybierzesz zdjęcia, zostanie wygenerowany domyślny avatar na podstawie inicjałów.</p>
                    </div>
                </div>
            </div>
        </div>


        <div class="lg:col-span-2">

            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <form id="user-form" method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data" class="space-y-6">
                    @csrf


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
                                       value="{{ old('firstname') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('firstname') @enderror"
                                       required
                                       oninput="updateAvatarPreview()">
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
                                       value="{{ old('lastname') }}"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('lastname') @enderror"
                                       required
                                       oninput="updateAvatarPreview()">
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
                                       value="{{ old('email') }}"
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
                                       value="{{ old('phone') }}"
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
                                       value="{{ old('date_of_birth') }}"
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
                                    <option value="male" {{ old('gender') === 'male' ? 'selected' : '' }}>Mężczyzna</option>
                                    <option value="female" {{ old('gender') === 'female' ? 'selected' : '' }}>Kobieta</option>
                                    <option value="other" {{ old('gender') === 'other' ? 'selected' : '' }}>Inna</option>
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
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('address') @enderror">{{ old('address') }}</textarea>
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
                                       value="{{ old('emergency_contact') }}"
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
                                            onchange="toggleMedicalHistory()">
                                        <option value="">Wybierz rolę</option>
                                        <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                                        <option value="doctor" {{ old('role') === 'doctor' ? 'selected' : '' }}>Fizjoterapeuta</option>
                                        <option value="user" {{ old('role') === 'user' ? 'selected' : '' }}>Pacjent</option>
                                    </select>
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
                                               {{ old('is_active', '1') ? 'checked' : '' }}
                                               class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <label for="is_active" class="ml-2 text-sm text-gray-700">
                                            Konto aktywne
                                        </label>
                                    </div>
                                </div>


                                <div>
                                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                        Hasło <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="password"
                                               id="password"
                                               name="password"
                                               class="w-full px-3 py-2 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('password') @enderror"
                                               required>
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
                        </div>
                    </div>


                    <div id="medical-history-section" class="border-t border-gray-200 hidden">
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
                                          class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent @error('medical_history') @enderror">{{ old('medical_history') }}</textarea>
                                <p class="mt-1 text-sm text-gray-500">
                                    Wprowadź każdą informację w osobnej linii. Zostanie to zapisane jako lista.
                                </p>
                                @error('medical_history')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
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
                                    class="inline-flex justify-center items-center px-6 py-2 bg-indigo-600 text-white font-medium rounded-md hover:bg-indigo-700 transition-colors duration-150">
                                <i class="fas fa-user-plus mr-2"></i>
                                Utwórz użytkownika
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
            document.getElementById('medical_history').value = '';
        }
    }


    function updateAvatarPreview() {
        const firstname = document.getElementById('firstname').value.trim();
        const lastname = document.getElementById('lastname').value.trim();
        const avatarPreview = document.getElementById('avatar-preview');


        const avatarInput = document.getElementById('avatar-input');
        if (!avatarInput.files || avatarInput.files.length === 0) {
            let initials = 'N U';
            if (firstname || lastname) {
                const firstInitial = firstname ? firstname.charAt(0).toUpperCase() : 'N';
                const lastInitial = lastname ? lastname.charAt(0).toUpperCase() : 'U';
                initials = firstInitial + ' ' + lastInitial;
            }


            const colors = [
                '667eea', '764ba2', 'f093fb', 'f5576c', 'ffd89b',
                '96fbc4', '74b9ff', '0984e3', 'a29bfe', '6c5ce7',
                'fd79a8', 'e84393', 'ff7675', 'd63031', 'fab1a0',
                'e17055', 'fdcb6e', 'e84393', '00b894', '00cec9'
            ];
            const colorIndex = (firstname.length + lastname.length) % colors.length;
            const backgroundColor = colors[colorIndex];

            const avatarUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&size=200&background=${backgroundColor}&color=ffffff&bold=true&rounded=true`;
            avatarPreview.src = avatarUrl;
        }
    }


    document.getElementById('avatar-input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('avatar-preview').src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {

            updateAvatarPreview();
        }
    });


    document.addEventListener('DOMContentLoaded', function() {
        toggleMedicalHistory();
        updateAvatarPreview();
    });
</script>
@endsection
