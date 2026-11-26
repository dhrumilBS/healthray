<?php
/**
 * Template Name: Awards Landing
 * Template Post Type: page
 *
 * Page:       /awards/ (page 62094) - replaces the Elementor layout.
 * Body class: landing-page awards-landing (functions.php body_class map)
 * CSS:        css/common-landing.css (inlined in wp_head); no page-only CSS needed
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_awards_faq_items() in functions.php (page accordion + FAQPage schema)
 * Focus kw:   "ABDM compliant hospital software" (Yoast). Information page: no SoftwareApplication schema.
 * Note:       The old page listed six awards with no awarding body, date or source, so they were
 *             not carried over. This is a certifications/compliance page built on real, verifiable
 *             claims the site already makes. CERT-In audit wording matches existing site usage.
 */
$hr_media      = 'https://healthray.com/wp-content/uploads';
$hr_awards_faq = function_exists( 'healthray_awards_faq_items' ) ? healthray_awards_faq_items() : array();
$hr_check      = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon       = static function ( $paths ) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">Awards &amp; Certifications</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">NHA approved · NABH certified · ISO 27001 · HIPAA</p>
			<h1 id="hero-title">ABDM compliant hospital software,
				<span class="pulse-word">certified end to end
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Healthray is built to meet India's health-data rules, not bolt them on afterwards. It is NHA approved and ABDM compliant, NABH certified, ISO 27001 certified, HIPAA compliant, and security-audited by a CERT-In empanelled auditor.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> NHA approved and ABDM compliant: create ABHA and link records inside your workflow</li>
				<li><?php echo $hr_check; ?> NABH-certified healthcare software, listed on the official NABH portal</li>
				<li><?php echo $hr_check; ?> ISO 27001, HIPAA and a CERT-In empanelled security audit</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See how Healthray keeps compliance built in. Our team replies within one business day.</p>
			<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
		</div>
	</div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Certifications &amp; standards</p>
			<h2 id="compliance-title">Every certification and standard Healthray supports</h2>
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

<!-- ============ WHAT EACH CERTIFICATION MEANS ============ -->
<section aria-labelledby="means-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What they mean</p>
			<h2 id="means-title">What each certification means for your hospital</h2>
			<p>Healthray's compliance is verifiable, not decorative. Here is what each standard covers.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2.2"/><path d="M6 16c.6-1.5 1.7-2.2 3-2.2s2.4.7 3 2.2M14.5 10h4M14.5 13.5h3"/>' ); ?></span>
				<h3>NHA approved &amp; ABDM compliant</h3>
				<p>Approved by the National Health Authority under the Ayushman Bharat Digital Mission. Create and verify ABHA and link records through ABDM. <a href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">See our ABDM integration</a>.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4M9 21v-4h6v4"/>' ); ?></span>
				<h3>NABH certified</h3>
				<p>NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals &amp; Healthcare Providers, so accreditation evidence comes from daily work.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>' ); ?></span>
				<h3>ISO 27001 certified</h3>
				<p>Certified against the international standard for information security management: controlled access, encryption, and documented security processes.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M12 3l7 3v5c0 4.5-3 8.5-7 10-4-1.5-7-5.5-7-10V6l7-3z"/><path d="M9 12l2 2 4-4"/>' ); ?></span>
				<h3>HIPAA compliant</h3>
				<p>Patient data is handled to HIPAA privacy and security rules, with role-based access control and complete audit trails on every view and edit.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M4 7h16M4 12h16M4 17h10"/><circle cx="18" cy="17" r="3"/>' ); ?></span>
				<h3>HL7, FHIR, SNOMED CT &amp; ICD</h3>
				<p>Records are structured on HL7 and FHIR with SNOMED CT terminology and ICD-10/11 coding, so they can be exchanged with labs, insurers and other providers.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M12 2l8 4v6c0 5-3.5 8.5-8 10-4.5-1.5-8-5-8-10V6l8-4z"/><path d="M12 8v4M12 16h.01"/>' ); ?></span>
				<h3>CERT-In empanelled audit</h3>
				<p>The application is security-audited by a CERT-In empanelled auditor, the Government of India's standard for independent application security testing.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ WHY IT MATTERS ============ -->
<section class="define" aria-labelledby="matters-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Why it matters</p>
			<h2 id="matters-title">Why certified software matters when you choose an HMS</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4"/>' ); ?> For your hospital</h3>
				<ul>
					<li><?php echo $hr_check; ?> NABH accreditation evidence is generated by everyday use, not assembled before an assessment</li>
					<li><?php echo $hr_check; ?> <span>Meets the compliance conditions in government and institutional tenders</span></li>
					<li><?php echo $hr_check; ?> <span>Eligible for incentives on ABHA-linked records under the <a href="<?php echo esc_url( home_url( '/dhis/' ) ); ?>">Digital Health Incentive Scheme</a></span></li>
					<li><?php echo $hr_check; ?> Lower security risk, with an independent audit and documented controls</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/>' ); ?> For your patients</h3>
				<ul>
					<li><?php echo $hr_check; ?> Health data is encrypted and access-controlled</li>
					<li><?php echo $hr_check; ?> <span>Records link to the patient's <a href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">ABHA</a> and travel with them</span></li>
					<li><?php echo $hr_check; ?> Records are shared with other providers only with the patient's consent</li>
					<li><?php echo $hr_check; ?> Privacy is designed in, not added as an afterthought</li>
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
			<h2 id="related-title">More on India's digital health standards</h2>
		</div>
		<div class="grid-3 related-grid">
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">
				<h3>ABDM integration</h3>
				<p>How ABDM connects hospitals, labs and patients, and what M1, M2 and M3 integration means.</p>
				<span class="more">Read about ABDM</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">
				<h3>ABHA card</h3>
				<p>What the Ayushman Bharat Health Account is, and how patients create one in a few steps.</p>
				<span class="more">Read about ABHA</span>
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

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode( '[hr_clients eyebrow="Trusted across India" title="Hospitals that run on certified, ABDM-compliant Healthray"]' ); ?>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode( '[hr_testimonials eyebrow="Independently rated" title="Rated 4.8/5 on Capterra and SoftwareSuggest"]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_awards_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Certifications &amp; compliance FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_awards_faq as $hr_i => $hr_faq ) : ?>
				<details name="awards-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">See compliance built into a live hospital system</h2>
					<p>A 30-minute demo on your own workflow: ABHA at registration, records linked with consent, and the audit trails and security controls that back every certification.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>See how Healthray keeps compliance built in. Our team replies within one business day.</p>
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
