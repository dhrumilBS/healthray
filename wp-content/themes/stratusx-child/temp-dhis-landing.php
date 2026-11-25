<?php
/**
 * Template Name: DHIS Landing
 * Template Post Type: page
 *
 * Page:       /dhis/ (page 26844) - replaces the Elementor layout.
 * Body class: landing-page dhis-landing (functions.php body_class map)
 * CSS:        css/common-landing.css + css/dhis-software.css (inlined in wp_head)
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_dhis_faq_items() in functions.php (page accordion + FAQPage schema)
 * Rates:      NHA DHIS Corrigendum 7 (9 April 2026), valid for April-September 2026.
 *             Re-check abdm.gov.in/dhis for a newer corrigendum before each update.
 */
$hr_media    = 'https://healthray.com/wp-content/uploads';
$hr_dhis_faq = function_exists( 'healthray_dhis_faq_items' ) ? healthray_dhis_faq_items() : array();
$hr_check    = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon     = static function ( $paths ) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
$hr_c7_pdf   = 'https://abdm.gov.in/strapicms/uploads/Corrigendum_7_f456cb207a.pdf';
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">DHIS</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">For hospitals, clinics, labs and pharmacies</p>
			<h1 id="hero-title">Digital Health Incentive Scheme (DHIS): incentives for
				<span class="pulse-word">ABHA-linked records
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Under DHIS, the National Health Authority pays health facilities and their software companies for digital health records linked to patients' ABHA, above the first 100 eligible records each month. Healthray creates those records inside your normal OPD and IPD workflow.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Open to public and private facilities registered on the Health Facility Registry</li>
				<li><?php echo $hr_check; ?> ₹10 per discharge summary or diagnostic report, ₹5 per prescription or other record (April-September 2026 rates)</li>
				<li><?php echo $hr_check; ?> Healthray is NHA approved and ABDM compliant</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free ABDM demo</h2>
			<p>See ABHA record linking live. Our team replies within one business day.</p>
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

<!-- ============ WHAT IS DHIS ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is the Digital Health Incentive Scheme (DHIS)?</h2>
			<p style="margin-top:14px">The Digital Health Incentive Scheme is a National Health Authority (NHA) scheme under the <a href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">Ayushman Bharat Digital Mission (ABDM)</a>. It started on 1 January 2023 to cover part of the cost of going digital: health facilities, and the software companies that serve them, earn a fixed amount for each eligible digital health transaction above a monthly threshold.</p>
			<p>An eligible transaction is usually a health record, such as a prescription, lab report or discharge summary, that your software links to the patient's <a href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">ABHA</a>. From April 2026, only records linked to KYC-verified ABHA addresses count.</p>
			<p>NHA revises the scheme through corrigenda. The rates on this page come from Corrigendum 7, dated 9 April 2026, which covers transactions from April to September 2026. Check <a href="https://abdm.gov.in/DHIS" target="_blank" rel="noopener">abdm.gov.in/dhis</a> for the rules that apply to later months.</p>
		</div>
		<img class="define-img" src="<?php echo esc_url( $hr_media . '/2024/02/About-Digital-Health-Incentive-Scheme-1.webp' ); ?>" alt="Patient holding a health card at a clinic, for the Digital Health Incentive Scheme" width="525" height="687" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ WHO CAN CLAIM ============ -->
<section aria-labelledby="who-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Eligibility</p>
			<h2 id="who-title">Who can claim DHIS incentives?</h2>
			<p>The scheme is open to all public and private sector entities that create or carry ABHA-linked records.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4M9 21v-4h6v4"/>' ); ?></span>
				<h3>Hospitals, clinics and nursing homes</h3>
				<p>Public or private, of any bed size, registered on the Health Facility Registry (HFR).</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 1.7 3h10.6a2 2 0 0 0 1.7-3l-5-9V3"/>' ); ?></span>
				<h3>Labs, diagnostic centres and pharmacies</h3>
				<p>Laboratories, imaging and diagnostic centres and pharmacies, also registered on HFR.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="2" y="4" width="20" height="13" rx="2"/><path d="M8 21h8M12 17v4"/>' ); ?></span>
				<h3>Digital solution companies</h3>
				<p>Companies whose software, such as an HMIS or LMIS, facilities use. They claim for transactions done in their software, when at least 10 of their facilities link records in a month.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ INCENTIVE RATES ============ -->
<section class="define" aria-labelledby="rates-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Incentive rates</p>
			<h2 id="rates-title">DHIS incentive per transaction (April-September 2026)</h2>
			<p>Rates for health facilities and digital solution companies from NHA's Corrigendum 7. Only transactions above the first 100 eligible ones in a month are paid.</p>
		</div>
		<div class="table-scroll">
			<table class="versus-table rates-table">
				<caption class="hp">DHIS incentive rates for April to September 2026, from Corrigendum 7 dated 9 April 2026</caption>
				<thead>
					<tr><th scope="col">Transaction</th><th scope="col">Health facility</th><th scope="col">Digital solution company</th></tr>
				</thead>
				<tbody>
					<tr><td>Discharge summary or diagnostic report linked to a KYC-verified ABHA (Category 1)</td><td>₹10 per record</td><td>₹5 per record</td></tr>
					<tr><td>Prescription, OPD consultation, immunisation, wellness record, invoice or other record linked to a KYC-verified ABHA (Category 2)</td><td>₹5 per record</td><td>₹2.50 per record</td></tr>
					<tr><td>Records shared successfully after a consent request</td><td colspan="2">₹10 to the party that requested the records (HIU) and ₹5 to the source of the records (HIP or health locker)</td></tr>
					<tr><td>AB PM-JAY claim linked to ABHA, filed in FHIR format through NHCX</td><td>₹200 or 10% of the claim amount, whichever is lower, up to ₹1 crore in total</td><td>₹10 per claim, up to ₹1 crore in total</td></tr>
				</tbody>
			</table>
		</div>
		<p class="versus-cta">What decides the payment: records must be linked to a KYC-verified ABHA address; the same ABHA counts once a day and up to five times a month; the first 100 eligible transactions in a month earn nothing, and that deduction comes off the lowest-paying records first; Scan &amp; Share and health-locker transactions earn no record-linking incentive. The corrigendum also sets rates for UHI and Scan &amp; Pay transactions. Source: <a href="<?php echo esc_url( $hr_c7_pdf ); ?>" target="_blank" rel="noopener">NHA DHIS Corrigendum 7 (PDF)</a>.</p>
	</div>
</section>

<!-- ============ WORKED EXAMPLE ============ -->
<section aria-labelledby="example-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Worked example</p>
			<h2 id="example-title">How a month's DHIS incentive is calculated</h2>
			<p>NHA's own example: in May 2026 a clinic links 70 discharge summaries and 80 OPD prescriptions to KYC-verified ABHA addresses.</p>
		</div>
		<div class="calc-grid">
			<div class="calc-card reveal">
				<h3>Health facility</h3>
				<ul>
					<li>70 discharge summaries × ₹10 <b>₹700</b></li>
					<li>80 prescriptions × ₹5 <b>₹400</b></li>
					<li>Minus the first 100 records, cheapest first: 80 × ₹5 + 20 × ₹10 <b>− ₹600</b></li>
					<li class="total">Incentive for the month <b>₹500</b></li>
				</ul>
			</div>
			<div class="calc-card reveal">
				<h3>Digital solution company</h3>
				<ul>
					<li>70 discharge summaries × ₹5 <b>₹350</b></li>
					<li>80 prescriptions × ₹2.50 <b>₹200</b></li>
					<li>Minus the first 100 records, cheapest first: 80 × ₹2.50 + 20 × ₹5 <b>− ₹300</b></li>
					<li class="total">Incentive for the month <b>₹250</b></li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- ============ HOW TO REGISTER AND CLAIM ============ -->
<section class="define" aria-labelledby="steps-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Step by step</p>
			<h2 id="steps-title">How to register for DHIS and claim incentives</h2>
			<p>The first three steps happen once. After that, eligible records count every month.</p>
		</div>
		<ol class="num-steps">
			<li class="reveal">
				<h3>Register on HPR and HFR</h3>
				<p>The doctor or owner gets a Healthcare Professionals Registry ID, and the facility is registered and verified on the Health Facility Registry.</p>
			</li>
			<li class="reveal">
				<h3>Use ABDM-integrated software</h3>
				<p>A record counts only when your software links it to the patient's ABHA. Healthray does this inside the normal OPD and IPD workflow.</p>
			</li>
			<li class="reveal">
				<h3>Register for DHIS</h3>
				<p>Register from your facility's HFR account and give the bank account the incentive should be paid into. Software companies register through their ABDM sandbox profile.</p>
			</li>
			<li class="reveal">
				<h3>Link records and claim</h3>
				<p>Link records to KYC-verified ABHA addresses, then submit the month's claim. Transactions above the first 100 are paid at that period's rates.</p>
			</li>
		</ol>
		<p class="versus-cta">Problems with registration go to NHA at abdm.incentives@nha.gov.in.</p>
	</div>
</section>

<!-- ============ WITH HEALTHRAY ============ -->
<section aria-labelledby="healthray-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">With Healthray</p>
			<h2 id="healthray-title">Create DHIS-eligible records without extra work</h2>
			<p>DHIS pays for records your staff already create. The work is making sure each one is linked to the right ABHA.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2.2"/><path d="M6 16c.6-1.5 1.7-2.2 3-2.2s2.4.7 3 2.2M14.5 10h4M14.5 13.5h3"/>' ); ?></span>
				<h3>ABHA at registration</h3>
				<p>Create or verify the patient's ABHA at the front desk, so the records that follow can be linked to it.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>' ); ?></span>
				<h3>Records linked after every visit</h3>
				<p>Prescriptions, lab reports and discharge summaries are linked to the patient's ABHA inside the OPD and IPD workflow, with no separate portal.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>' ); ?></span>
				<h3>OPD, IPD, lab and pharmacy on one system</h3>
				<p>Category 1 records, discharge summaries and diagnostic reports, come from IPD and the lab. With both on Healthray, both count.</p>
			</div>
		</div>
		<p class="versus-cta">PM-JAY claims linked to ABHA earn their own DHIS incentive. Healthray supports PM-JAY and government scheme workflows inside billing and claims; read more in our <a href="<?php echo esc_url( home_url( '/pmjay/' ) ); ?>">PM-JAY guide</a> and on the <a href="<?php echo esc_url( home_url( '/hospital-information-management-system/' ) ); ?>">HIMS software</a> page.</p>
	</div>
</section>

<!-- ============ RELATED ============ -->
<section class="define" aria-labelledby="related-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Keep reading</p>
			<h2 id="related-title">More on India's digital health schemes</h2>
		</div>
		<div class="grid-3 related-grid">
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">
				<h3>ABDM integration</h3>
				<p>What ABDM integration means for a hospital: registries, consent and the M1, M2 and M3 milestones.</p>
				<span class="more">Read about ABDM</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">
				<h3>ABHA card</h3>
				<p>What the Ayushman Bharat Health Account is, and how patients create one in a few steps.</p>
				<span class="more">Read about ABHA</span>
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
<?php echo do_shortcode( '[hr_clients eyebrow="Trusted across India" title="Hospitals linking records to ABHA with Healthray"]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_dhis_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">DHIS FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_dhis_faq as $hr_i => $hr_faq ) : ?>
				<details name="dhis-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Turn every visit into an ABHA-linked record</h2>
					<p>A 30-minute demo on your own workflow: ABHA at registration, record linking after each visit and consent-based sharing, so you see exactly what changes for your staff.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free ABDM demo</h2>
					<p>See ABHA record linking live. Our team replies within one business day.</p>
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
