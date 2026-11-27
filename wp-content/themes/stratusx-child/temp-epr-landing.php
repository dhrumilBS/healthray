<?php
/**
 * Template Name: EPR Landing
 * Template Post Type: page
 *
 * Page:       /electronic-patient-records/ (page 57873) - replaces the Elementor layout.
 * Body class: landing-page epr-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_epr_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Electronic Patient Records Software" (Yoast). Product page: SoftwareApplication schema in functions.php.
 * Note:       Rewritten from the old components/benefits copy, keeping the structure and
 *             only claims the site already makes. Dropped the [elementor-template] blocks.
 *             EPR sits alongside the EHR (79754) and EMR (79713) pages - cross-linked, not duplicated.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_epr_faq = function_exists('healthray_epr_faq_items') ? healthray_epr_faq_items() : array();
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
			<li><span aria-current="page">Electronic Patient Records</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Digital charts · interoperable · secure</p>
			<h1 id="hero-title">Electronic patient records software for
				<span class="pulse-word">safer care
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Keep one complete digital record for every patient: history, prescriptions, lab and radiology results, bills and discharge papers. Healthray's electronic patient records software is cloud-ready, exchanges data across departments and keeps it secure.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> One digital chart per patient, from first visit onward</li>
				<li><?php echo $hr_check; ?> Records that move between departments and hospitals</li>
				<li><?php echo $hr_check; ?> ISO 27001 secure, ABDM compliant, with a full audit trail</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See the patient record on your own workflow. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS EPR ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is electronic patient records software?</h2>
			<p style="margin-top:14px">An electronic patient record (EPR) is the digital version of a patient's chart: their details, visit history, diagnoses, prescriptions, lab and radiology results, bills and discharge papers, held in one place instead of on paper in a file.</p>
			<p>Electronic patient records software creates and manages that record, controls who can see it and lets it move safely between the people treating the patient.</p>
			<p>On Healthray the EPR is the same record used by the <a href="<?php echo esc_url(home_url('/emr-software/')); ?>">EMR</a> within your hospital and shared across your network by the <a href="<?php echo esc_url(home_url('/ehr-software/')); ?>">EHR</a>, so the patient's history is never split or re-typed.</p>
		</div>
		<img src="<?php echo esc_url($hr_media . '/2025/03/Modern-Electronic-Patient-Records-System-For-Advanced-Security-Healthray.webp'); ?>" alt="Modern electronic patient records system with advanced security" width="448" height="381" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ COMPONENTS / FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What is inside</p>
			<h2 id="features-title">Vital parts of the electronic patient record</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>'); ?></span>
				<h3>Record administration</h3>
				<p>Demographics, visit history, diagnoses, prescriptions, discharge forms and bills, all filed under one patient.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 12h4l2 6 4-12 2 6h6"/>'); ?></span>
				<h3>Disease tracking</h3>
				<p>Follow a condition over time across visits, so trends and changes are clear rather than buried in separate notes.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M8 2v4M16 2v4"/>'); ?></span>
				<h3>Appointment management</h3>
				<p>Patients book, reschedule and cancel without queueing, and the visit attaches straight to their record.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 7h18v12H3zM3 11h18M7 15h4"/>'); ?></span>
				<h3>E-medical billing</h3>
				<p>Raise bills against the record, including TPA and insurance, with no separate re-entry of the patient's details.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M4 17v-5a8 8 0 0 1 16 0v5M2 17h20M8 21h8"/>'); ?></span>
				<h3>Interoperability</h3>
				<p>Exchange records across departments and hospitals over HL7/FHIR and ABDM, so care is not held up by missing history.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 3v18h18M7 15l3-4 3 3 4-6"/>'); ?></span>
				<h3>Statistical analysis</h3>
				<p>Turn the records into reports and dashboards, so you can see patterns across patients and act on them.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What you gain</p>
			<h2 id="benefits-title">What electronic patient records change</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<h3>Better patient outcomes</h3>
				<p>Doctors see the full history at once, so decisions are faster and based on everything, not part of the picture.</p>
			</div>
			<div class="card reveal">
				<h3>Stronger data safety</h3>
				<p>ISO 27001 security, a login per user and a full audit trail replace paper files that can be lost or read by anyone.</p>
			</div>
			<div class="card reveal">
				<h3>Higher revenue</h3>
				<p>Nothing billable is missed when orders, prescriptions and procedures all sit against the record.</p>
			</div>
			<div class="card reveal">
				<h3>Lower cost</h3>
				<p>Less printing, storage and retrieval, and far less time spent hunting for an old file.</p>
			</div>
			<div class="card reveal">
				<h3>More engaged patients</h3>
				<p>Patients read their own records, prescriptions and reports, so they take a bigger part in their care.</p>
			</div>
			<div class="card reveal">
				<h3>Coordinated care</h3>
				<p>Every department works from the same record, so referrals and handovers do not lose the patient's history.</p>
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
				<p>One longitudinal record shared across departments, branches and your whole network.</p>
				<span class="more">Explore EHR</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/emr-software/')); ?>">
				<h3>EMR software</h3>
				<p>Digital records, speciality templates and e-prescriptions within your hospital or clinic.</p>
				<span class="more">Explore EMR</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/abdm/')); ?>">
				<h3>ABDM &amp; ABHA</h3>
				<p>Link records to a patient's ABHA number under the Ayushman Bharat Digital Mission.</p>
				<span class="more">Read about ABDM</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals that went paperless with Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_epr_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Electronic patient records FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_epr_faq as $hr_i => $hr_faq) : ?>
				<details name="epr-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Put every patient's full history in one record</h2>
					<p>A 30-minute demo on your own workflow: the digital chart, interoperability, e-billing, security and the reports you can build on top.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See the patient record on your own workflow. Our team replies within one business day.</p>
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
