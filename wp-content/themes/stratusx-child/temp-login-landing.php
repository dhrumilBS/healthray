<?php

/*
Template Name: Login Portal Landing Page
Page ID:     81325   (/log-in/)
*/

if (! defined('ABSPATH')) {
	exit;
}
?>

<main class="login-landing-main">
	<a class="login-brand" href="<?php echo esc_url(home_url('/')); ?>" aria-label="<?php echo esc_attr(get_bloginfo('name') . ' home'); ?>">
		<img src="<?php echo esc_url(content_url('/uploads/2024/02/Healthray-Logo.svg')); ?>" width="150" height="83" alt="Healthray" fetchpriority="high" decoding="async">
	</a>

	<section class="hero login-hero" aria-labelledby="login-hero-title">
		<div class="wrap">
			<div class="section-head center">
				<h1 id="login-hero-title" class="eyebrow">Healthray log-in</h1>
				<p class="login-headline">Sign in to Your Healthray Account</p>
				<p>Select your portal to continue to your workspace.</p>
			</div>
		</div>
	</section>

	<!-- ---- Portal cards ---- -->
	<section class="login-portals" aria-label="Choose your Healthray portal">
		<div class="wrap">
			<div class="grid-3 login-portal-grid">
				<a class="login-portal-card portal-ray is-featured" data-portal="ray" href="https://ray.healthray.com/" target="_blank" rel="noopener">
					<span class="portal-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" xmlns="http://www.w3.org/2000/svg">
							<path d="M4.6 20.4V5.8A1.8 1.8 0 0 1 6.4 4h11.2a1.8 1.8 0 0 1 1.8 1.8v14.6" stroke-linejoin="round" />
							<path d="M2.6 20.4h18.8" stroke-linecap="round" />
							<path d="M12 7v3.4M10.3 8.7h3.4" stroke-linecap="round" />
							<path d="M10.2 20.4v-4.2h3.6v4.2" stroke-linejoin="round" />
						</svg>
					</span>
					<h2 class="portal-title">Hospital / HMS</h2>
					<p class="portal-desc">OPD, IPD, Appointments, EMR, Billing, Administration and more.</p>
					<span class="portal-go" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
							<path d="M5 12h13M12.5 6.5 19 12l-6.5 5.5" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</span>
					<span class="portal-host">ray.healthray.com<span class="login-sr-only"> (opens in a new tab)</span></span>
				</a>

				<a class="login-portal-card portal-pharmacy" data-portal="pharmacy" href="https://pharmacy.healthray.com/" target="_blank" rel="noopener">
					<span class="portal-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" xmlns="http://www.w3.org/2000/svg">
							<path d="M13.9 4.3a4.4 4.4 0 0 1 6.2 6.2l-9.6 9.6a4.4 4.4 0 0 1-6.2-6.2l9.6-9.6Z" stroke-linejoin="round" />
							<path d="m9.1 9.1 5.8 5.8" stroke-linecap="round" />
						</svg>
					</span>
					<h2 class="portal-title">Pharmacy</h2>
					<p class="portal-desc">Inventory, Billing, Purchase, Sales, Expiry Management and more.</p>
					<span class="portal-go" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
							<path d="M5 12h13M12.5 6.5 19 12l-6.5 5.5" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</span>
					<span class="portal-host">pharmacy.healthray.com<span class="login-sr-only"> (opens in a new tab)</span></span>
				</a>

				<a class="login-portal-card portal-lab" data-portal="lab" href="https://lab.healthray.com/" target="_blank" rel="noopener">
					<span class="portal-icon" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" xmlns="http://www.w3.org/2000/svg">
							<path d="M9 3.4h6" stroke-linecap="round" />
							<path d="M10 3.4v5L5.5 17.5a1.9 1.9 0 0 0 1.7 2.9h9.6a1.9 1.9 0 0 0 1.7-2.9L14 8.4v-5" stroke-linejoin="round" />
							<path d="M7.7 14.8h8.6" stroke-linecap="round" />
						</svg>
					</span>
					<h2 class="portal-title">Laboratory</h2>
					<p class="portal-desc">Sample Registration, Test Management, Reports, Integrations and more.</p>
					<span class="portal-go" aria-hidden="true">
						<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg">
							<path d="M5 12h13M12.5 6.5 19 12l-6.5 5.5" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</span>
					<span class="portal-host">lab.healthray.com<span class="login-sr-only"> (opens in a new tab)</span></span>
				</a>

			</div>
		</div>
	</section>

	<!-- ---- Support card (stands in for the removed site footer) ---- -->
	<section class="login-contact-note" aria-labelledby="login-help-title">
		<div class="wrap">

			<div class="login-help-card">
				<span class="help-icon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" xmlns="http://www.w3.org/2000/svg">
						<path d="M4.6 13.4v-1.2a7.4 7.4 0 0 1 14.8 0v1.2" stroke-linecap="round" />
						<rect x="2.7" y="13" width="4.1" height="6.3" rx="1.6" />
						<rect x="17.2" y="13" width="4.1" height="6.3" rx="1.6" />
						<path d="M19.2 19.3v.6a2.4 2.4 0 0 1-2.4 2.4h-2.5" stroke-linecap="round" />
					</svg>
				</span>

				<div class="help-copy">
					<p id="login-help-title" class="help-title">Need help?</p>
					<p>Can&rsquo;t find your portal? Contact Support.</p>
				</div>

				<span class="help-divider" aria-hidden="true"></span>
				<a class="help-btn" href="mailto:contact@healthray.com">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<rect x="2.8" y="5" width="18.4" height="14" rx="2.4" />
						<path d="m3.7 6.6 8.3 5.9 8.3-5.9" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
					Contact Support
				</a>
			</div>

			<p class="login-back">
				<a href="<?php echo esc_url(home_url('/')); ?>">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
						<path d="M19 12H6M11.5 5.5 5 12l6.5 6.5" stroke-linecap="round" stroke-linejoin="round" />
					</svg>
					Back to Healthray website
				</a>
			</p>

		</div>
	</section>

</main>

<style id="login-landing-inline-css">
	.login-landing .portal-ray {
		--portal-accent: #2563EB;
		--portal-tint: #E8F0FE;
		--portal-wash: linear-gradient(180deg, #F3F8FF 0%, #E9F1FE 100%);
		--portal-border: #BFD6FB;
	}

	.login-landing .portal-pharmacy {
		--portal-accent: #10B981;
		--portal-tint: #DFF6EC;
		--portal-wash: linear-gradient(180deg, #F2FDF8 0%, #E6F8F0 100%);
		--portal-border: #A9E6CC;
	}

	.login-landing .portal-lab {
		--portal-accent: #7C3AED;
		--portal-tint: #EDE6FE;
		--portal-wash: linear-gradient(180deg, #F8F5FF 0%, #F0E9FE 100%);
		--portal-border: #CBB6FA;
	}

	.login-landing { background: #F2F6FD; }
	.login-landing .content { background: #F2F6FD; }
	.login-landing .login-landing-main { position: relative; min-height: 100vh; overflow: hidden; background: linear-gradient(180deg, #FAFCFF 0%, #F2F6FD 100%); }
	.login-landing .login-landing-main::before,
	.login-landing .login-landing-main::after { content: ""; position: absolute; border-radius: 50%; pointer-events: none; z-index: 0; }
	.login-landing .login-landing-main::before { width: 430px; height: 430px; left: -250px; top: 190px; background: rgba(37, 99, 235, 0.05); }
	.login-landing .login-landing-main::after { width: 620px; height: 620px; right: -320px; top: -140px; background: rgba(124, 58, 237, 0.045); }
	.login-landing .login-landing-main>* { position: relative; z-index: 1; }
	.login-landing .login-sr-only { position: absolute; width: 1px; height: 1px; margin: -1px; padding: 0; overflow: hidden; clip: rect(0 0 0 0); clip-path: inset(50%); white-space: nowrap; border: 0; }
	.login-landing .login-brand { display: flex; justify-content: center; padding: 34px 24px 0; }
	.login-landing .login-brand img { width: auto; height: 50px; }
	.login-landing .login-brand:focus-visible { outline-offset: 6px; }
	.login-landing .login-hero { padding: 42px 0 30px; background: none; }
	.login-landing .login-hero .section-head.center { max-width: 780px; margin-bottom: 0; }
	.login-landing .login-hero .eyebrow { margin: 0 0 12px; color: var(--hr-ink-faint); font-size: 0.74rem; font-weight: 700; line-height: 1.6; letter-spacing: 0.22em; text-transform: uppercase; }
	.login-landing .login-hero .eyebrow::before { display: none; }
	.login-landing .login-hero .section-head .login-headline { margin: 0; color: var(--brand-deep); font-family: var(--font-display); font-size: clamp(1.75rem, 3.2vw, 2.35rem); font-weight: 800; line-height: 1.15; letter-spacing: -0.02em; }
	.login-landing .login-hero .section-head p { margin-top: 14px; font-size: 1.02rem; color: var(--hr-ink-faint); }
	.login-landing .login-portals { padding: 0 0 40px; }
	.login-landing .login-portal-grid { align-items: stretch; gap: 22px; max-width: 1020px; margin: 0 auto; }
	.login-landing .login-portal-card { position: relative; display: flex; flex-direction: column; align-items: flex-start; height: 100%; padding: 28px; background: #fff; border: 1px solid #E7ECF6; border-radius: 20px; box-shadow: 0 10px 30px rgba(18, 32, 51, 0.05); transition: transform 0.18s ease, box-shadow 0.18s ease, border-color 0.18s ease; }
	.login-landing .login-portal-card:hover { transform: translateY(-4px); border-color: var(--portal-accent); box-shadow: 0 18px 40px rgba(18, 32, 51, 0.1); }
	.login-landing .portal-icon { display: grid; place-items: center; width: 54px; height: 54px; margin-bottom: 20px; border-radius: 15px; background: var(--portal-tint); color: var(--portal-accent); }
	.login-landing .portal-icon svg { width: 27px; height: 27px; }
	.login-landing .portal-title { margin: 0 0 10px; color: var(--brand-deep); font-size: 1.32rem; font-weight: 800; letter-spacing: -0.01em; text-transform: none; }
	.login-landing .portal-desc { margin: 0 0 26px; color: var(--hr-ink-faint); font-size: 0.94rem; line-height: 1.55; }
	.login-landing .portal-go { display: grid; place-items: center; width: 42px; height: 42px; margin-top: auto; border-radius: 50%; background: var(--portal-tint); color: var(--portal-accent); transition: transform 0.18s ease, box-shadow 0.18s ease; }
	.login-landing .portal-go svg { width: 19px; height: 19px; }
	.login-landing .is-featured .portal-go { background: var(--portal-accent); color: #fff; box-shadow: 0 6px 16px rgba(18, 32, 51, 0.2); }
	.login-landing .login-portal-card:hover .portal-go { transform: translateX(3px); }
	.login-landing .portal-host { width: 100%; margin-top: 24px; padding-top: 16px; border-top: 1px solid rgba(18, 32, 51, 0.08); color: var(--hr-ink-muted); font-size: 0.84rem; letter-spacing: 0.01em; }
	.login-landing .login-portal-card.is-last-used { border-color: var(--portal-accent); }
	.login-landing .login-portal-card .portal-last-used { position: absolute; top: 18px; right: 18px; padding: 4px 10px; border-radius: 99px; background: var(--portal-tint); color: var(--portal-accent); font-size: 0.64rem; font-weight: 700; letter-spacing: 0.06em; text-transform: uppercase; line-height: 1.5; }
	.login-landing .login-contact-note { padding: 8px 0 56px; }
	.login-landing .login-help-card { display: flex; align-items: center; gap: 20px; max-width: 1020px; margin: 0 auto; padding: 20px 24px; background: #fff; border: 1px solid #E7ECF6; border-radius: 18px; box-shadow: 0 10px 30px rgba(18, 32, 51, 0.05); }
	.login-landing .help-icon { flex: none; display: grid; place-items: center; width: 48px; height: 48px; border-radius: 50%; background: #E8F0FE; color: var(--brand); }
	.login-landing .help-icon svg { width: 24px; height: 24px; }
	.login-landing .help-copy { flex: 1 1 0; min-width: 0; }
	.login-landing .login-help-card .help-copy .help-title { margin: 0 0 3px; color: var(--brand-deep); font-family: var(--font-display); font-size: 1.06rem; font-weight: 800; line-height: 1.15; letter-spacing: -0.01em; }
	.login-landing .help-copy p { margin: 0; font-size: 0.92rem; color: var(--hr-ink-faint); }
	.login-landing .help-divider { flex: none; align-self: stretch; width: 1px; margin: 2px 4px; background: rgba(18, 32, 51, 0.1); }
	.login-landing .help-btn { flex: none; display: inline-flex; align-items: center; gap: 10px; padding: 12px 22px; background: #fff; border: 1.5px solid var(--portal-border, #BFD6FB); border-radius: 12px; color: var(--brand); font-size: 0.94rem; font-weight: 700; transition: background 0.15s ease, border-color 0.15s ease, transform 0.15s ease; }
	.login-landing .help-btn:hover { background: #F3F8FF; border-color: var(--brand); transform: translateY(-1px); }
	.login-landing .help-btn svg { width: 18px; height: 18px; }
	.login-landing .login-back { margin: 22px 0 0; text-align: center; }
	.login-landing .login-back a { display: inline-flex; align-items: center; gap: 8px; color: var(--brand); font-size: 0.94rem; font-weight: 700; }
	.login-landing .login-back svg { width: 18px; height: 18px; transition: transform 0.15s ease; }
	.login-landing .login-back a:hover svg { transform: translateX(-3px); }
	@media (max-width: 980px) {
		.login-landing .login-portal-grid { gap: 18px; }
		.login-landing .login-portal-card { padding: 24px; }	
	}

	@media (max-width: 720px) {
		.login-landing .login-brand { padding: 24px 16px 0; }
		.login-landing .login-brand img { height: 34px; }
		.login-landing .login-hero { padding: 28px 0 30px; }
		.login-landing .login-portals { padding-bottom: 44px; }
		.login-landing .login-portal-card { padding: 22px; }
		.login-landing .portal-icon { width: 48px; height: 48px; margin-bottom: 16px; }
		.login-landing .portal-icon svg { width: 24px; height: 24px; }
		.login-landing .portal-desc { margin-bottom: 22px; }
		.login-landing .login-contact-note { padding-bottom: 48px; }
		.login-landing .login-landing-main::before,
		.login-landing .login-landing-main::after { display: none; }
		.login-landing .login-help-card { flex-wrap: wrap; gap: 14px; padding: 18px; }
		.login-landing .help-divider { display: none; }
		.login-landing .help-btn { width: 100%; justify-content: center; }	
	}

	@media (max-width: 767px) {
		.login-landing .login-hero .eyebrow { display: inline-flex; }	
	}

	@media (prefers-reduced-motion: reduce) {
		.login-landing .login-portal-card,
		.login-landing .login-portal-card:hover,
		.login-landing .portal-go,
		.login-landing .login-portal-card:hover .portal-go {
			transform: none;
			transition: none;
		}
	}
</style>

<script id="login-landing-inline-js">
	(function() {
		'use strict';
		var STORAGE_KEY = 'hrLastLoginPortal';
		var LABEL = 'Last used';
		function readPortal() {
			try {
				return window.localStorage.getItem(STORAGE_KEY);
			} catch (e) {
				return null;
			}
		}

		function writePortal(value) {
			try {
				window.localStorage.setItem(STORAGE_KEY, value);
			} catch (e) {
			}
		}

		function init() {
			var root = document.querySelector('.login-landing-main');
			if (!root) {
				return;
			}

			var cards = root.querySelectorAll('.login-portal-card[data-portal]');
			if (!cards.length) {
				return;
			}

			var lastUsed = readPortal();
			Array.prototype.forEach.call(cards, function(card) {
				var portal = card.getAttribute('data-portal');
				if (!portal) {
					return;
				}

				card.addEventListener('click', function() {
					writePortal(portal);
				});

				if (portal !== lastUsed) {
					return;
				}

				card.classList.add('is-last-used');
				var flag = document.createElement('span');
				flag.className = 'portal-last-used';
				flag.textContent = LABEL;
				card.appendChild(flag);
			});
		}

		if (document.readyState === 'loading') {
			document.addEventListener('DOMContentLoaded', init, {
				once: true
			});
		} else {
			init();
		}
	})();
</script>