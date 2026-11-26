<?php
/**
 * Template Name: Telehealth Landing
 * Template Post Type: page
 *
 * Page:       /telehealth-services/ (page 31051) - replaces the Elementor layout.
 * Body class: landing-page telehealth-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_telehealth_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Telehealth services" (Yoast). Feature page: no SoftwareApplication schema.
 * Note:       Old copy was an unrelated dev-agency template ("Panther SaaS"); rewritten as Healthray's
 *             teleconsultation feature, using only claims the site makes. Telemedicine Practice
 *             Guidelines (2020) referenced as India's legal framework for teleconsultation by RMPs.
 */
$hr_media     = 'https://healthray.com/wp-content/uploads';
$hr_tele_faq  = function_exists( 'healthray_telehealth_faq_items' ) ? healthray_telehealth_faq_items() : array();
$hr_check     = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon      = static function ( $paths ) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">Telehealth Services</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Video, audio &amp; chat consults · e-prescriptions · ABDM ready</p>
			<h1 id="hero-title">Telehealth services, built into
				<span class="pulse-word">every consult
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Healthray lets your doctors consult patients remotely by video, audio or chat, write e-prescriptions, and keep every teleconsultation in the same patient record as an in-person visit.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Remote consultations by video, audio or chat, from the patient portal</li>
				<li><?php echo $hr_check; ?> E-prescriptions that follow India's Telemedicine Practice Guidelines (2020)</li>
				<li><?php echo $hr_check; ?> The same record, billing and reports as your OPD and IPD visits</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See teleconsultation live on your own workflow. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS TELEHEALTH ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What are telehealth services?</h2>
			<p style="margin-top:14px">Telehealth, also called telemedicine, is the delivery of healthcare remotely using technology. A doctor consults a patient by video, audio or chat instead of in person, which saves travel and time, reaches patients in distant places, and keeps follow-up simple.</p>
			<p>In India, teleconsultation follows the Telemedicine Practice Guidelines approved in 2020, which let a registered medical practitioner consult patients remotely and issue an e-prescription within the conditions the guidelines set.</p>
			<p>On Healthray, telehealth is not a separate app. It runs on the same platform as your <a href="<?php echo esc_url( home_url( '/emr-software/' ) ); ?>">EMR</a>, billing and reports, so a remote consult is recorded exactly like a visit to the clinic.</p>
		</div>
		<img src="<?php echo esc_url( $hr_media . '/2024/03/Amplified-Patient-Engagement.webp' ); ?>" alt="Doctor consulting a patient over a video teleconsultation" width="500" height="406" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What you get</p>
			<h2 id="features-title">Everything a teleconsultation needs</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="2" y="5" width="14" height="14" rx="2"/><path d="M16 10l6-3v10l-6-3z"/>' ); ?></span>
				<h3>Video, audio &amp; chat consults</h3>
				<p>Doctors and patients connect by video, audio or text, whichever suits the patient's connection and need.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>' ); ?></span>
				<h3>E-prescriptions</h3>
				<p>Write a digital prescription during the consult, following the Telemedicine Practice Guidelines, saved to the patient's record.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>' ); ?></span>
				<h3>Appointments &amp; payments</h3>
				<p>Patients book a slot, pay online and get reminders, so the schedule stays full and no-shows drop.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>' ); ?></span>
				<h3>Patient portal</h3>
				<p>Patients join the consult, see prescriptions and reports and pay, from a phone or computer anywhere.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>' ); ?></span>
				<h3>Records &amp; history</h3>
				<p>Every teleconsultation joins the patient's longitudinal record alongside in-person visits, labs and pharmacy.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 11l-3 3-2-2"/>' ); ?></span>
				<h3>Referrals &amp; follow-up</h3>
				<p>Refer to a specialist, book a follow-up and send reminders, all from the same consultation screen.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section class="define" aria-labelledby="steps-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Step by step</p>
			<h2 id="steps-title">How a teleconsultation works on Healthray</h2>
			<p>Four steps, the same for the front desk whether the visit is in person or remote.</p>
		</div>
		<ol class="num-steps">
			<li class="reveal">
				<h3>Patient books a slot</h3>
				<p>The patient books online or the front desk schedules the teleconsultation, and the patient gets a reminder.</p>
			</li>
			<li class="reveal">
				<h3>Join the consultation</h3>
				<p>At the appointment time the patient joins from the portal and connects with the doctor by video, audio or chat.</p>
			</li>
			<li class="reveal">
				<h3>Consult and e-prescribe</h3>
				<p>The doctor consults, records notes and writes an e-prescription, following the Telemedicine Practice Guidelines.</p>
			</li>
			<li class="reveal">
				<h3>Follow up and save</h3>
				<p>Payment, prescription and notes are saved to the patient's record, and any follow-up or referral is booked.</p>
			</li>
		</ol>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it helps</p>
			<h2 id="benefits-title">Benefits of telehealth</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/>' ); ?> For your hospital or clinic</h3>
				<ul>
					<li><?php echo $hr_check; ?> See more patients without adding consulting rooms</li>
					<li><?php echo $hr_check; ?> Fill cancelled slots and reduce no-shows with online booking and reminders</li>
					<li><?php echo $hr_check; ?> Keep follow-ups in-house instead of losing them</li>
					<li><?php echo $hr_check; ?> One record and one bill for remote and in-person care</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>' ); ?> For patients</h3>
				<ul>
					<li><?php echo $hr_check; ?> Consult from home, with no travel or waiting room</li>
					<li><?php echo $hr_check; ?> Reach specialists who are far away</li>
					<li><?php echo $hr_check; ?> Get the prescription and reports on their phone</li>
					<li><?php echo $hr_check; ?> Easier follow-up for ongoing and chronic conditions</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- ============ RELATED ============ -->
<section class="define" aria-labelledby="related-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Keep reading</p>
			<h2 id="related-title">Explore the rest of the platform</h2>
		</div>
		<div class="grid-3 related-grid">
			<a class="card reveal" href="<?php echo esc_url( home_url( '/emr-software/' ) ); ?>">
				<h3>EMR software</h3>
				<p>The patient record, speciality templates and e-prescriptions that teleconsultation writes to.</p>
				<span class="more">Explore EMR</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/clinic-management-software/' ) ); ?>">
				<h3>Clinic software</h3>
				<p>Appointments, records, billing and pharmacy for clinics, with a mobile app for doctors.</p>
				<span class="more">Explore clinic software</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/patient-flow/' ) ); ?>">
				<h3>Patient flow</h3>
				<p>How Healthray moves patients from booking to follow-up with less waiting.</p>
				<span class="more">Read about patient flow</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode( '[hr_stats]' ); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode( '[hr_clients eyebrow="Trusted across India" title="Hospitals and clinics running on Healthray"]' ); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode( '[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_tele_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Telehealth FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_tele_faq as $hr_i => $hr_faq ) : ?>
				<details name="telehealth-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Add telehealth without adding another system</h2>
					<p>A 30-minute demo on your own workflow: booking, the video consult, e-prescription and follow-up, all in the record your team already uses.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See teleconsultation live on your own workflow. Our team replies within one business day.</p>
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
