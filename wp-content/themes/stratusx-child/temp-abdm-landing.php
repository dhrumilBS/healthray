<?php
/**
 * Template Name: ABDM Landing
 * Template Post Type: page
 *
 * Page:       /abdm/ (page 27377) - replaces the Elementor layout.
 * Body class: landing-page abdm-landing (functions.php body_class map)
 * CSS:        css/common-landing.css + css/abdm-software.css (inlined in wp_head)
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_abdm_faq_items() in functions.php - the same list feeds
 *             this accordion and the FAQPage schema (hr_head_faqs filter).
 */
$hr_media    = 'https://healthray.com/wp-content/uploads';
$hr_abdm_faq = function_exists( 'healthray_abdm_faq_items' ) ? healthray_abdm_faq_items() : array();
$hr_check    = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">ABDM</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">NHA approved · ABDM compliant · ABHA ready</p>
			<h1 id="hero-title">ABDM integration for hospitals and clinics, built into
				<span class="pulse-word">every visit
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Healthray connects your facility to the Ayushman Bharat Digital Mission without extra screens. Create or verify the patient's ABHA at registration, link each visit's records to it, and share them only with the patient's consent.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> ABHA creation and verification at the front desk, with Aadhaar OTP</li>
				<li><?php echo $hr_check; ?> Prescriptions, lab reports and discharge summaries linked to the patient's ABHA</li>
				<li><?php echo $hr_check; ?> Works inside the OPD, IPD, lab and pharmacy software your staff already use</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free ABDM demo</h2>
			<p>See ABHA creation and record linking live. Our team replies within one business day.</p>
			<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
		</div>
	</div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Compliance built in</p>
			<h2 id="compliance-title">Approved, certified and built on open health standards</h2>
		</div>
		<div class="comp-grid">
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abdm-healthray-150x150.webp' ); ?>" alt="ABDM compliant hospital software - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abha-healthray-150x150.webp' ); ?>" alt="ABHA integrated health records" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/NHA.webp' ); ?>" alt="NHA approved software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
			<a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"><img src="<?php echo esc_url( $hr_media . '/2026/07/NABH-ehr-certified-150x150.webp' ); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span></a>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/FHIR.webp' ); ?>" alt="FHIR compliant health record exchange" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/hl7-healthray-150x150.webp' ); ?>" alt="HL7 interoperable hospital software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/iso-27001-healthray-150x150.webp' ); ?>" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
		</div>
	</div>
</section>

<!-- ============ WHAT IS ABDM ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is the Ayushman Bharat Digital Mission (ABDM)?</h2>
			<p style="margin-top:14px">The Ayushman Bharat Digital Mission is the Government of India's programme to build one connected digital health ecosystem for the country. Prime Minister Narendra Modi launched it nationwide on 27 September 2021, after a pilot in six union territories that began in August 2020 under the name National Digital Health Mission. The National Health Authority (NHA), under the Ministry of Health and Family Welfare, runs it.</p>
			<p>ABDM does not put everyone's medical records in one central database. Records stay with the hospitals, clinics and labs that create them. ABDM provides the shared IDs, the national registries and a consent system, so a record can move from one provider to another when, and only when, the patient approves.</p>
			<p>For a hospital or clinic, joining ABDM means three things: your facility and doctors are listed in national registries, your patients are identified by their ABHA number, and the records you create can reach the patient's phone and, with consent, the next doctor who treats them.</p>
		</div>
		<img class="define-img" src="<?php echo esc_url( $hr_media . '/2024/02/About-Ayushman-Bharat-Digital-Mission.webp' ); ?>" alt="Ayushman Bharat Digital Mission connecting hospitals, doctors and patients" width="525" height="687" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ BUILDING BLOCKS ============ -->
<section aria-labelledby="blocks-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">How ABDM is built</p>
			<h2 id="blocks-title">The building blocks of ABDM</h2>
			<p>ABDM is a set of national registries and digital rails. These are the parts a hospital or clinic works with.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2.2"/><path d="M6 16c.6-1.5 1.7-2.2 3-2.2s2.4.7 3 2.2M14.5 10h4M14.5 13.5h3"/></svg></span>
				<h3>ABHA - Ayushman Bharat Health Account</h3>
				<p>A free 14-digit health ID for every resident, created with Aadhaar or a driving licence. Patients also get an ABHA address, such as name@abdm, to use in health apps. <a href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">How to create an ABHA</a>.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 11l-3 3-2-2"/></svg></span>
				<h3>HPR - Healthcare Professionals Registry</h3>
				<p>A verified national list of doctors, nurses and other health professionals, across modern and traditional systems of medicine. Registered doctors can sign and share records inside ABDM.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4M9 21v-4h6v4"/></svg></span>
				<h3>HFR - Health Facility Registry</h3>
				<p>A national list of public and private hospitals, clinics, labs, imaging centres and pharmacies. A facility must be on HFR before it can share records through ABDM.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
				<h3>Consent manager</h3>
				<p>The Health Information Exchange and Consent Manager moves records between providers. Nothing is shared until the patient approves a request, and the patient can set an expiry or withdraw consent.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/></svg></span>
				<h3>Personal health records (PHR) apps</h3>
				<p>Patients see the records linked to their ABHA in the ABHA app or another ABDM-approved app, and decide from there who can see them.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7h4l2 3h6l2-3h4M7 7v10a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V7"/></svg></span>
				<h3>Scan &amp; Share registration</h3>
				<p>Patients scan the facility's QR code with their ABHA app, and their details reach the registration desk instantly. Less typing, shorter OPD queues.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ M1 / M2 / M3 ============ -->
<section class="define" id="milestones" aria-labelledby="milestones-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">ABDM integration, explained</p>
			<h2 id="milestones-title">What ABDM integration means: M1, M2 and M3</h2>
			<p>NHA describes ABDM integration for software in three milestones. Each one adds a step to how your hospital handles patient records.</p>
		</div>
		<div class="table-scroll">
			<table class="versus-table">
				<caption class="hp">ABDM integration milestones M1, M2 and M3</caption>
				<thead>
					<tr><th scope="col">Milestone</th><th scope="col">What it covers</th><th scope="col">What changes in your hospital</th></tr>
				</thead>
				<tbody>
					<tr><td>M1 - ABHA creation and verification</td><td>Create a new ABHA for a patient or verify an existing one, and save it with the patient's registration</td><td>The front desk creates or verifies the ABHA while registering the patient, or the patient uses Scan &amp; Share</td></tr>
					<tr><td>M2 - Health Information Provider (HIP)</td><td>Link the records you create - prescriptions, lab reports, discharge summaries - to the patient's ABHA and share them when the patient asks</td><td>After each visit, the patient finds the records in their ABHA app instead of carrying paper files</td></tr>
					<tr><td>M3 - Health Information User (HIU)</td><td>Request a patient's records from other providers and view them after the patient gives consent</td><td>Doctors see reports from other hospitals and labs before the consultation, so fewer tests are repeated</td></tr>
				</tbody>
			</table>
		</div>
		<p class="versus-cta">Healthray is NHA approved and ABDM compliant. ABHA creation, verification and record linking happen inside the normal OPD and IPD workflow, with no separate portal or second login. Every record links to the same longitudinal patient file in our <a href="<?php echo esc_url( home_url( '/ehr-software/' ) ); ?>">EHR software</a>.</p>
	</div>
</section>

<!-- ============ HOW IT WORKS ============ -->
<section aria-labelledby="steps-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Step by step</p>
			<h2 id="steps-title">How ABDM works in your hospital with Healthray</h2>
			<p>Four steps, and only the first one happens outside your daily routine.</p>
		</div>
		<ol class="num-steps">
			<li class="reveal">
				<h3>Register your facility and doctors</h3>
				<p>Your hospital is listed on the Health Facility Registry and your doctors on the Healthcare Professionals Registry. Our onboarding team walks you through it.</p>
			</li>
			<li class="reveal">
				<h3>Identify the patient with ABHA</h3>
				<p>The patient scans your QR code with Scan &amp; Share, or the front desk creates or verifies their ABHA with an Aadhaar OTP.</p>
			</li>
			<li class="reveal">
				<h3>Treat and document as usual</h3>
				<p>Consultation notes, e-prescriptions, lab results and discharge summaries are recorded in Healthray the way your team already works.</p>
			</li>
			<li class="reveal">
				<h3>Link and share with consent</h3>
				<p>The visit's records are linked to the patient's ABHA. The patient sees them on their phone, and other providers see them only with consent.</p>
			</li>
		</ol>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="benefits-title">Benefits of ABDM for hospitals and patients</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/></svg> For hospitals, clinics and labs</h3>
				<ul>
					<li><?php echo $hr_check; ?> Faster registration: Scan &amp; Share fills the patient's details, so queues at the counter move quicker</li>
					<li><?php echo $hr_check; ?> A fuller history before the consultation, from other providers, with the patient's consent</li>
					<li><?php echo $hr_check; ?> Fewer repeat tests and fewer lost or unreadable paper reports</li>
					<li><?php echo $hr_check; ?> <span>Incentives for ABHA-linked records under the <a href="<?php echo esc_url( home_url( '/dhis/' ) ); ?>">Digital Health Incentive Scheme</a></span></li>
					<li><?php echo $hr_check; ?> <span>Ready for government schemes such as <a href="<?php echo esc_url( home_url( '/pmjay/' ) ); ?>">PM-JAY</a> that use ABHA</span></li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg> For patients</h3>
				<ul>
					<li><?php echo $hr_check; ?> One free ABHA number for life, accepted by participating providers across India</li>
					<li><?php echo $hr_check; ?> Prescriptions and reports on the phone, at any time</li>
					<li><?php echo $hr_check; ?> Full control: records are shared only with consent, which can be withdrawn</li>
					<li><?php echo $hr_check; ?> Easier referrals and second opinions, with the history already available</li>
					<li><?php echo $hr_check; ?> Less paperwork at every hospital visit</li>
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
			<h2 id="related-title">More on India's digital health schemes</h2>
		</div>
		<div class="grid-3 related-grid">
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">
				<h3>ABHA card</h3>
				<p>What the Ayushman Bharat Health Account is, and how patients create one in minutes.</p>
				<span class="more">Read about ABHA</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/dhis/' ) ); ?>">
				<h3>Digital Health Incentive Scheme</h3>
				<p>How hospitals, labs and software companies earn incentives for ABDM transactions.</p>
				<span class="more">Read about DHIS</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/pmjay/' ) ); ?>">
				<h3>PM-JAY</h3>
				<p>Pradhan Mantri Jan Arogya Yojana: cover, eligibility and what it means for hospitals.</p>
				<span class="more">Read about PM-JAY</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode( '[hr_stats]' ); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode( '[hr_clients eyebrow="Trusted across India" title="Hospitals running ABDM-ready records on Healthray"]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_abdm_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">ABDM FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_abdm_faq as $hr_i => $hr_faq ) : ?>
				<details name="abdm-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Connect your hospital to ABDM without slowing the counter</h2>
					<p>A 30-minute demo on your own workflow: ABHA creation at registration, record linking after the visit, and consent-based sharing, so you see exactly what changes for your staff.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free ABDM demo</h2>
					<p>See ABHA creation and record linking live. Our team replies within one business day.</p>
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
