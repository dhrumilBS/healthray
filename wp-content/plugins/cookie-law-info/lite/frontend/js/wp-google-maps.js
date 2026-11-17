(function () {
    'use strict';

    const config = window._ckyWpgmza;
    if (!config) {
        return;
    }
    const category = config.category || 'functional';
    const scriptSelector = 'script[type="text/plain"][data-cky-wpgmza-src]';
    const placeholderTag = 'wpgmza-placeholder';
    const styleId = 'cky-wpgmza-style';
    const wpgmzaConsentCookie = 'wpgmza-api-consent-given';
    const reloadKey = 'cky-wpgmza-reloaded';
    let released = false;
    let reloading = false;
    let warnedStale = false;

    /**
     * Whether WP Go Maps' own consent cookie is set.
     */
    function hasWpgmzaConsentCookie() {
        return document.cookie.split(';').some(function (item) {
            return item.trim().indexOf(wpgmzaConsentCookie + '=') === 0;
        });
    }

    /**
     * Set or remove WP Go Maps' own consent cookie, so its "Require consent
     * before loading" setting follows the CookieYes choice. It lasts as long
     * as the CookieYes consent cookie, so a returning visitor's page already
     * has the maps API instead of reloading in every new browser session.
     */
    function setWpgmzaConsentCookie(given) {
        if (given === hasWpgmzaConsentCookie()) {
            return;
        }
        const store = window._ckyConfig;
        const days = store && store._expiry ? store._expiry : 365;
        const expires = given ? new Date(Date.now() + days * 864e5) : new Date(0);
        const secure = window.location.protocol === 'https:' ? '; Secure' : '';
        document.cookie = wpgmzaConsentCookie + '=' + (given ? 'true' : '') + '; expires=' + expires.toUTCString() + '; path=/; SameSite=Lax' + secure;
    }

    /**
     * Whether this page may be reloaded to let WP Go Maps load the maps API.
     * A consent choice always may. Otherwise only once per page and browser
     * session: a page served from a cache (WP Rocket, a CDN) still says no
     * consent after the reload, and would reload again and again. The pages
     * already reloaded are kept for the session, so moving between cached
     * pages does not reload each of them again.
     */
    function mayReload(fromConsentEvent) {
        const page = window.location.pathname + window.location.search;
        try {
            let pages = JSON.parse(window.sessionStorage.getItem(reloadKey) || '[]');
            if (!Array.isArray(pages)) {
                pages = [];
            }
            if (pages.indexOf(page) !== -1) {
                return !!fromConsentEvent;
            }
            pages.push(page);
            window.sessionStorage.setItem(reloadKey, JSON.stringify(pages));
            return true;
        } catch (e) {
            return !!fromConsentEvent;
        }
    }

    /**
     * Whether WP Go Maps left the maps API out of this page while waiting for its own consent.
     */
    function isWpgmzaWaitingForConsent() {
        const status = window.wpgmza_google_api_status;
        return !!status && status.code === 'USER_CONSENT_NOT_GIVEN';
    }

    /**
     * Whether the visitor has already consented to the maps category.
     */
    function hasConsent() {
        try {
            if (typeof window.getCkyConsent !== 'function') {
                return false;
            }
            const consent = window.getCkyConsent();
            return !!consent && !!consent.categories && consent.categories[category] === true;
        } catch (e) {
            return false;
        }
    }

    /**
     * Inject the placeholder styles once.
     */
    function addStyle() {
        if (document.getElementById(styleId)) {
            return;
        }
        const style = document.createElement('style');
        style.id = styleId;
        style.textContent = '.cky-wpgmza-placeholder[data-cky-tag]{visibility:visible;position:absolute;top:0;left:0;width:100%;height:100%;box-sizing:border-box;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:12px;padding:16px;background:#e8e8e8;color:#212121;text-align:center;font:14px/1.5 sans-serif;z-index:9}' +
            '.cky-wpgmza-placeholder button{padding:8px 16px;border:1px solid #212121;border-radius:2px;background:#fff;color:#212121;font:inherit;cursor:pointer}' +
            '.cky-wpgmza-placeholder button:hover,.cky-wpgmza-placeholder button:focus{background:#212121;color:#fff}';
        (document.head || document.documentElement).appendChild(style);
    }

    /**
     * Draw one placeholder over every blocked map container.
     */
    function addPlaceholders() {
        if (released) {
            return;
        }
        const maps = document.querySelectorAll('.wpgmza_map');
        if (!maps.length) {
            return;
        }
        addStyle();
        Array.prototype.forEach.call(maps, function (map) {
            if (map.querySelector(':scope > [data-cky-tag="' + placeholderTag + '"]')) {
                return;
            }
            if (window.getComputedStyle(map).position === 'static') {
                map.style.position = 'relative';
                map.setAttribute('data-cky-wpgmza-positioned', 'true');
            }
            const overlay = document.createElement('div');
            overlay.className = 'cky-wpgmza-placeholder';
            overlay.setAttribute('data-cky-tag', placeholderTag);
            const text = document.createElement('div');
            text.textContent = config.text || '';
            overlay.appendChild(text);
            const button = document.createElement('button');
            button.type = 'button';
            button.textContent = config.button || '';
            button.addEventListener('click', function () {
                if (typeof window.revisitCkyConsent === 'function') {
                    window.revisitCkyConsent();
                }
            });
            overlay.appendChild(button);
            map.appendChild(overlay);
        });
    }

    /**
     * Remove all placeholders and restore the container positioning.
     */
    function removePlaceholders() {
        Array.prototype.forEach.call(document.querySelectorAll('[data-cky-tag="' + placeholderTag + '"]'), function (item) {
            item.remove();
        });
        Array.prototype.forEach.call(document.querySelectorAll('[data-cky-wpgmza-positioned]'), function (map) {
            map.style.position = '';
            map.removeAttribute('data-cky-wpgmza-positioned');
        });
        const style = document.getElementById(styleId);
        style && style.remove();
    }

    /**
     * Whether a held URL points to the Google Maps API.
     */
    function isMapsApiUrl(src) {
        try {
            const host = new URL(src, window.location.href).hostname;
            return host === 'maps.googleapis.com' || host === 'maps.google.com';
        } catch (e) {
            return false;
        }
    }

    /**
     * Load the held scripts. WP Go Maps keeps polling for the maps API and
     * starts the maps once it is available. Safe to call again: a held tag
     * printed later (classic themes print it in the footer) is released too.
     *
     * @param {boolean} fromConsentEvent Whether a consent choice triggered it.
     */
    function release(fromConsentEvent) {
        if (reloading) {
            return;
        }
        released = true;
        setWpgmzaConsentCookie(true);
        // WP Go Maps decides on the server, so the page has no maps API to release:
        // reload once the cookie is stored, as WP Go Maps' own consent button does.
        if (isWpgmzaWaitingForConsent() && hasWpgmzaConsentCookie()) {
            if (mayReload(fromConsentEvent)) {
                reloading = true;
                window.location.reload();
                return;
            }
            if (!warnedStale && window.console) {
                warnedStale = true;
                console.warn('CookieYes: this page was served without the Google Maps API although the visitor consented, probably from a page cache. Turn off "Require consent before loading" in WP Go Maps, or exclude this page from the cache.');
            }
        }
        removePlaceholders();
        Array.prototype.forEach.call(document.querySelectorAll(scriptSelector), function (inert) {
            const src = inert.getAttribute('data-cky-wpgmza-src');
            const id = inert.id;
            inert.remove();
            if (src && isMapsApiUrl(src)) {
                whenWpgmzaReady(function () {
                    const script = document.createElement('script');
                    script.src = src;
                    if (id) {
                        script.id = id;
                    }
                    document.head.appendChild(script);
                });
            }
        });
    }

    /**
     * Run once WP Go Maps has defined the callback the maps API calls on load.
     * Its scripts can run much later than this one: an optimizer such as WP
     * Rocket's Delay JavaScript execution holds them until the visitor first
     * interacts. Loading the API before the callback exists would break the
     * map, so keep waiting: every 200 ms for 30 seconds, then every second.
     */
    function whenWpgmzaReady(callback) {
        let tries = 0;
        (function wait() {
            if (typeof window.__wpgmzaMapEngineLoadedCallback === 'function') {
                callback();
                return;
            }
            if (++tries === 150 && window.console) {
                console.warn('CookieYes: WP Go Maps has not loaded yet, so the Google Maps API is still waiting for it.');
            }
            setTimeout(wait, tries < 150 ? 200 : 1000);
        })();
    }

    /**
     * Release when consent is already given, otherwise show the placeholders.
     */
    function check() {
        if (released || hasConsent()) {
            release(false);
        } else {
            setWpgmzaConsentCookie(false);
            addPlaceholders();
        }
    }

    document.addEventListener('cookieyes_consent_update', function (event) {
        const accepted = event && event.detail && event.detail.accepted;
        if (Array.isArray(accepted) && accepted.indexOf(category) !== -1) {
            release(true);
        } else {
            setWpgmzaConsentCookie(false);
        }
    });

    check();
    document.addEventListener('cookieyes_banner_load', check);
    document.addEventListener('DOMContentLoaded', check);
    window.addEventListener('load', check);
})();
