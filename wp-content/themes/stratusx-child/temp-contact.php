<?php

/**
 * Template Name: Contact Us
 * Page ID: 167  (https://healthray.com/contact/)
 * @package stratusx-child
 */

$hr_home = home_url('/');
$hr_phone_field = get_field('talk_to_team', 'option');
$hr_email_field = get_field('customer_support_email', 'option');
$hr_address     = get_field('company_address', 'option');

$hr_phone_label = ! empty($hr_phone_field['title']) ? $hr_phone_field['title'] : '+91-971-487-4435';
$hr_phone_href  = ! empty($hr_phone_field['url']) ? $hr_phone_field['url'] : 'tel:+919714874435';
$hr_email_label = ! empty($hr_email_field['title']) ? $hr_email_field['title'] : 'contact@healthray.com';
$hr_email_href  = ! empty($hr_email_field['url']) ? $hr_email_field['url'] : 'mailto:contact@healthray.com';
$hr_address     = ! empty($hr_address) ? $hr_address : '1st Floor, A - Millenium Point, Opp. Gabani Kidney Hospital, Station Rd, Surat 395003, Gujarat, India';

/*
 * Google Maps short link for the office.
 *
 * This must be a share link taken from Healthray's own Google Business
 * listing, because it carries the place ID. Do not replace it with a
 * ?q=<address> URL: the office shares a building with other companies, and an
 * address-only query resolves to whichever business Google has listed there -
 * which previously rendered a different company's name on this page.
 */
$hr_map_link = 'https://maps.app.goo.gl/xGwX7XgajeWGkHgPA';

// Support mailbox with a subject line, so tickets arrive pre-triaged.
$hr_support_href = $hr_email_href . '?subject=' . rawurlencode('Support request - existing Healthray customer');

if (! function_exists('hr_contact_tick')) {
    /**
     * Inline check icon reused by the hero and CTA lists.
     *
     * @return string
     */
    function hr_contact_tick()
    {
        return '<svg viewBox="0 0 24 24" fill="none" stroke-width="2.2" stroke-linecap="round" aria-hidden="true"><path d="M20 6 9 17l-5-5"/></svg>';
    }
}
?>
<main id="contact-main">

    <!-- ============ BREADCRUMBS ============ -->
    <nav class="crumbs" aria-label="Breadcrumb">
        <div class="wrap">
            <ol>
                <li><a href="<?php echo esc_url($hr_home); ?>">Home</a></li>
                <li><span aria-current="page">Contact</span></li>
            </ol>
        </div>
    </nav>

    <!-- ============ HERO + PRIMARY FORM ============ -->
    <section class="hero" aria-labelledby="hero-title" style="padding-top:38px">
        <div class="wrap hero-grid">
            <div>
                <p class="eyebrow">Contact us &middot; Sales &middot; Support &middot; Partnerships</p>
                <p class="sla-pill"><span class="pip" aria-hidden="true"></span> 97% of enquiries answered in under 30 minutes</p>
                <h1 id="hero-title">Contact Healthray and hear back within
                    <span class="pulse-word">one business day
                        <svg viewBox="0 0 300 26" aria-hidden="true" preserveAspectRatio="none">
                            <path class="ecg-path" d="M0 13 H70 l8-9 10 18 8-16 6 7 H140 l7-11 9 20 7-13 5 4 H300" />
                        </svg>
                    </span>
                </h1>
                <p class="hero-sub">A demo of the hospital management software, a price for your facility, help with a system that is already live, or a partnership conversation - send it once and the right specialist picks it up.</p>
                <ul class="hero-points">
                    <li><?php echo hr_contact_tick(); ?> You reach a product specialist who knows Indian hospital workflows, not a call queue</li>
                    <li><?php echo hr_contact_tick(); ?> Sales, support, partner and press enquiries all routed from this one form</li>
                    <li><?php echo hr_contact_tick(); ?> No obligation and no sales pressure - most conversations start with a 15-minute call</li>
                </ul>
                <div class="quick-actions">
                    <a class="btn btn-primary" href="<?php echo esc_url($hr_phone_href); ?>">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                        Call <?php echo esc_html($hr_phone_label); ?>
                    </a>
                    <a class="btn btn-ghost" href="<?php echo esc_url($hr_email_href); ?>">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                            <path d="m22 7-8.99 5.73a2 2 0 0 1-2.02 0L2 7" />
                        </svg>
                        Email us
                    </a>
                </div>
                <p class="hero-proof">
                    <strong>2,500+ hospitals</strong> <span class="dot"></span>
                    <strong>5M+ patient records</strong> <span class="dot"></span>
                    <strong>5+ countries</strong> <span class="dot"></span>
                    Rated <strong>4.8/5</strong> on Capterra &amp; SoftwareSuggest
                </p>
            </div>

            <div class="lead-card" id="contact-form">
                <h2>Tell us what you need</h2>
                <p>One form for demos, pricing, support and partnerships. Your details are used only to reply to this enquiry.</p>
                <?php echo do_shortcode('[contact-form-7 id="da67515" title="Contact Page CTA"]'); ?>
            </div>
        </div>
    </section>

    <!-- ============ COMPLIANCE / TRUST ============ -->
    <section class="compliance" aria-labelledby="compliance-title">
        <div class="wrap">
            <div class="section-head center">
                <p class="eyebrow">Why hospitals take the call</p>
                <h2 id="compliance-title">NABH certified, ABDM compliant, NHA approved</h2>
                <p>Healthray is <strong>NABH-certified healthcare software</strong> listed on the official NABH portal, and it is HIPAA and ABDM compliant by design. Before you spend time on a demo, the compliance question is already answered.</p>
            </div>
            <div class="comp-grid">
                <a class="comp-item reveal" href="https://nabh.co/software/healthray/" target="_blank" rel="noopener" title="View Healthray's listing on the official NABH portal">
                    <img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="NABH certified healthcare software - Healthray listed on the official NABH portal" loading="lazy" decoding="async" width="56" height="56"><span>NABH Certified</span>
                </a>
                <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp" alt="HIPAA compliant hospital management software" loading="lazy" decoding="async" width="56" height="56"><span>HIPAA</span></div>
                <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp" alt="ABDM compliant hospital management software" loading="lazy" decoding="async" width="56" height="56"><span>ABDM</span></div>
                <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/abha-healthray-150x150.webp" alt="ABHA integrated hospital software" loading="lazy" decoding="async" width="56" height="56"><span>ABHA</span></div>
                <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved hospital management system" loading="lazy" decoding="async" width="56" height="56"><span>NHA Approved</span></div>
                <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001 certified information security" loading="lazy" decoding="async" width="56" height="56"><span>ISO 27001</span></div>
                <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="HL7 interoperable healthcare software" loading="lazy" decoding="async" width="56" height="56"><span>HL7</span></div>
                <div class="comp-item reveal"><img src="https://healthray.com/wp-content/uploads/2025/07/FHIR.webp" alt="FHIR compliant healthcare data interoperability" loading="lazy" decoding="async" width="56" height="56"><span>FHIR</span></div>
            </div>
        </div>
    </section>

    <!-- ============ CONTACT CHANNELS ============ -->
    <section aria-labelledby="channels-title">
        <div class="wrap">
            <div class="section-head">
                <p class="eyebrow">Direct lines</p>
                <h2 id="channels-title">Reach us the way you prefer</h2>
                <p>Four ways in. Pick whichever suits the question - they all land with the same team.</p>
            </div>
            <div class="channel-grid">
                <a class="channel reveal" href="<?php echo esc_url($hr_phone_href); ?>">
                    <span class="c-ico">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </span>
                    <h3>Sales &amp; demos</h3>
                    <span class="c-value"><?php echo esc_html($hr_phone_label); ?></span>
                    <p>Products, modules, pricing and demo scheduling. Fastest route if you want an answer today.</p>
                    <span class="c-meta">Phone &middot; IST working hours</span>
                </a>

                <a class="channel reveal" href="<?php echo esc_url($hr_email_href); ?>">
                    <span class="c-ico">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M4 4h16a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                            <path d="m22 7-8.99 5.73a2 2 0 0 1-2.02 0L2 7" />
                        </svg>
                    </span>
                    <h3>General enquiries</h3>
                    <span class="c-value"><?php echo esc_html($hr_email_label); ?></span>
                    <p>Detailed requirements, tender documents, RFPs or anything that needs an attachment.</p>
                    <span class="c-meta">Email &middot; Reply in 1 business day</span>
                </a>

                <a class="channel reveal" href="<?php echo esc_url($hr_support_href); ?>">
                    <span class="c-ico">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z" />
                        </svg>
                    </span>
                    <h3>Existing customer support</h3>
                    <span class="c-value">Raise a support ticket</span>
                    <p>Already live on Healthray? Email support with your hospital name and priority, or call your account manager.</p>
                    <span class="c-meta">Phone &middot; Email &middot; Ticketing</span>
                </a>

                <a class="channel reveal" href="<?php echo esc_url($hr_map_link); ?>" target="_blank" rel="noopener">
                    <span class="c-ico">
                        <svg viewBox="0 0 24 24" aria-hidden="true">
                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                            <circle cx="12" cy="10" r="3" />
                        </svg>
                    </span>
                    <h3>Visit the office</h3>
                    <span class="c-value">Surat, Gujarat</span>
                    <p>Healthray Technologies Pvt. Ltd., A - Millenium Point, opposite Gabani Kidney Hospital.</p>
                    <span class="c-meta">Open in Google Maps</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ============ INTENT ROUTING ============ -->
    <section class="define route" aria-labelledby="route-title">
        <div class="wrap">
            <div class="section-head">
                <p class="eyebrow">Get to the right place faster</p>
                <h2 id="route-title">What do you need help with?</h2>
                <p>Most people arrive here with one of six questions. If yours is on this list, the shortcut saves you a round trip.</p>
            </div>
            <div class="grid-3">
                <a class="card reveal" href="#contact-form">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M8 2v4M16 2v4M3 10h18M5 4h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2z" />
                        </svg>
                    </span>
                    <h3>Book a product demo</h3>
                    <p>See HMS, EMR, EHR, pharmacy or lab modules running against your own OPD, IPD or billing flow - not a generic tour.</p>
                    <span class="more">Use the enquiry form &rarr;</span>
                </a>

                <a class="card reveal" href="<?php echo esc_url($hr_home . 'pricing/'); ?>">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </span>
                    <h3>Pricing for your facility</h3>
                    <p>Plans for clinics, hospitals, labs and pharmacies, plus what changes the number - bed count, branches and modules.</p>
                    <span class="more">See pricing &rarr;</span>
                </a>

                <a class="card reveal" href="<?php echo esc_url($hr_support_href); ?>">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M12 22a10 10 0 1 0-10-10v3a3 3 0 0 0 3 3h1v-6H5M19 12v6h-1a3 3 0 0 1-3-3v-3z" />
                        </svg>
                    </span>
                    <h3>Support for a live system</h3>
                    <p>Already using Healthray? Email support with your hospital name and priority level, and it is triaged against the targets below.</p>
                    <span class="more">Email support &rarr;</span>
                </a>

                <a class="card reveal" href="<?php echo esc_url($hr_home . 'become-a-partner/'); ?>">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2M9 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8zM22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75" />
                        </svg>
                    </span>
                    <h3>Become a channel partner</h3>
                    <p>Consultants, resellers and healthcare IT firms selling into hospitals, labs and pharmacies across India and abroad.</p>
                    <span class="more">Partner programme &rarr;</span>
                </a>

                <a class="card reveal" href="<?php echo esc_url($hr_home . 'case-studies/'); ?>">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8zM14 2v6h6M9 15h6M9 11h2" />
                        </svg>
                    </span>
                    <h3>See what other hospitals did</h3>
                    <p>Implementation stories from hospitals, clinic chains and diagnostic labs - what changed, and how long it took.</p>
                    <span class="more">Read case studies &rarr;</span>
                </a>

                <a class="card reveal" href="<?php echo esc_url($hr_home . 'reviews/'); ?>">
                    <span class="icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="m12 2 3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01z" />
                        </svg>
                    </span>
                    <h3>Check verified reviews</h3>
                    <p>Ratings and written reviews from doctors and hospital administrators on Capterra, SoftwareSuggest, G2 and Trustpilot.</p>
                    <span class="more">Read reviews &rarr;</span>
                </a>
            </div>
        </div>
    </section>

    <!-- ============ OFFICE ============
         No embedded map here, deliberately. A Google Maps iframe built from a
         plain address query has no place ID, so Google resolves it to whichever
         business it has listed at that building - which rendered a different
         company's name on our own contact page. The short link below carries
         Healthray's place ID and resolves correctly, so we link out instead of
         embedding. It also drops a third-party iframe and its cookies from the
         page. If an embed is ever wanted again, build it from the place ID, not
         from the address string.

         Phone and email are intentionally absent: they already have their own
         cards in the "Reach us the way you prefer" section above, and repeating
         them here was duplicate content for no gain. This section answers one
         question only - where is the office and how do I get there.
    -->
    <section aria-labelledby="office-title">
        <div class="wrap">
            <div class="section-head">
                <p class="eyebrow">Head office</p>
                <h2 id="office-title">Visit the Healthray office in Surat</h2>
                <p>Walk-ins are welcome, though calling ahead means the right person is free when you arrive.</p>
            </div>
            <div class="office-card reveal">
                <span class="office-pin" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </span>
                <div class="office-body">
                    <h3>Healthray Technologies Pvt. Ltd.</h3>
                    <address><?php echo nl2br(esc_html($hr_address)); ?></address>
                    <p class="office-hint">Millenium Point is directly opposite Gabani Kidney Hospital on Station Road.</p>
                </div>
                <div class="office-actions">
                    <a class="btn btn-primary" href="<?php echo esc_url($hr_map_link); ?>" target="_blank" rel="noopener">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            <path d="M3 11l19-9-9 19-2-8-8-2z" />
                        </svg>
                        Get directions
                    </a>
                    <span class="office-meta">Opens Google Maps in a new tab</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ WHAT HAPPENS NEXT ============ -->
    <section class="define" aria-labelledby="next-title">
        <div class="wrap">
            <div class="section-head">
                <p class="eyebrow">No black box</p>
                <h2 id="next-title">What happens after you contact us</h2>
                <p>Four steps from your message to a written quote. You can stop at any of them.</p>
            </div>
            <div class="steps">
                <div class="step reveal">
                    <h3>A specialist reads it</h3>
                    <p>Your enquiry is routed by facility type - hospital, clinic, laboratory or pharmacy - so the first reply already knows your workflow.</p>
                    <span class="when">Under 30 minutes, working hours</span>
                </div>
                <div class="step reveal">
                    <h3>A short discovery call</h3>
                    <p>Fifteen minutes on your departments, your current software and what is actually breaking. No slide deck yet.</p>
                    <span class="when">Day 1 to 2</span>
                </div>
                <div class="step reveal">
                    <h3>A demo on your workflows</h3>
                    <p>Thirty to forty-five minutes walking your own OPD, IPD, billing or lab flow through the platform, with your team in the room.</p>
                    <span class="when">Usually within a week</span>
                </div>
                <div class="step reveal">
                    <h3>A written quote and plan</h3>
                    <p>Module list, pricing for your size, migration scope and a go-live timeline - in writing, so you can compare it against anyone else.</p>
                    <span class="when">After the demo</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ EXISTING CUSTOMER SUPPORT ============ -->
    <section aria-labelledby="support-title">
        <div class="wrap">
            <div class="section-head">
                <p class="eyebrow">Already a customer</p>
                <h2 id="support-title">How Healthray support works</h2>
                <p>Support runs on phone, email and ticketing during working hours. Tickets are triaged by priority rather than by arrival order, so a ward that cannot bill is never queued behind a feature request.</p>
            </div>
            <table class="sla-table">
                <caption>Response and resolution targets by priority, as published on our FAQ page.</caption>
                <thead>
                    <tr>
                        <th scope="col">Priority</th>
                        <th scope="col">First response</th>
                        <th scope="col">Target resolution</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <th scope="row">Critical - system down</th>
                        <td>1 hour</td>
                        <td>4 hours</td>
                    </tr>
                    <tr>
                        <th scope="row">Medium - minor issue</th>
                        <td>4 hours</td>
                        <td>2 days</td>
                    </tr>
                    <tr>
                        <th scope="row">Low - feature request</th>
                        <td>5 days</td>
                        <td>Next upgrade release</td>
                    </tr>
                </tbody>
            </table>
            <p class="versus-cta">Looking for product answers rather than a person? The <a href="<?php echo esc_url($hr_home . 'faqs/'); ?>">Healthray FAQ library</a> covers implementation, training, integrations and deployment options in detail.</p>
        </div>
    </section>

    <!-- ============ TESTIMONIALS ============ -->
    <section class="define" aria-labelledby="testi-title">
        <div class="wrap">
            <div class="section-head">
                <p class="eyebrow">They started with one call too</p>
                <h2 id="testi-title">What hospital teams say about working with us</h2>
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
                        <div><b>Dr. Vimal Dhaduk</b><span>GI Surgery, VR Group of Hospitals</span></div>
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

    <!-- ============ FAQ (mirrors the FAQPage schema for page 167) ============ -->
    <section id="faq" aria-labelledby="faq-title">
        <div class="wrap">
            <div class="section-head center">
                <p class="eyebrow">Before you write</p>
                <h2 id="faq-title">Contact and support FAQs</h2>
            </div>
            <div class="faq-list">
                <details>
                    <summary>How quickly will someone reply to my enquiry?</summary>
                    <div class="answer">97% of enquiries get a first response in under 30 minutes during working hours, and every enquiry is answered within one business day. Phone is fastest if you need an answer the same day; email suits detailed requirements, tender documents and anything with an attachment.</div>
                </details>
                <details>
                    <summary>Is the demo free, and how long does it take?</summary>
                    <div class="answer">Yes, the demo is free and there is no credit card or commitment involved. It usually runs 30 to 45 minutes and is built around your own workflow - OPD and queues, IPD and billing, laboratory reporting or pharmacy counter - rather than a generic product tour. Most teams have a short discovery call first so the demo is worth the time.</div>
                </details>
                <details>
                    <summary>How do I get pricing for my hospital, clinic or lab?</summary>
                    <div class="answer">Pricing depends on your facility size, number of branches and which modules you switch on, so we quote per facility rather than publish one number. Plan structures for clinics, hospitals, labs and pharmacies are on the <a href="<?php echo esc_url($hr_home . 'pricing/'); ?>">pricing page</a>, and a written quote follows the demo - module list, migration scope and go-live timeline included.</div>
                </details>
                <details>
                    <summary>I already use Healthray. How do I raise a support ticket?</summary>
                    <div class="answer">Email <a href="<?php echo esc_url($hr_support_href); ?>"><?php echo esc_html($hr_email_label); ?></a> with your hospital name and the priority level, or call your account manager on <?php echo esc_html($hr_phone_label); ?>. Support runs on phone, email and ticketing during working hours, with targets of one hour for critical system-down issues, four hours for minor issues and five days for feature requests.</div>
                </details>
                <details>
                    <summary>Do you work with hospitals outside India?</summary>
                    <div class="answer">Yes. Healthray runs in 5+ countries alongside its Indian base of 2,500+ hospitals, clinics, laboratories and pharmacies. Mention your country and regulatory requirements in the form and the reply will cover deployment model, data residency and local compliance rather than sending you Indian-only material.</div>
                </details>
                <details>
                    <summary>Can you help with a tender, RFP or government procurement?</summary>
                    <div class="answer">Yes. Send the tender document or RFP to <a href="<?php echo esc_url($hr_email_href); ?>"><?php echo esc_html($hr_email_label); ?></a> with the submission deadline. Our team supports technical compliance sheets, deployment declarations for on-premise or hybrid requirements, and phased institutional rollout plans for government, trust and medical college hospitals.</div>
                </details>
                <details>
                    <summary>I want to resell or implement Healthray. Who do I speak to?</summary>
                    <div class="answer">Consultants, resellers and healthcare IT firms should start on the <a href="<?php echo esc_url($hr_home . 'become-a-partner/'); ?>">become a partner</a> page, which covers the channel programme and commercial model. You can also use the form on this page and choose "Channel Partner / Consultant" as your business type.</div>
                </details>
                <details>
                    <summary>Where is the Healthray office, and can I visit?</summary>
                    <div class="answer">Healthray Technologies Pvt. Ltd. is at <?php echo esc_html($hr_address); ?>. Visitors are welcome - call <?php echo esc_html($hr_phone_label); ?> first so the right person is free when you arrive. <a href="<?php echo esc_url($hr_map_link); ?>" target="_blank" rel="noopener">Directions are on Google Maps</a>.</div>
                </details>
            </div>
        </div>
    </section>

    <!-- ============ FINAL CTA ============ -->
    <section aria-labelledby="cta-title" style="padding-top:0">
        <div class="wrap">
            <div class="cta">
                <div class="cta-grid">
                    <div class="cta-copy">
                        <h2 id="cta-title">Prefer to just talk to someone?</h2>
                        <p>Call the number below and you will be speaking to a specialist, not routed through a menu. Fifteen minutes is usually enough to know whether this is worth a demo.</p>
                        <ul class="cta-points">
                            <li><?php echo hr_contact_tick(); ?> Free demo and free trial - no credit card required</li>
                            <li><?php echo hr_contact_tick(); ?> Migration from your existing system included in every implementation</li>
                            <li><?php echo hr_contact_tick(); ?> Tender documentation and on-premise deployment support available</li>
                        </ul>
                        <div class="cta-actions">
                            <a class="btn btn-light" href="<?php echo esc_url($hr_phone_href); ?>">
                                <svg viewBox="0 0 24 24" aria-hidden="true">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.7 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.34 1.85.57 2.81.7A2 2 0 0 1 22 16.92z" />
                                </svg>
                                <?php echo esc_html($hr_phone_label); ?>
                            </a>
                            <a class="btn btn-outline" href="#contact-form">Send a message instead</a>
                        </div>
                    </div>
                    <div class="cta-copy" style="align-self:center">
                        <p style="font-size:.95rem"><strong style="color:#fff">Not ready to talk?</strong><br>
                            Read how other hospitals rolled this out in our <a href="<?php echo esc_url($hr_home . 'case-studies/'); ?>" style="color:#fff;text-decoration:underline;text-underline-offset:3px">case studies</a>, compare plans on the <a href="<?php echo esc_url($hr_home . 'pricing/'); ?>" style="color:#fff;text-decoration:underline;text-underline-offset:3px">pricing page</a>, or browse the <a href="<?php echo esc_url($hr_home . 'faqs/'); ?>" style="color:#fff;text-decoration:underline;text-underline-offset:3px">FAQ library</a>. Nothing here needs a form first.</p>
                    </div>
                </div>
                <svg class="ecg-bg" viewBox="0 0 1200 70" preserveAspectRatio="none" aria-hidden="true">
                    <path d="M0 35 H200 l15-18 20 36 15-30 12 12 H480 l14-22 18 40 14-26 10 8 H800 l15-18 20 36 15-30 12 12 H1200" />
                </svg>
            </div>
        </div>
    </section>

</main>