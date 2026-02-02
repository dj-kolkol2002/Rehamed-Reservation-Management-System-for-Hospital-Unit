{{-- resources/views/chat/conversation.blade.php --}}
@extends('layouts.app')

@section('styles')
<style>
    /* Chat Conversation Styles */
    .chat-container {
        height: calc(100vh - 120px);
        max-height: 920px;
    }

    .chat-sidebar {
        border-right: 1px solid #e5e7eb;
        height: 100%;
        overflow-y: auto;
    }

    .chat-main {
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .messages-container {
        flex: 1;
        overflow-y: auto;
        padding: 1rem;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.02), rgba(118, 75, 162, 0.02));
    }

    .message {
        margin-bottom: 1rem;
        animation: fadeIn 0.3s ease-in;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .message-sent {
        text-align: right;
    }

    .message-received {
        text-align: left;
    }

    .message-bubble {
        display: inline-block;
        max-width: 75%;
        padding: 12px 16px;
        border-radius: 18px;
        word-wrap: break-word;
        position: relative;
    }

    .message-sent .message-bubble {
        background: linear-gradient(135deg, #667eea, #764ba2);
        color: white;
        border-bottom-right-radius: 4px;
    }

    .message-received .message-bubble {
        background: #f3f4f6;
        color: #1f2937;
        border: 1px solid #e5e7eb;
        border-bottom-left-radius: 4px;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .message-time {
        font-size: 0.75rem;
        color: #9ca3af;
        margin-top: 4px;
    }

    .message-sent .message-time {
        color: rgba(255, 255, 255, 0.8);
    }

    /* Dark Mode - Message bubbles */
    .dark-mode .message-received .message-bubble {
        background: #334155 !important;
        color: #e2e8f0 !important;
        border-color: #475569 !important;
    }

    .dark-mode .message-time {
        color: #94a3b8 !important;
    }

    .dark-mode .message-sent .message-time {
        color: rgba(255, 255, 255, 0.8) !important;
    }

    /* Emoji message styles */
    .emoji-message {
        font-size: 2rem;
        line-height: 1;
        padding: 8px 12px;
    }

    /* File message styles */
    .file-message {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .file-message:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-1px);
    }

    .file-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        background: rgba(255, 255, 255, 0.2);
        font-size: 1.25rem;
    }

    .file-info h4 {
        font-weight: 600;
        font-size: 0.875rem;
        margin-bottom: 2px;
    }

    .file-info p {
        font-size: 0.75rem;
        opacity: 0.8;
    }

    /* Image message styles */
    .image-message {
        max-width: 300px;
        border-radius: 12px;
        overflow: hidden;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .image-message:hover {
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .image-message img {
        width: 100%;
        height: auto;
        display: block;
    }

    /* Conversation item styles */
    .conversation-item {
        transition: all 0.2s ease;
        cursor: pointer;
        border-left: 3px solid transparent;
    }

    .conversation-item:hover {
        background: rgba(102, 126, 234, 0.05);
        border-left-color: rgba(102, 126, 234, 0.3);
    }

    .conversation-item.active {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.1), rgba(118, 75, 162, 0.1));
        border-left-color: #667eea;
    }

    .conversation-item.unread {
        background: rgba(59, 130, 246, 0.03);
        border-left-color: #3b82f6;
    }

    .unread-badge {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        min-width: 20px;
        height: 20px;
    }

    .user-card {
        background: #e5e7eb;
        border: 1px solid #d1d5db;
        transition: all 0.2s ease;
    }

    .user-card:hover {
        background: #d1d5db;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
    }

    .dark-mode .user-card {
        background: #334155 !important;
        border: 1px solid #475569 !important;
        color: #e2e8f0 !important;
    }

    .dark-mode .user-card:hover {
        background: #475569 !important;
        border-color: #667eea !important;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25) !important;
    }

    .dark-mode .user-card h4 {
        color: #e2e8f0 !important;
    }

    .dark-mode .user-card p {
        color: #cbd5e1 !important;
    }

    .dark-mode .user-card i {
        color: #667eea !important;
    }

    /* Message input area styles */
    .message-form-container {
        position: relative;
        border-top: 1px solid #e5e7eb;
        background: white;
        padding: 1rem;
    }

    .input-wrapper {
        display: flex;
        align-items: end;
        gap: 12px;
        background: transparent;
        border: 2px solid #e5e7eb;
        border-radius: 24px;
        padding: 12px 16px;
        transition: all 0.3s ease;
    }

    .input-wrapper:focus-within {
        /* Highlight removed */
    }    .message-input {
        flex: 1;
        background: transparent;
        border: none;
        padding: 0;
        transition: all 0.3s ease;
        resize: none;
        max-height: 120px;
        font-family: inherit;
        color: #1f2937;
        outline: none;
    }

    .message-input::placeholder {
        color: #9ca3af;
    }

    .input-tools {
        display: flex;
        align-items: center;
        gap: 8px;
        position: relative;
    }

    .tool-button {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6b7280;
        transition: all 0.2s ease;
        cursor: pointer;
        border: none;
        background: none;
    }

    .tool-button:hover {
        background: rgba(102, 126, 234, 0.1);
        color: #667eea;
        transform: scale(1.05);
    }

    .tool-button.active {
        background: rgba(102, 126, 234, 0.2);
        color: #667eea;
    }

    .send-button {
        background: linear-gradient(135deg, #667eea, #764ba2);
        border-radius: 50%;
        width: 48px;
        height: 48px;
        border: none;
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.3s ease;
        flex-shrink: 0;
    }

    .send-button:hover:not(:disabled) {
        transform: scale(1.05);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
    }

    .send-button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
        transform: none;
    }

    /* Emoji picker styles */
    .emoji-picker {
        position: absolute;
        bottom: 100%;
        left: 0;
        margin-bottom: 8px;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
        padding: 16px;
        max-height: 300px;
        width: 300px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
    }

    .emoji-picker.show {
        display: block;
    }

    .emoji-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 8px;
    }

    .emoji-item {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 1.25rem;
    }

    .emoji-item:hover {
        background: rgba(102, 126, 234, 0.1);
        transform: scale(1.1);
    }

    /* File preview styles */
    .file-preview {
        position: absolute;
        bottom: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 12px 12px 0 0;
        padding: 16px;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.1);
        display: none;
        z-index: 999;
    }

    .file-preview.show {
        display: block;
    }

    /* File drop zone styles */
    .file-drop-zone {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255, 255, 255, 0.95);
        z-index: 9999;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(8px);
    }

    .file-drop-zone.active {
        display: flex;
    }

    .file-drop-zone .drop-area {
        border: 3px dashed #667eea;
        border-radius: 20px;
        padding: 60px;
        text-align: center;
        background: rgba(102, 126, 234, 0.05);
        max-width: 500px;
        width: 90%;
    }

    /* Lightbox styles */
    .image-lightbox {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        z-index: 10000;
        display: none;
        align-items: center;
        justify-content: center;
        backdrop-filter: blur(4px);
    }

    .lightbox-image {
        max-width: 90%;
        max-height: 90%;
        object-fit: contain;
        border-radius: 8px;
    }

    .lightbox-close {
        position: absolute;
        top: 20px;
        right: 20px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        width: 50px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
        backdrop-filter: blur(8px);
    }

    .lightbox-close:hover {
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1.05);
    }

    /* Mobile styles */
    @media (max-width: 768px) {
        .chat-container {
            height: calc(100vh - 140px);
            max-height: none;
        }

        .chat-sidebar {
            position: absolute;
            left: -100%;
            top: 0;
            width: 100%;
            height: 100%;
            background: white;
            z-index: 1000;
            transition: left 0.3s ease-in-out;
        }

        .chat-sidebar.open {
            left: 0;
        }

        .messages-container {
            padding: 0.75rem 1rem;
        }

        .message-form-container {
            padding: 0.75rem 1rem;
        }

    /* Extra small phones */
    @media (max-width: 390px) {
        .message-bubble {
            max-width: 85%;
            font-size: 0.85rem;
            padding: 8px 12px;
        }

        .emoji-grid {
            grid-template-columns: repeat(5, 1fr);
        }

        .tool-button {
            width: 32px;
            height: 32px;
        }

        .send-button {
            width: 38px;
            height: 38px;
        }

        .file-message {
            padding: 8px;
            gap: 8px;
        }

        .file-icon {
            width: 32px;
            height: 32px;
            font-size: 1rem;
        }

        .file-info h4 {
            font-size: 0.8rem;
        }

        .file-info p {
            font-size: 0.7rem;
        }
    }

    /* Custom Scrollbar */
    .chat-sidebar::-webkit-scrollbar,
    .messages-container::-webkit-scrollbar,
    .emoji-picker::-webkit-scrollbar {
        width: 6px;
    }

    .chat-sidebar::-webkit-scrollbar-track,
    .messages-container::-webkit-scrollbar-track,
    .emoji-picker::-webkit-scrollbar-track {
        background: #f1f5f9;
    }

    .chat-sidebar::-webkit-scrollbar-thumb,
    .messages-container::-webkit-scrollbar-thumb,
    .emoji-picker::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .chat-sidebar::-webkit-scrollbar-thumb:hover,
    .messages-container::-webkit-scrollbar-thumb:hover,
    .emoji-picker::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* Dark Mode Styles */
    .dark-mode {
        color-scheme: dark;
    }

    .dark-mode .chat-container {
        background: #1e293b;
    }

    .dark-mode .chat-sidebar {
        background: #1e293b;
        border-color: #334155;
    }

    .dark-mode .messages-container {
        background: linear-gradient(135deg, rgba(15, 23, 42, 0.8), rgba(30, 41, 59, 0.8));
    }

    .dark-mode .message-received .message-bubble {
        background: #334155;
        color: #e2e8f0;
        border-color: #475569;
    }

    .dark-mode .conversation-item {
        color: #e2e8f0;
        border-color: #334155 !important;
    }

    .dark-mode .conversation-item:hover {
        background: rgba(102, 126, 234, 0.15);
        border-left-color: rgba(102, 126, 234, 0.5);
    }

    .dark-mode .conversation-item.active {
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
        border-left-color: #667eea;
    }

    .dark-mode .conversation-item.unread {
        background: rgba(59, 130, 246, 0.1);
    }

    /* User cards for new conversations */
    .dark-mode .user-card {
        background: #334155 !important;
        border: 1px solid #475569 !important;
        color: #e2e8f0 !important;
    }

    .dark-mode .user-card:hover {
        background: #475569 !important;
        border-color: #667eea !important;
        transform: translateY(-1px) !important;
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.25) !important;
    }

    .dark-mode .user-card h4 {
        color: #e2e8f0 !important;
    }

    .dark-mode .user-card p {
        color: #cbd5e1 !important;
    }

    .dark-mode .user-card i {
        color: #667eea !important;
    }

    /* Message Input Area */
    .dark-mode .message-form-container {
        background: #1e293b !important;
        border-top-color: #334155 !important;
    }

    .dark-mode .input-wrapper {
        background: #334155 !important;
        border-color: #475569 !important;
    }

    .dark-mode .input-wrapper:focus-within {
        border-color: #667eea !important;
        background: #334155 !important;
    }

    .dark-mode .message-input {
        color: #e2e8f0 !important;
        background: transparent !important;
    }

    .dark-mode .message-input::placeholder {
        color: #94a3b8 !important;
    }

    .dark-mode .tool-button {
        color: #94a3b8 !important;
        background: transparent !important;
    }

    .dark-mode .tool-button:hover {
        background: rgba(102, 126, 234, 0.2) !important;
        color: #a78bfa !important;
    }

    .dark-mode .tool-button.active {
        background: rgba(102, 126, 234, 0.3) !important;
        color: #667eea !important;
    }

    .dark-mode .send-button {
        background: linear-gradient(135deg, #667eea, #764ba2) !important;
    }

    /* Emoji and File Pickers */
    .dark-mode .emoji-picker {
        background: #1e293b !important;
        border-color: #334155 !important;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5) !important;
    }

    .dark-mode .emoji-item:hover {
        background: rgba(102, 126, 234, 0.2) !important;
    }

    .dark-mode .file-preview {
        background: #1e293b !important;
        border-color: #334155 !important;
    }

    .dark-mode .file-preview .bg-gray-50 {
        background: #334155 !important;
    }

    .dark-mode .file-preview .bg-blue-100 {
        background: #1e3a8a !important;
    }

    .dark-mode .file-preview .text-gray-900 {
        color: #e2e8f0 !important;
    }

    .dark-mode .file-preview .text-gray-500 {
        color: #94a3b8 !important;
    }

    .dark-mode .file-preview .text-gray-700 {
        color: #cbd5e1 !important;
    }

    .dark-mode .file-preview .border-gray-200 {
        border-color: #475569 !important;
    }

    /* File Drop Zone */
    .dark-mode .file-drop-zone {
        background: rgba(15, 23, 42, 0.95) !important;
    }

    .dark-mode .file-drop-zone .drop-area {
        border-color: #667eea !important;
        background: rgba(102, 126, 234, 0.1) !important;
        color: #e2e8f0 !important;
    }

    .dark-mode .file-drop-zone h3 {
        color: #e2e8f0 !important;
    }

    .dark-mode .file-drop-zone p {
        color: #cbd5e1 !important;
    }

    /* Image Lightbox */
    .dark-mode .image-lightbox {
        background: rgba(0, 0, 0, 0.95) !important;
    }

    .dark-mode .lightbox-close {
        background: rgba(255, 255, 255, 0.2) !important;
        color: white !important;
    }

    .dark-mode .lightbox-close:hover {
        background: rgba(255, 255, 255, 0.3) !important;
    }

    /* Loading Dialog */
    .dark-mode #chat-loading {
        background: rgba(15, 23, 42, 0.95) !important;
    }

    .dark-mode #chat-loading .bg-white {
        background: #1e293b !important;
        border: 1px solid #334155 !important;
    }

    .dark-mode #chat-loading .text-gray-700 {
        color: #e2e8f0 !important;
    }

    /* Scrollbar */
    .dark-mode .chat-sidebar::-webkit-scrollbar-track,
    .dark-mode .messages-container::-webkit-scrollbar-track,
    .dark-mode .emoji-picker::-webkit-scrollbar-track {
        background: #0f172a;
    }

    .dark-mode .chat-sidebar::-webkit-scrollbar-thumb,
    .dark-mode .messages-container::-webkit-scrollbar-thumb,
    .dark-mode .emoji-picker::-webkit-scrollbar-thumb {
        background: #334155;
    }

    .dark-mode .chat-sidebar::-webkit-scrollbar-thumb:hover,
    .dark-mode .messages-container::-webkit-scrollbar-thumb:hover,
    .dark-mode .emoji-picker::-webkit-scrollbar-thumb:hover {
        background: #475569;
    }

    /* Mobile Header */
    .dark-mode .mobile-chat-header {
        background: #1e293b !important;
        border-bottom-color: #334155 !important;
    }

    .dark-mode .chat-sidebar.open {
        background: #1e293b !important;
    }

    .dark-mode .chat-overlay.open {
        background: rgba(0, 0, 0, 0.7) !important;
    }

    /* Generic text and border overrides */
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
</style>
@endsection

@section('content')
<div class="min-h-screen flex items-center justify-center p-2 sm:p-4 bg-gray-100 dark:bg-gray-900">
    <div class="w-full max-w-full">
        <div class="md:hidden mb-4">
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-4">
            <div class="flex items-center justify-between">
                <button onclick="toggleChatSidebar()" class="p-2 text-gray-600 dark:text-gray-400 hover:text-indigo-600 dark:hover:text-indigo-400">
                    <i class="fas fa-bars text-lg"></i>
                </button>
                @if($otherUser)
                <div class="flex items-center space-x-3">
                    <img src="{{ $otherUser->avatar_url }}"
                         alt="{{ $otherUser->full_name }}"
                         class="w-8 h-8 rounded-full object-cover">
                    <div>
                        <h1 class="text-lg font-bold text-gray-900 dark:text-white">{{ $otherUser->full_name }}</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $otherUser->role_display }}</p>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Chat Container -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl chat-container overflow-hidden h-[680px] md:h-[820px]">
        <div class="flex h-full relative">
            <!-- Sidebar - Conversations List -->
            <div class="chat-sidebar w-full md:w-1/3 lg:w-1/4 border-r border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                <!-- Desktop Header -->
                <div class="hidden md:block p-4 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-base font-semibold text-gray-900 dark:text-white flex items-center">
                        <i class="fas fa-comments mr-2 text-indigo-600 dark:text-indigo-400 text-sm"></i>
                        Konwersacje
                    </h2>
                </div>

                <!-- Mobile Header -->
                <div class="md:hidden mobile-chat-header border-b border-gray-200 dark:border-gray-700 p-3">
                    <div class="flex items-center justify-between">
                        <h2 class="text-base font-semibold text-gray-900 dark:text-white">Konwersacje</h2>
                        <button onclick="toggleChatSidebar()" class="p-1 text-gray-600 dark:text-gray-300 hover:text-indigo-600">
                            <i class="fas fa-times text-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Available Users to Chat -->
                @if($chatableUsers->count() > 0)
                <div class="border-b border-gray-200 dark:border-gray-700">
                    <div class="p-3">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-3">Rozpocznij nową konwersację</h3>
                        <div class="space-y-2">
                            @foreach($chatableUsers as $user)
                            <div class="user-card p-3 rounded-lg cursor-pointer transition-all duration-200"
                                 onclick="startConversation({{ $user->id }})">
                                <div class="flex items-center">
                                    <img src="{{ $user->avatar_url }}"
                                         alt="{{ $user->full_name }}"
                                         class="w-8 h-8 rounded-full object-cover mr-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-medium text-gray-900 truncate">
                                            {{ $user->full_name }}
                                        </h4>
                                        <p class="text-xs text-gray-500">
                                            {{ $user->role_display }}
                                        </p>
                                    </div>
                                    <i class="fas fa-plus text-indigo-600 text-sm"></i>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif

                <!-- Existing Conversations -->
                <div class="flex-1 overflow-y-auto custom-scrollbar">
                    @if($conversations->count() > 0)
                        @foreach($conversations as $conv)
                        @php
                            $otherParticipant = $conv->getOtherParticipant(Auth::id());
                            $lastMessage = $conv->lastMessage;
                            $unreadCount = $conv->unreadMessagesFor(Auth::id())->count();
                            $isActive = $conversation && $conv->id === $conversation->id;
                        @endphp
                        @if($otherParticipant)
                        <div class="conversation-item p-4 border-b border-gray-100 dark:border-gray-700 transition-all duration-200
                                    {{ $unreadCount > 0 ? 'unread' : '' }} {{ $isActive ? 'active' : '' }}"
                             onclick="openConversation({{ $conv->id }})">
                            <div class="flex items-center">
                                <img src="{{ $otherParticipant->avatar_url }}"
                                     alt="{{ $otherParticipant->full_name }}"
                                     class="w-12 h-12 rounded-full object-cover mr-3">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center justify-between mb-1">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $otherParticipant->full_name }}
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
                        @endforeach
                    @else
                        <div class="p-8 text-center">
                            <div class="text-gray-300 dark:text-gray-600 mb-3">
                                <i class="fas fa-comments text-4xl"></i>
                            </div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400 mb-1">Brak konwersacji</p>
                            @if($chatableUsers->count() > 0)
                            <p class="text-xs text-gray-400 dark:text-gray-500">Wybierz osobę powyżej, aby rozpocząć rozmowę</p>
                            @endif
                        </div>
                    @endif
                </div>
            </div>

            <!-- Main Chat Area -->
            <div class="chat-main flex-1 flex flex-col">
                @if($otherUser && $conversation)
                <!-- Chat Header -->
                <div class="hidden md:flex items-center p-4 border-b border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800">
                    <img src="{{ $otherUser->avatar_url }}" alt="{{ $otherUser->full_name }}" class="w-10 h-10 rounded-full object-cover mr-3">
                    <div class="flex-1 min-w-0">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $otherUser->full_name }}</h3>
                        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $otherUser->role_display }}</p>
                    </div>
                </div>

                <!-- Messages Container -->
                <div id="messages-container" class="messages-container flex-1">
                    @foreach($messages as $message)
                    <div class="message {{ $message->sender_id === Auth::id() ? 'message-sent' : 'message-received' }}"
                         data-message-id="{{ $message->id }}">
                        <div class="message-bubble">
                            @if($message->type === 'text')
                                <p>{{ $message->message }}</p>
                            @elseif($message->type === 'emoji')
                                <div class="emoji-message">{{ $message->message }}</div>
                            @elseif($message->type === 'image')
                                <div class="image-message" onclick="openLightbox('/chat/file/{{ $message->id }}/image')">
                                    <img src="/chat/file/{{ $message->id }}/thumbnail"
                                         alt="{{ $message->file_name ?: 'Obraz' }}"
                                         loading="lazy">
                                </div>
                                @if($message->message)
                                    <p class="mt-2">{{ $message->message }}</p>
                                @endif
                            @elseif($message->type === 'file')
                                <div class="file-message" onclick="window.open('/chat/file/{{ $message->id }}/download', '_blank')">
                                    <div class="file-icon">
                                        <i class="{{ $message->file_icon }}"></i>
                                    </div>
                                    <div class="file-info flex-1">
                                        <h4>{{ $message->file_name ?: 'Plik' }}</h4>
                                        <p>{{ $message->formatted_file_size ?: '' }}</p>
                                    </div>
                                    <div class="text-indigo-600">
                                        <i class="fas fa-download"></i>
                                    </div>
                                </div>
                                @if($message->message)
                                    <p class="mt-2">{{ $message->message }}</p>
                                @endif
                            @endif
                        </div>
                        <div class="message-time">
                            {{ $message->created_at->format('H:i') }}
                            @if($message->sender_id === Auth::id())
                                <i class="fas fa-check text-gray-400"></i>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Message Input Form -->
                <div class="message-form-container bg-white dark:bg-gray-800">
                    <!-- File Preview (hidden by default) -->
                    <div id="file-preview" class="file-preview">
                        <!-- Will be populated by JavaScript -->
                    </div>

                    <form id="message-form" class="input-wrapper">
                        <div class="input-tools">
                            <!-- Emoji Picker (hidden by default) -->
                            <div id="emoji-picker" class="emoji-picker">
                                <div id="emoji-grid" class="emoji-grid">
                                    <!-- Will be populated by JavaScript -->
                                </div>
                            </div>

                            <button type="button" id="emoji-button" class="tool-button" title="Dodaj emoji">
                                <i class="fas fa-smile"></i>
                            </button>
                            <label for="file-input" class="tool-button" title="Dodaj plik">
                                <i class="fas fa-paperclip"></i>
                                <input type="file" id="file-input" style="display: none;"
                                       accept="image/*,.pdf,.doc,.docx,.txt,.zip,.rar">
                            </label>
                        </div>
                        <textarea id="message-input"
                                  class="message-input"
                                  placeholder="Napisz wiadomość..."
                                  rows="1"></textarea>
                        <button type="submit" id="send-button" class="send-button" disabled>
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </form>
                </div>
                @else
                <!-- No conversation selected -->
                <div class="flex-1 flex items-center justify-center p-8 bg-gray-50 dark:bg-gray-900">
                    <div class="text-center">
                        <div class="text-gray-300 dark:text-gray-600 mb-6">
                            <i class="fas fa-comment-dots text-7xl"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-700 dark:text-gray-300 mb-3">Rozpocznij konwersację</h3>
                        <p class="text-gray-500 dark:text-gray-400 mb-2">Wybierz osobę z listy po lewej stronie lub rozpocznij nową rozmowę.</p>
                    </div>
                </div>
                @endif
            </div>

            <!-- Mobile Overlay -->
            <div class="chat-overlay" onclick="closeChatSidebar()"></div>
        </div>
    </div>
    </div>
</div>

<!-- File Drop Zone (hidden by default) -->
<div id="file-drop-zone" class="file-drop-zone">
    <div class="drop-area">
        <div class="text-indigo-600 mb-4">
            <i class="fas fa-cloud-upload-alt text-6xl"></i>
        </div>
        <h3 class="text-xl font-bold text-gray-900 mb-2">Upuść plik tutaj</h3>
        <p class="text-gray-600">lub kliknij aby wybrać plik</p>
    </div>
</div>

<!-- Image Lightbox (hidden by default) -->
<div id="image-lightbox" class="image-lightbox" onclick="closeLightbox()">
    <button class="lightbox-close" onclick="closeLightbox()">
        <i class="fas fa-times"></i>
    </button>
    <img class="lightbox-image" id="lightbox-image" src="" alt="">
</div>

<div id="chat-loading" class="fixed inset-0 bg-gray-600 bg-opacity-50 z-50 flex items-center justify-center" style="display: none;">
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 shadow-xl">
        <div class="flex items-center">
            <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mr-3"></div>
            <span class="text-gray-700 dark:text-gray-300">Wysyłanie...</span>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Chat conversation functionality
const conversationId = {{ $conversation->id ?? 'null' }};
const currentUserId = {{ Auth::id() }};
let lastMessageId = 0;
let isPolling = false;
let pollInterval = null;
let isSending = false;
let currentFile = null;
let emojis = [];

// Get last message ID from DOM
const messageElements = document.querySelectorAll('[data-message-id]');
if (messageElements.length > 0) {
    const lastElement = messageElements[messageElements.length - 1];
    lastMessageId = parseInt(lastElement.getAttribute('data-message-id'));
}

// DOM elements
const messageForm = document.getElementById('message-form');
const messageInput = document.getElementById('message-input');
const sendButton = document.getElementById('send-button');
const messagesContainer = document.getElementById('messages-container');
const filePreview = document.getElementById('file-preview');
const emojiPicker = document.getElementById('emoji-picker');
const emojiGrid = document.getElementById('emoji-grid');
const emojiButton = document.getElementById('emoji-button');
const fileDropZone = document.getElementById('file-drop-zone');

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    // Apply theme based on user preference
    const userPreferences = @json(Auth::user()->preferences_with_defaults);
    const userTheme = userPreferences.theme || 'light';

    // Najpierw usuÅ„ wszystkie klasy motywu
    document.body.classList.remove('dark-mode', 'dark');
    document.documentElement.classList.remove('dark-mode', 'dark');

    // ZnajdÅº wszystkie elementy z dark mode i usuÅ„
    document.querySelectorAll('.dark-mode, .dark').forEach(el => {
        el.classList.remove('dark-mode', 'dark');
    });

    // NastÄ™pnie zastosuj wybrany motyw
    if (userTheme === 'dark') {
        document.documentElement.classList.add('dark-mode');
        document.documentElement.classList.add('dark');
        document.body.classList.add('dark-mode');
        console.log('Chat: Zastosowano ciemny motyw w konwersacji');
    } else {
        console.log('Chat: Zastosowano jasny motyw w konwersacji');
    }

    if (conversationId) {
        setupMessageInput();
        setupFileHandling();
        setupEmojis();
        startPolling();
        scrollToBottom();
        markConversationAsRead();
    }
    hideLoading();
});

// Setup message input
function setupMessageInput() {
    if (!messageInput) return;

    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (!sendButton.disabled) {
                sendMessage();
            }
        }
    });

    messageInput.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = Math.min(this.scrollHeight, 120) + 'px';
        updateSendButton();
    });

    if (messageForm) {
        messageForm.addEventListener('submit', function(e) {
            e.preventDefault();
            sendMessage();
        });
    }
}

// Rest of the JavaScript functions remain the same as in the original file...
// (I'll include the essential ones for the fix)

// Open conversation (redirect to conversation page)
function openConversation(conversationId) {
    showLoading();
    window.location.href = `/chat/conversation/${conversationId}`;
}

// Start new conversation
function startConversation(userId) {
    showLoading();

    fetch('/chat/start', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({
            user_id: userId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.href = `/chat/conversation/${data.conversation_id}`;
        } else {
            hideLoading();
            showNotification('BÃ…â€šÃ„â€¦d: ' + (data.message || 'Nie udaÃ…â€šo siÃ„â„¢ rozpoczÃ„â€¦Ã„â€¡ konwersacji'), 'error');
        }
    })
    .catch(error => {
        console.error('Error starting conversation:', error);
        hideLoading();
        showNotification('WystÃ„â€¦piÃ…â€š bÃ…â€šÃ„â€¦d podczas rozpoczynania konwersacji', 'error');
    });
}

// Update send button state
function updateSendButton() {
    if (!sendButton || !messageInput) return;

    const hasText = messageInput.value.trim().length > 0;
    const hasFile = currentFile !== null;

    sendButton.disabled = !hasText && !hasFile;
}

// Send message function (simplified version)
async function sendMessage() {
    if (!conversationId || isSending) return;

    const message = messageInput ? messageInput.value.trim() : '';

    if (!message && !currentFile) return;

    isSending = true;
    showLoading();

    try {
        const formData = new FormData();
        formData.append('conversation_id', conversationId);

        if (currentFile) {
            formData.append('file', currentFile);
            formData.append('type', currentFile.type.startsWith('image/') ? 'image' : 'file');
            if (message) {
                formData.append('message', message);
            }
        } else {
            const emojiOnly = /^[\u{1F600}-\u{1F64F}]|[\u{1F300}-\u{1F5FF}]|[\u{1F680}-\u{1F6FF}]|[\u{1F1E0}-\u{1F1FF}]|[\u{2600}-\u{26FF}]|[\u{2700}-\u{27BF}]+$/u.test(message);
            formData.append('type', emojiOnly ? 'emoji' : 'text');
            formData.append('message', message);
        }

        const response = await fetch('/chat/send', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok && data.success) {
            addMessageToUI(data.message, true);
            if (messageInput) {
                messageInput.value = '';
                messageInput.style.height = 'auto';
            }
            removeFilePreview();
            scrollToBottom();
            lastMessageId = data.message.id;
        } else {
            showNotification('BÃ…â€šÃ„â€¦d: ' + (data.message || 'Nie udaÃ…â€šo siÃ„â„¢ wysÃ…â€šaÃ„â€¡ wiadomoÃ…â€ºci'), 'error');
        }
    } catch (error) {
        console.error('Error sending message:', error);
        showNotification('WystÃ„â€¦piÃ…â€š bÃ…â€šÃ„â€¦d podczas wysyÃ…â€šania wiadomoÃ…â€ºci', 'error');
    } finally {
        isSending = false;
        updateSendButton();
        hideLoading();
    }
}

// Mobile functions
function toggleChatSidebar() {
    const sidebar = document.querySelector('.chat-sidebar');
    const overlay = document.querySelector('.chat-overlay');

    if (sidebar && overlay) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('open');
    }
}

function closeChatSidebar() {
    const sidebar = document.querySelector('.chat-sidebar');
    const overlay = document.querySelector('.chat-overlay');

    if (sidebar && overlay) {
        sidebar.classList.remove('open');
        overlay.classList.remove('open');
    }
}

// Utility functions
function scrollToBottom() {
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

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

// Show notification
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = 'fixed top-4 right-4 px-6 py-3 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300';

    const bgColor = type === 'success' ? 'bg-green-500' :
                   type === 'error' ? 'bg-red-500' :
                   type === 'warning' ? 'bg-yellow-500' : 'bg-blue-500';

    notification.classList.add(bgColor, 'text-white');

    notification.innerHTML = `
        <div class="flex items-center justify-between">
            <span>${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="ml-4 text-white hover:text-gray-200">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => notification.classList.remove('translate-x-full'), 100);
    setTimeout(() => {
        notification.classList.add('translate-x-full');
        setTimeout(() => notification.remove(), 300);
    }, 5000);
}

// Lightbox functions
function openLightbox(imageSrc) {
    const lightbox = document.getElementById('image-lightbox');
    const lightboxImage = document.getElementById('lightbox-image');

    if (lightbox && lightboxImage) {
        lightboxImage.src = imageSrc;
        lightbox.style.display = 'flex';
    }
}

function closeLightbox() {
    const lightbox = document.getElementById('image-lightbox');
    if (lightbox) {
        lightbox.style.display = 'none';
    }
}

// Essential functions for message handling
function addMessageToUI(message, isNew = false) {
    if (!messagesContainer) return;

    const messageDiv = document.createElement('div');
    const isSent = message.sender_id === currentUserId;

    messageDiv.className = `message ${isSent ? 'message-sent' : 'message-received'}`;
    messageDiv.setAttribute('data-message-id', message.id);

    const timeString = new Date(message.created_at).toLocaleString('pl-PL', {
        hour: '2-digit',
        minute: '2-digit'
    });

    let contentHTML = '';

    if (message.type === 'text') {
        contentHTML = `<p>${escapeHtml(message.message)}</p>`;
    } else if (message.type === 'emoji') {
        contentHTML = `<div class="emoji-message">${escapeHtml(message.message)}</div>`;
    }
    // Add other message types as needed...

    messageDiv.innerHTML = `
        <div class="message-bubble">
            ${contentHTML}
        </div>
        <div class="message-time">
            ${timeString}
            ${isSent ? '<i class="fas fa-check text-gray-400"></i>' : ''}
        </div>
    `;

    messagesContainer.appendChild(messageDiv);

    if (isNew) {
        setTimeout(() => scrollToBottom(), 100);
    }
}

function escapeHtml(text) {
    const map = {
        '&': '&amp;',
        '<': '&lt;',
        '>': '&gt;',
        '"': '&quot;',
        "'": '&#039;'
    };
    return text.replace(/[&<>"']/g, function(m) { return map[m]; });
}

// Essential polling and file handling functions
function startPolling() {
    if (!conversationId || isPolling) return;

    isPolling = true;
    pollInterval = setInterval(async function() {
        try {
            const response = await fetch(`/chat/conversation/${conversationId}/messages?after=${lastMessageId}`);
            if (response.ok) {
                const data = await response.json();
                if (data.messages && data.messages.length > 0) {
                    data.messages.forEach(message => {
                        appendMessage(message, false);
                        lastMessageId = Math.max(lastMessageId, message.id);
                    });
                }
            }
        } catch (error) {
            console.error('Polling error:', error);
        }
    }, 3000);
}

function setupFileHandling() {
    const fileInput = document.getElementById('file-input');
    if (!fileInput) return;

    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            handleFileSelect(file);
        }
    });

    // Drag and drop support
    const container = document.querySelector('.input-wrapper');
    if (container) {
        container.addEventListener('dragover', function(e) {
            e.preventDefault();
            container.classList.add('drag-over');
        });

        container.addEventListener('dragleave', function(e) {
            e.preventDefault();
            container.classList.remove('drag-over');
        });

        container.addEventListener('drop', function(e) {
            e.preventDefault();
            container.classList.remove('drag-over');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleFileSelect(files[0]);
            }
        });
    }
}

function handleFileSelect(file) {
    currentFile = file;
    showFilePreview(file);
    updateSendButton();
}

function showFilePreview(file) {
    const preview = document.getElementById('file-preview');
    if (!preview) return;

    const isImage = file.type.startsWith('image/');
    const fileName = file.name;
    const fileSize = formatFileSize(file.size);

    preview.innerHTML = `
        <div class="bg-gray-50 rounded-lg p-3 flex items-center justify-between">
            <div class="flex items-center space-x-3">
                <div class="file-icon">
                    <i class="fas ${isImage ? 'fa-image' : 'fa-file'} text-indigo-600"></i>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-900">${fileName}</p>
                    <p class="text-xs text-gray-500">${fileSize}</p>
                </div>
            </div>
            <button type="button" onclick="removeFilePreview()" class="text-gray-400 hover:text-red-500">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;
    preview.style.display = 'block';
}

function removeFilePreview() {
    currentFile = null;
    const preview = document.getElementById('file-preview');
    if (preview) {
        preview.style.display = 'none';
        preview.innerHTML = '';
    }
    const fileInput = document.getElementById('file-input');
    if (fileInput) {
        fileInput.value = '';
    }
    updateSendButton();
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

// Emoji functionality
function setupEmojis() {
    if (!emojiButton || !emojiPicker || !emojiGrid) return;

    // Load emojis
    loadEmojis();

    // Toggle emoji picker
    emojiButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        toggleEmojiPicker();
    });

    // Close emoji picker when clicking outside
    document.addEventListener('click', function(e) {
        if (!emojiPicker.contains(e.target) && !emojiButton.contains(e.target)) {
            emojiPicker.classList.remove('show');
        }
    });
}

function loadEmojis() {
    // Basic emoji set
    emojis = [
        "😀", "😃", "😄", "😁", "😆", "😅", "😂", "🤣",
        "😊", "😇", "🙂", "🙃", "😉", "😌", "😍", "🥰",
        "😘", "😗", "😙", "😚", "😋", "😛", "😝", "😜",
        "🤪", "🤨", "🧐", "🤓", "😎", "🥸", "🤩", "🥳",
        "😏", "😒", "😞", "😔", "😟", "😕", "🙁", "☹️",
        "😣", "😖", "😫", "😩", "🥺", "😢", "😭", "😤",
        "😠", "😡", "🤬", "🤯", "😳", "🥵", "🥶", "😱",
        "😨", "😰", "😥", "😓", "🤗", "🤔", "🤭", "🤫",
        "🤥", "😶", "😐", "😑", "😬", "🙄", "😯", "😦",
        "😧", "😮", "😲", "🥱", "😴", "🤤", "😪", "😵",
        "🤐", "🥴", "🤢", "🤮", "🤧", "😷", "🤒", "🤕",
        "👍", "👎", "👌", "✌️", "🤞", "🤟", "🤘", "🤙",
        "👈", "👉", "👆", "👇", "☝️", "✋", "🤚", "🖐",
        "🖖", "👋", "🤝", "💪", "🦾", "🖕", "✍️", "🙏",
        "❤️", "🧡", "💛", "💚", "💙", "💜", "🖤", "🤍",
        "🤎", "💔", "❣️", "💕", "💞", "💓", "💗", "💖",
        "💘", "💝", "💟", "☮️", "✝️", "☪️", "🕉", "☸️"
    ];

    // Populate emoji grid
    emojiGrid.innerHTML = '';
    emojis.forEach(emoji => {
        const emojiElement = document.createElement('div');
        emojiElement.className = 'emoji-item';
        emojiElement.textContent = emoji;
        emojiElement.addEventListener('click', function() {
            insertEmoji(emoji);
        });
        emojiGrid.appendChild(emojiElement);
    });
}

function toggleEmojiPicker() {
    emojiPicker.classList.toggle('show');
}

function insertEmoji(emoji) {
    if (!messageInput) return;

    const start = messageInput.selectionStart;
    const end = messageInput.selectionEnd;
    const text = messageInput.value;

    messageInput.value = text.substring(0, start) + emoji + text.substring(end);
    messageInput.focus();
    messageInput.setSelectionRange(start + emoji.length, start + emoji.length);

    // Trigger input event
    messageInput.dispatchEvent(new Event('input'));

    // Close emoji picker
    emojiPicker.classList.remove('show');
}

function markConversationAsRead() {
    if (!conversationId) return;

    fetch(`/chat/conversation/${conversationId}/mark-read`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/json'
        }
    }).catch(error => {
        console.error('Error marking conversation as read:', error);
    });
}

function scrollToBottom() {
    if (messagesContainer) {
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    }
}

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

// Cleanup loading on page load
window.addEventListener('load', function() {
    hideLoading();
});
</script>
@endsection
