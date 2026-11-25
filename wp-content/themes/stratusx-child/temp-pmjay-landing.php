<?php
/**
 * Template Name: PMJAY Landing
 * Template Post Type: page
 *
 * Page:       /pmjay/ (page 26475) - replaces the Elementor layout.
 * Body class: landing-page pmjay-landing (functions.php body_class map)
 * CSS:        css/common-landing.css + css/pmjay-software.css (inlined in wp_head)
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        healthray_pmjay_faq_items() in functions.php (page accordion + FAQPage schema)
 * Facts:      cover, launch date, family counts and the 70+ rule from MoHFW/NHA/PIB releases
 *             (checked 25 Nov 2026); eligibility lists are the SECC 2011 criteria.
 */
$hr_media     = 'https://healthray.com/wp-content/uploads';
$hr_pmjay_faq = function_exists( 'healthray_pmjay_faq_items' ) ? healthray_pmjay_faq_items() : array();
$hr_check     = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon      = static function ( $paths ) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">PM-JAY</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Ayushman Bharat · ₹5 lakh cover · Cashless</p>
			<h1 id="hero-title">PMJAY (Ayushman Bharat Yojana): ₹5 lakh cashless health cover,
				<span class="pulse-word">explained
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">Ayushman Bharat Pradhan Mantri Jan Arogya Yojana (PM-JAY) gives eligible families free, cashless hospital treatment worth up to ₹5 lakh a year at government and empanelled private hospitals across India. Every citizen aged 70 or above is covered too, whatever their income.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> ₹5 lakh per family per year for secondary and tertiary hospital care</li>
				<li><?php echo $hr_check; ?> No limit on family size or age, and pre-existing illnesses covered from day one</li>
				<li><?php echo $hr_check; ?> Tests and medicines covered for 3 days before admission and 15 days after discharge</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free hospital software demo</h2>
			<p>For empanelled hospitals: see PM-JAY billing and claims in Healthray. Our team replies within one business day.</p>
			<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
		</div>
	</div>
</section>

<!-- ============ AT A GLANCE ============ -->
<section aria-labelledby="glance-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">At a glance</p>
			<h2 id="glance-title">PMJAY scheme details</h2>
		</div>
		<table class="versus-table glance-table">
			<caption class="hp">Key facts about Ayushman Bharat Pradhan Mantri Jan Arogya Yojana</caption>
			<tbody>
				<tr><td>Full name</td><td>Ayushman Bharat Pradhan Mantri Jan Arogya Yojana (AB PM-JAY)</td></tr>
				<tr><td>Launched</td><td>23 September 2018</td></tr>
				<tr><td>Run by</td><td>National Health Authority (NHA), with a State Health Agency in each state</td></tr>
				<tr><td>Cover</td><td>Up to ₹5 lakh per family per year, for secondary and tertiary hospital care</td></tr>
				<tr><td>Who is covered</td><td>About 12.37 crore families (about 55 crore people) in the poorest 40% of the population, and every citizen aged 70 or above</td></tr>
				<tr><td>Cost to the patient</td><td>Nothing: no premium, and covered treatment is cashless at empanelled hospitals</td></tr>
				<tr><td>Card</td><td>Ayushman card, from beneficiary.nha.gov.in, the Ayushman app or an empanelled hospital's help desk</td></tr>
				<tr><td>Helpline</td><td>14555</td></tr>
			</tbody>
		</table>
	</div>
</section>

<!-- ============ WHAT IS PMJAY ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is PMJAY?</h2>
			<p style="margin-top:14px">PM-JAY is the Government of India's health assurance scheme for families who could not otherwise afford hospital care. Prime Minister Narendra Modi launched it on 23 September 2018. The scheme pays the hospital directly: an eligible patient shows their Ayushman card at an empanelled hospital and pays nothing for the covered treatment.</p>
			<p>It is one of the two parts of Ayushman Bharat. The other is Ayushman Arogya Mandir, the network of primary health centres earlier called Health and Wellness Centres. PM-JAY pays for hospital admissions; Ayushman Arogya Mandir provides primary care close to home.</p>
			<p>The Centre and the states share the cost. The National Health Authority runs the scheme nationally and each State Health Agency runs it locally, which is why some states cover more families or add benefits of their own.</p>
		</div>
		<img class="define-img" src="<?php echo esc_url( $hr_media . '/2024/02/Ayushman-Bharath-Yojana-2.webp' ); ?>" alt="Doctor with a phone, for Ayushman Bharat Pradhan Mantri Jan Arogya Yojana" width="477" height="625" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ ELIGIBILITY ============ -->
<section aria-labelledby="eligibility-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Eligibility</p>
			<h2 id="eligibility-title">Who is eligible for PMJAY?</h2>
			<p>Families qualify through the deprivation and occupation criteria of the Socio-Economic Caste Census (SECC) 2011, families active under the earlier RSBY scheme, and families added by their state.</p>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M3 21h18M5 21V10l7-6 7 6v11M10 21v-6h4v6"/>' ); ?> In rural areas</h3>
				<ul>
					<li><?php echo $hr_check; ?> Households with only one room, with kutcha walls and roof</li>
					<li><?php echo $hr_check; ?> No adult member aged 16 to 59</li>
					<li><?php echo $hr_check; ?> Female-headed households with no adult male member aged 16 to 59</li>
					<li><?php echo $hr_check; ?> A disabled member and no able-bodied adult</li>
					<li><?php echo $hr_check; ?> SC and ST households</li>
					<li><?php echo $hr_check; ?> Landless households that earn mainly from manual casual labour</li>
					<li><?php echo $hr_check; ?> Included automatically: households without shelter, destitute families, manual scavenger families, primitive tribal groups and released bonded labourers</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><?php echo $hr_icon( '<path d="M3 21h18M5 21V8l6-4v17M11 21V10l8 3v8M14 14h2M14 17h2M7 11h1M7 14h1M7 17h1"/>' ); ?> In urban areas: 11 occupation groups</h3>
				<ul>
					<li><?php echo $hr_check; ?> Ragpickers and beggars</li>
					<li><?php echo $hr_check; ?> Domestic workers</li>
					<li><?php echo $hr_check; ?> Street vendors, cobblers and hawkers</li>
					<li><?php echo $hr_check; ?> Construction workers, plumbers, masons, painters, welders, security guards and porters</li>
					<li><?php echo $hr_check; ?> Sweepers, sanitation workers and gardeners</li>
					<li><?php echo $hr_check; ?> Home-based workers, artisans, handicraft workers and tailors</li>
					<li><?php echo $hr_check; ?> Transport workers: drivers, conductors, helpers, cart and rickshaw pullers</li>
					<li><?php echo $hr_check; ?> Shop workers, assistants, peons, delivery staff, attendants and waiters</li>
					<li><?php echo $hr_check; ?> Electricians, mechanics, assemblers and repair workers</li>
					<li><?php echo $hr_check; ?> Washermen and watchmen</li>
				</ul>
			</div>
		</div>
		<div class="note-card reveal">
			<h3>Aged 70 or above? You are covered, whatever your income</h3>
			<p>Since October 2024, every citizen aged 70 or above can get cover of up to ₹5 lakh a year through the Ayushman Vay Vandana card. If your family is already covered under PM-JAY, you get a separate top-up of up to ₹5 lakh a year for yourself, which you don't share with family members under 70.</p>
		</div>
	</div>
</section>

<!-- ============ BENEFITS ============ -->
<section class="define" aria-labelledby="benefits-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Benefits</p>
			<h2 id="benefits-title">What PMJAY covers</h2>
			<p>Covered treatment is cashless at the hospital. Any member of an eligible family can use the ₹5 lakh, and the cover includes:</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3"/><path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4"/><circle cx="20" cy="10" r="2"/>' ); ?></span>
				<h3>Consultation and treatment</h3>
				<p>Medical examination, consultation and treatment during the admission.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M10.5 20.5 3.5 13.5a5 5 0 0 1 7-7l7 7a5 5 0 0 1-7 7zM8.5 8.5l7 7"/>' ); ?></span>
				<h3>Medicines and tests</h3>
				<p>Medicines, medical consumables, and diagnostic and laboratory tests.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M22 12h-4l-3 9L9 3l-3 9H2"/>' ); ?></span>
				<h3>Surgery, ICU and implants</h3>
				<p>Intensive and non-intensive care, and implants where the treatment needs them.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M2 4v16M2 8h18a2 2 0 0 1 2 2v10M2 17h20M6 8v9"/>' ); ?></span>
				<h3>Hospital stay and food</h3>
				<p>A bed and food during your stay in hospital.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/>' ); ?></span>
				<h3>Before and after admission</h3>
				<p>Tests and medicines for up to 3 days before admission and up to 15 days after discharge.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M12 9v4M12 17h.01M10.3 3.9 1.8 18a2 2 0 0 0 1.7 3h17a2 2 0 0 0 1.7-3L13.7 3.9a2 2 0 0 0-3.4 0z"/>' ); ?></span>
				<h3>Complications</h3>
				<p>Treatment of complications that arise during the hospital stay.</p>
			</div>
		</div>
		<p class="versus-cta">Examples of major treatments covered: prostate cancer treatment, coronary artery bypass grafting, double valve replacement, carotid angioplasty with stent, pulmonary valve replacement, skull base surgery, anterior spine fixation, laryngopharyngectomy with gastric pull-up, and tissue expanders for disfigurement after burns.</p>
	</div>
</section>

<!-- ============ HOW TO CHECK AND USE ============ -->
<section aria-labelledby="steps-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Step by step</p>
			<h2 id="steps-title">How to check PMJAY eligibility and get treatment</h2>
			<p>You can check, get the card and find a hospital online, then use the card at any empanelled hospital.</p>
		</div>
		<ol class="num-steps">
			<li class="reveal">
				<h3>Check if you are eligible</h3>
				<p>Search at <a href="https://beneficiary.nha.gov.in/" target="_blank" rel="noopener">beneficiary.nha.gov.in</a> or in the Ayushman app, or call the PM-JAY helpline on 14555.</p>
			</li>
			<li class="reveal">
				<h3>Get your Ayushman card</h3>
				<p>Complete eKYC with your Aadhaar and download the card. Arogya Mitras at empanelled hospitals and Common Service Centres can also make it.</p>
			</li>
			<li class="reveal">
				<h3>Find an empanelled hospital</h3>
				<p>Search by state, district, hospital type and speciality at <a href="https://hospitals.pmjay.gov.in/" target="_blank" rel="noopener">hospitals.pmjay.gov.in</a>.</p>
			</li>
			<li class="reveal">
				<h3>Get cashless treatment</h3>
				<p>Show your Ayushman card at the hospital's Ayushman Bharat help desk. Covered treatment is free at any empanelled hospital in India, in any state.</p>
			</li>
		</ol>
	</div>
</section>

<!-- ============ AYUSHMAN CARD VS ABHA ============ -->
<section class="define" aria-labelledby="vs-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Often confused</p>
			<h2 id="vs-title">Ayushman card vs ABHA card</h2>
			<p>Both carry the Ayushman Bharat name, but only the Ayushman card gives you PM-JAY cover.</p>
		</div>
		<div class="table-scroll">
			<table class="versus-table">
				<caption class="hp">Difference between the Ayushman card and the ABHA card</caption>
				<thead>
					<tr><th scope="col">&nbsp;</th><th scope="col">Ayushman card (PM-JAY)</th><th scope="col">ABHA card</th></tr>
				</thead>
				<tbody>
					<tr><td>What it is</td><td>The card that shows you are covered under PM-JAY</td><td>A health ID that links your medical records</td></tr>
					<tr><td>Scheme</td><td>Ayushman Bharat Pradhan Mantri Jan Arogya Yojana (PM-JAY)</td><td>Ayushman Bharat Digital Mission (ABDM)</td></tr>
					<tr><td>Who can get it</td><td>Eligible families, and every citizen aged 70 or above</td><td>Any resident of India</td></tr>
					<tr><td>What it gives you</td><td>Cashless hospital treatment of up to ₹5 lakh per family per year</td><td>Digital access to your records, shared with your consent</td></tr>
					<tr><td>Where to get it</td><td>beneficiary.nha.gov.in, the Ayushman app or an empanelled hospital's help desk</td><td>abha.abdm.gov.in, the ABHA app or Healthray's ABHA page</td></tr>
				</tbody>
			</table>
		</div>
		<p class="versus-cta">Creating an ABHA is free for every resident, but it does not give you PM-JAY cover. <a href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">Create your ABHA card online</a>.</p>
	</div>
</section>

<!-- ============ FOR HOSPITALS ============ -->
<section aria-labelledby="hospitals-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">For empanelled hospitals</p>
			<h2 id="hospitals-title">Running PM-JAY in your hospital</h2>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 21h18M5 21V7l7-4 7 4v14M10 11h4M12 9v4M9 21v-4h6v4"/>' ); ?></span>
				<h3>Empanelment</h3>
				<p>Hospitals apply to their State Health Agency to join PM-JAY and treat patients under its benefit packages.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 15l2 2 4-4"/>' ); ?></span>
				<h3>Pre-authorisation and claims</h3>
				<p>Most planned treatments need pre-authorisation before admission, and the hospital claims the package rate after discharge.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2.2"/><path d="M6 16c.6-1.5 1.7-2.2 3-2.2s2.4.7 3 2.2M14.5 10h4M14.5 13.5h3"/>' ); ?></span>
				<h3>ABHA-linked claims</h3>
				<p>PM-JAY claims linked to ABHA and filed through NHCX earn a <a href="<?php echo esc_url( home_url( '/dhis/' ) ); ?>">DHIS incentive</a> of ₹200 or 10% of the claim, whichever is lower (April-September 2026 rates).</p>
			</div>
		</div>
		<p class="versus-cta">Healthray's hospital software supports PM-JAY and government scheme workflows inside billing and claims, with ABHA created and verified at registration. See our <a href="<?php echo esc_url( home_url( '/hospital-information-management-system/' ) ); ?>">HIMS software</a>.</p>
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
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abha/' ) ); ?>">
				<h3>ABHA card</h3>
				<p>Create your free Ayushman Bharat Health Account online and keep your records in one place.</p>
				<span class="more">Read about ABHA</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>">
				<h3>Ayushman Bharat Digital Mission</h3>
				<p>How ABDM connects hospitals, labs and patients, with records shared only on consent.</p>
				<span class="more">Read about ABDM</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/dhis/' ) ); ?>">
				<h3>Digital Health Incentive Scheme</h3>
				<p>How hospitals and labs earn incentives for ABHA-linked records and PM-JAY claims.</p>
				<span class="more">Read about DHIS</span>
			</a>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode( '[hr_stats]' ); ?>

<!-- ============ CLIENTS ============ -->
<?php echo do_shortcode( '[hr_clients eyebrow="Trusted across India" title="Hospitals running government scheme workflows on Healthray"]' ); ?>

<!-- ============ FAQ ============ -->
<?php if ( $hr_pmjay_faq ) : ?>
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">PMJAY FAQs</h2>
		</div>
		<div class="faq-list">
			<?php // Shared name = one open at a time (native + script.js fallback); first item starts open. ?>
			<?php foreach ( $hr_pmjay_faq as $hr_i => $hr_faq ) : ?>
				<details name="pmjay-faq"<?php echo 0 === $hr_i ? ' open' : ''; ?>>
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
					<h2 id="cta-title">Run PM-JAY billing and claims from one hospital system</h2>
					<p>A 30-minute demo on your own workflow: ABHA at registration, PM-JAY and government scheme billing, and records linked after every visit.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free hospital software demo</h2>
					<p>See PM-JAY billing and claims in Healthray. Our team replies within one business day.</p>
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
