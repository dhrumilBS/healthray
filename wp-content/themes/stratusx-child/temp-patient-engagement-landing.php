<?php
/**
 * Template Name: Patient Engagement Landing
 * Template Post Type: page
 *
 * Page:       /patient-engagement-software/ (page 28423) - replaces the Elementor layout.
 * Body class: landing-page patient-engagement-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_patient_engagement_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Patient Engagement Software" (Yoast). Product page: SoftwareApplication schema in functions.php.
 * Note:       Rewritten from the old features/benefits copy, keeping the structure and
 *             only claims the site already makes.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_pe_faq = function_exists('healthray_patient_engagement_faq_items') ? healthray_patient_engagement_faq_items() : array();
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
			<li><span aria-current="page">Patient Engagement Software</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Patient portal · appointments · reminders · teleconsultation</p>
			<h1 id="hero-title">Patient engagement software that keeps patients
				<span class="pulse-word">connected
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Give patients one place to book appointments, get reminders, consult a doctor online, pay bills and read their own records and reports. Healthray's patient engagement software links every touchpoint to the same patient record your staff already use.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Online booking and reminders that cut no-shows</li>
				<li><?php echo $hr_check; ?> Prescriptions, lab reports and invoices on the patient's phone</li>
				<li><?php echo $hr_check; ?> Teleconsultation and secure messaging with the care team</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See the patient portal and app on your own workflow. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS PATIENT ENGAGEMENT SOFTWARE ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is patient engagement software?</h2>
			<p style="margin-top:14px">Patient engagement software gives patients a portal and mobile app to take part in their own care: book and reschedule appointments, get reminders, consult a doctor remotely, pay online and read their prescriptions, lab reports and bills.</p>
			<p>Because it runs on the same platform as your <a href="<?php echo esc_url(home_url('/ehr-software/')); ?>">electronic health records</a>, everything the patient sees is the same record your doctors and front desk use. Nothing is entered twice, and nothing is out of date.</p>
			<p>The result is fewer no-shows, less crowding at the counter and patients who stay in touch with your hospital between visits.</p>
		</div>
		<img src="<?php echo esc_url($hr_media . '/2024/04/Medical-Software-for-Clinics-Hospitals-and-Doctors.webp'); ?>" alt="Patient engagement software portal and app connected to the hospital record" width="643" height="536" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What patients can do</p>
			<h2 id="features-title">Explore the patient engagement features</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>'); ?></span>
				<h3>Online appointments</h3>
				<p>Patients book and reschedule from your website, Google or WhatsApp. If a doctor cancels, the next free slot is offered and the patient is notified at once.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/>'); ?></span>
				<h3>Reminders &amp; notifications</h3>
				<p>Automatic reminders for appointments and medicines, plus a notification whenever something changes, so patients turn up on time and stay on treatment.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="2" y="5" width="14" height="14" rx="2"/><path d="M16 10l6-3v10l-6-3z"/>'); ?></span>
				<h3>Teleconsultation &amp; messaging</h3>
				<p>Patients consult doctors by video, audio or chat and message the care team securely, all linked to the same record. <a href="<?php echo esc_url(home_url('/telehealth-services/')); ?>">See telehealth services</a>.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 7h18v12H3zM3 11h18M7 15h4"/>'); ?></span>
				<h3>Digital invoices &amp; payments</h3>
				<p>Bills are raised digitally and paid online in a click, so there is no cash handling and no queue at the billing counter.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>'); ?></span>
				<h3>Records &amp; reports</h3>
				<p>Prescriptions, lab reports and past visits sit in one place on the patient's phone, so they can track their own health and their medical spend.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/><path d="M8 10h.01M12 10h.01M16 10h.01"/>'); ?></span>
				<h3>Feedback surveys</h3>
				<p>After a visit, patients rate their experience, so you hear what went well and fix what did not before it costs you a patient.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">Better experience, better retention</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/>'); ?> For your hospital</h3>
				<ul>
					<li><?php echo $hr_check; ?> Fewer no-shows and idle slots from automatic reminders</li>
					<li><?php echo $hr_check; ?> Less crowding at reception and the billing counter</li>
					<li><?php echo $hr_check; ?> Patients who stay in touch and come back for follow-up</li>
					<li><?php echo $hr_check; ?> Honest feedback you can act on, visit by visit</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>'); ?> For patients</h3>
				<ul>
					<li><?php echo $hr_check; ?> Book, pay and consult without standing in a queue</li>
					<li><?php echo $hr_check; ?> Reminders before the visit and for medicines after it</li>
					<li><?php echo $hr_check; ?> Prescriptions, reports and bills on their own phone</li>
					<li><?php echo $hr_check; ?> A clear view of their health and their medical spend</li>
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
			<a class="card reveal" href="<?php echo esc_url(home_url('/ehr-software/')); ?>">
				<h3>EHR software</h3>
				<p>The one longitudinal patient record that the portal, app and every department share.</p>
				<span class="more">Explore EHR</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/telehealth-services/')); ?>">
				<h3>Telehealth services</h3>
				<p>Video, audio and chat consultations in the same record as an in-person visit.</p>
				<span class="more">Read about telehealth</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/patient-flow/')); ?>">
				<h3>Patient flow</h3>
				<p>Booking, queues, consultation, billing and follow-up managed on one system.</p>
				<span class="more">See patient flow</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals that engage their patients with Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_pe_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Patient engagement software FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_pe_faq as $hr_i => $hr_faq) : ?>
				<details name="patient-engagement-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Engage your patients from booking to follow-up</h2>
					<p>A 30-minute demo on your own workflow: the patient portal and app, online booking, reminders, teleconsultation, digital payments and records.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See the patient portal and app on your own workflow. Our team replies within one business day.</p>
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
