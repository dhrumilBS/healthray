<?php
/**
 * Template Name: Patient Flow Landing
 * Template Post Type: page
 *
 * Page:       /patient-flow/ (page 31079) - replaces the Elementor layout.
 * Body class: landing-page patient-flow-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_patient_flow_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Patient Flow Management" (Yoast). Feature page: no SoftwareApplication schema.
 * Note:       Rewritten from the old pre-visit/visit/post-visit copy, keeping that structure and
 *             only claims the site already makes.
 */
$hr_media    = 'https://healthray.com/wp-content/uploads';
$hr_pf_faq   = function_exists( 'healthray_patient_flow_faq_items' ) ? healthray_patient_flow_faq_items() : array();
$hr_check    = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon     = static function ( $paths ) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">Patient Flow</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Pre-visit · visit · post-visit, on one system</p>
			<h1 id="hero-title">Patient flow management that
				<span class="pulse-word">cuts the wait
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Healthray manages the whole patient journey - booking, registration, queues, consultation, billing and follow-up - on one system, so patients wait less and your hospital treats more.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Online booking and reminders that cut no-shows</li>
				<li><?php echo $hr_check; ?> Token and queue management with a waiting-area display</li>
				<li><?php echo $hr_check; ?> One record from check-in to follow-up, with waiting-time analytics</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See the patient journey on your own workflow. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS PATIENT FLOW ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is patient flow management?</h2>
			<p style="margin-top:14px">Patient flow is how a patient moves through your hospital, from booking an appointment to being seen, treated, billed and followed up. When any stage stalls, queues build, patients wait and rooms and beds sit idle.</p>
			<p>Patient flow management software coordinates every stage on one system. Booking, registration, the OPD queue, consultation, beds, billing and follow-up share one patient record, so staff always know where each patient is and what happens next.</p>
			<p>Healthray does this across the whole journey, and because it is the same platform that runs your <a href="<?php echo esc_url( home_url( '/modules/opd-management/' ) ); ?>">OPD</a> and <a href="<?php echo esc_url( home_url( '/modules/ipd-management/' ) ); ?>">IPD</a>, nothing is entered twice.</p>
		</div>
		<img src="<?php echo esc_url( $hr_media . '/2024/04/Healthray-Patient-Flow.webp' ); ?>" alt="Patient flow across booking, visit and follow-up in Healthray" width="1024" height="894" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ THE JOURNEY ============ -->
<section aria-labelledby="journey-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Step by step</p>
			<h2 id="journey-title">The patient journey on Healthray</h2>
			<p>Four stages, each handing the next everything it needs.</p>
		</div>
		<ol class="num-steps">
			<li class="reveal">
				<h3>Book &amp; remind</h3>
				<p>The patient books online, confirms insurance and gets reminders. If a doctor is unavailable, a new slot is offered automatically.</p>
			</li>
			<li class="reveal">
				<h3>Register &amp; queue</h3>
				<p>Front-desk registration, a token and the OPD queue with a waiting-area display, so patients are seen in order with less crowding.</p>
			</li>
			<li class="reveal">
				<h3>Consult &amp; treat</h3>
				<p>The doctor sees the full record, writes notes and e-prescriptions, orders tests, and admits to a bed when needed.</p>
			</li>
			<li class="reveal">
				<h3>Bill, follow up &amp; recall</h3>
				<p>Billing and insurance claims, then follow-up reminders, feedback and monitoring keep the patient engaged after the visit.</p>
			</li>
		</ol>
	</div>
</section>

<!-- ============ CAPABILITIES ============ -->
<section class="define" aria-labelledby="caps-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What moves the queue</p>
			<h2 id="caps-title">What runs the flow at each stage</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>' ); ?></span>
				<h3>Online appointments</h3>
				<p>Patients book and reschedule online, with reminders that cut no-shows and fill cancelled slots.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="4" width="18" height="14" rx="2"/><path d="M7 20h10M12 18v2M7 9h4M7 13h7"/>' ); ?></span>
				<h3>Queue &amp; waiting-area display</h3>
				<p>Token numbers and a live display keep the OPD orderly and tell patients when it is their turn.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M2 20V9a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v11M2 14h18M22 20V12M6 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/>' ); ?></span>
				<h3>Bed &amp; ward management</h3>
				<p>See bed availability and assignments in real time, so admissions and transfers do not stall.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="2" y="5" width="14" height="14" rx="2"/><path d="M16 10l6-3v10l-6-3z"/>' ); ?></span>
				<h3>Teleconsultation</h3>
				<p>Offer remote consults in the same flow, linked to the same record. <a href="<?php echo esc_url( home_url( '/telehealth-services/' ) ); ?>">See telehealth services</a>.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 7h18v12H3zM3 11h18M7 15h4"/>' ); ?></span>
				<h3>Payments &amp; insurance</h3>
				<p>Digital payments and TPA, insurance and PM-JAY claims, so billing and reimbursement do not hold up discharge.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 3v18h18M7 15l3-4 3 3 4-6"/>' ); ?></span>
				<h3>Analytics dashboard</h3>
				<p>Waiting times, throughput and bottlenecks on one dashboard, so you can see where the flow slows and fix it.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">Less waiting, more patients seen</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/>' ); ?> For your hospital</h3>
				<ul>
					<li><?php echo $hr_check; ?> Shorter queues and higher throughput from the same rooms and staff</li>
					<li><?php echo $hr_check; ?> Fewer no-shows and idle slots, so revenue is not lost</li>
					<li><?php echo $hr_check; ?> Faster bed turnaround and smoother admissions</li>
					<li><?php echo $hr_check; ?> Data on where patients wait, so you can fix the real bottleneck</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>' ); ?> For patients</h3>
				<ul>
					<li><?php echo $hr_check; ?> Less time in the waiting room</li>
					<li><?php echo $hr_check; ?> Reminders before the visit and for medicines after it</li>
					<li><?php echo $hr_check; ?> Digital payments, prescriptions and reports</li>
					<li><?php echo $hr_check; ?> A smoother experience from booking to follow-up</li>
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
			<a class="card reveal" href="<?php echo esc_url( home_url( '/modules/opd-management/' ) ); ?>">
				<h3>OPD software</h3>
				<p>Registration and tokens, the waiting-area display, the doctor's screen, OPD billing and day-end reports.</p>
				<span class="more">Explore OPD</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/modules/ipd-management/' ) ); ?>">
				<h3>IPD software</h3>
				<p>Admissions, ward and bed management, daily notes, charges and discharge for admitted patients.</p>
				<span class="more">Explore IPD</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/telehealth-services/' ) ); ?>">
				<h3>Telehealth services</h3>
				<p>Remote consultations by video, audio or chat, in the same patient record as an in-person visit.</p>
				<span class="more">Read about telehealth</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode( '[hr_stats]' ); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode( '[hr_clients eyebrow="Trusted across India" title="Hospitals that moved their patient flow to Healthray"]' ); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode( '[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_pf_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Patient flow FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_pf_faq as $hr_i => $hr_faq ) : ?>
				<details name="patient-flow-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Move patients through faster, start to finish</h2>
					<p>A 30-minute demo on your own workflow: booking and reminders, the OPD queue, beds, billing and follow-up, all on one record.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See the patient journey on your own workflow. Our team replies within one business day.</p>
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
