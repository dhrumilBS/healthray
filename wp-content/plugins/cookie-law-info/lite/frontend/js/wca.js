(function () {
    const consentType = {
        gdpr: 'optin',
        ccpa: 'optout',
    };
    const categoryMap = {
        functional: 'preferences',
        analytics: ['statistics','statistics-anonymous'],
        performance: 'functional',
        advertisement: 'marketing',
    };
    const gskEnabled = typeof _ckyGsk !== 'undefined' && _ckyGsk ? _ckyGsk : false;
    // Last type announced with wp_consent_type_defined, so it is not repeated for the same value.
    let announcedType = null;
    // Whether the type was announced once the page was ready, for plugins whose scripts load after this one.
    let announcedWhenReady = false;
    // Whether a stored choice has been passed to the WP Consent API on this page load.
    let storedChoiceApplied = false;

    function getConsentData() {
        if (typeof window.getCkyConsent !== 'function') {
            return null;
        }
        try {
            return window.getCkyConsent();
        } catch (e) {
            return null;
        }
    }

    function setConsentStatus(key, status) {
        if (Array.isArray(categoryMap[key])) {
            categoryMap[key].forEach(el => {
                wp_set_consent(el, status);
            });
        } else {
            wp_set_consent(categoryMap[key], status);
        }
    }

    /**
     * Set window.wp_consent_type and announce it with wp_consent_type_defined.
     *
     * @param {object} consentData getCkyConsent() result.
     * @param {boolean} force Announce even when this type was announced already.
     */
    function setConsentType(consentData, force) {
        const type = consentType[consentData.activeLaw] || 'optin';
        window.wp_consent_type = type;
        if (force || type !== announcedType) {
            announcedType = type;
            document.dispatchEvent(new CustomEvent('wp_consent_type_defined'));
        }
    }

    /**
     * Pass each category to the WP Consent API.
     *
     * @returns {boolean} Whether the categories were applied.
     */
    function applyCategories(consentData) {
        // Normally loaded before this file (a script dependency); missing only if another plugin removed it.
        if (typeof wp_set_consent !== 'function') {
            return false;
        }
        const categories = Object.entries(consentData.categories || {});
        categories.forEach(([key, value]) => {
            if (!(key in categoryMap))
                return;
            setConsentStatus(key, value ? 'allow' : 'deny');
        });
        return categories.length > 0;
    }

    // On every consent update: the banner's defaults on a first visit, and each choice the visitor makes.
    function onConsentUpdate() {
        const consentData = getConsentData();
        if (!consentData) {
            return;
        }
        // Always set the type, also when Site Kit is active: an unset type makes wp_has_consent() allow everything.
        setConsentType(consentData, true);
        const categories = consentData.categories || {};
        // Site Kit: no explicit deny signals before the visitor has made a choice.
        if ((consentData.isUserActionCompleted === false) && gskEnabled && !Object.values(categories).slice(1).includes(true)) {
            return;
        }
        applyCategories(consentData);
    }

    /**
     * Apply the stored consent without waiting for cookieyes_consent_update. That event does not fire
     * for a returning visitor, and when JavaScript is delayed it fires before this file has loaded.
     *
     * @param {boolean} lawResolved The banner has resolved the visitor's law, even if no law applies.
     * @param {boolean} pageReady   The page has loaded, so later plugins are listening.
     */
    function applyStoredConsent(lawResolved, pageReady) {
        const consentData = getConsentData();
        // Until the web app banner has resolved the visitor's region, the law is unknown. Announcing a
        // guess would make plugins act on the wrong type; the server's type covers the meantime.
        if (!consentData || (!consentData.activeLaw && !lawResolved)) {
            return;
        }
        const announceAgain = pageReady && !announcedWhenReady;
        if (pageReady) {
            announcedWhenReady = true;
        }
        setConsentType(consentData, announceAgain);
        // Only a stored choice: before the banner has stored its defaults every category reads as not
        // granted, which would opt CCPA visitors out. Defaults arrive with cookieyes_consent_update.
        if (!storedChoiceApplied && consentData.isUserActionCompleted === true) {
            storedChoiceApplied = applyCategories(consentData);
        }
    }

    /**
     * The banner script never loaded (blocked or failed). Announce optin anyway, so plugins told to
     * wait for wp_consent_type_defined do not wait forever; the visitor's wp_consent_* cookies from
     * earlier visits still apply.
     */
    function announceWithoutBanner() {
        if (announcedType === null && typeof window.getCkyConsent !== 'function') {
            window.wp_consent_type = 'optin';
            announcedType = 'optin';
            document.dispatchEvent(new CustomEvent('wp_consent_type_defined'));
        }
    }

    document.addEventListener("cookieyes_consent_update", onConsentUpdate);
    // The web app banner fires this once it has resolved the visitor's region and law.
    document.addEventListener("cookieyes_banner_load", function () {
        applyStoredConsent(true, false);
    });
    // Once more when the page is ready, so plugins whose scripts load after this one (WooCommerce,
    // Site Kit) receive wp_consent_type_defined. When an optimiser delays JavaScript the page is
    // already ready and LiteSpeed (7.9.3) does not fire this event again; those plugins then rely on
    // the server-side type, which wp_has_consent() falls back to.
    document.addEventListener("DOMContentLoaded", function () {
        applyStoredConsent(false, true);
    });
    window.addEventListener("load", announceWithoutBanner);
    // Loaded after the page (an optimiser delayed or async-loaded the scripts): the banner may already
    // have fired cookieyes_banner_load, so take the law as resolved, even when no law applies.
    const loadedLate = document.readyState === 'complete';
    applyStoredConsent(loadedLate, false);
    if (loadedLate) {
        announceWithoutBanner();
    }
})();
