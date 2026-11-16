<?php
/**
 * Template Name: Modules Hub
 */

$hr_mod_cards = function_exists( 'healthray_module_cards' ) ? healthray_module_cards( get_queried_object_id() ) : array();
$hr_mod_faqs  = function_exists( 'healthray_modules_faq_items' ) ? healthray_modules_faq_items() : array();
?>
<main class="hx" id="modules-main">

<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <symbol id="i-wa" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.4a.5.5 0 0 0 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 2.9 2.9 0 0 0-.9 2.2 5 5 0 0 0 1 2.7 11.6 11.6 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.2c0-.1-.2-.2-.4-.3Z"/></symbol>
</svg>

<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <h1>Healthray Modules for Hospitals and Clinics</h1>
      <p class="sub">Each module runs one part of your hospital, like the OPD or the wards. They all work on the same patient file, so a patient's visits, reports and bills stay together.</p>
      <p class="offer"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm-1.2 14.2-4-4 1.4-1.4 2.6 2.6 5.6-5.6 1.4 1.4Z"/></svg><span>Free demo on a video call. We move your old data and train your staff for free.</span></p>

      <div class="hero-proof">
        <div class="faces">
          <div class="pile" aria-hidden="true">
            <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt="" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-kaivan-shah-150x150.webp" alt="" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Gautam-Beladiya-150x150.webp" alt="" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt="" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="" loading="lazy" width="44" height="44"></span>
          </div>
          <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
        </div>
        <div class="trust-row">
          <div class="seals">
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="NABH certified" loading="lazy" width="42" height="42"></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp" alt="ABDM compliant" loading="lazy" width="42" height="42"></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved" loading="lazy" width="42" height="42"></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001" loading="lazy" width="42" height="42"></span>
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
    <div class="formbox" id="demo">
      <h2>Book a Free Demo</h2>
      <p class="fsub">A video call. We show the modules you need, set up for your hospital or clinic.</p>
      <?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
    </div>
  </div>
</section>

<section class="moved" aria-label="Hospitals that moved to Healthray">
  <p>Hospitals that use Healthray</p>
  <div class="track" aria-hidden="false">
    <ul>
      <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/vibrant-hospital.webp" alt="Vibrant Multispecialty Hospital, Vapi" loading="lazy"></li>
      <li><span class="beds">120+ beds</span><img src="https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp" alt="Shraddha Arogya Mandir, Vapi" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 1,100 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/shri-manakula-vinayak-medical-college.webp" alt="Sri Manakula Vinayagar Medical College, Puducherry" loading="lazy"></li>
      <li><span class="beds">140 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/parivar-super-speciality-hospital.webp" alt="Parivar Super Speciality Hospital, Madhya Pradesh" loading="lazy"></li>
      <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jeevan-rekha-hospital.webp" alt="Jeevan Rekha Hospital, West Bengal" loading="lazy"></li>
      <li><span class="beds">150 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/hzb-arogyam-multispeciality-hospital.webp" alt="HZB Arogyam Multispeciality Hospital, Hazaribagh" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 670 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/mamta-medical-college-hospital.webp" alt="Mamta Medical College and Hospital, Siwan" loading="lazy"></li>
      <li><span class="beds">130 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/budha-baba-multispeciality-hospital.webp" alt="Budha Baba Multispecialty Hospital, Cuttack" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 1,000 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-medical-college.webp" alt="Heritage Medical College, Varanasi" loading="lazy"></li>
    </ul>
    <ul aria-hidden="true">
      <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/vibrant-hospital.webp" alt="" loading="lazy"></li>
      <li><span class="beds">120+ beds</span><img src="https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp" alt="" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 1,100 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/shri-manakula-vinayak-medical-college.webp" alt="" loading="lazy"></li>
      <li><span class="beds">140 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/parivar-super-speciality-hospital.webp" alt="" loading="lazy"></li>
      <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jeevan-rekha-hospital.webp" alt="" loading="lazy"></li>
      <li><span class="beds">150 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/hzb-arogyam-multispeciality-hospital.webp" alt="" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 670 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/mamta-medical-college-hospital.webp" alt="" loading="lazy"></li>
      <li><span class="beds">130 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/budha-baba-multispeciality-hospital.webp" alt="" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 1,000 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-medical-college.webp" alt="" loading="lazy"></li>
    </ul>
  </div>
</section>

<section class="sec soft" id="modules">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow">Modules</span>
      <h2>Pick a Module to See What It Does</h2>
      <p class="hx-lead">Each page shows the module's screens, what changes in your hospital, and what doctors say about it.</p>
    </div>
    <?php if ( $hr_mod_cards ) : ?>
    <ul class="mods">
      <?php foreach ( $hr_mod_cards as $hr_mod ) : ?>
      <li class="mod">
        <?php if ( ! empty( $hr_mod['image'] ) ) : ?>
        <a class="mod-pic<?php echo ! empty( $hr_mod['cutout'] ) ? ' cut' : ''; ?>" href="<?php echo esc_url( $hr_mod['url'] ); ?>" tabindex="-1" aria-hidden="true" data-module="<?php echo esc_attr( $hr_mod['key'] ); ?>"><img src="<?php echo esc_url( $hr_mod['image'] ); ?>" alt="" loading="lazy" width="1200" height="800"></a>
        <?php endif; ?>
        <div class="mod-body">
          <?php if ( ! empty( $hr_mod['eyebrow'] ) ) : ?><span class="eyebrow"><?php echo esc_html( $hr_mod['eyebrow'] ); ?></span><?php endif; ?>
          <h3><a href="<?php echo esc_url( $hr_mod['url'] ); ?>" data-module="<?php echo esc_attr( $hr_mod['key'] ); ?>"><?php echo esc_html( $hr_mod['title'] ); ?></a></h3>
          <?php if ( ! empty( $hr_mod['summary'] ) ) : ?><p class="sum"><?php echo esc_html( $hr_mod['summary'] ); ?></p><?php endif; ?>
          <?php if ( ! empty( $hr_mod['points'] ) ) : ?>
          <ul class="ticks">
            <?php foreach ( $hr_mod['points'] as $hr_point ) : ?><li><?php echo esc_html( $hr_point ); ?></li><?php endforeach; ?>
          </ul>
          <?php endif; ?>
          <div class="mod-links">
            <a class="btn btn-main" href="<?php echo esc_url( $hr_mod['url'] ); ?>" data-module="<?php echo esc_attr( $hr_mod['key'] ); ?>"><?php echo esc_html( $hr_mod['cta'] ); ?></a>
            <a class="mod-demo" href="#demo">Book a demo</a>
          </div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
    <?php endif; ?>
  </div>
</section>

<section class="ask" id="start-small">
  <div class="wrap ask-grid">
    <div>
      <span class="eyebrow">Every hospital owner asks this</span>
      <h2>“Do I have to buy every module at once?”</h2>
      <p>No. Start with the part you need most. A clinic can use the OPD part alone. A hospital adds IPD, lab, pharmacy and TPA in the same software when it needs them.</p>
      <p class="certs-h">Our certificates</p>
      <div class="certs">
        <figure><img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="" loading="lazy" width="56" height="56"><figcaption>NABH certified</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/abdm-healthray-150x150.webp" alt="" loading="lazy" width="56" height="56"><figcaption>ABDM compliant</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="" loading="lazy" width="56" height="56"><figcaption>NHA approved</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/abha-healthray-150x150.webp" alt="" loading="lazy" width="56" height="56"><figcaption>Makes ABHA IDs</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="" loading="lazy" width="56" height="56"><figcaption>ISO 27001 data security</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp" alt="" loading="lazy" width="56" height="56"><figcaption>HIPAA-aligned</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="" loading="lazy" width="56" height="56"><figcaption>HL7, connects lab machines</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2025/07/Certin.webp" alt="" loading="lazy" width="56" height="56"><figcaption>CERT-In security audit</figcaption></figure>
      </div>
    </div>
    <dl>
      <dt>One patient file</dt>
      <dd>OPD visits, ward notes, lab reports and bills join the same patient record. An OPD patient is admitted without typing the details again.</dd>
      <dt>One department at a time</dt>
      <dd>OPD and registration first, then IPD billing, pharmacy and lab. Billing does not stop for even a day.</dd>
      <dt>Help at 2 in the night too</dt>
      <dd>A hospital does not close at night. Call, chat or share your screen, 24 hours, in English, Hindi or Gujarati.</dd>
      <dt>If the internet stops</dt>
      <dd>Connect a mobile hotspot and keep working. A hospital can also run Healthray on its own server inside the hospital.</dd>
    </dl>
  </div>
</section>

<section class="sec soft" id="start">
  <div class="wrap">
    <div class="sec-head"><h2>Start in 3 Steps</h2><p class="hx-lead">Even a 200-bed hospital usually starts in one to three weeks. A clinic starts faster.</p></div>
    <ol class="steps">
      <li><span class="n">1</span><h3>Book a free demo</h3><p>Fill the short form. On a video call we show the modules you need, set up for a place like yours.</p></li>
      <li><span class="n">2</span><h3>We move your data and train your staff, free</h3><p>Old records from your registers, Excel or old software. Our team comes to your hospital and teaches each person their own work.</p></li>
      <li><span class="n">3</span><h3>Start one department at a time</h3><p>OPD and registration first, then IPD billing, pharmacy and lab. Our team stays on call day and night.</p></li>
    </ol>
  </div>
</section>

<section class="sec" id="faq">
  <div class="wrap">
    <div class="sec-head"><h2>Questions Before You Buy</h2></div>
    <div class="faq">
      <?php $hr_faq_i = 0; foreach ( $hr_mod_faqs as $hr_faq ) : ?>
      <details name="hx-faq"<?php echo 0 === $hr_faq_i++ ? ' open' : ''; ?>><summary><?php echo esc_html( $hr_faq['q'] ); ?></summary><p><?php echo esc_html( $hr_faq['a'] ); ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="final">
  <div class="wrap">
    <div class="doc-row" aria-hidden="true">
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Milan-Modi-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Samir-Gami-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Gautam-Beladiya-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Maharshi-Desai-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-kaivan-shah-150x150.webp" alt="" loading="lazy" width="56" height="56"></span>
    </div>
    <h2>See the Modules Working for a Place Like Yours</h2>
    <p>A short video call, set up for your hospital or clinic. No charge. No need to decide on the call.</p>
    <div class="hx-row">
      <a class="btn btn-main" href="#demo">Book a free demo</a>
    </div>
  </div>
</section>

</main>