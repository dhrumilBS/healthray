<?php
/**
 * Template Name: Pharmacy Software Landing
 * Template Post Type: page
 *
 * Page ID: 79928 | body class: pharmacy-landing | CSS file: pharmacy-software.css
 *
 * No get_header()/get_footer() - matches every other template on this
 * site. Real nav/footer render independently via Elementor Theme Builder.
 *
 * CSS inlining, schema, and body_class additions for this page are
 * centralized in functions.php (is_page(79928) blocks) - see
 * functions-pharmacy-additions.php.
 *
 * FAQ content is inlined directly in this file (below) AND separately
 * inlined again in functions.php's schema block, per explicit
 * instruction - NOT pulled from a shared helper function. If the FAQ
 * copy is ever edited, both places need updating by hand.
 *
 * All visible text below is unchanged from the approved HTML mockup.
 *
 * ⚠️ ACTION NEEDED BEFORE GOING LIVE
 * - Verify aggregateRating ratingCount (180) against live
 *   Capterra/SoftwareSuggest before launch.
 * - Verify each compliance certification (esp. ISO 27001) is current.
 * - The compliance section's fixed 8-column layout intentionally
 *   overrides common-landing.css's auto-fit version for this page -
 *   confirm that's the intended design, not accidental drift.
 */

$hr_media = 'https://healthray.com/wp-content/uploads';
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">Pharmacy Management System</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">Medical stores · Chains · Hospital pharmacies · NABH certified</p>
			<h1 id="hero-title">AI-powered pharmacy software that actually
				<span class="pulse-word">saves you time
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">One pharmacy management system for the whole counter - GST billing, expiry-safe inventory, Schedule H/H1 registers, e-prescriptions, delivery and accounting - for stores, chains and hospital pharmacies.</p>
			<ul class="hero-points">
				<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Start billing the same day - Indian brand product masters come preloaded</li>
				<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> FEFO expiry alerts and batch tracking that cut expired-stock losses</li>
				<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Digital Schedule H/H1/X registers - drug-inspector ready without paper</li>
			</ul>
			<p class="hero-proof"><strong>2,000+ pharmacies</strong> <span class="dot"></span> <strong>4M+ prescriptions dispensed</strong> <span class="dot"></span> <strong>5+ countries</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free pharmacy demo</h2>
			<p>See a prescription go from counter to compliant bill. Our team replies within one business day. <strong>10% off premium plans</strong> is currently available.</p>
			<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
		</div>
	</div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Compliance built in</p>
			<h2 id="compliance-title">One Pharmacy Platform, Every Compliance Standard</h2>
			<p>Healthray is <strong>NABH-certified healthcare software</strong> - listed on the official NABH portal - and ABDM compliant by design. Built for Indian pharmacy regulation - <strong>GST invoicing, Schedule H/H1/X registers under the Drugs &amp; Cosmetics Rules</strong> - with HIPAA-aligned security for pharmacies serving international markets. Compliance lives at the platform level, so it's never your counter's extra work.</p>
		</div>
		<!-- NOTE: verify each certification (esp. ISO 27001) is current before deploy. -->
		<div class="comp-grid">
			<a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal">
				<img src="<?php echo esc_url( $hr_media . '/2026/07/NABH-ehr-certified-150x150.webp' ); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span>
			</a>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/Hipaa-healthray-150x150.webp' ); ?>" alt="HIPAA compliant pharmacy software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abdm-healthray-150x150.webp' ); ?>" alt="ABDM compliant pharmacy management system - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abha-healthray-150x150.webp' ); ?>" alt="ABHA integrated pharmacy software" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/NHA.webp' ); ?>" alt="NHA approved pharmacy management software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/iso-27001-healthray-150x150.webp' ); ?>" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/hl7-healthray-150x150.webp' ); ?>" alt="HL7 interoperable pharmacy software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/FHIR.webp' ); ?>" alt="FHIR compliant pharmacy data interoperability" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
		</div>
	</div>
</section>

<!-- ============ WHAT IS PHARMACY SOFTWARE (+ medical store absorption) ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is pharmacy software?</h2>
			<p style="margin-top:14px">Pharmacy software runs a pharmacy's entire day on one platform: prescription intake, GST billing at the counter, inventory with batch and expiry tracking, purchase orders to wholesalers, regulatory registers, and end-of-day accounting. Instead of manual bill books, stock registers and a separate accounting tool, every strip that enters or leaves the store is traceable - with faster billing, fewer expired losses and inspection-ready records.</p>
			<p>You'll hear the same category called a <strong>pharmacy management system</strong>, <strong>medical store software</strong> or <strong>chemist shop software</strong> - they all describe this product. What separates Healthray is scope: the same platform runs a single neighbourhood store, a multi-store chain, and a hospital's in-house pharmacy with ward supply - so you never migrate as you grow.</p>
			<p>And because Healthray is one healthcare platform, the pharmacy connects natively to doctors' <a href="<?php echo esc_url( home_url( '/emr-software/' ) ); ?>" style="color:var(--brand);font-weight:600">EMR software</a> - e-prescriptions arrive at the counter with no re-typing, and dispensing posts back to the patient's record.</p>
		</div>
		<img src="<?php echo esc_url( $hr_media . '/2026/07/pharmacy-software.svg' ); ?>" alt="Healthray pharmacy software showing GST billing, stock with expiry alerts and prescriptions on one dashboard" width="690" height="500" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ WHO IT SERVES (6 pharmacy types, India-tuned) ============ -->
<section aria-labelledby="serves-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Built for your kind of pharmacy</p>
			<h2 id="serves-title">Who this pharmacy management software serves</h2>
			<p>From a single-counter store to a manufacturing unit - the platform adapts to how your pharmacy actually runs.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9l1.5-5h15L21 9M3 9v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V9M3 9h18M9 21v-6h6v6"/></svg></span>
				<h3>Medical stores &amp; community pharmacies</h3>
				<p>Neighbourhood chemist shops with one to five counters - fast billing, personal patient relationships and stock that never surprises you.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M12 7v4M10 9h4M9 21v-4h6v4"/></svg></span>
				<h3>Hospital pharmacies</h3>
				<p>In-house pharmacies handling ward indents, unit-dose supply and charges that post straight to patient bills - inpatient and OPD together.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.7 6.9 9.2a6.5 6.5 0 1 0 10.2 0zM12 12v4M10 14h4"/></svg></span>
				<h3>Specialty &amp; cold chain pharmacies</h3>
				<p>High-value medications needing temperature-controlled storage, batch-level traceability and careful patient support programs.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M6 21V8l6-5 6 5v13M10 21v-5h4v5M9 11h.01M15 11h.01"/></svg></span>
				<h3>Retail chains &amp; franchises</h3>
				<p>Multi-store networks running standardized pricing, centralized product masters and inter-store stock transfers from one dashboard.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="2" y="7" width="14" height="10" rx="1.5"/><path d="M16 10h3.5L22 13v4h-6M5.5 20a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6zM18 20a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6z"/></svg></span>
				<h3>Online &amp; delivery pharmacies</h3>
				<p>Order-to-doorstep operations with route optimization, live tracking, cold-chain monitoring and proof of delivery.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 20h20M4 20V9l4 3V9l4 3V9l4 3V5h4v15M17 9h.01"/></svg></span>
				<h3>Manufacturers &amp; compounding units</h3>
				<p>Batch records, SOP control, CAPA tracking and audit-ready quality documentation for units producing at scale.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ FEATURES (12 consolidated modules, India-anchored) ============ -->
<section class="define" id="features" aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">One platform, the whole counter</p>
			<h2 id="features-title">Pharmacy software features for every counter in your store</h2>
			<p>Every module shares one stock and one patient record - so nothing is typed twice, and nothing expires unnoticed on a back shelf.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/><circle cx="12" cy="12" r="3.5"/></svg></span>
				<h3>AI-powered automation</h3>
				<p>OCR that reads handwritten prescriptions, demand forecasting from seasonal patterns, smart auto-ordering at reorder levels, and drug-interaction alerts before dispensing.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M2 7h5M2 12h5M2 17h5M17 7h5M17 12h5M17 17h5"/></svg></span>
				<h3>GST billing &amp; POS</h3>
				<p>GST-compliant invoices for retail and credit sales, UPI/card/cash payments, batch and expiry checks at the point of sale, and e-way bill documentation when you need it.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 20.5 3.5 13.5a4.95 4.95 0 1 1 7-7l7 7a4.95 4.95 0 1 1-7 7zM8.5 8.5l7 7"/></svg></span>
				<h3>Inventory with expiry protection</h3>
				<p>Live stock across counters and godown, FEFO (first-expired, first-out) alerts that cut expired losses, and full batch traceability for recalls.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 3h5v5M21 3l-7 7M8 21H3v-5M3 21l7-7M21 16v5h-5M14 14l7 7M3 8V3h5M10 10 3 3"/></svg></span>
				<h3>Purchases &amp; wholesaler management</h3>
				<p>Auto-generated purchase orders at min/max levels, rate comparison across distributors, scheme and margin tracking, and barcode-verified goods receiving.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></span>
				<h3>e-Prescriptions &amp; doctor integration</h3>
				<p>Prescriptions arrive digitally from Healthray clinics and hospitals - no re-typing, allergy and duplicate-therapy checks run automatically, and dispensing posts back to the patient record.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M9 13h6M9 17h6M9 9h1"/></svg></span>
				<h3>Schedule H/H1/X registers</h3>
				<p>Restricted-drug sales recorded with prescription, doctor and patient details as the Drugs &amp; Cosmetics Rules require - digital registers with audit trails, ready for inspector visits.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 10h.01M15 10h.01"/></svg></span>
				<h3>Hospital pharmacy &amp; ward supply</h3>
				<p>Ward indents and unit-dose dispensing, bedside barcode verification, charges posting to patient bills, and automated replenishment of nursing-station stock.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V8l6-5 6 5v13M10 21v-5h4v5"/></svg></span>
				<h3>Multi-store management</h3>
				<p>Centralized product master and pricing, inter-store transfers to balance demand, unified patient history across branches, and consolidated reporting for owners.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="14" height="10" rx="1.5"/><path d="M16 10h3.5L22 13v4h-6M5.5 20a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6zM18 20a1.8 1.8 0 1 0 0-3.6 1.8 1.8 0 0 0 0 3.6z"/></svg></span>
				<h3>Delivery &amp; cold chain</h3>
				<p>Route optimization with live GPS tracking, temperature monitoring for vaccines and biologics, proof of delivery, and automated customer notifications at every step.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM20 8v6M23 11h-6"/></svg></span>
				<h3>Patient engagement &amp; loyalty</h3>
				<p>Refill reminders by SMS and WhatsApp, loyalty points on purchases, a patient portal for orders and payments, and adherence programs for chronic-care customers.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg></span>
				<h3>Accounting &amp; reports</h3>
				<p>Stock-to-ledger sync in real time, supplier dues and customer credit tracking, daily P&amp;L with margin analysis, and GSTR-ready tax reports.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
				<h3>Access control &amp; audit</h3>
				<p>Role-based permissions for owners, pharmacists and counter staff, multi-factor login, and tamper-proof logs of every bill, edit and stock adjustment.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ RETAIL vs HOSPITAL PHARMACY (boundary section) ============ -->
<section id="retail-vs-hospital" aria-labelledby="versus-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Choosing the right fit</p>
			<h2 id="versus-title">Retail pharmacy or hospital pharmacy: which setup do you need?</h2>
			<p>The same platform runs both - but the daily flow differs, and knowing which you are buying for saves you from paying for the wrong shape.</p>
		</div>
		<table class="versus-table">
			<caption class="hp">Comparison of retail pharmacy and hospital pharmacy setups</caption>
			<thead>
				<tr><th scope="col"></th><th scope="col">Retail Pharmacy / Medical Store</th><th scope="col">Hospital Pharmacy</th></tr>
			</thead>
			<tbody>
				<tr><td>Orders come from</td><td>Walk-in customers, prescriptions in hand, refills and online orders</td><td>Doctors' e-prescriptions and ward indents inside the hospital system</td></tr>
				<tr><td>Billing</td><td>GST retail invoices, UPI/card/cash at the counter, credit customers</td><td>Charges post to the patient's hospital bill - OPD, IPD and insurance</td></tr>
				<tr><td>Stock flow</td><td>Wholesaler purchases → counter sales, expiry-safe rotation</td><td>Central store → ward and nursing-station stock, unit-dose supply</td></tr>
				<tr><td>Runs best with</td><td>Healthray pharmacy software standalone - billing the same day</td><td>Pharmacy integrated with Healthray's hospital and clinic platform</td></tr>
			</tbody>
		</table>
		<p class="versus-cta">Healthray covers both on one platform - and the rest of the organization too. Running a hospital? See our complete <a href="<?php echo esc_url( home_url( '/' ) ); ?>">hospital management system</a>. Running a clinic with a dispensing counter? Start from our <a href="<?php echo esc_url( home_url( '/clinic-management-software/' ) ); ?>">clinic management software</a>. And if your setup includes diagnostics, the same platform runs our <a href="<?php echo esc_url( home_url( '/laboratory-information-management-system/' ) ); ?>">LIMS software</a> for the lab.</p>
	</div>
</section>

<!-- ============ OUTCOMES BAND (attributed) ============ -->
<section class="outcomes" aria-labelledby="outcomes-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Measured, not promised</p>
			<h2 id="outcomes-title">What pharmacies report after switching to Healthray</h2>
			<p>Typical outcomes our partner pharmacies share after their first months on the platform.</p>
		</div>
		<div class="outcomes-grid">
			<div class="outcome reveal"><b class="down">↓45%</b><span>Manual work at the counter</span></div>
			<div class="outcome reveal"><b class="down">↓30%</b><span>Prescription waiting time</span></div>
			<div class="outcome reveal"><b>↑40%</b><span>Billing and processing speed</span></div>
			<div class="outcome reveal"><b>↑25%</b><span>Revenue captured, fewer missed charges</span></div>
			<div class="outcome reveal"><b class="down">↓20%</b><span>Expired stock losses</span></div>
			<div class="outcome reveal"><b class="down">↓35%</b><span>Claim and billing errors</span></div>
		</div>
		<p class="outcomes-note">Figures reflect ranges reported by Healthray partner pharmacies; individual results vary by store size and workflow. <a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>" style="color:#8FB2F5;font-weight:600">Read the case studies →</a></p>
	</div>
</section>

<!-- ============ ARCHITECTURE, SECURITY & SUPPORT ============ -->
<section class="define" aria-labelledby="arch-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Under the hood</p>
			<h2 id="arch-title">Architecture, integrations, security &amp; support</h2>
			<p>Enterprise-grade pharmacy management software built for secure deployment, Indian regulatory compliance and high prescription volumes.</p>
		</div>
		<div class="arch-grid">
			<div class="arch reveal">
				<h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19a4.5 4.5 0 1 0-.42-8.98 6 6 0 1 0-11.5 2.2A3.5 3.5 0 0 0 6.5 19z"/></svg> Deployment &amp; architecture</h3>
				<ul>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Cloud, on-premise and hybrid deployment options</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Scales from a single store to multi-branch chains</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> High availability with automated backups and recovery</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Performance-optimized for high prescription volumes</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3M7 12h10M12 7v10"/></svg> Interoperability &amp; integrations</h3>
				<ul>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> ABDM-compliant integration with ABHA linking</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> HL7 and FHIR healthcare data exchange</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> EMR/EHR, hospital and LIMS system integration</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Dispensing robots, smart cabinets and barcode hardware</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Data security &amp; regulatory</h3>
				<ul>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Encryption in transit and at rest, HIPAA-aligned controls</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Schedule H/H1/X and narcotics register compliance support</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Role-based access with multi-factor authentication</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Tamper-proof audit trails for bills, stock and registers</li>
				</ul>
			</div>
			<div class="arch reveal">
				<h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg> Implementation &amp; support</h3>
				<ul>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Start billing the same day with preloaded product masters</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Stock import from Excel or legacy pharmacy software</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Counter-workflow training for owners and staff</li>
					<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> 24/7 technical support in English, Hindi and Gujarati</li>
				</ul>
			</div>
		</div>
	</div>
</section>

<!-- ============ CLIENTS (10, single instance, PHARMACY-weighted) ============ -->
<section aria-labelledby="clients-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Trusted across India</p>
			<h2 id="clients-title">Pharmacies that run on Healthray</h2>
			<p>From single-counter stores to hospital pharmacies - 2,000+ pharmacies bill their daily prescriptions on our platform.</p>
		</div>
		<?php
		$hr_clients = array(
			array( 'Universal Pharmacy', 'Surat, Gujarat', $hr_media . '/2024/02/Universal-Pharmacy-Healthray.webp' ),
			array( 'Beladiya Pharmacy', 'Surat, Gujarat', $hr_media . '/2024/02/Beladiya-Pharmacy-Healthray.webp' ),
			array( 'Raaz Pharmacy', 'Gujarat', $hr_media . '/2024/02/Raaz-Pharmacy-Healthray.webp' ),
			array( 'Satva Pharmacy', 'Gujarat', $hr_media . '/2024/02/Satva-Pharmacy-Healthray.webp' ),
			array( 'Suhani Pharmacy', 'Gujarat', $hr_media . '/2024/02/Suhani-Pharmacy-Healthray.webp' ),
			array( 'Om Medical', 'Gujarat', $hr_media . '/2024/02/Om-Pharmacy-Healthray.webp' ),
			array( 'Naritva Medical Store', 'Gujarat', $hr_media . '/2024/02/Naritva-Pharmacy-Healthray.webp' ),
			array( 'Radient Pharmacy', 'Gujarat', $hr_media . '/2024/02/Radient-Pharmacy-Healthray.webp' ),
			array( 'MediCeylon Pharmacy', 'Welipenna, Sri Lanka', $hr_media . '/2025/04/MediCeylon-Pharmacy.webp' ),
			array( 'Shushrusha Ayurvedic &amp; Panchkarma', 'Maharashtra', $hr_media . '/2024/02/shushrusha-Pharmacy-Healthray.webp' ),
		);
		?>
		<div class="clients-grid">
			<?php foreach ( $hr_clients as $hr_client ) : ?>
				<div class="client reveal">
					<img src="<?php echo esc_url( $hr_client[2] ); ?>" alt="<?php echo esc_attr( wp_strip_all_tags( $hr_client[0] ) . ' logo' ); ?>" loading="lazy" decoding="async" width="120" height="54">
					<b><?php echo $hr_client[0]; /* contains &amp; already escaped */ ?></b>
					<span><?php echo esc_html( $hr_client[1] ); ?></span>
				</div>
			<?php endforeach; ?>
		</div>
		<p class="clients-note">…and 1,990+ more pharmacies across India and beyond. <a href="<?php echo esc_url( home_url( '/case-studies/' ) ); ?>">Read their case studies →</a></p>
	</div>
</section>

<!-- ============ TESTIMONIALS (9, carousel: 3 per view on desktop) ============ -->
<section class="define" aria-labelledby="testi-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Pharmacy stories</p>
			<h2 id="testi-title">What pharmacies say about Healthray</h2>
		</div>
		<div class="t-carousel">
			<div class="t-track" id="tTrack" aria-label="Pharmacy testimonials" tabindex="0">
			<?php
			$hr_testimonials = array(
				array( 'We appreciate how adaptable Healthray\'s Pharmacy Management System is. It was easy to configure it according to our pharmacy\'s requirements.', 'Universal Pharmacy', 'Surat, Gujarat', $hr_media . '/2024/02/Universal-Pharmacy-Healthray.webp' ),
				array( 'I am very satisfied with Healthray\'s pharmacy software. It is a user-friendly and secure system that simplifies pharmacy operations and enhances quality control.', 'Beladiya Pharmacy', 'Surat, Gujarat', $hr_media . '/2024/02/Beladiya-Pharmacy-Healthray.webp' ),
				array( 'Since using Healthray\'s Pharmacy Management System, our pharmacy has experienced a noticeable increase in operational efficiency.', 'Raaz Pharmacy', 'Gujarat', $hr_media . '/2024/02/Raaz-Pharmacy-Healthray.webp' ),
				array( 'I appreciate how responsive the development team behind Healthray\'s Pharmacy Management System is to user feedback and suggestions.', 'Sonani Dental Pharmacy', 'Gujarat', $hr_media . '/2024/02/Sonani-Denrtal-Pharmacy-Healthray.webp' ),
				array( 'The reporting features in Healthray\'s Pharmacy Management System have been invaluable for tracking sales and inventory trends.', 'Naritva Medical Store', 'Gujarat', $hr_media . '/2024/02/Naritva-Pharmacy-Healthray.webp' ),
				array( 'The automated reminders in Healthray\'s Pharmacy Management System help ensure that patients never miss a refill.', 'Om Medical', 'Gujarat', $hr_media . '/2024/02/Om-Pharmacy-Healthray.webp' ),
				array( 'I highly recommend Healthray\'s Pharmacy Management System to any pharmacy looking to modernize and streamline their operations.', 'Radient Pharmacy', 'Gujarat', $hr_media . '/2024/02/Radient-Pharmacy-Healthray.webp' ),
				array( 'We\'ve been able to reduce medication errors significantly since switching to Healthray\'s Pharmacy Management System.', 'Satva Pharmacy', 'Gujarat', $hr_media . '/2024/02/Satva-Pharmacy-Healthray.webp' ),
				array( 'The customer service provided by Healthray\'s Pharmacy Management System team sets a high standard for excellence.', 'Suhani Pharmacy', 'Gujarat', $hr_media . '/2024/02/Suhani-Pharmacy-Healthray.webp' ),
			);
			foreach ( $hr_testimonials as $hr_t ) :
				?>
				<article class="t-card">
					<blockquote><?php echo esc_html( $hr_t[0] ); ?></blockquote>
					<div class="t-who">
						<img src="<?php echo esc_url( $hr_t[3] ); ?>" alt="<?php echo esc_attr( $hr_t[1] ); ?>" loading="lazy" decoding="async" width="48" height="48">
						<div><b><?php echo esc_html( $hr_t[1] ); ?></b><span><?php echo esc_html( $hr_t[2] ); ?></span></div>
					</div>
				</article>
			<?php endforeach; ?>
			</div>
			<div class="t-nav">
				<button type="button" class="t-btn" id="tPrev" aria-label="Previous testimonials"><svg viewBox="0 0 24 24"><path d="M15 18l-6-6 6-6"/></svg></button>
				<button type="button" class="t-btn" id="tNext" aria-label="Next testimonials"><svg viewBox="0 0 24 24"><path d="M9 6l6 6-6 6"/></svg></button>
			</div>
		</div>
		<div class="ratings" aria-label="Review platform ratings">
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.8 <span>Capterra</span></span>
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.8 <span>SoftwareSuggest</span></span>
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 5.0 <span>G2</span></span>
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.5 <span>Trustpilot</span></span>
		</div>
	</div>
</section>

<!-- ============ FAQ (matches FAQPage schema in functions.php word-for-word -
     inlined here directly, NOT pulled from a shared function; if this copy
     changes, the schema block in functions.php must be updated by hand too) ============ -->
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">Pharmacy software FAQs</h2>
		</div>
		<div class="faq-list">
			<?php
			$hr_faqs = array(
				array(
					'q' => 'What is pharmacy software?',
					'a' => "Pharmacy software runs a pharmacy's entire day on one platform: prescription intake, GST billing at the counter, inventory with batch and expiry tracking, purchase orders to wholesalers, regulatory registers, and end-of-day accounting. Instead of manual bill books, stock registers and separate accounting tools, every strip that enters or leaves the store is traceable - with faster billing, fewer expired losses and inspection-ready records.",
				),
				array(
					'q' => 'Is this the same as medical store software or chemist shop software?',
					'a' => 'Yes. Medical store software, chemist shop software, pharmacy management system and pharmacy software all describe the same category - software that runs a medicine retail counter and its stock. Healthray covers the full range: a single neighbourhood medical store, a chain of chemist shops, or a hospital\'s in-house pharmacy, on one platform.',
				),
				array(
					'q' => 'How much does pharmacy software cost in India?',
					'a' => 'Pricing depends on your setup - a single medical store pays far less than a multi-store chain or a hospital pharmacy with ward supply. Healthray offers flexible plans with a free trial, and a 10% discount is currently available on premium plans. Book a demo and we will share a quote matched to your pharmacy.',
				),
				array(
					'q' => 'Does it handle GST billing and e-way bills?',
					'a' => 'Yes. The billing counter generates GST-compliant invoices for retail and credit sales, supports UPI, card and cash payments, and produces GSTR-ready reports and e-way bill documentation. Stock movements sync to the accounting ledgers in real time, so your P&L and tax filings never need manual reconciliation.',
				),
				array(
					'q' => 'Does it maintain Schedule H, H1 and narcotics registers?',
					'a' => 'Yes. Sales of Schedule H, H1 and X drugs are recorded with prescription details, doctor information and patient records as required under the Drugs and Cosmetics Rules, and the software maintains the corresponding digital registers with complete audit trails - ready for drug inspector visits without paper registers.',
				),
				array(
					'q' => 'Does it work for both retail pharmacies and hospital pharmacies?',
					'a' => 'Yes. A retail medical store gets fast GST billing, expiry-safe inventory and wholesaler purchase management. A hospital pharmacy gets indent-based ward supply, doctor e-prescription integration and charges that post directly to patient bills. Both run on the same platform, so a pharmacy attached to a clinic or hospital shares one stock and one set of accounts.',
				),
				array(
					'q' => "Is Healthray's pharmacy software ABDM compliant?",
					'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant, and it is NABH-certified healthcare software listed on the official NABH portal. Pharmacies connected to Healthray clinics and hospitals participate in the ABHA-linked digital health ecosystem, with patient consent, inside the normal workflow.',
				),
				array(
					'q' => 'How long does implementation take?',
					'a' => "A single medical store can start billing the same day - product masters for common Indian brands come preloaded, and your existing stock is imported from Excel or your old software. Multi-store chains and hospital pharmacies typically take a few days to two weeks including staff training, with migration handled by Healthray's onboarding team.",
				),
				array(
					'q' => 'Is there a free trial or free version of the pharmacy software?',
					'a' => 'Yes. You can take a free trial of Healthray\'s pharmacy software, or book a free demo where a product specialist walks through your counter\'s daily flow - billing, stock entry, expiry alerts and reports. Small medical stores can also explore our <a href="' . esc_url( home_url( '/free-pharmacy-software/' ) ) . '">free pharmacy software</a> plan to get started at no cost.',
				),
			);
			foreach ( $hr_faqs as $hr_faq ) :
				?>
				<details>
					<summary><?php echo esc_html( $hr_faq['q'] ); ?></summary>
					<div class="answer"><?php echo wp_kses_post( $hr_faq['a'] ); ?></div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>

<!-- ============ FINAL CTA with form ============ -->
<section aria-labelledby="cta-title" style="padding-top:0">
	<div class="wrap">
		<div class="cta">
			<div class="cta-grid">
				<div class="cta-copy">
					<h2 id="cta-title">See a prescription go from counter to compliant bill</h2>
					<p>A 30-minute demo built around your pharmacy - billing, stock with expiry alerts, purchase orders and registers - so you judge it on your daily reality, not slides.</p>
					<ul class="cta-points">
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Free trial available - and 10% off premium plans right now</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Stock import from Excel or old software included in every plan</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free pharmacy demo</h2>
					<p>See a prescription go from counter to compliant bill. Our team replies within one business day. <strong>10% off premium plans</strong> is currently available.</p>
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