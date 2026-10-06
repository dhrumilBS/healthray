<?php

/**
 * Template Name: EMR Software Landing
 * Template Post Type: page
 *
 * @package Healthray_EMR_Software_Template
 */

$hr_media = 'https://healthray.com/wp-content/uploads';

wp_enqueue_script('emr-software', get_stylesheet_directory_uri() . '/js/emr-software.js', array(), '1', true);
?>

<!-- ============ BREADCRUMBS (matches BreadcrumbList schema) ============ -->
<nav class="crumbs" aria-label="Breadcrumb">
    <div class="wrap">
        <ol>
            <li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
            <li><span aria-current="page">EMR Software</span></li>
        </ol>
    </div>
</nav>

<!-- ============ HERO: copy left, compact lead form right ============ -->
<section class="hero" aria-labelledby="hero-title" style="padding-top:42px">
    <div class="wrap hero-grid">
        <div>
            <p class="eyebrow">AI-powered · ABDM compliant · 30+ specialities</p>
            <h1 id="hero-title">EMR software that gives doctors their
                <span class="pulse-word">time back
                    <svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
                        <path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300" />
                    </svg>
                </span>
            </h1>
            <p class="hero-sub">Digital patient records, e-prescriptions, lab orders and billing on one screen - with speciality-specific templates instead of generic forms. From a single doctor's clinic to a multi-speciality hospital.</p>
            <ul class="hero-points">
                <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                        <path d="M20 6 9 17l-5-5" />
                    </svg> Complete a consultation note in under 2 minutes with smart templates</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                        <path d="M20 6 9 17l-5-5" />
                    </svg> ABHA ID creation and ABDM record linking inside the normal workflow</li>
                <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                        <path d="M20 6 9 17l-5-5" />
                    </svg> e-Prescriptions with automatic drug-interaction checks, per MCI guidelines</li>
            </ul>
            <p class="hero-proof"><strong>2,500+ hospitals</strong> <span class="dot"></span> <strong>4M+ prescriptions written</strong> <span class="dot"></span> Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest</p>
        </div>

        <div class="lead-card" id="demo-form">
            <h2>Book a free EMR demo</h2>
            <p>See your speciality's workflows live. Our team replies within one business day.</p>
            <?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]'); ?>
        </div>
    </div>
</section>

<!-- ============ COMPLIANCE STANDARDS ============ -->
<section class="compliance" aria-labelledby="compliance-title">
    <div class="wrap">
        <div class="section-head center">
            <p class="eyebrow">Compliance built in</p>
            <h2 id="compliance-title">One EMR Platform, Every Compliance Standard</h2>
            <p>Healthray is <strong>NABH-certified healthcare software</strong> - listed on the official NABH portal - and HIPAA and ABDM compliant by design. Built for Ayushman Bharat workflows, HL7/FHIR interoperability, SNOMED CT terminology and ICD-10/11 coding, compliance lives at the platform level, so it's never your team's extra work.</p>
        </div>
        <div class="comp-grid">
             <a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal"><img src="<?php echo esc_url($hr_media . '/2026/07/NABH-emr-certified-150x150.webp'); ?>" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span></a>
            <div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/Hipaa-healthray-150x150.webp'); ?>" alt="HIPAA compliant EMR software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
            <div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/abdm-healthray-150x150.webp'); ?>" alt="ABDM compliant EMR - Ayushman Bharat Digital Mission" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
            <div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/abha-healthray-150x150.webp'); ?>" alt="ABHA integrated EMR software" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
            <div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2025/07/NHA.webp'); ?>" alt="NHA approved EMR software" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
            <div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/iso-27001-healthray-150x150.webp'); ?>" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
            <div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2026/03/hl7-healthray-150x150.webp'); ?>" alt="HL7 compatible EMR software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
            <div class="comp-item reveal"><img src="<?php echo esc_url($hr_media . '/2025/07/FHIR.webp'); ?>" alt="FHIR compliant health data interoperability" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
        </div>
    </div>
</section>

<!-- ============ WHAT IS EMR SOFTWARE ============ -->
<section class="define" aria-labelledby="define-title">
    <div class="wrap define-grid">
        <div>
            <p class="eyebrow">The basics</p>
            <h2 id="define-title">What is EMR software?</h2>
            <p style="margin-top:14px">EMR software - Electronic Medical Records software - is the digital replacement for paper patient charts. Instead of files and registers, a doctor records consultations, writes e-prescriptions, orders lab tests and reviews the patient's full history on one screen, with every entry stored securely and available to the care team in real time.</p>
            <p>A good EMR system does more than store records: it structures them. Healthray's EMR uses speciality-specific templates, standard code sets (ICD-10/11, SNOMED CT) and HL7/FHIR interoperability, so records are searchable, shareable across departments, and ready for insurance claims and ABDM linking - not just digital paper.</p>
            <p>The EMR works standalone for clinics and individual practitioners, or as the clinical core of Healthray's complete <a href="<?php echo esc_url(home_url('/')); ?>" style="color:var(--brand);font-weight:600">hospital management system</a> - where OPD/IPD, pharmacy, laboratory and billing all share the same patient record.</p>
        </div>
        <img src="<?php echo esc_url($hr_media . '/2024/05/Revolutionize-Your-Medical-Records.webp'); ?>" alt="Healthray EMR software consultation screen with patient record, e-prescription and lab orders" width="700" height="510" loading="lazy" decoding="async">
    </div>
</section>

<!-- ============ EMR vs EHR ============ -->
<section aria-labelledby="versus-title">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Clearing up the confusion</p>
            <h2 id="versus-title">EMR vs EHR: what's the difference?</h2>
            <p>The terms get used interchangeably, but they mean different things - and knowing the difference helps you buy the right system.</p>
        </div>
        <table class="versus-table">
            <caption class="hp">Comparison of EMR and EHR systems</caption>
            <thead>
                <tr>
                    <th scope="col"></th>
                    <th scope="col">EMR (Electronic Medical Record)</th>
                    <th scope="col">EHR (Electronic Health Record)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Scope</td>
                    <td>The digital patient chart within one practice or hospital</td>
                    <td>The patient's complete health history, designed to travel across providers</td>
                </tr>
                <tr>
                    <td>Primary use</td>
                    <td>Clinical documentation at the point of care - notes, prescriptions, results</td>
                    <td>Care coordination between hospitals, labs, specialists and national systems</td>
                </tr>
                <tr>
                    <td>Sharing</td>
                    <td>Within your facility and its departments</td>
                    <td>Across organizations via interoperability standards (HL7/FHIR, ABDM)</td>
                </tr>
                <tr>
                    <td>Best for</td>
                    <td>Running your clinic or hospital's daily consultations efficiently</td>
                    <td>Longitudinal patient records across the healthcare ecosystem</td>
                </tr>
            </tbody>
        </table>
        <p class="versus-cta">Healthray gives you both: its EMR runs your facility's daily workflow, while HL7/FHIR support and ABDM/ABHA linking give records EHR-grade portability. Managing records across multiple facilities? See our <a href="<?php echo esc_url(home_url('/ehr-software/')); ?>">EHR software</a>.</p>
    </div>
</section>

<!-- ============ FEATURES (12) ============ -->
<section class="spec-hub" id="features" aria-labelledby="features-title">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Everything on one screen</p>
            <h2 id="features-title">EMR software features built around the consultation</h2>
            <p>Every feature exists to shorten the path from "patient walks in" to "patient treated, documented and billed" - with nothing typed twice.</p>
        </div>
        <div class="grid-3">
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M9 15h6M9 11h2" />
                    </svg></span>
                <h3>Speciality clinical templates</h3>
                <p>Structured note templates for 28+ specialities - a cardiologist and a dermatologist see different, relevant fields, so notes are complete in under two minutes.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4z" />
                    </svg></span>
                <h3>e-Prescriptions with interaction checks</h3>
                <p>Generate digital prescriptions that follow MCI guidelines. The drug-interaction checker flags dangerous combinations automatically before a prescription is finalized.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M9 3h6M10 3v6.34L4.72 18.5A2 2 0 0 0 6.46 21.5h11.08a2 2 0 0 0 1.74-3L14 9.34V3" />
                    </svg></span>
                <h3>Lab orders &amp; results</h3>
                <p>Order investigations from the consultation screen; results flow back into the patient record automatically and link to the originating note.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M10.5 20.5 3.5 13.5a4.95 4.95 0 1 1 7-7l7 7a4.95 4.95 0 1 1-7 7zM8.5 8.5l7 7" />
                    </svg></span>
                <h3>Pharmacy integration</h3>
                <p>e-Prescriptions reach the pharmacy counter directly - no re-entry, no transcription errors, with stock and billing handled in the same flow.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="14" rx="2" />
                        <path d="M8 21h8M12 18v3M7 9l3 3 3-4 2 2" />
                    </svg></span>
                <h3>Imaging orders &amp; DICOM viewer</h3>
                <p>Send X-ray and CT orders and view images in original quality inside the platform - radiology order and viewer on a single screen.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M2 7h5M2 12h5M2 17h5M17 7h5M17 12h5M17 17h5" />
                    </svg></span>
                <h3>Billing with ICD-10/11 &amp; claims</h3>
                <p>Invoices auto-suggest ICD-10/11 and CPT codes from the clinical note; insurance and TPA claims are tracked from submission to settlement.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                    </svg></span>
                <h3>Appointments &amp; OPD queue</h3>
                <p>Bookings, reschedules and automated reminders with real-time doctor availability - fewer no-shows and no front-desk chaos on busy OPD days.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M23 7l-7 5 7 5V7zM14 5H3a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2z" />
                    </svg></span>
                <h3>Teleconsultation</h3>
                <p>Run video consultations with clinical notes, e-prescriptions and lab orders on the same screen - no context lost between in-person and remote visits.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 11l-3 3-2-2" />
                    </svg></span>
                <h3>Patient health portal</h3>
                <p>Patients access their records, prescriptions and lab reports themselves - fewer phone calls to your front desk, better follow-up compliance.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                    </svg></span>
                <h3>ABDM &amp; ABHA built in</h3>
                <p>Create and verify ABHA IDs at registration and link records to the Ayushman Bharat Digital Mission - inside the workflow, not as separate software.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17.5 19a4.5 4.5 0 1 0-.42-8.98 6 6 0 1 0-11.5 2.2A3.5 3.5 0 0 0 6.5 19z" />
                    </svg></span>
                <h3>Cloud access &amp; mobile app</h3>
                <p>Secure cloud infrastructure with 99.9% uptime - doctors review records from any device, anywhere, through the browser or the mobile EMR app.</p>
            </div>
            <div class="card reveal">
                <span class="icon"><svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="11" width="18" height="10" rx="2" />
                        <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                    </svg></span>
                <h3>Security &amp; audit trails</h3>
                <p>Encryption in transit and at rest, role-based access, and a logged trail of every view and edit - HIPAA-aligned and ready for compliance reviews.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ SPECIALITY LINK HUB ============ -->
<section id="specialities" aria-labelledby="spec-title">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Built for your practice</p>
            <h2 id="spec-title">EMR software for every speciality</h2>
            <p>Generic EMRs force every doctor into the same form. Healthray ships 28+ speciality-specific workflows - pick yours to see templates, features and doctor stories for your practice.</p>
        </div>
        <div class="spec-grid">
            <?php
            $hr_spec_links = array(
                'emr-software-for-cardiologist'         => 'Cardiology',
                'emr-software-for-orthopedics'          => 'Orthopedics',
                'emr-software-for-gastroenterologists'  => 'Gastroenterology',
                'emr-software-for-gynaecologists'       => 'Gynaecology',
                'emr-software-for-pediatric'            => 'Pediatrics',
                'emr-software-for-urologist'            => 'Urology',
                'emr-software-for-nephrologists'        => 'Nephrology',
                'emr-software-for-pulmonologists'       => 'Pulmonology',
                'emr-software-for-oncologist'           => 'Oncology',
                'emr-software-for-dermatologist'        => 'Dermatology',
                'emr-software-for-ophthalmologist'      => 'Ophthalmology',
                'emr-software-for-neurologist'          => 'Neurology',
                'emr-software-for-neuropsychiatrists'   => 'Neuropsychiatry',
                'emr-software-for-ent-surgeon'          => 'ENT',
                'emr-software-for-endocrinologist'      => 'Endocrinology',
                'emr-software-for-diabetologist'        => 'Diabetology',
                'emr-software-for-hematologist'         => 'Hematology',
                'emr-software-for-rheumatologist'       => 'Rheumatology',
                'emr-software-for-general-surgeon'      => 'General Surgery',
                'emr-software-for-general-medicine'     => 'General Medicine',
                'emr-software-for-internal-medicine'    => 'Internal Medicine',
                'emr-software-for-family-medicine'      => 'Family Medicine',
                'emr-software-for-consultant-physicians' => 'Consultant Physicians',
                'emr-software-for-dental-care'          => 'Dental Care',
                'emr-software-for-nutritionist'         => 'Nutrition',
                'emr-software-for-ayurveda'             => 'Ayurveda',
                'emr-software-for-homeopathy'           => 'Homeopathy',
                'emr-software-for-osteopathy'           => 'Osteopathy',
            );
            foreach ($hr_spec_links as $hr_slug => $hr_label) :
            ?>
                <a href="<?php echo esc_url(home_url('/' . $hr_slug . '/')); ?>"><?php echo esc_html($hr_label); ?></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ============ IMPLEMENTATION STEPS ============ -->
<section class="spec-hub steps" aria-labelledby="steps-title">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Go-live in days, not months</p>
            <h2 id="steps-title">EMR implementation in <br/>5 simple steps</h2>
            <p>A dedicated onboarding team handles migration, setup and training. Most clinics are live in about 7 days; larger hospitals within 2–4 weeks.</p>
        </div>
        <div class="steps-track reveal" aria-hidden="true">
            <svg viewBox="0 0 1200 56" preserveAspectRatio="none">
                <path d="M0 28 H84 l8-12 12 22 8-16 6 6 H324 l8-12 12 22 8-16 6 6 H564 l8-12 12 22 8-16 6 6 H804 l8-12 12 22 8-16 6 6 H1044 l8-12 12 22 8-16 6 6 H1200" />
            </svg>
            <i></i><i></i><i></i><i></i><i></i>
        </div>
        <div class="steps-grid reveal">
            <div class="step">
                <h3>Setup &amp; onboarding</h3>
                <p>We configure your speciality workflows, migrate existing patient data and train your team.</p>
            </div>
            <div class="step">
                <h3>Patient registration</h3>
                <p>Demographics, insurance details and consent forms are captured once and feed the record from day one.</p>
            </div>
            <div class="step">
                <h3>Consultation &amp; notes</h3>
                <p>SOAP notes, e-prescriptions and investigation orders - completed from a single consultation screen.</p>
            </div>
            <div class="step">
                <h3>Billing &amp; claims</h3>
                <p>Invoices with ICD-10/11 and CPT suggestions, claim submission and reconciliation without manual work.</p>
            </div>
            <div class="step">
                <h3>Reports &amp; analytics</h3>
                <p>Revenue, productivity, diagnosis and claim reports - pre-built and ready from day one.</p>
            </div>
        </div>
    </div>
</section>

<!-- ============ STATS ============ -->
<?php echo do_shortcode('[hr_stats]'); ?>


<!-- ============ CLIENTS (10) ============ -->
<?php echo do_shortcode('[hr_clients eyebrow="Trusted across India"]'); ?>


<section class="spec-hub" aria-labelledby="video-title">
    <div class="wrap">
        <div class="section-head">
            <p class="eyebrow">Seen under real pressure</p>
            <h2 id="video-title">How Healthray's EMR Works Inside a Real Hospital Setting</h2>
            <p>The best way to understand an EMR is to watch it running on a real OPD floor - not in a sales deck.</p>
        </div>
        <div class="video-grid">
            <div class="video-wrap" id="videoFacade" role="button" tabindex="0" aria-label="Play video: An Operations Leader's Perspective - Divyesh Gandhi, Universal Hospital, Surat">
                <img src="https://i.ytimg.com/vi/VRHZ9ejnBWk/oardefault.jpg" alt="Divyesh Gandhi, Head of Operations at Universal Hospital Surat, on using Healthray daily across departments" loading="lazy" decoding="async" width="360" height="640">
                <button class="play" aria-hidden="true" tabindex="-1"><svg viewBox="0 0 24 24">
                        <path d="M8 5v14l11-7z" />
                    </svg></button>
            </div>
            <div class="video-side">
                <h3>An Operations Leader's Perspective</h3>
                <p>Divyesh Gandhi, Head of Operations at Universal Hospital, Surat, shares his first-hand experience running Healthray across departments daily - how clinical and administrative teams adapted quickly, and how operational visibility improved from day one.</p>
                <ul class="video-points">
                    <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg> Day-to-day processes become easier to track and control</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg> Teams adapt quickly with minimal training</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg> Better coordination between departments on shared, reliable data</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg> Higher accuracy in reporting and operational oversight</li>
                    <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                            <path d="M20 6 9 17l-5-5" />
                        </svg> A system that keeps supporting the hospital as it scales</li>
                </ul>
                <div class="video-cta">
                    <a class="btn btn-primary" href="#demo-form">See it on your workflows</a>
                    <a class="more-link" href="<?php echo esc_url(home_url('/case-studies/')); ?>">More customer stories →</a>
                </div>
                <div class="ratings">
                    <span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.8 <span>Capterra</span></span>
                    <span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.8 <span>SoftwareSuggest</span></span>
                    <span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 5.0 <span>G2</span></span>
                    <span class="rating"><span class="stars" aria-hidden="true">★★★★★</span> 4.5 <span>Trustpilot</span></span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ============ FAQ (matches FAQPage schema word-for-word) ============ -->
<section id="faq" aria-labelledby="faq-title">
    <div class="wrap">
        <div class="section-head center">
            <p class="eyebrow">Common questions</p>
            <h2 id="faq-title">EMR software FAQs</h2>
        </div>
        <div class="faq-list">
            <?php
            $faq_entities = array(
                array('q' => 'What is EMR software?', 'a' => "EMR software (Electronic Medical Records software) is a digital system that replaces paper patient charts. Doctors use it to record consultations, write e-prescriptions, order lab tests and view a patient's complete medical history in one place. Healthray's EMR adds speciality-specific templates, ABDM/ABHA integration and billing, so the whole clinical workflow runs on a single screen."),
                array('q' => 'What is the difference between EMR and EHR?', 'a' => "An EMR (Electronic Medical Record) is the digital patient chart used within one practice or hospital - consultations, prescriptions and results recorded at the point of care. An EHR (Electronic Health Record) is broader: it is designed to travel with the patient and share records across multiple providers and systems. In short: EMR = one facility's clinical record; EHR = the patient's portable, interoperable health history. Healthray supports both - its EMR runs your facility, and HL7/FHIR plus ABDM linking give records EHR-grade portability."),
                array('q' => 'How much does EMR software cost in India?', 'a' => 'EMR software pricing in India depends on the number of doctors, locations and modules you need - a single-doctor clinic pays far less than a multi-speciality hospital. Healthray offers flexible plans with no cost penalty for small practices, plus a free trial. Book a demo and we will share a quote matched to your practice size.'),
                array('q' => "Is Healthray's EMR software ABDM compliant?", 'a' => 'Yes. Healthray is NHA-approved and ABDM-compliant. You can create and verify ABHA IDs during registration, link records to the Ayushman Bharat Digital Mission ecosystem, and generate e-prescriptions that follow MCI digital prescription guidelines - all inside the normal consultation workflow.'),
                array('q' => 'Which specialities does the EMR support?', 'a' => "Healthray's EMR ships with clinical templates for 28+ specialities, including cardiology, orthopedics, gastroenterology, gynaecology, pediatrics, urology, nephrology, pulmonology, oncology, dermatology, ENT, general and internal medicine, dental care, Ayurveda and homeopathy. Each speciality gets its own documentation flows rather than a generic form."),
                array('q' => 'How long does EMR implementation take?', 'a' => "Single-doctor clinics typically go live in about 7 days. Mid-size hospitals take 1–2 weeks, and large multi-speciality hospitals with complex departments take 3–4 weeks. Healthray's onboarding team handles data migration, workflow setup and staff training, so consultations continue without interruption."),
                array('q' => 'Is patient data secure in a cloud-based EMR?', 'a' => 'Yes. Healthray encrypts patient data in transit and at rest, enforces role-based access control, logs every view and edit in audit trails, and backs data up continuously on redundant cloud infrastructure with 99.9% uptime. Records are structured on HL7/FHIR, SNOMED CT and ICD-10/11 standards, and handling aligns with Indian data-protection requirements.'),
                array('q' => 'Is there a free trial or demo of the EMR software?', 'a' => "Yes. You can take a free trial of Healthray's EMR software, or book a free 30-minute demo where a product specialist walks through your speciality's workflows - appointments, clinical notes, e-prescriptions and billing. No credit card is required."),
            );
            foreach ($faq_entities as $hr_faq) : ?>
                <details>
                    <summary><?php echo esc_html($hr_faq['q']); ?></summary>
                    <div class="answer"><?php echo esc_html($hr_faq['a']); ?></div>
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
                    <h2 id="cta-title">See the EMR on your own workflows</h2>
                    <p>A 30-minute demo built around your speciality - templates, e-prescriptions, lab orders and billing - so you can judge it on your daily reality, not slides.</p>
                    <ul class="cta-points">
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg> Free trial available - no credit card required</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg> Data migration &amp; staff training included in every plan</li>
                        <li><svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round">
                                <path d="M20 6 9 17l-5-5" />
                            </svg> Support in English, Hindi and Gujarati</li>
                    </ul>
                </div>

                <div class="lead-card">
                    <h2>Book a free EMR demo</h2>
                    <p>See your speciality's workflows live. Our team replies within one business day.</p>
                    <?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]'); ?>
                </div>
            </div>
            <svg class="ecg-bg" viewBox="0 0 1200 70" preserveAspectRatio="none" aria-hidden="true">
                <path d="M0 35 H200 l15-18 20 36 15-30 12 12 H480 l14-22 18 40 14-26 10 8 H800 l15-18 20 36 15-30 12 12 H1200" />
            </svg>
        </div>
    </div>
</section>