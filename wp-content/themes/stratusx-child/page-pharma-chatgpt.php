<?php

/**
 * Template Name: Pharmacy Billing Software - ChatGPT Ads
 * Template Post Type: page
 */
defined('ABSPATH') || exit;

// Testimonials: customers' permission received. Set to false to hide the section again.
$hr_show_testimonials = true;

// Media files hosted in the Healthray WordPress uploads directory.
$hr_pharmacy_asset_base = 'https://healthray.com/wp-content/uploads/2026/10/';

// Render one CF7 instance, shared between the hero and popup to avoid duplicate field IDs.
$hr_pharmacy_cf7 = '';
if (shortcode_exists('contact-form-7')) {
  if (function_exists('wpcf7_enqueue_styles')) {
    wpcf7_enqueue_styles();
  }
  if (function_exists('wpcf7_enqueue_scripts')) {
    wpcf7_enqueue_scripts();
  }
  $hr_pharmacy_cf7 = do_shortcode('[contact-form-7 id="63d0105" title="Pharmacy - PPC"]');
}

// ---------- Per-page settings for the shared script (js/pharma-ppc.js) ----------
// channel: sent with every dataLayer event. variants: headline per ad, picked with ?v= on the ad link.
// The first variant is the default and should match the H1 written in the hero below.
$hr_pharma_page = array(
  'channel'        => 'chatgpt',
  'defaultVariant' => 'main',
  'variants'       => array(
    'main'     => array('h' => 'Pharmacy Billing Software for Medical Stores, With a Free Plan'),
    'free'     => array('h' => 'Free Pharmacy Billing Software. Not a Trial, No Time Limit.', 's' => '10 bills a month and 1 user, free forever. No card, no auto payment. Upgrade only when you need more bills or users.'),
    'switch'   => array('h' => 'Looking for a Marg Alternative? We Move Your Data Free', 's' => 'Our team moves your medicines, stock, suppliers and customers. Free plan to start, paid plans from ₹4,999 + GST a year, and 24×7 help.'),
    'new'      => array('h' => 'Opening a Medical Store? Start Billing Free From Day One', 's' => 'Ready medicine list, GST bills with your DL and GST numbers, H1 register and batch-wise stock. Free plan with no time limit.'),
    'gst'      => array('h' => 'GST Billing Software for Pharmacy, With Reports Your CA Needs', 's' => 'Every bill has the HSN code and GST. Export GSTR-1, GSTR-2B and GSTR-3B as Excel (Basic plan and above). Free plan to start.'),
    'expiry'   => array('h' => 'Find Near-Expiry Medicines Before It’s Too Late to Return Them', 's' => 'See near-expiry stock batch-wise with the party name, so you can send it for expiry return on time. The list comes to your WhatsApp by itself.'),
    'rush'     => array('h' => 'Fast Billing, Even When 5 Customers Are Waiting at the Counter', 's' => 'Find any medicine by name, salt or barcode. Sell a full strip or loose tablets, and the price is calculated for you. The bill goes to the customer on WhatsApp.'),
    'purchase' => array('h' => 'Upload the Distributor Bill. Purchase Entry Done, No Typing.', 's' => 'Take a photo of the bill, or upload the PDF or CSV. Batch, expiry, MRP, PTR and free qty are filled in for you. Photo and PDF upload are on paid plans.'),
    'staff'    => array('h' => 'Know What Your Staff Bill, Even When You’re Not at the Counter', 's' => 'Each staff member gets their own login. Hide profit, stop discount changes, and get cash, UPI, card and udhaar totals on WhatsApp every night.'),
    'hi'       => array('h' => 'Medical Store ki Billing, Stock aur Expiry, Ek Hi Software Mein', 'k' => 'Medical store owners ke liye', 's' => 'Counter par fast billing, expiry se pehle WhatsApp par list, aur raat ko din ka poora hisaab. Computer ya mobile, dono par chalta hai.'),
  ),
);

// ---------- Shared pharmacy PPC assets (child theme) ----------
$hr_pharma_css_file = get_stylesheet_directory() . '/css/pharma-ppc.css';
$hr_pharma_js_file  = get_stylesheet_directory() . '/js/pharma-ppc.js';
$hr_pharma_css      = is_readable($hr_pharma_css_file) ? file_get_contents($hr_pharma_css_file) : '';

add_action('wp_enqueue_scripts', function () use ($hr_pharma_js_file, $hr_pharma_page) {
  if (!is_readable($hr_pharma_js_file)) {
    return;
  }
  wp_enqueue_script(
    'hr-pharma-ppc',
    get_stylesheet_directory_uri() . '/js/pharma-ppc.js',
    array(),
    (string) filemtime($hr_pharma_js_file), // new version on every edit, so browsers and caches pick it up
    true
  );
  wp_add_inline_script('hr-pharma-ppc', 'window.hrPharmaPage = ' . wp_json_encode($hr_pharma_page) . ';', 'before');
});

// Indexing is managed through Yoast in the page editor.
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo('charset'); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<?php if (!current_theme_supports('title-tag')) : ?>
<title><?php echo esc_html(wp_get_document_title()); ?></title>
<?php endif; ?>
<?php wp_head(); ?>
<?php if ($hr_pharma_css) : ?>
<style id="hr-pharma-ppc-css"><?php echo $hr_pharma_css; // Theme file, trusted. ?></style>
<?php endif; ?>
<style id="hr-pharma-chatgpt-css">
/* Straight answers: one row per thing chemists compare. Green tick = yes, amber = yes with a plan limit, grey = no. */
.answers {background:var(--sky-2)}
.facts {display:grid;grid-template-columns:1fr 1fr;gap:12px;max-width:980px;margin:0 auto}
.fact {position:relative;margin:0;padding:14px 16px 14px 50px;background:#fff;border:1px solid var(--line);border-radius:10px}
.fact::before {content:"";position:absolute;left:16px;top:16px;width:22px;height:22px;border-radius:50%;background:var(--green) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='white' d='M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z'/%3E%3C/svg%3E") center/16px no-repeat}
.fact.part::before {background-color:#B45309}
.fact.no::before {background:#6B7280 url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='white' d='M18.3 5.7 12 12l6.3 6.3-1.4 1.4L10.6 13.4 4.3 19.7l-1.4-1.4L9.2 12 2.9 5.7l1.4-1.4 6.3 6.3 6.3-6.3z'/%3E%3C/svg%3E") center/14px no-repeat}
.fact dt {font-size:.8rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--navy);margin:0 0 2px}
.fact dd {margin:0;color:var(--muted);line-height:1.5}
.fact dd b {color:var(--navy-dark)}
.answers-cta {display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:10px 18px;margin-top:28px}
.answers-cta p {margin:0;color:var(--muted);font-weight:600}
@media (max-width:720px) {
  .facts {grid-template-columns:1fr;gap:8px}
  .fact {padding:12px 14px 12px 46px}
  .fact::before {left:14px;top:14px}
  .fact dd {font-size:.95rem}
  .answers-cta {margin-top:22px}
  .answers-cta .btn {width:100%}
}
/* ChatGPT page only: line under testimonials, slightly smaller hero headline on phones. */
.testi {border-bottom:1px solid var(--line)}
@media (max-width:720px) {
  .hero .hero-grid h1 {font-size:1.85rem}
}
</style>
</head>
<body <?php body_class('hr-pharmacy-landing'); ?>>
<?php wp_body_open(); ?>

<header class="site-header">
  <div class="wrap">
    <!-- Official Healthray logo from healthray.com. Not a link, so visitors stay on the page. -->
    <div class="logo">
      <img src="https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg" alt="Healthray" width="90" height="50">
      <span class="logo-sub">Pharmacy</span>
    </div>
    <!-- The form is the only goal: no WhatsApp or phone links anywhere on this page. -->
    <div class="header-actions">
      <a class="btn btn-cta js-start" href="#start" data-cta="header_start">Start free</a>
    </div>
  </div>
</header>

<main>
<!-- ============ HERO ============ -->
<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <!-- ChatGPT visitors were comparing options: name the category, the difference, and the plain facts. No hype. -->
      <span class="eyebrow" id="hero-eyebrow">For medical stores and chemists</span>
      <h1 id="hero-title">Pharmacy Billing Software for Medical Stores, With a Free Plan</h1>
      <p class="sub" id="hero-sub">GST billing, batch-wise stock and expiry, purchase entry and GST reports in one place. Works online on your computer, laptop or mobile.</p>
      <p class="free-line">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-1.5 14.5-4-4 1.4-1.4 2.6 2.6 5.6-5.6 1.4 1.4z"/></svg>
        <span>Free forever: 10 bills a month, 1 user. No card needed. No auto payment.</span>
      </p>
      <ul class="trust">
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z"/></svg>Trusted by 2000+ pharmacies across India</li>
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z"/></svg>24×7 support</li>
        <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z"/></svg>Old data moved free</li>
      </ul>
    </div>

    <!-- ============ FORM ============ -->
    <div class="form-card" id="start">
      <h2 id="form-title">Start free today</h2>
      <p class="hint" id="form-hint">Takes 30 seconds. Our team will call you to set it up and move your old data free.</p>
      <div id="hr-pharmacy-form-home">
            <div id="hr-pharmacy-cf7">
              <?php if ($hr_pharmacy_cf7) : ?>
                <?php echo $hr_pharmacy_cf7; // Trusted output generated by the CF7 shortcode. 
                ?>
              <?php else : ?>
                <p role="alert">Please activate Contact Form 7 to display the Pharmacy - PPC form.</p>
              <?php endif; ?>
            </div>
          </div>
    </div>
  </div>
</section>

<!-- ============ STRAIGHT ANSWERS (first after the hero: a ChatGPT visitor arrives with a checklist from their chat) ============ -->
<!-- Facts only, each one also stated elsewhere on this page. The "no" row is on purpose: it is what makes the rest believable. -->
<section class="answers" id="answers" aria-labelledby="answers-title">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow">Comparing pharmacy software?</span>
      <h2 id="answers-title">Straight answers to what you're checking</h2>
      <p class="lead">What chemists compare most, answered for Healthray. Including what it doesn't do.</p>
    </div>
    <dl class="facts">
      <div class="fact yes"><dt>Price</dt><dd><b>Free plan with no time limit.</b> 10 bills a month and 1 user. Paid plans from ₹4,999 + GST a year.</dd></div>
      <div class="fact yes"><dt>Billing</dt><dd><b>Fast, even at rush hour.</b> Search by name, salt or barcode. Sell a full strip or loose tablets.</dd></div>
      <div class="fact yes"><dt>Batch &amp; expiry</dt><dd><b>Batch-wise stock with expiry date.</b> Near-expiry list with party name, sent to your WhatsApp.</dd></div>
      <div class="fact part"><dt>Purchase entry</dt><dd><b>From the distributor bill.</b> Upload a photo, PDF or CSV. Photo and PDF upload on paid plans.</dd></div>
      <div class="fact part"><dt>GST reports</dt><dd><b>GSTR-1, GSTR-2B and GSTR-3B</b> as Excel for your CA. Basic plan and above.</dd></div>
      <div class="fact yes"><dt>H1 register &amp; medicine list</dt><dd><b>Both included,</b> so you can start billing from day one.</dd></div>
      <div class="fact yes"><dt>Staff control</dt><dd><b>Own login for each staff.</b> Hide profit, stop discount changes, see who did what.</dd></div>
      <div class="fact yes"><dt>Old data</dt><dd><b>Moved free</b> from Marg, other software or Excel: medicines, stock, suppliers and customers.</dd></div>
      <div class="fact yes"><dt>Support</dt><dd><b>24×7 on phone,</b> also in the evening and on Sunday.</dd></div>
      <div class="fact part"><dt>More than one store</dt><dd><b>Yes, on the Enterprise plan:</b> all your stores from one place.</dd></div>
      <div class="fact no"><dt>Works without internet</dt><dd><b>No, it works online.</b> On computer, laptop or mobile. If Wi-Fi stops, use your phone's hotspot.</dd></div>
    </dl>
    <div class="answers-cta">
      <a class="btn btn-cta js-start" href="#start" data-cta="answers_start">Start free</a>
      <p>Takes 30 seconds. No card needed.</p>
    </div>
  </div>
</section>

<!-- ============ PRODUCT TOUR VIDEO (after the facts: see it is real) ============ -->
<section class="tour" id="tour" aria-labelledby="tour-title">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow">1-minute video</span>
      <h2 id="tour-title">See the real software in 1 minute</h2>
      <p class="lead">Watch a bill being made and sent on WhatsApp, a purchase being added, and the expiry list. This is the real software.</p>
    </div>
    <div class="tour-frame">
      <!-- No audio track and no controls: plays by itself when scrolled into view, pauses when out of view. -->
      <video id="tour-video" width="1280" height="682" muted playsinline loop disablepictureinpicture preload="metadata"
             poster="https://healthray.com/wp-content/uploads/2026/10/tour-poster.jpg" aria-label="1-minute tour of Healthray pharmacy billing software">
        <source src="https://healthray.com/wp-content/uploads/2026/10/product-tour.mp4" type="video/mp4">
      </video>
    </div>
    <div class="tour-cta">
      <p>Want this for your medical store?</p>
      <a class="btn btn-cta js-start" href="#start" data-cta="tour_start">Start free</a>
    </div>
  </div>
</section>

<!-- ============ PROOF STRIP ============ -->
<div class="proof">
  <div class="wrap">
    <p class="logos-title">2,000+ pharmacies use Healthray</p>
    <!-- Customer logos, served from healthray.com (same files as the free-pharmacy-software page) -->
    <!-- Scrolls right to left in a loop; the second set is a copy so the loop has no gap. Pauses on hover. -->
    <div class="logo-marquee">
      <div class="logo-track">
        <div class="logo-set">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Universal-Pharmacy-Healthray.webp" alt="Universal Pharmacy" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Beladiya-Pharmacy-Healthray.webp" alt="Beladiya Pharmacy" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Satva-Pharmacy-Healthray.webp" alt="Satva Pharmacy" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/shushrusha-Pharmacy-Healthray.webp" alt="Shushrusha Pharmacy" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Raaz-Pharmacy-Healthray.webp" alt="Raaz Pharmacy" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Om-Pharmacy-Healthray.webp" alt="Om Pharmacy" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Naritva-Pharmacy-Healthray.webp" alt="Naritva Pharmacy" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Radient-Pharmacy-Healthray.webp" alt="Radient Pharmacy" width="150" height="150" loading="lazy">
        </div>
        <div class="logo-set" aria-hidden="true">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Universal-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Beladiya-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Satva-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/shushrusha-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Raaz-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Om-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Naritva-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
          <img src="https://healthray.com/wp-content/uploads/2024/02/Radient-Pharmacy-Healthray.webp" alt="" width="150" height="150" loading="lazy">
        </div>
      </div>
    </div>
  </div>
  <div class="wrap proof-grid">
    <div><strong>24×7</strong><span>support</span></div>
    <div><strong>Free</strong><span>old data transfer</span></div>
    <div><strong>144</strong><span>staff controls</span></div>
    <div><strong>₹0</strong><span>to start</span></div>
  </div>
</div>

<!-- ============ FEATURES ============ -->
<section id="features">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">What you get</span><h2>Billing, stock, expiry and GST in one simple software</h2></div>

    <div class="features">
      <!-- 1 Billing -->
      <div class="feature">
        <div class="feature-head">
          <div class="feature-copy">
            <span class="eyebrow">GST billing</span>
            <h3>Fast billing, even when the counter is full</h3>
            <p>Find any medicine by name, salt or barcode. Sell a full strip or loose tablets, and the price is calculated for you.</p>
          </div>
          <div class="feature-points">
            <ul class="ticks">
              <li>Prints on your A4, A5 or thermal printer</li>
              <li>Your logo, DL and GST numbers on every bill</li>
              <li>Bill sent to the customer on WhatsApp</li>
            </ul>
          </div>
        </div>
        <div class="feature-visual">
          <img src="https://healthray.com/wp-content/uploads/2026/10/feature-billing.webp" width="650" height="491" loading="lazy" decoding="async"
               alt="Healthray pharmacy billing software sale screen with four items billed for ₹229, and the customer's WhatsApp showing the bill PDF with a thank-you message">
        </div>
      </div>

      <!-- 2 Expiry & stock alerts -->
      <div class="feature">
        <div class="feature-head">
          <div class="feature-copy">
            <span class="eyebrow">Stock &amp; expiry management</span>
            <h3>Catch near-expiry stock before it becomes a loss</h3>
            <p>See near-expiry stock batch-wise with party name, so you can send it for expiry return on time. The list comes to your WhatsApp by itself.</p>
          </div>
          <div class="feature-points">
            <ul class="ticks">
              <li>Batch-wise stock (inventory) with expiry date</li>
              <li>Alert when an expired batch is billed</li>
              <li>Near-expiry list with party name</li>
              <li>Shortage list daily, expiry list monthly, on WhatsApp</li>
            </ul>
          </div>
        </div>
        <div class="feature-visual">
          <img src="https://healthray.com/wp-content/uploads/2026/10/feature-expiry.webp" width="650" height="475" loading="lazy" decoding="async"
               alt="Medical store stock expiry list in Healthray pharmacy management software, and a phone showing the Stock Expiry and Stock Alert reports received on WhatsApp">
        </div>
      </div>

      <!-- 3 Purchase -->
      <div class="feature">
        <div class="feature-head">
          <div class="feature-copy">
            <span class="eyebrow">Purchase entry</span>
            <h3>Upload the distributor bill. No typing.</h3>
            <p>Take a photo of the bill, or upload the PDF or CSV. Batch, expiry, MRP, PTR and scheme (free qty) are filled in for you.</p>
          </div>
          <div class="feature-points">
            <ul class="ticks">
              <li>PDF, photo or CSV from your distributor</li>
              <li>See if your margin went up&nbsp;▲ or down&nbsp;▼ from last time</li>
              <li>Bill total and GST calculated for you</li>
            </ul>
            <span class="plan-note">Photo/PDF upload on paid plans</span>
          </div>
        </div>
        <div class="feature-visual">
          <img src="https://healthray.com/wp-content/uploads/2026/10/feature-purchase.webp" width="650" height="509" loading="lazy" decoding="async"
               alt="Purchase entry in Healthray pharmacy software: Upload Purchase PDF/Image window showing a photo of a distributor's bill, over the purchase entry filled with the same five items and margin change arrows">
        </div>
      </div>

      <!-- 4 GST -->
      <div class="feature">
        <div class="feature-head">
          <div class="feature-copy">
            <span class="eyebrow">GST &amp; accounts</span>
            <h3>GST data for your CA in one click</h3>
            <p>Every bill has the HSN code and GST. Click GSTR-1, GSTR-2B or GSTR-3B and send your CA an Excel file.</p>
          </div>
          <div class="feature-points">
            <ul class="ticks">
              <li>HSN summary with CGST and SGST</li>
              <li>Tax report by bill or by date</li>
            </ul>
            <span class="plan-note">GST report in Basic plan and above</span>
          </div>
        </div>
        <div class="feature-visual">
          <img src="https://healthray.com/wp-content/uploads/2026/10/feature-gst.webp" width="650" height="524" loading="lazy" decoding="async"
               alt="Healthray GST billing report with the cursor on the GSTR1 export button, and the exported file open in Excel on the HSN summary sheet">
        </div>
      </div>

      <!-- 5 Staff + owner view -->
      <div class="feature">
        <div class="feature-head">
          <div class="feature-copy">
            <span class="eyebrow">Staff control</span>
            <h3>Your staff, your rules</h3>
            <p>Give each staff their own login and choose what they can see or change. Every bill and change is saved, and you get the day's collection on WhatsApp every night.</p>
          </div>
          <div class="feature-points">
            <ul class="ticks">
              <li>Hide profit, stop discount changes, lock bill date</li>
              <li>See who did what, and when</li>
              <li>Cash, UPI, card and udhaar totals on WhatsApp</li>
            </ul>
          </div>
        </div>
        <div class="feature-visual">
          <img src="https://healthray.com/wp-content/uploads/2026/10/feature-staff.webp" width="650" height="480" loading="lazy" decoding="async"
               alt="Staff roles in Healthray pharmacy management software: permissions for Counter Staff with View Profit and Edit Discount turned off, the activity log, and the owner's WhatsApp with the nightly collection report">
        </div>
      </div>
    </div>

    <div class="also-wrap">
      <h3>Also included</h3>
      <div class="also">
        <div>Ready medicine list</div>
        <div>H1 register</div>
        <div>Barcode &amp; shelf labels</div>
        <div>Dose stickers</div>
        <div>Medicine refill reminders</div>
        <div>Loyalty points</div>
        <div>Online orders</div>
        <div>Many stores in one account</div>
      </div>
    </div>
  </div>
</section>

<!-- ============ HOW TO START ============ -->
<!-- Light blue, so it doesn't merge with the white features above into one big empty gap. -->
<section class="how">
  <div class="wrap">
    <div class="sec-head"><h2>Start in 3 easy steps</h2></div>
    <div class="steps">
      <div class="step"><h3>Start free</h3><p>Fill a short form. No card needed.</p></div>
      <div class="step"><h3>We move your old data free</h3><p>Medicines, stock, suppliers and customers from your old software or Excel.</p></div>
      <div class="step"><h3>Start billing</h3><p>Our team sets up your printer and bill format. Then start billing. Help is there 24×7.</p></div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS (placeholders: use only real customers' exact words, with permission) ============ -->
<!-- Shown while $hr_show_testimonials is true (top of this file). Use only real customers' exact words, with permission. -->
<?php if ($hr_show_testimonials) : ?>
<section class="testi">
  <div class="wrap">
    <div class="sec-head"><h2>What chemists say</h2></div>
    <div class="testi-grid">
      <figure class="testi-card" style="margin:0">
        <blockquote>We moved from our old software in one evening. All medicines and party outstanding were shifted, and billing is fast even at 9 pm.</blockquote>
        <figcaption class="who"><span class="avatar" aria-hidden="true">RP</span><span><b>Rakesh Patel</b><small>Shree Ram Medical Store, Ahmedabad</small></span></figcaption>
      </figure>
      <figure class="testi-card" style="margin:0">
        <blockquote>We opened our medical store this year and started on the free plan. GST bills and the medicine list were ready from the first day.</blockquote>
        <figcaption class="who"><span class="avatar" aria-hidden="true">NS</span><span><b>Neha Sharma</b><small>Sharma Medicos, Jaipur</small></span></figcaption>
      </figure>
      <figure class="testi-card" style="margin:0">
        <blockquote>I run two stores and can't be at both counters. The 9:30 pm WhatsApp report tells me cash, UPI and udhaar for the day.</blockquote>
        <figcaption class="who"><span class="avatar" aria-hidden="true">IS</span><span><b>Imran Shaikh</b><small>Care Plus Pharmacy, Pune</small></span></figcaption>
      </figure>
    </div>
  </div>
</section>
<?php endif; ?>


<!-- ============ PRICING ============ -->
<section id="pricing">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Pricing</span><h2>Healthray prices, with GST shown</h2><p class="lead">Start free. Upgrade only when you need more bills or users.</p></div>
    <!-- Monthly price is the yearly price ÷ 12 (as shown in the app's own pricing). The yearly amount and GST stay right under it. -->
    <div class="pricing-grid">
      <div class="plan">
        <h3>Free</h3>
        <div class="price">₹0 <small>/month</small></div>
        <div class="yearly">Free forever. No card needed.</div>
        <ul><li>10 bills a month</li><li>1 user</li><li>Sales &amp; purchase</li><li>Basic reports</li></ul>
        <a class="btn btn-cta js-start" href="#start" data-cta="pricing_free">Start free</a>
      </div>
      <div class="plan">
        <h3>Basic</h3>
        <div class="price">₹417 <small>/month</small></div>
        <div class="yearly">Paid yearly: ₹4,999 + 18% GST<br>(₹5,899 a year)</div>
        <ul><li>Unlimited bills</li><li>3 users</li><li>GST report &amp; vouchers</li><li>Label print &amp; CSV import</li><li>Staff roles</li></ul>
        <a class="btn btn-outline js-start" href="#start" data-cta="pricing_basic">Start free &amp; upgrade</a>
      </div>
      <div class="plan popular">
        <span class="badge">Popular</span>
        <h3>Advance</h3>
        <div class="price">₹833 <small>/month</small></div>
        <div class="yearly">Paid yearly: ₹9,999 + 18% GST<br>(₹11,799 a year)</div>
        <ul><li>Everything in Basic</li><li>10 users</li><li>Purchase orders &amp; reorder</li><li>Discount approval &amp; bill lock</li><li>Activity report</li><li>Expense tracking</li></ul>
        <a class="btn btn-cta js-start" href="#start" data-cta="pricing_advance">Start free &amp; upgrade</a>
      </div>
    </div>

    <!-- Enterprise is not a price card: big stores and chains book a demo instead. -->
    <div class="ent">
      <div>
        <h3>Have more than one medical store?</h3>
        <p>Ask about the Enterprise plan: unlimited users and bills, manage all your stores from one place, Tally export and faster support.</p>
      </div>
      <a class="btn btn-outline js-demo" href="#start" data-cta="pricing_enterprise">Book a free demo</a>
    </div>

    <div class="pricing-foot">
      <p>We move your old data free, and help is there 24×7. 3-year and 5-year plans also available.</p>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section style="background:var(--sky-2)" id="faq">
  <div class="wrap">
    <div class="sec-head"><h2>Questions chemists ask us</h2></div>
    <div class="faq">
      <!-- Order follows someone comparing options: is free real, the honest limit, why this one, then switching. -->
      <details name="hr-pharmacy-faq" open><summary>Is the free plan really free?</summary><p>Yes. 10 bills a month and 1 user, with no time limit. It is not a trial. No card needed, no auto payment. Upgrade only if you want to.</p></details>
      <details name="hr-pharmacy-faq"><summary>Does it work without internet?</summary><p>No. Healthray Pharmacy works online, so you need internet. It works on any computer, laptop or mobile, so if Wi-Fi stops you can use your phone's hotspot. Your data stays safe online even if a computer breaks, and our team is available 24×7.</p></details>
      <details name="hr-pharmacy-faq"><summary>Why choose Healthray over other pharmacy software?</summary><p>It depends on what matters to you. Healthray has a free plan with no time limit, the near-expiry list and the day's collection on WhatsApp, purchase entry from a bill photo (paid plans), free data move and 24×7 support. It works online only, so if you need software that runs without internet, it is not the right fit.</p></details>
      <details name="hr-pharmacy-faq"><summary>Can you move my data from Marg or my current software?</summary><p>Yes, free. Our team moves your medicines, stock, suppliers and customers from Marg, any other software or Excel, so everything is ready from day one.</p></details>
      <details name="hr-pharmacy-faq"><summary>What happens after I fill the form?</summary><p>Someone from our team calls you to set up your account, printer and bill format, and to move your old data free. There is nothing to pay to start.</p></details>
      <details name="hr-pharmacy-faq"><summary>Can I manage more than one store?</summary><p>Yes, with the Enterprise plan: unlimited users and bills, all your stores from one place, Tally export and faster support. Use the "Book a free demo" button in the pricing section.</p></details>
      <details name="hr-pharmacy-faq"><summary>Can I use it on my mobile?</summary><p>Yes. It works on your counter computer, laptop or mobile, so you can check sales and stock even when you are not at the store.</p></details>
      <details name="hr-pharmacy-faq"><summary>Is my data safe?</summary><p>Your data is saved online, not on one computer, so a broken computer or theft can't delete it. Each staff member sees only what you allow, and every change is saved.</p></details>
      <details name="hr-pharmacy-faq"><summary>Will it print on my printer?</summary><p>Yes. It prints A4, A5 and 80 mm thermal bills, with GST invoice layouts, your logo, and your DL and GST numbers.</p></details>
      <details name="hr-pharmacy-faq"><summary>How long will my staff take to learn it?</summary><p>It's easy: type a few letters, pick the medicine, press Enter. Search by name, salt or barcode. Our team is one call away while your staff learn.</p></details>
      <details name="hr-pharmacy-faq"><summary>What support do I get?</summary><p>24×7 help on phone, even in the evening and on Sunday.</p></details>
    </div>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="final">
  <div class="wrap">
    <h2>Try it free in your medical store</h2>
    <p style="margin:0">10 free bills a month, forever. No card. We'll move your old data free.</p>
    <div class="btns">
      <a class="btn btn-cta js-start" href="#start" data-cta="final_start">Start free</a>
    </div>
  </div>
</section>
</main>

<footer>
  <div class="wrap">
    © <span id="yr">2026</span> Healthray Technologies Pvt. Ltd.
    <nav aria-label="Legal">
      <a href="https://healthray.com/privacy-policy/" target="_blank" rel="noopener">Privacy Policy</a>
      <a href="https://healthray.com/terms-condition/" target="_blank" rel="noopener">Terms</a>
      <a href="https://healthray.com/contact/" target="_blank" rel="noopener">Contact</a>
    </nav>
  </div>
</footer>

<div class="sticky" id="sticky" aria-hidden="true">
  <a class="btn btn-cta js-start" href="#start" data-cta="sticky_start" tabindex="-1">Start free</a>
</div>


<dialog id="hr-pharmacy-modal" aria-labelledby="hr-pharmacy-modal-title" aria-describedby="hr-pharmacy-modal-hint">
    <button type="button" class="hr-pharmacy-modal-close" aria-label="Close form">×</button>
    <h2 id="hr-pharmacy-modal-title">Start free today</h2>
    <p class="hint" id="hr-pharmacy-modal-hint">Takes 30 seconds. Our team will call you to set it up and move your old data free.</p>
    <div id="hr-pharmacy-modal-slot"></div>
  </dialog>
<?php wp_footer(); ?>
</body>
</html>