<?php
/**
 * Template Name: Medical IoT Landing
 * Template Post Type: page
 *
 * Page:       /medical-iot-solution/ (page 30286) - replaces the Elementor layout.
 * Body class: landing-page medical-iot-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_medical_iot_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Medical IoT Solution" (Yoast; stored as "Medical loT" - typo to fix in admin).
 * Note:       Information/solution page - NO SoftwareApplication schema (it is a connected-care
 *             capability, not a rated product). Framed on HL7/FHIR interoperability, remote
 *             monitoring and analytics; specific device/RFID integrations left to confirm.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_iot_faq = function_exists('healthray_medical_iot_faq_items') ? healthray_medical_iot_faq_items() : array();
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
			<li><span aria-current="page">Medical IoT Solution</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Connected care · remote monitoring · analytics</p>
			<h1 id="hero-title">Medical IoT solution for connected,
				<span class="pulse-word">data-driven care
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Healthray's medical IoT (IoMT) approach brings data from your devices and systems into one patient record, supports remote monitoring and shows it all on one analytics dashboard, so your team can act on what is happening, not what was written down hours ago.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Data from many sources in one patient record</li>
				<li><?php echo $hr_check; ?> Remote monitoring through the patient app and teleconsultation</li>
				<li><?php echo $hr_check; ?> HL7/FHIR interoperability with your existing systems</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See connected, data-driven care on your own workflow. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS MEDICAL IOT ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is medical IoT (IoMT)?</h2>
			<p style="margin-top:14px">The Internet of Medical Things (IoMT) is the idea of connecting the devices and systems a hospital already uses so their data flows into one place, instead of sitting in separate machines and registers.</p>
			<p>Healthray's role is the software side of that: it holds the single patient record, exchanges data over open standards like HL7 and FHIR, supports remote monitoring through the patient app, and turns the data into charts and alerts on one dashboard.</p>
			<p>The payoff is quicker, better-informed decisions, less manual data entry and a clearer view of each patient and of the hospital as a whole.</p>
		</div>
		<img src="<?php echo esc_url($hr_media . '/2024/07/Overview-Of-Medical-IoT-Solution.webp'); ?>" alt="Medical IoT solution bringing device and system data into one patient record" width="556" height="498" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ CAPABILITIES ============ -->
<section aria-labelledby="caps-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What it brings together</p>
			<h2 id="caps-title">Connected, data-driven care on one platform</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M4 7h16M4 12h16M4 17h10"/><circle cx="19" cy="17" r="2"/>'); ?></span>
				<h3>One connected record</h3>
				<p>Data from different sources comes together in one patient record, so nothing is re-typed and nothing is out of date.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>'); ?></span>
				<h3>Remote monitoring</h3>
				<p>Patients share readings and consult from home through the app, so your team can keep an eye on them between visits. <a href="<?php echo esc_url(home_url('/telehealth-services/')); ?>">See telehealth services</a>.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 3v18h18M7 15l3-4 3 3 4-6"/>'); ?></span>
				<h3>Analytics dashboard</h3>
				<p>Live charts and reports on patients and on the hospital, so decisions are based on what is happening now.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M4 17v-5a8 8 0 0 1 16 0v5M2 17h20M8 21h8"/>'); ?></span>
				<h3>HL7 / FHIR interoperability</h3>
				<p>Open health-data standards let Healthray exchange information with the systems you already run, instead of locking your data in.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/>'); ?></span>
				<h3>Equipment &amp; inventory visibility</h3>
				<p>Track stock and asset records with maintenance and reorder alerts, so the right item is in the right place at the right time.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>'); ?></span>
				<h3>Secure by design</h3>
				<p>ISO 27001 certified, with a separate login for every user, so connected data stays private and traceable.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">From scattered data to joined-up care</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/>'); ?> For your hospital</h3>
				<ul>
					<li><?php echo $hr_check; ?> One view of each patient and of the whole hospital</li>
					<li><?php echo $hr_check; ?> Less manual data entry and fewer transcription errors</li>
					<li><?php echo $hr_check; ?> Better use of staff, stock and equipment</li>
					<li><?php echo $hr_check; ?> Decisions based on live data, not yesterday's registers</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>'); ?> For patients</h3>
				<ul>
					<li><?php echo $hr_check; ?> Monitoring and consultation without travelling in</li>
					<li><?php echo $hr_check; ?> Care that reaches remote and underserved areas</li>
					<li><?php echo $hr_check; ?> Earlier attention when a reading looks wrong</li>
					<li><?php echo $hr_check; ?> One clear record of their own health</li>
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
			<a class="card reveal" href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">
				<h3>Hospital management system</h3>
				<p>The full platform that runs OPD/IPD, EMR/EHR, pharmacy, lab, billing and analytics.</p>
				<span class="more">Explore the HMS</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/patient-engagement-software/')); ?>">
				<h3>Patient engagement</h3>
				<p>The portal and app patients use to share readings, consult and stay in touch.</p>
				<span class="more">See patient engagement</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/telehealth-services/')); ?>">
				<h3>Telehealth services</h3>
				<p>Remote consultations by video, audio or chat in the same patient record.</p>
				<span class="more">Read about telehealth</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals running on Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_iot_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Medical IoT FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_iot_faq as $hr_i => $hr_faq) : ?>
				<details name="medical-iot-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Join up your data, your devices and your decisions</h2>
					<p>A 30-minute demo on your own workflow: one connected record, remote monitoring, HL7/FHIR interoperability and the analytics dashboard.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See connected, data-driven care on your own workflow. Our team replies within one business day.</p>
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
