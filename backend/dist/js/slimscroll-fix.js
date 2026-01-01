/**
 * SlimScroll Fix - Deep Fix
 * ==========================
 * Complete rewrite to properly handle SlimScroll initialization
 * The core issue: SlimScroll is destroyed and recreated but with incorrect height
 * Solution: Force proper re-calculation and use explicit mouse event handlers
 */

(function($) {
    'use strict';

    var initialized = false;
    var lastHeight = 0;
    var resizeTimeout = null;

    /**
     * Main initialization function
     */
    function initSlimScrollFix() {

        // Check dependencies
        if (!$.AdminLTE || !$.AdminLTE.layout) {
            console.warn('⚠ AdminLTE not available yet, will retry...');
            setTimeout(initSlimScrollFix, 500);
            return;
        }

        if (typeof $.fn.slimScroll === 'undefined') {
            console.warn('⚠ SlimScroll plugin not found');
            return;
        }

        console.log('✓ All dependencies loaded');

        /**
         * Strategy: Override the fixSidebar function with our own better version
         * that ensures proper initialization with correct dimensions
         */
        if (!initialized) {
            initialized = true;
            overrideFixSidebar();
            setupEventListeners();
        }

        // Perform initial fix
        setTimeout(function() {
            fixSidebarProperly();
        }, 100);
    }

    /**
     * Override AdminLTE's fixSidebar with a better version
     */
    function overrideFixSidebar() {
        var original_fixSidebar = $.AdminLTE.layout.fixSidebar;

        $.AdminLTE.layout.fixSidebar = function() {
            // Call original first
            original_fixSidebar.call(this);

            // Then apply our enhancements
            fixSidebarProperly();
        };
    }

    /**
     * The core fix: Properly initialize SlimScroll with correct height
     */
    function fixSidebarProperly() {
        var $sidebar = $('.sidebar');
        var $body = $('body');
        var $header = $('.main-header');

        // Only proceed if body has fixed class
        if (!$body.hasClass('fixed')) {
            console.log('📝 Non-fixed layout detected, skipping SlimScroll');
            return;
        }

        if ($sidebar.length === 0) {
            console.warn('⚠ Sidebar not found');
            return;
        }

        if (typeof $.fn.slimScroll === 'undefined') {
            console.warn('⚠ SlimScroll not available');
            return;
        }

        try {
            // Calculate proper height
            var windowHeight = $(window).height();
            var headerHeight = $header.length > 0 ? $header.outerHeight() : 0;
            var calculatedHeight = windowHeight - headerHeight;

            console.log('📊 Dimensions - Window:', windowHeight, 'Header:', headerHeight, 'Calculated:', calculatedHeight);

            // CRITICAL: First, completely destroy any existing slimscroll
            $sidebar.slimScroll({ destroy: true });
            $sidebar.height('auto');

            // Clear inline height style that might be blocking
            $sidebar.css('height', '');

            // Remove any existing scrollbar elements
            $sidebar.parent().find('.slimScrollRail, .slimScrollBar').remove();

            // Small delay to ensure DOM update
            setTimeout(function() {
                try {
                    // Now initialize with proper settings
                    $sidebar.slimscroll({
                        height: calculatedHeight + 'px',
                        size: '6px',
                        color: 'rgba(0,0,0,0.3)',
                        position: 'right',
                        distance: '0px',
                        start: 'top',
                        opacity: 0.6,
                        alwaysVisible: false,
                        disableFadeOut: false,
                        railVisible: false,
                        railColor: '#222',
                        railOpacity: 0.2,
                        wheelStep: 20,
                        touchScrollStep: 200,
                        borderRadius: '4px',
                        railBorderRadius: '4px'
                    });

                    // Force initialization by triggering mouse enter
                    $sidebar.trigger('mouseenter');

                    console.log('✓ SlimScroll properly initialized - Height: ' + calculatedHeight + 'px');

                } catch (e) {
                    console.error('✗ Error initializing SlimScroll:', e);
                }
            }, 10);

        } catch (e) {
            console.error('✗ Error in fixSidebarProperly:', e);
        }
    }

    /**
     * Setup event listeners for re-initialization triggers
     */
    function setupEventListeners() {
        // Window load - critical initialization
        if (document.readyState === 'complete') {
            fixSidebarProperly();
        } else {
            $(window).on('load', function() {
                console.log('📝 Window load event fired');
                setTimeout(fixSidebarProperly, 150);
            });
        }

        // Tab visibility change
        document.addEventListener('visibilitychange', function() {
            if (!document.hidden) {
                console.log('📝 Tab became visible, reinitializing...');
                setTimeout(fixSidebarProperly, 100);
            }
        }, false);

        // Window resize with smart detection
        $(window).on('resize', function() {
            if (resizeTimeout) {
                clearTimeout(resizeTimeout);
            }

            resizeTimeout = setTimeout(function() {
                var currentHeight = $(window).height();

                // Only if height changed significantly (> 30px)
                if (Math.abs(currentHeight - lastHeight) > 30) {
                    lastHeight = currentHeight;
                    console.log('📝 Window height changed, reinitializing...');
                    fixSidebarProperly();
                }
            }, 300);
        });

        // Force recalc on sidebar tree menu open/close
        $(document).on('click', '.sidebar .treeview > a', function() {
            setTimeout(function() {
                console.log('📝 Sidebar menu changed, recalculating...');
                
                // Get current slimscroll instance
                var $sidebar = $('.sidebar');
                if ($sidebar.length) {
                    // Trigger mouseenter to make scrollbar active
                    $sidebar.trigger('mouseenter');
                }
            }, 100);
        });

        // Ensure slimscroll stays active when sidebar is hovered
        $(document).on('mouseenter', '.sidebar', function() {
            var $sidebar = $('.sidebar');
            if ($sidebar.data('slimscroll')) {
                // Slimscroll is active, keep it visible
                $sidebar.find('.slimScrollBar').show();
            }
        });

        console.log('✓ Event listeners installed');
    }

    /**
     * Public API
     */
    window.SlimScrollFix = {
        reinit: function() {
            console.log('📝 Manual reinit requested');
            fixSidebarProperly();
        },
        
        destroy: function() {
            var $sidebar = $('.sidebar');
            if ($sidebar.length && typeof $.fn.slimScroll !== 'undefined') {
                $sidebar.slimScroll({ destroy: true }).height('auto');
                console.log('✓ SlimScroll destroyed');
            }
        }
    };

    // Start initialization when document is ready
    if (document.readyState === 'loading') {
        $(document).on('ready', initSlimScrollFix);
    } else {
        $(document).ready(initSlimScrollFix);
    }

    // Also try on window load just to be safe
    $(window).on('load', function() {
        if (!initialized) {
            console.log('📝 Initializing via window load event');
            initSlimScrollFix();
        }
    });

})(jQuery);
