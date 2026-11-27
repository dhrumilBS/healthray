<?php
/**
 * Template Name: Medical College Landing
 * Template Post Type: page
 *
 * Page:       /medical-college-software/ (page 59673) - replaces the Elementor layout.
 * Body class: landing-page medical-college-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_medical_college_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "Medical College Software" (Yoast). Product page: SoftwareApplication schema in functions.php.
 * Note:       Rewritten from the old features/modules copy, keeping the structure and
 *             only claims the site already makes. Dropped the [elementor-template] blocks.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_mc_faq = function_exists('healthray_medical_college_faq_items') ? healthray_medical_college_faq_items() : array();
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
			<li><span aria-current="page">Medical College Software</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Admissions · academics · hostel · faculty</p>
			<h1 id="hero-title">Medical college software that runs academics and the
				<span class="pulse-word">hospital together
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Manage students, admissions, attendance, fees, hostel and faculty on one platform, and connect it to the teaching hospital that runs on Healthray, so academic and clinical records sit side by side.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Admissions, fees and student records in one place</li>
				<li><?php echo $hr_check; ?> Attendance, timetables and academics with e-books</li>
				<li><?php echo $hr_check; ?> Hostel, faculty and counselling management</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See the college and hospital on one platform. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS MEDICAL COLLEGE SOFTWARE ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is medical college software?</h2>
			<p style="margin-top:14px">Medical college software runs the administration of a medical college: admissions and fees, student records and attendance, timetables and academics, hostel, faculty and counselling, in one system instead of scattered registers and spreadsheets.</p>
			<p>What sets a medical college apart is its teaching hospital. Healthray connects the two, so the same platform that manages students also runs the <a href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">hospital</a> where they train.</p>
			<p>Students see real cases, faculty track their clinical rotations, and academic and clinical records stay in one secure place.</p>
		</div>
		<img src="<?php echo esc_url($hr_media . '/2025/05/Why-To-Choose-Our-Medical-College-Management-Software_-Healthray.webp'); ?>" alt="Why choose Healthray medical college management software" width="433" height="403" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ FEATURES ============ -->
<section aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What it manages</p>
			<h2 id="features-title">Robust features of the medical college software</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6M9 13h6M9 17h4"/>'); ?></span>
				<h3>Admissions &amp; forms</h3>
				<p>Build any form you need, from the admission form to fee receipts, and accept fees online with reminders for what is due.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M22 10L12 5 2 10l10 5 10-5zM6 12v5c0 1 3 2 6 2s6-1 6-2v-5"/>'); ?></span>
				<h3>Academic management</h3>
				<p>Timetables, attendance and e-books students download after logging in, with AI help to build faculty schedules.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>'); ?></span>
				<h3>Counselling management</h3>
				<p>Group students by interest, assign a counsellor and run counselling online, with a record of each session.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/>'); ?></span>
				<h3>Hostel management</h3>
				<p>Track occupied and vacant rooms, room item lists, documents and hostel finances, all in one view.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M12 3l9 4-9 4-9-4zM7 10v5c0 1 2 3 5 3s5-2 5-3v-5M20 9v6"/>'); ?></span>
				<h3>Faculty management</h3>
				<p>Assign duties, track workload and clinical rotations, and keep a ledger and record for every professor and trainer.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon('<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 11l2 2 4-4"/>'); ?></span>
				<h3>Analytics &amp; data security</h3>
				<p>An interactive dashboard for every task, and ISO 27001 security with a login per user to protect college data.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">Less admin, more teaching</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/>'); ?> For the college</h3>
				<ul>
					<li><?php echo $hr_check; ?> Student records, attendance and fees in one place</li>
					<li><?php echo $hr_check; ?> Online fees with reminders and clear ledgers</li>
					<li><?php echo $hr_check; ?> Reports on admissions, attendance and finances</li>
					<li><?php echo $hr_check; ?> One secure system across academics and the hospital</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>'); ?> For students &amp; faculty</h3>
				<ul>
					<li><?php echo $hr_check; ?> Timetables, materials and e-books in one login</li>
					<li><?php echo $hr_check; ?> Clinical rotations tracked in the teaching hospital</li>
					<li><?php echo $hr_check; ?> Notifications for fees, messages and course updates</li>
					<li><?php echo $hr_check; ?> Less paperwork, so faculty spend time teaching</li>
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
				<p>The platform that runs your teaching hospital, where students train on real cases.</p>
				<span class="more">Explore the HMS</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/laboratory-information-management-system/')); ?>">
				<h3>LIMS software</h3>
				<p>Lab and pathology management for the diagnostics students learn on.</p>
				<span class="more">Explore LIMS</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url(home_url('/ehr-software/')); ?>">
				<h3>EHR software</h3>
				<p>The patient record that sits behind every clinical rotation in the hospital.</p>
				<span class="more">Explore EHR</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Institutions that run on Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode('[hr_testimonials eyebrow="Customer stories" title="What doctors say about Healthray"]'); ?>

<!-- ============ FAQ ============ -->
<?php if ($hr_mc_faq) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Medical college software FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ($hr_mc_faq as $hr_i => $hr_faq) : ?>
				<details name="medical-college-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Run your college and its hospital on one platform</h2>
					<p>A 30-minute demo on your own setup: admissions and fees, academics and attendance, hostel and faculty, linked to the teaching hospital.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See the college and hospital on one platform. Our team replies within one business day.</p>
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
