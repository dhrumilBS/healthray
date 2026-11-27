<?php
/**
 * Template Name: Billing Software Landing
 * Template Post Type: page
 *
 * Page:       /medical-billing-software/ (page 28345) - replaces the Elementor layout.
 * Body class: landing-page billing-software-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_billing_software_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Medical Billing Software" (Yoast). Product page: SoftwareApplication schema in functions.php.
 * Note:       Rewritten for Indian hospital and clinic billing (OPD, IPD, cashless TPA, package),
 *             keeping only claims the site already makes.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_bs_faq = function_exists('healthray_billing_software_faq_items') ? healthray_billing_software_faq_items() : array();
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
			<li><span aria-current="page">Medical Billing Software</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">OPD · IPD · cashless TPA · package · GST</p>
			<h1 id="hero-title">Medical billing software that stops
				<span class="pulse-word">revenue leakage
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Make every hospital and clinic bill - OPD, IPD, cashless TPA and package - in one software. Lab, pharmacy and room charges are added to the bill automatically, so a service is never given but left off the bill.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Every bill in one place: OPD, IPD, cashless TPA and package</li>
				<li><?php echo $hr_check; ?> Lab, pharmacy and room charges added to the bill automatically</li>
				<li><?php echo $hr_check; ?> A Revenue Leakage report that shows what was given but not billed</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See every bill - OPD, IPD, cashless TPA and package - built on your own rate lists. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS MEDICAL BILLING SOFTWARE ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is medical billing software?</h2>
		</div>
		<p>Medical billing software is the system a hospital or clinic uses to make its bills - the OPD bill, the IPD bill for an admitted patient, the cashless TPA bill and the Ayushman bill - with GST applied only where it is due.</p>
		<p>In India, "medical billing software" and <a href="<?php echo esc_url(home_url('/modules/hospital-billing-software/')); ?>">hospital billing software</a> mean the same thing. It is not US-style insurance-claim submission, and it is not only pharmacy or chemist billing - it is the software that prepares the hospital's own bills.</p>
		<p>Because it runs on the same platform as the rest of your <a href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">hospital management system</a>, lab, pharmacy and room charges flow onto the bill on their own, so the amount a patient pays matches the care they received.</p>
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What the billing software does</p>
			<h2 id="features-title">Explore the medical billing features</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 7h18v12H3zM3 11h18M7 15h4"/>'); ?></span>
				<h3>Every bill in one software</h3>
				<p>Raise OPD, IPD, cashless TPA and package bills in the same place, each on the right rate list, so your front desk and wards work from one screen.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<circle cx="11" cy="11" r="7"/><path d="m21 21-4.3-4.3M11 8v4M11 15h.01"/>'); ?></span>
				<h3>No missed charges</h3>
				<p>Lab, pharmacy and room charges are added to the bill automatically, and a Revenue Leakage report lists medicines and tests that were given but not billed.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M9 4v16"/>'); ?></span>
				<h3>Rate lists per payer &amp; ward</h3>
				<p>Keep separate rates for cash, each TPA and each company, and for General, Private and ICU wards. Import them from Excel instead of keying each one in.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<line x1="19" y1="5" x2="5" y2="19"/><circle cx="6.5" cy="6.5" r="2.5"/><circle cx="17.5" cy="17.5" r="2.5"/>'); ?></span>
				<h3>GST &amp; tax reports</h3>
				<p>GST is applied only on the services that need it, and the software keeps tax reports ready, so filing matches what you actually billed.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>'); ?></span>
				<h3>Doctor share, automatic</h3>
				<p>Each doctor's share is worked out automatically from the services billed, so there is no manual tally at the end of the month.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 3v18h18M7 16v-5M12 16V8M17 16v-8"/>'); ?></span>
				<h3>Collection &amp; discount reports</h3>
				<p>See collections by cash, UPI and card, and every discount with its reason and the staff member who gave it, so nothing moves without a record.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">Collect more, with less effort at the counter</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M3 3v18h18M7 16v-5M12 16V8M17 16v-8"/>'); ?> For your revenue</h3>
				<ul>
					<li><?php echo $hr_check; ?> Fewer charges given but not billed, caught by the Revenue Leakage report</li>
					<li><?php echo $hr_check; ?> The right rate for every payer and ward, applied automatically</li>
					<li><?php echo $hr_check; ?> Doctor shares worked out without a manual tally</li>
					<li><?php echo $hr_check; ?> Collection and discount reports that show where money moves</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 10h18M7 15h4"/>'); ?> For your billing counter</h3>
				<ul>
					<li><?php echo $hr_check; ?> One screen for OPD, IPD, cashless TPA and package bills</li>
					<li><?php echo $hr_check; ?> Lab, pharmacy and room charges added to the bill on their own</li>
					<li><?php echo $hr_check; ?> GST applied only where it is due, with tax reports ready</li>
					<li><?php echo $hr_check; ?> Rate lists imported from Excel, so there is less to key in</li>
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
			<a class="card reveal" href="<?php echo esc_url(home_url('/modules/hospital-billing-software/')); ?>">
				<h3>Hospital billing module</h3>
				<p>A closer look at how the billing module handles OPD, IPD, cashless TPA and package bills end to end.</p>
				<span class="more">See the billing module</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/pharmacy-management-system/')); ?>">
				<h3>Pharmacy software</h3>
				<p>Pharmacy stock and sales that push medicine charges straight onto the patient's bill.</p>
				<span class="more">Explore pharmacy software</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">
				<h3>Hospital management system</h3>
				<p>The full platform - OPD, IPD, lab, pharmacy and billing - running on one patient record.</p>
				<span class="more">See the full HMS</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals that bill on Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_bs_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Medical billing software FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_bs_faq as $hr_i => $hr_faq) : ?>
				<details name="billing-software-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">See every charge on the bill, in one software</h2>
					<p>A 30-minute demo on your own rate lists: OPD, IPD, cashless TPA and package bills, the Revenue Leakage report, GST and tax reports, doctor shares and collection reports.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See every bill built on your own rate lists. Our team replies within one business day.</p>
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
