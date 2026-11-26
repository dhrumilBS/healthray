<?php
/**
 * Template Name: Why Healthray Landing
 * Template Post Type: page
 *
 * Page:       /why-healthray/ (page 30243) - replaces the Elementor layout.
 * Body class: landing-page why-healthray-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_why_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Healthray" (Yoast). Information page: no SoftwareApplication schema.
 * Note:       Old copy was low-quality machine text; rewritten for people and the keyword,
 *             keeping only claims the site already makes. Real FAQ questions retained, answers rewritten.
 */
$hr_media   = 'https://healthray.com/wp-content/uploads';
$hr_why_faq = function_exists( 'healthray_why_faq_items' ) ? healthray_why_faq_items() : array();
$hr_check   = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon    = static function ( $paths ) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">Why Healthray</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">One platform · NHA approved · 2,500+ hospitals</p>
			<h1 id="hero-title">Why hospitals choose
				<span class="pulse-word">Healthray
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Healthray runs your whole hospital on one platform - OPD, IPD, records, laboratory, pharmacy, billing and HR - so every department works from the same real-time patient data instead of separate tools that do not talk to each other.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> One patient record across OPD, IPD, lab, pharmacy and billing</li>
				<li><?php echo $hr_check; ?> NHA approved, ABDM compliant and NABH certified</li>
				<li><?php echo $hr_check; ?> Used by 2,500+ hospitals, clinics and labs across India</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See how Healthray runs a hospital, from registration to billing. Our team replies within one business day.</p>
			<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
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
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abdm-healthray-150x150.webp' ); ?>" alt="ABDM compliant hospital software - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abha-healthray-150x150.webp' ); ?>" alt="ABHA integrated health records" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/NHA.webp' ); ?>" alt="NHA approved software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
			<a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"><img src="<?php echo esc_url( $hr_media . '/2026/07/NABH-ehr-certified-150x150.webp' ); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span></a>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/Hipaa-healthray-150x150.webp' ); ?>" alt="HIPAA compliant hospital software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/iso-27001-healthray-150x150.webp' ); ?>" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/hl7-healthray-150x150.webp' ); ?>" alt="HL7 interoperable hospital software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/FHIR.webp' ); ?>" alt="FHIR compliant health record exchange" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
		</div>
	</div>
</section>

<!-- ============ WHAT IS HEALTHRAY ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The short version</p>
			<h2 id="define-title">Healthray is one connected hospital management system</h2>
			<p style="margin-top:14px">Healthray replaces registers, spreadsheets and disconnected software with a single platform for the whole hospital. Registration, OPD and IPD, electronic records, laboratory, pharmacy, billing, insurance and HR all work from the same real-time patient data.</p>
			<p>Because every department shares one record, information is entered once and flows everywhere it is needed. Doctors across specialities - from primary care to cardiology, dermatology and physiotherapy - see the same complete history, and the platform is built on ABDM, HL7 and FHIR standards so records stay portable.</p>
			<p>It is NHA approved, ABDM compliant and NABH certified, and it is used by 2,500+ hospitals, clinics and laboratories across India and by providers in the USA.</p>
		</div>
		<img src="<?php echo esc_url( $hr_media . '/2024/03/Healthray-is-the-Next-Frontier-In-Healthcare.webp' ); ?>" alt="Healthray hospital management software on desktop and mobile" width="573" height="574" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ WHY CHOOSE ============ -->
<section aria-labelledby="reasons-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why choose Healthray</p>
			<h2 id="reasons-title">Six reasons hospitals move to Healthray</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 17l6-6 4 4 8-8M14 7h7v7"/>' ); ?></span>
				<h3>Scales with you</h3>
				<p>Start with appointments and EMR in one clinic, then switch on pharmacy, laboratory, IPD, billing and more branches on the same platform as you grow.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>' ); ?></span>
				<h3>Speciality-ready templates</h3>
				<p>Documentation flows for 28+ specialities instead of one generic form, so clinical notes fit how each department actually works.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM19 8v6M22 11h-6"/>' ); ?></span>
				<h3>Built-in HR and payroll</h3>
				<p>Staff rosters, attendance and payroll are part of the platform, so HR runs on the same system as clinical and billing work.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 12a9 9 0 1 0 9-9M3 4v5h5"/><path d="M12 7v5l3 2"/>' ); ?></span>
				<h3>Fewer no-shows, better retention</h3>
				<p>Appointment reminders, a patient portal and follow-up tools reduce missed visits and keep patients coming back.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>' ); ?></span>
				<h3>Lower running cost</h3>
				<p>Automating registration, billing, claims and reconciliation cuts manual work and the revenue lost to errors and uncharged services.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M9 4v16"/>' ); ?></span>
				<h3>One connected record</h3>
				<p>EMR and EHR, orders, results, prescriptions and billing live in one longitudinal record, linked to the patient's ABHA.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ SOLUTIONS ============ -->
<section class="define" aria-labelledby="solutions-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">One platform, every module</p>
			<h2 id="solutions-title">Explore what runs on Healthray</h2>
			<p>Switch modules on as you need them - they all share the same patient record.</p>
		</div>
		<div class="grid-3 related-grid">
			<a class="card reveal" href="<?php echo esc_url( home_url( '/hospital-information-management-system/' ) ); ?>">
				<h3>HIMS software</h3>
				<p>Run the whole hospital: registration with ABHA, OPD and IPD, OT, billing, TPA, stores, HR and MIS.</p>
				<span class="more">Explore HIMS</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/emr-software/' ) ); ?>">
				<h3>EMR software</h3>
				<p>Digital patient charts, speciality templates and e-prescriptions on a single consultation screen.</p>
				<span class="more">Explore EMR</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/ehr-software/' ) ); ?>">
				<h3>EHR software</h3>
				<p>One longitudinal record shared across departments and branches, linked to the patient's ABHA.</p>
				<span class="more">Explore EHR</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/clinic-management-software/' ) ); ?>">
				<h3>Clinic software</h3>
				<p>Appointments, records, billing and pharmacy for clinics and polyclinics, with a mobile app for doctors.</p>
				<span class="more">Explore clinic software</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/laboratory-information-management-system/' ) ); ?>">
				<h3>LIMS software</h3>
				<p>Sample tracking, analyzer interfacing, QC and reporting for clinical and diagnostic laboratories.</p>
				<span class="more">Explore LIMS</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/pharmacy-management-system/' ) ); ?>">
				<h3>Pharmacy software</h3>
				<p>GST billing, batch and expiry tracking, purchases and regulatory registers for retail and hospital pharmacies.</p>
				<span class="more">Explore pharmacy software</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode( '[hr_stats]' ); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode( '[hr_clients eyebrow="Trusted across India" title="Hospitals, clinics and labs that run on Healthray"]' ); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode( '[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_why_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Why Healthray FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_why_faq as $hr_i => $hr_faq ) : ?>
				<details name="why-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
					<summary><?php echo esc_html( $hr_faq['q'] ); ?></summary>
					<div class="answer"><?php echo esc_html( $hr_faq['a'] ); ?></div>
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
					<h2 id="cta-title">See why hospitals switch to Healthray</h2>
					<p>A 30-minute demo on your own workflow, from registration and OPD to IPD, lab, pharmacy and billing - so you see exactly what changes for your team.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>Tell us about your hospital. Our team replies within one business day.</p>
					<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
				</div>
			</div>
			<svg class="ecg-bg" viewBox="0 0 1200 70" preserveAspectRatio="none" aria-hidden="true">
				<path d="M0 35 H200 l15-18 20 36 15-30 12 12 H480 l14-22 18 40 14-26 10 8 H800 l15-18 20 36 15-30 12 12 H1200"/>
			</svg>
		</div>
	</div>
</section>

</main>
