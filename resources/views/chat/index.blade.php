{{-- resources/views/chat/index.blade.php --}}

@extends('layouts.app')

@section('title', 'Wiadomości')

@section('styles')
<style>
/* Chat container */
.chat-container {
    height: calc(100vh - 120px);
    min-height: 600px;
}

/* Conversation items */
.conversation-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
    cursor: pointer;
}

.conversation-item:hover {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
    border-left-color: rgba(102, 126, 234, 0.5);
    transform: translateX(2px);
}

.conversation-item.active {
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.15), rgba(118, 75, 162, 0.15));
    border-left-color: #667eea;
}

.conversation-item.unread {
    background: rgba(59, 130, 246, 0.05);
    border-left-color: #3b82f6;
}

.conversation-item.unread:hover {
    background: rgba(59, 130, 246, 0.1);
}

/* User cards for starting new conversations */
.user-card {
    background: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(102, 126, 234, 0.2);
    backdrop-filter: blur(10px);
    transition: all 0.3s ease;
}

.user-card:hover {
    background: rgba(102, 126, 234, 0.1);
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
    border-color: rgba(102, 126, 234, 0.4);
}

/* Empty chat state */
.empty-chat {
    background: linear-gradient(135deg, #f8fafc, #e2e8f0);
    border: 2px dashed #cbd5e1;
}

/* Unread badge */
.unread-badge {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.7; }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .chat-container {
        height: calc(100vh - 100px);
        min-height: 500px;
    }

    .conversation-item {
        padding: 12px;
    }

    .user-card {
        padding: 12px;
    }
}

/* Avatar styling */
.avatar-circle {
    border: 2px solid rgba(102, 126, 234, 0.3);
    transition: border-color 0.3s ease;
}

.conversation-item:hover .avatar-circle,
.user-card:hover .avatar-circle {
    border-color: rgba(102, 126, 234, 0.6);
}

/* Light mode (default) - ensure text is visible */
body:not(.dark-mode) .conversation-item .text-gray-900,
body:not(.dark-mode) .user-card .text-gray-900 {
    color: #111827 !important;
}

body:not(.dark-mode) .conversation-item .text-gray-600,
body:not(.dark-mode) .user-card .text-gray-600 {
    color: #4b5563 !important;
}

body:not(.dark-mode) .conversation-item .text-gray-500,
body:not(.dark-mode) .user-card .text-gray-500 {
    color: #6b7280 !important;
}

body:not(.dark-mode) .conversation-item .text-gray-400,
body:not(.dark-mode) .user-card .text-gray-400 {
    color: #9ca3af !important;
}

body:not(.dark-mode) .user-card {
    background: #ffffff;
    border-color: #e5e7eb;
}

body:not(.dark-mode) .user-card:hover {
    background: rgba(102, 126, 234, 0.05);
}

body:not(.dark-mode) .conversation-item {
    background: #ffffff;
}

body:not(.dark-mode) .conversation-item:hover {
    background: #f9fafb;
}

/* Dark mode specific styles */
.dark-mode .chat-container {
    background: #1e293b;
}

.dark-mode .chat-sidebar {
    background: #1e293b;
    border-color: #334155;
}

.dark-mode .conversation-item:hover {
    background: rgba(102, 126, 234, 0.15);
}

.dark-mode .conversation-item.unread {
    background: rgba(59, 130, 246, 0.1);
}

.dark-mode .user-card {
    background: rgba(51, 65, 85, 0.5);
    border: 1px solid rgba(102, 126, 234, 0.3);
}

.dark-mode .user-card:hover {
    background: rgba(102, 126, 234, 0.2);
}

.dark-mode .empty-chat {
    background: #0f172a;
}

.dark-mode .text-gray-900 {
    color: #e2e8f0 !important;
}

.dark-mode .text-gray-600 {
    color: #cbd5e1 !important;
}

.dark-mode .text-gray-500 {
    color: #94a3b8 !important;
}

.dark-mode .text-gray-400 {
    color: #64748b !important;
}

.dark-mode .border-gray-200 {
    border-color: #334155 !important;
}

.dark-mode .border-gray-100 {
    border-color: #1e293b !important;
}

/* Animations */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.conversation-item {
    animation: slideIn 0.3s ease-out;
}
</style>
@endsection

@section('scripts')
<script>

// Chat functionality
let isPolling = false;
let pollInterval = null;

// Apply theme based on user preference
document.addEventListener('DOMContentLoaded', function() {
    const userPreferences = @json(Auth::user()->preferences_with_defaults);
    const userTheme = userPreferences.theme || 'light';

    // Najpierw usuń wszystkie klasy motywu z całej strony
    document.body.classList.remove('dark-mode', 'dark');
    document.documentElement.classList.remove('dark-mode', 'dark');

    // Znajdź wszystkie elementy z dark mode i usuń
    document.querySelectorAll('.dark-mode, .dark').forEach(el => {
        el.classList.remove('dark-mode', 'dark');
    });

    // Następnie zastosuj wybrany motyw
    if (userTheme === 'dark') {
        document.documentElement.classList.add('dark-mode');
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark-mode');
        console.log('Chat: Zastosowano ciemny motyw');
    } else {
        console.log('Chat: Zastosowano jasny motyw');
    }
});

// Open conversation (redirect to conversation page)
function openConversation(conversationId) {
    showLoading();
    window.location.href = `/chat/conversation/${conversationId}`;
}

// Start new conversation
function startConversation(userId) {
    showLoading();
    // Redirect to the start conversation route - it will create or find the conversation and redirect
    window.location.href = `/chat/start/${userId}`;
}

// Loading functions
function showLoading() {
    const loading = document.getElementById('chat-loading');
    if (loading) {
        loading.style.display = 'flex';
    }
}

function hideLoading() {
    const loading = document.getElementById('chat-loading');
    if (loading) {
        loading.style.display = 'none';
    }
}

// Notification function
function showNotification(message, type = 'info') {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `fixed top-20 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm ${
        type === 'error' ? 'bg-red-500 text-white' :
        type === 'success' ? 'bg-green-500 text-white' :
        'bg-blue-500 text-white'
    }`;
    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    hideLoading();

    // Search conversations
    const searchConversations = document.getElementById('searchConversations');
    if (searchConversations) {
        searchConversations.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const conversationItems = document.querySelectorAll('.conversation-item');

            conversationItems.forEach(item => {
                const userName = item.getAttribute('data-user-name') || '';
                if (userName.includes(searchTerm)) {
                    item.style.display = '';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }

    // Search users
    const searchUsers = document.getElementById('searchUsers');
    if (searchUsers) {
        searchUsers.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const userCards = document.querySelectorAll('.user-card');

            userCards.forEach(card => {
                const userName = card.getAttribute('data-user-name') || '';
                const userRole = card.getAttribute('data-user-role') || '';
                if (userName.includes(searchTerm) || userRole.includes(searchTerm)) {
                    card.style.display = '';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    }
});
</script>
@endsection

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <h1 class="text-3xl font-bold text-gray-900 mb-2">
            <i class="fas fa-comments mr-3 text-indigo-600"></i>
            Wiadomości
        </h1>
        <p class="text-gray-600">Zarządzaj swoimi konwersacjami</p>
    </div>

    <!-- Chat Container -->
    <div class="bg-white rounded-2xl shadow-lg chat-container overflow-hidden">
        <div class="flex h-full">
            <!-- Conversations List -->
            <div class="w-full md:w-1/2 lg:w-1/3 border-r border-gray-200 flex flex-col">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 flex items-center justify-between mb-3">
                        <span>
                            <i class="fas fa-inbox mr-2 text-indigo-600"></i>
                            Konwersacje
                        </span>
                        @if($conversations->count() > 0)
                        <span class="text-sm bg-indigo-100 text-indigo-700 px-2 py-1 rounded-full">
                            {{ $conversations->count() }}
                        </span>
                        @endif
                    </h2>
                    <!-- Search Conversations -->
                    <div class="relative">
                        <input type="text"
                               id="searchConversations"
                               placeholder="Szukaj konwersacji..."
                               class="w-full px-3 py-2 pl-9 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>
                </div>

                <!-- Conversations -->
                <div class="flex-1 overflow-y-auto">
                    @forelse($conversations as $conversation)
                        @php
                            $otherUser = $conversation->getOtherParticipant(Auth::id());
                            $lastMessage = $conversation->lastMessage;
                            $unreadCount = $conversation->getUnreadCountFor(Auth::id());
                        @endphp
                        @if($otherUser && !$otherUser->trashed())
                        <div class="conversation-item p-4 border-b border-gray-100 {{ $unreadCount > 0 ? 'unread' : '' }}"
                             data-user-name="{{ strtolower($otherUser->full_name) }}"
                             onclick="openConversation({{ $conversation->id }})">
                            <div class="flex items-start space-x-3">
                                <div class="relative">
                                    <img src="{{ $otherUser->avatar_url }}"
                                         alt="{{ $otherUser->full_name }}"
                                         class="w-12 h-12 rounded-full object-cover avatar-circle">
                                    @if($otherUser->is_active ?? true)
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $otherUser->full_name }}
                                        </h3>
                                        @if($unreadCount > 0)
                                        <span class="unread-badge bg-indigo-600 text-white text-xs font-bold rounded-full px-2 py-1 ml-2">
                                            {{ $unreadCount }}
                                        </span>
                                        @endif
                                    </div>
                                    @if($lastMessage)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 truncate {{ $unreadCount > 0 ? 'font-medium' : '' }}">
                                        @if($lastMessage->sender_id === Auth::id())
                                            <span class="text-gray-500 dark:text-gray-500">Ty:</span>
                                        @endif
                                        {{ Str::limit($lastMessage->message, 40) }}
                                    </p>
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-1">
                                        {{ $lastMessage->created_at->diffForHumans() }}
                                    </p>
                                    @else
                                    <p class="text-xs text-gray-400 dark:text-gray-500 italic">Brak wiadomości</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endif
                    @empty
                        <div class="p-6 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-comments text-6xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Brak konwersacji</h3>
                            <p class="text-gray-600 mb-4">Rozpocznij pierwszą konwersację wybierając osobę z prawej strony.</p>
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- New Conversation / Users List -->
            <div class="hidden md:flex flex-1 flex-col">
                <!-- Header -->
                <div class="p-4 border-b border-gray-200 bg-gray-50">
                    <h2 class="text-lg font-semibold text-gray-900 mb-3">
                        <i class="fas fa-user-plus mr-2 text-green-600"></i>
                        Rozpocznij konwersację
                    </h2>
                    <!-- Search Users -->
                    <div class="relative">
                        <input type="text"
                               id="searchUsers"
                               placeholder="Szukaj użytkowników..."
                               class="w-full px-3 py-2 pl-9 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                        <i class="fas fa-search absolute left-3 top-3 text-gray-400 text-sm"></i>
                    </div>
                </div>

                <!-- Available Users -->
                <div class="flex-1 overflow-y-auto p-4">
                    @forelse($chatableUsers as $user)
                        <div class="user-card p-4 rounded-xl mb-3 cursor-pointer"
                             data-user-name="{{ strtolower($user->full_name) }}"
                             data-user-role="{{ $user->role_display ?? '' }}"
                             onclick="startConversation({{ $user->id }})">
                            <div class="flex items-center space-x-3">
                                <div class="relative">
                                    <img src="{{ $user->avatar_url }}"
                                         alt="{{ $user->full_name }}"
                                         class="w-10 h-10 rounded-full object-cover avatar-circle">
                                    @if($user->isActive ?? true)
                                        <div class="absolute bottom-0 right-0 w-3 h-3 bg-green-400 border-2 border-white rounded-full"></div>
                                    @endif
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-gray-900">{{ $user->full_name }}</h3>
                                    <p class="text-xs text-gray-500">{{ $user->role_display }}</p>
                                </div>
                                <div class="text-gray-400">
                                    <i class="fas fa-plus-circle text-lg"></i>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="empty-chat rounded-xl p-8 text-center">
                            <div class="text-gray-400 mb-4">
                                <i class="fas fa-users text-6xl"></i>
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 mb-2">Brak dostępnych użytkowników</h3>
                            <p class="text-gray-600">Aktualnie nie ma użytkowników z którymi możesz rozpocząć konwersację.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="chat-loading" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
    <div class="bg-white rounded-lg p-6 shadow-xl">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mr-3"></div>
            <span class="text-gray-700">Ładowanie...</span>
        </div>
    </div>
</div>
@endsection
