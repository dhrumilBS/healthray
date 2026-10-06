document.addEventListener("DOMContentLoaded", () => {

    function isCrawler() {
        const ua = navigator.userAgent.toLowerCase();

        const crawlers = [
            "googlebot",
            "AhrefsSiteAudit",
            "facebookexternalhit",
            "linkedinbot"
        ];

        return crawlers.some(bot => ua.includes(bot));
    }

    const thisDomain = window.location.hostname.replace(/^www\./, '');
    console.log(thisDomain);
    

    const getReferer = () => {
        if (!document.referrer) return "direct"; // FIX 1: Use "direct" instead of thisDomain

        try {
            return new URL(document.referrer).hostname.replace(/^www\./, '');
        } catch (_) {
            return "direct";
        }
    };

    const isValidUrl = (url) => {
        try {
            new URL(url);
            return true;
        } catch (_) {
            return false;
        }
    };

    const isSameDomain = (url) => {
        try {
            // FIX 2: Strict equality check instead of .includes() to prevent spoofing
            return new URL(url).hostname.replace(/^www\./, '') === thisDomain;
        } catch (_) {
            return false;
        }
    };

	const appendUTMParams = (encodedParams) => {
		const links = document.querySelectorAll('.elementor-567 a, .wrap a, .footer-section a');
		links.forEach((a) => {
			const href = a.getAttribute("href");
			if (isValidUrl(href) && isSameDomain(href)) {
				const url = new URL(href, window.location.origin);
				const currentParams = new URLSearchParams(url.search);

				if (![...currentParams.keys()].some(key => key.startsWith("utm_"))) {
					const sep = currentParams.toString() ? "&" : "?";
					a.setAttribute("href", href + sep + encodedParams);
				}
			}
		});
	};

	const getExistingUTMs = () => {
        const params = new URLSearchParams(window.location.search);
        const allowed = ["utm_source", "utm_medium", "utm_campaign"];
        const collected = new URLSearchParams();
        allowed.forEach((key) => {
            if (params.has(key)) collected.set(key, params.get(key));
        });
        return collected.toString();
    };

    // FIX 3: Populate form fields from UTM params (works for both URL UTMs and default UTMs)
    const populateFormFields = (utm_source, utm_medium, utm_campaign) => {
        const formUTMs = {
            utm_source: utm_source,
            utm_medium: utm_medium,
            utm_campaign: utm_campaign
        };

        setTimeout(() => {
            Object.keys(formUTMs).forEach((key) => {
                document.querySelectorAll(`input[name="${key}"]`).forEach((field) => {
                    field.value = formUTMs[key];
                });
            });
        }, 500);
    };

    const referer = getReferer();
    const utmFromUrl = getExistingUTMs();

    if (!isCrawler()) {
        console.log("Human visitor");

        if (utmFromUrl) {
            appendUTMParams(utmFromUrl);
            const params = new URLSearchParams(utmFromUrl);
            populateFormFields(
                params.get("utm_source") || "",
                params.get("utm_medium") || "",
                params.get("utm_campaign") || ""
            );
        } else {
            const utm_source = referer;
            const utm_medium = "organic";
            const utm_campaign = "seo";
            const defaultUTMs = new URLSearchParams({ utm_source, utm_medium, utm_campaign }).toString();

            populateFormFields(utm_source, utm_medium, utm_campaign);
            appendUTMParams(defaultUTMs);
        }
    } else {
        console.log("Crawler detected");
    }
});
/* End Optimized User Tracking Code */