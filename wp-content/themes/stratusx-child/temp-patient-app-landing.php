<?php
/**
 * Template Name: Patient App Landing
 * Template Post Type: page
 *
 * Page:       /patient-mobile-app/ (page 30956) - replaces the Elementor layout.
 * Body class: landing-page patient-app-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_patient_app_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Patient Mobile App" (Yoast). Product page: SoftwareApplication schema in functions.php.
 * Note:       Rewritten from the old features/benefits copy, keeping the structure and
 *             only claims the site already makes. This is the patient APP; the broader
 *             portal+app platform lives at /patient-engagement-software/ (cross-linked, not duplicated).
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_pa_faq = function_exists('healthray_patient_app_faq_items') ? healthray_patient_app_faq_items() : array();
$hr_check = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon = static function ($paths) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
			<li><span aria-current="page">Patient Mobile App</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Appointments · reminders · reports · ABHA · online payments</p>
			<h1 id="hero-title">Patient mobile app for appointments, reports and
				<span class="pulse-word">ABHA linking
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">The Healthray patient mobile app lets patients book appointments, get appointment and medicine reminders, read their prescriptions, lab reports and bills, pay online, link their ABHA number and consult a doctor by video, audio or chat, all from their phone.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Book appointments and get appointment and medicine reminders</li>
				<li><?php echo $hr_check; ?> View prescriptions, lab reports and bills, and pay online</li>
				<li><?php echo $hr_check; ?> Link an ABHA number and consult by video, audio or chat</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See the patient app on your own workflow. Our team replies within one business day.</p>
			<?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]'); ?>
		</div>
	</div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Built on standards</p>
			<h2 id="compliance-title">Approved, certified and built on open health standards</h2>
		</div>
		<div class="comp-grid">
			<div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/abdm-healthray-150x150.webp'); ?>" alt="ABDM compliant hospital software - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/abha-healthray-150x150.webp'); ?>" alt="ABHA integrated health records" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2025/07/NHA.webp'); ?>" alt="NHA approved software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
			<a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"><img src="<?php echo esc_url($hr_media . '/2026/07/NABH-ehr-certified-150x150.webp'); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span></a>
			<div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/Hipaa-healthray-150x150.webp'); ?>" alt="HIPAA compliant hospital software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/iso-27001-healthray-150x150.webp'); ?>" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/hl7-healthray-150x150.webp'); ?>" alt="HL7 interoperable hospital software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2025/07/FHIR.webp'); ?>" alt="FHIR compliant health record exchange" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
		</div>
	</div>
</section>

<!-- ============ WHAT IS THE PATIENT MOBILE APP ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is the patient mobile app?</h2>
		</div>
		<p>The Healthray patient mobile app is a free app for patients, on Android and iOS. From their phone, patients book and reschedule appointments, get reminders, read their prescriptions, lab reports and bills, pay online and consult a doctor by video, audio or chat.</p>
		<p>It runs on the same record your doctors and front desk already use, so everything a patient sees is current. Patients can also link their <a href="<?php echo esc_url(home_url('/abdm/')); ?>">ABHA number</a> and keep their health records in one place across providers.</p>
		<p>The app is the patient's side of Healthray's <a href="<?php echo esc_url(home_url('/patient-engagement-software/')); ?>">patient engagement software</a>, the full portal and tools your team uses to stay in touch with patients between visits.</p>
	</div>
</section>

<!-- ============ APP DOWNLOAD ============ -->
<section class="app-cta" aria-labelledby="download-title">
	<div class="wrap">
		<div class="app-dl">
			<div class="app-dl-copy">
				<p class="eyebrow">Download free</p>
				<h2 id="download-title">Get the Healthray patient app</h2>
				<p>Book appointments, get reminders, read your prescriptions, lab reports and bills, pay online and consult a doctor, all from your phone.</p>
				<div class="store-badges">
					<a class="store-badge" href="<?php echo esc_url('https://play.google.com/store/apps/details?id=com.healthray.patient'); ?>" target="_blank" rel="noopener" aria-label="Get the Healthray patient app on Google Play">
						<svg viewBox="0 0 512 512" aria-hidden="true"><path fill="#00D2FF" d="M26 20.6C22.3 24.5 20.1 30.6 20.1 38.4v435.2c0 7.8 2.2 13.9 5.9 17.8l1.5 1.4 243.8-243.8v-5.8L27.5 19.2z"/><path fill="#FFCE00" d="M352.8 340.2l-81.3-81.3v-5.8l81.4-81.4 1.8 1.1 96.4 54.8c27.5 15.6 27.5 41.2 0 56.9l-96.4 54.8z"/><path fill="#FF3C4A" d="M354.6 339.1 271.5 256 26 501.5c9.1 9.6 24 10.8 40.9 1.3l287.7-163.7z"/><path fill="#00E676" d="M354.6 172.9 66.9 9.2C50-.3 35.1.9 26 10.5L271.5 256z"/></svg>
						<span><small>Get it on</small><b>Google Play</b></span>
					</a>
					<a class="store-badge" href="<?php echo esc_url('https://apps.apple.com/in/app/healthray-for-patients/id6470002190'); ?>" target="_blank" rel="noopener" aria-label="Download the Healthray patient app on the App Store">
						<svg viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M17.57 12.62c-.02-2.33 1.9-3.45 1.99-3.5-1.08-1.59-2.77-1.8-3.37-1.83-1.43-.15-2.8.84-3.53.84-.73 0-1.85-.82-3.04-.8-1.56.02-3.01.91-3.81 2.31-1.63 2.82-.42 7 1.17 9.29.78 1.12 1.7 2.38 2.91 2.33 1.17-.05 1.61-.76 3.03-.76 1.42 0 1.81.76 3.04.73 1.26-.02 2.05-1.14 2.82-2.27.89-1.3 1.25-2.56 1.27-2.62-.03-.01-2.44-.94-2.46-3.72zM15.25 5.87c.65-.79 1.09-1.88.97-2.97-.94.04-2.07.63-2.74 1.41-.6.7-1.12 1.81-.98 2.88 1.05.08 2.11-.53 2.75-1.32z"/></svg>
						<span><small>Download on the</small><b>App Store</b></span>
					</a>
				</div>
				<p class="app-trust">
					<span><?php echo $hr_check; ?> Free to download</span>
					<span><?php echo $hr_check; ?> Android &amp; iOS</span>
					<span><?php echo $hr_check; ?> ABHA ready</span>
				</p>
			</div>
			<div class="app-dl-art">
				<svg class="app-phone" viewBox="0 0 280 560" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="The Healthray app on a phone">
					<defs>
						<linearGradient id="hrBar" x1="18" y1="48" x2="262" y2="96" gradientUnits="userSpaceOnUse"><stop stop-color="#1D4ED8"/><stop offset="1" stop-color="#2563EB"/></linearGradient>
						<linearGradient id="hrCard" x1="30" y1="112" x2="250" y2="206" gradientUnits="userSpaceOnUse"><stop stop-color="#2563EB"/><stop offset="1" stop-color="#1b3c74"/></linearGradient>
						<clipPath id="hrScreen"><rect x="18" y="16" width="244" height="528" rx="33"/></clipPath>
					</defs>
					<rect x="8" y="6" width="264" height="548" rx="42" fill="#fff"/>
					<rect x="8.5" y="6.5" width="263" height="547" rx="41.5" fill="none" stroke="#e6ecf7" stroke-width="2"/>
					<rect x="18" y="16" width="244" height="528" rx="33" fill="#f3f7ff"/>
					<rect x="112" y="24" width="56" height="12" rx="6" fill="#0d1b3e"/>
					<g clip-path="url(#hrScreen)">
						<rect x="18" y="48" width="244" height="50" fill="url(#hrBar)"/>
						<path d="M43 80C30 70 31 59 39 59c3 0 4 3 4 4 0-1 1-4 4-4 8 0 9 11-4 21z" fill="#fff"/>
						<rect x="66" y="64" width="78" height="9" rx="4.5" fill="#fff" opacity=".95"/>
						<circle cx="245" cy="73" r="11" fill="#fff" opacity=".9"/>
						<rect x="30" y="112" width="220" height="94" rx="16" fill="url(#hrCard)"/>
						<circle cx="60" cy="146" r="17" fill="#fff" opacity=".2"/>
						<path d="M60 138v16M52 146h16" stroke="#fff" stroke-width="2.6" stroke-linecap="round"/>
						<rect x="88" y="132" width="118" height="10" rx="5" fill="#fff" opacity=".95"/>
						<rect x="88" y="150" width="76" height="8" rx="4" fill="#fff" opacity=".6"/>
						<rect x="88" y="172" width="64" height="20" rx="10" fill="#fff"/>
						<rect x="30" y="224" width="220" height="52" rx="14" fill="#fff"/><circle cx="56" cy="250" r="14" fill="#eff4ff"/><rect x="80" y="242" width="120" height="8" rx="4" fill="#d7e0f2"/><rect x="80" y="257" width="78" height="7" rx="3.5" fill="#e9eefa"/>
						<rect x="30" y="286" width="220" height="52" rx="14" fill="#fff"/><circle cx="56" cy="312" r="14" fill="#eff4ff"/><rect x="80" y="304" width="104" height="8" rx="4" fill="#d7e0f2"/><rect x="80" y="319" width="66" height="7" rx="3.5" fill="#e9eefa"/>
						<rect x="30" y="348" width="220" height="52" rx="14" fill="#fff"/><circle cx="56" cy="374" r="14" fill="#eff4ff"/><rect x="80" y="366" width="118" height="8" rx="4" fill="#d7e0f2"/><rect x="80" y="381" width="58" height="7" rx="3.5" fill="#e9eefa"/>
						<rect x="18" y="498" width="244" height="46" fill="#fff"/>
						<rect x="18" y="498" width="244" height="1.5" fill="#eef2f9"/>
						<circle cx="54" cy="521" r="8" fill="#2563EB"/>
						<circle cx="116" cy="521" r="6.5" fill="#c3d0ea"/>
						<circle cx="178" cy="521" r="6.5" fill="#c3d0ea"/>
						<circle cx="240" cy="521" r="6.5" fill="#c3d0ea"/>
					</g>
				</svg>
			</div>
		</div>
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What patients can do</p>
			<h2 id="features-title">Everything the patient app does</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>'); ?></span>
				<h3>Appointments &amp; reminders</h3>
				<p>Patients book and reschedule appointments from the app and get a reminder before each visit, so they turn up on time and fewer slots go idle.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>'); ?></span>
				<h3>Prescriptions &amp; lab reports</h3>
				<p>Prescriptions, lab reports and past visits sit in one place on the patient's phone, ready to read or share whenever they need them.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 7h18v12H3zM3 11h18M7 15h4"/>'); ?></span>
				<h3>Online payments &amp; digital bills</h3>
				<p>Bills are raised digitally and paid online in a click, so there is no cash to carry and no queue at the billing counter.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M12 2l8 4v6c0 5-3.5 8-8 10-4.5-2-8-5-8-10V6z"/><path d="M9 12l2 2 4-4"/>'); ?></span>
				<h3>ABHA linking</h3>
				<p>Patients link their ABHA number so their health records stay in one place across providers. Built ABDM compliant and NHA approved.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="2" y="5" width="14" height="14" rx="2"/><path d="M16 10l6-3v10l-6-3z"/>'); ?></span>
				<h3>Teleconsultation</h3>
				<p>Patients consult doctors by video, audio or chat, all linked to the same record as an in-person visit. <a href="<?php echo esc_url(home_url('/telehealth-services/')); ?>">See telehealth services</a>.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M10.5 20.5a5 5 0 0 1-7-7l6-6a5 5 0 0 1 7 7z"/><path d="M8.5 8.5l7 7"/>'); ?></span>
				<h3>Medicine reminders &amp; health records</h3>
				<p>Timely medicine reminders help patients stay on their treatment, and a running health record lets them track visits, reports and spend over time.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">One app, better for patients and your hospital</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>'); ?> For patients</h3>
				<ul>
					<li><?php echo $hr_check; ?> Book, pay and consult without standing in a queue</li>
					<li><?php echo $hr_check; ?> Reminders before each visit and for medicines after it</li>
					<li><?php echo $hr_check; ?> Prescriptions, reports and bills on their own phone</li>
					<li><?php echo $hr_check; ?> ABHA linked, with records in one place across providers</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/>'); ?> For your hospital</h3>
				<ul>
					<li><?php echo $hr_check; ?> Fewer no-shows and idle slots from automatic reminders</li>
					<li><?php echo $hr_check; ?> Less crowding at reception and the billing counter</li>
					<li><?php echo $hr_check; ?> Patients who stay in touch and come back for follow-up</li>
					<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati for every patient</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- ============ RELATED ============ -->
<section aria-labelledby="related-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Keep reading</p>
			<h2 id="related-title">Explore the rest of the platform</h2>
		</div>
		<div class="grid-3 related-grid">
			<a class="card reveal" href="<?php echo esc_url(home_url('/patient-engagement-software/')); ?>">
				<h3>Patient engagement</h3>
				<p>The full portal and tools your team uses to stay in touch with patients between visits.</p>
				<span class="more">Explore patient engagement</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/telehealth-services/')); ?>">
				<h3>Telehealth services</h3>
				<p>Video, audio and chat consultations in the same record as an in-person visit.</p>
				<span class="more">Read about telehealth</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/ehr-software/')); ?>">
				<h3>EHR software</h3>
				<p>The one longitudinal patient record that the app and every department share.</p>
				<span class="more">Explore EHR</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals whose patients use the Healthray app"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_pa_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Patient mobile app FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_pa_faq as $hr_i => $hr_faq) : ?>
				<details name="patient-app-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
					<summary><?php echo esc_html($hr_faq['q']); ?></summary>
					<div class="answer"><?php echo esc_html($hr_faq['a']); ?></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ============ FINAL CTA with form ============ -->
<section aria-labelledby="cta-title" style="padding-top:0">
	<div class="wrap">
		<div class="cta">
			<div class="cta-grid">
				<div class="cta-copy">
					<h2 id="cta-title">Put your hospital in your patients' pocket</h2>
					<p>A 30-minute demo on your own workflow: the patient app, online booking, reminders, reports, ABHA linking, teleconsultation and digital payments.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Most hospitals start in 1 to 3 weeks</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See the patient app on your own workflow. Our team replies within one business day.</p>
					<?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]'); ?>
				</div>
			</div>
			<svg class="ecg-bg" viewBox="0 0 1200 70" preserveAspectRatio="none" aria-hidden="true">
				<path d="M0 35 H200 l15-18 20 36 15-30 12 12 H480 l14-22 18 40 14-26 10 8 H800 l15-18 20 36 15-30 12 12 H1200"/>
			</svg>
		</div>
	</div>
</section>

</main>
