<?php
/*
Template Name: Clinic Management Software Landing
--------------------------------------------------------------
Page ID:      79865  (replaces the existing live
              /clinic-management-software/ page - confirm this
              is intended before publishing; the current live
              page has different content built a different way)
Body class:   clinic-landing
CSS file:     css/clinic-landing.css (enqueued + inlined via
              functions-clinic-additions.php)
JS file:      js/clinic-landing.js (EMPTY placeholder - this
              page has no genuinely page-specific interactivity;
              see comment in that file)
*/
?>
<main>

<!-- ============ BREADCRUMBS (matches BreadcrumbList schema) ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
  <div class="wrap">
    <ol>
      <li><a href="https://healthray.com/">Home</a></li>
      <li><span aria-current="page">Clinic Management Software</span></li>
    </ol>
  </div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
  <div class="wrap hero-grid">
    <div>
      <p class="eyebrow">Solo doctors · Polyclinics · Urgent care · NABH certified</p>
      <h1 id="hero-title">Clinic management software that fits how clinics
        <span class="pulse-word">actually work
          <svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
            <path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
          </svg>
        </span>
      </h1>
      <p class="hero-sub">Appointments, patient records, e-prescriptions, billing, pharmacy and lab - one platform for the whole clinic, from the front desk to the follow-up call.</p>
      <ul class="hero-points">
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Go live in about 7 days - migration from registers or old software included</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> ABHA ID creation and ABDM linking built into your front-desk workflow</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Starts solo-doctor small, scales to multi-location polyclinics - same platform</li>
      </ul>
      <p class="hero-proof"><strong>2,500+ clinics &amp; hospitals</strong> <span class="dot"></span> <strong>5M+ patient records</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
    </div>

    <!-- Compact demo form (same shared CF7 form as every other product page) -->
    <div class="lead-card" id="demo-form">
      <h2>Book a free clinic demo</h2>
      <p>See your clinic's daily flow live - booking to billing. Our team replies within one business day.</p>
      <?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
    </div>
  </div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<?php
$hr_clinic_compliance = array(
    array( 'href' => 'https://nabh.co/software/healthray/', 'target' => true, 'img' => 'https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp', 'alt' => "NABH certified healthcare software - Healthray listed on the official NABH portal", 'label' => 'NABH Certified' ),
    array( 'href' => '', 'img' => 'https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp', 'alt' => 'HIPAA compliant clinic management software', 'label' => 'HIPAA' ),
    array( 'href' => '', 'img' => 'https://healthray.com/wp-content/uploads/2026/03/abdm-healthray-150x150.webp', 'alt' => 'ABDM compliant clinic software - Ayushman Bharat Digital Mission', 'label' => 'ABDM' ),
    array( 'href' => '', 'img' => 'https://healthray.com/wp-content/uploads/2026/03/abha-healthray-150x150.webp', 'alt' => 'ABHA integrated clinic management system', 'label' => 'ABHA' ),
    array( 'href' => '', 'img' => 'https://healthray.com/wp-content/uploads/2025/07/NHA.webp', 'alt' => 'NHA approved clinic management software', 'label' => 'NHA Approved' ),
    array( 'href' => '', 'img' => 'https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp', 'alt' => 'ISO 27001 certified information security', 'label' => 'ISO 27001' ),
    array( 'href' => '', 'img' => 'https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp', 'alt' => 'HL7 interoperable clinic software', 'label' => 'HL7' ),
    array( 'href' => '', 'img' => 'https://healthray.com/wp-content/uploads/2025/07/FHIR.webp', 'alt' => 'FHIR compliant health data interoperability', 'label' => 'FHIR' ),
);
?>
<section class="compliance" aria-labelledby="compliance-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Compliance built in</p>
      <h2 id="compliance-title">One Clinic Platform, Every Compliance Standard</h2>
      <p>Healthray is <strong>NABH-certified healthcare software</strong> - listed on the official NABH portal - and HIPAA and ABDM compliant by design. Built for Ayushman Bharat workflows, HL7/FHIR interoperability and ICD-10/11 coding, compliance lives at the platform level, so it's never your clinic's extra work.</p>
    </div>
    <!-- NOTE: verify each certification (esp. ISO 27001) is current before deploy. -->
    <div class="comp-grid">
      <?php foreach ( $hr_clinic_compliance as $hr_comp ) :
        $hr_tag = $hr_comp['href'] ? 'a' : 'div';
      ?>
      <<?php echo $hr_tag; ?> class="comp-item reveal"<?php if ( $hr_comp['href'] ) : ?> href="<?php echo esc_url( $hr_comp['href'] ); ?>" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"<?php endif; ?>>
        <img src="<?php echo esc_url( $hr_comp['img'] ); ?>" alt="<?php echo esc_attr( $hr_comp['alt'] ); ?>" loading="lazy" decoding="async" width="56" height="56"><span><?php echo esc_html( $hr_comp['label'] ); ?></span>
      </<?php echo $hr_tag; ?>>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ WHAT IS CLINIC MANAGEMENT SOFTWARE (+ practice mgmt absorption) ============ -->
<section class="define" aria-labelledby="define-title">
  <div class="wrap define-grid">
    <div>
      <p class="eyebrow">The basics</p>
      <h2 id="define-title">What is clinic management software?</h2>
      <p style="margin-top:14px">Clinic management software runs a clinic's entire day on one platform: appointment booking and queues, patient registration, electronic medical records, e-prescriptions, billing and payments, pharmacy and lab coordination, and end-of-day reporting. Instead of registers, spreadsheets and disconnected tools, the front desk, doctors and accounts all work from the same system - patient information is entered once and flows everywhere it's needed.</p>
      <p>You'll also hear this category called <strong>practice management software</strong> - that term emphasizes the administrative side of a doctor's practice (scheduling, billing, claims), while clinic management software usually implies the clinical side too. Healthray covers both: complete practice administration plus EMR and e-prescriptions, with a mobile app for doctors who run their practice on the move.</p>
      <p>The result is a clinic that feels predictable: patients arrive prepared, queues move, doctors finish notes in minutes, and the owner sees the day's revenue without chasing anyone.</p>
    </div>
    <img src="https://healthray.com/wp-content/uploads/2026/07/clinic-management.svg" alt="Healthray clinic management software dashboard showing appointments, patient queue and billing" width="700" height="525" loading="lazy" decoding="async">
  </div>
</section>

<!-- ============ WHO IT SERVES (preserved from current page, copy corrected) ============ -->
<section aria-labelledby="serves-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Built for your kind of clinic</p>
      <h2 id="serves-title">Who this clinic management system serves</h2>
      <p>Different clinics run on different rhythms. The platform adapts to yours instead of forcing a generic workflow.</p>
    </div>
    <div class="grid-3">
      <div class="card reveal">
        <h3>General practice clinics</h3>
        <p>For general physicians handling routine care, follow-ups and preventive visits for a wide patient base - high volume, fast documentation, dependable queues.</p>
      </div>
      <div class="card reveal">
        <h3>Specialist clinics</h3>
        <p>For specialist-led practices - cardiology, dermatology, urology and more - focused on condition-specific care, with structured clinical templates for each speciality.</p>
      </div>
      <div class="card reveal">
        <h3>Urgent care clinics</h3>
        <p>For clinics handling sudden, non-emergency cases where walk-in volume, rapid triage and quick patient turnaround decide whether the day works.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ FEATURES (12 modules, corrected copy, hub links to sibling pages) ============ -->
<section class="define" id="features" aria-labelledby="features-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">One platform, the whole clinic</p>
      <h2 id="features-title">Clinic software features for every desk in your practice</h2>
      <p>Every module shares one patient record - so nothing is typed twice, and nothing gets lost between the front desk, the consultation room and the counter.</p>
    </div>
    <div class="grid-3">
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/></svg></span>
        <h3>Appointments &amp; scheduling</h3>
        <p>Online booking, automated SMS/WhatsApp reminders that cut no-shows, live queues for walk-ins, and waitlists that fill cancelled slots automatically.</p>
        <a class="more" href="https://healthray.com/doctor-appointment-system/">Explore the appointment system →</a>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 11l-3 3-2-2"/></svg></span>
        <h3>Patient registration &amp; ABHA</h3>
        <p>Digital intake with e-consent, ABHA ID creation and verification at the front desk, and quick check-in flows for busy OPD hours.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M9 15h6M9 11h2"/></svg></span>
        <h3>EMR with speciality templates</h3>
        <p>Structured clinical notes tuned to your speciality, complete patient history on one screen, and automated alerts for allergies and interactions.</p>
        <a class="more" href="https://healthray.com/emr-software/">Explore EMR software →</a>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z"/></svg></span>
        <h3>e-Prescriptions</h3>
        <p>MCI-compliant digital prescriptions with drug-interaction checks, delivered to the patient's phone and flowing straight to the pharmacy counter.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M2 7h5M2 12h5M2 17h5M17 7h5M17 12h5M17 17h5"/></svg></span>
        <h3>Billing &amp; payments</h3>
        <p>Itemized invoices generated from the consultation, UPI/card/wallet payments, insurance claim tracking, and revenue reports the owner actually reads.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6M10 3v6.34L4.72 18.5A2 2 0 0 0 6.46 21.5h11.08a2 2 0 0 0 1.74-3L14 9.34V3"/></svg></span>
        <h3>Laboratory integration</h3>
        <p>Digital lab orders - in-house or outsourced - with real-time status, results attached to the patient record, and automatic SMS/WhatsApp alerts to patients.</p>
        <a class="more" href="https://healthray.com/laboratory-information-management-system/">Explore LIMS →</a>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 20.5 3.5 13.5a4.95 4.95 0 1 1 7-7l7 7a4.95 4.95 0 1 1-7 7zM8.5 8.5l7 7"/></svg></span>
        <h3>Pharmacy management</h3>
        <p>e-Prescriptions flow to dispensing automatically, with batch and expiry alerts that cut wastage and reorder automation that prevents stock-outs.</p>
        <a class="more" href="https://healthray.com/pharmacy-management-system/">Explore pharmacy →</a>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 21h8M12 18v3M7 9l3 3 3-4 2 2"/></svg></span>
        <h3>Radiology &amp; imaging</h3>
        <p>DICOM viewing inside the patient record, worklists that eliminate re-typing patient details, and real-time alerts for critical findings.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M23 7l-7 5 7 5V7zM14 5H3a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z"/></svg></span>
        <h3>Teleconsultation</h3>
        <p>Video consultations with a virtual waiting room, notes and e-prescriptions on the same screen, and after-hours access for follow-ups.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM20 8v6M23 11h-6"/></svg></span>
        <h3>Patient portal</h3>
        <p>Patients book and reschedule themselves, complete intake forms before arriving, and access their reports and prescriptions any time - fewer calls to your desk.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg></span>
        <h3>MIS dashboard &amp; reports</h3>
        <p>Real-time patient flow, revenue and dues, staff productivity and inventory - the clinic's command center, readable in one glance.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/><circle cx="12" cy="12" r="3.5"/></svg></span>
        <h3>AI assistance</h3>
        <p>Smart scheduling that balances doctor load, voice-to-note transcription that shortens documentation, and automated reminders that run themselves.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ CLINIC vs HOSPITAL SOFTWARE (boundary section) ============ -->
<section id="clinic-vs-hospital" aria-labelledby="versus-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Choosing the right size</p>
      <h2 id="versus-title">Clinic software vs hospital software: which do you need?</h2>
      <p>Buying hospital-grade software for a clinic means paying for complexity you won't use. Buying clinic software for a hospital means outgrowing it in a year. Here's the honest boundary.</p>
    </div>
    <table class="versus-table">
      <caption class="hp">Comparison of clinic management software and hospital management software</caption>
      <thead>
        <tr><th scope="col"></th><th scope="col">Clinic Management Software</th><th scope="col">Hospital Management Software (HMS)</th></tr>
      </thead>
      <tbody>
        <tr><td>Built for</td><td>Outpatient practices - solo doctors, specialist clinics, polyclinics, urgent care</td><td>Hospitals with admissions - IPD, wards, operation theatres, multiple departments</td></tr>
        <tr><td>Core workflow</td><td>Appointment → consultation → e-prescription → billing → follow-up</td><td>Everything a clinic does, plus admissions, bed management, OT scheduling and discharge</td></tr>
        <tr><td>Billing</td><td>OPD billing, packages, UPI/card payments, basic insurance</td><td>IPD billing, TPA claims, insurance desks, departmental accounting</td></tr>
        <tr><td>Team size</td><td>1–20 staff across front desk, doctors and counter</td><td>Dozens to hundreds of staff across departments and shifts</td></tr>
      </tbody>
    </table>
    <p class="versus-cta">Healthray runs both on one platform. Start with clinic modules today; if your practice grows into a hospital, activate IPD, wards and TPA workflows on the same system - no migration, no re-entry. Running a hospital already? See our complete <a href="https://healthray.com/">hospital management system</a>.</p>
  </div>
</section>

<!-- ============ OUTCOMES BAND (visual signature - reframed, attributed) ============ -->
<section class="outcomes" aria-labelledby="outcomes-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Measured, not promised</p>
      <h2 id="outcomes-title">What clinics report after switching to Healthray</h2>
      <p>Typical outcomes our partner clinics share after their first months on the platform.</p>
    </div>
    <div class="outcomes-grid">
      <div class="outcome reveal"><b class="down">↓45%</b><span>Admin work at the front desk</span></div>
      <div class="outcome reveal"><b class="down">↓40%</b><span>No-shows, with automated reminders</span></div>
      <div class="outcome reveal"><b class="down">↓35%</b><span>Average patient wait time</span></div>
      <div class="outcome reveal"><b>↑30%</b><span>Patient volume handled per day</span></div>
      <div class="outcome reveal"><b>↑25%</b><span>Revenue captured, fewer missed charges</span></div>
      <div class="outcome reveal"><b>↑50%</b><span>Documentation speed with templates</span></div>
    </div>
    <p class="outcomes-note">Figures reflect ranges reported by Healthray partner clinics; individual results vary by clinic size and workflow. <a href="https://healthray.com/case-studies/" style="color:#8FB2F5;font-weight:600">Read the case studies →</a></p>
  </div>
</section>

<!-- ============ ARCHITECTURE, SECURITY & SUPPORT (preserved content, cleaned) ============ -->
<section class="define" aria-labelledby="arch-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Under the hood</p>
      <h2 id="arch-title">Architecture, integrations, security &amp; support</h2>
      <p>Clinic management software designed for secure deployment, regulatory compliance and clean integration with India's digital health ecosystem.</p>
    </div>
    <div class="arch-grid">
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19a4.5 4.5 0 1 0-.42-8.98 6 6 0 1 0-11.5 2.2A3.5 3.5 0 0 0 6.5 19z"/></svg> Architecture &amp; deployment</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Cloud, on-premise and hybrid deployment options</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Scales from a single clinic to multi-location practice networks</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> High availability with automated backups and recovery</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Performance tuned for high appointment and visit loads</li>
        </ul>
      </div>
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3M7 12h10M12 7v10"/></svg> Healthcare integrations</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> ABDM-compliant integration with ABHA creation and linking</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Standards-based interoperability on HL7 and FHIR</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> DICOM support for viewing and exchanging radiology images</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Secure APIs for insurance, billing and digital payments</li>
        </ul>
      </div>
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Data security &amp; compliance</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Encryption in transit and at rest, HIPAA-aligned controls</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Consent-driven data sharing aligned with ABDM guidelines</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Role-based permissions with multi-factor authentication</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Complete audit trails for clinical, admin and billing actions</li>
        </ul>
      </div>
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg> Implementation &amp; support</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Dedicated implementation specialists for clinics</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Secure migration from registers or existing software</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Role-based training for front desk, doctors and admin</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Ongoing support in English, Hindi and Gujarati</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ============ CLIENTS (10, single instance, clinic-weighted) ============ -->
<?php
$hr_media = 'https://healthray.com/wp-content/uploads';
$hr_clinic_clients = array(
    array( 'Dave Eye Hospital', 'Surat, Gujarat', $hr_media . '/2025/08/Dr.-Dave-Eye-Hospital.webp' ),
    array( 'Modi Children Hospital', 'Vyara, Gujarat', $hr_media . '/2025/08/Modi-Children-Hospital.webp' ),
    array( 'Zenith Doctor House', 'Valsad, Gujarat', $hr_media . '/2025/08/Zenith-Doctor-House.webp' ),
    array( 'Kanha Medical Centre', 'Chegur, Telangana', $hr_media . '/2025/04/Kanha-Medical-Centre.webp' ),
    array( 'Chakraa Medical Center', 'Tiruppur, Tamil Nadu', $hr_media . '/2024/09/Chakraa-Medical-Center.webp' ),
    array( 'Mehta Hospital', 'Valsad, Gujarat', $hr_media . '/2025/08/Mehta-Hospital.webp' ),
    array( 'Pardi Hospital', 'Vapi, Gujarat', $hr_media . '/2025/08/Pardi-Hospital.webp' ),
    array( 'Kishori Hospital', 'Bargarh, Odisha', $hr_media . '/2025/04/Kishori-Hospital.webp' ),
    array( 'Universal Multispeciality Hospital', 'Surat, Gujarat', $hr_media . '/2024/04/Universal-Multispecialty-Hospital.webp' ),
    array( 'Gastron Super Speciality Hospital', 'Surat, Gujarat', $hr_media . '/2024/06/Gastron.webp' ),
);
?>
<section aria-labelledby="clients-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Trusted across India</p>
      <h2 id="clients-title">Clinics and hospitals that run on Healthray</h2>
      <p>From single-doctor clinics to multi-speciality facilities - 2,500+ healthcare providers manage their daily operations on our platform.</p>
    </div>
    <div class="clients-grid">
      <?php foreach ( $hr_clinic_clients as $hr_client ) : ?>
        <div class="client reveal">
          <img src="<?php echo esc_url( $hr_client[2] ); ?>" alt="<?php echo esc_attr( $hr_client[0] . ' logo' ); ?>" loading="lazy" decoding="async" width="120" height="54">
          <b><?php echo esc_html( $hr_client[0] ); ?></b>
          <span><?php echo esc_html( $hr_client[1] ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="clients-note">…and 2500+ more across India. <a href="https://healthray.com/case-studies/">Read their case studies →</a></p>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="define" aria-labelledby="testi-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Doctor stories</p>
      <h2 id="testi-title">What practice owners say about Healthray</h2>
    </div>
    <div class="t-grid">
      <article class="t-card reveal">
        <blockquote>This platform has advanced functionalities which revolutionized our hospital from conventional to a modern healthcare facility. Best choice for urologists and kidney surgeons.</blockquote>
        <div class="t-who">
          <img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp" alt="Dr. Ketan Rupala" loading="lazy" decoding="async" width="48" height="48">
          <div><b>Dr. Ketan Rupala</b><span>Urologist &amp; Urosurgeon, Rupala Kidney &amp; Prostate Hospital</span></div>
        </div>
      </article>
      <article class="t-card reveal">
        <blockquote>It works best for our hospital team to provide effective gastrointestinal treatment through in-depth analytics reports.</blockquote>
        <div class="t-who">
          <img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp" alt="Dr. Vimal Dhaduk" loading="lazy" decoding="async" width="48" height="48">
          <div><b>Dr. Vimal Dhaduk</b><span>GI Surgery, VR Group of Hospitals / Gastron Hospital</span></div>
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

<!-- ============ FAQ (matches FAQPage schema word-for-word via shared helper) ============ -->
<section id="faq" aria-labelledby="faq-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Common questions</p>
      <h2 id="faq-title">Clinic management software FAQs</h2>
    </div>
    <div class="faq-list">
      <?php
      $faqs = array(
        array(
            'q' => 'What is clinic management software?',
            'a' => "Clinic management software is a single platform that runs a clinic's daily operations: appointment booking and queues, patient registration, electronic medical records, e-prescriptions, billing and payments, pharmacy and lab coordination, and reporting. Instead of registers, spreadsheets and separate tools, the front desk, doctors and accounts all work from one system - so patient information is entered once and flows everywhere it's needed.",
        ),
        array(
            'q' => 'Is clinic management software the same as practice management software?',
            'a' => "Largely, yes - the two terms describe the same category. 'Practice management software' emphasizes the administrative side of a doctor's practice (scheduling, billing, claims), while 'clinic management software' usually implies the full clinical picture too, including EMR and e-prescriptions. Healthray covers both: complete practice administration plus clinical records, on one platform, with a mobile app for doctors who manage their practice on the go.",
        ),
        array(
            'q' => 'What is the difference between clinic software and hospital management software?',
            'a' => 'Clinic management software is built for outpatient practices - appointments, consultations, billing and day-to-day admin for one or a few doctors. Hospital management software (HMS) adds inpatient workflows: bed and ward management, IPD billing, operation theatre scheduling, TPA claims and multi-department coordination. Healthray runs on one platform across both, so a clinic that grows into a hospital activates the additional modules without migrating systems.',
        ),
        array(
            'q' => 'How much does clinic management software cost in India?',
            'a' => 'Pricing depends on the number of doctors, locations and modules you need - a solo practitioner pays far less than a multi-doctor polyclinic. Healthray offers flexible plans that start small and scale with your practice, plus a free trial. Book a demo and we will share a quote matched to your clinic\'s size.',
        ),
        array(
            'q' => "Is Healthray's clinic software ABDM compliant?",
            'a' => "Yes. Healthray is NHA-approved and ABDM-compliant. Your clinic can create and verify patients' ABHA IDs at registration and link health records to the Ayushman Bharat Digital Mission ecosystem - inside the normal front-desk workflow, without separate software.",
        ),
        array(
            'q' => 'Is Healthray NABH certified?',
            'a' => 'Yes. Healthray is NABH-certified healthcare software, listed on the official portal of the National Accreditation Board for Hospitals & Healthcare Providers. For clinics pursuing NABH entry-level accreditation, using NABH-certified software supports your digital compliance requirements out of the box.',
        ),
        array(
            'q' => 'Does it work for a single-doctor clinic?',
            'a' => 'Yes. A solo practitioner can start with appointments, EMR and billing - the modules a small clinic actually uses - at pricing that matches a small practice. As the clinic grows, pharmacy, laboratory, teleconsultation and additional locations can be switched on without changing systems or re-entering data.',
        ),
        array(
            'q' => 'How long does clinic software implementation take?',
            'a' => "Most clinics go live in about 7 days, including data migration from registers or older software, workflow setup and staff training. Larger polyclinics with pharmacy and lab typically take 1–2 weeks. Healthray's onboarding team handles the transition so consultations continue without interruption.",
        ),
        array(
            'q' => 'Is there a free trial or demo of the clinic management software?',
            'a' => "Yes. You can take a free trial of Healthray's clinic management software, or book a free 30-minute demo where a product specialist walks through your clinic's daily flow - booking, consultation, e-prescription and billing. No credit card is required.",
        ),
    );
      foreach ( $faqs as $hr_faq ) : ?>
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
          <h2 id="cta-title">See your clinic's whole day on one screen</h2>
          <p>A 30-minute demo built around your practice - booking, consultation, e-prescription and billing - so you judge it on your daily reality, not slides.</p>
          <ul class="cta-points">
            <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Free trial available - no credit card required</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Migration from registers or old software included in every plan</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Support in English, Hindi and Gujarati</li>
          </ul>
        </div>

        <div class="lead-card">
          <h2>Book a free clinic demo</h2>
          <p>See your clinic's daily flow live. Our team replies within one business day.</p>
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