<?php
/**
 * Template Name: PPC - Hospital Management Software (HIMS)
 *
 * Google Ads landing page for hospital management software (variant "google" of the
 * "hims" PPC page: the -google suffix does not change the key, see hr_ppc_page_key()).
 * Built from assets/healthray-ads-ppc.html (final design).
 *
 * - Standalone render: this page has its OWN header (.top) and footer (.foot)
 *   from the PPC mockup, NOT the site header/footer. templates/base-standalone.php is used as
 *   the Roots wrapper for every temp-ppc-*.php template (functions.php,
 *   section 12), so the site nav, footer, popup and preloader are never printed.
 * - URL        : /ppc/hospital-management-software/ (lib/virtual-urls.php)
 * - Body class : ppc-hims   (all CSS is scoped under it)
 * - CSS        : css/ppc-hims.css + css/ppc-popup.css (inlined, minified, in <head>)
 * - JS         : js/ppc-hims.js
 * - Assets     : assets/ppc-hims/ (walkthrough poster, consultation screens)
 * - Form       : CF7 stepper form c8e3c0a, in the hero card AND in the popup ($ppc_form below).
 * - CTAs       : every "Book a free demo" (.hr-cta-btn) opens the popup with that form (js/script.js).
 * - Images     : no inline handlers; a failed image falls back via functions.php
 *                (data-fallback="AB" -> initials, data-fallback-src -> retry, else .fb text).
 *
 * Oct 2026 update: ?lp= headline per ad, phone trust line in the hero, logo strip with
 * bed counts (22 hospitals), bed size on result cards, doctor section title, modules shown
 * as the app menu (own section), new price answer. New CSS: section 17-20, new JS: section 6.
 *
 * Oct 2026 round 2: copy rewritten in plain words, new logo order (+ Mamta Medical College),
 * new customer quotes, Jabalpur result card (replaces Lilavati), price band after the modules
 * (section 5c), longer FAQ. New CSS: section 21-23.
 *
 * Oct 2026 round 3 (phone review): "How the switch happens" moved up after the staff worry (3b),
 * hero line in two parts (phones show the second), 7 certificates, footer in two lines on phones.
 * New CSS: section 24-25. JS: bottom bar also hides over the final CTA and footer; no video autoplay
 * on Data Saver / 2G.
 */

defined('ABSPATH') || exit;

$ppc_assets = get_stylesheet_directory_uri() . '/assets/ppc-hims';

/*
 * Lead form: the same stepper form in the hero card and in the popup that every
 * "Book a free demo" button opens (instead of the site-wide popup form).
 */
$ppc_form = '[contact-form-7 id="c8e3c0a" title="PPC Stepper Form"]';
add_filter('hr_ppc_popup_args', function () use ($ppc_form) {
	return array(
		'form'  => $ppc_form,
		'title' => 'Book a free demo',
		'text'  => "A free 30-minute demo, set up for your hospital's size and departments.",
	);
});
$ppc_headlines = array(
	'billing'  => array( 'Hospital billing software', "Hospital billing software that doesn't miss a single charge" ),
	'pharmacy' => array( 'Hospital pharmacy software', 'Hospital pharmacy software where stock and bills always match' ),
	'cloud'    => array( 'Cloud hospital management software', 'Cloud hospital software your staff will actually use' ),
	'abdm'     => array( 'ABDM compliant hospital software', 'ABDM-ready hospital software your staff will actually use' ),
	'his'      => array( 'Hospital information system (HIS)', 'Run your whole hospital on one system your staff will actually use' ),
);
$ppc_lp   = isset( $_GET['lp'] ) ? sanitize_key( wp_unslash( $_GET['lp'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$ppc_head = isset( $ppc_headlines[ $ppc_lp ] )
	? $ppc_headlines[ $ppc_lp ]
	: array( 'Hospital management software', 'Run your whole hospital on one system your staff will actually use' );
$ppc_hospitals = array(
	array( 'name' => 'HZB Arogyam Multispeciality Hospital', 'place' => 'Hazaribagh, Jharkhand', 'beds' => '150 beds', 'logo' => $ppc_assets . '/hzb-arogyam-multispeciality-hospital.webp' ),
	array( 'name' => 'Vibrant Multispecialty Hospital', 'place' => 'Vapi', 'beds' => '120 beds', 'logo' => $ppc_assets . '/vibrant-hospital.webp' ),
	array( 'name' => 'Shraddha Arogya Mandir', 'place' => 'Vapi', 'beds' => '120+ beds', 'logo' => 'https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp' ),
	array( 'name' => 'Sri Manakula Vinayagar Medical College', 'place' => 'Puducherry', 'beds' => '1,100 beds', 'college' => true, 'logo' => $ppc_assets . '/shri-manakula-vinayak-medical-college.webp' ),
	array( 'name' => 'Parivar Super Speciality Hospital', 'place' => 'Madhya Pradesh', 'beds' => '140 beds', 'logo' => $ppc_assets . '/parivar-super-speciality-hospital.webp' ),
	array( 'name' => 'Jeevan Rekha Hospital', 'place' => 'West Bengal', 'beds' => '120 beds', 'logo' => $ppc_assets . '/jeevan-rekha-hospital.webp' ),
	array( 'name' => 'Mamta Medical College & Hospital', 'place' => 'Siwan, Bihar', 'beds' => '670 beds', 'college' => true, 'logo' => $ppc_assets . '/mamta-medical-college-hospital.webp' ),
	array( 'name' => 'Budha Baba Multispecialty Hospital', 'place' => 'Cuttack, Odisha', 'beds' => '130 beds', 'logo' => $ppc_assets . '/budha-baba-multispeciality-hospital.webp' ),
	array( 'name' => 'Heritage Medical College', 'place' => 'Varanasi', 'beds' => '1,000 beds', 'college' => true, 'logo' => $ppc_assets . '/heritage-medical-college.webp' ),
	array( 'name' => 'PIMS Multi-Superspeciality Hospital', 'place' => 'Udaipur', 'beds' => '120 beds', 'logo' => $ppc_assets . '/pims-multi-superspeciality-hospital.webp' ),
	array( 'name' => 'JJ Plus Hospitals', 'place' => '', 'beds' => '135 beds', 'logo' => $ppc_assets . '/jj-plus-hospitals.webp' ),
	array( 'name' => 'Jabalpur Hospital & Research Centre', 'place' => 'Jabalpur, MP', 'beds' => '350 beds', 'logo' => $ppc_assets . '/jabalpur-hospital-research-center.webp' ),
	array( 'name' => 'Prabh Aasra Charitable Hospital', 'place' => 'Punjab', 'beds' => '120 beds', 'logo' => 'https://healthray.com/wp-content/uploads/2025/08/Prabh-Aasra-Unified-Family.webp' ),
	array( 'name' => 'Heritage Hospital', 'place' => 'Noida', 'beds' => '100 beds', 'logo' => $ppc_assets . '/heritage-hospital.webp' ),
	array( 'name' => 'Universal Hospital', 'place' => 'Surat', 'beds' => '250 beds', 'logo' => $ppc_assets . '/universal-hospital.webp' ),
	array( 'name' => 'Jupiter Hospital Group', 'place' => 'Vadodara', 'beds' => '100 beds', 'logo' => $ppc_assets . '/jupiter-hospital-group.webp' ),
);

// Small inline icons (static markup, printed as is).
$ppc_icon_bed = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>';
$ppc_icon_cap = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 9l10-5 10 5-10 5z"/><path d="M6 11v5c3 2 9 2 12 0v-5"/></svg>';
?>
<header class="top">
  <div class="wrap">
    <?php // Logo is not linked on purpose: no exits from an ads page. ?>
    <div class="brand"><img src="https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg" alt="Healthray"
        width="150" height="34"><span class="fb">Healthray</span></div>
    <div class="top-right">
      <a class="btn btn-primary btn-sm hr-cta-btn" href="#demo">Book a free demo</a>
    </div>
  </div>
</header>

<main>
  <!-- 1. HERO: keyword match + the #1 fear answered + form without scrolling -->
  <section class="hero">
    <div class="wrap hero-grid">
      <div class="hero-copy">
        <p class="eyebrow"><?php echo esc_html($ppc_head[0]); ?></p>
        <h1><?php echo esc_html($ppc_head[1]); ?></h1>
        <?php // Phones show only the second part (.hs-b), css section 25. ?>
        <p class="hero-sub"><span class="hs-a">OPD, IPD, billing, pharmacy, lab and TPA claims in one software. </span><span
            class="hs-b">Moving from paper, Excel or separate software? Go live in 1 to 3 weeks, with your data moved,
            staff trained and 24×7 support.</span></p>
        <?php // Phones only (the full proof block sits below the form there). ?>
        <p class="mini-trust" aria-label="Rated 4.8, used by 2,500+ hospitals and clinics, NABH certified, ABDM compliant"><b>★ 4.8</b> <i>·</i> <b>2,500+</b> hospitals &amp; clinics <i>·</i> NABH <i>·</i> ABDM</p>
      </div>

      <div class="form-card" id="demo">
        <h2>Book a free demo</h2>
        <p class="fc-sub">A free 30-minute demo, set up for your hospital's size and departments.</p>
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

  <!-- 2. HOSPITALS + SIZE: "a hospital like mine uses this". Bed badge on each tile (confirmed numbers only). -->
  <section class="logos" aria-label="Hospitals using Healthray">
    <div class="wrap">
      <p class="lg-label">Hospitals that moved to Healthray</p>
      <div class="marquee">
        <div class="marquee-track">
          <?php // The list is printed twice for a seamless loop; the copy is hidden from screen readers. ?>
          <?php foreach (array(false, true) as $ppc_copy) : ?>
          <ul<?php echo $ppc_copy ? ' aria-hidden="true"' : ''; ?>>
            <?php foreach ($ppc_hospitals as $ppc_h) :
              $ppc_alt = $ppc_h['name'] . ($ppc_h['place'] ? ', ' . $ppc_h['place'] : ''); ?>
            <li>
              <span class="lg-tile<?php echo $ppc_h['logo'] ? '' : ' noimg'; ?><?php echo empty($ppc_h['zoom']) ? '' : ' lg-zoom'; ?>">
                <?php if ($ppc_h['logo']) : ?>
                <img src="<?php echo esc_url($ppc_h['logo']); ?>" alt="<?php echo $ppc_copy ? '' : esc_attr($ppc_alt); ?>" loading="lazy">
                <?php endif; ?>
                <span class="lg-name"><b><?php echo esc_html($ppc_h['name']); ?></b><?php if ($ppc_h['place']) : ?><small><?php echo esc_html($ppc_h['place']); ?></small><?php endif; ?></span>
              </span>
              <?php if ($ppc_h['beds']) : ?>
              <?php if (! empty($ppc_h['college'])) : ?>
              <span class="lg-badge mc"><?php echo $ppc_icon_cap; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Medical college · <?php echo esc_html($ppc_h['beds']); ?></span>
              <?php else : ?>
              <span class="lg-badge"><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html($ppc_h['beds']); ?></span>
              <?php endif; ?>
              <?php endif; ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- 3. PRODUCT WALKTHROUGH: "what does it look like, is it easy?" - shown right after peer proof. -->
  <section class="section tint walk" aria-labelledby="walkTitle">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">1-minute video</p>
        <h2 class="h2" id="walkTitle">See one patient go from registration to discharge in Healthray</h2>
        <p class="lead">Reception, doctor, admission, billing, TPA and discharge. Sample patient, real software.</p>
      </div>
      <div class="player" id="player">
        <?php // Autoplays muted and loops on screen, pauses off screen; play/pause and fullscreen buttons (js/ppc-hims.js, section 3). ?>
        <div class="player-screen">
          <img class="player-poster" loading="lazy" decoding="async"
            src="<?php echo esc_url($ppc_assets . '/walkthrough-poster.jpg'); ?>" width="1600" height="900"
            alt="Healthray dashboard: OPD visits, IPD admissions, bed occupancy, collections and today's patients">
          <video id="walkVideo" muted loop playsinline preload="none" disablepictureinpicture disableremoteplayback
            controlslist="nodownload noremoteplayback" width="1600" height="900" aria-hidden="true">
            <source src="https://healthray.com/wp-content/uploads/2026/10/healthray-walkthrough.mp4" type="video/mp4">
          </video>
        </div>
        <button type="button" class="vt" id="walkBtn" aria-label="Play the walkthrough">
          <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" />
          </svg>
          <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true">
            <rect x="6" y="5" width="4" height="14" rx="1" />
            <rect x="14" y="5" width="4" height="14" rx="1" />
          </svg>
        </button>
        <?php // Hidden by js/ppc-hims.js when the browser has no fullscreen support. ?>
        <button type="button" class="vt vt-fs" id="walkFs" aria-label="Watch the walkthrough full screen">
          <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
            stroke-linejoin="round" aria-hidden="true">
            <path d="M4 9V5a1 1 0 0 1 1-1h4M15 4h4a1 1 0 0 1 1 1v4M20 15v4a1 1 0 0 1-1 1h-4M9 20H5a1 1 0 0 1-1-1v-4" />
          </svg>
        </button>
      </div>
      <div class="walk-cta">
        <p><b>Want to see this set up for your hospital?</b> Get a free 30-minute demo with your own workflows.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
    </div>
  </section>

  <!-- 3. THE REAL FEAR: "will my staff use it / will switching break things?" answered by customers, not by us -->
  <section class="section">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">The question every hospital asks first</p>
        <h2 class="h2">Will your staff actually use it? Here's what hospitals told us after switching.</h2>
      </div>
      <div class="fear">
        <div>
          <blockquote>
            <p class="big-quote">For us, buying the software was not the main concern. The real question was whether our
              team would actually be able to use it in daily work… The Healthray team came to the hospital, trained the
              staff on-site and showed each workflow in a simple way… Now the staff uses it comfortably in their own
              roles.</p>
          </blockquote>
          <div class="q-by">
            <span class="q-icon q-initials" aria-hidden="true">MP</span>
            <span><b>Dr. Monil Parmar</b>Universal Superspeciality Hospital<br><span class="verified"><svg width="12"
                  height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                  <path d="m5 12.5 4.5 4.5L19 7.5" stroke="#047857" stroke-width="3" stroke-linecap="round"
                    stroke-linejoin="round" />
                </svg>Verified Healthray customer</span></span>
          </div>
          <div class="bbmh-note">
            <p>"We already had different systems and years of data, so switching was not something we wanted to do
              casually… Healthray's team worked with us through the setup and configuration, and we were able to bring
              the major workflows into one system."<br><span class="bbmh-by">CIO / IT Head, Multispecialty Hospital ·
                Verified customer</span></p>
            <p><b>Moving your old data and training your staff are part of every setup.</b> One department switches at a
              time, so daily work doesn't stop.</p>
          </div>
        </div>
        <div class="reel-wrap">
          <div class="video" id="video">
            <?php // Customer video hosted on healthray.com; plays with sound when tapped (js/ppc-hims.js). ?>
            <video id="storyVideo" playsinline preload="metadata" disablepictureinpicture controlslist="nodownload"
              src="https://healthray.com/wp-content/uploads/2026/08/Universal-Hospital-Surat-Review_-How-Healthray-Streamlined-Our-Workflow.mp4#t=0.5"></video>
            <button type="button" id="playBtn" class="story-cover">
              <img src="https://i.ytimg.com/vi/VRHZ9ejnBWk/oardefault.jpg" alt="" loading="lazy"
                data-fallback-src="https://i.ytimg.com/vi/VRHZ9ejnBWk/hqdefault.jpg">
              <span class="shade"></span>
              <span class="tag">Customer story</span>
              <span class="cap"><span class="watch"><i><svg width="14" height="14" viewBox="0 0 24 24"
                      aria-hidden="true">
                      <path d="M8 5v14l11-7z" fill="#fff" />
                    </svg></i>Watch his story</span><b>Divyesh Gandhi</b>Head of Operations, Universal Hospital,
                Surat</span>
            </button>
            <button type="button" class="vt" id="storyBtn" aria-label="Pause the video" hidden>
              <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true">
                <path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" />
              </svg>
              <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true">
                <rect x="6" y="5" width="4" height="14" rx="1" />
                <rect x="14" y="5" width="4" height="14" rx="1" />
              </svg>
            </button>
          </div>
          <p class="video-alt">Universal Hospital, Surat: how Healthray streamlined their workflow.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- 3b. HOW THE SWITCH HAPPENS: placed right after the staff worry, so the "how" answers the fear (real sequence, so it is numbered) + real certification badges -->
  <section class="section sw-sec">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Switching to Healthray</p>
        <h2 class="h2">Live in 1 to 3 weeks, without stopping your billing counter</h2>
        <p class="lead">Even a 200-bed hospital usually goes live in one to three weeks. You switch one department at a
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
          <div class="n"><b>3</b></div><small>Go-live</small>
          <h3>Switch one department at a time</h3>
          <p>OPD and registration first, then IPD billing, pharmacy and lab. Any issue stays small and gets fixed
            quickly.</p>
        </li>
        <li>
          <div class="n"><b>4</b></div><small>After go-live</small>
          <h3>24×7 support</h3>
          <p>In English, Hindi or Gujarati, from a team that knows hospital work.</p>
        </li>
      </ol>

      <div class="certs">
        <p>Certified and compliant</p>
        <ul>
          <li><span class="seal"><img
                src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt=""
                loading="lazy"><span class="fb">NABH</span></span>NABH certified</li>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp"
                alt="" loading="lazy"><span class="fb">ABDM</span></span>ABDM compliant</li>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt=""
                loading="lazy"><span class="fb">NHA</span></span>NHA approved</li>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/abha-healthray-150x150.webp"
                alt="" loading="lazy"><span class="fb">ABHA</span></span>ABHA integrated</li>
          <li><span class="seal"><img
                src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt=""
                loading="lazy"><span class="fb">ISO</span></span>ISO 27001</li>
          <li><span class="seal"><img
                src="https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp" alt=""
                loading="lazy"><span class="fb">HIPAA</span></span>HIPAA-aligned</li>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp"
                alt="" loading="lazy"><span class="fb">HL7</span></span>Connects with lab machines</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- 4. RESULTS from named hospitals (specific beats vague) -->
  <section class="section tint">
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
          <p>Patient records, billing and inventory were spread across paper, Excel and separate software. Nobody had a
            clear view of what was pending.</p>
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



  <!-- 5. THE PRODUCT: doctor consultation screen (real screenshot) -->
  <section class="section">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">For your doctors</p>
        <h2 class="h2">Doctors see the full patient history on one screen, even in a busy OPD</h2>
        <p class="lead">Old visits, reports, medicines and today's prescription in one place. A consultation takes no longer
          than writing on paper.</p>
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

  <!-- 5b. MODULES: shown as the real Healthray menu, so visitors instantly read them as modules -->
  <section class="section mm-sec" aria-labelledby="mmTitle">
    <div class="wrap mm-in">
      <div class="mm-copy">
        <p class="eyebrow">All departments</p>
        <h2 class="h2" id="mmTitle">40+ modules for every department, in one software</h2>
        <p class="mm-lead">Reception, doctors, nurses, pharmacy, lab and billing all work in the same software. Enter patient
          details once, and every department sees them.</p>
        <p class="mm-works"><b>Also connects with</b> Tally, lab machines, PACS, WhatsApp and ABDM. Runs on cloud or your
          own server.</p>
        <a class="btn btn-primary hr-cta-btn mm-cta-desk" href="#demo">Book a free demo</a>
      </div>
      <div class="mm-app" id="mmApp">
        <div class="mm-bar">
          <img class="mm-logo" src="https://healthray.com/wp-content/uploads/2026/08/favicon-300x300.png" width="24"
            height="24" alt="">
          <b>Healthray</b><span>Menu</span>
        </div>
        <nav class="mm-menu" aria-label="Healthray modules">
          <div class="mm-grp">
            <h3>Front desk</h3>
            <ul>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 19a5.5 5.5 0 0 1 11 0M16 11h5M18.5 8.5v5"/></svg><span>Registration with ABHA</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="5" width="17" height="15" rx="2"/><path d="M3.5 10h17M8 3v4M16 3v4"/></svg><span>Appointments</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="8" cy="9" r="3"/><circle cx="17" cy="10" r="2.4"/><path d="M2.5 19a5.5 5.5 0 0 1 11 0M14 19a4 4 0 0 1 7.5-1.5"/></svg><span>OPD</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16v3a2 2 0 0 0 0 4v3H4v-3a2 2 0 0 0 0-4z"/><path d="M12 7v10" stroke-dasharray="2 2"/></svg><span>Token display (queue)</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="5" y="4" width="14" height="17" rx="2"/><path d="M9 4h6v3H9zM9 13l2 2 4-4"/></svg><span>Health check-up packages</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2.5 12S6 5.5 12 5.5 21.5 12 21.5 12 18 18.5 12 18.5 2.5 12 2.5 12z"/><circle cx="12" cy="12" r="3"/></svg><span>Visitor management</span></li>
            </ul>
          </div>
          <div class="mm-grp">
            <h3>Doctor &amp; emergency</h3>
            <ul>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3v6a4 4 0 0 0 8 0V3"/><path d="M10 13v2a5 5 0 0 0 10 0v-2"/><circle cx="20" cy="11" r="2"/></svg><span>EMR &amp; e-prescription</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3a6 6 0 0 1 6 6v6H6V9a6 6 0 0 1 6-6zM4 19h16M12 15v4"/></svg><span>Emergency cases</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 11 12 4l8 7v9H4z"/><path d="M10 20v-5h4v5"/></svg><span>Home care</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 11h18a9 9 0 0 1-18 0zM8 7c0-2 2-2 2-4M13 7c0-2 2-2 2-4"/></svg><span>Patient diet</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M2 16V7h11v9M13 10h4l4 4v2h-8"/><circle cx="6.5" cy="17" r="1.8"/><circle cx="17" cy="17" r="1.8"/><path d="M7.5 9.5v4M5.5 11.5h4"/></svg><span>Ambulance</span></li>
            </ul>
          </div>
          <div class="mm-grp">
            <h3>IPD &amp; OT</h3>
            <ul>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg><span>IPD admission &amp; discharge</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9v10M3 16h12v3M15 16v-2.5a2.5 2.5 0 0 0-2.5-2.5H9v5"/><circle cx="6" cy="13" r="1.6"/><path d="m16 7 2 2 4-4"/></svg><span>Bed availability</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg><span>Ward status board</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 7.5v9M7.5 12h9"/></svg><span>OT management</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 7v5l3 2"/></svg><span>OT schedule board</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3v4M12 17v4M3 12h4M17 12h4M5.6 5.6l2.8 2.8M15.6 15.6l2.8 2.8M18.4 5.6l-2.8 2.8M8.4 15.6l-2.8 2.8"/></svg><span>CSSD (sterilisation)</span></li>
            </ul>
          </div>
          <div class="mm-grp">
            <h3>Lab &amp; diagnostics</h3>
            <ul>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 1.7 3h10.6A2 2 0 0 0 19 18l-5-9V3M7.5 15h9"/></svg><span>Laboratory (LIMS)</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8V5a1 1 0 0 1 1-1h3M16 4h3a1 1 0 0 1 1 1v3M20 16v3a1 1 0 0 1-1 1h-3M8 20H5a1 1 0 0 1-1-1v-3"/><circle cx="12" cy="12" r="3.5"/></svg><span>Radiology</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3s6 6.5 6 11a6 6 0 0 1-12 0c0-4.5 6-11 6-11z"/></svg><span>Blood bank</span></li>
            </ul>
          </div>
          <div class="mm-grp">
            <h3>Pharmacy &amp; stores</h3>
            <ul>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="9" width="18" height="6" rx="3" transform="rotate(-45 12 12)"/><path d="m9.2 9.2 5.6 5.6"/></svg><span>Pharmacy</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3.5 7.5 12 3l8.5 4.5v9L12 21l-8.5-4.5z"/><path d="M3.5 7.5 12 12l8.5-4.5M12 12v9"/></svg><span>Inventory</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 9 4.5 4h15L21 9M3 9h18v11H3zM3 9a3 3 0 0 0 6 0 3 3 0 0 0 6 0 3 3 0 0 0 6 0"/><path d="M10 20v-5h4v5"/></svg><span>Store management</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14.5 6.5a4 4 0 0 0 5 5L12 19a2.1 2.1 0 0 1-3-3z"/><circle cx="17" cy="7" r="1"/></svg><span>Equipment management</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg><span>Biomedical waste</span></li>
            </ul>
          </div>
          <div class="mm-grp">
            <h3>Billing &amp; insurance</h3>
            <ul>
              <li class="mm-item on"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2z"/><path d="M9 8h6M9 12h6M9 16h3"/></svg><span>OPD &amp; IPD billing</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 3 4.5 6v6c0 4.5 3.2 7.8 7.5 9 4.3-1.2 7.5-4.5 7.5-9V6z"/><path d="m9 12 2 2 4-4"/></svg><span>TPA &amp; insurance</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2M3 12h18"/></svg><span>Treatment packages</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5.5" width="18" height="13" rx="2"/><path d="M3 10h18M7 15h4"/></svg><span>Payments</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M5 4h12a2 2 0 0 1 2 2v14H7a2 2 0 0 1-2-2z"/><path d="M5 18a2 2 0 0 1 2-2h12"/></svg><span>Tally accounting sync</span></li>
            </ul>
          </div>
          <div class="mm-grp">
            <h3>Management</h3>
            <ul>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg><span>Reports &amp; analytics</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 6a1 1 0 0 1 1-1h5l2 2h9a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z"/></svg><span>MRD (medical records)</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2.2"/><path d="M5.5 16a3.5 3.5 0 0 1 7 0M15 10h3M15 14h3"/></svg><span>HRMS (staff &amp; payroll)</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="m12 3.5 2.6 5.3 5.9.8-4.3 4.1 1 5.8L12 16.8l-5.2 2.7 1-5.8L3.5 9.6l5.9-.8z"/></svg><span>Patient feedback</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 4 2.5 20h19z"/><path d="M12 10v4M12 17h.01"/></svg><span>Patient complaints</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 6h11M9 12h11M9 18h11M4 6h.01M4 12h.01M4 18h.01"/></svg><span>Staff activity log</span></li>
              <li class="mm-item"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 8h14l-3-3M20 16H6l3 3"/></svg><span>Multi-branch control</span></li>
              <li class="mm-item more"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="8.5"/><path d="M12 8v8M8 12h8"/></svg><span>+ more modules, ask in your demo</span></li>
            </ul>
          </div>
        </nav>
        <?php // Phones: the menu is cut to a preview; this button opens all of it (js/ppc-hims.js, section 6). ?>
        <div class="mm-more"><button type="button" id="mmMore" aria-expanded="false">Show all 40+ modules</button></div>
      </div>
      <a class="btn btn-primary hr-cta-btn mm-cta-phone" href="#demo">Book a free demo</a>
    </div>
  </section>

  <!-- 5c. PRICE: answers "what will it cost?" where the reader starts asking it, with a matching CTA -->
  <section class="price-band" aria-labelledby="priceTitle">
    <div class="wrap">
      <div class="pb-card">
        <div class="pb-copy">
          <p class="eyebrow">Pricing</p>
          <h2 id="priceTitle">What will Healthray cost for your hospital?</h2>
          <p>There is no one fixed price. You pay for what your hospital needs, based on:</p>
        </div>
        <ul class="pb-factors">
          <li><?php echo $ppc_icon_bed; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>Number of beds</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="3.5" width="7" height="7" rx="1.5"/><rect x="3.5" y="13.5" width="7" height="7" rx="1.5"/><rect x="13.5" y="13.5" width="7" height="7" rx="1.5"/></svg>Departments you use</li>
          <li><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 18a4.5 4.5 0 0 1-.6-9 6 6 0 0 1 11.4 1.5A3.75 3.75 0 0 1 17.5 18z"/></svg>Cloud or your own server</li>
        </ul>
        <div class="pb-cta">
          <a class="btn btn-primary hr-cta-btn" href="#demo">Get a price for my hospital</a>
          <small>Free, no obligation. A quote matched to your hospital's size.</small>
        </div>
      </div>
    </div>
  </section>

  <!-- 6. DOCTOR WALL: each quote answers one objection (support, customisation, visibility, no IT staff) -->
  <section class="section tint">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Reviews</p>
        <h2 class="h2">What doctors and hospital teams say about Healthray</h2>
      </div>
      <div class="wall">
        <blockquote class="card-q">
          <div>
            <p class="topic">Support</p>
            <p>"We've reached out a few times during OPD hours over call or email, and they respond properly and help us
              sort out the issue instead of just giving a generic reply."</p>
          </div>
          <footer><span class="av"><img
                src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt=""
                loading="lazy" data-fallback="AG"></span><span><b>Dr. Arpit Gajjar</b><small>Shubh Multispeciality
                Hospital</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">Fits our process</p>
            <p>"No two specialties work exactly the same way. Healthray was customized based on our requirements, so it
              works according to our process."</p>
          </div>
          <footer><span class="av"><img
                src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp" alt=""
                loading="lazy" data-fallback="KR"></span><span><b>Dr. Ketan Rupala</b><small>Rupala Kidney &amp;
                Prostate Hospital</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">Visibility for the owner</p>
            <p>"I can see the hospital activity much more clearly now, and it is easier to identify where something is
              pending instead of finding out at the end of the day."</p>
          </div>
          <footer><span class="av" aria-hidden="true">HD</span><span><b>Hospital Director</b><small>Multispecialty
                Hospital, Gujarat · Verified customer</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">No IT team needed</p>
            <p>"If I face any issue, I can call them and someone responds who understands the problem. That kind of
              support really matters because we don't have an IT person here all the time."</p>
          </div>
          <footer><span class="av"><img
                src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Milan-Modi-150x150.webp" alt="" loading="lazy"
                data-fallback="MM"></span><span><b>Dr. Milan Modi</b><small>Modi Allergy &amp; Chest
                Clinic</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">One system</p>
            <p>"A surgical patient passes through many teams before going home… With Healthray, these departments are
              connected on the same patient record, which has made the overall workflow smoother and reduced confusion
              for our team."</p>
          </div>
          <footer><span class="av"><img
                src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp" alt=""
                loading="lazy" data-fallback="VD"></span><span><b>Dr. Vimal Dhaduk</b><small>Gastrointestinal Surgeon,
                VR Group of Hospitals</small></span></footer>
        </blockquote>
        <blockquote class="card-q">
          <div>
            <p class="topic">Front desk</p>
            <p>"The automated appointment reminders have been quite useful for our front desk. It reduces a lot of
              routine calling because patients are automatically reminded."</p>
          </div>
          <footer><span class="av"><img
                src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Gautam-Beladiya-150x150.webp" alt=""
                loading="lazy" data-fallback="GB"></span><span><b>Dr. Gautam Beladiya</b><small>Beladiya Eye &amp;
                Dental Hospital</small></span></footer>
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

  <!-- 8. OBJECTIONS -->
  <section class="section faq-sec">
    <div class="wrap faq-grid">
      <div class="faq">
        <div class="sec-head">
          <p class="eyebrow">Before you book</p>
          <h2 class="h2">Questions hospital owners ask us</h2>
        </div>
        <details name="ppc-faq" open>
          <summary>How much does Healthray cost?</summary>
          <p>The price depends on three things: how many beds you have, which departments you want to use (OPD, IPD,
            pharmacy, lab and so on), and whether you want it on the cloud or on your own server. So a 40-bed hospital
            pays less than a 300-bed hospital. Tell us your bed count in the form, and after the demo we'll send you a
            written price for your hospital within 24 hours.</p>
        </details>
        <details name="ppc-faq">
          <summary>What happens to billing if the internet goes down?</summary>
          <p>Healthray can run on-premise or in a hybrid setup inside your hospital network, so OPD, billing and
            pharmacy keep working during an outage.</p>
        </details>
        <details name="ppc-faq">
          <summary>We already use another software. Will we lose our data?</summary>
          <p>No. Moving your data is included in every setup. Patient, billing and stock records come over from your
            current system before you go live. At BBMH Hospital in Cuttack, this took one day with zero downtime.</p>
        </details>
        <details name="ppc-faq">
          <summary>Is our patient data safe? Can we take it with us if we leave?</summary>
          <p>Your hospital owns its data. You can export your records at any time, so you are never locked in. Data is
            encrypted when stored and when sent, with automatic backups and disaster recovery. Healthray is ISO 27001
            certified.</p>
        </details>
        <details name="ppc-faq">
          <summary>Our staff isn't good with computers. Will they manage?</summary>
          <p>Every role is trained on its own screens before go-live, and departments switch one at a time. Doctors get
            templates set up for their speciality, so a consultation does not take longer than writing on paper.</p>
        </details>
        <details name="ppc-faq">
          <summary>Our hospital works differently. Can the software be changed for us?</summary>
          <p>Yes. Workflows, forms, bill formats and packages are configured for your departments and speciality during
            setup.</p>
        </details>
        <details name="ppc-faq">
          <summary>What support do we get after go-live?</summary>
          <p>24×7 support in English, Hindi, Gujarati and more, from a team that understands hospital workflows.</p>
        </details>
        <details name="ppc-faq">
          <summary>Does it handle Ayushman Bharat (PMJAY) and TPA claims?</summary>
          <p>Yes. PMJAY and other government scheme workflows are built into billing and claims, along with TPA and
            insurance claims.</p>
        </details>
        <details name="ppc-faq">
          <summary>Is Healthray ABDM compliant and NABH ready?</summary>
          <p>Yes. Healthray is ABDM compliant and NHA approved, with ABHA creation and linking at registration, and is
            NABH certified as healthcare software. Over 1 million ABHA IDs have been created through Healthray.</p>
        </details>
        <details name="ppc-faq">
          <summary>We have more than one branch. Can all of them use Healthray?</summary>
          <p>Yes. All branches run on one system with shared patient records. Each branch keeps its own tariffs,
            medicine list, departments and reports.</p>
        </details>
        <details name="ppc-faq">
          <summary>What happens in the demo?</summary>
          <p>A free, no-obligation 30-minute demo with a Healthray product specialist, using your own workflows. You'll
            see registration to discharge, billing, TPA claims and reports, set up for a hospital of your size. It helps
            to bring your administrator or billing head.</p>
        </details>
      </div>
      <aside class="ask">
        <h3>Still have questions?</h3>
        <p>Book a free demo. A Healthray hospital specialist will answer them for your hospital, on your workflow.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
      </aside>
    </div>
  </section>

  <!-- 9. FINAL CTA -->
  <section class="final">
    <div class="wrap">
      <div>
        <h2>See how Healthray will work in your hospital</h2>
        <p>Tell us your bed count. In 30 minutes, we'll show you OPD, IPD, billing and pharmacy set up the way your
          hospital works.
        </p>
      </div>
      <div class="final-actions">
        <a class="btn btn-white hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
    </div>
  </section>
</main>

<footer class="foot">
  <div class="wrap">
    <span>© <?php echo esc_html(wp_date('Y')); ?> Healthray Technologies Pvt. Ltd.<span class="foot-sep"> · </span><span
        class="foot-loc">Surat, Gujarat · Since 2019</span></span>
    <a href="https://healthray.com/privacy-policy/" rel="nofollow">Privacy policy</a>
  </div>
</footer>

<nav class="mbar" id="mbar" aria-label="Quick actions">
  <a class="go hr-cta-btn" href="#demo">Book a free demo</a>
</nav>