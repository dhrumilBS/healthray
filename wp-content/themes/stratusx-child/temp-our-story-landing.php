<?php
/**
 * Template Name: Our Story Landing
 * Template Post Type: page
 *
 * Page:       /our-story/ (page 31213) - replaces the Elementor layout.
 * Body class: landing-page our-story-landing (functions.php body_class map)
 * CSS:        css/common-landing.css + css/our-story-software.css (inlined in wp_head)
 * JS:         none - reveal-on-scroll and the form handlers live in script.js
 * FAQ:        none on this page
 * Blocks:     [journey_timeline] (milestones from this page's ACF fields), [hr_stats],
 *             [hr_testimonials]; the old 1000+ hospitals counter now comes from [hr_stats].
 * Founder:    Ketan's Gravatar from his user account (get_avatar_url(), as author.php does),
 *             so the photo matches /author/ketan-mangukiya/. Settings > Discussion has avatars
 *             switched off, which makes get_avatar() return nothing; get_avatar_url() still works.
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_founder      = get_user_by( 'slug', 'ketan-mangukiya' );
$hr_founder_img  = $hr_founder ? get_avatar_url( $hr_founder->ID, array( 'size' => 400 ) ) : '';
$hr_founder_img2 = $hr_founder ? get_avatar_url( $hr_founder->ID, array( 'size' => 640 ) ) : '';
$hr_check = '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
$hr_icon  = static function ( $paths ) {
	return '<svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">' . $paths . '</svg>';
};
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">Our Story</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">About Healthray</p>
			<h1 id="hero-title">Built for hospitals that refuse to
				<span class="pulse-word">stand still
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">We build AI-powered hospital management software, trusted by hospitals in India and the USA and created to make better care unavoidable, regardless of scale.</p>
			<ul class="hero-points">
				<li><?php echo $hr_check; ?> Founded in 2019 in Surat, India</li>
				<li><?php echo $hr_check; ?> Serving hospitals in India and the USA</li>
				<li><?php echo $hr_check; ?> ABDM compliant · NHA approved</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free demo</h2>
			<p>See how Healthray runs a hospital, from registration to billing. Our team replies within one business day.</p>
			<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
		</div>
	</div>
</section>

<!-- ============ STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Our standards</p>
			<h2 id="compliance-title">Built on global standards your hospital management system can trust</h2>
			<p>In 2019, we saw hospitals drowning in paperwork, patients waiting far too long, and doctors carrying burdens no one should carry alone. We created a hospital management system to change that, and we have not taken a break since.</p>
		</div>
		<div class="comp-grid">
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abdm-healthray-150x150.webp' ); ?>" alt="ABDM-compliant HMS - NHA approved" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abha-healthray-150x150.webp' ); ?>" alt="ABHA integrated hospital management system" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/NHA.webp' ); ?>" alt="NHA approved hospital software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
			<a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"><img src="<?php echo esc_url( $hr_media . '/2026/07/NABH-ehr-certified-150x150.webp' ); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span></a>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/Hipaa-healthray-150x150.webp' ); ?>" alt="HIPAA-compliant hospital management software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/iso-27001-healthray-150x150.webp' ); ?>" alt="ISO 27001 certified hospital software" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/hl7-healthray-150x150.webp' ); ?>" alt="HL7-compatible hospital software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/FHIR.webp' ); ?>" alt="FHIR-compliant health data interoperability" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
		</div>
	</div>
</section>

<!-- ============ THE PROBLEM WE SAW ============ -->
<section aria-labelledby="problem-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">The problem we saw</p>
			<h2 id="problem-title">Hospital management was running on hope and spreadsheets</h2>
			<p>In India and the USA, we kept hearing the same story: brilliant doctors, hard-working nurses and well-meaning trustees held back by hospital software that was never designed to support them. Missed diagnoses. Lost records. Long queues. Revenue slipping through administrative loopholes.</p>
			<p>The struggling hospitals were not failing because of their people. They were failing because of their tools. An ineffective hospital information management system, or none at all, was costing hospitals time, money and patient trust every day.</p>
		</div>
		<div class="grid-4">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8z"/><path d="M14 3v5h5M9 13h6M9 17h4"/>' ); ?></span>
				<h3>Paper-first hospital operations</h3>
				<p>Important patient information locked away on paper, out of reach of other departments and emergencies.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/>' ); ?></span>
				<h3>Long patient wait times</h3>
				<p>Waiting for the doctor, for lab reports and for billing, because hospital software systems could not talk to each other.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 17l6-6 4 4 8-8M14 7h7v7"/>' ); ?></span>
				<h3>Unseen revenue leakage</h3>
				<p>Hospitals losing 15-25% of revenue to billing mistakes, uncharged services and manual claim reconciliation, all avoidable with the right hospital software.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M3 3h7v7H3zM14 3h7v7h-7zM3 14h7v7H3zM14 14h7v7h-7z"/>' ); ?></span>
				<h3>Disconnected departments</h3>
				<p>Lab, pharmacy, OPD and billing working in silos, causing delays, duplicate work and costly clinical errors every day.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ FOUNDER LETTER ============ -->
<section class="define" aria-labelledby="founder-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">A letter from our founder</p>
			<h2 id="founder-title">One moment in 2019 changed everything</h2>
		</div>
		<div class="founder-grid">
			<div class="founder-card reveal">
				<?php if ( $hr_founder_img ) : ?>
					<img src="<?php echo esc_url( $hr_founder_img ); ?>" srcset="<?php echo esc_url( $hr_founder_img ); ?> 400w, <?php echo esc_url( $hr_founder_img2 ); ?> 640w" sizes="(max-width: 980px) 376px, 296px" alt="Ketan Mangukiya, founder and CEO of Healthray" width="400" height="400" loading="lazy" decoding="async">
				<?php endif; ?>
				<h3>Ketan Mangukiya</h3>
				<p class="role">Founder &amp; CEO, Healthray Technologies</p>
				<p class="place">Surat, India</p>
				<div class="founder-links">
					<a class="btn btn-ghost" href="<?php echo esc_url( home_url( '/author/ketan-mangukiya/' ) ); ?>">Read Ketan's blog</a>
					<a class="btn btn-ghost" href="https://www.linkedin.com/in/ketan-mangukiya/" target="_blank" rel="noopener">Connect on LinkedIn</a>
				</div>
			</div>
			<div class="letter">
				<p>I remember the moment clearly. A doctor I knew, gifted, dedicated and truly caring, told me he spent more time on paperwork than with his patients. He was not complaining. He had accepted it as normal. That acceptance was the most troubling part.</p>
				<blockquote class="founder-quote">
					<p>"Good people doing important work should not be held back by bad systems. I knew the right hospital management software could close that gap, and I knew we had the skills to build it."</p>
					<cite>Ketan Mangukiya, Founder &amp; CEO, Healthray</cite>
				</blockquote>
				<p>In 2019, I made the decision. I had built Bigscal Technologies into a strong software company, but healthcare was asking for something more focused and more purposeful. I wanted to build an AI-powered hospital information management system that would do more than bring money to hospitals: it would save the time that saves lives.</p>
				<p>We started in Surat, a city that taught me ambition does not need a particular zip code. From day one, we chose to build for real hospitals: institutions that could not afford to wait for enterprise software, and growing healthcare providers across geographies who needed a partner, not just a vendor.</p>
				<p>Today, Healthray's cloud-based hospital management software runs in hospitals in India and the USA. We are ABDM compliant, NHA approved, HIPAA compliant and AI-powered. But what I am most proud of is simpler than any of those credentials: somewhere tonight, a doctor is spending more time with a patient because Healthray is handling the rest.</p>
				<p>That is why we exist. That is why we keep building. And in 2026, the mission is exactly where it began: unfinished, urgent, and ours.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ JOURNEY ============ -->
<section aria-labelledby="journey-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Our journey</p>
			<h2 id="journey-title">Founded in 2019. Still building, still growing.</h2>
			<p>Every milestone below is a hospital that trusted us, a problem we solved and a promise we kept. The mission continues in 2026 and beyond.</p>
		</div>
		<?php echo do_shortcode( '[journey_timeline]' ); ?>
	</div>
</section>

<!-- ============ VISION AND MISSION ============ -->
<section class="define" aria-labelledby="mission-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Vision, mission &amp; values</p>
			<h2 id="mission-title">We measure success by what happens to the patient</h2>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<p class="mv-label">Our vision</p>
				<h3>We don't just manage hospitals. We imagine what world-class hospital care can become.</h3>
				<p>From a clinic in Gujarat to a specialty center in Texas, from a district hospital in a small city to a multi-specialty network in a large one, every patient deserves care backed by great hospital management software. We see a healthcare system where geography, hospital size or budget is no obstacle to excellence.</p>
			</div>
			<div class="arch reveal">
				<p class="mv-label">Our mission</p>
				<h3>To equip all hospitals with AI-powered tools that make better care inevitable</h3>
				<p>We build AI-powered hospital management software that fits India's ABDM model and adapts to the needs of healthcare in the USA, because we will not choose one market over the other. Our mission is to bring accessible, smart, connected, cloud-based hospital information management to every institution that wants to grow.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ VALUES ============ -->
<section aria-labelledby="values-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">What we stand for</p>
			<h2 id="values-title">Four values we do not compromise on</h2>
		</div>
		<div class="grid-4">
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1-1.1a5.5 5.5 0 0 0-7.8 7.8l1 1.1L12 21l7.8-7.5 1-1.1a5.5 5.5 0 0 0 0-7.8z"/>' ); ?></span>
				<h3>Patient above all</h3>
				<p>Every feature starts with one question: will this improve the patient's experience? If the answer is no, we go back to the drawing board.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>' ); ?></span>
				<h3>Globally competent, locally grounded</h3>
				<p>We understand ABDM compliance as only an Indian healthcare team can, and US healthcare standards as a dedicated global team does. Both, without compromise.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 11l-3 3-2-2"/>' ); ?></span>
				<h3>Relied on by healthcare providers</h3>
				<p>We don't build only for hospital administrators. We build for the doctors and nurses who rely on our software under pressure at 3 a.m., when there is no room for mistakes.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><?php echo $hr_icon( '<rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>' ); ?></span>
				<h3>Data as a responsibility</h3>
				<p>Patient information is sacred. Privacy, security and compliance are built into the foundation of our system, not added later as features.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ IMPACT ============ -->
<section aria-labelledby="impact-title" style="padding-bottom:0">
	<div class="wrap">
		<div class="section-head center" style="margin-bottom:0">
			<p class="eyebrow">Our impact</p>
			<h2 id="impact-title">Every number is a hospital that chose to do better</h2>
		</div>
	</div>
</section>
<?php echo do_shortcode( '[hr_stats]' ); ?>

<!-- ============ WHO WE SERVE ============ -->
<section class="define" aria-labelledby="serve-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Who we serve</p>
			<h2 id="serve-title">Every facility that cares about patient care deserves the right hospital management system</h2>
			<p>Healthray's hospital management software is not a generic product. It was designed for the unique demands, compliance requirements and clinical processes of institutions like yours.</p>
		</div>
		<div class="grid-3 related-grid">
			<a class="card reveal" href="<?php echo esc_url( home_url( '/solutions/multispeciality-hospitals/' ) ); ?>">
				<h3>Multi-specialty hospitals</h3>
				<p>Many departments, specialties and high daily patient volumes, brought together by Healthray's hospital information management system on one real-time platform with no departmental silos.</p>
				<span class="more">Multispeciality hospitals</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/solutions/hospital-chains/' ) ); ?>">
				<h3>Clinic and hospital chains</h3>
				<p>Growing chains that need one cloud-based hospital management system at every site, with central MIS reporting, unified patient records and branch-level control.</p>
				<span class="more">Hospital chains</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/laboratory-information-management-system/' ) ); ?>">
				<h3>Diagnostic and laboratory centers</h3>
				<p>Labs that need faster report turnaround, built-in billing, NABL-ready workflows and easy sharing with referring doctors, through an integrated laboratory information management system.</p>
				<span class="more">Lab software (LIMS)</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/pharmacy-management-system/' ) ); ?>">
				<h3>Pharmacy networks</h3>
				<p>Hospital pharmacies and pharmacy chains that need inventory intelligence, e-prescription management and compliance reporting in one integrated pharmacy management system.</p>
				<span class="more">Pharmacy software</span>
			</a>
			<a class="card reveal" href="<?php echo esc_url( home_url( '/hospital-information-management-system/' ) ); ?>">
				<h3>Government and trust hospitals</h3>
				<p>Public hospitals and charitable trusts that need ABDM-compliant, NHA-approved software for high patient volumes, ABHA health IDs and PM-JAY requirements, without expensive custom integrations.</p>
				<span class="more">HIMS software</span>
			</a>
			<div class="card reveal">
				<h3>US healthcare providers</h3>
				<p>Ambulatory care centers, specialty practices and healthcare groups in the USA that need a proven, AI-powered, HIPAA-ready hospital management system built for US clinical workflows and compliance requirements.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<?php echo do_shortcode( '[hr_testimonials eyebrow="Customer stories" title="The best hospital management system for healthcare"]' ); ?>

<!-- ============ FINAL CTA with form ============ -->
<section aria-labelledby="cta-title" style="padding-top:0">
	<div class="wrap">
		<div class="cta">
			<div class="cta-grid">
				<div class="cta-copy">
					<h2 id="cta-title">See what Healthray can do for your hospital</h2>
					<p>A 30-minute demo on your own workflow, from registration and OPD to IPD, lab, pharmacy and billing.</p>
					<ul class="cta-points">
						<li><?php echo $hr_check; ?> Free demo - no credit card required</li>
						<li><?php echo $hr_check; ?> Data migration and staff training included</li>
						<li><?php echo $hr_check; ?> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free demo</h2>
					<p>Tell us about your hospital. Our team replies within one business day.</p>
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
