<?php
/* Template Name: LIMS Landing
 *
 * Page ID:      79924
 * Body class:   lims-landing
 *                 ⚠️ ASSUMED, not explicitly given - follows the established
 *                 [abc]-landing pattern (emr-landing, ehr-landing) and matches
 *                 the "lims" prefix already used throughout this mockup's own
 *                 form field IDs (lims-hero-name, lims-footer-cta-phone, etc).
 *                 Confirm before deploy.
 * CSS file:     css/lims-software.css (enqueued/inlined via functions.php)
 * JS file:      js/lims-software.js (empty placeholder - no page-specific
 *                 interactivity found on this page; see file for why)
 */
?>

<main>

<!-- ============ BREADCRUMBS (matches BreadcrumbList schema) ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
  <div class="wrap">
    <ol>
      <li><a href="https://healthray.com/">Home</a></li>
      <li><span aria-current="page">Laboratory Information Management System (LIMS)</span></li>
    </ol>
  </div>
</nav>

<!-- ============ HERO ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
  <div class="wrap hero-grid">
    <div>
      <p class="eyebrow">Pathology &middot; Diagnostics &middot; Blood bank &middot; NABH certified</p>
      <h1 id="hero-title">LIMS software that runs your lab from sample to
        <span class="pulse-word">signed report
          <svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
            <path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300"/>
          </svg>
        </span>
      </h1>
      <p class="hero-sub">Cloud-based lab software for clinical, diagnostic, blood bank, research and public health laboratories - orders, sample tracking, analyzer interfacing, QC, billing and report delivery on one platform.</p>
      <ul class="hero-points">
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Start printing bills and reports in about 10 minutes</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Bi-directional analyzer interfacing - no manual transcription of results</li>
        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Built to support NABL (ISO 15189) accreditation workflows, audit-ready by default</li>
      </ul>
      <p class="hero-proof"><strong>500+ active labs</strong> <span class="dot"></span> <strong>4M+ reports printed</strong> <span class="dot"></span> <strong>5+ countries</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
    </div>

    <!-- Compact demo form (same component as other product pages) -->
    <div class="lead-card" id="demo-form">
      <h2>Book a free LIMS demo</h2>
      <p>See a sample travel from collection to signed report. Our team replies within one business day. <strong>10% off premium plans</strong> is currently available.</p>
      <?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
    </div>
  </div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Compliance built in</p>
      <h2 id="compliance-title">One Lab Platform, Every Compliance Standard</h2>
      <p>Healthray is <strong>NABH-certified healthcare software</strong> - listed on the official NABH portal - HIPAA and ABDM compliant by design, and built to support <strong>NABL (ISO 15189)</strong> accreditation workflows: QC rules, document control, audit trails and inspection-ready reports live at the platform level, so compliance is never your lab's extra work.</p>
    </div>
    <!-- NOTE: verify each certification (esp. ISO 27001) is current before deploy. -->
    <div class="comp-grid">
      <a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal">
        <img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span>
      </a>
      <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp" alt="HIPAA compliant LIMS software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
      <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/abdm-healthray-150x150.webp" alt="ABDM compliant lab software - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
      <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/abha-healthray-150x150.webp" alt="ABHA integrated laboratory information management system" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
      <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved LIMS software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
      <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
      <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="HL7 interoperable lab software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
      <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2025/07/FHIR.webp" alt="FHIR compliant laboratory data interoperability" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
    </div>
  </div>
</section>

<!-- ============ WHAT IS LIMS SOFTWARE (+ LIS distinction + pathology absorption) ============ -->
<section class="define" aria-labelledby="define-title">
  <div class="wrap define-grid">
    <div>
      <p class="eyebrow">The basics</p>
      <h2 id="define-title">What is LIMS software?</h2>
      <p style="margin-top:14px">LIMS software - a Laboratory Information Management System - runs a lab's entire operation on one platform: test orders and booking, barcode-based sample tracking, analyzer interfacing, result validation, quality control, inventory, billing and report delivery. Instead of registers, Excel sheets and manual transcription from analyzers, every sample is traceable from collection to signed report, with fewer errors and faster turnaround times.</p>
      <p>Two neighbouring terms are worth knowing. <strong>LIS</strong> (Laboratory Information System) traditionally means the patient-centric system inside a hospital lab, while LIMS is the broader, sample-centric category - Healthray covers both on one platform. And in India you'll often hear this category called <strong>pathology lab software</strong>, since pathology labs are its largest user group; that's the same product you're looking at here.</p>
      <p>Healthray's LIMS also plugs into the rest of your organization: the same platform powers our <a href="https://healthray.com/clinic-management-software/" style="color:var(--brand);font-weight:600">clinic management software</a> and hospital systems, so lab orders and results flow to doctors without re-entry.</p>
    </div>
    <img src="https://healthray.com/wp-content/uploads/2026/07/lims-software.svg" alt="Healthray LIMS software showing sample tracking, analyzer results and lab reports on one dashboard" width="690" height="500" loading="lazy" decoding="async">
  </div>
</section>

<!-- ============ WHO IT SERVES (preserved 6 lab types) ============ -->
<section aria-labelledby="serves-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Built for your kind of lab</p>
      <h2 id="serves-title">Who this laboratory management software serves</h2>
      <p>From a single pathology lab to a multi-branch diagnostic chain - the platform adapts to how your lab actually runs.</p>
    </div>
    <div class="grid-3">
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16M12 7v4M10 9h4M9 21v-4h6v4"/></svg></span>
        <h3>Clinical &amp; hospital laboratories</h3>
        <p>Patient diagnostic testing on blood, urine and tissue - supporting diagnosis, treatment decisions and ongoing health monitoring at clinical volume.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 18h8M3 21h18M14 18a6 6 0 0 0 3-11.2M9 9l1 1M10 3l5 5-3.5 3.5a2.12 2.12 0 0 1-3-3z"/></svg></span>
        <h3>Pathology &amp; diagnostic centers</h3>
        <p>Pathology, biochemistry, hematology, microbiology and imaging under one roof - including multi-branch chains, franchises and collection centers.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 2.7 6.9 9.2a6.5 6.5 0 1 0 10.2 0zM9.5 13.5a2.5 2.5 0 0 0 2.5 2.5"/></svg></span>
        <h3>Blood bank laboratories</h3>
        <p>Donor screening, compatibility testing, component separation and storage - with the traceability and regulatory reporting transfusion safety demands.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="13" rx="2"/><path d="M8 21h8M12 17v4M7 10.5h2.5l1.5-3 2 5 1.5-2H17"/></svg></span>
        <h3>Diagnostic imaging laboratories</h3>
        <p>X-ray, CT, MRI and ultrasound workflows on an integrated RIS - DICOM worklists, PACS connectivity and standardized reporting templates.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3h6M10 3v6.34L4.72 18.5A2 2 0 0 0 6.46 21.5h11.08a2 2 0 0 0 1.74-3L14 9.34V3M7.5 15h9"/></svg></span>
        <h3>Research laboratories</h3>
        <p>Experiments, clinical trials and investigative studies - with batching, chain-of-custody documentation and grant or study budget tracking.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M3 12h3l2-3 3 6 2-4 1.5 1H21M12 3a15 15 0 0 1 0 18M12 3a15 15 0 0 0 0 18"/></svg></span>
        <h3>Industrial, QC &amp; public health labs</h3>
        <p>Product and materials testing to quality standards, plus outbreak detection and large-scale disease surveillance for public health programs.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ FEATURES (12 consolidated modules, corrected copy) ============ -->
<section class="define" id="features" aria-labelledby="features-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">One platform, the whole lab</p>
      <h2 id="features-title">Lab software features for every bench in your laboratory</h2>
      <p>Every module shares one sample record - so nothing is transcribed twice, and nothing gets lost between the front desk, the bench and the billing counter.</p>
    </div>
    <div class="grid-3">
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 3v3M12 18v3M3 12h3M18 12h3M5.6 5.6l2.1 2.1M16.3 16.3l2.1 2.1M5.6 18.4l2.1-2.1M16.3 7.7l2.1-2.1"/><circle cx="12" cy="12" r="3.5"/></svg></span>
        <h3>AI-powered lab automation</h3>
        <p>Smart sample routing to the right analyzer, urgent-sample prioritization, early error detection on barcodes and specimens, and abnormal-result flagging for fast human review.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M9 3h6M10 3v6.34L4.72 18.5A2 2 0 0 0 6.46 21.5h11.08a2 2 0 0 0 1.74-3L14 9.34V3"/></svg></span>
        <h3>Sample &amp; order management</h3>
        <p>Digital order entry from portals, APIs or EHR interfaces; barcode/RFID tracking from accessioning to disposal; STAT prioritization; and a customizable test catalog with your own panels and reference ranges.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3M7 12h10M12 7v10"/></svg></span>
        <h3>Analyzer interfacing &amp; automation</h3>
        <p>Bi-directional communication with chemistry, hematology and microbiology analyzers - results land in the LIMS automatically, with rule-based validation and TAT tracking at every step.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M9 15l2 2 4-4"/></svg></span>
        <h3>Results management &amp; reporting</h3>
        <p>Auto-verification of normal results, critical-value alerts, multi-level technologist-to-pathologist approval, branded report templates, cumulative trend reports and dispatch by email, SMS or portal.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10zM9 12l2 2 4-4"/></svg></span>
        <h3>Quality management (QC &amp; QA)</h3>
        <p>Automated QC with Levey-Jennings charts and Westgard rules, SOP version control, CAPA tracking, staff competency records and tamper-proof audit trails - NABL inspection-ready without the paper.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21C7 17 4 13.5 4 10a8 8 0 0 1 16 0c0 3.5-3 7-8 11z"/><path d="M12 7v6M9 10h6"/></svg></span>
        <h3>Blood bank management</h3>
        <p>Donor database and outreach, component separation with barcode traceability, real-time stock with expiry alerts, automated cross-matching and NABH/regulatory transfusion reporting.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="14" rx="2"/><path d="M8 21h8M12 18v3M7 9l3 3 3-4 2 2"/></svg></span>
        <h3>Radiology (RIS) &amp; imaging</h3>
        <p>DICOM modality worklists that eliminate re-typed patient details, PACS integration with an in-record viewer, standardized reporting templates and prior-study comparison.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20M2 7h5M2 12h5M2 17h5M17 7h5M17 12h5M17 17h5"/></svg></span>
        <h3>Billing, accounting &amp; TPA</h3>
        <p>Automated invoicing at booking or completion, insurance eligibility checks and claims scrubbing, payer-specific pricing, multi-branch accounting, expense tracking and payroll/HR for lab staff.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M10.5 20.5 3.5 13.5a4.95 4.95 0 1 1 7-7l7 7a4.95 4.95 0 1 1-7 7zM8.5 8.5l7 7"/></svg></span>
        <h3>Inventory &amp; reagent management</h3>
        <p>Live stock levels across departments, FIFO expiry alerts that cut reagent wastage, lot and batch tracking for recalls, and per-test consumption analytics for real cost control.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM20 8v6M23 11h-6"/></svg></span>
        <h3>Patient portal &amp; delivery</h3>
        <p>Patients book tests, follow prep instructions, and download verified reports (PDF/DICOM) any time - with SMS/WhatsApp alerts the moment results are ready.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z"/></svg></span>
        <h3>Health packages &amp; B2B pricing</h3>
        <p>Bundle tests and imaging into checkup packages with their own TAT, auto-split orders across departments, and volume-based pricing for corporate and referral clients.</p>
      </div>
      <div class="card reveal">
        <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 3v18h18M7 15l4-4 3 3 5-6"/></svg></span>
        <h3>ROI dashboard &amp; analytics</h3>
        <p>TAT bottlenecks, cost-per-test margins, instrument utilization, rejection rates and department-wise profitability - your lab's finances and operations in one view.</p>
      </div>
    </div>
  </div>
</section>

<!-- ============ STANDALONE LAB vs HOSPITAL LAB (boundary section) ============ -->
<section id="lab-vs-hospital" aria-labelledby="versus-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Choosing the right fit</p>
      <h2 id="versus-title">Standalone lab or hospital lab: which setup do you need?</h2>
      <p>The same LIMS runs both - but the workflows differ, and knowing which you are buying for saves you from paying for the wrong shape.</p>
    </div>
    <table class="versus-table">
      <caption class="hp">Comparison of standalone laboratory and hospital in-house laboratory setups</caption>
      <thead>
        <tr><th scope="col"></th><th scope="col">Standalone Lab / Diagnostic Center</th><th scope="col">Hospital In-House Lab</th></tr>
      </thead>
      <tbody>
        <tr><td>Orders come from</td><td>Walk-ins, home collection, referring doctors and B2B clients</td><td>OPD/IPD doctors ordering inside the hospital system</td></tr>
        <tr><td>Billing</td><td>Patient billing, packages, corporate and referral pricing</td><td>Charges post to the patient's hospital bill, including IPD and TPA claims</td></tr>
        <tr><td>Reports go to</td><td>Patients, referring doctors and client portals</td><td>Straight into the patient's EMR for the treating doctor</td></tr>
        <tr><td>Runs best with</td><td>Healthray LIMS standalone - live in minutes</td><td>LIMS integrated with Healthray's hospital and clinic platform</td></tr>
      </tbody>
    </table>
    <p class="versus-cta">Healthray covers both on one platform. A standalone pathology lab starts printing reports in minutes; a hospital lab plugs into the same record that runs OPD, IPD and billing. Running a hospital? See our complete <a href="https://healthray.com/">hospital management software</a>. Running a clinic with an in-house lab? Start from our <a href="https://healthray.com/clinic-management-software/">clinic management software</a>.</p>
  </div>
</section>

<!-- ============ OUTCOMES BAND (attributed) ============ -->
<section class="outcomes" aria-labelledby="outcomes-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Measured, not promised</p>
      <h2 id="outcomes-title">What labs report after switching to Healthray</h2>
      <p>Typical outcomes our partner laboratories share after their first months on the platform.</p>
    </div>
    <div class="outcomes-grid">
      <div class="outcome reveal"><b>&uarr;50%</b><span>Data accuracy, with analyzer interfacing</span></div>
      <div class="outcome reveal"><b class="down">&darr;45%</b><span>Manual work across the bench</span></div>
      <div class="outcome reveal"><b class="down">&darr;25%</b><span>Turnaround time per report</span></div>
      <div class="outcome reveal"><b class="down">&darr;70%</b><span>Audit preparation time</span></div>
      <div class="outcome reveal"><b class="down">&darr;80%</b><span>Sample loss and mix-ups</span></div>
      <div class="outcome reveal"><b>&uarr;30%</b><span>Lab productivity overall</span></div>
    </div>
    <p class="outcomes-note">Figures reflect ranges reported by Healthray partner laboratories; individual results vary by lab size and workflow. <a href="https://healthray.com/case-studies/" style="color:#8FB2F5;font-weight:600">Read the case studies &rarr;</a></p>
  </div>
</section>

<!-- ============ ARCHITECTURE, SECURITY & SUPPORT (preserved content, cleaned) ============ -->
<section class="define" aria-labelledby="arch-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Under the hood</p>
      <h2 id="arch-title">Architecture, integrations, security &amp; support</h2>
      <p>Enterprise-grade laboratory information management software built for secure deployment, regulatory compliance and high-volume processing.</p>
    </div>
    <div class="arch-grid">
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M17.5 19a4.5 4.5 0 1 0-.42-8.98 6 6 0 1 0-11.5 2.2A3.5 3.5 0 0 0 6.5 19z"/></svg> Deployment &amp; architecture</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Cloud-based LIMS, on-premise and hybrid deployment options</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Scales from a single lab to multi-branch diagnostic networks</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> High availability with automated backups and disaster recovery</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Performance-optimized for high-volume sample processing</li>
        </ul>
      </div>
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3H5a2 2 0 0 0-2 2v3M16 3h3a2 2 0 0 1 2 2v3M8 21H5a2 2 0 0 1-2-2v-3M16 21h3a2 2 0 0 0 2-2v-3M7 12h10M12 7v10"/></svg> Interoperability &amp; integrations</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> HL7 and FHIR healthcare data exchange standards</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> EMR, EHR and hospital information system integration</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Bi-directional analyzer and instrument interfacing</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> RIS/PACS connectivity with DICOM support for imaging labs</li>
        </ul>
      </div>
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="10" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg> Data security &amp; regulatory</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> HIPAA-aligned encryption and secure data transmission</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> ABDM-compliant integration with ABHA authentication</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Role-based access with multi-factor authentication and e-signatures</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Tamper-proof audit logging supporting NABL, ISO and CAP inspections</li>
        </ul>
      </div>
      <div class="arch reveal">
        <h3><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg> Implementation &amp; support</h3>
        <ul>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Start printing bills and reports in about 10 minutes</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Secure migration from registers or legacy lab systems</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Workflow-based training for technicians, pathologists and admin</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> 24/7 technical support in English, Hindi and Gujarati</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ============ CLIENTS (10, single instance, LAB-weighted) ============ -->
<?php
$hr_media       = 'https://healthray.com/wp-content/uploads';
$hr_lims_clients = array(
    array( 'Pratham Laboratory', 'Surat, Gujarat', $hr_media . '/2024/01/Pratham-lab-1.webp' ),
    array( 'Diabcare Laboratory', 'Mumbai, Maharashtra', $hr_media . '/2024/01/Diabcare-lab-1.webp' ),
    array( 'Om Pathology Lab', 'Surat, Gujarat', $hr_media . '/2024/06/Om-Pathology-Lab-Healthray.webp' ),
    array( 'Bansal Diagnostics', 'Kurukshetra, Haryana', $hr_media . '/2024/06/Bansal-Diagnostics-Healthray.webp' ),
    array( 'Omed Diagnostics', 'Cachar, Assam', $hr_media . '/2024/06/Omed-Diagnostics-Healthray.webp' ),
    array( 'Chakraa Medical Center', 'Tiruppur, Tamil Nadu', $hr_media . '/2024/06/Chakraa-Medical-Center-Healthray.webp' ),
    array( 'Shiv Krishna Hospital', 'Ghaziabad, Uttar Pradesh', $hr_media . '/2024/06/Shiv-Krishna-Hospital-Healthray.webp' ),
    array( 'Universal Hospital', 'Surat, Gujarat', $hr_media . '/2024/01/Universal-lab-1.webp' ),
    array( 'Gastron Super Speciality Hospital', 'Surat, Gujarat', $hr_media . '/2024/06/Gastron.webp' ),
    array( 'Sri Manakula Vinayagar Hospital', 'Pondicherry', $hr_media . '/2024/11/Sri-Manakula-Vinayagar-Hospital.webp' ),
);
?>
<section aria-labelledby="clients-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Trusted across India</p>
      <h2 id="clients-title">Laboratories that run on Healthray</h2>
      <p>From single pathology labs to hospital diagnostic departments - 500+ laboratories process their daily samples on our platform.</p>
    </div>
    <div class="clients-grid">
      <?php foreach ( $hr_lims_clients as $hr_client ) : ?>
        <div class="client reveal">
          <img src="<?php echo esc_url( $hr_client[2] ); ?>" alt="<?php echo esc_attr( $hr_client[0] . ' logo' ); ?>" loading="lazy" decoding="async" width="120" height="54">
          <b><?php echo esc_html( $hr_client[0] ); ?></b>
          <span><?php echo esc_html( $hr_client[1] ); ?></span>
        </div>
      <?php endforeach; ?>
    </div>
    <p class="clients-note">&hellip;and 490+ more labs across India and beyond. <a href="https://healthray.com/case-studies/">Read their case studies &rarr;</a></p>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="define" aria-labelledby="testi-title">
  <div class="wrap">
    <div class="section-head">
      <p class="eyebrow">Lab stories</p>
      <h2 id="testi-title">What laboratories say about Healthray LIMS</h2>
    </div>
    <div class="t-grid">
      <article class="t-card reveal">
        <blockquote>I'm very happy with Healthray's LIMS. The system is easy to use, runs reliably, and has made our laboratory workflow much smoother while helping us maintain high quality standards.</blockquote>
        <div class="t-who">
          <img src="https://healthray.com/wp-content/uploads/2024/01/Pratham-lab-1.webp" alt="Pratham Laboratory" loading="lazy" decoding="async" width="48" height="48">
          <div><b>Pratham Laboratory</b><span>Surat, Gujarat</span></div>
        </div>
      </article>
      <article class="t-card reveal">
        <blockquote>Healthray's LIMS has comprehensive features that improve collaboration among our internal staff and contribute to real patient satisfaction.</blockquote>
        <div class="t-who">
          <img src="https://healthray.com/wp-content/uploads/2024/01/Diabcare-lab-1.webp" alt="Diabcare Laboratory" loading="lazy" decoding="async" width="48" height="48">
          <div><b>Diabcare Laboratory</b><span>Mumbai, Maharashtra</span></div>
        </div>
      </article>
      <article class="t-card reveal">
        <blockquote>Since we started using Healthray's LIMS, our staff's daily work has become much simpler - and its features have helped us bring down our overall lab expenses.</blockquote>
        <div class="t-who">
          <img src="https://healthray.com/wp-content/uploads/2024/06/Om-Pathology-Lab-Healthray.webp" alt="Om Pathology Lab" loading="lazy" decoding="async" width="48" height="48">
          <div><b>Om Pathology Lab</b><span>Surat, Gujarat</span></div>
        </div>
      </article>
    </div>
    <div class="ratings" aria-label="Review platform ratings">
      <span class="rating"><span class="stars" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span> 4.8 <span>Capterra</span></span>
      <span class="rating"><span class="stars" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span> 4.8 <span>SoftwareSuggest</span></span>
      <span class="rating"><span class="stars" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span> 5.0 <span>G2</span></span>
      <span class="rating"><span class="stars" aria-hidden="true">&#9733;&#9733;&#9733;&#9733;&#9733;</span> 4.5 <span>Trustpilot</span></span>
    </div>
  </div>
</section>

<?php
$hr_lims_faq = array(
    array(
        'q' => 'What is LIMS software?',
        'a' => "LIMS software (Laboratory Information Management System) runs a laboratory's entire operation on one platform: test orders and sample tracking, analyzer interfacing, result validation and reporting, quality control, inventory, billing and patient delivery. Instead of registers, Excel sheets and manual transcription from analyzers, every sample is traceable from collection to signed report - with fewer errors and faster turnaround times.",
    ),
    array(
        'q' => 'What is the difference between LIMS and LIS?',
        'a' => 'The two terms overlap heavily. LIS (Laboratory Information System) traditionally describes patient-centric hospital lab systems, while LIMS (Laboratory Information Management System) is the broader term covering sample-centric workflows in diagnostic, research and industrial labs too. Healthray covers both: patient-centric reporting for clinical and hospital labs, plus sample-centric tracking, QC and inventory for every other lab type.',
    ),
    array(
        'q' => 'Is this pathology lab software?',
        'a' => "Yes. Pathology labs are Healthray LIMS's largest user group in India - the platform handles pathology, biochemistry, hematology, microbiology and molecular workflows, along with radiology (RIS), blood bank and multi-branch diagnostic chains. If you searched for pathology lab software, this is the same category of product.",
    ),
    array(
        'q' => 'How much does LIMS software cost in India?',
        'a' => "Pricing depends on your lab's size, branches and modules - a single collection center pays far less than a multi-branch diagnostic chain with blood bank and imaging. Healthray offers flexible plans with a free trial, and a 10% discount is currently available on premium plans. Book a demo and we will share a quote matched to your lab.",
    ),
    array(
        'q' => 'Does Healthray LIMS support NABL (ISO 15189) accreditation?',
        'a' => 'Yes. The platform is built to support NABL (ISO 15189) accreditation workflows: automated QC tracking with Levey-Jennings and Westgard rules, document and SOP version control, complete audit trails, staff competency records, equipment calibration logs and inspection-ready compliance reports. Healthray itself is NABH-certified healthcare software, listed on the official NABH portal.',
    ),
    array(
        'q' => "Is Healthray's lab software ABDM compliant?",
        'a' => "Yes. Healthray is NHA-approved and ABDM-compliant. Labs can verify patients' ABHA IDs at registration and, with patient consent, link reports to the Ayushman Bharat Digital Mission ecosystem - inside the normal front-desk workflow.",
    ),
    array(
        'q' => 'Should I choose cloud-based LIMS or on-premise?',
        'a' => 'Most labs choose cloud: no server room, automatic updates, access from any branch or device, and automated backups with 99.9% uptime. On-premise and hybrid deployments are available for labs with specific infrastructure or policy requirements. Healthray supports all three models on the same platform.',
    ),
    array(
        'q' => 'How long does LIMS implementation take?',
        'a' => "You can start printing bills and reports in about 10 minutes with the standard test catalog. Full implementation - custom test panels, reference ranges, analyzer interfacing and staff training - typically takes a few days to two weeks depending on lab size, with migration from registers or older software handled by Healthray's onboarding team.",
    ),
    array(
        'q' => 'Is there a free trial or free version of the lab software?',
        'a' => 'Yes. You can take a free trial of Healthray\'s LIMS software, or book a free demo where a product specialist walks through your lab\'s daily flow - booking, sample tracking, result entry and report delivery. Small pathology labs can also explore our <a href="https://healthray.com/free-pathology-lab-software/">free pathology lab software</a> plan to get started at no cost.',
    ),
);
?>
<section id="faq" aria-labelledby="faq-title">
  <div class="wrap">
    <div class="section-head center">
      <p class="eyebrow">Common questions</p>
      <h2 id="faq-title">LIMS software FAQs</h2>
    </div>
    <div class="faq-list">
      <?php foreach ( $hr_lims_faq as $hr_faq ) : ?>
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
          <h2 id="cta-title">See a sample travel from collection to signed report</h2>
          <p>A 30-minute demo built around your lab - booking, barcode tracking, analyzer results, QC and report delivery - so you judge it on your daily reality, not slides.</p>
          <ul class="cta-points">
            <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Free trial available - and 10% off premium plans right now</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Migration from registers or old lab software included in every plan</li>
            <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round"><path d="M20 6 9 17l-5-5"/></svg> Support in English, Hindi and Gujarati</li>
          </ul>
        </div>

        <div class="lead-card">
          <h2>Book a free LIMS demo</h2>
          <p>See a sample travel from collection to signed report. Our team replies within one business day. <strong>10% off premium plans</strong> is currently available.</p>
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