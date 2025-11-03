/**
 * Mandatly WP Consent API Synchronization
 *
 * @since 1.3.0
 */
(function() {
    'use strict';

    // Configuration
    const POLL_INTERVAL = 200; // Check every 200ms
    const MAX_ATTEMPTS = 150;  // Maximum 30 seconds (150 * 200ms)
    let attempts = 0;

    /**
     * Check if WP Consent API is ready
     */
    function isWPConsentAPIReady() {
        return typeof wp_has_consent === 'function' && typeof wp_set_consent === 'function';
    }

    /**
     * Check if Mandatly is ready
     */
    function isMandatlyReady() {
        return typeof MandatlyCookie !== 'undefined' &&
               typeof MandatlyCookie.GetConsent === 'function';
    }

    /**
     * Wait for MandatlyCookie object to be available
     */
    function waitForMandatlyCookie(callback) {
        const checkInterval = setInterval(function() {
            attempts++;

            // Check if both Mandatly and WP Consent API are ready
            if (isMandatlyReady() && isWPConsentAPIReady()) {
                clearInterval(checkInterval);
                callback();
            } else if (attempts >= MAX_ATTEMPTS) {
                clearInterval(checkInterval);
                console.warn('[Mandatly WP Sync] Timeout waiting for MandatlyCookie or WP Consent API');
            }
        }, POLL_INTERVAL);
    }

    /**
     * Map Mandatly consent to WP Consent API categories
     */
    function mapConsentCategories(mandatlyConsent) {
        const mapping = mandatly_consent_api.category_mapping;
        const wpCategories = {};

        if (mandatlyConsent.Detail && Array.isArray(mandatlyConsent.Detail)) {
            mandatlyConsent.Detail.forEach(function(category) {
                const wpCategory = mapping[category.Name];
                if (wpCategory) {
                    wpCategories[wpCategory] = category.IsConsented;
                }
            });
        }

        return wpCategories;
    }

    /**
     * Sync consent with WP Consent API
     */
    function syncWithWPConsentAPI(consentData) {
        if (!mandatly_consent_api.is_api_active) {
            return;
        }

        const wpCategories = mapConsentCategories(consentData);

        // Set consent for each category
        Object.keys(wpCategories).forEach(function(category) {
            const consentValue = wpCategories[category] ? 'allow' : 'deny';

            if (typeof wp_set_consent === 'function') {
                try {
                    wp_set_consent(category, consentValue);
                } catch (e) {
                    console.error('[Mandatly WP Sync] Error setting consent for ' + category + ':', e);
                }
            }
        });
    }

    /**
     * Initialize consent synchronization
     */
    function initialize() {
        waitForMandatlyCookie(function() {
            // Get initial consent state
            try {
                const initialConsent = MandatlyCookie.GetConsent();
                if (initialConsent) {
                    syncWithWPConsentAPI(initialConsent);
                }
            } catch (e) {
                console.error('[Mandatly WP Sync] Error getting initial consent:', e);
            }

            // Listen for consent updates on window object
            window.addEventListener('MandatlyCookieConsentUpdated', function(event) {
                // Event detail contains the categories array directly
                if (event.detail && Array.isArray(event.detail)) {
                    syncWithWPConsentAPI({ Detail: event.detail });
                } else if (event.detail) {
                    // Try syncing with the detail object directly in case structure is different
                    syncWithWPConsentAPI(event.detail);
                }
            });

            // Also trigger WordPress consent change event for compatibility
            window.addEventListener('MandatlyCookieConsentUpdated', function(event) {
                try {
                    const wpEvent = new CustomEvent('wp_listen_for_consent_change', {
                        detail: mapConsentCategories({ Detail: event.detail })
                    });
                    document.dispatchEvent(wpEvent);
                } catch (e) {
                    console.error('[Mandatly WP Sync] Error dispatching wp_listen_for_consent_change:', e);
                }
            });
        });
    }

    // Start initialization when DOM is ready
    function startInitialization() {
        // Small delay to ensure scripts have executed
        setTimeout(function() {
            initialize();
        }, 500); // Wait 500ms after DOM ready to let banner script initialize
    }

    if (document.readyState === 'loading') {
        // DOM still loading, wait for DOMContentLoaded
        document.addEventListener('DOMContentLoaded', startInitialization);
    } else if (document.readyState === 'interactive' || document.readyState === 'complete') {
        // DOM already loaded, start immediately
        startInitialization();
    }

    // Also try when window fully loads (backup method)
    if (document.readyState !== 'complete') {
        window.addEventListener('load', function() {
            // Only initialize if not already started
            if (attempts === 0) {
                startInitialization();
            }
        });
    }
})();
