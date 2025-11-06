/**
 * Intranet Notifications JavaScript
 * Real-time notification system with AJAX updates
 */

(function($) {
    'use strict';

    // Notifications object
    const IntranetNotifications = {
        lastCheck: Date.now() / 1000,
        checkInterval: null,
        dropdown: null,
        toggle: null,

        /**
         * Initialize notifications system
         */
        init: function() {
            this.dropdown = $('.notifications-dropdown');
            this.toggle = $('.notifications-toggle');

            if (!this.toggle.length) {
                return;
            }

            this.bindEvents();
            this.startRealTimeUpdates();
            this.initTabs();
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            const self = this;

            // Toggle dropdown
            this.toggle.on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.toggleDropdown();
            });

            // Close dropdown when clicking outside
            $(document).on('click', function(e) {
                if (!$(e.target).closest('.intranet-notifications-wrapper').length) {
                    self.closeDropdown();
                }
            });

            // Mark notification as read
            $(document).on('click', '.mark-read', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const notificationId = $(this).closest('.notification-item').data('id');
                self.markAsRead(notificationId);
            });

            // Delete notification
            $(document).on('click', '.delete-notification', function(e) {
                e.preventDefault();
                e.stopPropagation();
                const notificationId = $(this).closest('.notification-item').data('id');
                self.deleteNotification(notificationId);
            });

            // Mark all as read
            $('.mark-all-read').on('click', function(e) {
                e.preventDefault();
                self.markAllAsRead();
            });

            // Click on notification item
            $(document).on('click', '.notification-item', function(e) {
                if (!$(e.target).closest('button').length) {
                    const link = $(this).data('link');
                    if (link && link !== '#') {
                        window.location.href = link;
                    }

                    const notificationId = $(this).data('id');
                    if ($(this).hasClass('unread')) {
                        self.markAsRead(notificationId, false);
                    }
                }
            });
        },

        /**
         * Initialize tabs
         */
        initTabs: function() {
            const self = this;

            $('.tab-btn').on('click', function() {
                const tab = $(this).data('tab');

                // Update active tab
                $('.tab-btn').removeClass('active');
                $(this).addClass('active');

                // Show corresponding content
                self.switchTab(tab);
            });
        },

        /**
         * Switch tab
         */
        switchTab: function(tab) {
            $('.notifications-list').hide();
            $(`.notifications-list[data-tab-content="${tab}"]`).show();

            // Filter notifications based on tab
            if (tab === 'unread') {
                this.filterUnread();
            } else if (tab === 'mentions') {
                this.filterMentions();
            }
        },

        /**
         * Filter unread notifications
         */
        filterUnread: function() {
            const allNotifications = $('.notifications-list[data-tab-content="all"] .notification-item').clone();
            const unreadList = $('.notifications-list[data-tab-content="unread"]');

            unreadList.empty();

            allNotifications.filter('.unread').each(function() {
                unreadList.append($(this));
            });

            if (unreadList.children().length === 0) {
                unreadList.html(`
                    <div class="no-notifications">
                        <i class="fa fa-check-circle"></i>
                        <p>Toutes les notifications sont lues</p>
                    </div>
                `);
            }
        },

        /**
         * Filter mention notifications
         */
        filterMentions: function() {
            const allNotifications = $('.notifications-list[data-tab-content="all"] .notification-item').clone();
            const mentionsList = $('.notifications-list[data-tab-content="mentions"]');

            mentionsList.empty();

            allNotifications.filter(function() {
                return $(this).find('.notification-icon.mention').length > 0;
            }).each(function() {
                mentionsList.append($(this));
            });

            if (mentionsList.children().length === 0) {
                mentionsList.html(`
                    <div class="no-notifications">
                        <i class="fa fa-at"></i>
                        <p>Aucune mention</p>
                    </div>
                `);
            }
        },

        /**
         * Toggle dropdown
         */
        toggleDropdown: function() {
            this.dropdown.toggleClass('active');

            if (this.dropdown.hasClass('active')) {
                // Refresh notifications when opening
                this.checkNewNotifications();
            }
        },

        /**
         * Close dropdown
         */
        closeDropdown: function() {
            this.dropdown.removeClass('active');
        },

        /**
         * Start real-time updates
         */
        startRealTimeUpdates: function() {
            const self = this;

            // Check for new notifications every 30 seconds
            this.checkInterval = setInterval(function() {
                self.checkNewNotifications();
            }, 30000);

            // Check immediately
            this.checkNewNotifications();
        },

        /**
         * Check for new notifications
         */
        checkNewNotifications: function() {
            const self = this;

            $.ajax({
                url: oceanwpLocalize.ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'get_new_notifications',
                    nonce: oceanwpLocalize.nonce || '',
                    last_check: this.lastCheck
                },
                success: function(response) {
                    if (response.success && response.data.count > 0) {
                        self.addNewNotifications(response.data.notifications);
                        self.updateBadge(response.data.count);
                        self.playNotificationSound();
                        self.toggle.addClass('has-notifications');

                        setTimeout(() => {
                            self.toggle.removeClass('has-notifications');
                        }, 1000);
                    }

                    self.lastCheck = Date.now() / 1000;
                },
                error: function(xhr, status, error) {
                    console.error('Notification check failed:', error);
                }
            });
        },

        /**
         * Add new notifications to the list
         */
        addNewNotifications: function(notifications) {
            const list = $('.notifications-list[data-tab-content="all"]');

            // Remove "no notifications" message if present
            list.find('.no-notifications').remove();

            notifications.forEach(notification => {
                const notificationHtml = this.createNotificationHtml(notification);
                list.prepend(notificationHtml);

                // Add animation class
                list.find('.notification-item').first().addClass('new-notification');
            });
        },

        /**
         * Create notification HTML
         */
        createNotificationHtml: function(notification) {
            const iconClass = notification.type;
            const icon = this.getNotificationIcon(notification.type);
            const timeAgo = this.getTimeAgo(notification.time);

            return `
                <div class="notification-item ${!notification.read ? 'unread' : ''}" data-id="${notification.id}" data-link="${notification.link || '#'}">
                    <div class="notification-icon ${iconClass}">
                        ${icon}
                    </div>
                    <div class="notification-content">
                        <div class="notification-text">
                            ${notification.message}
                        </div>
                        <div class="notification-meta">
                            <span class="notification-time">
                                <i class="fa fa-clock"></i>
                                ${timeAgo}
                            </span>
                            ${notification.category ? `<span class="notification-category">${notification.category}</span>` : ''}
                        </div>
                    </div>
                    ${!notification.read ? '<button class="mark-read" title="Marquer comme lu"><i class="fa fa-check"></i></button>' : ''}
                    <button class="delete-notification" title="Supprimer"><i class="fa fa-times"></i></button>
                </div>
            `;
        },

        /**
         * Get notification icon
         */
        getNotificationIcon: function(type) {
            const icons = {
                announcement: '<i class="fa fa-bullhorn"></i>',
                comment: '<i class="fa fa-comment"></i>',
                mention: '<i class="fa fa-at"></i>',
                event: '<i class="fa fa-calendar"></i>',
                like: '<i class="fa fa-heart"></i>',
                document: '<i class="fa fa-file-alt"></i>',
                message: '<i class="fa fa-envelope"></i>',
                task: '<i class="fa fa-tasks"></i>',
                alert: '<i class="fa fa-exclamation-triangle"></i>',
                success: '<i class="fa fa-check-circle"></i>'
            };

            return icons[type] || '<i class="fa fa-bell"></i>';
        },

        /**
         * Get time ago string
         */
        getTimeAgo: function(timestamp) {
            const now = Date.now() / 1000;
            const diff = now - timestamp;

            if (diff < 60) {
                return 'à l\'instant';
            } else if (diff < 3600) {
                const mins = Math.floor(diff / 60);
                return `il y a ${mins} minute${mins > 1 ? 's' : ''}`;
            } else if (diff < 86400) {
                const hours = Math.floor(diff / 3600);
                return `il y a ${hours} heure${hours > 1 ? 's' : ''}`;
            } else {
                const days = Math.floor(diff / 86400);
                return `il y a ${days} jour${days > 1 ? 's' : ''}`;
            }
        },

        /**
         * Update notification badge
         */
        updateBadge: function(count) {
            let badge = this.toggle.find('.notifications-badge');

            if (count > 0) {
                if (!badge.length) {
                    badge = $('<span class="notifications-badge"></span>');
                    this.toggle.append(badge);
                }
                badge.text(count > 9 ? '9+' : count);
            } else {
                badge.remove();
            }
        },

        /**
         * Mark notification as read
         */
        markAsRead: function(notificationId, showFeedback = true) {
            const self = this;
            const item = $(`.notification-item[data-id="${notificationId}"]`);

            item.addClass('loading');

            $.ajax({
                url: oceanwpLocalize.ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'mark_notification_read',
                    nonce: oceanwpLocalize.nonce || '',
                    notification_id: notificationId
                },
                success: function(response) {
                    if (response.success) {
                        item.removeClass('unread loading');
                        item.find('.mark-read').remove();

                        // Update badge
                        const currentCount = parseInt(self.toggle.find('.notifications-badge').text()) || 0;
                        self.updateBadge(Math.max(0, currentCount - 1));

                        // Update unread tab badge
                        const unreadTabBadge = $('.tab-btn[data-tab="unread"] .tab-badge');
                        const unreadCount = parseInt(unreadTabBadge.text()) || 0;
                        if (unreadCount > 1) {
                            unreadTabBadge.text(unreadCount - 1);
                        } else {
                            unreadTabBadge.remove();
                        }

                        if (showFeedback) {
                            self.showToast('Notification marquée comme lue', 'success');
                        }
                    }
                },
                error: function() {
                    item.removeClass('loading');
                    self.showToast('Erreur lors du marquage', 'error');
                }
            });
        },

        /**
         * Mark all notifications as read
         */
        markAllAsRead: function() {
            const self = this;

            $.ajax({
                url: oceanwpLocalize.ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'mark_all_notifications_read',
                    nonce: oceanwpLocalize.nonce || ''
                },
                success: function(response) {
                    if (response.success) {
                        $('.notification-item').removeClass('unread');
                        $('.mark-read').remove();
                        self.updateBadge(0);
                        $('.tab-btn[data-tab="unread"] .tab-badge').remove();
                        self.showToast('Toutes les notifications sont marquées comme lues', 'success');
                    }
                },
                error: function() {
                    self.showToast('Erreur lors du marquage', 'error');
                }
            });
        },

        /**
         * Delete notification
         */
        deleteNotification: function(notificationId) {
            const self = this;
            const item = $(`.notification-item[data-id="${notificationId}"]`);
            const isUnread = item.hasClass('unread');

            item.addClass('loading');

            $.ajax({
                url: oceanwpLocalize.ajaxurl || '/wp-admin/admin-ajax.php',
                type: 'POST',
                data: {
                    action: 'delete_notification',
                    nonce: oceanwpLocalize.nonce || '',
                    notification_id: notificationId
                },
                success: function(response) {
                    if (response.success) {
                        item.fadeOut(300, function() {
                            $(this).remove();

                            // Update badge if unread
                            if (isUnread) {
                                const currentCount = parseInt(self.toggle.find('.notifications-badge').text()) || 0;
                                self.updateBadge(Math.max(0, currentCount - 1));
                            }

                            // Check if list is empty
                            if ($('.notifications-list[data-tab-content="all"] .notification-item').length === 0) {
                                $('.notifications-list[data-tab-content="all"]').html(`
                                    <div class="no-notifications">
                                        <i class="fa fa-bell-slash"></i>
                                        <p>Aucune notification pour le moment</p>
                                    </div>
                                `);
                            }
                        });

                        self.showToast('Notification supprimée', 'success');
                    }
                },
                error: function() {
                    item.removeClass('loading');
                    self.showToast('Erreur lors de la suppression', 'error');
                }
            });
        },

        /**
         * Play notification sound
         */
        playNotificationSound: function() {
            // Simple beep using Web Audio API
            try {
                const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                const oscillator = audioContext.createOscillator();
                const gainNode = audioContext.createGain();

                oscillator.connect(gainNode);
                gainNode.connect(audioContext.destination);

                oscillator.frequency.value = 800;
                oscillator.type = 'sine';

                gainNode.gain.setValueAtTime(0.1, audioContext.currentTime);
                gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

                oscillator.start(audioContext.currentTime);
                oscillator.stop(audioContext.currentTime + 0.1);
            } catch (e) {
                // Silently fail if audio is not supported
            }
        },

        /**
         * Show toast notification
         */
        showToast: function(message, type = 'info') {
            const toast = $(`
                <div class="notification-toast ${type}">
                    <i class="fa fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
                    <span>${message}</span>
                </div>
            `);

            $('body').append(toast);

            setTimeout(() => toast.addClass('show'), 10);

            setTimeout(() => {
                toast.removeClass('show');
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        },

        /**
         * Destroy (cleanup)
         */
        destroy: function() {
            if (this.checkInterval) {
                clearInterval(this.checkInterval);
            }
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        IntranetNotifications.init();
    });

    // Cleanup on page unload
    $(window).on('beforeunload', function() {
        IntranetNotifications.destroy();
    });

    // Expose to global scope
    window.IntranetNotifications = IntranetNotifications;

})(jQuery);

// Toast styles
const toastStyles = `
<style>
.notification-toast {
    position: fixed;
    bottom: -100px;
    right: 20px;
    background: #fff;
    padding: 12px 20px;
    border-radius: 10px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 10px;
    z-index: 10000;
    transition: bottom 0.3s ease;
    font-size: 14px;
}

.notification-toast.show {
    bottom: 20px;
}

.notification-toast.success i {
    color: #10b981;
}

.notification-toast.error i {
    color: #ef4444;
}
</style>
`;

if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        document.head.insertAdjacentHTML('beforeend', toastStyles);
    });
}
