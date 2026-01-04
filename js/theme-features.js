/**
 * SWG Theme Features JavaScript
 */
(function($) {
    'use strict';

    // Dark Mode Toggle
    if (swgTheme.darkModeEnabled) {
        const darkModeToggle = () => {
            const htmlElement = document.documentElement;
            let currentMode;
            
            // Check for saved preference first
            const savedMode = localStorage.getItem('swg-theme-mode');
            
            if (savedMode) {
                currentMode = savedMode;
            } else if (swgTheme.darkModeSystemPref && window.matchMedia) {
                // Check system preference
                currentMode = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
            } else {
                // Use default setting
                currentMode = swgTheme.darkModeDefault || 'dark';
            }
            
            // Check auto schedule if enabled
            if (swgTheme.darkModeAuto && swgTheme.darkModeAutoStart && swgTheme.darkModeAutoEnd) {
                const now = new Date();
                const currentTime = now.getHours() * 60 + now.getMinutes();
                
                const [startHour, startMin] = swgTheme.darkModeAutoStart.split(':').map(Number);
                const [endHour, endMin] = swgTheme.darkModeAutoEnd.split(':').map(Number);
                
                const startTime = startHour * 60 + startMin;
                const endTime = endHour * 60 + endMin;
                
                // Handle overnight schedules (e.g., 18:00 to 06:00)
                let isDarkTime;
                if (startTime > endTime) {
                    isDarkTime = currentTime >= startTime || currentTime < endTime;
                } else {
                    isDarkTime = currentTime >= startTime && currentTime < endTime;
                }
                
                if (isDarkTime) {
                    currentMode = 'dark';
                } else {
                    currentMode = 'light';
                }
            }
            
            // Set transition speed
            const speeds = {
                instant: '0s',
                fast: '0.2s',
                normal: '0.3s',
                slow: '0.5s'
            };
            const transitionSpeed = speeds[swgTheme.darkModeTransitionSpeed] || '0.3s';
            htmlElement.style.setProperty('--theme-transition-speed', transitionSpeed);
            
            // Set initial mode
            htmlElement.setAttribute('data-theme', currentMode);
            
            // Create toggle button
            if (!document.getElementById('swg-dark-mode-toggle')) {
                const toggle = document.createElement('button');
                toggle.id = 'swg-dark-mode-toggle';
                toggle.className = 'swg-theme-toggle swg-toggle-' + (swgTheme.darkModeTogglePosition || 'bottom-right');
                toggle.setAttribute('aria-label', 'Toggle dark mode');
                toggle.innerHTML = currentMode === 'dark' 
                    ? '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>'
                    : '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>';
                
                document.body.appendChild(toggle);
                
                // Toggle functionality
                toggle.addEventListener('click', () => {
                    const newMode = htmlElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
                    htmlElement.setAttribute('data-theme', newMode);
                    localStorage.setItem('swg-theme-mode', newMode);
                    
                    // Update icon
                    toggle.innerHTML = newMode === 'dark'
                        ? '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>'
                        : '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>';
                });
            }
            
            // Listen for system preference changes if enabled
            if (swgTheme.darkModeSystemPref && window.matchMedia) {
                const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
                darkModeMediaQuery.addEventListener('change', (e) => {
                    if (!localStorage.getItem('swg-theme-mode')) {
                        const newMode = e.matches ? 'dark' : 'light';
                        htmlElement.setAttribute('data-theme', newMode);
                        
                        const toggle = document.getElementById('swg-dark-mode-toggle');
                        if (toggle) {
                            toggle.innerHTML = newMode === 'dark'
                                ? '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z"/></svg>'
                                : '<svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/></svg>';
                        }
                    }
                });
            }
        };
        
        darkModeToggle();
    }

    // Back to Top Button
    if (swgTheme.backToTopEnabled) {
        $(window).on('load', function() {
            if (!$('#swg-back-to-top').length) {
                $('body').append('<button id="swg-back-to-top" class="swg-back-to-top" aria-label="Back to top"><svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" clip-rule="evenodd"/></svg></button>');
                
                const $backToTop = $('#swg-back-to-top');
                
                $(window).scroll(function() {
                    if ($(this).scrollTop() > 300) {
                        $backToTop.addClass('visible');
                    } else {
                        $backToTop.removeClass('visible');
                    }
                });
                
                $backToTop.on('click', function(e) {
                    e.preventDefault();
                    $('html, body').animate({ scrollTop: 0 }, 600);
                });
            }
        });
    }

    // Preloader
    if (swgTheme.preloaderEnabled) {
        $(window).on('load', function() {
            $('.swg-preloader').fadeOut(500, function() {
                $(this).remove();
            });
        });
    }

    // Notification Bar
    const notificationBar = document.getElementById('swgNotificationBar');
    if (notificationBar) {
        // Check if notification was previously closed
        const notificationClosed = localStorage.getItem('swg-notification-closed');
        
        if (notificationClosed === 'true') {
            notificationBar.style.display = 'none';
        }
        
        // Close button functionality
        const closeButton = notificationBar.querySelector('.swg-notification-close');
        if (closeButton) {
            closeButton.addEventListener('click', function() {
                notificationBar.style.display = 'none';
                localStorage.setItem('swg-notification-closed', 'true');
            });
        }
    }

    // Scroll Animations
    if (swgTheme.animationsEnabled === '1') {
        const animationSpeed = parseInt(swgTheme.animationSpeed) || 400;
        
        // Add animation classes to elements
        $('.widget, .post, .page, article, section').each(function(index) {
            if (!$(this).hasClass('no-animation')) {
                $(this).addClass('swg-animate').css('animation-duration', animationSpeed + 'ms');
            }
        });
        
        // Intersection Observer for scroll-triggered animations
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('swg-animated');
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            });
            
            document.querySelectorAll('.swg-animate').forEach(function(element) {
                observer.observe(element);
            });
        } else {
            // Fallback for browsers without IntersectionObserver
            $('.swg-animate').addClass('swg-animated');
        }
    }

})(jQuery);
