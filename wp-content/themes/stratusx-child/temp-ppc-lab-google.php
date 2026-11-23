<?php

/**
 * Template Name: PPC - Lab Google
 *
 * Page 82606, /ppc/lab-google/. CSS/JS: css/ppc-lab.css, js/ppc-lab.js (loaded by functions.php, key "lab").
 * Form: CF7 "PPC - Lab", 2 steps; Odoo keys bed_size, name, mobile, email, city, ref.
 * Heading per ad group: ?k=diag|switch|lis.
 */

defined('ABSPATH') || exit;

$hr_lab_form   = '[contact-form-7 id="4f8a16e" title="PPC - Lab"]';
$hr_lab_up    = 'https://healthray.com/wp-content/uploads';
$hr_lab_media  = 'https://healthray.com/wp-content/uploads/2026/11';

// Label + heading per ad group (same list in js/ppc-lab.js)
$hr_lab_heads = array(
  'diag'   => array('Diagnostic centre software', 'Bill, report and WhatsApp. One easy software for your diagnostic centre.'),
  'switch' => array('Pathology lab software', 'Changing your lab software? Bill, report and WhatsApp in one easy place.'),
  'lis'    => array('Lab information system (LIS)', 'Bill, report and WhatsApp. One easy software for your whole lab.'),
);
$hr_lab_k    = isset($_GET['k']) ? sanitize_key(wp_unslash($_GET['k'])) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$hr_lab_head = isset($hr_lab_heads[$hr_lab_k]) ? $hr_lab_heads[$hr_lab_k] : array('Pathology lab software', 'Easy pathology lab software for your bill, report and WhatsApp.');

?>
<script>
  window.HR_LAB = <?php echo wp_json_encode(array('uploads' => $hr_lab_up)); ?>;
</script>
<header class="top">
  <div class="wrap tb">
    <img src="<?php echo esc_url($hr_lab_up . '/2024/02/Healthray-Logo.svg'); ?>" alt="Healthray" width="150" height="40">
    <div class="tb-r"><a class="bt sm" href="#demo" data-lab-pop>Book a free demo</a></div>
  </div>
</header>

<main>

  <section class="hero" id="demo">
    <div class="wrap">
      <div class="h-copy">
        <p class="eb" id="eyebrow"><?php echo esc_html($hr_lab_head[0]); ?></p>
        <h1 id="headline"><?php echo esc_html($hr_lab_head[1]); ?></h1>
        <p class="sub">Online laboratory software for pathology labs and diagnostic centres in India. It works with your lab machines, your letterhead and your rate list.</p>
        <ul class="ticks">
          <li>We move your old data for you</li>
          <li>Help on call, day and night, in Hindi, Gujarati and English</li>
          <li>Free demo. Free trial available.</li>
        </ul>
        <p class="mini"><b>★ 4.8</b><i>·</i><b>500+</b> labs<i>·</i>ABDM<i>·</i>ISO 27001</p>
      </div>
      <div class="fm" id="hr-lab-form">
        <h2>Book a free demo</h2>
        <p class="fs">30 minutes. We show it for your kind of lab.</p>
        <div class="prog">STEP <span class="stepno">1</span> OF 2</div>
        <div class="progbar" aria-hidden="true"><i></i></div>
        <?php echo do_shortcode($hr_lab_form); ?>
        <p class="safe">No unwanted calls. We call only to fix your demo time.</p>
      </div>
      <div class="h-proof">
        <p class="count"><b>500+ labs</b> in India use Healthray. <b>4M+</b> reports printed.</p>
        <div class="sealrow">
          <div class="seals">
            <span class="seal"><img src="<?php echo esc_url($hr_lab_up . '/2026/07/NABH-ehr-certified-150x150.webp'); ?>" alt="NABH"></span>
            <span class="seal"><img src="<?php echo esc_url($hr_lab_up . '/2026/03/abdm-healthray-150x150.webp'); ?>" alt="ABDM"></span>
            <span class="seal"><img src="<?php echo esc_url($hr_lab_up . '/2025/07/NHA.webp'); ?>" alt="NHA"></span>
            <span class="seal"><img src="<?php echo esc_url($hr_lab_up . '/2026/03/iso-27001-healthray-150x150.webp'); ?>" alt="ISO 27001"></span>
          </div>
          <p class="sealtext"><b>NABH certified, ABDM compliant</b>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="stars" aria-label="Ratings">
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>SoftwareSuggest</span>
          <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
        </div>
      </div>
    </div>
  </section>

  <section class="labs" aria-label="Labs that use Healthray">
    <div class="wrap labs-h">
      <h2><span class="hl">500+ labs</span> use Healthray.</h2>
      <p>Pathology labs, diagnostic centres and hospital labs</p>
    </div>
    <div class="rail">
      <div class="track" id="track"></div>
    </div>
  </section>

  <section class="see">
    <div class="wrap">
      <div class="head2">
        <h2 class="big">See it work in <span class="hl">1 minute</span>.</h2>
        <p>One patient, from the bill to the WhatsApp report.</p>
      </div>
      <div class="player" id="player" data-src="<?php echo esc_url($hr_lab_media . '/lab-tour-web.mp4'); ?>">
        <button type="button" class="pl-btn" id="playbtn" aria-label="Play the 1-minute video"><img src="<?php echo esc_url($hr_lab_media . '/lab-tour-poster.webp'); ?>" width="1920" height="1080" alt="A lab report on the lab's own letterhead, with signature and QR code, ready to share on WhatsApp" loading="lazy"><span class="play"></span><span class="dur">1:00<small>Watch the video</small></span></button>
        <button type="button" class="pl-pp" id="playpp" aria-label="Play"><svg class="i-play" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M8 5.2v13.6L19.2 12z" />
          </svg><svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M6.8 5h3.7v14H6.8zM13.5 5h3.7v14h-3.7z" />
          </svg></button>
        <button type="button" class="pl-fs" id="playfs" aria-label="Full screen"><svg class="i-max" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5" />
          </svg><svg class="i-min" viewBox="0 0 24 24" aria-hidden="true">
            <path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5" />
          </svg></button>
      </div>
    </div>
  </section>

  <section class="chgs">
    <div class="wrap">
      <div class="sh-row">
        <div>
          <h2 class="big">What <span class="hl">changes</span> for you.</h2>
          <p class="sh-sub">Four things. Each one is shown in your free demo.</p>
        </div>
        <div class="arrows"><button type="button" class="prev" aria-label="Previous"></button><button type="button" class="next" aria-label="Next"></button></div>
      </div>
      <div class="gal" id="gal" tabindex="0" aria-label="Four changes, slide to see all">
        <div class="pn k-bill">
          <h3>One bill for blood tests and scans.</h3>
          <p>CBC, RFT and an X-ray on the same bill.</p>
          <picture class="shot">
            <source media="(max-width:820px)" srcset="<?php echo esc_url($hr_lab_media . '/card-bill-phone.webp'); ?>" width="258" height="274"><img src="<?php echo esc_url($hr_lab_media . '/card-bill.webp'); ?>" width="448" height="330" alt="A bill with two Pathology tests, CBC and RFT. X-Ray is being picked from the list to add it to the same bill" loading="lazy">
          </picture>
        </div>
        <div class="pn dark k-due">
          <h3>No payment, no report.</h3>
          <p>If payment is pending, the report cannot be printed or sent.</p>
          <picture class="shot">
            <source media="(max-width:820px)" srcset="<?php echo esc_url($hr_lab_media . '/card-due-phone.webp'); ?>" width="258" height="274"><img src="<?php echo esc_url($hr_lab_media . '/card-due.webp'); ?>" width="448" height="330" alt="Three reports are ready. Two patients have no due and a Print button. One patient has 450 rupees due and a lock in place of Print" loading="lazy">
          </picture>
        </div>
        <div class="pn k-day">
          <h3>Today's collection and pending payments on one screen.</h3>
          <p>Cash, UPI, card and pending amount.</p>
          <picture class="shot">
            <source media="(max-width:820px)" srcset="<?php echo esc_url($hr_lab_media . '/card-day-phone.webp'); ?>" width="258" height="274"><img src="<?php echo esc_url($hr_lab_media . '/card-day.webp'); ?>" width="448" height="330" alt="Today: collected 16,685 rupees. Cash 9,500, UPI 5,400, Card 1,785. Pending 2,450 rupees in 3 cases" loading="lazy">
          </picture>
        </div>
        <div class="pn k-comm">
          <h3>Doctor commission, set one time.</h3>
          <p>For each test, in % or ₹.</p>
          <picture class="shot">
            <source media="(max-width:820px)" srcset="<?php echo esc_url($hr_lab_media . '/card-comm-phone.webp'); ?>" width="258" height="274"><img src="<?php echo esc_url($hr_lab_media . '/card-comm.webp'); ?>" width="448" height="330" alt="CBC, price 500 rupees. Doctor commission 30, with a list open to choose Percent or Rupees. The doctor gets 150 rupees on each CBC" loading="lazy">
          </picture>
        </div>
      </div>
      <div class="gdots" id="gdots"><button type="button" class="on" aria-label="Show change 1" aria-pressed="true"></button><button type="button" aria-label="Show change 2" aria-pressed="false"></button><button type="button" aria-label="Show change 3" aria-pressed="false"></button><button type="button" aria-label="Show change 4" aria-pressed="false"></button></div>
      <div class="gnote">
        <p>See all four with your own tests and rates in a free 30-minute demo.</p>
        <a class="bt" href="#demo" data-lab-pop>Book a free demo</a>
      </div>
    </div>
  </section>

  <section class="report">
    <div class="wrap rp">
      <div>
        <h2 class="big">The <span class="hl">report</span> your patient gets.</h2>
        <p class="sh-sub">Tap a line to see it.</p>
        <div class="tabs" id="rtabs">
          <button type="button" class="tab on" data-k="a" aria-pressed="true"><b>Your report format, set one time.</b><span>Letterhead on top, signature below, your letter size. You see the report as you change it.</span></button>
          <button type="button" class="tab" data-k="b" aria-pressed="false"><b>High and low results in colour.</b><span>Very high or very low results get their own colour too.</span></button>
          <button type="button" class="tab" data-k="c" aria-pressed="false"><b>On WhatsApp, on your letterhead.</b><span>To the patient and the doctor, with your signature and a QR code.</span></button>
        </div>
      </div>
      <div class="rp-pnl" id="rpnl" data-on="a">
        <div class="scene s-a full">
          <picture>
            <source media="(max-width:600px)" srcset="<?php echo esc_url($hr_lab_media . '/report-format-phone.webp'); ?>" width="343" height="330">
            <source media="(min-width:821px) and (max-width:1040px)" srcset="<?php echo esc_url($hr_lab_media . '/report-format-phone.webp'); ?>" width="343" height="330"><img src="<?php echo esc_url($hr_lab_media . '/report-format.webp'); ?>" width="680" height="440" alt="Report format with switches for Letterhead, Signature, QR code and Letter size. Beside it a lab report on a lab's own letterhead; the letterhead has a blue outline" loading="lazy">
          </picture>
        </div>
        <div class="scene s-b full">
          <picture>
            <source media="(max-width:600px)" srcset="<?php echo esc_url($hr_lab_media . '/report-colour-phone.webp'); ?>" width="343" height="330">
            <source media="(min-width:821px) and (max-width:1040px)" srcset="<?php echo esc_url($hr_lab_media . '/report-colour-phone.webp'); ?>" width="343" height="330"><img src="<?php echo esc_url($hr_lab_media . '/report-colour.webp'); ?>" width="680" height="440" alt="Result colours: High value green, Low value yellow, Critical value red. Beside it a lab report with one low, one high and one critical result in those colours" loading="lazy">
          </picture>
        </div>
        <div class="scene s-c full">
          <picture>
            <source media="(max-width:600px)" srcset="<?php echo esc_url($hr_lab_media . '/report-share-phone.webp'); ?>" width="343" height="330">
            <source media="(min-width:821px) and (max-width:1040px)" srcset="<?php echo esc_url($hr_lab_media . '/report-share-phone.webp'); ?>" width="343" height="330"><img src="<?php echo esc_url($hr_lab_media . '/report-share.webp'); ?>" width="680" height="440" alt="Share report with WhatsApp, Letterhead, Signature and QR code switched on, and Share to Patient and Doctor. Beside it a phone with the report arriving as a PDF on the lab's letterhead" loading="lazy">
          </picture>
        </div>
      </div>
    </div>
  </section>

  <section class="price">
    <div class="wrap">
      <h2 class="big"><span class="hl">Start free.</span> Pay only for what your lab uses.</h2>
      <p class="sh-sub">A free demo and a free trial first. Then a price made for your lab.</p>
      <div class="p-grid">
        <div class="zero" aria-label="Zero rupees to start"><span class="ru">&#8377;</span>0<small>to start</small></div>
        <ul class="p-list">
          <li><b>Free demo.</b><span>30 minutes. We show it for your kind of lab.</span></li>
          <li><b>Free trial.</b><span>Use it in your own lab before you pay.</span></li>
          <li><b>Free plan for small pathology labs.</b><span>Ask us in the demo if your lab fits.</span></li>
        </ul>
      </div>
      <div class="after">
        <p><b>After that, your price depends on three things:</b> how big your lab is, how many branches you have, and what you do (blood tests, USG, X-ray, CT, MRI, ECG). One small lab pays much less than a lab with many branches. After the demo we send your price.<span class="off">10% off premium plans right now.</span></p>
        <a class="bt" href="#demo" data-lab-pop>Get my price</a>
      </div>
    </div>
  </section>

  <section class="move">
    <div class="wrap">
      <div class="head2">
        <h2 class="big">Using <span class="hl">another lab software</span> now?</h2>
        <p>Our team moves everything for you.</p>
      </div>
      <div class="nums">
        <div><b><span class="ru">&#8377;</span>0</b><strong>to move your old data</strong><span>From registers or from your old software. In every plan.</span></div>
        <div><b>10 min</b><strong>to make your first bill and report</strong><span>The test list is ready in the software.</span></div>
        <div><b>2 weeks</b><strong>to set up everything, in most labs</strong><span>Your tests, normal ranges and machines. Many labs finish in a few days.</span></div>
        <div><b>24/7</b><strong>help on call</strong><span>In Hindi, Gujarati and English.</span></div>
      </div>
      <p class="train">We train your staff: the counter person, the technician and the doctor. Each one learns their own work.</p>
    </div>
  </section>

  <section class="say">
    <div class="wrap">
      <h2 class="big">What <span class="hl">lab owners say</span> about Healthray.</h2>
      <div class="say-sub">
        <p>Pathology labs and diagnostic centres that use Healthray. You can read all these reviews on healthray.com.</p>
        <div class="rv-arrows"><button type="button" class="prev" aria-label="Previous reviews"></button><button type="button" class="next" aria-label="Next reviews"></button></div>
      </div>
      <div class="rv-row" id="rvrow" tabindex="0" aria-label="Six reviews, slide to see all">
        <blockquote class="rv">
          <div class="rv-who"><span class="lg"><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Om-Pathology-Lab-Healthray.webp'); ?>" alt="Om Pathology Lab logo"></span>
            <div><b>Om Pathology Lab</b><span>Surat, Gujarat</span></div>
          </div>
          <p>Since we started using Healthray's LIMS, our staff's daily work has become much simpler, and its features have <mark>helped us bring down our overall lab expenses</mark>.</p>
        </blockquote>
        <blockquote class="rv c2">
          <div class="rv-who"><span class="lg"><img src="<?php echo esc_url($hr_lab_up . '/2024/01/Pratham-lab-1.webp'); ?>" alt="Pratham Laboratory logo"></span>
            <div><b>Pratham Laboratory</b><span>Surat, Gujarat</span></div>
          </div>
          <p>I'm very happy with Healthray's LIMS. The system is <mark>easy to use, runs reliably</mark>, and has made our laboratory workflow much smoother while helping us maintain high quality standards.</p>
        </blockquote>
        <blockquote class="rv c3">
          <div class="rv-who"><span class="lg"><img src="<?php echo esc_url($hr_lab_up . '/2024/01/Diabcare-lab-1.webp'); ?>" alt="Diabcare Laboratory logo"></span>
            <div><b>Diabcare Laboratory</b><span>Mumbai, Maharashtra</span></div>
          </div>
          <p>Healthray's LIMS has comprehensive features that <mark>improve collaboration among our internal staff</mark> and contribute to real patient satisfaction.</p>
        </blockquote>
        <blockquote class="rv">
          <div class="rv-who"><span class="lg"><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Bansal-Diagnostics-Healthray.webp'); ?>" alt="Bansal Diagnostics logo"></span>
            <div><b>Bansal Diagnostics</b><span>Kurukshetra, Haryana</span></div>
          </div>
          <p>This tailor-made solution makes our tasks easier and more simpler. Moreover, it has the AI properties that <mark>saves a lot of time</mark>.</p>
        </blockquote>
        <blockquote class="rv c2">
          <div class="rv-who"><span class="lg"><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Omed-Diagnostics-Healthray.webp'); ?>" alt="Omed Diagnostics logo"></span>
            <div><b>Omed Diagnostics</b><span>Cachar, Assam</span></div>
          </div>
          <p>This is an <mark>all-in-one lab solution</mark> and embracing Healthray's LIMS is one of our leading decisions. It transforms our whole laboratory system.</p>
        </blockquote>
        <blockquote class="rv c3">
          <div class="rv-who"><span class="lg"><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Chakraa-Medical-Center-Healthray.webp'); ?>" alt="Chakraa Medical Center logo"></span>
            <div><b>Chakraa Medical Center</b><span>Tiruppur, Tamil Nadu</span></div>
          </div>
          <p>We are thrilled to use Healthray's LIMS solution to <mark>automate our laboratory operations and minimize errors</mark>. Recommend this software to all!</p>
        </blockquote>
      </div>
    </div>
  </section>

  <section class="faq">
    <div class="wrap">
      <div>
        <h2 class="big"><span class="hl">Questions</span> lab owners ask.</h2>
        <p class="sh-sub faq-note">Did not find your question? <a href="#book">Ask us on WhatsApp</a></p>
      </div>
      <div>
        <details name="lab-faq" open>
          <summary>What is the price of this lab software?</summary>
          <p>The price depends on three things: how big your lab is, how many branches you have, and what you do. One small lab pays much less than a lab with many branches. You can start with a free trial. After the demo we send your price.</p>
        </details>
        <details name="lab-faq">
          <summary>How many days does it take to start?</summary>
          <p>You can make bills and reports in about 10 minutes. The test list is ready. To set up everything, most labs take a few days to two weeks.</p>
        </details>
        <details name="lab-faq">
          <summary>What happens to my old data?</summary>
          <p>Our team moves it for you, from registers or from your old lab software. There is no extra charge in any plan.</p>
        </details>
        <details name="lab-faq">
          <summary>Will it work with my machines?</summary>
          <p>Healthray connects with your lab machines. Results come into the report by themselves. No typing. Tell us your machine names in the demo and we will check each one.</p>
        </details>
        <details name="lab-faq">
          <summary>Is it online? Do I need a special computer?</summary>
          <p>Yes, it is online (cloud based) lab software. It runs on the internet, on any computer. You do not need a server or a special computer. Your data is saved by itself.</p>
        </details>
        <details name="lab-faq">
          <summary>Can I make bills and reports both?</summary>
          <p>Yes. It is pathology billing software and pathology report software in one place. One bill, one report, in your own report format.</p>
        </details>
        <details name="lab-faq">
          <summary>Is there a free version?</summary>
          <p>Yes. You can start with a free demo and a free trial in your own lab. Small pathology labs can also use the free plan.</p>
        </details>
      </div>
    </div>
  </section>

  <section class="last" id="book">
    <div class="wrap">
      <div class="cta-logos" aria-hidden="true"><span><img src="<?php echo esc_url($hr_lab_up . '/2024/01/Pratham-lab-1.webp'); ?>" alt=""></span><span><img src="<?php echo esc_url($hr_lab_up . '/2024/01/Diabcare-lab-1.webp'); ?>" alt=""></span><span><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Om-Pathology-Lab-Healthray.webp'); ?>" alt=""></span><span><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Bansal-Diagnostics-Healthray.webp'); ?>" alt=""></span><span><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Omed-Diagnostics-Healthray.webp'); ?>" alt=""></span><span><img src="<?php echo esc_url($hr_lab_up . '/2024/06/Chakraa-Medical-Center-Healthray.webp'); ?>" alt=""></span></div>
      <div>
        <h2 class="big">Your reports go on WhatsApp. <span class="hl">Your demo can start there too.</span></h2>
        <p class="sub">The message is already written. Tap the button, press send, and we fix a time for your free 30-minute demo.</p>
      </div>
      <div class="wa-box">
        <p class="wa-msg">Hi, I run a lab. I want a free demo of Healthray Lab.<small>Your message, ready to send</small></p>
        <a class="wa-bt" href="<?php echo esc_url(hr_ppc_wa_url('Hi, I run a lab. I want a free demo of Healthray Lab.')); ?>" target="_blank" rel="noopener"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path d="M21 11.5a8.5 8.5 0 0 1-12.6 7.4L3 20.5l1.7-5A8.5 8.5 0 1 1 21 11.5z" />
          </svg>Send it on WhatsApp</a>
        <p class="wa-alt"><a href="#demo" data-lab-pop>Or fill the short form</a></p>
      </div>
    </div>
  </section>

</main>

<footer>
  <div class="wrap"><span>&copy; <?php echo esc_html(gmdate('Y')); ?> Healthray Technologies Pvt. Ltd. &middot; Surat, Gujarat &middot; Since 2019</span><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" rel="nofollow">Privacy policy</a></div>
</footer>

<div class="bar off"><a class="bt" href="#demo">Book free demo</a></div>

<div class="lab-pop" id="lab-pop" role="dialog" aria-modal="true" aria-label="Book a free demo" hidden>
  <div class="lab-pop-in"><button type="button" class="lab-pop-x" aria-label="Close">&times;</button></div>
</div>