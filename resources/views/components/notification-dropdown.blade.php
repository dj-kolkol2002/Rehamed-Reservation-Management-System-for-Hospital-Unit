{{-- resources/views/components/notification-dropdown.blade.php --}}
<div class="relative">
    <!-- Przycisk powiadomień -->
    <button
        id="notification-bell"
        class="relative p-2 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 rounded-full transition-colors"
        aria-label="Powiadomienia"
        aria-expanded="false"
        aria-haspopup="true"
    >
        <i class="fas fa-bell text-lg"></i>
        <!-- Badge z liczbą nieprzeczytanych -->
        <span
            id="notification-count"
            class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-bold animate-pulse"
            aria-label="Nieprzeczytane powiadomienia"
        >
            0
        </span>
    </button>

    <!-- Dropdown powiadomień -->
    <div
        id="notification-dropdown"
        class="absolute right-0 mt-2 w-96 bg-white rounded-lg shadow-lg border border-gray-200 z-50 max-h-96 flex flex-col"
        role="menu"
        aria-orientation="vertical"
        aria-labelledby="notification-bell"
    >
        <!-- Header -->
        <div class="px-4 py-3 border-b border-gray-200 bg-gray-50 rounded-t-lg">
            <div class="flex items-center justify-between">
                <h3 class="text-sm font-semibold text-gray-900">Powiadomienia</h3>
                <div class="flex space-x-2">
                    <button
                        id="mark-all-read-btn"
                        class="text-xs text-indigo-600 hover:text-indigo-800 font-medium"
                        title="Oznacz wszystkie jako przeczytane"
                    >
                        <i class="fas fa-check-double mr-1"></i>
                        Oznacz wszystkie
                    </button>
                    <button
                        id="clear-read-btn"
                        class="text-xs text-gray-500 hover:text-red-600 font-medium"
                        title="Usuń przeczytane"
                    >
                        <i class="fas fa-trash mr-1"></i>
                        Wyczyść
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="px-4 py-2 border-b border-gray-100 bg-gray-50">
            <div class="flex space-x-1">
                <button class="notification-tab active px-3 py-1 text-xs font-medium rounded-md bg-indigo-100 text-indigo-700" data-tab="all">
                    Wszystkie
                </button>
                <button class="notification-tab px-3 py-1 text-xs font-medium rounded-md text-gray-600 hover:bg-gray-100" data-tab="appointments">
                    Wizyty
                </button>
                <button class="notification-tab px-3 py-1 text-xs font-medium rounded-md text-gray-600 hover:bg-gray-100" data-tab="documents">
                    Dokumenty
                </button>
                <button class="notification-tab px-3 py-1 text-xs font-medium rounded-md text-gray-600 hover:bg-gray-100" data-tab="messages">
                    Wiadomości
                </button>
            </div>
        </div>

        <!-- Lista powiadomień -->
        <div class="flex-1 overflow-y-auto">
            <div id="notification-list" class="divide-y divide-gray-100">
                <!-- Powiadomienia będą ładowane przez JavaScript -->
                <div class="p-4 text-center text-gray-500" id="loading-notifications">
                    <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-indigo-600 mx-auto"></div>
                    <p class="mt-2 text-sm">Ładowanie powiadomień...</p>
                </div>

                <!-- Placeholder gdy brak powiadomień -->
                <div class="p-4 text-center text-gray-500 hidden" id="no-notifications">
                    <i class="fas fa-bell-slash text-3xl mb-2"></i>
                    <p class="text-sm">Brak powiadomień</p>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="px-4 py-3 border-t border-gray-200 bg-gray-50 rounded-b-lg">
            <div class="flex items-center justify-between">
                <button
                    id="load-more-notifications"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium hidden"
                >
                    <i class="fas fa-chevron-down mr-1"></i>
                    Załaduj więcej
                </button>
                <a
                    href="{{ route('notifications.index') }}"
                    class="text-sm text-indigo-600 hover:text-indigo-800 font-medium ml-auto"
                >
                    Zobacz wszystkie
                    <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.notification-item {
    transition: all 0.2s ease;
}

.notification-item:hover {
    background-color: #f9fafb;
}

.notification-item.unread {
    background-color: #eff6ff;
    border-left: 3px solid #3b82f6;
}

.notification-tab.active {
    background-color: #e0e7ff;
    color: #3730a3;
}

.notification-tab:not(.active):hover {
    background-color: #f3f4f6;
}

#notification-dropdown {
    max-width: 90vw;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
}

@media (max-width: 640px) {
    #notification-dropdown {
        width: 90vw;
        right: -10px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let currentTab = 'all';
    let notificationsCache = {};
    let isDropdownOpen = false;

    const bell = document.getElementById('notification-bell');
    const dropdown = document.getElementById('notification-dropdown');
    const countBadge = document.getElementById('notification-count');
    const notificationList = document.getElementById('notification-list');
    const loadingElement = document.getElementById('loading-notifications');
    const noNotificationsElement = document.getElementById('no-notifications');
    const markAllReadBtn = document.getElementById('mark-all-read-btn');
    const clearReadBtn = document.getElementById('clear-read-btn');
    const tabs = document.querySelectorAll('.notification-tab');

    // Toggle dropdown
    bell.addEventListener('click', function(e) {
        e.stopPropagation();
        isDropdownOpen = !isDropdownOpen;

        if (isDropdownOpen) {
            dropdown.classList.remove('hidden');
            loadNotifications();
        } else {
            dropdown.classList.add('hidden');
        }
    });

    // Zamknij dropdown po kliknięciu poza nim
    document.addEventListener('click', function(e) {
        if (!dropdown.contains(e.target) && !bell.contains(e.target)) {
            dropdown.classList.add('hidden');
            isDropdownOpen = false;
        }
    });

    // Tab switching
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            const tabType = this.dataset.tab;
            switchTab(tabType);
        });
    });

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        markAllAsRead();
    });

    // Clear read notifications
    clearReadBtn.addEventListener('click', function() {
        clearReadNotifications();
    });

    function switchTab(tabType) {
        currentTab = tabType;

        // Update tab appearance
        tabs.forEach(tab => {
            if (tab.dataset.tab === tabType) {
                tab.classList.add('active');
                tab.classList.remove('text-gray-600', 'hover:bg-gray-100');
            } else {
                tab.classList.remove('active');
                tab.classList.add('text-gray-600', 'hover:bg-gray-100');
            }
        });

        loadNotifications();
    }

    function loadNotifications() {
        showLoading();

        fetch('/notifications/api/get', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            hideLoading();
            notificationsCache[currentTab] = data.notifications;
            updateNotificationCount(data.unread_count);
            renderNotifications(filterNotifications(data.notifications));
        })
        .catch(error => {
            console.error('Error loading notifications:', error);
            hideLoading();
            showError('Błąd podczas ładowania powiadomień');
        });
    }

    function filterNotifications(notifications) {
        if (currentTab === 'all') {
            return notifications;
        }

        const typeMap = {
            'appointments': ['appointment_created', 'appointment_updated', 'appointment_cancelled', 'appointment_reminder'],
            'documents': ['document_created', 'document_updated'],
            'messages': ['message_received']
        };

        return notifications.filter(notification =>
            typeMap[currentTab] && typeMap[currentTab].includes(notification.type)
        );
    }

    function renderNotifications(notifications) {
        if (notifications.length === 0) {
            showNoNotifications();
            return;
        }

        const html = notifications.map(notification => createNotificationHTML(notification)).join('');
        notificationList.innerHTML = html;

        // Add click handlers
        document.querySelectorAll('.notification-item').forEach(item => {
            item.addEventListener('click', function() {
                const notificationId = this.dataset.id;
                const actionUrl = this.dataset.url;

                if (!this.classList.contains('read')) {
                    markAsRead(notificationId);
                }

                if (actionUrl && actionUrl !== '#') {
                    window.location.href = actionUrl;
                }
            });
        });
    }

    function createNotificationHTML(notification) {
        const isUnread = !notification.is_read;
        const isNew = notification.is_new;

        return `
            <div class="notification-item ${isUnread ? 'unread' : 'read'} p-3 cursor-pointer hover:bg-gray-50 transition-colors"
                 data-id="${notification.id}"
                 data-url="${notification.action_url}">
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-${notification.color}-100 flex items-center justify-center">
                            <i class="${notification.icon} text-${notification.color}-600 text-sm"></i>
                        </div>
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-900 truncate flex items-center">
                                ${notification.title}
                                ${isNew ? '<span class="ml-2 inline-block w-2 h-2 bg-red-500 rounded-full"></span>' : ''}
                            </h4>
                            <span class="text-xs text-gray-500 ml-2">${notification.formatted_time}</span>
                        </div>

                        <p class="text-sm text-gray-600 mt-1 line-clamp-2">${notification.message}</p>

                        <div class="flex items-center justify-between mt-2">
                            <span class="text-xs px-2 py-1 rounded-full bg-${notification.color}-100 text-${notification.color}-700">
                                ${getTypeDisplayName(notification.type)}
                            </span>
                            ${isUnread ? '<span class="text-xs text-indigo-600 font-medium">Nieprzeczytane</span>' : ''}
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    function getTypeDisplayName(type) {
        const types = {
            'appointment_created': 'Nowa wizyta',
            'appointment_updated': 'Zmiana wizyty',
            'appointment_cancelled': 'Anulowana wizyta',
            'appointment_reminder': 'Przypomnienie',
            'document_created': 'Nowy dokument',
            'document_updated': 'Zmiana dokumentu',
            'message_received': 'Wiadomość',
            'system': 'System'
        };
        return types[type] || 'Powiadomienie';
    }

    function markAsRead(notificationId) {
        fetch(`/notifications/${notificationId}/mark-read`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`[data-id="${notificationId}"]`);
                if (item) {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    const unreadSpan = item.querySelector('.text-indigo-600');
                    if (unreadSpan) unreadSpan.remove();
                }
                updateUnreadCount();
            }
        })
        .catch(error => console.error('Error marking notification as read:', error));
    }

    function markAllAsRead() {
        fetch('/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                document.querySelectorAll('.notification-item.unread').forEach(item => {
                    item.classList.remove('unread');
                    item.classList.add('read');
                    const unreadSpan = item.querySelector('.text-indigo-600');
                    if (unreadSpan) unreadSpan.remove();
                });
                updateNotificationCount(0);
                showToast('Wszystkie powiadomienia zostały oznaczone jako przeczytane', 'success');
            }
        })
        .catch(error => console.error('Error marking all as read:', error));
    }

    function clearReadNotifications() {
        if (!confirm('Czy na pewno chcesz usunąć wszystkie przeczytane powiadomienia?')) {
            return;
        }

        fetch('/notifications/clear-read', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadNotifications();
                showToast(data.message, 'success');
            }
        })
        .catch(error => console.error('Error clearing read notifications:', error));
    }

    function updateNotificationCount(count) {
        if (count > 0) {
            countBadge.textContent = count > 99 ? '99+' : count;
            countBadge.classList.remove('hidden');
        } else {
            countBadge.classList.add('hidden');
        }
    }

    function updateUnreadCount() {
        fetch('/notifications/api/unread-count')
            .then(response => response.json())
            .then(data => {
                updateNotificationCount(data.unread_count);
            })
            .catch(error => console.error('Error updating unread count:', error));
    }

    function showLoading() {
        loadingElement.classList.remove('hidden');
        noNotificationsElement.classList.add('hidden');
        notificationList.innerHTML = '';
        notificationList.appendChild(loadingElement);
    }

    function hideLoading() {
        loadingElement.classList.add('hidden');
    }

    function showNoNotifications() {
        notificationList.innerHTML = '';
        notificationList.appendChild(noNotificationsElement);
        noNotificationsElement.classList.remove('hidden');
    }

    function showError(message) {
        notificationList.innerHTML = `
            <div class="p-4 text-center text-red-500">
                <i class="fas fa-exclamation-triangle text-2xl mb-2"></i>
                <p class="text-sm">${message}</p>
            </div>
        `;
    }

    function showToast(message, type = 'info') {
        // Możesz użyć istniejącej funkcji showToast z app.blade.php
        if (window.showToast) {
            window.showToast(message, type);
        }
    }

    // Automatyczne odświeżanie co 30 sekund
    setInterval(updateUnreadCount, 30000);

    // Początkowe załadowanie liczby nieprzeczytanych
    updateUnreadCount();
});
</script>
