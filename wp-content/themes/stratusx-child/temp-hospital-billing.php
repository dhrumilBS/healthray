<?php
/**
 * Template Name: Healthray - Hospital Billing Software
 * Template Post Type: page
 *
 * Page:   /modules/hospital-billing-software/ (page 82741, child of /modules/ 82372). Organic SEO page: indexed.
 * Source: marketing's package "healthray-hospital-billing-wordpress" (25 Nov 2026). Words, pictures and spacing are approved: do not change.
 * Files:  css/hospital-billing.css and js/hospital-billing.js (enqueued in functions.php). Images and video: media
 *         library (uploads/2026/11 for this page's own pictures; existing faces, badges, photos) and assets/ppc-hims/ logos.
 *         CSS and JS are enqueued in functions.php (is_page(82741)): base.php prints <head> before this file runs,
 *         so the package's own get_header()/wp_enqueue_scripts lines were removed. Mulish already loads site-wide.
 * Form:   the PPC Form (CF7 11eec7d) in the hero card and in the site popup. Its 2 steps and the "+91" box run in
 *         js/hospital-billing.js (ppc-hims.js loads only on PPC pages). Every "Book a free demo" button opens the popup.
 * SEO:    title and description are set in Yoast for this page.
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

// Lead form: the PPC Form (CF7 11eec7d, post 81836), in the hero card and in the site popup (#myPopup)
$hr_bill_form = '[contact-form-7 id="11eec7d" title="PPC Form"]';

// Site popup: same form, heading and line as the hero card. The 8 "Book a demo" buttons have .hr-cta-btn,
// so js/script.js opens it (templates/footer.php -> templates/lead-popup.php).
add_filter( 'hr_ppc_popup_args', static function () use ( $hr_bill_form ) {
	return array(
		'form'  => $hr_bill_form,
		'title' => 'See your own bills in a free demo',
		'text'  => 'In a 30-minute video call, we make an OPD bill, an IPD bill and a cashless bill for a hospital of your size.',
	);
} );

// Pictures, video and poster made for this page: media library, uploads/2026/11 (same file names as marketing's package)
$hr_up   = 'https://healthray.com/wp-content/uploads/2026/11/';
// Hospital logos used across the site (theme folder). Faces, badges and hospital photos: existing media library files.
$hr_logo = get_stylesheet_directory_uri() . '/assets/ppc-hims/';
?>


<div class="hr-billing">
<div class="hr-page">

  <!-- ===== Section 1 · Hero (approved) ===== -->
  <section class="a-hero"><div class="in">
    <div class="a-l">
      <div class="a-copy">
        <h1 class="h1">Hospital billing software that <em>does not miss a single charge</em> on the final bill.</h1>
        <p class="lead"><span class="ld-full">Make every bill of your hospital in one software: OPD bill, IPD bill and cashless TPA bill. Lab, pharmacy and room charges are added to the bill by themselves. One report shows what was given to the patient but not billed.</span><span class="ld-short">Every bill in one software: OPD, IPD and cashless TPA. Lab, pharmacy and room charges are added to the bill by themselves.</span></p>
        <a class="more" href="#how">See how a bill is made</a>
      </div>
      <div class="money" aria-label="Results at hospitals that use Healthray">
        <div><b>₹25 lakh</b>saved a year at Vibrant Hospital, Vapi</div>
        <div><b>30% faster</b>billing and discharge at BBMH Hospital, Cuttack</div>
        <div><b>6 h → 3 h</b>discharge-day wait at Rainbow Hospital, Vapi</div>
      </div>
      <div class="a-trust">
        <div class="hrow">
          <div class="faces" aria-hidden="true"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt=""><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt=""><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp" alt=""><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt=""></div>
          <p><b>2,500+ hospitals and clinics</b> across India use Healthray</p>
        </div>
        <div class="hrow">
          <div class="tiles"><span><img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="NABH certified"></span><span><img src="https://healthray.com/wp-content/uploads/2026/03/abdm-healthray-150x150.webp" alt="ABDM"></span><span><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved"></span><span><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001"></span></div>
          <p><b>NABH certified, follows ABDM rules</b><br>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="rates"><span><b>4.8</b> <i class="stars">★★★★★</i> Google</span><span><b>4.8</b> <i class="stars">★★★★★</i> Capterra</span><span><b>5.0</b> <i class="stars">★★★★★</i> G2</span></div>
      </div>
    </div>
    <div class="card" id="a-form">
      <p class="hr-cta-title">See your own bills in a free demo</p>
      <p>In a 30-minute video call, we make an OPD bill, an IPD bill and a cashless bill for a hospital of your size.</p>
      <?php if ( shortcode_exists( 'contact-form-7' ) ) { echo do_shortcode( $hr_bill_form ); } ?>
      <p class="small" style="text-align:center">The demo is free. You do not have to buy.</p>
    </div>
  </div></section>

  <!-- ===== Section 2 · Logo strip (fixed: same as home-new-page) ===== -->
  <section class="logos" aria-label="Hospitals using Healthray"><div class="in">
    <p class="lg-label">Hospitals and medical colleges that use Healthray</p>
    <div class="marquee"><div class="marquee-track" id="lgtrack">
      <ul id="lglist">
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>hzb-arogyam-multispeciality-hospital.webp" alt="HZB Arogyam Multispeciality Hospital, Hazaribagh, Jharkhand" width="1336" height="534"></span><span class="lg-badge">150 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>vibrant-hospital.webp" alt="Vibrant Multispecialty Hospital, Vapi" width="1600" height="489"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp" alt="Shraddha Arogya Mandir, Vapi"></span><span class="lg-badge">120+ beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>shri-manakula-vinayak-medical-college.webp" alt="Sri Manakula Vinayagar Medical College, Puducherry"></span><span class="lg-badge mc">Medical college · 1,100 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>parivar-super-speciality-hospital.webp" alt="Parivar Super Speciality Hospital, Madhya Pradesh"></span><span class="lg-badge">140 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>jeevan-rekha-hospital.webp" alt="Jeevan Rekha Hospital, West Bengal"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>budha-baba-multispeciality-hospital.webp" alt="Budha Baba Multispecialty Hospital, Cuttack, Odisha"></span><span class="lg-badge">130 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>heritage-medical-college.webp" alt="Heritage Medical College, Varanasi"></span><span class="lg-badge mc">Medical college · 1,000 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>pims-multi-superspeciality-hospital.webp" alt="PIMS Multi-Superspeciality Hospital, Udaipur"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>jj-plus-hospitals.webp" alt="JJ Plus Hospitals"></span><span class="lg-badge">135 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>jabalpur-hospital-research-center.webp" alt="Jabalpur Hospital &amp; Research Centre, Jabalpur, MP"></span><span class="lg-badge">350 beds</span></li>
        <li><span class="lg-tile"><img src="https://healthray.com/wp-content/uploads/2025/08/Prabh-Aasra-Unified-Family.webp" alt="Prabh Aasra Charitable Hospital, Punjab"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>heritage-hospital.webp" alt="Heritage Hospital, Noida"></span><span class="lg-badge">100 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>universal-hospital.webp" alt="Universal Hospital, Surat"></span><span class="lg-badge">250 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_logo ); ?>jupiter-hospital-group.webp" alt="Jupiter Hospital Group, Vadodara"></span><span class="lg-badge">100 beds</span></li>
      </ul>
    </div></div>
  </div></section>

  <!-- ===== Section 3 · Video tour (fixed: same as home-new-page, billing words) ===== -->
  <section class="sec tint" id="how" aria-labelledby="walkTitle"><div class="in">
    <div class="sh c">
      <p class="eyebrow">Video tour</p>
      <h2 id="walkTitle">Watch how a hospital bill is <span class="hl">made in Healthray</span></h2>
      <p class="lead">Watch one patient from the OPD counter to the final bill: the OPD bill, room rent added every night, the ICU rate, the cashless TPA bill and the day's money. The patient is a sample. The software is real.</p>
    </div>
    <div class="player" id="player">
      <div class="player-screen">
        <img class="player-poster" src="<?php echo esc_url( $hr_up ); ?>bill-tour-poster.jpg" width="1920" height="1080" alt="Healthray product tour: the IPD bill of an admitted patient, with room rent added every night" loading="lazy">
        <video id="walkVideo" muted loop playsinline preload="none" disablepictureinpicture width="1920" height="1080" aria-hidden="true"><source data-src="<?php echo esc_url( $hr_up ); ?>bill-tour-web.mp4" type="video/mp4"></video>
      </div>
      <button type="button" class="vt" id="walkBtn" aria-label="Play the video tour">
        <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z"/></svg>
        <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
      </button>
      <button type="button" class="vt vt-fs" id="walkFs" aria-label="Watch the video tour full screen">
        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9V5a1 1 0 0 1 1-1h4M15 4h4a1 1 0 0 1 1 1v4M20 15v4a1 1 0 0 1-1 1h-4M9 20H5a1 1 0 0 1-1-1v-4"/></svg>
      </button>
    </div>
    <div class="walk-cta">
      <p><b>Want to see it with your rates?</b> Book a free 30-minute demo. We will make a bill for a hospital like yours.</p>
      <a class="btn hr-cta-btn" href="#a-form">Book a free demo</a>
    </div>
  </div></section>

  <!-- ===== What is hospital billing software (organic SEO, approved 25 Nov 2026) ===== -->
  <section class="sec wh" id="what">
    <div class="in"><div class="wh2">
      <div><h2>What is <span class="hl">hospital billing software</span>?</h2>
        <p class="lead">Hospital billing software is the software a hospital uses to make all its bills: the OPD bill, the IPD bill of an admitted patient and the cashless bill for the TPA. It keeps your rate list, adds charges to the patient's bill, takes payments and deposits, and gives reports of the day's money. In Healthray, billing is part of the same software that your reception, wards, lab and pharmacy use, so their charges are added to the bill without typing them again.</p></div>
      <div class="wbox"><h3>Is hospital billing software the same as medical billing software?</h3>
        <p>In India, people often use both names for the same thing: software that makes bills in a hospital or clinic. In the USA, "medical billing" usually means sending insurance claims for doctors. Healthray is made for Indian hospitals: OPD, IPD, cashless TPA and Ayushman bills.</p></div>
    </div></div>
  </section>

  <!-- ===== Section 4 · Four places a hospital bill loses money (Version A, temporary pick) ===== -->
  <section class="sec tint s4t" id="money">
    <div class="in">
      <div class="sh">
        <h2>4 ways a hospital <span class="hl">loses money in billing</span></h2>
        <p class="lead">Healthray adds charges to the bill by itself and shows what is still not billed. We show all four in your free demo.</p>
      </div>
      <div class="rbx">
        <ul class="lk-list" id="lklist">
          <li class="lk on"><button type="button" aria-expanded="true"><h3>Medicines and tests not billed</h3><p>What the doctor orders goes on the bill. The Revenue Leakage report shows each patient's medicines and tests that were not billed.</p></button>
            <div class="ph"><img class="pic4" src="<?php echo esc_url( $hr_up ); ?>lc-leak.webp" alt="" width="1040" height="840" loading="lazy"></div></li>
          <li class="lk"><button type="button" aria-expanded="false"><h3>Room rent missed or at the wrong rate</h3><p>Room rent is added every night at 12, at that ward's rate. If the patient moves to ICU, change the rate for all those days together.</p></button>
            <div class="ph"><img class="pic4" src="<?php echo esc_url( $hr_up ); ?>lc-rent.webp" alt="" width="1040" height="840" loading="lazy"></div></li>
          <li class="lk"><button type="button" aria-expanded="false"><h3>Discounts given without asking you</h3><p>Every discount is saved with the bill, the reason and the name of the staff who gave it. The Discount Report shows all of them.</p></button>
            <div class="ph"><img class="pic4" src="<?php echo esc_url( $hr_up ); ?>lc-disc.webp" alt="" width="1040" height="840" loading="lazy"></div></li>
          <li class="lk"><button type="button" aria-expanded="false"><h3>Cash at the counter that does not match the bills</h3><p>The collection report shows the day's money in cash, UPI and card, for each person at the counter. Refunds are shown separately.</p></button>
            <div class="ph"><img class="pic4" src="<?php echo esc_url( $hr_up ); ?>lc-coll.webp" alt="" width="1040" height="840" loading="lazy"></div></li>
        </ul>
        <div class="rb-pnl pnl"><div class="shot swap" id="rbshot">
          <img class="on" src="<?php echo esc_url( $hr_up ); ?>lk-leak-w.webp" alt="Revenue Leakage report: medicines ordered but never billed, with patient and doctor. Sample figures" width="2220" height="1296" loading="lazy">
          <img src="<?php echo esc_url( $hr_up ); ?>lk-rent-w.webp" alt="IPD bill: bed charges added at 12:00 AM each night, 3 nights in General Ward, and Change Charges moving 23 Nov to the ICU rate. Sample figures" width="2220" height="1296" loading="lazy">
          <img src="<?php echo esc_url( $hr_up ); ?>lk-disc-w.webp" alt="Discount Report: each discounted bill with amount, reason and operator; one bill has no reason. Sample figures" width="2220" height="1296" loading="lazy">
          <img src="<?php echo esc_url( $hr_up ); ?>lk-coll-w.webp" alt="Total collection for today split into cash, UPI and card, with refunds and balance. Sample figures" width="2220" height="1296" loading="lazy">
        </div></div>
      </div>
      <div class="s4-after"><a class="btn hr-cta-btn" href="#a-form">See this in a free demo</a></div>
    </div>
  </section>

<!-- ===== Section 5 · Every bill in one software (Version D, approved) ===== -->
  <section class="sec s5 s5w" id="bills">
    <div class="in">
      <div class="cm-head"><div class="sh"><h2>All your hospital bills <span class="hl">in one software</span></h2><p class="lead">OPD bill, IPD bill, cashless TPA bill and package bill. One patient file and one rate list, from the first visit to the final bill.</p></div><div class="cm-nav"><button type="button" class="cm-prev" aria-label="Previous bill">‹</button><button type="button" class="cm-next" aria-label="Next bill">›</button></div></div>
      <div class="cm" id="cm5"><article class="cm-card on" data-i="0" aria-label="1 of 4"><div class="cm-pic"><picture><source media="(max-width: 600px)" srcset="<?php echo esc_url( $hr_up ); ?>bcm-opd.webp" width="720" height="1160"><img class="pic5" src="<?php echo esc_url( $hr_up ); ?>bc-opd.webp" alt="OPD bill: first consultation fee added automatically, CBC, HbA1c and ECG, total ₹1,500, paid by UPI with the bill sent on WhatsApp. Sample figures" width="1040" height="840"></picture></div><div class="tx"><h3>The OPD bill</h3><p>The doctor's fee is added when the appointment is booked. Add tests, take payment by cash, UPI, card or more than one way, and send the bill on WhatsApp.</p></div></article><article class="cm-card" data-i="1" aria-label="2 of 4"><div class="cm-pic"><picture><source media="(max-width: 600px)" srcset="<?php echo esc_url( $hr_up ); ?>bcm-ipd.webp" width="720" height="1160"><img class="pic5" src="<?php echo esc_url( $hr_up ); ?>bc-ipd.webp" alt="IPD bill on day 4: two deposits of ₹15,000 and ₹5,000, bill till today ₹26,660, due ₹6,660, and the printed interim bill. Sample figures" width="1040" height="840"></picture></div><div class="tx"><h3>The IPD bill (bill of an admitted patient)</h3><p>Take a deposit at admission. Charges are added every day, and the bill till today can be printed whenever the family asks. At discharge, the deposit is cut from the bill and you see what the patient must pay or get back.</p></div></article><article class="cm-card" data-i="2" aria-label="3 of 4"><div class="cm-pic"><picture><source media="(max-width: 600px)" srcset="<?php echo esc_url( $hr_up ); ?>bcm-tpa.webp" width="720" height="1160"><img class="pic5" src="<?php echo esc_url( $hr_up ); ?>bc-tpa.webp" alt="TPA patient list with payer, ward, claim number, insurance company and bill status, and the print menu with Cashless Bill Print. Sample names and figures" width="1040" height="840" loading=lazy></picture></div><div class="tx"><h3>Cashless bills for TPA and insurance</h3><p>The TPA, insurance company and policy number are saved at admission. One list shows every cashless patient with the claim number and whether the bill is paid. The bill prints in the TPA's format.</p></div></article><article class="cm-card" data-i="3" aria-label="4 of 4"><div class="cm-pic"><picture><source media="(max-width: 600px)" srcset="<?php echo esc_url( $hr_up ); ?>bcm-pkg.webp" width="720" height="1160"><img class="pic5" src="<?php echo esc_url( $hr_up ); ?>bc-pkg.webp" alt="Assign Package: Hernia Repair package for a General Ward patient, 3 days, ₹42,000, chosen from the packages for that ward and payer. Sample figures" width="1040" height="840" loading=lazy></picture></div><div class="tx"><h3>Packages for surgeries</h3><p>Choose a package by ward and by who pays (cash, TPA or company). Its amount, days and doctor go on the bill.</p></div></article></div>
      <div class="cm-dots" id="cmdots"></div>
      <div class="s5-after c"><a class="btn hr-cta-btn" href="#a-form">See your own bills in a free demo</a></div>
    </div>
  </section>

<!-- ===== Hospital billing process, step by step (organic SEO, approved 25 Nov 2026) ===== -->
  <section class="sec tint pr" id="process">
    <div class="in">
      <div class="sh"><h2>Hospital billing process in Healthray, <span class="hl">step by step</span></h2><p class="lead">How one patient's bill is made, from the first visit to discharge.</p></div>
      <ol class="steps-pr"><li><span class="pn">1</span><h3>Registration</h3><p>The patient is registered once. Who pays is saved here: cash, TPA, company or Ayushman.</p></li><li><span class="pn">2</span><h3>OPD bill</h3><p>The doctor's fee is added when the appointment is booked. Tests are added, and the patient pays by cash, UPI or card.</p></li><li><span class="pn">3</span><h3>Admission and deposit</h3><p>The ward and bed are chosen, and a deposit is taken.</p></li><li><span class="pn">4</span><h3>Daily charges</h3><p>Room rent is added every night at 12. Lab tests, medicines and doctor visits are added when they happen.</p></li><li><span class="pn">5</span><h3>Cashless TPA</h3><p>The TPA and policy number are saved. One list shows each claim number and whether the bill is paid.</p></li><li><span class="pn">6</span><h3>Bill till today</h3><p>Whenever the family asks, print the bill till today.</p></li><li><span class="pn">7</span><h3>Final bill and discharge</h3><p>The deposit is cut from the final bill. The patient pays the rest or gets money back, and the bill prints in the format you need, including the TPA's format.</p></li></ol>
    </div>
  </section>

<!-- ===== Section 6 · The right rate for every patient (Version 3, approved) ===== -->
  <section class="sec s6" id="rates">
    <div class="in">
      <div class="sh c"><h2>The right rate on every bill: <span class="hl">cash, TPA or company patient</span></h2><p class="lead">Set your rates once for cash, each TPA and each company, and for each ward. The bill takes the right rate by itself, so counter staff do not need to check a rate list.</p></div>
      <div class="zz6"><div class="zr6"><div class="zt6"><small>01</small><h3>A rate list for each TPA, company and ward</h3><p>Keep separate rates for cash patients, each TPA and each company, with different rates for General, Private and ICU. Set a date, and new rates start from that day.</p></div><div class="zp6"><img class="pic6" src="<?php echo esc_url( $hr_up ); ?>rt-rate.webp" alt="IPD Billing Master: rates for Bed Charges, Doctor Visit and Nursing in General, Private and ICU, from 01/12/2026, with the payer list open on Health First TPA. Sample figures" width="1040" height="840" loading=lazy></div></div><div class="zr6"><div class="zt6"><small>02</small><h3>Bring your rate list from Excel</h3><p>Add your current rate list from an Excel file. Copy one TPA's rates to make the next one, and download the list any time.</p></div><div class="zp6"><img class="pic6" src="<?php echo esc_url( $hr_up ); ?>rt-excel.webp" alt="An Excel rate list next to the IPD Billing Master buttons Import, Clone and Download. Sample figures" width="1040" height="840" loading=lazy></div></div><div class="zr6"><div class="zt6"><small>03</small><h3>Doctor share, calculated by itself</h3><p>Set each doctor's share once. A surgery bill is divided into surgeon, anaesthesia and OT charges, and the Doctor Share report shows what each doctor gets.</p></div><div class="zp6"><img class="pic6" src="<?php echo esc_url( $hr_up ); ?>rt-share.webp" alt="Hernia Repair ₹42,000 split into surgeon 50%, anaesthesia 15% and OT 35%, and the Doctor Share report for the month. Sample figures" width="1040" height="840" loading=lazy></div></div><div class="zr6"><div class="zt6"><small>04</small><h3>GST only on the services that need it</h3><p>Add GST only to the services that have it. The bill shows GST on each line, and the Item Wise Tax report shows the month's total.</p></div><div class="zp6"><img class="pic6" src="<?php echo esc_url( $hr_up ); ?>rt-tax.webp" alt="IPD bill with tax only on the Mesh Implant line, ₹1,440 tax in total, and the Item Wise Tax report for the month. Sample figures" width="1040" height="840" loading=lazy></div></div></div>
      <p class="sw6" aria-hidden="true">Swipe to see all four →</p>
      <div class="s6-after c"><a class="btn hr-cta-btn" href="#a-form">See your own rates in a free demo</a></div>
    </div>
  </section>

<!-- ===== Section 7 · What changed at hospitals like yours (Version 1, approved) ===== -->
  <section class="sec tint s7" id="results">
    <div class="in">
      <div class="hd7"><div class="sh"><h2>Results at hospitals <span class="hl">like yours</span></h2><p class="lead">Four hospitals that make all their bills in Healthray. These numbers are from their case studies.</p></div><div class="ar7"><button type="button" class="p7" aria-label="Previous hospitals">←</button><button type="button" class="n7" aria-label="Next hospitals">→</button></div></div>
      <div class="deck7" id="deck7"><article class="dc7"><div class="ph7"><img src="https://healthray.com/wp-content/uploads/2025/12/Vibrant-Hospital-Healthray-1024x512.webp" alt="Vibrant Multispeciality Hospital building, Vapi" width="900" height="506" loading=lazy><div class="res7"><b>₹25 lakh</b><span>saved a year</span></div></div><div class="dt7"><span class="meta7">Vibrant Multispeciality Hospital · Vapi · 200+ beds</span><ul class="pts7"><li><b>30%</b> less time on billing</li><li><b>40%</b> fewer mistakes in bills and records</li></ul><p class="q7"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt="" width="40" height="40" loading="lazy"><span>"Now patient records, billing, pharmacy and inventory are all connected in one system."<small>Dr. Bhaumik Rathore</small></span></p><a class="rs7" href="https://healthray.com/case-studies/vibrant-hospital/" target="_blank" rel="noopener">Read the story<span class="sr"> of Vibrant Multispeciality Hospital</span> →</a></div></article><article class="dc7"><div class="ph7"><img src="https://healthray.com/wp-content/uploads/2025/12/Budha-Baba-Multispecialty-Hospital-1.webp" alt="Budha Baba Multispeciality Hospital building, Cuttack" width="900" height="506" loading=lazy><div class="res7"><b>30%</b><span>faster billing and discharge</span></div></div><div class="dt7"><span class="meta7">Budha Baba Multispeciality Hospital · Cuttack · 100 beds</span><ul class="pts7"><li><b>35%</b> less office work</li><li><b>40%</b> fewer mistakes in bills and records</li></ul><p class="q7 plain">Old data moved in 1 day. Work did not stop.</p><a class="rs7" href="https://healthray.com/case-studies/bbmh-hospital/" target="_blank" rel="noopener">Read the story<span class="sr"> of Budha Baba Multispeciality Hospital</span> →</a></div></article><article class="dc7"><div class="ph7"><img src="https://healthray.com/wp-content/uploads/2026/10/Rainbow-Hospital-1024x577.webp" alt="Rainbow Super Speciality Hospital building, Vapi" width="900" height="506" loading=lazy><div class="res7"><b>6 → 3 hrs</b><span>wait on discharge day</span></div></div><div class="dt7"><span class="meta7">Rainbow Super Speciality Hospital · Vapi · 100 beds</span><ul class="pts7"><li><b>3 → 1 day</b> to get a TPA claim ready</li><li><b>20 min</b> less to admit a patient</li></ul><p class="q7 plain">Moved to Healthray in 14 working days.</p><a class="rs7" href="https://healthray.com/case-studies/rainbow-hospital-vapi/" target="_blank" rel="noopener">Read the story<span class="sr"> of Rainbow Super Speciality Hospital</span> →</a></div></article><article class="dc7"><div class="ph7"><img src="https://healthray.com/wp-content/uploads/2025/11/Jabalpur-Hospital-1024x402.webp" alt="Jabalpur Hospital &amp; Research Centre building, Jabalpur" width="900" height="506" loading=lazy><div class="res7"><b>50%</b><span>faster billing, pharmacy and TPA work</span></div></div><div class="dt7"><span class="meta7">Jabalpur Hospital &amp; Research Centre · Jabalpur · 300+ beds</span><ul class="pts7"><li><b>95%</b> correct patient details</li><li><b>60%</b> faster lab and scan reports</li></ul><p class="q7"><img src="https://healthray.com/wp-content/uploads/2026/10/Dr.-Rajesh-B.-Dhirawani-150x150.webp" alt="" width="40" height="40" loading="lazy"><span>"Every work operation from diagnosis to discharge can now be accessed in just one single click."<small>Dr. Rajesh B. Dhirawani, Surgeon</small></span></p><a class="rs7" href="https://healthray.com/case-studies/jabalpur-hospital/" target="_blank" rel="noopener">Read the story<span class="sr"> of Jabalpur Hospital &amp; Research Centre</span> →</a></div></article></div>
      <div class="cta7"><p>Want the same for your hospital? See it with your own bills in a free 30-minute demo.</p><a class="btn hr-cta-btn" href="#a-form">Book a free demo</a></div>
    </div>
  </section>

<!-- ===== Section 8 · Move to Healthray without stopping your billing (Version 1, approved) ===== -->
  <section class="sec s8" id="move">
    <div class="in">
      <div class="sh"><h2>Move to Healthray <span class="hl">without stopping your billing</span></h2><p class="lead">Our team brings in your old records, teaches your staff and starts one counter at a time. Most hospitals in our case studies started in under 2 weeks.</p></div>
      <div class="row8"><div class="bf8"><b class="n">0 days</b><span class="l">without billing, at 7 hospitals that changed to Healthray</span><h3>Will billing stop while we change?</h3><p>No. One counter moves at a time: OPD first, then admission and billing, then pharmacy and lab.</p></div><div class="bf8"><b class="n">1 day</b><span class="l">to bring in old records at BBMH and Vibrant</span><h3>What about our old records and rate lists?</h3><p>Our team brings in your patient, billing and stock records, and your rate lists, before you start.</p></div><div class="bf8"><b class="n">2 weeks</b><span class="l">or less to start at most hospitals in our case studies</span><h3>My staff is not good with computers.</h3><p>Each person learns only their own screen, at your hospital. The billing counter, reception, nurses and pharmacy are taught one by one.</p></div><div class="bf8"><b class="n">1 hour</b><span class="l">reply to a big problem, day or night</span><h3>What if the internet stops, or something goes wrong at night?</h3><p>Use your mobile hotspot, or keep Healthray on your own computer (server) inside the hospital. Call, chat or share your screen 24×7, in English, Hindi or Gujarati.</p></div></div>
      <p class="proof8">BBMH, Vibrant, Universal, Lotus and Jabalpur started in under 2 weeks. Rainbow took 14 working days. From Healthray's published case studies.</p>
      <div class="cta8"><p>Tell us which software you use today. In the free demo, we show how your old data comes into Healthray.</p><a class="btn hr-cta-btn" href="#a-form">Book a free demo</a></div>
    </div>
  </section>

<!-- ===== Section 9 · What Healthray costs for a hospital your size (Version 1, approved) ===== -->
  <section class="sec s9 nv9" id="price">
    <div class="in">
      <div class="sh"><h2>What Healthray costs for <span class="hl">a hospital your size</span></h2><p class="lead">There is no fixed price list. Three things decide your price, so a 30-bed hospital pays less than a 300-bed hospital.</p></div>
      <div class="z9"><div class="zbig9"><b>₹0</b><span>for your 30-minute demo</span></div>
        <div><p class="lab9 lt9">Included in every price</p><ul class="inc9"><li><b>Moving your old records</b></li><li><b>Training at your hospital</b></li><li><b>Your price in writing within 24 hours of the demo</b></li></ul></div></div>
      <div class="box9"><div><p><b>After the demo, your price depends on three things:</b></p>
        <ol class="f9 dk"><li><b>Number of beds</b> You pay for the beds you have today.</li><li><b>Departments you use</b> Start with OPD, admission and billing. Add pharmacy, lab and the rest later, in the same software.</li><li><b>Online, or on your own computer in the hospital</b> Use it online (data kept in India), or put it on your own computer (server) inside the hospital.</li></ol><p class="note9">WhatsApp and SMS messages are paid separately.</p></div>
        <a class="btn hr-cta-btn" href="#a-form">Get a price for my hospital</a></div>
    </div>
  </section>

  <!-- ===== Section 10 · Questions + last call (Version 1, approved) ===== -->
  <?php
  // FAQ: shared section (templates/module-faq.php); the same items feed the FAQPage schema
  get_template_part( 'templates/module-faq', null, array(
    'section_class' => 'sec tint q10',
    'wrap_class'    => 'in',
    'head_class'    => 'sh',
    'lead_class'    => 'lead',
    'title'         => 'Questions owners ask about <span class="hl">hospital billing software</span>',
    'lead'          => 'Did not find yours? <a href="#a-form">Ask it in the free demo</a>',
    'items'         => function_exists( 'healthray_billing_faq_items' ) ? healthray_billing_faq_items() : array(),
  ) );
  ?>
  <section class="sec lc10" id="book">
    <div class="in c10"><div class="faces10" aria-hidden="true"><span class="fs10"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt="" width="44" height="44"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt="" width="44" height="44"><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp" alt="" width="44" height="44"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="" width="44" height="44"></span><span>2,500+ hospitals and clinics use Healthray</span></div><p class="lh10 hr-cta-title">See your own bills in Healthray, <span class="hl">before you decide</span></p><p class="lead">In a free 30-minute demo we make an OPD bill, an admitted patient's bill and a cashless bill for a hospital like yours.</p><a class="btn hr-cta-btn big10" href="#a-form">Show me Healthray for my hospital <span aria-hidden="true">→</span></a><p class="prom10">The demo is free. You do not have to buy.</p></div>
  </section>
</div>
</div><!-- /.hr-billing -->

