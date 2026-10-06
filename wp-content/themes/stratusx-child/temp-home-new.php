<?php

/**
 * Template Name: Healthray Home Landing
 * Template Post Type: page
 * @package Healthray_Home_Template
 */

$hr_media = 'https://healthray.com/wp-content/uploads';

?>

<div id="hr-home">
	<main id="main">
		<!-- ============ HERO: copy left, compact lead form right ============ -->
		<section class="hero" aria-labelledby="hero-title">
			<div class="wrap hero-grid">
				<div>
					<p class="eyebrow">AI-powered · ABDM compliant · Safe & Secure</p>
					<h1 id="hero-title">The hospital management system that keeps your
						<span class="pulse-word">hospital running
							<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
								<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300" />
							</svg>
						</span>
					</h1>
					<p class="hero-sub">One cloud platform for OPD, IPD, EMR, pharmacy, laboratory, billing and TPA claims - with ABHA verification built into your front desk.</p>
					<ul class="hero-points">
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
								<path d="M20 6 9 17l-5-5" />
							</svg> Go live in days, not months - data migration and training included</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
								<path d="M20 6 9 17l-5-5" />
							</svg> NHA-approved &amp; ABDM-compliant, with ABHA ID creation at registration</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
								<path d="M20 6 9 17l-5-5" />
							</svg> GST-ready billing, TPA claims and finance reports out of the box</li>
					</ul>
					<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
				</div>

				<div class="lead-card" id="demo-form">
					<h2>Book a free demo</h2>
					<p>See Healthray on your own workflows. Our team replies within one business day.</p>
					<?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM"]'); ?>
				</div>
			</div>
		</section>

		<!-- ============ COMPLIANCE STRIP ============ -->
		<div class="strip" aria-label="Healthcare standards and compliance">
			<div class="wrap">
				<small>Certified &amp; compliant with Indian and global health standards</small>
				<?php foreach (array('NHA Approved', 'ABDM Compliant', 'HIPAA compliant', 'FHIR', 'SNOMED CT', 'ICD-10/11', 'CERT-In') as $hr_badge): ?>
					<span class="badge"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
							<path d="M20 6 9 17l-5-5" />
						</svg>
						<?php echo esc_html($hr_badge); ?>
					</span>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- ============ PRODUCT PREVIEW ============ -->
		<section class="hr-section preview" aria-labelledby="preview-title">
			<div class="wrap preview-grid">
				<div>
					<p class="eyebrow">Live in one dashboard</p>
					<h2 id="preview-title">Your whole hospital, on one screen</h2>
					<p style="margin-top:12px;font-size:1.02rem">OPD queues, revenue, bed occupancy and lab status update in real time - with every patient ABHA-verified at the front desk. Owners see the business; doctors see their patients; nobody chases paper.</p>
				</div>
				<div class="mock reveal" role="img" aria-label="Healthray dashboard showing today's OPD queue, revenue and bed occupancy">
					<div class="mock-bar"><i></i><i></i><i></i><span>ABDM · Live</span></div>
					<div class="mock-body">
						<div class="mock-row">
							<div class="mock-card">
								<small>Today's OPD revenue</small>
								<div class="big">₹1,84,250</div>
								<span class="up">▲ 12% vs yesterday</span>
								<div class="bars" aria-hidden="true"><i style="height:40%"></i><i style="height:62%"></i><i style="height:48%"></i><i style="height:75%"></i><i style="height:58%"></i><i style="height:90%"></i><i style="height:70%"></i></div>
							</div>
							<div class="mock-card">
								<small>Bed occupancy</small>
								<div class="big">86%</div>
								<span class="up">124 / 144 beds</span>
								<div class="bars" aria-hidden="true"><i style="height:80%"></i><i style="height:86%"></i><i style="height:74%"></i><i style="height:88%"></i><i style="height:82%"></i><i style="height:86%"></i><i style="height:90%"></i></div>
							</div>
						</div>
						<div class="queue">
							<div class="queue-head"><span>OPD queue - Dr. Mehta</span><span>Token</span></div>
							<div class="q-item"><span class="avatar">RS</span><span class="meta"><b>Rakesh Shah</b><span>Follow-up · Cardiology</span></span><span class="chip chip-abha">ABHA ✓</span></div>
							<div class="q-item"><span class="avatar">PP</span><span class="meta"><b>Priya Patel</b><span>New patient · General medicine</span></span><span class="chip chip-abha">ABHA ✓</span></div>
							<div class="q-item"><span class="avatar">AK</span><span class="meta"><b>Amit Kumar</b><span>Lab reports ready</span></span><span class="chip chip-wait">Waiting</span></div>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ============ STATS ============ -->
		<?php echo do_shortcode('[hr_stats]'); ?>

		<!-- ============ CLIENTS (5 x 2) ============ -->
		<?php echo do_shortcode('[hr_clients]'); ?>

		<!-- ============ MODULES ============ -->
		<section class="hr-section modules" id="modules" aria-labelledby="modules-title">
			<div class="wrap">
				<div class="section-head">
					<p class="eyebrow">One platform, every department</p>
					<h2 id="modules-title">Every module your hospital management software needs</h2>
					<p>From the front desk to the pharmacy counter, each Healthray module shares one patient record - so nothing is typed twice and nothing gets lost between departments.</p>
				</div>
				<div class="grid-3">
					<a class="card reveal" href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6M9 10h.01M15 10h.01" />
							</svg>
						</span>
						<h3>OPD &amp; IPD Management</h3>
						<p>Registration, appointments, token queues, admissions, bed allocation and discharge summaries in one flow.</p>
						<span class="more">Explore HIMS →</span>
					</a>
					<a class="card reveal" href="<?php echo esc_url(home_url('/emr-software/')); ?>">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M9 15h6M9 11h2" />
							</svg>
						</span>
						<h3>EMR / EHR Software</h3>
						<p>Structured electronic medical records with speciality templates, e-prescriptions and full patient history at a glance.</p>
						<span class="more">Explore EMR →</span>
					</a>
					<a class="card reveal" href="<?php echo esc_url(home_url('/pharmacy-management-system/')); ?>">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M10.5 20.5 3.5 13.5a4.95 4.95 0 1 1 7-7l7 7a4.95 4.95 0 1 1-7 7zM8.5 8.5l7 7" />
							</svg>
						</span>
						<h3>Pharmacy Management</h3>
						<p>Stock, batch and expiry tracking with GST-ready invoicing and low-inventory alerts that prevent stock-outs.</p>
						<span class="more">Explore pharmacy →</span>
					</a>
					<a class="card reveal" href="<?php echo esc_url(home_url('/laboratory-information-management-system/')); ?>">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M9 3h6M10 3v6.34L4.72 18.5A2 2 0 0 0 6.46 21.5h11.08a2 2 0 0 0 1.74-3L14 9.34V3" />
							</svg>
						</span>
						<h3>Laboratory (LIMS)</h3>
						<p>Sample tracking from collection to report, instrument integration and automatic result delivery to the patient record.</p>
						<span class="more">Explore LIMS →</span>
					</a>
					<a class="card reveal" href="<?php echo esc_url(home_url('/hospital-information-management-system/')); ?>">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 2v20M2 7h5M2 12h5M2 17h5M17 7h5M17 12h5M17 17h5" />
							</svg>
						</span>
						<h3>Billing, TPA &amp; Claims</h3>
						<p>Accurate OPD/IPD billing, insurance and TPA claim workflows, and finance reports your accountant will actually use.</p>
						<span class="more">Explore billing →</span>
					</a>
					<a class="card reveal" href="<?php echo esc_url(home_url('/clinic-management-software/')); ?>">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2M12 11v6M9 14h6M9 2h6v4H9z" />
							</svg>
						</span>
						<h3>Clinic Management</h3>
						<p>A lighter setup for clinics and nursing homes - appointments, EMR and billing without enterprise complexity.</p>
						<span class="more">Explore clinics →</span>
					</a>
				</div>
			</div>
		</section>

		<!-- ============ WHY ============ -->
		<section class="hr-section" id="why" aria-labelledby="why-title">
			<div class="wrap">
				<div class="section-head">
					<p class="eyebrow">Why Healthray</p>
					<h2 id="why-title">Built for how Indian hospitals actually work</h2>
					<p>Most hospital software is adapted from foreign products. Healthray was designed for Indian workflows from day one - ABDM, GST, TPA desks, high OPD volumes and all.</p>
				</div>
				<div class="why-grid">
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
							</svg>
						</span>
						<div>
							<h3>ABDM &amp; ABHA in the workflow, not bolted on</h3>
							<p>Create and verify ABHA IDs at registration, link records to the ABDM ecosystem and support PMJAY - without extra software or duplicate data entry.</p>
						</div>
					</div>
                    <div class="why reveal">
						<span class="icon">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 13v8"></path>
								<path d="M4 14.899A7 7 0 1 1 15.71 8h1.79a4.5 4.5 0 0 1 2.5 8.242"></path>
								<path d="m8 17 4-4 4 4"></path>
							</svg>
						</span>
						<div>
							<h3>Automatic cloud backups</h3>
							<p>Your data is backed up continuously to secure cloud infrastructure - a hardware failure or local incident never means lost records.</p>
						</div>
					</div>
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1" />
								<circle cx="12" cy="12" r="3.5" />
							</svg>
						</span>
						<div>
							<h3>AI that saves clinical time</h3>
							<p>Smart templates, faster documentation and analytics that surface what matters - so doctors spend minutes on records instead of hours.</p>
						</div>
					</div>
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="11" width="18" height="10" rx="2" />
								<path d="M7 11V7a5 5 0 0 1 10 0v4" />
							</svg>
						</span>
						<div>
							<h3>Secure by standard</h3>
							<p>Role-based access, encrypted records and audit trails - structured on FHIR, SNOMED CT and ICD-10/11 so your data stays interoperable and yours.</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ============ SECURITY ============ -->
		<section class="hr-section security" id="security" aria-labelledby="security-title">
			<div class="wrap">
				<div class="section-head">
					<p class="eyebrow">Data security</p>
					<h2 id="security-title">We Secure Your Data Like<br> No One Else</h2>
					<p>Patient records are the most sensitive data a hospital holds. Healthray protects them with layered, standards-based security - so your hospital stays compliant and your patients stay confident.</p>
				</div>
				<div class="why-grid">
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<rect x="3" y="11" width="18" height="10" rx="2" />
								<path d="M7 11V7a5 5 0 0 1 10 0v4" />
							</svg>
						</span>
						<div>
							<h3>End-to-end encryption</h3>
							<p>Patient data is encrypted in transit and at rest, so records stay unreadable to anyone outside your authorized team.</p>
						</div>
					</div>
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 11l-3 3-2-2" />
							</svg>
						</span>
						<div>
							<h3>Role-based access control</h3>
							<p>Reception sees appointments, doctors see clinical records, accounts sees billing - each user accesses only what their role requires.</p>
						</div>
					</div>
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8M8 17h5" />
							</svg>
						</span>
						<div>
							<h3>Complete audit trails</h3>
							<p>Every view, edit and export of a patient record is logged with user, time and action - full accountability for compliance reviews.</p>
						</div>
					</div>
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 3a9 9 0 1 0 9 9M12 3v6M12 3l3 3M21 12h-6" />
							</svg>
						</span>
						<div>
							<h3>Automatic cloud backups</h3>
							<p>Your data is backed up continuously to secure cloud infrastructure - a hardware failure or local incident never means lost records.</p>
						</div>
					</div>
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM9 12l2 2 4-4" />
							</svg>
						</span>
						<div>
							<h3>Compliant With Indian And Global Standards</h3>
							<p>Aligned with India's DPDP Act and HIPAA, our data handling structures health records using global FHIR, SNOMED CT, and ICD-10/11 standards.</p>
						</div>
					</div>
					<div class="why reveal">
						<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83" />
							</svg>
						</span>
						<div>
							<h3>Your data stays yours</h3>
							<p>Interoperable, exportable records mean you're never locked in - your hospital owns its data, always.</p>
						</div>
					</div>
				</div>
			</div>
		</section>

		<!-- ============ TESTIMONIALS (4-up with photos) ============ -->
		<?php echo do_shortcode('[hr_testimonials]'); ?>

		<!-- ============ FAQ ============ -->
		<section class="hr-section" id="faq" aria-labelledby="faq-title">
			<div class="wrap">
				<div class="section-head center">
					<p class="eyebrow">Common questions</p>
					<h2 id="faq-title">Hospital management system FAQs</h2>
				</div>
				<div class="faq-list">
					<?php
					$hr_faqs = array(
						array(
							'q' => 'What is a hospital management system (HMS)?',
							'a' => "A hospital management system is software that runs a hospital's daily operations on one platform - patient registration, OPD and IPD workflows, electronic medical records, pharmacy, laboratory, billing, insurance and TPA claims, inventory and staff management. Healthray's HMS connects all of these so every department works from the same real-time data.",
						),
						array(
							'q' => 'Is Healthray ABDM compliant?',
							'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. You can create and verify ABHA IDs at registration, link health records to the Ayushman Bharat Digital Mission ecosystem, and support PMJAY workflows - all inside your normal OPD and IPD process.',
						),
						array(
							'q' => 'How much does hospital management software cost in India?',
							'a' => 'Pricing depends on hospital size, bed count and the modules you need - a small clinic pays far less than a 200-bed multi-speciality hospital. Healthray offers flexible plans for clinics, nursing homes and hospitals. Book a free demo and we will share a quote matched to your facility.',
						),
						array(
							'q' => 'Can Healthray work for small clinics as well as large hospitals?',
							'a' => 'Yes. A solo practitioner can start with appointments and EMR, a clinic can add pharmacy and lab, and a multi-speciality hospital can run full OPD/IPD, billing, TPA and HR on the same platform. You activate modules as you grow - no need to change software later.',
						),
						array(
							'q' => 'Is patient data secure on a cloud-based hospital management system?',
							'a' => 'Healthray uses encrypted storage and transmission, role-based access control and audit logs, with automatic cloud backups. Data handling follows Indian healthcare data-protection requirements, and standards such as FHIR, SNOMED CT and ICD-10/11 keep records interoperable and structured.',
						),
						array(
							'q' => 'How long does it take to implement Healthray in a hospital?',
							'a' => 'Most clinics go live within days; mid-size hospitals are typically operational in one to three weeks, including data migration, workflow setup and staff training. A dedicated onboarding team handles the transition so patient care is never interrupted.',
						),
					);
					foreach ($hr_faqs as $hr_faq):
						?>
						<details>
							<summary>
								<?php echo esc_html($hr_faq['q']); ?>
							</summary>
							<div class="answer">
								<?php echo esc_html($hr_faq['a']); ?>
							</div>
						</details>
					<?php endforeach; ?>
				</div>
			</div>
		</section>

		<!-- ============ FINAL CTA with form ============ -->
		<section class="hr-section" aria-labelledby="cta-title" style="padding-top:0">
			<div class="wrap">
				<div class="cta">
					<div class="cta-grid">
						<div class="cta-copy">
							<h2 id="cta-title">See your hospital on Healthray</h2>
							<p>A 30-minute demo with your own workflows - OPD, billing, pharmacy and ABHA - so you can judge it on your daily reality, not slides.</p>
							<ul class="cta-points">
								<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
										<path d="M20 6 9 17l-5-5" />
									</svg> Free, no-obligation walkthrough with a product specialist</li>
								<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
										<path d="M20 6 9 17l-5-5" />
									</svg> Data migration &amp; staff training included in every plan</li>
								<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
										<path d="M20 6 9 17l-5-5" />
									</svg> Support in English, Hindi and Gujarati</li>
							</ul>
						</div>
						<div class="lead-card">
							<h2>Book a free demo</h2>
							<p>See Healthray on your own workflows. Our team replies within one business day.</p>
							<?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM"]'); ?>
						</div>
					</div>
					<svg class="ecg-bg" viewBox="0 0 1200 70" preserveAspectRatio="none" aria-hidden="true">
						<path d="M0 35 H200 l15-18 20 36 15-30 12 12 H480 l14-22 18 40 14-26 10 8 H800 l15-18 20 36 15-30 12 12 H1200" />
					</svg>
				</div>
			</div>
		</section>
	</main>
</div><!-- #hr-home -->