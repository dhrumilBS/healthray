<?php
/**
 * Template Name: PPC - Hospital Management Software (HIMS) - ChatGPT
 *
 * ChatGPT Ads landing page for hospital management software (variant "chatgpt" of the
 * "hims" PPC page: the -chatgpt suffix does not change the key, see hr_ppc_page_key()).
 * Built from assets/healthray-chatgpt.html. The reader comes from a ChatGPT conversation,
 * "checking options", so the page opens with a checklist and closes their checking process.
 *
 * - Standalone render: own header (.top) and footer (.foot); templates/base-standalone.php
 *   is the Roots wrapper for every temp-ppc-*.php template (functions.php, section 12).
 * - Body class : ppc-hims   (shared with temp-ppc-hims-google.php; all CSS is scoped under it)
 * - CSS        : css/ppc-hims.css (+ section 29-30 for this page) + css/ppc-popup.css
 * - JS         : js/ppc-hims.js (shared)
 * - Shared     : templates/ppc-hims/ (setup, logo strip, video player, certificates,
 *                modules menu, footer), the same as on the Google page.
 * - Form       : CF7 stepper form c8e3c0a in the hero card and in the popup (setup.php),
 *                popup heading "Get a free demo and quotation" ($ppc_popup below).
 * - CTAs       : every "Get free demo and quotation" (.hr-cta-btn) opens that popup (js/script.js).
 * - URL        : add the page ID to lib/virtual-urls.php for a /ppc/{slug}/ address.
 */

defined('ABSPATH') || exit;

// Popup heading and text for this page (setup.php adds the form).
$ppc_popup = array(
	'title' => 'Get a free demo and quotation',
	'text'  => "30 minutes, on your hospital's size and departments.",
);
require __DIR__ . '/templates/ppc-hims/setup.php';
?>
<header class="top">
  <div class="wrap">
    <?php // Logo is not linked on purpose: no exits from an ads page. ?>
    <div class="brand"><img src="https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg" alt="Healthray"
        width="150" height="34"><span class="fb">Healthray</span></div>
    <div class="top-right">
      <a class="btn btn-primary btn-sm hr-cta-btn" href="#demo"><span class="cta-long">Get free demo and quotation</span><span
          class="cta-short">Free demo + quotation</span></a>
    </div>
  </div>
</header>

<main>
  <!-- 1. HERO: continues the ChatGPT conversation ("checking options") + the offer (demo + quotation) + form -->
  <section class="hero">
    <div class="wrap hero-grid">
      <div class="hero-copy">
        <p class="eyebrow">Hospital management software</p>
        <h1>Checking options for hospital software? See if Healthray fits your hospital.</h1>
        <p class="hero-sub">One software from OPD to discharge, used by 2,500+ hospitals and clinics. Get a free demo and
          a quotation for your hospital.</p>
        <?php // Phones only (the full proof block sits below the form there). ?>
        <p class="mini-trust" aria-label="Rated 4.8, used by 2,500+ hospitals and clinics, NABH certified, ABDM compliant"><b>★ 4.8</b> <i>·</i> <b>2,500+</b> hospitals &amp; clinics <i>·</i> NABH <i>·</i> ABDM</p>
      </div>

      <div class="form-card" id="demo">
        <h2>Get a free demo and quotation</h2>
        <p class="fc-sub">30 minutes, on your hospital's size and departments.</p>
        <?php echo do_shortcode($ppc_form); ?>
      </div>

      <div class="hero-proof">
        <div class="faces">
          <div class="pile">
            <span><img loading="lazy"
                src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp"
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
          <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
        </div>
        <div class="trust-row">
          <div class="seals">
            <span class="seal"><img loading="lazy"
                src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp"
                alt="NABH certified" width="42" height="42"><span class="fb">NABH</span></span>
            <span class="seal"><img loading="lazy" src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp"
                alt="ABDM compliant" width="42" height="42"><span class="fb">ABDM</span></span>
            <span class="seal"><img loading="lazy" src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved"
                width="42" height="42"><span class="fb">NHA</span></span>
            <span class="seal"><img loading="lazy"
                src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001"
                width="42" height="42"><span class="fb">ISO 27001</span></span>
          </div>
          <p class="seal-text"><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="ratings" aria-label="Ratings">
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span>
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
          <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
        </div>
      </div>
    </div>
  </section>

  <!-- 2. CHECKLIST: the reader arrives with ChatGPT's checklist; each row = their own question, answered plainly -->
  <section class="section tint ck-sec" aria-labelledby="ckTitle">
    <div class="wrap">
      <div class="sec-head">
        <h2 class="h2" id="ckTitle">Before you shortlist, check these 8 things</h2>
      </div>
      <dl class="ck-list">
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>Which departments does it cover?</dt>
          <dd>OPD, IPD, billing, pharmacy, lab, TPA claims and more: <a href="#modules">40+ modules</a> in one software</dd>
        </div>
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>Is it ABDM and NABH ready?</dt>
          <dd>ABDM compliant, NHA approved and NABH certified</dd>
        </div>
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>Online or our own server?</dt>
          <dd>Both. Use it online, or on a server inside your hospital.</dd>
        </div>
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>How long before we start?</dt>
          <dd>Usually 1 to 3 weeks, even for a 200-bed hospital. One department at a time, so billing doesn't stop.</dd>
        </div>
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>What happens to our old data?</dt>
          <dd>We move it for you as part of setup.</dd>
        </div>
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>Who trains our staff?</dt>
          <dd>Our team trains each department on its own screens before you start.</dd>
        </div>
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>What about support later?</dt>
          <dd>24×7, in Hindi, Gujarati, English and more.</dd>
        </div>
        <div class="ck-row">
          <dt><span class="ck-ico"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m5 12.5 4.5 4.5L19 7.5"/></svg></span>How much will it cost?</dt>
          <dd>It depends on beds, departments, and online or server. You get a quotation after the demo.</dd>
        </div>
      </dl>
      <div class="ck-cta"><a class="btn btn-primary hr-cta-btn" href="#demo">Get free demo and quotation</a></div>
    </div>
  </section>

  <?php include __DIR__ . '/templates/ppc-hims/logos.php'; ?>

  <!-- 4. PRODUCT WALKTHROUGH: "what does it look like, is it easy?" - shown right after peer proof. -->
  <section class="section tint walk" aria-labelledby="walkTitle">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">1-minute video</p>
        <h2 class="h2" id="walkTitle">See one patient go from registration to discharge in Healthray</h2>
        <p class="lead">Reception, doctor, admission, billing, TPA and discharge. Sample patient, real software.</p>
      </div>
      <?php include __DIR__ . '/templates/ppc-hims/player.php'; ?>
      <div class="walk-cta">
        <p><b>Want to see it with your departments and bill formats?</b></p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Get free demo and quotation</a>
      </div>
    </div>
  </section>

  <!-- 5. RESULTS from named hospitals (specific beats vague) -->
  <section class="section">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Results</p>
        <h2 class="h2">Real results from hospitals like yours</h2>
      </div>

      <article class="case-main">
        <div class="case-photo">
          <img src="https://healthray.com/wp-content/uploads/2025/12/Budha-Baba-Multispecialty-Hospital-1.webp"
            alt="Budha Baba Multispecialty Hospital, Cuttack" loading="lazy">
          <span class="fb">Budha Baba Multispecialty Hospital</span>
          <div class="bed-glass"><i><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></i><span><b>130 beds</b><small>Multispecialty · Cuttack, Odisha</small></span></div>
        </div>
        <div class="case-body">
          <p class="case-tag">Multispecialty hospital · Cuttack, Odisha</p>
          <h3>BBMH Hospital moved from paper files and Excel to one software in two weeks</h3>
          <p>Patient records, billing and inventory were spread across paper, Excel and separate software. Nobody had a clear view of what was pending.</p>
          <div class="nums">
            <div><b>35%</b><span>less paperwork and admin work</span></div>
            <div><b>30%</b><span>faster billing and discharge</span></div>
            <div><b>40%</b><span>fewer mistakes</span></div>
          </div>
        </div>
      </article>

      <div class="cases">
        <article class="case">
          <div class="case-meta"><span class="case-tag">Vapi, Gujarat</span><span class="bed-pill"><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>120 beds</span></div>
          <span class="big">₹25 lakh</span>
          <h3>saved a year at Vibrant Multispecialty Hospital</h3>
          <p>Billing got 30% faster and errors dropped 40% after they replaced separate software for each department
            with one.
          </p>
          <div class="who"><span class="av"><img
                src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt=""
                loading="lazy" data-fallback="BR"></span><span><b>Dr. Bhaumik Rathore</b>"There is less duplicate entry
              and it is easier to track what is pending between departments."</span></div>
        </article>
        <article class="case">
          <div class="case-meta"><span class="case-tag">Surat, Gujarat</span><span class="bed-pill"><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>250 beds</span></div>
          <span class="big">45%</span>
          <h3>shorter patient wait times at Universal Hospital</h3>
          <p>OPD, lab and pharmacy now work from the same patient file. Patient satisfaction reached 92%.</p>
          <div class="who"><span class="av" aria-hidden="true">DG</span><span><b>Divyesh Gandhi</b>Head of Operations,
              Universal Hospital</span></div>
        </article>
        <article class="case">
          <div class="case-meta"><span class="case-tag">Jabalpur, Madhya Pradesh</span><span class="bed-pill"><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>350 beds</span></div>
          <span class="big">60%</span>
          <h3>more patients keeping their appointments at Jabalpur Hospital</h3>
          <p>Workflow efficiency improved by 50%, and patient satisfaction reached 95% after scattered department data
            moved into one system.</p>
          <div class="who"><span class="av av-logo"><img
                src="<?php echo esc_url($ppc_assets . '/jabalpur-hospital-research-center.webp'); ?>" alt=""
                loading="lazy"><span class="fb">JH</span></span><span><b>Jabalpur Hospital &amp; Research Centre</b>"Healthray has given us complete patient
              visibility." Senior Consultant</span></div>
        </article>
      </div>
      <p class="case-src">Figures from Healthray case studies published on healthray.com.</p>
    </div>
  </section>



  <!-- 6. THE PRODUCT: doctor consultation screen (real screenshot) -->
  <section class="section tint">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">For your doctors</p>
        <h2 class="h2">Doctors see the full patient history on one screen, even in a busy OPD</h2>
        <p class="lead">Old visits, reports, medicines and today's prescription in one place. A consultation takes no longer
          than writing on paper.</p>
      </div>
      <figure class="consult">
        <picture>
          <source media="(max-width: 720px)" srcset="<?php echo esc_url($ppc_assets . '/consult-mobile.webp'); ?>">
          <img src="<?php echo esc_url($ppc_assets . '/consult-desktop.webp'); ?>" width="2880" height="1800"
            loading="lazy" decoding="async"
            alt="Healthray doctor consultation screen for patient Lakshmi Narayanan: today's prescription and lab orders on the left, previous visits with ECG and reports on the right, and a cardiology template at the top">
        </picture>
      </figure>
      <ol class="callouts">
        <li><span class="num" aria-hidden="true">1</span>
          <div><b>Past visits, reports and images on one screen</b><span>Doctors pick up from the last visit without
              asking staff to find the file.</span></div>
        </li>
        <li><span class="num" aria-hidden="true">2</span>
          <div><b>Prescription and lab orders in the same place</b><span>Medicines go to the pharmacy, tests go to the lab, and both are added to the
              bill automatically.</span></div>
        </li>
        <li><span class="num" aria-hidden="true">3</span>
          <div><b>Set up for your speciality</b><span>Ready templates, like the cardiology one above, so doctors don't
              type the same notes again.</span></div>
        </li>
      </ol>
      <figure class="doc-aside doc-wide">
        <span class="av"><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp"
            alt="" loading="lazy" data-fallback="DV"></span>
        <blockquote>"Morning OPD is usually very busy, so the system needs to be fast. I need old consultation, reports
          and medicines quickly while seeing the patient. Healthray shows all this on one screen… so I don't have to ask
          staff for old files."<small>Dr. Dipak Viradia, Pulmonologist, Universal Hospital</small></blockquote>
      </figure>

    </div>
  </section>

  <!-- 7. HOW THE SWITCH HAPPENS: after the proof, answering "is switching safe?" (real sequence, so it is numbered) + real certification badges -->
  <section class="section sw-sec">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Switching to Healthray</p>
        <h2 class="h2">Live in 1 to 3 weeks, without stopping your billing counter</h2>
        <p class="lead">Even a 200-bed hospital usually starts using it in one to three weeks. You switch one department at a
          time, so OPD and billing keep running.</p>
      </div>
      <ol class="plan">
        <li>
          <div class="n"><b>1</b></div><small>Setup</small>
          <h3>Set up and move your data</h3>
          <p>Departments, tariffs and bill formats are set up. Your old data moves over, usually in a day.</p>
        </li>
        <li>
          <div class="n"><b>2</b></div><small>Training</small>
          <h3>Train every department</h3>
          <p>Reception, nurses, pharmacy, billing and doctors practise on their own screens before anything goes live.
          </p>
        </li>
        <li>
          <div class="n"><b>3</b></div><small>Start</small>
          <h3>Switch one department at a time</h3>
          <p>OPD and registration first, then IPD billing, pharmacy and lab. Any issue stays small and gets fixed
            quickly.</p>
        </li>
        <li>
          <div class="n"><b>4</b></div><small>After you start</small>
          <h3>24×7 support</h3>
          <p>In English, Hindi or Gujarati, from a team that knows hospital work.</p>
        </li>
      </ol>
      <figure class="doc-aside doc-wide sw-quote">
        <span class="av" aria-hidden="true">MP</span>
        <blockquote>"The real question was whether our team would actually be able to use it in daily work... The Healthray
          team came to the hospital, trained the staff on-site and showed each workflow in a simple way."<small>Dr. Monil
            Parmar, Universal Superspeciality Hospital</small></blockquote>
      </figure>


      <?php include __DIR__ . '/templates/ppc-hims/certs.php'; ?>
    </div>
  </section>

  <!-- 8. MODULES: shown as the real Healthray menu, so visitors instantly read them as modules -->
  <section class="section mm-sec" id="modules" aria-labelledby="mmTitle">
    <div class="wrap mm-in">
      <div class="mm-copy">
        <p class="eyebrow">All departments</p>
        <h2 class="h2" id="mmTitle">40+ modules for every department, in one software</h2>
        <p class="mm-lead">Reception, doctors, nurses, pharmacy, lab and billing all work in the same software. Enter patient
          details once, and every department sees them.</p>
        <p class="mm-works"><b>Also connects with</b> Tally, lab machines, PACS, WhatsApp and ABDM. Runs on cloud or your
          own server.</p>
        <a class="btn btn-primary hr-cta-btn mm-cta-desk" href="#demo">Get free demo and quotation</a>
      </div>
      <div class="mm-app" id="mmApp">
        <div class="mm-bar">
          <img class="mm-logo" src="https://healthray.com/wp-content/uploads/2026/08/favicon-300x300.png" width="24"
            height="24" alt="">
          <b>Healthray</b><span>Menu</span>
        </div>
        <?php include __DIR__ . '/templates/ppc-hims/modules-menu.php'; ?>
                <div class="mm-more"><button type="button" id="mmMore" aria-expanded="false">Show all 40+ modules</button></div>
      </div>
      <a class="btn btn-primary hr-cta-btn mm-cta-phone" href="#demo">Get free demo and quotation</a>
    </div>
  </section>

  <!-- 8b. PRICE: answers "what will it cost?" where the reader starts asking it, with a matching CTA -->
  <section class="price-band" aria-labelledby="priceTitle">
    <div class="wrap">
      <div class="pb-card">
        <div class="pb-copy">
          <p class="eyebrow">Pricing</p>
          <h2 id="priceTitle">What will Healthray cost for your hospital?</h2>
          <p>There is no one fixed price. You pay for what your hospital needs, based on:</p>
        </div>
        <ul class="pb-factors">
          <li><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>How many beds you have</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>Which departments you need</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 18a4.5 4.5 0 0 1-.6-9 6 6 0 0 1 11.4 1.5A3.75 3.75 0 0 1 17.5 18z"/></svg>Online, or a server in your hospital</li>
        </ul>
        <div class="pb-cta">
          <a class="btn btn-primary hr-cta-btn" href="#demo">Get a quotation for my hospital</a>
          <small>No obligation. You get a quotation for your hospital.</small>
        </div>
      </div>
    </div>
  </section>

  <!-- 9. RATINGS on outside review sites (this reader trusts these more than quotes we pick) -->
  <section class="section tint rt-sec" aria-labelledby="rtTitle">
    <div class="wrap">
      <div class="sec-head">
        <h2 class="h2" id="rtTitle">Ratings on review sites</h2>
      </div>
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

  <!-- 10. QUESTIONS: in the order an evaluator asks them, "how is it different?" first -->
  <section class="section faq-sec">
    <div class="wrap faq-grid">
      <div class="faq">
        <div class="sec-head">
          <p class="eyebrow">Before you book</p>
          <h2 class="h2">Questions hospital owners ask us</h2>
        </div>
        <details name="ppc-faq" open>
          <summary>How is Healthray different from other hospital software?</summary>
          <p>Most hospitals start using Healthray in 1 to 3 weeks. We move your old data and train your staff as part of
            setup, and our support team is there 24×7 in Hindi, Gujarati and English. Healthray is ABDM compliant, NHA
            approved and NABH certified, and you can run it online or on your own server.</p>
        </details>
        <details name="ppc-faq">
          <summary>How much does Healthray cost?</summary>
          <p>The price depends on three things: how many beds you have, which departments you want to use (OPD, IPD,
            pharmacy, lab and so on), and whether you want it on the cloud or on your own server. So a 40-bed hospital
            pays less than a 300-bed hospital. Tell us your bed count in the form, and after the demo we'll send you a
            quotation for your hospital.</p>
        </details>
        <details name="ppc-faq">
          <summary>Is our patient data safe? Can we take it with us if we leave?</summary>
          <p>Your hospital owns its data. You can export your records at any time, so you are never locked in. Data is
            encrypted when stored and when sent, with automatic backups and disaster recovery. Healthray is ISO 27001
            certified.</p>
        </details>
        <details name="ppc-faq">
          <summary>Is Healthray ABDM compliant and NABH ready?</summary>
          <p>Yes. Healthray is ABDM compliant and NHA approved, with ABHA creation and linking at registration, and is
            NABH certified as healthcare software. Over 1 million ABHA IDs have been created through Healthray.</p>
        </details>
        <details name="ppc-faq">
          <summary>What support do we get after we start?</summary>
          <p>24×7 support in English, Hindi, Gujarati and more, from a team that understands how hospitals work.</p>
        </details>
        <details name="ppc-faq">
          <summary>What happens to billing if the internet goes down?</summary>
          <p>Healthray can run on a server inside your hospital, or online and on your own server together, so OPD,
            billing and pharmacy keep working when the internet is down.</p>
        </details>
        <details name="ppc-faq">
          <summary>We already use another software. Will we lose our data?</summary>
          <p>No. Moving your data is included in every setup. Patient, billing and stock records come over from your
            current system before you start. At BBMH Hospital in Cuttack, this took one day with zero downtime.</p>
        </details>
        <details name="ppc-faq">
          <summary>Does it handle Ayushman Bharat (PMJAY) and TPA claims?</summary>
          <p>Yes. PMJAY and other government schemes are built into billing and claims, along with TPA and
            insurance claims.</p>
        </details>
        <details name="ppc-faq">
          <summary>We have more than one branch. Can all of them use Healthray?</summary>
          <p>Yes. All branches run on one system with shared patient records. Each branch keeps its own tariffs,
            medicine list, departments and reports.</p>
        </details>
        <details name="ppc-faq">
          <summary>Our staff isn't good with computers. Will they manage?</summary>
          <p>Every department is trained on its own screens before you start, and departments switch one at a time. Doctors get
            templates set up for their speciality, so a consultation does not take longer than writing on paper.</p>
        </details>
        <details name="ppc-faq">
          <summary>Our hospital works differently. Can the software be changed for us?</summary>
          <p>Yes. Forms, bill formats and packages are set up for your departments and speciality before you start.</p>
        </details>
        <details name="ppc-faq">
          <summary>What happens in the demo?</summary>
          <p>A free, no-obligation 30-minute demo with a Healthray product specialist, using your own departments and bill formats.
            You'll see registration to discharge, billing, TPA claims and reports, set up for a hospital of your size, and
            we'll send you a quotation after. It helps to bring your administrator or billing head.</p>
        </details>
      </div>
      <aside class="ask">
        <h3>Still have questions?</h3>
        <p>Ask them in the demo. A Healthray hospital specialist will answer them for your hospital.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Get free demo and quotation</a>
      </aside>
    </div>
  </section>

  <!-- 11. FINAL STEP: closes their own checking process -->
  <section class="final">
    <div class="wrap">
      <div>
        <h2>Checked everything you can from here?</h2>
        <p>The rest is easier to see live. In 30 minutes we'll show you Healthray set up for a hospital like yours, and
          send you a quotation.</p>
      </div>
      <div class="final-actions">
        <a class="btn btn-white hr-cta-btn" href="#demo">Get free demo and quotation</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/templates/ppc-hims/footer.php'; ?>

<nav class="mbar" id="mbar" aria-label="Quick actions">
  <a class="go hr-cta-btn" href="#demo">Get free demo and quotation</a>
</nav>
