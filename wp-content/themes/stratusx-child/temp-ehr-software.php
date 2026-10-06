<?php
/**
 * Template Name: EHR Software Landing
 * Template Post Type: page
 */
$hr_media = 'https://healthray.com/wp-content/uploads';
?>

<main>

<!-- ============ BREADCRUMBS ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
	<div class="wrap">
		<ol>
			<li><a href="<?php echo esc_url( home_url( '/' ) ); ?>">Home</a></li>
			<li><span aria-current="page">EHR Software</span></li>
		</ol>
	</div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
	<div class="wrap hero-grid">
		<div>
			<p class="eyebrow">ABDM linked · HL7/FHIR interoperable · Multi-branch ready</p>
			<h1 id="hero-title">Electronic health records software that
				<span class="pulse-word">follows the patient
					<svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
						<path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
					</svg>
				</span>
			</h1>
			<p class="hero-sub">One longitudinal record per patient - shared across departments, branches and the ABDM ecosystem. Every authorized clinician sees the same complete history, wherever care happens.</p>
			<ul class="hero-points">
				<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> One patient, one record - across OPD, IPD, lab, pharmacy and every branch</li>
				<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> ABHA-linked records that join the patient's national digital health history</li>
				<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> HL7/FHIR interoperability - exchange records with labs, insurers and other providers</li>
			</ul>
			<p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
		</div>

		<div class="lead-card" id="demo-form">
			<h2>Book a free EHR demo</h2>
			<p>See cross-department record sharing live. Our team replies within one business day.</p>
			<?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
		</div>
	</div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Compliance built in</p>
			<h2 id="compliance-title">One EHR Platform, Every Compliance Standard</h2>
			<p>Healthray is <strong>NABH-certified healthcare software</strong> - listed on the official NABH portal - and HIPAA and ABDM compliant by design. Built for Ayushman Bharat workflows, HL7/FHIR interoperability, SNOMED CT terminology and ICD-10/11 coding, compliance lives at the platform level, so it's never your team's extra work.</p>
		</div>
		<div class="comp-grid">
		    <a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"><img src="<?php echo esc_url($hr_media . '/2026/07/NABH-ehr-certified-150x150.webp'); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span></a>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/Hipaa-healthray-150x150.webp' ); ?>" alt="HIPAA compliant EHR software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abdm-healthray-150x150.webp' ); ?>" alt="ABDM compliant EHR system - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/abha-healthray-150x150.webp' ); ?>" alt="ABHA integrated electronic health records" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/NHA.webp' ); ?>" alt="NHA approved EHR software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/iso-27001-healthray-150x150.webp' ); ?>" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2026/03/hl7-healthray-150x150.webp' ); ?>" alt="HL7 interoperable EHR software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
			<div class="comp-item reveal"><img src="<?php echo esc_url( $hr_media . '/2025/07/FHIR.webp' ); ?>" alt="FHIR compliant health record interoperability" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
		</div>
	</div>
</section>

<!-- ============ WHAT IS EHR SOFTWARE ============ -->
<section class="define" aria-labelledby="define-title">
	<div class="wrap define-grid">
		<div>
			<p class="eyebrow">The basics</p>
			<h2 id="define-title">What is EHR software?</h2>
			<p style="margin-top:14px">EHR software - Electronic Health Records software - maintains a patient's complete, longitudinal health history in one digital record: every consultation, diagnosis, prescription, lab result and imaging report, across time and across providers. Where a paper file or an isolated digital chart lives in one place, an EHR system is designed to travel - the record follows the patient between departments, branches and, through India's ABDM ecosystem, between healthcare organizations.</p>
			<p>That portability is what makes an EHR more than storage. Records structured on HL7/FHIR, SNOMED CT and ICD-10/11 can be exchanged, queried and analyzed - so a cardiologist at your second branch sees the allergy recorded at your first, and a patient's ABHA-linked history is available wherever they seek care next.</p>
			<p>Healthray's EHR is the record layer of its complete <a href="<?php echo esc_url( home_url( '/' ) ); ?>" style="color:var(--brand);font-weight:600">hospital management software</a> - the same longitudinal record powers OPD/IPD, laboratory, pharmacy and billing, so nothing is entered twice and nothing exists in a silo.</p>
		</div>
		<img src="<?php echo esc_url( $hr_media . '/2026/07/ehr-what-is-ehr.svg' ); ?>" alt="Healthray EHR software showing a patient's longitudinal health record shared across departments" width="700" height="510" loading="lazy" decoding="async">
	</div>
</section>

<!-- ============ EHR vs EMR ============ -->
<section id="ehr-vs-emr" aria-labelledby="versus-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Choosing the right system</p>
			<h2 id="versus-title">EHR vs EMR: which one does your organization need?</h2>
			<p>The two terms get used interchangeably, but they solve different problems - and buying the wrong one is an expensive mistake to unwind.</p>
		</div>
		<table class="versus-table">
			<caption class="hp">Comparison of EHR and EMR systems</caption>
			<thead>
				<tr><th scope="col"></th><th scope="col">EHR (Electronic Health Record)</th><th scope="col">EMR (Electronic Medical Record)</th></tr>
			</thead>
			<tbody>
				<tr><td>Scope</td><td>The patient's complete health history, designed to travel across providers and locations</td><td>The digital patient chart within one practice or hospital</td></tr>
				<tr><td>Primary use</td><td>Care coordination - between departments, branches, labs, insurers and national systems</td><td>Clinical documentation at the point of care - notes, prescriptions, results</td></tr>
				<tr><td>Sharing</td><td>Across organizations via interoperability standards (HL7/FHIR, ABDM)</td><td>Within your facility and its departments</td></tr>
				<tr><td>Best for</td><td>Multi-branch hospitals, networks, and providers coordinating care beyond one location</td><td>Running a single clinic or hospital's daily consultations efficiently</td></tr>
			</tbody>
		</table>
		<p class="versus-cta">Healthray gives you both on one platform: the record layer is a full EHR system, and the consultation experience is a speciality-tuned EMR. Running a single clinic and mainly need fast documentation? Start with our <a href="<?php echo esc_url( home_url( '/emr-software/' ) ); ?>">EMR software</a> - you can grow into the EHR capabilities without changing systems.</p>
	</div>
</section>

<!-- ============ FEATURES (12, interoperability-first) ============ -->
<section class="define" id="features" aria-labelledby="features-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Built for connected care</p>
			<h2 id="features-title">EHR software features built around one shared record</h2>
			<p>Every feature serves one principle: the patient's record is complete, current and available wherever care happens - never trapped in a department, a branch or a filing room.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v18M3 8h4M3 12h4M3 16h4M17 8h4M17 12h4M17 16h4"/></svg></span>
				<h3>Longitudinal patient timeline</h3>
				<p>Every visit, diagnosis, prescription, report and procedure sits on one chronological record. A patient's full history is readable in minutes, not reconstructed from files.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg></span>
				<h3>ABHA &amp; ABDM record linking</h3>
				<p>ABHA IDs verified at registration; with patient consent, records join their national digital health history under the <a href="<?php echo esc_url( home_url( '/abdm/' ) ); ?>" style="color:var(--brand);font-weight:600">Ayushman Bharat Digital Mission</a>.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3M7 12h10M12 7v10"/></svg></span>
				<h3>HL7/FHIR interoperability</h3>
				<p>Standards-based record exchange with labs, imaging centers, insurers and other providers. Your data speaks the language the healthcare ecosystem understands.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/></svg></span>
				<h3>Multi-branch shared records</h3>
				<p>A patient registered at one branch is treated at another with full history, allergies and medications already on screen. One network, one record.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.83 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5z"/></svg></span>
				<h3>Clinical documentation &amp; e-prescriptions</h3>
				<p>Speciality-tuned notes and MCI-compliant e-prescriptions with drug-interaction checks, captured once at the point of care and visible everywhere it's needed.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6M10 3v6.34L4.72 18.5A2 2 0 0 0 6.46 21.5h11.08a2 2 0 0 0 1.74-3L14 9.34V3"/></svg></span>
				<h3>Lab &amp; pharmacy integration</h3>
				<p>Orders flow out; results and dispensing records flow back, attached to the patient's record automatically. Nothing is re-entered between departments.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17 8h2a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V10a2 2 0 0 1 2-2h2M12 2v13M8 6l4-4 4 4"/></svg></span>
				<h3>Referral &amp; care coordination</h3>
				<p>Refer patients between doctors, departments or facilities with the relevant record attached, so the receiving clinician starts informed instead of starting from zero.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 11l-3 3-2-2"/></svg></span>
				<h3>Patient health portal</h3>
				<p>Patients view their own records, reports and prescriptions, book follow-ups and share documents. The result: engaged patients and fewer front-desk calls.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
				<h3>Consent &amp; access control</h3>
				<p>Role-based access decides who sees what; record sharing beyond your organization happens with patient consent, and every access is logged.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19a4.5 4.5 0 1 0-.42-8.98 6 6 0 1 0-11.5 2.2A3.5 3.5 0 0 0 6.5 19z"/></svg></span>
				<h3>Cloud access &amp; mobile app</h3>
				<p>Secure cloud infrastructure with 99.9% uptime keeps records available on any device, at any branch, with automatic backups and no server room.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M8 13h8M8 17h5"/></svg></span>
				<h3>Security &amp; audit trails</h3>
				<p>Encryption in transit and at rest, DPDP-aligned data handling, and a complete log of every view, edit and export. Accountability is the default.</p>
			</div>
			<div class="card reveal">
				<span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/></svg></span>
				<h3>Appointments &amp; scheduling</h3>
				<p>Bookings, reminders and doctor availability across locations. Patients book at any branch, and the record is ready before they arrive.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ WHO IT'S FOR ============ -->
<section aria-labelledby="whofor-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Built to scale with you</p>
			<h2 id="whofor-title">Who needs an EHR system?</h2>
			<p>If patient records need to exist in more than one place - more departments, more branches, more providers - you've outgrown a standalone chart.</p>
		</div>
		<div class="grid-3">
			<div class="card reveal">
				<h3>Multi-speciality hospitals</h3>
				<p>Cardiology, ortho, gastro and the lab all work from one record: no duplicate registrations, no "please bring your old file."</p>
			</div>
			<div class="card reveal">
				<h3>Hospital chains &amp; multi-branch networks</h3>
				<p>Patients move between your branches; their records move with them. Management sees network-wide analytics from one dashboard.</p>
			</div>
			<div class="card reveal">
				<h3>Growing clinics &amp; medical colleges</h3>
				<p>Start with one facility and add locations, departments or teaching workflows without migrating systems. The record layer already scales.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ IMAGE-CENTRIC: INSIDE THE EHR (zigzag) ============ -->
<section aria-labelledby="inside-title" style="padding-top:0">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Inside the platform</p>
			<h2 id="inside-title">A look inside Healthray's EHR system</h2>
			<p>Two views that show what "one shared record" actually means on screen - for the people using it every day.</p>
		</div>
		<div class="media-rows">
			<div class="media-row">
				<div class="media-img reveal">
					<img src="<?php echo esc_url( $hr_media . '/2026/07/ehr-integrated-departments.svg' ); ?>" alt="Healthray EHR system centralized patient information view - one record shared across departments" width="700" height="500" loading="lazy" decoding="async">
					<span class="float-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>One source of truth</span>
				</div>
				<div class="media-copy">
					<h3>Every department reads the same record</h3>
					<p>The complete patient story (demographics, history, allergies, medications, reports) lives in one centralized record instead of five departmental copies.</p>
					<ul>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Clinicians access the record from any department, branch or device</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> No duplicate data entry, and far fewer transcription errors</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Faster, better-informed clinical decisions at every touchpoint</li>
					</ul>
				</div>
			</div>
			<div class="media-row flip">
				<div class="media-img reveal">
					<img src="<?php echo esc_url( $hr_media . '/2026/07/ehr-centralized-record.svg' ); ?>" alt="Healthray electronic health records software streamlining workflow between hospital departments" width="700" height="500" loading="lazy" decoding="async">
					<span class="float-chip"><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg>Connected workflow</span>
				</div>
				<div class="media-copy">
					<h3>Departments that finally work as one</h3>
					<p>OPD, lab, pharmacy, billing and administration operate on integrated data, so a change in one place is instantly the truth everywhere else.</p>
					<ul>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> All departmental data integrated in one platform</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Less manual effort and smarter use of staff and resources</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Clinical documentation maintained in a legally sound format</li>
					</ul>
				</div>
			</div>
		</div>
	</div>
</section>

<!-- ============ HOW THE RECORD TRAVELS (animated timeline) ============ -->
<section class="define" aria-labelledby="journey-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">One record, end to end</p>
			<h2 id="journey-title">How a patient's record travels through Healthray</h2>
			<p>Follow one patient from the front desk to the follow-up - and notice that nobody re-enters anything.</p>
		</div>
		<div class="steps-track reveal" aria-hidden="true">
			<svg viewBox="0 0 1200 56" preserveAspectRatio="none">
				<path d="M0 28 H84 l8-12 12 22 8-16 6 6 H324 l8-12 12 22 8-16 6 6 H564 l8-12 12 22 8-16 6 6 H804 l8-12 12 22 8-16 6 6 H1044 l8-12 12 22 8-16 6 6 H1200"/>
			</svg>
			<i></i><i></i><i></i><i></i><i></i>
		</div>
		<div class="steps-grid reveal">
			<div class="step">
				<h3>Registration with ABHA</h3>
				<p>The patient's ABHA ID is verified at the front desk; their record opens - or their existing history loads.</p>
			</div>
			<div class="step">
				<h3>Consultation captured</h3>
				<p>The doctor documents once - notes, diagnosis, e-prescription - directly into the longitudinal record.</p>
			</div>
			<div class="step">
				<h3>Orders flow out</h3>
				<p>Lab, imaging and pharmacy receive orders instantly; results and dispensing attach back automatically.</p>
			</div>
			<div class="step">
				<h3>Record travels</h3>
				<p>A referral, another department or another branch - the complete record is already there when the patient arrives.</p>
			</div>
			<div class="step">
				<h3>Follow-up &amp; insight</h3>
				<p>The patient sees reports in their portal; your team sees outcomes, trends and revenue in analytics.</p>
			</div>
		</div>
	</div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>


<!-- ============ CLIENTS (10) ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India" title="Hospitals running their records on Healthray"]'); ?>

<!-- ============ TESTIMONIALS ============ -->
<section class="define" aria-labelledby="testi-title">
	<div class="wrap">
		<div class="section-head">
			<p class="eyebrow">Doctor stories</p>
			<h2 id="testi-title">What doctors say about their records on Healthray</h2>
		</div>
		<div class="t-grid">
			<article class="t-card reveal">
				<blockquote>It's easy with Healthray to track the client's insulin reports and foster a collaborative environment among endocrinologists.</blockquote>
				<div class="t-who">
					<img src="<?php echo esc_url( $hr_media . '/2024/04/Dr.-Pradip-Dalwadi-150x150.webp' ); ?>" alt="Dr. Pradip Dalwadi" loading="lazy" decoding="async" width="48" height="48">
					<div><b>Dr. Pradip Dalwadi</b><span>Endocrinologist &amp; Diabetologist, Pratham Endocrine &amp; Diabetes Centre</span></div>
				</div>
			</article>
			<article class="t-card reveal">
				<blockquote>It helps me ease my healthcare practice and significantly assists in effective diagnosis and solving critical respiratory cases. Kudos to Healthray!</blockquote>
				<div class="t-who">
					<img src="<?php echo esc_url( $hr_media . '/2024/04/Dr.-Milan-Modi-150x150.webp' ); ?>" alt="Dr. Milan Modi" loading="lazy" decoding="async" width="48" height="48">
					<div><b>Dr. Milan Modi</b><span>Pulmonologist &amp; Chest Physician, Modi Allergy &amp; Chest Clinic</span></div>
				</div>
			</article>
		</div>
		<div class="ratings" aria-label="Review platform ratings">
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.8 <span>Capterra</span></span>
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.8 <span>SoftwareSuggest</span></span>
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 5.0 <span>G2</span></span>
			<span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.5 <span>Trustpilot</span></span>
		</div>
	</div>
</section>

<!-- ============ FAQ ============ -->
<section id="faq" aria-labelledby="faq-title">
	<div class="wrap">
		<div class="section-head center">
			<p class="eyebrow">Common questions</p>
			<h2 id="faq-title">EHR software FAQs</h2>
		</div>
		<div class="faq-list">
			<?php
			$faqs = array(
					array('q' => 'What is EHR software?', 'a' => "EHR software (Electronic Health Records software) maintains a patient's complete, longitudinal health history - consultations, diagnoses, prescriptions, lab results and imaging - in one digital record designed to be shared across departments, branches and other healthcare providers. Unlike a paper file or an isolated digital chart, an EHR system follows the patient: every authorized clinician sees the same up-to-date record, wherever care happens."),
					array('q' => 'What is the difference between EHR and EMR software?', 'a' => "An EMR (Electronic Medical Record) is the digital chart used within a single practice or hospital for day-to-day consultations. An EHR (Electronic Health Record) is broader: it aggregates the patient's history across providers and locations, built on interoperability standards so records can move with the patient. If you run one clinic, an EMR may be enough; if you run multiple departments, branches or coordinate care with other providers, you need an EHR system. Healthray provides both on one platform."),
					array('q' => "Is Healthray's EHR software ABDM compliant?", 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. Patients\' ABHA IDs are created and verified at registration, and their health records can be linked to the Ayushman Bharat Digital Mission ecosystem - so records created in your hospital become part of the patient\'s national digital health history, with patient consent.'),
					array('q' => 'Is Healthray NABH certified?', 'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals & Healthcare Providers. For hospitals pursuing or maintaining NABH accreditation, using NABH-certified software supports your digital health standards compliance out of the box.'),
					array('q' => 'Can patient records be shared across hospital branches?', 'a' => 'Yes. Multi-branch hospitals and clinic networks run on one shared EHR: a patient registered at one branch can be treated at another with their full history, allergies, medications and reports already available. Role-based access ensures each staff member sees only what their role permits, and every access is logged.'),
					array('q' => 'How much does EHR software cost in India?', 'a' => 'EHR software pricing in India depends on the number of doctors, branches and modules you need - a single clinic pays far less than a multi-branch hospital network. Healthray offers flexible plans with a free trial, and pricing scales with your organization rather than penalizing growth. Book a demo and we will share a quote matched to your setup.'),
					array('q' => 'Is patient data secure in a cloud-based EHR system?', 'a' => 'Yes. Healthray encrypts health records in transit and at rest, enforces role-based access control, logs every view and edit in audit trails, and backs data up continuously on redundant cloud infrastructure with 99.9% uptime. Records follow HL7/FHIR, SNOMED CT and ICD-10/11 standards, and data handling aligns with Indian data-protection requirements including the DPDP Act.'),
					array('q' => 'How long does EHR implementation take?', 'a' => "A single facility typically goes live in 1–2 weeks. Multi-branch rollouts are phased - usually 2–4 weeks per wave including data migration from legacy systems, workflow configuration and staff training. Healthray's onboarding team manages the transition so patient care continues without interruption."),
					array('q' => 'Is there a free trial or demo of the EHR software?', 'a' => "Yes. You can take a free trial of Healthray's EHR software, or book a free 30-minute demo where a product specialist walks through your organization's setup - registration with ABHA, cross-department record sharing, referrals and reporting. No credit card is required."),
				);
				foreach ($faqs as $hr_faq): ?>
				<details>
					<summary><?php echo esc_html( $hr_faq['q'] ); ?></summary>
					<div class="answer"><?php echo esc_html( $hr_faq['a'] ); ?></div>
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
					<h2 id="cta-title">See one record travel through your whole hospital</h2>
					<p>A 30-minute demo built around your organization - registration with ABHA, cross-department sharing, referrals and analytics - so you judge it on your daily reality, not slides.</p>
					<ul class="cta-points">
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Free trial available - no credit card required</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Legacy data migration &amp; staff training included in every plan</li>
						<li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Support in English, Hindi and Gujarati</li>
					</ul>
				</div>

				<div class="lead-card">
					<h2>Book a free EHR demo</h2>
					<p>See cross-department record sharing live. Our team replies within one business day.</p>
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