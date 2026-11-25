<?php
/**
 * Template Name: ABHA Landing
 * Template Post Type: page
 *
 * Page:       /abha/ (page 26834) - replaces the Elementor layout.
 * Body class: landing-page abha-landing (functions.php body_class map)
 * CSS:        css/common-landing.css + css/abha-software.css (inlined in wp_head)
 * JS:         none of its own. The hero form is [adharAuthForm] from the ABHA Card plugin
 *             (plugins/abha-card), which brings its own CSS/JS (handle abha-card);
 *             functions.php section 4 keeps that handle on this page only.
 * FAQ:        healthray_abha_faq_items() in functions.php (page accordion + FAQPage schema)
 * Forms:      hero = ABHA creation form; final CTA = shared lead form (id="demo-form")
 */
$hr_media    = 'https://healthray.com/wp-content/uploads';
$hr_abha_faq = function_exists( 'healthray_abha_faq_items' ) ? healthray_abha_faq_items() : array();
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
			<li><span aria-current="page">ABHA</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO: ABHA creation form ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Free · Voluntary · Aadhaar or mobile OTP</p>
			<h1 id="hero-title">Ayushman Bharat Health Account (ABHA): create your ABHA card
				<span class="pulse-word">online
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">ABHA is a free 14-digit health ID from the Government of India. It links your prescriptions, lab reports and discharge summaries from different hospitals to one account, and a new doctor sees them only when you give consent.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Create your ABHA on this page with your Aadhaar number and an OTP</li>
				<li><?php echo $hr_check; ?> Show it at ABDM-connected hospitals, clinics and labs instead of carrying files</li>
				<li><?php echo $hr_check; ?> Voluntary: you can deactivate or delete your ABHA later</li>
			</ul>
			<p class="hero-proof"><strong>1M+ ABHA IDs</strong> created on Healthray <span class="dot"></span> <strong>2,500+ hospitals</strong> <span class="dot"></span> NHA approved</p>
		</div>

		<div class="abha-form" id="abha-form">
			<?php echo do_shortcode( '[adharAuthForm]' ); ?>
			<p class="portal-note">You can also create it on the official portal, <a href="https://abha.abdm.gov.in/" target="_blank" rel="noopener">abha.abdm.gov.in</a>, or in the ABHA app.</p>
		</div>
	</div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Why create it with Healthray</p>
			<h2 id="compliance-title">NHA approved and built on national health standards</h2>
		</div>
		<div class="comp-grid">
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abdm-healthray-150x150.webp' ); ?>" alt="ABDM compliant - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abha-healthray-150x150.webp' ); ?>" alt="ABHA integrated health records" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/NHA.webp' ); ?>" alt="NHA approved" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
			<a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"><img src="<?php echo esc_url( $hr_media . '/2026/07/NABH-ehr-certified-150x150.webp' ); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span></a>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/iso-27001-healthray-150x150.webp' ); ?>" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/hl7-healthray-150x150.webp' ); ?>" alt="HL7 interoperable health records" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/FHIR.webp' ); ?>" alt="FHIR compliant health record exchange" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
		</div>
	</div>
</section>

<!-- ============ WHAT IS ABHA ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is the Ayushman Bharat Health Account (ABHA)?</h2>
			<p style="margin-top:14px">The Ayushman Bharat Health Account, or ABHA, is a health ID that any resident of India can create for free. It was earlier called the Health ID. ABHA is part of the <a href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">Ayushman Bharat Digital Mission (ABDM)</a>, which the National Health Authority (NHA) runs under the Ministry of Health and Family Welfare.</p>
			<p>Your ABHA has two parts. The ABHA number is a 14-digit number, printed on your ABHA card with a QR code. The ABHA address is a username you choose, such as name@abdm, that you use to sign in to health apps and share records.</p>
			<p>ABHA does not put your records in one government database. Each hospital, clinic or lab keeps the records it creates and links them to your ABHA. When another doctor needs your history, you approve the request in the ABHA app, choose what to share and for how long, and you can withdraw consent at any time.</p>
		</div>
		<img src="<?php echo esc_url( $hr_media . '/2024/12/Why-Generate-ABHA-Health-ID-with-Healthray.webp' ); ?>" alt="ABHA card shown on a computer and a phone after download" width="492" height="405" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ ABHA NUMBER VS ABHA ADDRESS ============ -->
<section aria-labelledby="number-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Two parts, one account</p>
			<h2 id="number-title">ABHA number vs ABHA address</h2>
			<p>You get both when you create your ABHA. Hospitals use the number; you use the address in apps.</p>
		</div>
		<div class="table-scroll">
			<table class="versus-table">
				<caption class="hp">Difference between the ABHA number and the ABHA address</caption>
				<thead>
					<tr><th scope="col">&nbsp;</th><th scope="col">ABHA number</th><th scope="col">ABHA address</th></tr>
				</thead>
				<tbody>
					<tr><td>What it is</td><td>A 14-digit number that identifies you in India's digital health system</td><td>A username you choose, such as name@abdm</td></tr>
					<tr><td>How you get it</td><td>Created once, with Aadhaar or a driving licence</td><td>You pick it while creating your ABHA, and you can link more than one</td></tr>
					<tr><td>Where it is used</td><td>At hospital registration, on your ABHA card and in its QR code</td><td>To sign in to the ABHA app and other health apps, and to share records</td></tr>
					<tr><td>Can it change?</td><td>No, it stays the same</td><td>Yes, you can create a new one</td></tr>
				</tbody>
			</table>
		</div>
	</div>
</section>

<!-- ============ HOW TO CREATE ============ -->
<section class="define" aria-labelledby="steps-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Step by step</p>
			<h2 id="steps-title">How to create your ABHA card online</h2>
			<p>With Aadhaar, it takes four steps in the form at the top of this page. Keep the phone linked to your Aadhaar with you for the OTP.</p>
		</div>
		<ol class="num-steps">
			<li class="reveal">
				<h3>Enter your Aadhaar number</h3>
				<p>Choose Aadhaar Number, type your 12-digit Aadhaar number, accept the consent and select Get OTP.</p>
			</li>
			<li class="reveal">
				<h3>Verify the OTP</h3>
				<p>Enter the OTP sent to your Aadhaar-linked mobile and give the number you want ABHA messages on. If it is a different number, you verify that one too.</p>
			</li>
			<li class="reveal">
				<h3>Choose your ABHA address</h3>
				<p>Pick one of the suggested ABHA addresses or type your own, such as name@abdm.</p>
			</li>
			<li class="reveal">
				<h3>Download your ABHA card</h3>
				<p>Your 14-digit ABHA number is created with a QR code. Download the card and keep it on your phone.</p>
			</li>
		</ol>
		<p class="versus-cta">Already have an ABHA? Choose Mobile Number in the form to see the ABHA addresses linked to your number. No Aadhaar? The official portal, <a href="https://abha.abdm.gov.in/" target="_blank" rel="noopener">abha.abdm.gov.in</a>, also accepts a driving licence.</p>
	</div>
</section>

<!-- ============ DOCUMENTS AND ELIGIBILITY ============ -->
<section aria-labelledby="docs-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Before you start</p>
			<h2 id="docs-title">Documents needed for ABHA, and who can create one</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>' ); ?> What you need</h3>
				<ul>
					<li><?php echo $hr_check; ?> Your Aadhaar number, and the phone linked to it for the OTP</li>
					<li><?php echo $hr_check; ?> Or a driving licence, on the official ABHA portal</li>
					<li><?php echo $hr_check; ?> A mobile number for ABHA messages</li>
					<li><?php echo $hr_check; ?> Nothing else: no PAN card, bank account, income or caste certificate</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/>' ); ?> Who can create one</h3>
				<ul>
					<li><?php echo $hr_check; ?> Any resident of India, free of charge</li>
					<li><?php echo $hr_check; ?> Each family member needs their own ABHA, because health records are personal</li>
					<li><?php echo $hr_check; ?> It is not linked to income or to PM-JAY eligibility</li>
					<li><?php echo $hr_check; ?> It is voluntary, and you can deactivate or delete it later</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it helps</p>
			<h2 id="benefits-title">Benefits of the ABHA card</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 7a2 2 0 0 1 2-2h4l2 2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>' ); ?></span>
				<h3>All your records in one place</h3>
				<p>Prescriptions, lab reports and discharge summaries from different hospitals and labs, linked to one ID.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="7" y="2" width="10" height="20" rx="2"/><path d="M11 18h2"/>' ); ?></span>
				<h3>No more carrying files</h3>
				<p>A new doctor can see your history with your consent, so you bring your ABHA instead of a folder of papers.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>' ); ?></span>
				<h3>You decide who sees what</h3>
				<p>Every request needs your consent. You choose the records and how long access lasts, and you can withdraw it.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 7V5a2 2 0 0 1 2-2h2M17 3h2a2 2 0 0 1 2 2v2M21 17v2a2 2 0 0 1-2 2h-2M7 21H5a2 2 0 0 1-2-2v-2M7 12h10"/>' ); ?></span>
				<h3>Faster registration</h3>
				<p>At hospitals that support Scan &amp; Share, scan the desk's QR code with the ABHA app and your details reach the counter.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 1.7 3h10.6a2 2 0 0 0 1.7-3l-5-9V3"/>' ); ?></span>
				<h3>Fewer repeat tests</h3>
				<p>Doctors can see recent reports from other labs, so tests are not repeated without need.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2.2"/><path d="M6 16c.6-1.5 1.7-2.2 3-2.2s2.4.7 3 2.2M14.5 10h4M14.5 13.5h3"/>' ); ?></span>
				<h3>Free, and it moves with you</h3>
				<p>ABHA costs nothing and works with participating providers across India, so it stays useful when you change city or doctor.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ ABHA VS AYUSHMAN CARD ============ -->
<section aria-labelledby="vs-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Often confused</p>
			<h2 id="vs-title">ABHA card vs Ayushman card: what's the difference?</h2>
			<p>Both carry the Ayushman Bharat name, but they belong to different schemes and do different jobs.</p>
		</div>
		<div class="table-scroll">
			<table class="versus-table">
				<caption class="hp">Difference between the ABHA card and the Ayushman card</caption>
				<thead>
					<tr><th scope="col">&nbsp;</th><th scope="col">ABHA card</th><th scope="col">Ayushman card (PM-JAY)</th></tr>
				</thead>
				<tbody>
					<tr><td>What it is</td><td>A health ID that links your medical records</td><td>The card that shows you are covered under PM-JAY</td></tr>
					<tr><td>Scheme</td><td>Ayushman Bharat Digital Mission (ABDM)</td><td>Ayushman Bharat Pradhan Mantri Jan Arogya Yojana (PM-JAY)</td></tr>
					<tr><td>Who can get it</td><td>Any resident of India</td><td>Eligible families, and every citizen aged 70 or above</td></tr>
					<tr><td>What it gives you</td><td>Digital access to your records, shared with your consent</td><td>Cashless hospital treatment of up to ₹5 lakh per family per year</td></tr>
					<tr><td>Where to get it</td><td>This page, abha.abdm.gov.in or the ABHA app</td><td>beneficiary.nha.gov.in, the Ayushman app or an empanelled hospital's help desk</td></tr>
				</tbody>
			</table>
		</div>
		<p class="versus-cta">An ABHA alone does not give you PM-JAY cover. To check whether your family is eligible and how to get the Ayushman card, read our <a href="<?php echo esc_url( home_url( '/pmjay/' ) ); ?>">PM-JAY guide</a>.</p>
	</div>
</section>

<!-- ============ FOR HOSPITALS ============ -->
<section class="define" aria-labelledby="hospitals-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">For hospitals and clinics</p>
			<h2 id="hospitals-title">Create and verify ABHA at your registration desk</h2>
			<p style="margin-top:14px">Healthray creates and verifies patients' ABHA numbers during registration and links each visit's prescriptions, lab reports and discharge summaries to them, inside the OPD and IPD software your staff already use. There is no separate portal and no second login.</p>
			<p>Records linked to KYC-verified ABHA addresses also count towards payments under the <a href="<?php echo esc_url( home_url( '/dhis/' ) ); ?>">Digital Health Incentive Scheme</a>. Read how the whole system fits together on our <a href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">ABDM integration</a> page.</p>
			<a class="btn btn-primary" href="#demo-form">Book a free demo</a>
		</div>
		<img src="<?php echo esc_url( $hr_media . '/2024/12/What-are-the-Steps-to-Create-Ayushman-Bharat-Health.webp' ); ?>" alt="ABHA creation screen in the Healthray app" width="418" height="552" loading="lazy" decoding="async">
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
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">
				<h3>Ayushman Bharat Digital Mission</h3>
				<p>How ABDM connects hospitals, labs and patients, and what M1, M2 and M3 integration means.</p>
				<span class="more">Read about ABDM</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/pmjay/' ) ); ?>">
				<h3>PM-JAY</h3>
				<p>Who is eligible for ₹5 lakh cashless cover, what it pays for and how to get the Ayushman card.</p>
				<span class="more">Read about PM-JAY</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/dhis/' ) ); ?>">
				<h3>Digital Health Incentive Scheme</h3>
				<p>How hospitals, labs and software companies earn incentives for ABHA-linked records.</p>
				<span class="more">Read about DHIS</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode( '[hr_stats]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_abha_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">ABHA card FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_abha_faq as $hr_i => $hr_faq ) : ?>
				<details name="abha-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Make ABHA part of every patient registration</h2>
					<p>A 30-minute demo on your own workflow: ABHA creation and verification at the front desk, record linking after each visit, and sharing with the patient's consent.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card" id="demo-form">
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
