<?php
/**
 * Template Name: Inventory Landing
 * Template Post Type: page
 *
 * Page:       /inventory-management-software/ (page 57646) - replaces the Elementor layout.
 * Body class: landing-page inventory-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_inventory_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Inventory Management Software" (Yoast). Product page: SoftwareApplication schema in functions.php.
 * Note:       Rewritten from the old features/benefits copy, keeping the structure and
 *             only claims the site already makes. Dropped the [elementor-template] blocks.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_inv_faq = function_exists('healthray_inventory_faq_items') ? healthray_inventory_faq_items() : array();
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
			<li><span aria-current="page">Inventory Management Software</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Stock tracking · forecasting · alerts · GST billing</p>
			<h1 id="hero-title">Hospital inventory management software that
				<span class="pulse-word">cuts wastage
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Track every medicine, consumable and asset across your pharmacy and stores in real time. Healthray forecasts demand, warns you before stock runs out or expires, and keeps your GST billing and purchase records in the same place.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Live stock across pharmacy, wards and central stores</li>
				<li><?php echo $hr_check; ?> Low-stock and expiry alerts, with demand forecasting</li>
				<li><?php echo $hr_check; ?> GST billing, purchase and order management in one place</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See stock control on your own item list. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS INVENTORY MANAGEMENT SOFTWARE ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is hospital inventory management software?</h2>
			<p style="margin-top:14px">Hospital inventory management software keeps track of everything you buy and use: medicines, consumables and equipment, across the pharmacy, the wards and the central store. It records what came in, what went out and what is left, all in one system.</p>
			<p>Healthray adds the parts that save money: it predicts how much you will need from past usage, warns you before an item runs out or expires, and files the purchase and GST billing in the same place.</p>
			<p>Because it shares the platform that runs your <a href="<?php echo esc_url(home_url('/pharmacy-management-system/')); ?>">pharmacy</a> and the rest of the <a href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">hospital system</a>, a medicine dispensed to a patient updates stock at once.</p>
		</div>
		<img src="<?php echo esc_url($hr_media . '/2025/03/AI-based-Inventory-Management-Software-To-Reduce-Wastage-Healthray.webp'); ?>" alt="AI-based hospital inventory management software that reduces wastage" width="560" height="421" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What it does</p>
			<h2 id="features-title">Explore the inventory features</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 9l9-6 9 6v11a1 1 0 0 1-1 1h-4v-7H8v7H4a1 1 0 0 1-1-1z"/>'); ?></span>
				<h3>Stock tracking</h3>
				<p>See the exact quantity of every item and where it is, across the pharmacy, wards and store, with no duplicate or stale entries.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 3v18h18M7 14l3-3 3 2 5-6"/>'); ?></span>
				<h3>Demand forecasting</h3>
				<p>Healthray predicts how much of each item you will need from past usage, so you order the right quantity, not too much or too little.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M18 8a6 6 0 0 0-12 0c0 7-3 9-3 9h18s-3-2-3-9M13.7 21a2 2 0 0 1-3.4 0"/>'); ?></span>
				<h3>Low-stock &amp; expiry alerts</h3>
				<p>Automatic warnings before an item runs out or reaches its expiry date, so you reorder in time and throw away less.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 7h18v12H3zM3 11h18M7 15h4"/>'); ?></span>
				<h3>GST billing &amp; invoicing</h3>
				<p>Raise GST-ready invoices and purchase records, add a digital signature and export to PDF, all from the same screen.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<circle cx="9" cy="21" r="1"/><circle cx="20" cy="21" r="1"/><path d="M1 1h4l2.7 13.4a2 2 0 0 0 2 1.6h9.7a2 2 0 0 0 2-1.6L23 6H6"/>'); ?></span>
				<h3>Order &amp; purchase management</h3>
				<p>Accept or decline orders after checking availability, and pull up any supplier or purchase history from anywhere.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M4 4h16v4H4zM4 10h16v10H4zM9 14h6"/>'); ?></span>
				<h3>Reports &amp; analytics</h3>
				<p>Compare price rates, filter by department and see clear visual reports, so you can make buying decisions on the numbers.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">Less wastage, tighter control, more margin</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/>'); ?> For your hospital</h3>
				<ul>
					<li><?php echo $hr_check; ?> Less money tied up in dead and expired stock</li>
					<li><?php echo $hr_check; ?> No out-of-stock surprises and no overstocking</li>
					<li><?php echo $hr_check; ?> Accurate records with no duplicate entries</li>
					<li><?php echo $hr_check; ?> A clear effect on wastage and on revenue</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M3 7h18M6 7l1.5 12a1 1 0 0 0 1 1h7a1 1 0 0 0 1-1L19 7M9 7V4h6v3"/>'); ?> For your store &amp; pharmacy</h3>
				<ul>
					<li><?php echo $hr_check; ?> Reorder points and alerts do the watching for you</li>
					<li><?php echo $hr_check; ?> Dispensing updates stock automatically</li>
					<li><?php echo $hr_check; ?> GST billing and purchase records in one place</li>
					<li><?php echo $hr_check; ?> Techniques like ABC analysis built in</li>
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
			<a class="card reveal" href="<?php echo esc_url(home_url('/pharmacy-management-system/')); ?>">
				<h3>Pharmacy software</h3>
				<p>Dispensing, billing and stock for the pharmacy, sharing the same inventory.</p>
				<span class="more">Explore pharmacy</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">
				<h3>Hospital management system</h3>
				<p>The full platform that runs OPD/IPD, EMR/EHR, pharmacy, lab and billing.</p>
				<span class="more">Explore the HMS</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/laboratory-information-management-system/')); ?>">
				<h3>LIMS software</h3>
				<p>Lab orders, sample tracking and reagent stock on the same platform.</p>
				<span class="more">Explore LIMS</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals that control stock with Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_inv_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Inventory management software FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_inv_faq as $hr_i => $hr_faq) : ?>
				<details name="inventory-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Stop wastage before it reaches your books</h2>
					<p>A 30-minute demo on your own item list: live stock, forecasting, low-stock and expiry alerts, GST billing and reports.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See stock control on your own item list. Our team replies within one business day.</p>
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
