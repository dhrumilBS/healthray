<?php
/**
 * Template Name: PPC - HIMS Meta
 *
 * Meta / Instagram ads landing page (healthray.com/ppc/hms-meta/). Same system as the Google and ChatGPT pages:
 * css/ppc-hims.css (section 33 = this page), js/ppc-hims.js (section 8 = this page), templates/ppc-hims/ partials,
 * the PPC Form stepper in the hero card and the site-wide popup that every "Book free demo" button opens.
 */

defined('ABSPATH') || exit;

// Popup heading and text for this page (setup.php adds the form).
$ppc_popup = array(
	'title' => 'Book free demo',
	'text'  => 'Free 30-minute live demo. Answer 2 quick questions and we will show you the software set up for a hospital like yours.',
);
require __DIR__ . '/templates/ppc-hims/setup.php';

// js/ppc-hims.js section 8 (GTM events, UTM / fbclid into the form, thank-you panel) runs on this class.
add_filter('body_class', function ($classes) {
	$classes[] = 'hr-hms-meta';
	return $classes;
});

$ppc_tick_green = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#047857" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$ppc_no         = '<span class="pi" aria-hidden="true"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="M6 6l12 12M18 6L6 18" stroke="#B42318" stroke-width="3" stroke-linecap="round"/></svg></span>';
$ppc_yes        = '<span class="pi" aria-hidden="true"><svg width="12" height="12" viewBox="0 0 24 24" fill="none"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#047857" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
$ppc_cost_dot   = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><circle cx="12" cy="12" r="9" stroke="#0F68EA" stroke-width="2"/></svg>';
$ppc_cost_tick  = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#047857" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg>';
$ppc_wa_price   = hr_ppc_wa_url('Hi Healthray, I want to know the price for my hospital. Beds: ___ City: ___');

$ppc_problems = array(
	array( 'IPD patient details are on paper and files go missing', "Admission, bed, nursing notes and charges are saved in the software. Open any patient's file from any computer." ),
	array( 'Lab reports come late or get lost', "The doctor orders the test in the software and the lab gets it at once. The report goes into the patient's file and the test is added to the bill." ),
	array( 'Medicines run out, or expire in the store', 'You get an alert before stock finishes or a medicine expires. Every sale reduces the stock on its own.' ),
	array( 'Charges get missed in the final bill', 'Every test, medicine and room charge is added to the bill when it happens. The TPA claim is made from the same bill.' ),
	array( 'Staff attendance and salary are kept in registers', 'Attendance, salary and NABH records are kept in the same software as billing.' ),
	array( "You find out the day's collection only at night", "See today's collection, OPD count and admissions on your phone, any time of the day." ),
);
?>
<header class="top">
  <div class="wrap">
    <?php // Logo is not linked on purpose: no exits from an ads page. ?>
    <div class="brand"><img src="https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg" alt="Healthray"
        width="150" height="34"><span class="fb">Healthray</span></div>
    <div class="top-right">
      <a class="btn btn-primary btn-sm hr-cta-btn" data-from="header" href="#demo">Book free demo</a>
    </div>
  </div>
</header>

<main>
  <!-- 1. HERO: the pain from the ad in their words (kicker), what it is (h1), their 3 fears (sub). Form beside it. -->
  <section class="hero">
    <div class="wrap hero-grid">
      <div class="hero-copy">
        <p class="kicker">Still running your hospital on registers, Excel or 3 different software?</p>
        <h1>One software for your full hospital: OPD, IPD, billing, pharmacy and lab</h1>
        <p class="hero-sub">For hospitals and nursing homes from 10 to 300 beds. We shift your old data and train your staff. Most hospitals start using it in about 2 weeks.</p>
        <ul class="ticks">
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#34D399" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>Free 30-minute live demo</b> for a hospital of your size</span></li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#34D399" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>Staff training included</b>, one department at a time</span></li>
          <li><svg width="18" height="18" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#34D399" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg><span><b>NABH certified, ABDM compliant</b>, NHA approved</span></li>
        </ul>
        <?php // Phones only (the full proof block sits below the form there). ?>
        <p class="mini-trust" aria-label="Rated 4.8, used by 2,500+ hospitals and clinics, NABH certified, ABDM compliant"><b>★ 4.8</b> <i>·</i> <b>2,500+</b> hospitals &amp; clinics <i>·</i> NABH <i>·</i> ABDM</p>
      </div>

      <?php // #formSlot / #formArea / #doneArea: js/ppc-hims.js section 8 shows the thank-you panel here after a hero submit. ?>
      <div class="form-slot" id="formSlot">
        <div class="form-card" id="demo">
          <div id="formArea">
            <h2>Book free demo</h2>
            <p class="fc-sub"><b>Free 30-minute live demo.</b> Answer 2 quick questions and we will show you the software set up for a hospital like yours.</p>
            <?php echo do_shortcode($ppc_form); ?>
          </div>
          <div id="doneArea" class="done" hidden aria-live="polite">
            <div class="tick"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#047857" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
            <h3 id="doneTitle">Thank you. We have got your request.</h3>
            <p id="doneText">Our team will call you once to fix a demo time.</p>
            <ol>
              <li>We ask a few questions about your hospital and what you use now.</li>
              <li>We fix a demo time that suits you.</li>
              <li>You see the software working for a hospital like yours, in about 30 minutes.</li>
            </ol>
            <a class="wa-done js-wa" href="<?php echo esc_url(hr_ppc_wa_url('Hi Healthray, I just booked a demo on your website.')); ?>" target="_blank" rel="noopener">Want to talk sooner? WhatsApp us →</a>
          </div>
        </div>
      </div>

      <div class="hero-proof">
        <div class="faces">
          <div class="pile">
            <span><img loading="lazy" src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp"
                alt="Dr. Ketan Rupala" width="44" height="44" data-fallback="KR"></span>
            <span><img loading="lazy" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp"
                alt="Dr. Bhaumik Rathore" width="44" height="44" data-fallback="BR"></span>
            <span><img loading="lazy" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Maharshi-Desai-150x150.webp"
                alt="Dr. Maharshi Desai" width="44" height="44" data-fallback="MD"></span>
            <span><img loading="lazy" src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp"
                alt="Dr. Vimal Dhaduk" width="44" height="44" data-fallback="VD"></span>
            <span><img loading="lazy" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp"
                alt="Dr. Arpit Gajjar" width="44" height="44" data-fallback="AG"></span>
          </div>
          <?php // CONFIRM: one hospital count and one doctor count, same as in every ad. ?>
          <p><b>2,500+ hospitals and 5,000+ doctors</b> use Healthray across India</p>
        </div>
        <div class="trust-row">
          <div class="seals">
            <span class="seal"><img loading="lazy" src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp"
                alt="NABH certified" width="42" height="42"><span class="fb">NABH</span></span>
            <span class="seal"><img loading="lazy" src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp"
                alt="ABDM compliant" width="42" height="42"><span class="fb">ABDM</span></span>
            <span class="seal"><img loading="lazy" src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved"
                width="42" height="42"><span class="fb">NHA</span></span>
            <span class="seal"><img loading="lazy" src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp"
                alt="ISO 27001" width="42" height="42"><span class="fb">ISO 27001</span></span>
          </div>
          <p class="seal-text"><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="ratings" aria-label="Ratings">
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span>
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
          <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
        </div>
        <p class="co"><b>Healthray Technologies Pvt. Ltd.</b>, Surat · Working with hospitals since 2019</p>
      </div>
    </div>
  </section>

  <?php include __DIR__ . '/templates/ppc-hims/logos.php'; ?>

  <!-- 3. PROBLEMS: the same list as the ad, so the reader knows they are in the right place.
       Each card = the problem in their words, then what Healthray does, in plain words. -->
  <section class="section tint">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Common problems</p>
        <h2 class="h2">Is this happening in your hospital?</h2>
        <p class="lead">Most of these happen because every department works on its own register, Excel sheet or software.</p>
      </div>
      <div class="probs">
        <?php foreach ($ppc_problems as $ppc_p) : ?>
        <article class="prob">
          <p class="no"><?php echo $ppc_no; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html($ppc_p[0]); ?></span></p>
          <p class="yes"><?php echo $ppc_yes; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html($ppc_p[1]); ?></span></p>
        </article>
        <?php endforeach; ?>
      </div>
      <div class="sec-cta">
        <a class="btn btn-primary hr-cta-btn" data-from="problems" href="#demo">Book free demo</a>
        <p>We will show you how this works in a hospital of your size.<small>Free, about 30 minutes</small></p>
      </div>
    </div>
  </section>

  <!-- 4. PRODUCT WALKTHROUGH: "what does it look like, is it easy?" -->
  <section class="section walk" aria-labelledby="walkTitle">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">1-minute video</p>
        <h2 class="h2" id="walkTitle">Watch one patient go from registration to discharge</h2>
        <p class="lead">This is the real software, shown with a demo patient: front desk, doctor, admission, billing, insurance and discharge.</p>
      </div>
      <?php include __DIR__ . '/templates/ppc-hims/player.php'; ?>
      <div class="walk-cta">
        <p><b>Want to see it set up for your own hospital?</b> We will show you on a live demo.</p>
        <a class="btn btn-primary hr-cta-btn" data-from="video" href="#demo">Book free demo</a>
      </div>
    </div>
  </section>

  <!-- 5. THE BIGGEST WORRY: "will my staff use it?" Asked in the owner's own words, answered by a customer first -->
  <section class="section tint">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">The biggest worry</p>
        <h2 class="h2">“Will my staff be able to use it?”</h2>
        <p class="lead">Almost every hospital owner asks us this first. Here is what one hospital told us after they started.</p>
      </div>
      <div class="fear">
        <div>
          <blockquote>
            <p class="big-quote">The biggest concern for me was not the software itself, it was whether our staff would actually use it properly. Once the workflows were set and the staff got used to it, the amount of coordination between departments reduced a lot.</p>
          </blockquote>
          <div class="q-by">
            <span class="q-icon" aria-hidden="true"><svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M4 21V5a1 1 0 0 1 1-1h9a1 1 0 0 1 1 1v16M15 9h4a1 1 0 0 1 1 1v11M3 21h18M8 8h3M8 12h3M8 16h3" stroke="#1B3C74" stroke-width="1.8" stroke-linecap="round"/></svg></span>
            <span><b>Hospital Administrator, Multispeciality Hospital</b><span class="verified"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5" stroke="#047857" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>Verified Healthray customer</span></span>
          </div>
          <ul class="staff-list">
            <li><?php echo $ppc_tick_green; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><b>We train each person on their own work.</b> Reception, nurses, pharmacy, billing and doctors practise before you start.</span></li>
            <li><?php echo $ppc_tick_green; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><b>We start one department at a time.</b> OPD first, then IPD billing, pharmacy and lab.</span></li>
            <li><?php echo $ppc_tick_green; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><b>Doctors get ready templates</b> for their speciality, so a consultation does not take longer than writing on paper.</span></li>
            <li><?php echo $ppc_tick_green; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><b>If anyone gets stuck,</b> they can call us during OPD hours.</span></li>
          </ul>
          <div class="bbmh-note">
            <p>"We battled constant operational issues, knew inefficiencies hurt us, but feared changing systems handling patient data, billing and inventory."<br><span class="bbmh-by">Senior Administrator, BBMH Hospital, Cuttack</span></p>
            <p><b>BBMH started in 2 weeks.</b> Their old data was shifted in 1 day and work did not stop.</p>
          </div>
        </div>
        <div class="reel-wrap">
          <div class="video" id="video">
            <?php // Customer video hosted on healthray.com; plays with sound when tapped (js/ppc-hims.js, section 1). ?>
            <video id="storyVideo" playsinline preload="metadata" disablepictureinpicture controlslist="nodownload"
              src="https://healthray.com/wp-content/uploads/2026/08/Universal-Hospital-Surat-Review_-How-Healthray-Streamlined-Our-Workflow.mp4#t=0.5"></video>
            <button type="button" id="playBtn" class="story-cover" aria-label="Play video: Divyesh Gandhi, Head of Operations, Universal Hospital Surat">
              <img src="https://i.ytimg.com/vi/VRHZ9ejnBWk/oardefault.jpg" alt="" loading="lazy"
                data-fallback-src="https://i.ytimg.com/vi/VRHZ9ejnBWk/hqdefault.jpg">
              <span class="shade"></span>
              <span class="tag">Customer story</span>
              <span class="cap"><span class="watch"><i><svg width="14" height="14" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5v14l11-7z" fill="#fff" /></svg></i>Watch his story</span><b>Divyesh Gandhi</b>Head of Operations, Universal Hospital, Surat</span>
            </button>
            <button type="button" class="vt" id="storyBtn" aria-label="Pause the video" hidden>
              <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" /></svg>
              <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1" /><rect x="14" y="5" width="4" height="14" rx="1" /></svg>
            </button>
          </div>
          <p class="video-alt">"With Healthray, these departments are connected on the same patient record."</p>
        </div>
      </div>
      <div class="sec-cta">
        <a class="btn btn-primary hr-cta-btn" data-from="staff" href="#demo">Book free demo</a>
        <p>Bring your administrator or billing head to the demo. They can ask their own questions.</p>
      </div>
    </div>
  </section>

  <!-- 6. RESULTS from named hospitals -->
  <section class="section">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Results</p>
        <h2 class="h2">What changed for these hospitals</h2>
      </div>

      <article class="case-main">
        <div class="case-photo">
          <img src="https://healthray.com/wp-content/uploads/2025/12/Budha-Baba-Multispecialty-Hospital-1.webp"
            alt="Budha Baba Multispecialty Hospital, Cuttack" loading="lazy">
          <span class="fb">Budha Baba Multispecialty Hospital</span>
        </div>
        <div class="case-body">
          <p class="case-tag">Multispeciality hospital · Cuttack, Odisha</p>
          <h3>BBMH Hospital moved from paper and Excel to one software in 2 weeks</h3>
          <p>Patient files, billing and stock were spread across paper, Excel and different software. Doctors and management could not see what was pending.</p>
          <div class="nums">
            <div><b>35%</b><span>less office work</span></div>
            <div><b>30%</b><span>faster billing and discharge</span></div>
            <div><b>40%</b><span>fewer mistakes</span></div>
          </div>
        </div>
      </article>

      <div class="cases">
        <article class="case">
          <span class="big">₹25 lakh</span>
          <h3>saved in a year at Vibrant Multispecialty Hospital</h3>
          <p>They had different software for each department. After moving to one software, billing time came down by 30% and mistakes by 40%.</p>
          <div class="who"><span class="av"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt=""
                loading="lazy" data-fallback="BR"></span><span><b>Dr. Bhaumik Rathore</b>"There is less duplicate entry and it is easier to track what is pending between departments."</span></div>
        </article>
        <article class="case">
          <span class="big">45%</span>
          <h3>less waiting time in OPD at Universal Hospital</h3>
          <p>OPD, lab and pharmacy now work on the same patient file. Patient satisfaction reached 92%.</p>
          <div class="who"><span class="av"><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt=""
                loading="lazy" data-fallback="DV"></span><span><b>Dr. Dipak Viradia</b>Pulmonologist, Universal Hospital, Surat</span></div>
        </article>
        <article class="case">
          <span class="big">40%</span>
          <h3>faster test reports at Lilavati Hospital</h3>
          <p>Running costs came down by 45%, and 70% of doctors moved to digital records.</p>
          <div class="who"><span class="av av-logo"><img src="https://healthray.com/wp-content/uploads/2025/08/Lilavati-Hospital.webp"
                alt="Lilavati Hospital logo" loading="lazy"><span class="fb">LH</span></span><span><b>Lilavati Hospital</b>Ahmedabad, Gujarat</span></div>
        </article>
      </div>
      <p class="case-src">Figures from Healthray case studies published on healthray.com.</p>
      <div class="sec-cta">
        <a class="btn btn-primary hr-cta-btn" data-from="results" href="#demo">Book free demo</a>
        <p>See what can change in your hospital.<small>Free, about 30 minutes</small></p>
      </div>
    </div>
  </section>

  <!-- 7. COST: their exact question. No price list, but a clear answer and a reason to book.
       CONFIRM: free trial, and that the written quote lists what is included and what is extra. -->
  <section class="section tint" id="cost">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Price</p>
        <h2 class="h2">How much does it cost?</h2>
        <p class="lead">It depends on 3 things. After the demo, we send you a written quote that shows clearly what is included and what is extra.</p>
      </div>
      <div class="cost-grid">
        <div class="cost-col">
          <h3>Price depends on</h3>
          <ul>
            <li><?php echo $ppc_cost_dot; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Number of beds</li>
            <li><?php echo $ppc_cost_dot; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Modules you need: OPD, IPD, pharmacy, lab, OT, HR, accounts</li>
            <li><?php echo $ppc_cost_dot; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Cloud, or a server inside your hospital</li>
          </ul>
        </div>
        <div class="cost-col">
          <h3>Included with setup</h3>
          <ul>
            <li><?php echo $ppc_cost_tick; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Software set up as per your hospital: departments, rates, packages and bill format</li>
            <li><?php echo $ppc_cost_tick; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Old patient, billing and stock data shifted from your current software</li>
            <li><?php echo $ppc_cost_tick; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Training for every staff member</li>
          </ul>
        </div>
        <div class="cost-col">
          <h3>Before you pay</h3>
          <ul>
            <li><?php echo $ppc_cost_tick; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Free demo for a hospital of your size</li>
            <li><?php echo $ppc_cost_tick; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Written quote for your hospital</li>
            <li><?php echo $ppc_cost_tick; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Free trial, so your staff can try it first</li>
          </ul>
        </div>
      </div>
      <div class="sec-cta">
        <a class="btn btn-primary hr-cta-btn" data-from="cost" href="#demo">Book free demo and get price</a>
        <p class="or-wa">Or <a class="js-wa" href="<?php echo esc_url($ppc_wa_price); ?>" target="_blank" rel="noopener">ask the price on WhatsApp</a></p>
      </div>
    </div>
  </section>

  <!-- 8. THE SOFTWARE (real screen) + modules -->
  <section class="section">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">The software</p>
        <h2 class="h2">This is the screen your doctor works on</h2>
      </div>
      <?php // Full width so it stays readable; phones get a zoomed-in crop. Numbers 1-3 on the image match the points below. ?>
      <figure class="consult">
        <picture>
          <source media="(max-width: 720px)" srcset="<?php echo esc_url($ppc_assets . '/consult-mobile.webp'); ?>">
          <img src="<?php echo esc_url($ppc_assets . '/consult-desktop.webp'); ?>" width="2880" height="1800"
            loading="lazy" decoding="async"
            alt="Healthray doctor consultation screen for patient Lakshmi Narayanan: today's prescription and lab orders on the left, previous visits with ECG and reports on the right, and a cardiology template at the top">
        </picture>
      </figure>
      <ol class="callouts">
        <li><span class="num" aria-hidden="true">1</span><div><b>Old visits, reports and images on one screen</b><span>The doctor does not have to ask staff to find the old file.</span></div></li>
        <li><span class="num" aria-hidden="true">2</span><div><b>Prescription and lab tests from the same screen</b><span>The order goes straight to the pharmacy and lab, and is added to the bill.</span></div></li>
        <li><span class="num" aria-hidden="true">3</span><div><b>Set up as per your speciality</b><span>Templates and forms are made for your departments.</span></div></li>
      </ol>
      <figure class="doc-aside doc-wide">
        <span class="av"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Maharshi-Desai-150x150.webp" alt=""
            loading="lazy" data-fallback="MD"></span>
        <blockquote>"It's not about features for me, it just saves me from jumping between screens when the ICU is already busy."<small>Dr. Maharshi Desai, Critical Care, Akshara Apollo CBCC Cancer Centre</small></blockquote>
      </figure>

      <div class="mods">
        <div class="mod"><h3>Front desk &amp; OPD</h3><ul><li>Registration with ABHA</li><li>Appointments and reminders</li><li>OPD queue</li><li>Digital prescription</li></ul></div>
        <div class="mod"><h3>IPD</h3><ul><li>Admission, wards and beds</li><li>Nursing station</li><li>OT booking</li><li>Emergency</li></ul></div>
        <div class="mod"><h3>Lab &amp; pharmacy</h3><ul><li>Lab (LIMS)</li><li>X-ray and scans (RIS / PACS)</li><li>Pharmacy and store</li><li>Purchase and stock</li></ul></div>
        <div class="mod"><h3>Billing &amp; office</h3><ul><li>OPD and IPD billing</li><li>TPA and insurance claims</li><li>Reports (MIS)</li><li>HR, salary, NABH records</li></ul></div>
      </div>
      <p class="deploy"><b>Use it on cloud, on a server inside your hospital, or both.</b> It connects with lab machines, PACS, SMS, WhatsApp, payment and your accounting software. If you have more than one branch, you can see all of them in one place.</p>
    </div>
  </section>

  <!-- 9. FOR THE OWNER -->
  <section class="section tint">
    <div class="wrap">
      <div class="owner">
        <div>
          <p class="eyebrow">For hospital owners</p>
          <h2 class="h2">Check your hospital's numbers on your phone</h2>
          <blockquote>
            <p class="big-quote">I can see the hospital activity much more clearly now, and it is easier to identify where something is pending instead of finding out at the end of the day.</p>
          </blockquote>
          <div class="q-by"><span class="av" aria-hidden="true">HD</span><span><b>Hospital Director</b>Multispeciality Hospital, Gujarat · Verified customer</span></div>
        </div>
        <div class="tiles">
          <div class="tile"><span class="ti"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M4 19h16M6 16V9M10 16V5M14 16v-6M18 16v-3" stroke="#1B3C74" stroke-width="2" stroke-linecap="round"/></svg></span><div><b>Today's collection</b><span>Department wise, any time of the day.</span></div></div>
          <div class="tile"><span class="ti"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="4" y="4" width="16" height="16" rx="3" stroke="#1B3C74" stroke-width="2"/><path d="M8 12h8M8 8h5M8 16h3" stroke="#1B3C74" stroke-width="2" stroke-linecap="round"/></svg></span><div><b>Pending discharges and bills</b><span>So no charge is missed at the counter.</span></div></div>
          <div class="tile"><span class="ti"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M12 3l8 4v6c0 5-3.5 7.5-8 8-4.5-.5-8-3-8-8V7l8-4z" stroke="#1B3C74" stroke-width="2"/><path d="M12 8v5M12 16h.01" stroke="#1B3C74" stroke-width="2" stroke-linecap="round"/></svg></span><div><b>Low stock and expiry alerts</b><span>Know before a medicine runs out or expires.</span></div></div>
          <div class="tile"><span class="ti"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" aria-hidden="true"><rect x="7" y="2" width="10" height="20" rx="2" stroke="#1B3C74" stroke-width="2"/><path d="M11 18h2" stroke="#1B3C74" stroke-width="2" stroke-linecap="round"/></svg></span><div><b>OPD count and admissions</b><span>Daily, weekly and monthly reports.</span></div></div>
        </div>
      </div>
    </div>
  </section>

  <!-- 10. REVIEWS: each one answers one worry -->
  <section class="section">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Reviews</p>
        <h2 class="h2">What doctors say after using Healthray</h2>
      </div>
      <div class="wall">
        <blockquote class="card-q">
          <div>
            <p class="topic">Support</p>
            <p>"We've reached out a few times during OPD hours over call or email, and they respond properly and help us sort out the issue instead of just giving a generic reply."</p>
          </div>
          <footer><span class="av"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt=""
                loading="lazy" data-fallback="AG"></span><span><b>Dr. Arpit Gajjar</b><small>Shubh Multispeciality Hospital</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">Made as per our process</p>
            <p>"No two specialties work exactly the same way. Healthray was customized based on our requirements, so it works according to our process."</p>
          </div>
          <footer><span class="av"><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp" alt=""
                loading="lazy" data-fallback="KR"></span><span><b>Dr. Ketan Rupala</b><small>Rupala Kidney &amp; Prostate Hospital</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">No IT person needed</p>
            <p>"If I face any issue, I can call them and someone responds who understands the problem. That kind of support really matters because we don't have an IT person here all the time."</p>
          </div>
          <footer><span class="av"><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Milan-Modi-150x150.webp" alt=""
                loading="lazy" data-fallback="MM"></span><span><b>Dr. Milan Modi</b><small>Modi Allergy &amp; Chest Clinic</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">One software for all departments</p>
            <p>"We were using different software for different departments earlier. Now patient records, billing, pharmacy and inventory are all connected in one system."</p>
          </div>
          <footer><span class="av"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt=""
                loading="lazy" data-fallback="BR"></span><span><b>Dr. Bhaumik Rathore</b><small>Vibrant Multispecialty Hospital</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">Front desk</p>
            <p>"The automated appointment reminders have been quite useful for our front desk."</p>
          </div>
          <footer><span class="av"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Gautam-Beladiya-150x150.webp" alt=""
                loading="lazy" data-fallback="GB"></span><span><b>Dr. Gautam Beladiya</b><small>Beladiya Eye &amp; Dental Hospital</small></span></footer>
        </blockquote>
      </div>
      <?php // Carousel dots for tablet/mobile, filled by js/ppc-hims.js (hidden on desktop). ?>
      <div class="wall-dots" id="wallDots" aria-label="Choose a quote"></div>
      <div class="review-bar" aria-label="Review ratings">
        <span><b>4.8 / 5</b> on Google</span>
        <span class="logo-r"><img src="https://healthray.com/wp-content/uploads/2023/11/capterra-1.webp" alt="Capterra"
            loading="lazy"><span class="fb">Capterra</span><b>4.8</b></span>
        <span class="logo-r"><img src="https://healthray.com/wp-content/uploads/2023/11/softwaresuggest-logo.webp"
            alt="SoftwareSuggest" loading="lazy"><span class="fb">SoftwareSuggest</span><b>4.8</b></span>
        <span class="logo-r"><img src="https://healthray.com/wp-content/uploads/2024/05/g2-logo.webp" alt="G2"
            loading="lazy"><span class="fb">G2</span><b>5.0</b></span>
      </div>
    </div>
  </section>

  <!-- 11. HOW WE START + certificates -->
  <section class="section tint">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">How we start</p>
        <h2 class="h2">Start in about 2 weeks, without stopping your billing counter</h2>
        <p class="lead">We start one department at a time, so OPD and billing keep running while your staff learns.</p>
      </div>
      <ol class="plan">
        <li>
          <div class="n"><b>1</b></div><small>Setup</small>
          <h3>We set up the software and shift your data</h3>
          <p>Departments, rates, packages and bill format are set as per your hospital. Old patient and stock data is shifted, usually in 1 day.</p>
        </li>
        <li>
          <div class="n"><b>2</b></div><small>Training</small>
          <h3>We train every staff member</h3>
          <p>Reception, nurses, pharmacy, billing and doctors practise on their own work before you start.</p>
        </li>
        <li>
          <div class="n"><b>3</b></div><small>Start</small>
          <h3>One department at a time</h3>
          <p>OPD and registration first, then IPD billing, pharmacy and lab. If something goes wrong, it stays small and gets fixed quickly.</p>
        </li>
        <li>
          <div class="n"><b>4</b></div><small>After you start</small>
          <h3>Help when you need it</h3>
          <p>Call or email us during OPD hours. You talk to someone who knows how a hospital works.</p>
        </li>
      </ol>
      <div class="sec-cta">
        <a class="btn btn-primary hr-cta-btn" data-from="steps" href="#demo">Book free demo</a>
        <p>The first step is a 30-minute demo. No payment, no commitment.</p>
      </div>

      <?php include __DIR__ . '/templates/ppc-hims/certs.php'; ?>
    </div>
  </section>

  <!-- 12. QUESTIONS, in the order they stop people from filling the form -->
  <section class="section faq-sec">
    <div class="wrap faq-grid">
      <div class="faq">
        <div class="sec-head">
          <p class="eyebrow">Questions</p>
          <h2 class="h2">Questions hospital owners ask us</h2>
        </div>
        <details name="ppc-faq" open>
          <summary>Will you keep calling me after I fill the form?</summary>
          <p>No. We call you once to fix a demo time that suits you. Your number is not shared with anyone.</p>
        </details>
        <details name="ppc-faq">
          <summary>How much will it cost?</summary>
          <p>It depends on your number of beds, the modules you need, and cloud or a server in your hospital. Setup, shifting your old data and staff training are included. After the demo we send you a written quote. A free trial is also available. You can also <a class="js-wa" href="<?php echo esc_url($ppc_wa_price); ?>" target="_blank" rel="noopener">ask the price on WhatsApp</a>.</p>
        </details>
        <details name="ppc-faq">
          <summary>How long is the demo?</summary>
          <p>About 30 minutes. We show you registration to discharge, billing, TPA claims and reports, set up for a hospital of your size. It helps if your administrator or billing head joins.</p>
        </details>
        <details name="ppc-faq">
          <summary>My staff is not good with computers. Will they manage?</summary>
          <p>Yes. We train each person on their own work before you start, and we start one department at a time. Doctors get ready templates for their speciality. If anyone gets stuck, they can call us during OPD hours.</p>
        </details>
        <details name="ppc-faq">
          <summary>We already use another software. What happens to our old data?</summary>
          <p>We shift your patient, billing and stock data from your current software during setup. At BBMH Hospital in Cuttack it took 1 day, and work did not stop. If you are on paper today, we start with registration and billing.</p>
        </details>
        <details name="ppc-faq">
          <summary>What if the internet goes?</summary>
          <p>Healthray can run on a server inside your hospital, or in a mix of cloud and server. Then OPD, billing and pharmacy keep working even when the internet is down.</p>
        </details>
        <details name="ppc-faq">
          <summary>We tried a software before and it did not work out. Why will this be different?</summary>
          <p>Usually a software fails when staff get one training session and nobody sets it up as per the hospital. We set it up for your departments first, train every person on their own work, start one department at a time, and stay on call during OPD hours after you start.</p>
        </details>
        <details name="ppc-faq">
          <summary>Can you change it as per our hospital?</summary>
          <p>Yes. Forms, bill formats, packages and templates are set as per your departments and speciality during setup.</p>
        </details>
        <details name="ppc-faq">
          <summary>Is it ABDM compliant? Does it handle TPA and insurance bills?</summary>
          <p>Yes. Healthray is ABDM compliant and NHA approved, with ABHA creation at registration, and NABH certified. Over 1 million ABHA IDs have been made through Healthray. TPA and insurance claims are made from the same patient file as the IPD bill.</p>
        </details>
        <details name="ppc-faq">
          <summary>Do you have hospitals in my state?</summary>
          <p>Healthray is used in hospitals in Gujarat, Maharashtra, Uttar Pradesh, Telangana, Odisha, Puducherry and other states. Tell us your city in the form and we can share hospitals near you.</p>
        </details>
        <details name="ppc-faq">
          <summary>I sell software, or I am a student.</summary>
          <p>If you sell software, see the <a href="https://healthray.com/become-a-partner/" rel="nofollow" target="_blank">Healthray partner programme</a>. This page is for hospitals, and we do not share source code or project files.</p>
        </details>
      </div>
      <aside class="ask">
        <h3>Still have a question?</h3>
        <p>Book a free demo and ask us directly.</p>
        <a class="btn btn-primary hr-cta-btn" data-from="faq" href="#demo">Book free demo</a>
      </aside>
    </div>
  </section>

  <!-- 13. LAST CTA -->
  <section class="final">
    <div class="wrap">
      <div>
        <h2>See Healthray working for a hospital like yours</h2>
        <p>Free 30-minute live demo. We call you once to fix the time.</p>
      </div>
      <div class="final-actions">
        <a class="btn btn-white hr-cta-btn" data-from="final" href="#demo">Book free demo</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/templates/ppc-hims/footer.php'; ?>

<?php $ppc_wa_message = "Hi Healthray, I saw your ad on Facebook / Instagram and want to see a demo of your hospital software. Please share pricing."; ?>
<?php include __DIR__ . '/templates/ppc-hims/whatsapp.php'; ?>

<nav class="mbar" id="mbar" aria-label="Quick actions">
  <a class="wa js-wa" href="<?php echo esc_url($ppc_wa); ?>" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><?php echo $ppc_wa_logo; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
  <a class="go hr-cta-btn" data-from="sticky" href="#demo">Book free demo</a>
</nav>