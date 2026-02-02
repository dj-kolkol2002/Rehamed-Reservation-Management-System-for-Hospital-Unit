// public/js/notifications.js
class NotificationManager {
    constructor() {
        this.notificationBell = document.getElementById('notification-bell');
        this.notificationDropdown = document.getElementById('notification-dropdown');
        this.notificationCount = document.getElementById('notification-count');
        this.notificationList = document.getElementById('notification-list');
        this.markAllReadBtn = document.getElementById('mark-all-read-btn');
        this.clearReadBtn = document.getElementById('clear-read-btn');
        this.loadMoreBtn = document.getElementById('load-more-notifications');

        this.isDropdownOpen = false;
        this.currentOffset = 0;
        this.hasMore = true;

        this.init();
    }

    init() {
        this.bindEvents();
        this.loadNotificationCount();

        // Automatyczne odświeżanie co 30 sekund
        setInterval(() => {
            this.loadNotificationCount();
            if (this.isDropdownOpen) {
                this.refreshNotifications();
            }
        }, 30000);
    }

    bindEvents() {
        // Toggle dropdown
        if (this.notificationBell) {
            this.notificationBell.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleDropdown();
            });
        }

        // Oznacz wszystkie jako przeczytane
        if (this.markAllReadBtn) {
            this.markAllReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.markAllAsRead();
            });
        }

        // Wyczyść przeczytane
        if (this.clearReadBtn) {
            this.clearReadBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.clearReadNotifications();
            });
        }

        // Załaduj więcej
        if (this.loadMoreBtn) {
            this.loadMoreBtn.addEventListener('click', (e) => {
                e.preventDefault();
                this.loadMoreNotifications();
            });
        }

        // Zamknij dropdown przy kliknięciu poza nim
        document.addEventListener('click', (e) => {
            if (this.isDropdownOpen && !this.notificationBell.contains(e.target) && !this.notificationDropdown.contains(e.target)) {
                this.closeDropdown();
            }
        });

        // Escape key closes dropdown
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isDropdownOpen) {
                this.closeDropdown();
            }
        });
    }

    async loadNotificationCount() {
        try {
            const response = await fetch('/notifications/api/unread-count');
            const data = await response.json();

            this.updateNotificationCount(data.count);
        } catch (error) {
            console.error('Error loading notification count:', error);
        }
    }

    updateNotificationCount(count) {
        if (this.notificationCount) {
            if (count > 0) {
                this.notificationCount.textContent = count > 99 ? '99+' : count;
                this.notificationCount.classList.remove('hidden');
                this.notificationBell.classList.add('animate-pulse');
            } else {
                this.notificationCount.classList.add('hidden');
                this.notificationBell.classList.remove('animate-pulse');
            }
        }
    }

    async toggleDropdown() {
        if (this.isDropdownOpen) {
            this.closeDropdown();
        } else {
            await this.openDropdown();
        }
    }

    async openDropdown() {
        this.isDropdownOpen = true;
        this.notificationDropdown.classList.remove('hidden');
        this.notificationDropdown.classList.add('animate-fade-in');

        // Załaduj powiadomienia
        await this.loadNotifications();

        // Focus na pierwszym elemencie dla accessibility
        const firstNotification = this.notificationDropdown.querySelector('[role="menuitem"]');
        if (firstNotification) {
            firstNotification.focus();
        }
    }

    closeDropdown() {
        this.isDropdownOpen = false;
        this.notificationDropdown.classList.add('hidden');
        this.notificationDropdown.classList.remove('animate-fade-in');
        this.currentOffset = 0;
        this.hasMore = true;
    }

    async loadNotifications(refresh = false) {
        if (refresh) {
            this.currentOffset = 0;
            this.hasMore = true;
        }

        try {
            const response = await fetch(`/notifications/api/notifications?limit=10&offset=${this.currentOffset}`);
            const data = await response.json();

            if (refresh) {
                this.notificationList.innerHTML = '';
            }

            this.renderNotifications(data.notifications, refresh);
            this.updateNotificationCount(data.unread_count);
            this.hasMore = data.has_more;

            // Pokaż/ukryj przycisk "Załaduj więcej"
            if (this.loadMoreBtn) {
                this.loadMoreBtn.style.display = this.hasMore ? 'block' : 'none';
            }

        } catch (error) {
            console.error('Error loading notifications:', error);
            this.showError('Błąd podczas ładowania powiadomień');
        }
    }

    async refreshNotifications() {
        await this.loadNotifications(true);
    }

    async loadMoreNotifications() {
        this.currentOffset += 10;
        await this.loadNotifications(false);
    }

    renderNotifications(notifications, refresh = false) {
        if (!notifications || notifications.length === 0) {
            if (refresh) {
                this.notificationList.innerHTML = `
                    <div class="p-4 text-center text-gray-500">
                        <i class="fas fa-bell-slash text-2xl mb-2"></i>
                        <p>Brak powiadomień</p>
                    </div>
                `;
            }
            return;
        }

        notifications.forEach(notification => {
            const notificationElement = this.createNotificationElement(notification);
            this.notificationList.appendChild(notificationElement);
        });
    }

    createNotificationElement(notification) {
        const div = document.createElement('div');
        div.className = `notification-item border-b border-gray-100 hover:bg-gray-50 transition-colors cursor-pointer ${notification.is_read ? '' : 'bg-blue-50'}`;
        div.setAttribute('role', 'menuitem');
        div.setAttribute('data-notification-id', notification.id);

        const isNewBadge = notification.is_new ? '<span class="inline-block w-2 h-2 bg-red-500 rounded-full"></span>' : '';

        div.innerHTML = `
            <div class="p-4 flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 rounded-full bg-${notification.color}-100 flex items-center justify-center">
                        <i class="${notification.icon} text-${notification.color}-600 text-sm"></i>
                    </div>
                </div>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                        <h4 class="text-sm font-medium text-gray-900 truncate">
                            ${notification.title}
                            ${isNewBadge}
                        </h4>
                        <span class="text-xs text-gray-500 ml-2">${notification.formatted_time}</span>
                    </div>
                    <p class="text-sm text-gray-600 mt-1 line-clamp-2">${notification.message}</p>
                    <div class="flex items-center justify-between mt-2">
                        <div class="flex space-x-2">
                            ${!notification.is_read ? '<button class="mark-read-btn text-xs text-blue-600 hover:text-blue-800">Oznacz jako przeczytane</button>' : ''}
                        </div>
                        <button class="delete-notification-btn text-xs text-gray-400 hover:text-red-600">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                </div>
            </div>
        `;

        // Event listeners dla tej notyfikacji
        this.bindNotificationEvents(div, notification);

        return div;
    }

    bindNotificationEvents(element, notification) {
        // Kliknięcie w powiadomienie - przejdź do akcji
        element.addEventListener('click', (e) => {
            if (!e.target.closest('.mark-read-btn') && !e.target.closest('.delete-notification-btn')) {
                this.handleNotificationClick(notification);
            }
        });

        // Oznacz jako przeczytane
        const markReadBtn = element.querySelector('.mark-read-btn');
        if (markReadBtn) {
            markReadBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.markAsRead(notification.id, element);
            });
        }

        // Usuń powiadomienie
        const deleteBtn = element.querySelector('.delete-notification-btn');
        if (deleteBtn) {
            deleteBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                this.deleteNotification(notification.id, element);
            });
        }
    }

    async handleNotificationClick(notification) {
        // Oznacz jako przeczytane jeśli nie jest
        if (!notification.is_read) {
            await this.markAsRead(notification.id);
        }

        // Przekieruj do akcji
        if (notification.action_url && notification.action_url !== '#') {
            window.location.href = notification.action_url;
        }

        this.closeDropdown();
    }

    async markAsRead(notificationId, element = null) {
        try {
            const response = await fetch(`/notifications/${notificationId}/mark-read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                if (element) {
                    element.classList.remove('bg-blue-50');
                    const markReadBtn = element.querySelector('.mark-read-btn');
                    if (markReadBtn) {
                        markReadBtn.remove();
                    }
                }

                // Odśwież licznik
                this.loadNotificationCount();
                this.showSuccess('Powiadomienie oznaczone jako przeczytane');
            } else {
                this.showError(data.message || 'Błąd podczas oznaczania powiadomienia');
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
            this.showError('Błąd podczas oznaczania powiadomienia');
        }
    }

    async markAllAsRead() {
        try {
            const response = await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                // Odśwież listę powiadomień
                await this.refreshNotifications();
                this.showSuccess(data.message);
            } else {
                this.showError(data.message || 'Błąd podczas oznaczania powiadomień');
            }
        } catch (error) {
            console.error('Error marking all notifications as read:', error);
            this.showError('Błąd podczas oznaczania powiadomień');
        }
    }

    async deleteNotification(notificationId, element) {
        try {
            const response = await fetch(`/notifications/${notificationId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                // Usuń element z DOM z animacją
                element.style.animation = 'slideOut 0.3s ease-out forwards';
                setTimeout(() => {
                    element.remove();
                    this.loadNotificationCount();
                }, 300);

                this.showSuccess('Powiadomienie zostało usunięte');
            } else {
                this.showError(data.message || 'Błąd podczas usuwania powiadomienia');
            }
        } catch (error) {
            console.error('Error deleting notification:', error);
            this.showError('Błąd podczas usuwania powiadomienia');
        }
    }

    async clearReadNotifications() {
        if (!confirm('Czy na pewno chcesz usunąć wszystkie przeczytane powiadomienia?')) {
            return;
        }

        try {
            const response = await fetch('/notifications/clear-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });

            const data = await response.json();

            if (data.success) {
                await this.refreshNotifications();
                this.showSuccess(data.message);
            } else {
                this.showError(data.message || 'Błąd podczas usuwania powiadomień');
            }
        } catch (error) {
            console.error('Error clearing read notifications:', error);
            this.showError('Błąd podczas usuwania powiadomień');
        }
    }

    showSuccess(message) {
        this.showToast(message, 'success');
    }

    showError(message) {
        this.showToast(message, 'error');
    }

    showToast(message, type = 'info') {
        // Usuń istniejące toasty
        const existingToast = document.querySelector('.notification-toast');
        if (existingToast) {
            existingToast.remove();
        }

        const toast = document.createElement('div');
        toast.className = `notification-toast fixed top-4 right-4 z-50 px-4 py-3 rounded-lg shadow-lg transform transition-all duration-300 translate-x-full`;

        const colors = {
            success: 'bg-green-500 text-white',
            error: 'bg-red-500 text-white',
            info: 'bg-blue-500 text-white'
        };

        const icons = {
            success: 'fas fa-check-circle',
            error: 'fas fa-exclamation-circle',
            info: 'fas fa-info-circle'
        };

        toast.className += ` ${colors[type]}`;
        toast.innerHTML = `
            <div class="flex items-center space-x-2">
                <i class="${icons[type]}"></i>
                <span>${message}</span>
                <button class="ml-2 hover:opacity-75" onclick="this.parentElement.parentElement.remove()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;

        document.body.appendChild(toast);

        // Animacja wejścia
        setTimeout(() => {
            toast.style.transform = 'translateX(0)';
        }, 100);

        // Automatyczne usunięcie po 5 sekundach
        setTimeout(() => {
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => {
                toast.remove();
            }, 300);
        }, 5000);
    }
}

// Dodaj style CSS
const notificationStyles = document.createElement('style');
notificationStyles.textContent = `
    .animate-fade-in {
        animation: fadeIn 0.2s ease-out forwards;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(-10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideOut {
        from {
            opacity: 1;
            transform: translateX(0);
            max-height: 200px;
        }
        to {
            opacity: 0;
            transform: translateX(100%);
            max-height: 0;
            padding: 0;
            margin: 0;
        }
    }

    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .notification-toast {
        max-width: 400px;
        word-wrap: break-word;
    }
`;

document.head.appendChild(notificationStyles);

// Inicjalizuj po załadowaniu DOM
document.addEventListener('DOMContentLoaded', function() {
    window.notificationManager = new NotificationManager();
});
