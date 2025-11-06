/**
 * Intranet Dashboard JavaScript
 * Interactive features and animations for the modern dashboard
 */

(function($) {
    'use strict';

    // Dashboard object
    const IntranetDashboard = {

        /**
         * Initialize all dashboard features
         */
        init: function() {
            this.animateStats();
            this.initQuickActions();
            this.initNotifications();
            this.initCalendar();
            this.initCharts();
            this.initSearch();
            this.initDarkMode();
            this.initWidgetAnimations();
            this.initRealTimeUpdates();
            this.initTooltips();
        },

        /**
         * Animate statistics counters
         */
        animateStats: function() {
            const statValues = document.querySelectorAll('.stat-value');

            const animateValue = (element, start, end, duration) => {
                let startTimestamp = null;

                const step = (timestamp) => {
                    if (!startTimestamp) startTimestamp = timestamp;
                    const progress = Math.min((timestamp - startTimestamp) / duration, 1);
                    const current = Math.floor(progress * (end - start) + start);
                    element.textContent = current.toLocaleString();

                    if (progress < 1) {
                        window.requestAnimationFrame(step);
                    }
                };

                window.requestAnimationFrame(step);
            };

            // Use Intersection Observer for better performance
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting && !entry.target.classList.contains('animated')) {
                        const target = parseInt(entry.target.dataset.count);
                        animateValue(entry.target, 0, target, 2000);
                        entry.target.classList.add('animated');
                    }
                });
            }, { threshold: 0.5 });

            statValues.forEach(stat => observer.observe(stat));
        },

        /**
         * Initialize quick action buttons
         */
        initQuickActions: function() {
            // New Announcement
            $('#new-announcement').on('click', function(e) {
                e.preventDefault();
                IntranetDashboard.showModal('announcement');
            });

            // New Document
            $('#new-document').on('click', function(e) {
                e.preventDefault();
                IntranetDashboard.showModal('document');
            });

            // New Event
            $('#new-event').on('click', function(e) {
                e.preventDefault();
                IntranetDashboard.showModal('event');
            });
        },

        /**
         * Show modal for creating content
         */
        showModal: function(type) {
            const titles = {
                announcement: 'Nouvelle Annonce',
                document: 'Nouveau Document',
                event: 'Nouvel Événement'
            };

            // Create modal (simplified version - can be expanded)
            const modal = $(`
                <div class="dashboard-modal">
                    <div class="modal-overlay"></div>
                    <div class="modal-content">
                        <div class="modal-header">
                            <h3>${titles[type]}</h3>
                            <button class="modal-close">&times;</button>
                        </div>
                        <div class="modal-body">
                            <p>Fonctionnalité en cours de développement...</p>
                        </div>
                    </div>
                </div>
            `);

            $('body').append(modal);

            // Animate modal
            setTimeout(() => modal.addClass('active'), 10);

            // Close modal
            modal.find('.modal-close, .modal-overlay').on('click', function() {
                modal.removeClass('active');
                setTimeout(() => modal.remove(), 300);
            });
        },

        /**
         * Initialize notification system
         */
        initNotifications: function() {
            // Check for new notifications every 30 seconds
            setInterval(() => {
                this.checkNotifications();
            }, 30000);

            // Initial check
            this.checkNotifications();
        },

        /**
         * Check for new notifications
         */
        checkNotifications: function() {
            // This would connect to a real notification API
            // For now, it's a placeholder
            console.log('Checking for notifications...');

            // Simulate notification
            if (Math.random() > 0.8) {
                this.showNotification('Nouvelle annonce disponible', 'info');
            }
        },

        /**
         * Show notification toast
         */
        showNotification: function(message, type = 'info') {
            const notification = $(`
                <div class="dashboard-notification ${type}">
                    <i class="fa fa-${type === 'info' ? 'info-circle' : 'check-circle'}"></i>
                    <span>${message}</span>
                    <button class="notification-close">&times;</button>
                </div>
            `);

            $('body').append(notification);

            setTimeout(() => notification.addClass('show'), 10);

            // Auto-hide after 5 seconds
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            }, 5000);

            // Manual close
            notification.find('.notification-close').on('click', function() {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            });
        },

        /**
         * Initialize calendar
         */
        initCalendar: function() {
            const calendar = $('#intranet-calendar');
            if (calendar.length) {
                // Simple calendar display
                const currentDate = new Date();
                const monthNames = ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin',
                                   'Juillet', 'Août', 'Septembre', 'Octobre', 'Novembre', 'Décembre'];

                calendar.html(`
                    <div class="simple-calendar">
                        <h4>${monthNames[currentDate.getMonth()]} ${currentDate.getFullYear()}</h4>
                        <p>Calendrier interactif à venir</p>
                    </div>
                `);
            }
        },

        /**
         * Initialize charts and graphs
         */
        initCharts: function() {
            // Placeholder for chart initialization
            // Would use Chart.js or similar library
            console.log('Charts initialized');
        },

        /**
         * Initialize enhanced search
         */
        initSearch: function() {
            // Global search functionality
            const searchInput = $('.dashboard-search-input');

            if (searchInput.length) {
                let searchTimeout;

                searchInput.on('input', function() {
                    clearTimeout(searchTimeout);
                    const query = $(this).val();

                    if (query.length > 2) {
                        searchTimeout = setTimeout(() => {
                            IntranetDashboard.performSearch(query);
                        }, 300);
                    }
                });
            }
        },

        /**
         * Perform search
         */
        performSearch: function(query) {
            console.log('Searching for:', query);
            // This would connect to a search API
        },

        /**
         * Initialize dark mode toggle
         */
        initDarkMode: function() {
            const darkModeToggle = $('.dark-mode-toggle');
            const isDarkMode = localStorage.getItem('darkMode') === 'true';

            if (isDarkMode) {
                $('body').addClass('dark-mode');
            }

            darkModeToggle.on('click', function(e) {
                e.preventDefault();
                $('body').toggleClass('dark-mode');
                localStorage.setItem('darkMode', $('body').hasClass('dark-mode'));
            });
        },

        /**
         * Initialize widget animations
         */
        initWidgetAnimations: function() {
            const widgets = document.querySelectorAll('.dashboard-widget');

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            widgets.forEach(widget => {
                widget.style.opacity = '0';
                widget.style.transform = 'translateY(30px)';
                widget.style.transition = 'all 0.6s ease-out';
                observer.observe(widget);
            });
        },

        /**
         * Initialize real-time updates
         */
        initRealTimeUpdates: function() {
            // Simulate real-time updates
            setInterval(() => {
                this.updateActivityFeed();
            }, 60000); // Update every minute
        },

        /**
         * Update activity feed
         */
        updateActivityFeed: function() {
            // This would fetch real activity from an API
            console.log('Updating activity feed...');
        },

        /**
         * Initialize tooltips
         */
        initTooltips: function() {
            // Simple tooltip implementation
            $('[data-tooltip]').each(function() {
                const $this = $(this);
                const text = $this.data('tooltip');

                $this.on('mouseenter', function() {
                    const tooltip = $(`<div class="dashboard-tooltip">${text}</div>`);
                    $('body').append(tooltip);

                    const offset = $this.offset();
                    tooltip.css({
                        top: offset.top - tooltip.outerHeight() - 10,
                        left: offset.left + ($this.outerWidth() / 2) - (tooltip.outerWidth() / 2)
                    });

                    setTimeout(() => tooltip.addClass('show'), 10);
                });

                $this.on('mouseleave', function() {
                    $('.dashboard-tooltip').remove();
                });
            });
        },

        /**
         * Refresh widget data
         */
        refreshWidget: function(widgetId) {
            const widget = $(`#${widgetId}`);
            widget.addClass('loading');

            // Simulate API call
            setTimeout(() => {
                widget.removeClass('loading');
                this.showNotification('Widget mis à jour', 'success');
            }, 1000);
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        if ($('#intranet-dashboard').length) {
            IntranetDashboard.init();
        }
    });

    // Expose to global scope for external access
    window.IntranetDashboard = IntranetDashboard;

})(jQuery);

// Additional styles for notifications and modals
const additionalStyles = `
<style>
.dashboard-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.dashboard-modal.active {
    opacity: 1;
    visibility: visible;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.7);
}

.modal-content {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: #fff;
    border-radius: 16px;
    min-width: 500px;
    max-width: 90%;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 25px;
    border-bottom: 1px solid #e5e7eb;
}

.modal-header h3 {
    margin: 0;
    font-size: 20px;
    font-weight: 600;
}

.modal-close {
    background: none;
    border: none;
    font-size: 28px;
    color: #9ca3af;
    cursor: pointer;
    line-height: 1;
    padding: 0;
    width: 30px;
    height: 30px;
}

.modal-body {
    padding: 25px;
}

.dashboard-notification {
    position: fixed;
    top: 20px;
    right: -400px;
    background: #fff;
    padding: 15px 20px;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 9998;
    transition: right 0.3s ease;
    min-width: 300px;
}

.dashboard-notification.show {
    right: 20px;
}

.dashboard-notification i {
    font-size: 20px;
}

.dashboard-notification.info i {
    color: #3b82f6;
}

.dashboard-notification.success i {
    color: #10b981;
}

.dashboard-notification span {
    flex: 1;
    font-size: 14px;
    color: #1a1a1a;
}

.notification-close {
    background: none;
    border: none;
    font-size: 20px;
    color: #9ca3af;
    cursor: pointer;
    padding: 0;
}

.dashboard-tooltip {
    position: absolute;
    background: #1a1a1a;
    color: #fff;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    white-space: nowrap;
    z-index: 9997;
    opacity: 0;
    transition: opacity 0.2s ease;
    pointer-events: none;
}

.dashboard-tooltip.show {
    opacity: 1;
}

.dashboard-tooltip::after {
    content: '';
    position: absolute;
    top: 100%;
    left: 50%;
    transform: translateX(-50%);
    border: 6px solid transparent;
    border-top-color: #1a1a1a;
}
</style>
`;

// Inject styles
if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('intranet-dashboard')) {
            document.head.insertAdjacentHTML('beforeend', additionalStyles);
        }
    });
}
