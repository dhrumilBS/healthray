<?php
/**
 * Template Name: Billing Services Landing
 * Template Post Type: page
 *
 * Page:       /medical-billing-services/ (page 28530) - replaces the Elementor layout.
 * Body class: landing-page billing-services-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_billing_services_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Medical Billing Services" (Yoast). INFORMATION page - no SoftwareApplication schema (handled in functions.php).
 * Note:       Revenue-cycle/outcomes angle. Billing stays your own team's work inside Healthray's
 *             software (not outsourced); the product page is /medical-billing-software/ and the
 *             module is /modules/hospital-billing-software/. Only claims the site already makes.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_bsv_faq = function_exists('healthray_billing_services_faq_items') ? healthray_billing_services_faq_items() : array();
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
			<li><span aria-current="page">Medical Billing Services</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Claims · collections · GST · revenue reports</p>
			<h1 id="hero-title">Medical billing services, from first charge to final
				<span class="pulse-word">collection
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Run your whole billing and revenue cycle inside Healthray. Your team raises OPD and IPD bills, files cashless TPA and Ayushman claims, applies rate lists and GST, and tracks every rupee collected, all on the same record your clinical and front-desk staff already use.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Raise OPD, IPD and cashless TPA bills from the patient record</li>
				<li><?php echo $hr_check; ?> File insurance and Ayushman claims and track each one to payment</li>
				<li><?php echo $hr_check; ?> Collection, discount and revenue-leakage reports for your finance team</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See billing, claims and collection reports on your own workflow. Our team replies within one business day.</p>
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

<!-- ============ WHAT BILLING SERVICES MEANS ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What it means</p>
			<h2 id="define-title">What medical billing services means with Healthray</h2>
		</div>
		<p>Medical billing services often means handing your billing to an outside agency. With Healthray it means the opposite: your own billing team does the work inside one software platform, keeping full control and a clear view of every bill, claim and payment.</p>
		<p>The billing engine is our <a href="<?php echo esc_url(home_url('/medical-billing-software/')); ?>">medical billing software</a>, available on its own or as the <a href="<?php echo esc_url(home_url('/modules/hospital-billing-software/')); ?>">hospital billing module</a> inside the wider platform. Charges flow in from OPD, IPD, pharmacy and lab as care happens, so bills are built from the real record instead of being keyed in twice.</p>
		<p>Because billing sits on the same record as your clinical and front-desk work, your team applies rate lists and GST, files cashless TPA and Ayushman claims, and tracks collections, discounts and revenue leakage in real time. Nothing is entered twice, and nothing is left out.</p>
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What your team does</p>
			<h2 id="features-title">Everything the revenue cycle needs, in one place</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="m9 15 2 2 4-4"/>'); ?></span>
				<h3>Electronic claim processing</h3>
				<p>File cashless TPA, insurance and Ayushman Bharat claims straight from the patient's bill. Your team tracks each claim from submission to payment, so nothing is left unpaid.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 3v18h18"/><path d="m7 14 4-4 3 3 5-6"/>'); ?></span>
				<h3>Revenue tracking &amp; reports</h3>
				<p>Collection, discount and revenue-leakage reports show what was billed, what was collected and what slipped through, so your finance team sees the full picture in real time.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M13 2 4 14h7l-1 8 9-12h-7z"/>'); ?></span>
				<h3>Billing automation</h3>
				<p>Charges from OPD, IPD, pharmacy and lab are added to the bill as care happens, using your own rate lists, so the total is ready without re-keying anything.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>'); ?></span>
				<h3>Compliance &amp; GST</h3>
				<p>Bills follow the required format with GST applied correctly, so your billing stays audit-ready and in line with the rules at every counter.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<rect x="6" y="2" width="12" height="20" rx="2"/><path d="M11 18h2"/>'); ?></span>
				<h3>Remote access</h3>
				<p>View collections, pending claims and daily billing from any device, so owners and managers can keep an eye on revenue without standing at the counter.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 12a9 9 0 1 0 3-6.7L3 8"/><path d="M3 3v5h5"/>'); ?></span>
				<h3>Denial, discount &amp; refund control</h3>
				<p>Work denied claims, approve discounts and manage refunds in one place, so money is not lost to missed follow-ups or unchecked write-offs.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">Stronger collections, tighter control</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M12 1v22"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/>'); ?> For collections</h3>
				<ul>
					<li><?php echo $hr_check; ?> Bills built from the real record, so fewer charges are missed</li>
					<li><?php echo $hr_check; ?> Cashless TPA and Ayushman claims tracked all the way to payment</li>
					<li><?php echo $hr_check; ?> A revenue-leakage report that flags money which slipped through</li>
					<li><?php echo $hr_check; ?> Online payment, so there is less cash handling and shorter queues</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="m9 12 2 2 4-4"/>'); ?> For compliance &amp; control</h3>
				<ul>
					<li><?php echo $hr_check; ?> GST applied and invoices kept in the required format</li>
					<li><?php echo $hr_check; ?> Discounts, refunds and write-offs approved in one place</li>
					<li><?php echo $hr_check; ?> Collection and discount reports for every counter and day</li>
					<li><?php echo $hr_check; ?> ISO 27001 and HIPAA security on your financial data</li>
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
			<a class="card reveal" href="<?php echo esc_url(home_url('/medical-billing-software/')); ?>">
				<h3>Medical billing software</h3>
				<p>The billing engine on its own: charges, claims, payments and reports for your OPD and IPD.</p>
				<span class="more">Explore billing software</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/modules/hospital-billing-software/')); ?>">
				<h3>Hospital billing module</h3>
				<p>The same billing built into the wider hospital platform, sharing one record with every department.</p>
				<span class="more">See the billing module</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">
				<h3>Hospital management system</h3>
				<p>Billing, pharmacy, lab, OPD and IPD managed together on one connected system.</p>
				<span class="more">Explore HIMS</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals that run billing on Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_bsv_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Medical billing services FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_bsv_faq as $hr_i => $hr_faq) : ?>
				<details name="billing-services-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Run your billing and revenue cycle on Healthray</h2>
					<p>A 30-minute demo on your own workflow: billing, cashless TPA and Ayushman claims, GST, collection reports and the revenue-leakage report.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See billing, claims and collection reports on your own workflow. Our team replies within one business day.</p>
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
