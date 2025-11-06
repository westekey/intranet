/**
 * Dark Mode JavaScript
 * Toggle and persist dark mode preference
 */

(function($) {
    'use strict';

    // Dark Mode object
    const DarkMode = {
        storageKey: 'intranet_dark_mode',

        /**
         * Initialize dark mode
         */
        init: function() {
            this.createToggleButton();
            this.loadPreference();
            this.bindEvents();
            this.detectSystemPreference();
        },

        /**
         * Create floating toggle button
         */
        createToggleButton: function() {
            // Check if button already exists
            if ($('.floating-dark-mode-toggle').length) {
                return;
            }

            // Create toggle button
            const toggleButton = $(`
                <button class="floating-dark-mode-toggle" aria-label="Toggle Dark Mode" title="Changer de thème">
                    <i class="fa fa-sun"></i>
                </button>
            `);

            $('body').append(toggleButton);

            // Also add to header if there's a navigation menu
            if ($('#site-navigation').length) {
                const headerToggle = $(`
                    <div class="dark-mode-toggle-wrapper">
                        <button class="dark-mode-toggle" aria-label="Toggle Dark Mode">
                            <span class="toggle-icon sun"><i class="fa fa-sun"></i></span>
                            <span class="toggle-icon moon"><i class="fa fa-moon"></i></span>
                        </button>
                    </div>
                `);

                $('#site-navigation').append(headerToggle);
            }
        },

        /**
         * Load user preference
         */
        loadPreference: function() {
            const preference = localStorage.getItem(this.storageKey);

            if (preference === 'dark') {
                this.enableDarkMode(false);
            } else if (preference === 'light') {
                this.disableDarkMode(false);
            } else {
                // No preference set, use system preference
                this.detectSystemPreference();
            }
        },

        /**
         * Detect system color scheme preference
         */
        detectSystemPreference: function() {
            if (window.matchMedia) {
                const darkModeQuery = window.matchMedia('(prefers-color-scheme: dark)');

                // Set initial state
                if (darkModeQuery.matches && !localStorage.getItem(this.storageKey)) {
                    this.enableDarkMode(false);
                }

                // Listen for changes
                darkModeQuery.addEventListener('change', (e) => {
                    if (!localStorage.getItem(this.storageKey)) {
                        if (e.matches) {
                            this.enableDarkMode(true);
                        } else {
                            this.disableDarkMode(true);
                        }
                    }
                });
            }
        },

        /**
         * Bind events
         */
        bindEvents: function() {
            const self = this;

            // Floating toggle button
            $(document).on('click', '.floating-dark-mode-toggle', function() {
                self.toggle();
            });

            // Header toggle button
            $(document).on('click', '.dark-mode-toggle', function() {
                self.toggle();
            });

            // Keyboard shortcut: Ctrl/Cmd + Shift + D
            $(document).on('keydown', function(e) {
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'D') {
                    e.preventDefault();
                    self.toggle();
                }
            });
        },

        /**
         * Toggle dark mode
         */
        toggle: function() {
            if ($('body').hasClass('dark-mode')) {
                this.disableDarkMode(true);
            } else {
                this.enableDarkMode(true);
            }
        },

        /**
         * Enable dark mode
         */
        enableDarkMode: function(animate = true) {
            $('body').addClass('dark-mode');

            // Update icon
            $('.floating-dark-mode-toggle i')
                .removeClass('fa-sun')
                .addClass('fa-moon');

            // Save preference
            localStorage.setItem(this.storageKey, 'dark');

            // Show notification
            if (animate) {
                this.showNotification('Mode sombre activé', 'dark');
                this.animateTransition();
            }

            // Trigger custom event
            $(document).trigger('darkModeEnabled');
        },

        /**
         * Disable dark mode
         */
        disableDarkMode: function(animate = true) {
            $('body').removeClass('dark-mode');

            // Update icon
            $('.floating-dark-mode-toggle i')
                .removeClass('fa-moon')
                .addClass('fa-sun');

            // Save preference
            localStorage.setItem(this.storageKey, 'light');

            // Show notification
            if (animate) {
                this.showNotification('Mode clair activé', 'light');
                this.animateTransition();
            }

            // Trigger custom event
            $(document).trigger('darkModeDisabled');
        },

        /**
         * Animate transition
         */
        animateTransition: function() {
            // Add transition class
            $('body').addClass('theme-transitioning');

            // Remove after animation
            setTimeout(() => {
                $('body').removeClass('theme-transitioning');
            }, 300);
        },

        /**
         * Show notification
         */
        showNotification: function(message, theme) {
            // Remove existing notifications
            $('.theme-notification').remove();

            // Create notification
            const notification = $(`
                <div class="theme-notification ${theme}">
                    <i class="fa fa-${theme === 'dark' ? 'moon' : 'sun'}"></i>
                    <span>${message}</span>
                </div>
            `);

            $('body').append(notification);

            // Show notification
            setTimeout(() => notification.addClass('show'), 10);

            // Hide and remove after 2 seconds
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => notification.remove(), 300);
            }, 2000);
        },

        /**
         * Reset to system preference
         */
        resetToSystem: function() {
            localStorage.removeItem(this.storageKey);
            this.detectSystemPreference();
            this.showNotification('Préférence système restaurée', 'info');
        },

        /**
         * Check if dark mode is enabled
         */
        isDarkMode: function() {
            return $('body').hasClass('dark-mode');
        }
    };

    // Initialize on document ready
    $(document).ready(function() {
        DarkMode.init();
    });

    // Expose to global scope
    window.DarkMode = DarkMode;

    // Add settings panel option (if settings panel exists)
    $(document).on('settingsPanelReady', function() {
        const settingsHTML = `
            <div class="settings-section">
                <h3>Thème de l'interface</h3>
                <div class="theme-options">
                    <label class="theme-option">
                        <input type="radio" name="theme" value="light" ${!DarkMode.isDarkMode() ? 'checked' : ''}>
                        <span><i class="fa fa-sun"></i> Clair</span>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="dark" ${DarkMode.isDarkMode() ? 'checked' : ''}>
                        <span><i class="fa fa-moon"></i> Sombre</span>
                    </label>
                    <label class="theme-option">
                        <input type="radio" name="theme" value="system">
                        <span><i class="fa fa-desktop"></i> Système</span>
                    </label>
                </div>
            </div>
        `;

        $('.settings-panel-content').prepend(settingsHTML);

        // Handle theme option change
        $('input[name="theme"]').on('change', function() {
            const value = $(this).val();

            if (value === 'light') {
                DarkMode.disableDarkMode(true);
            } else if (value === 'dark') {
                DarkMode.enableDarkMode(true);
            } else if (value === 'system') {
                DarkMode.resetToSystem();
            }
        });
    });

})(jQuery);

// Theme notification styles
const themeNotificationStyles = `
<style>
.theme-notification {
    position: fixed;
    bottom: -100px;
    left: 50%;
    transform: translateX(-50%);
    background: #fff;
    color: #1a1a1a;
    padding: 15px 25px;
    border-radius: 50px;
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.2);
    display: flex;
    align-items: center;
    gap: 12px;
    z-index: 10000;
    transition: bottom 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    font-size: 15px;
    font-weight: 600;
}

.theme-notification.show {
    bottom: 30px;
}

.theme-notification.dark {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: #fff;
}

.theme-notification i {
    font-size: 18px;
}

.theme-transitioning {
    position: relative;
}

.theme-transitioning::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: radial-gradient(circle at center, transparent 0%, rgba(0, 0, 0, 0.3) 100%);
    z-index: 9998;
    pointer-events: none;
    animation: themeTransition 0.3s ease-out;
}

@keyframes themeTransition {
    0% {
        opacity: 0;
        transform: scale(0.8);
    }
    50% {
        opacity: 1;
    }
    100% {
        opacity: 0;
        transform: scale(1.2);
    }
}

/* Settings Panel Theme Options */
.theme-options {
    display: flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 15px;
}

.theme-option {
    display: flex;
    align-items: center;
    padding: 12px 15px;
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.3s ease;
}

.theme-option:hover {
    background: #f3f4f6;
    border-color: #d1d5db;
}

.theme-option input[type="radio"] {
    margin-right: 12px;
}

.theme-option input[type="radio"]:checked + span {
    font-weight: 600;
    color: #667eea;
}

.theme-option span {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 15px;
}

.theme-option i {
    font-size: 18px;
}

body.dark-mode .theme-option {
    background: #1a202c;
    border-color: #4a5568;
    color: #e2e8f0;
}

body.dark-mode .theme-option:hover {
    background: #2d3748;
}
</style>
`;

// Inject styles
if (typeof document !== 'undefined') {
    document.addEventListener('DOMContentLoaded', function() {
        document.head.insertAdjacentHTML('beforeend', themeNotificationStyles);
    });
}
