<?php
/**
 * Template Name: Pharmacy Management Software - Site Header
 * Template Post Type: page
 */
defined('ABSPATH') || exit;

/* =====================================================================
 * EDIT PAGE CONTENT HERE
 * The FAQ list feeds both the visible FAQ and the FAQ schema, so they always match.
 * Plain text only (no HTML). The first FAQ is shown open.
 * ===================================================================== */

$hr_faq = array(
  array(
    'q' => 'What is pharmacy management software?',
    'a' => 'It is software that runs a medical store\'s daily work in one place, from prescription to bill: GST billing, batch-wise stock with FEFO expiry alerts, purchase entry from distributor bills, GST reports for your CA, and staff logins. Healthray does all of this online, on a computer, laptop or mobile.',
  ),
  array(
    'q' => 'Which is the best pharmacy management software in India?',
    'a' => 'There is no single best software for every medical store. Check 6 things: fast counter billing, batch-wise stock with expiry alerts, easy purchase entry, GST reports for your CA, staff control, and free data transfer with good support. Healthray does all 6, and you can try it free in your own store before you pay.',
  ),
  array(
    'q' => 'What if the internet goes down?',
    'a' => 'Healthray Pharmacy works online, so you need internet. It works on any computer, laptop or mobile, so if Wi-Fi stops you can use your phone\'s hotspot. Your data stays safe online even if a computer breaks, and our team is available 24×7.',
  ),
  array(
    'q' => 'How much does pharmacy software cost in India?',
    'a' => 'Healthray has a free plan with 10 bills a month and 1 user. The Basic plan is ₹4,999 a year + 18% GST (₹5,899), and the Advance plan is ₹9,999 a year + 18% GST (₹11,799). We move your old data free.',
  ),
  array(
    'q' => 'Is the free plan really free?',
    'a' => 'Yes. 10 bills a month and 1 user, with no time limit. No card needed, no auto payment. Upgrade only if you want to.',
  ),
  array(
    'q' => 'Can you move my data from Marg or my current software?',
    'a' => 'Yes, free. Our team moves your medicines, stock, suppliers and customers from Marg, any other software or Excel, so everything is ready from day one.',
  ),
  array(
    'q' => 'Is it only for billing?',
    'a' => 'No. Along with billing, it handles batch-wise stock and expiry, purchase entry, GST reports and staff control, all in one place. Start with billing and use the rest when you need it.',
  ),
  array(
    'q' => 'Can I manage more than one medical store?',
    'a' => 'Yes. The Enterprise plan lets you manage all your stores from one place, with unlimited users and bills. Book a free demo to get the price.',
  ),
  array(
    'q' => 'Is my data safe?',
    'a' => 'Your data is saved online, not on one computer, so a broken computer or theft can\'t delete it. Each staff member sees only what you allow, and every change is saved.',
  ),
);

// "How to choose the best pharmacy management software" (6 points).
$hr_choose = array(
  array('title' => 'Fast billing at the counter.', 'text' => 'Turn a prescription into a bill in seconds: find medicines by name, salt or barcode, and sell loose tablets without doing the maths.'),
  array('title' => 'Batch-wise stock with FEFO.', 'text' => 'First expiry, first out: sell the batch that expires first, and see near-expiry stock early enough to return it to the party.'),
  array('title' => 'Easy purchase entry.', 'text' => 'Add distributor bills from a PDF, photo or CSV instead of typing every line.'),
  array('title' => 'GST reports for your CA.', 'text' => 'GSTR-1, GSTR-2B and GSTR-3B data ready to send as Excel.'),
  array('title' => 'Staff control.', 'text' => 'A separate login for each staff member, and limits on what they can see or change.'),
  array('title' => 'Free data transfer and support.', 'text' => 'Your old data moved free, and help on call when you get stuck.'),
);

// "What chemists say". Real quotes only, with the customer's permission. Logos are in /wp-content/uploads/2024/02/.
$hr_logo_base    = 'https://healthray.com/wp-content/uploads/2024/02/';
$hr_testimonials = array(
  array(
    'name'  => 'Beladiya Pharmacy',
    'logo'  => 'Beladiya-Pharmacy-Healthray.webp',
    'quote' => 'I am very satisfied with Healthray’s software. It is a user-friendly and secure system that simplifies pharmacy operations and enhances quality control.',
  ),
  array(
    'name'  => 'Naritva Medical Store',
    'logo'  => 'Naritva-Pharmacy-Healthray.webp',
    'quote' => 'The reporting features in Healthray’s Pharmacy Management System have been invaluable for tracking sales and inventory trends.',
  ),
  array(
    'name'  => 'Om Medical',
    'logo'  => 'Om-Pharmacy-Healthray.webp',
    'quote' => 'The automated reminders in Healthray’s Pharmacy Management System help ensure that patients never miss a refill.',
  ),
);

// Customer logos in the scrolling strip (file name => store name).
$hr_logos = array(
  'Universal-Pharmacy-Healthray.webp'  => 'Universal Pharmacy',
  'Beladiya-Pharmacy-Healthray.webp'   => 'Beladiya Pharmacy',
  'Satva-Pharmacy-Healthray.webp'      => 'Satva Pharmacy',
  'shushrusha-Pharmacy-Healthray.webp' => 'Shushrusha Pharmacy',
  'Raaz-Pharmacy-Healthray.webp'       => 'Raaz Pharmacy',
  'Om-Pharmacy-Healthray.webp'         => 'Om Pharmacy',
  'Naritva-Pharmacy-Healthray.webp'    => 'Naritva Pharmacy',
  'Radient-Pharmacy-Healthray.webp'    => 'Radient Pharmacy',
);

// Plans shown in the SoftwareApplication schema (yearly price including 18% GST). Keep in line with the pricing section below.
$hr_offers = array(
  array('name' => 'Free', 'price' => '0'),
  array('name' => 'Basic (yearly, incl. 18% GST)', 'price' => '5899'),
  array('name' => 'Advance (yearly, incl. 18% GST)', 'price' => '11799'),
);

// Media for the tour video and feature screenshots.
$hr_media = 'https://healthray.com/wp-content/uploads/2026/10/';

/* ===================================================================== */

// Contact Form 7: one instance, shared between the hero and the popup.
$hr_pharmacy_cf7 = '';
if (shortcode_exists('contact-form-7')) {
  if (function_exists('wpcf7_enqueue_styles')) {
    wpcf7_enqueue_styles();
  }
  if (function_exists('wpcf7_enqueue_scripts')) {
    wpcf7_enqueue_scripts();
  }
  $hr_pharmacy_cf7 = do_shortcode('[contact-form-7 id="63d0105" title="PPC - Pharmacy"]');
}

// Schema: SoftwareApplication + FAQPage (Yoast adds Organization, WebPage, Breadcrumb).
$hr_url    = get_permalink();
$hr_schema = array(
  '@context' => 'https://schema.org',
  '@graph'   => array(
    array(
      '@type'                  => 'SoftwareApplication',
      '@id'                    => $hr_url . '#software',
      'name'                   => 'Healthray Pharmacy',
      'url'                    => $hr_url,
      'applicationCategory'    => 'BusinessApplication',
      'applicationSubCategory' => 'Pharmacy management software',
      'operatingSystem'        => 'Web browser on Windows, macOS, Android or iOS',
      'publisher'              => array('@type' => 'Organization', 'name' => 'Healthray Technologies Pvt. Ltd.', 'url' => 'https://healthray.com/'),
      'offers'                 => array_map(function ($o) {
        return array('@type' => 'Offer', 'name' => $o['name'], 'price' => $o['price'], 'priceCurrency' => 'INR');
      }, $hr_offers),
    ),
    array(
      '@type'      => 'FAQPage',
      '@id'        => $hr_url . '#faq',
      'mainEntity' => array_map(function ($f) {
        return array('@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f['a']));
      }, $hr_faq),
    ),
  ),
);

$hr_tick   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z"/></svg>';
$hr_warn   = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg>';
$hr_circle = '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm-1.5 14.5-4-4 1.4-1.4 2.6 2.6 5.6-5.6 1.4 1.4z"/></svg>';
?>
<script type="application/ld+json"><?php echo wp_json_encode($hr_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>

<style id="hr-pmo-css">
/* ======================================================================
 * All rules are scoped to .hr-pmo (the <main> of this page), so the site
 * header and footer are never affected. The popup sits inside .hr-pmo too.
 * ====================================================================== */
.hr-pmo {--navy:#1A5692; --navy-dark:#123E6B; --sky:#E3F2FD; --sky-2:#F4F9FE; --green:#2E7D32; --red:#C62828; --text:#3E3D3D; --muted:#5F6670; --line:#D9E3EE; --cta:#C2410C; --cta-hover:#9A3412; --white:#fff; --radius:12px; --shadow:0 10px 30px rgba(18,62,107,.12); --section:80px; --gap:56px;
  display:block;background:var(--white);color:var(--text);font:400 16px/1.6 Mulish,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;-webkit-font-smoothing:antialiased;text-align:left}
.hr-pmo *,.hr-pmo *::before,.hr-pmo *::after {box-sizing:border-box}
.hr-pmo img,.hr-pmo svg,.hr-pmo video {max-width:100%}
.hr-pmo h1,.hr-pmo h2,.hr-pmo h3,.hr-pmo .hr-h2 {color:var(--navy-dark);font-family:inherit;line-height:1.2;margin:0 0 .5em;padding:0;text-transform:none;text-wrap:balance}
.hr-pmo .sec-head p,.hr-pmo .feature-copy p {text-wrap:pretty}
.hr-pmo .hero h1 {font-size:clamp(1.9rem,4.6vw,2.9rem);font-weight:800;letter-spacing:-.01em}
.hr-pmo h2,.hr-pmo .hr-h2 {font-size:clamp(1.5rem,3.2vw,2.1rem);font-weight:800;letter-spacing:-.005em}
.hr-pmo h3 {font-size:clamp(1.2rem,2.2vw,1.45rem);font-weight:800;letter-spacing:normal}
.hr-pmo p {margin:0 0 1em}
.hr-pmo a {color:var(--navy)}
.hr-pmo ul {margin:0;padding:0}
.hr-pmo .wrap {max-width:1160px;margin:0 auto;padding:0 16px}
.hr-pmo section {padding:var(--section) 0}
.hr-pmo .sec-head {max-width:720px;margin:0 auto 40px;text-align:center}
.hr-pmo .sec-head h2 {margin-bottom:.4em}
.hr-pmo .sec-head p {margin:0}
.hr-pmo .lead {font-size:1.125rem;font-weight:400;color:var(--muted)}
.hr-pmo .eyebrow {display:inline-block;font-size:.8rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--navy);margin-bottom:.6rem}
.hr-pmo :focus-visible {outline:3px solid #F59E0B;outline-offset:2px}
/* Buttons */
.hr-pmo .btn {display:inline-flex;align-items:center;justify-content:center;gap:.5rem;min-height:48px;padding:12px 24px;border-radius:10px;border:2px solid transparent;box-shadow:none;font-family:inherit;font-weight:800;font-size:1.05rem;line-height:1.2;text-transform:none;letter-spacing:normal;text-decoration:none;cursor:pointer;transition:background .15s,border-color .15s}
.hr-pmo .btn-cta,.hr-pmo .btn-cta:focus {background:var(--cta);color:#fff}
.hr-pmo .btn-cta:hover {background:var(--cta-hover);color:#fff}
.hr-pmo .btn-outline,.hr-pmo .btn-outline:focus {background:#fff;color:var(--navy);border-color:var(--navy)}
.hr-pmo .btn-outline:hover {background:var(--sky-2);color:var(--navy);border-color:var(--navy)}
/* Hero */
.hr-pmo .hero {background:linear-gradient(0deg,var(--sky-2),#fff);padding:56px 0 48px}
.hr-pmo .hero-grid {display:grid;grid-template-columns:1.15fr .85fr;gap:var(--gap);align-items:start}
.hr-pmo .hero-grid>* {min-width:0}
.hr-pmo .hero .sub {font-size:1.15rem;color:var(--muted);max-width:36em;margin-bottom:1.25em}
.hr-pmo .free-line {display:flex;gap:8px;align-items:flex-start;background:#fff;border:1px solid var(--line);border-left:4px solid var(--green);border-radius:8px;padding:10px 14px;font-weight:700;color:var(--text);max-width:36em;margin:0}
.hr-pmo .free-line svg {flex:none;width:20px;height:20px;margin-top:2px;fill:var(--green)}
.hr-pmo .trust {list-style:none;padding:0;margin:20px 0 0;display:flex;flex-wrap:wrap;gap:8px 20px;font-weight:700;color:var(--navy-dark);font-size:.95rem}
.hr-pmo .trust li {display:flex;align-items:flex-start;gap:6px;margin:0}
.hr-pmo .trust svg {flex:none;width:18px;height:18px;margin-top:2px;fill:var(--green)}
.hr-pmo .form-card {background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow);padding:24px}
.hr-pmo .form-card h2,.hr-pmo .form-card .hr-h2 {font-size:1.4rem;margin-bottom:4px}
.hr-pmo .form-card .hint {color:var(--muted);font-size:.95rem;margin-bottom:16px}
/* Product tour video */
.hr-pmo .tour {padding:16px 0 var(--section)}
.hr-pmo .tour-frame {max-width:1040px;margin:0 auto;border-radius:16px;overflow:hidden;background:#0F2236;box-shadow:0 2px 0 rgba(18,62,107,.04),0 24px 60px rgba(18,62,107,.18);border:1px solid rgba(18,62,107,.10)}
.hr-pmo .tour-frame video {display:block;width:100%;height:auto;aspect-ratio:1280/682;background:#0F2236}
.hr-pmo .tour-cta {display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:12px 20px;margin-top:28px}
.hr-pmo .tour-cta p {margin:0;color:var(--muted);font-weight:600;text-align:center}
/* Proof strip */
.hr-pmo .proof {padding:44px 0 36px;border-top:1px solid var(--line);border-bottom:1px solid var(--line);background:#fff}
.hr-pmo .logos-title {margin:0 0 20px;text-align:center;font-weight:800;font-size:1.05rem;color:var(--muted)}
.hr-pmo .logo-marquee {overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent);mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}
.hr-pmo .logo-track {display:flex;width:max-content;animation:hr-pmo-logos 28s linear infinite}
.hr-pmo .logo-marquee:hover .logo-track {animation-play-state:paused}
.hr-pmo .logo-set {display:flex;align-items:center;gap:56px;padding-right:56px}
.hr-pmo .logo-set img {display:block;width:80px;height:80px;object-fit:contain}
@keyframes hr-pmo-logos {to {transform:translateX(-50%)}}
.hr-pmo .proof-grid {display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:center;margin-top:32px;padding-top:28px;border-top:1px solid #EEF2F6}
.hr-pmo .proof-grid strong {display:block;font-size:1.6rem;color:var(--navy-dark);font-weight:800;line-height:1.2}
.hr-pmo .proof-grid span {font-size:.92rem;color:var(--muted);font-weight:600}
/* Problems + buying guide */
.hr-pmo .problems {background:var(--sky-2)}
.hr-pmo .pain-list {list-style:none;padding:0;margin:0 auto;max-width:760px;display:grid;gap:12px}
.hr-pmo .pain-list li {display:flex;gap:12px;align-items:flex-start;background:#fff;border:1px solid var(--line);border-radius:10px;padding:14px 16px;margin:0;font-weight:600;text-align:left}
.hr-pmo .pain-list svg {flex:none;width:22px;height:22px;fill:var(--red);margin-top:1px}
.hr-pmo .pain-list.choose-list svg {fill:var(--green)}
.hr-pmo .pain-list.choose-list b {color:var(--navy-dark)}
/* Features: text and screenshot side by side, switching sides each row. */
.hr-pmo .features {display:grid;gap:96px;background:none}
.hr-pmo .feature {display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.25fr);gap:56px;align-items:center}
.hr-pmo .feature:nth-child(even) {grid-template-columns:minmax(0,1.25fr) minmax(0,1fr)}
.hr-pmo .feature:nth-child(even) .feature-head {order:2}
.hr-pmo .feature-head {display:flex;flex-direction:column;gap:20px}
.hr-pmo .feature-copy h3 {margin-bottom:.4em}
.hr-pmo .feature-copy p {color:var(--muted);font-size:1.05rem;margin:0}
.hr-pmo .feature-points .ticks {margin:0}
.hr-pmo .feature-points .plan-note {margin-top:14px}
.hr-pmo .feature-visual img {display:block;width:100%;height:auto;filter:drop-shadow(0 12px 22px rgba(18,52,90,.13)) drop-shadow(0 2px 4px rgba(18,52,90,.06))}
.hr-pmo .plan-note {display:inline-block;font-size:.85rem;font-weight:700;color:var(--navy);background:var(--sky);border-radius:6px;padding:3px 10px}
.hr-pmo .ticks {list-style:none;padding:0;margin:0 0 1.1em}
.hr-pmo .ticks li {position:relative;padding-left:30px;margin:0 0 8px;font-weight:600}
.hr-pmo .ticks li::before {content:"";position:absolute;left:0;top:4px;width:18px;height:18px;border-radius:50%;background:var(--green) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='white' d='M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z'/%3E%3C/svg%3E") center/14px no-repeat}
.hr-pmo .also-wrap {margin-top:88px;padding-top:48px;border-top:1px solid var(--line)}
.hr-pmo .also-wrap h3 {text-align:center;margin-bottom:20px}
.hr-pmo .also {display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.hr-pmo .also div {display:flex;align-items:center;justify-content:center;background:var(--sky-2);border:1px solid var(--line);border-radius:10px;padding:14px 12px;font-weight:700;font-size:.95rem;line-height:1.35;text-align:center}
/* Internet */
.hr-pmo .internet {background:var(--navy-dark);color:#E8F1FB}
.hr-pmo .internet h2 {color:#fff}
.hr-pmo .internet .eyebrow {color:#9CC3EA}
.hr-pmo .internet-grid {display:grid;grid-template-columns:1fr 1fr;gap:var(--gap);align-items:center}
.hr-pmo .internet-grid>* {min-width:0}
.hr-pmo .internet-points {list-style:none;padding:0;margin:0;display:grid;gap:12px}
.hr-pmo .internet-points li {background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:14px 16px;margin:0}
.hr-pmo .internet-points strong {color:#fff;display:block}
/* Steps */
.hr-pmo .steps {display:grid;grid-template-columns:repeat(3,1fr);gap:20px;counter-reset:s}
.hr-pmo .step {background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:24px;position:relative}
.hr-pmo .step::before {counter-increment:s;content:counter(s);display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:var(--navy);color:#fff;font-weight:800;margin-bottom:12px}
.hr-pmo .step h3 {font-size:1.15rem}
.hr-pmo .step p {color:var(--muted);margin:0}
/* Testimonials */
.hr-pmo .testi {background:var(--sky-2)}
.hr-pmo .testi-grid {display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.hr-pmo .testi-card {position:relative;display:flex;flex-direction:column;margin:0;background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:24px;box-shadow:0 1px 0 rgba(18,62,107,.03)}
.hr-pmo .testi-card blockquote {margin:0 0 20px;padding:0;border:0;flex:1;font-size:1.02rem;font-style:normal;line-height:1.6;color:var(--text)}
.hr-pmo .testi-card blockquote::before {content:"“";display:block;font-size:2.6rem;line-height:.8;color:var(--navy);opacity:.35;margin-bottom:6px}
.hr-pmo .testi-card .who {display:flex;gap:12px;align-items:center;padding-top:16px;border-top:1px solid #EEF2F6}
.hr-pmo .testi-card .who b {color:var(--navy-dark)}
.hr-pmo .avatar {width:44px;height:44px;border-radius:50%;background:#fff;border:1px solid var(--line);overflow:hidden;display:flex;align-items:center;justify-content:center;flex:none}
.hr-pmo .avatar img {display:block;width:100%;height:100%;object-fit:contain}
.hr-pmo .testi-card small {display:block;color:var(--muted)}
/* Pricing */
.hr-pmo .pricing-grid {display:grid;grid-template-columns:repeat(3,1fr);gap:20px;align-items:stretch;max-width:980px;margin:0 auto}
.hr-pmo .plan {border:1px solid var(--line);border-radius:14px;padding:22px 18px;display:flex;flex-direction:column;background:#fff}
.hr-pmo .plan.popular {border:2px solid var(--navy);box-shadow:var(--shadow);position:relative}
.hr-pmo .badge {position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--navy);color:#fff;font-size:.78rem;font-weight:800;line-height:1.4;padding:3px 10px;border-radius:999px;white-space:nowrap}
.hr-pmo .plan h3 {margin-bottom:4px;font-size:1.2rem}
.hr-pmo .price {font-size:1.7rem;font-weight:800;color:var(--navy-dark);line-height:1.2}
.hr-pmo .price small {font-size:.9rem;font-weight:700;color:var(--muted)}
.hr-pmo .yearly {font-size:.88rem;color:var(--muted);line-height:1.45;margin:6px 0 14px;min-height:2.9em}
.hr-pmo .plan ul {list-style:none;padding:0;margin:0 0 18px;flex:1}
.hr-pmo .plan li {padding:6px 0 6px 24px;margin:0;position:relative;font-size:.94rem;border-bottom:1px solid #F0F4F8}
.hr-pmo .plan li::before {content:"✓";position:absolute;left:2px;color:var(--green);font-weight:800}
.hr-pmo .plan .btn {width:100%;font-size:1rem}
.hr-pmo .ent {max-width:980px;margin:24px auto 0;display:flex;align-items:center;justify-content:space-between;gap:16px 32px;background:var(--sky-2);border:1px solid var(--line);border-radius:14px;padding:22px 24px}
.hr-pmo .ent h3 {font-size:1.15rem;margin:0 0 4px}
.hr-pmo .ent p {margin:0;color:var(--muted);max-width:600px}
.hr-pmo .ent .btn {flex:none}
.hr-pmo .pricing-foot {margin-top:28px;text-align:center;color:var(--muted);font-size:.95rem}
.hr-pmo .pricing-foot p {margin:0 0 4px}
/* FAQ */
.hr-pmo .faq-sec {background:var(--sky-2)}
.hr-pmo .faq {max-width:820px;margin:0 auto}
.hr-pmo .faq details {border:1px solid var(--line);border-radius:10px;margin-bottom:10px;background:#fff}
.hr-pmo .faq summary {cursor:pointer;list-style:none;padding:16px 48px 16px 18px;font-weight:800;color:var(--navy-dark);position:relative}
.hr-pmo .faq summary::-webkit-details-marker {display:none}
.hr-pmo .faq summary::after {content:"+";position:absolute;right:16px;top:16px;width:24px;height:1.6rem;line-height:1.6rem;text-align:center;font-size:1.5rem;font-weight:700;color:var(--navy)}
.hr-pmo .faq details[open] summary::after {content:"−"}
.hr-pmo .faq details p {padding:0 18px 16px;margin:0;color:var(--muted)}
/* Final CTA */
/* A rounded card on white, so it stands apart from the dark site footer below instead of merging into it. */
.hr-pmo .final {background:#fff;text-align:center}
.hr-pmo .final-card {position:relative;overflow:hidden;max-width:1040px;margin:0 auto;padding:56px 32px;border-radius:20px;background:linear-gradient(135deg,var(--navy),var(--navy-dark));color:#E8F1FB;box-shadow:0 20px 50px rgba(18,62,107,.22)}
.hr-pmo .final-card::before {content:"";position:absolute;inset:auto -80px -120px auto;width:320px;height:320px;border-radius:50%;background:radial-gradient(circle,rgba(255,255,255,.10),transparent 70%);pointer-events:none}
.hr-pmo .final-card>* {position:relative}
.hr-pmo .final h2,.hr-pmo .final .hr-h2 {color:#fff}
.hr-pmo .final p {margin:0 auto;max-width:36em;color:#D6E6F7}
.hr-pmo .final .btns {display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin:24px 0 0}
/* Sticky mobile bar */
.hr-pmo .sticky {position:fixed;left:0;right:0;bottom:0;display:none;gap:8px;padding:10px 16px calc(10px + env(safe-area-inset-bottom));background:#fff;border-top:1px solid var(--line);box-shadow:0 -6px 20px rgba(0,0,0,.08);z-index:999;transform:translateY(110%);transition:transform .2s}
.hr-pmo .sticky.show {transform:none}
.hr-pmo .sticky .btn {flex:1;min-height:48px;padding:10px}

@media (max-width:960px) {
  .hr-pmo {--section:64px;--gap:32px}
  .hr-pmo .hero-grid,.hr-pmo .internet-grid {grid-template-columns:1fr}
  .hr-pmo .feature,.hr-pmo .feature:nth-child(even) {grid-template-columns:1fr;gap:28px}
  .hr-pmo .feature:nth-child(even) .feature-head {order:0}
  .hr-pmo .feature-head {max-width:640px}
  .hr-pmo .features {gap:72px}
  .hr-pmo .pricing-grid {gap:14px}
  .hr-pmo .proof-grid {grid-template-columns:repeat(2,1fr)}
  .hr-pmo .also {grid-template-columns:repeat(2,1fr)}
}
@media (max-width:720px) {
  .hr-pmo {--section:52px;--gap:24px}
  .hr-pmo h2,.hr-pmo .hr-h2 {font-size:1.4rem}
  .hr-pmo .hero {padding:20px 0 28px}
  .hr-pmo .hero .eyebrow {font-size:.72rem;letter-spacing:.06em;margin-bottom:.45rem}
  .hr-pmo .hero .sub {font-size:1.02rem;line-height:1.55;margin-bottom:16px}
  .hr-pmo .free-line {padding:9px 12px;font-size:.93rem;line-height:1.45}
  .hr-pmo .free-line svg {width:18px;height:18px;margin-top:1px}
  .hr-pmo .trust {margin-top:14px;gap:6px 16px;font-size:.88rem}
  .hr-pmo .trust svg {width:16px;height:16px}
  .hr-pmo .form-card {padding:20px 18px}
  .hr-pmo .sec-head {margin-bottom:28px}
  .hr-pmo .lead {font-size:1rem;line-height:1.55}
  .hr-pmo .tour {padding:8px 0 40px}
  .hr-pmo .tour-frame {border-radius:12px}
  .hr-pmo .tour-cta {margin-top:20px}
  .hr-pmo .proof {padding:32px 0 28px}
  .hr-pmo .logos-title {margin-bottom:14px;font-size:1rem}
  .hr-pmo .logo-set {gap:36px;padding-right:36px}
  .hr-pmo .logo-set img {width:64px;height:64px}
  .hr-pmo .logo-track {animation-duration:22s}
  .hr-pmo .proof-grid {margin-top:24px;padding-top:20px;gap:14px}
  .hr-pmo .pain-list {gap:10px}
  .hr-pmo .pain-list li {padding:12px 14px;font-size:.98rem;line-height:1.5}
  .hr-pmo .pain-list svg {width:20px;height:20px;margin-top:2px}
  /* Each feature: label, heading and short text, then its picture, then the ticks. */
  .hr-pmo .features {gap:0}
  .hr-pmo .feature+.feature {margin-top:40px;padding-top:40px;border-top:1px solid var(--line)}
  .hr-pmo .feature,.hr-pmo .feature:nth-child(even) {gap:20px}
  .hr-pmo .feature-head,.hr-pmo .feature:nth-child(even) .feature-head {display:contents}
  .hr-pmo .feature-copy {order:1}
  .hr-pmo .feature-visual {order:2}
  .hr-pmo .feature-points {order:3}
  .hr-pmo .feature-copy p {font-size:1rem;line-height:1.6}
  .hr-pmo .ticks li {font-size:.98rem;line-height:1.5}
  .hr-pmo .ticks li::before {top:3px}
  .hr-pmo .feature-points .plan-note {margin-top:10px;font-size:.8rem}
  .hr-pmo .also-wrap {margin-top:56px;padding-top:36px}
  .hr-pmo .also div {padding:12px 10px}
  .hr-pmo .steps,.hr-pmo .testi-grid,.hr-pmo .pricing-grid {grid-template-columns:1fr}
  .hr-pmo .steps {gap:12px}
  .hr-pmo .step {display:grid;grid-template-columns:40px 1fr;gap:4px 14px;padding:18px 16px}
  .hr-pmo .step::before {grid-row:1 / span 2;margin:0}
  .hr-pmo .step h3 {margin:8px 0 0}
  .hr-pmo .testi-grid {gap:14px}
  .hr-pmo .testi-card {padding:20px 18px}
  .hr-pmo .testi-card blockquote {font-size:1rem;margin-bottom:16px}
  .hr-pmo .testi-card blockquote::before {font-size:2rem;line-height:.7;margin-bottom:4px}
  .hr-pmo .testi-card .who {padding-top:14px}
  .hr-pmo .pricing-grid .plan.popular {margin-top:8px}
  .hr-pmo .plan {padding:20px 18px}
  .hr-pmo .plan li {padding:5px 0 5px 26px}
  .hr-pmo .plan li::before {content:"";left:0;top:9px;width:16px;height:16px;border-radius:50%;background:var(--green) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='white' d='M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z'/%3E%3C/svg%3E") center/12px no-repeat}
  .hr-pmo .yearly {min-height:0}
  .hr-pmo .ent {flex-direction:column;align-items:flex-start}
  .hr-pmo .ent .btn {width:100%}
  .hr-pmo .pricing-foot {font-size:.9rem;margin-top:22px}
  .hr-pmo .faq summary {padding:14px 46px 14px 16px}
  .hr-pmo .faq summary::after {top:14px}
  .hr-pmo .faq details p {padding:0 16px 14px}
  .hr-pmo .final-card {padding:40px 20px;border-radius:16px}
  .hr-pmo .final .btns .btn {width:100%;max-width:320px}
  .hr-pmo .sticky {display:flex}
  /* Room for the sticky "Start free" bar, so it never covers the bottom of the site footer. */
  body:has(.hr-pmo .sticky) {padding-bottom:72px}
}
@media (prefers-reduced-motion:reduce) {
  .hr-pmo .logo-marquee {-webkit-mask-image:none;mask-image:none}
  .hr-pmo .logo-track {animation:none;width:auto;justify-content:center}
  .hr-pmo .logo-set {flex-wrap:wrap;justify-content:center;gap:16px 40px;padding:0}
  .hr-pmo .logo-set[aria-hidden] {display:none}
  .hr-pmo .sticky {transition:none}
}

/* ---------- Popup (shared by every button on the page) ---------- */
dialog#hr-pharmacy-modal {box-sizing:border-box !important; position:fixed !important; inset:0 !important; width:calc(100% - 32px) !important; min-width:0 !important; max-width:500px !important; height:fit-content !important; min-height:0 !important; max-height:calc(100vh - 40px) !important; max-height:calc(100dvh - 40px) !important; margin:auto !important; padding:28px !important; border:1px solid #D9E3EE !important; border-radius:20px !important; background:#fff !important; color:#3E3D3D !important; box-shadow:0 24px 80px rgba(10,30,50,.25) !important; overflow-x:hidden !important; overflow-y:auto !important; overscroll-behavior:contain; font:400 16px/1.6 Mulish,system-ui,-apple-system,"Segoe UI",Arial,sans-serif !important; text-align:left !important; transform:none !important; float:none !important; outline:none; z-index:100000}
dialog#hr-pharmacy-modal:not([open]) {display:none !important}
dialog#hr-pharmacy-modal[open] {display:block !important}
dialog#hr-pharmacy-modal::backdrop {background:rgba(10,30,50,.62) !important}
dialog#hr-pharmacy-modal button.hr-pharmacy-modal-close {box-sizing:border-box !important; position:absolute !important; top:14px !important; right:14px !important; bottom:auto !important; left:auto !important; display:flex !important; align-items:center !important; justify-content:center !important; width:32px !important; min-width:32px !important; max-width:32px !important; height:32px !important; min-height:32px !important; margin:0 !important; padding:0 !important; border:0 !important; border-radius:50% !important; background:#F4F9FE !important; background-image:none !important; color:#123E6B !important; box-shadow:none !important; font:400 23px/1 Arial,sans-serif !important; text-transform:none !important; letter-spacing:normal !important; cursor:pointer; z-index:2; appearance:none}
dialog#hr-pharmacy-modal button.hr-pharmacy-modal-close:hover {background:#E3F2FD !important}
dialog#hr-pharmacy-modal button.hr-pharmacy-modal-close:focus-visible {outline:2px solid #1A5692 !important; outline-offset:2px}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-title {display:block !important; margin:0 36px 6px 0 !important; padding:0 !important; color:#123E6B !important; font:800 23px/1.35 Mulish,system-ui,sans-serif !important; letter-spacing:normal !important; text-transform:none !important}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-hint {margin:0 0 22px !important; padding:0 !important; color:#5F6670 !important; font:400 14px/1.65 Mulish,system-ui,sans-serif !important}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-slot {display:block; width:100%; min-width:0; margin:0; padding:0}
html.hr-pharmacy-modal-open, html.hr-pharmacy-modal-open body {overflow:hidden !important}
@media (max-width:540px) {
  dialog#hr-pharmacy-modal {width:calc(100% - 24px) !important; max-height:calc(100vh - 24px) !important; max-height:calc(100dvh - 24px) !important; padding:24px 20px !important; border-radius:16px !important}
  dialog#hr-pharmacy-modal #hr-pharmacy-modal-title {font-size:22px !important}
}
@media (max-width:480px) {
  dialog#hr-pharmacy-modal {padding:24px 18px !important}
  dialog#hr-pharmacy-modal #hr-pharmacy-modal-title {font-size:21px !important}
}

/* ---------- CF7 two-step form (hero and popup) ---------- */
#hr-pharmacy-cf7, #hr-pharmacy-cf7 .wpcf7, #hr-pharmacy-cf7 form, #hr-pharmacy-cf7 .hr-fstep {width:100%; min-width:0; max-width:100%; box-sizing:border-box}
#hr-pharmacy-cf7 .wpcf7 {margin:0}
#hr-pharmacy-cf7 .wpcf7-form>p {margin:0 0 16px}
#hr-pharmacy-cf7 label {font-family:inherit}
#hr-pharmacy-cf7 .wpcf7-form-control-wrap {display:block}
#hr-pharmacy-cf7 :is(input[type="email"], input[type="url"], input[type="number"], select, textarea) {width:100%; min-height:48px; border:1.5px solid #B8C6D6; border-radius:8px; padding:10px 12px; font:inherit; color:var(--text); background:#fff}
#hr-pharmacy-cf7 textarea {min-height:100px; resize:vertical}
#hr-pharmacy-cf7 .wpcf7-acceptance label {display:flex; align-items:flex-start; gap:8px}
#hr-pharmacy-cf7 .wpcf7-acceptance input {flex:none; margin-top:5px}
#hr-pharmacy-cf7 .hr-fstep[hidden], #hr-pharmacy-cf7 .hr-err[hidden] {display:none !important}
#hr-pharmacy-cf7 .hr-fsteps {margin:0 0 20px}
#hr-pharmacy-cf7 .hr-fsteps-label {display:block; margin-bottom:8px; font-size:.85rem; font-weight:700; color:var(--muted)}
#hr-pharmacy-cf7 .hr-fsteps-bar {display:flex; gap:6px}
#hr-pharmacy-cf7 .hr-fsteps-bar i {height:4px; flex:1; border-radius:4px; background:var(--line)}
#hr-pharmacy-cf7 .hr-fsteps-bar i:first-child, #hr-pharmacy-cf7[data-step="2"] .hr-fsteps-bar i {background:var(--navy)}
#hr-pharmacy-cf7 .hr-field {display:block !important; margin:0 0 18px !important; padding:0 !important}
#hr-pharmacy-cf7 .hr-field>label, #hr-pharmacy-cf7 .hr-field legend {display:block !important; margin:0 0 8px !important; padding:0 !important; font-size:15px; font-weight:700; line-height:1.4; text-align:left}
#hr-pharmacy-cf7 .hr-field>.wpcf7-form-control-wrap {display:block !important; width:100% !important}
#hr-pharmacy-cf7 .hr-field>br, #hr-pharmacy-cf7 .hr-tel br {display:none !important}
#hr-pharmacy-cf7 input.wpcf7-text, #hr-pharmacy-cf7 input[name="your-name"], #hr-pharmacy-cf7 input[name="your-number"] {display:block !important; box-sizing:border-box !important; float:none !important; width:100% !important; max-width:100% !important; min-width:0 !important; height:48px !important; min-height:48px !important; margin:0 !important; padding:10px 12px !important; border:1.5px solid #B8C6D6 !important; border-radius:8px !important; background:#fff !important; color:var(--text) !important; font:400 16px/1.4 Mulish, system-ui, sans-serif !important; box-shadow:none !important; appearance:none}
#hr-pharmacy-cf7 input.wpcf7-text:focus {border-color:var(--navy) !important; outline:2px solid rgba(26,86,146,.18) !important; outline-offset:1px}
#hr-pharmacy-cf7 input.wpcf7-not-valid {border-color:#C62828 !important}
#hr-pharmacy-cf7 .hr-tel, #hr-pharmacy-cf7 .hr-tel>p {display:flex !important; align-items:flex-start !important; gap:0 !important; width:100% !important; min-width:0 !important; margin:0 !important; padding:0 !important}
#hr-pharmacy-cf7 .hr-tel>span[aria-hidden="true"], #hr-pharmacy-cf7 .hr-tel>p>span[aria-hidden="true"] {display:flex !important; align-items:center !important; justify-content:center !important; flex:0 0 54px !important; height:48px !important; box-sizing:border-box !important; margin:0 !important; padding:0 10px !important; border:1.5px solid #B8C6D6 !important; border-right:0 !important; border-radius:8px 0 0 8px !important; background:var(--sky-2) !important; font-weight:700}
#hr-pharmacy-cf7 .hr-tel .wpcf7-form-control-wrap {display:block !important; flex:1 1 0% !important; min-width:0 !important; width:auto !important; margin:0 !important; border:0 !important; border-radius:0 !important; background:transparent !important; box-shadow:none !important}
#hr-pharmacy-cf7 .hr-tel #hr-mobile, #hr-pharmacy-cf7 .hr-tel input {border-radius:0 8px 8px 0 !important}
#hr-pharmacy-cf7 .hr-picked {display:flex; justify-content:space-between; align-items:center; gap:8px; margin:0 0 18px !important; font-size:.9rem; line-height:1.5}
#hr-pharmacy-cf7 .hr-picked>span {min-width:0}
#hr-pharmacy-cf7 .hr-back {flex:none; min-height:32px; padding:4px; border:0; background:none; color:var(--navy); font:inherit; text-decoration:underline; cursor:pointer}
#hr-pharmacy-cf7 .hr-fstep input.wpcf7-submit, #hr-pharmacy-cf7 .hr-fstep input[type="submit"], #hr-pharmacy-cf7 .hr-fstep button[type="submit"], #hr-pharmacy-cf7 .hr-next {display:flex !important; align-items:center !important; justify-content:center !important; box-sizing:border-box !important; float:none !important; width:100% !important; max-width:none !important; min-height:48px !important; height:auto !important; margin:12px 0 0 !important; padding:14px 20px !important; border:0 !important; border-radius:10px !important; background:var(--cta) !important; background-image:none !important; color:#fff !important; font:800 16px/1.3 Mulish, system-ui, sans-serif !important; text-transform:none !important; letter-spacing:normal !important; box-shadow:none !important; cursor:pointer; appearance:none}
#hr-pharmacy-cf7 .hr-fstep input[type="submit"]:hover, #hr-pharmacy-cf7 .hr-fstep button[type="submit"]:hover, #hr-pharmacy-cf7 .hr-next:hover {background:var(--cta-hover) !important}
#hr-pharmacy-cf7 .hr-fstep input[type="submit"]:disabled {opacity:.65; cursor:wait}
#hr-pharmacy-cf7 .hr-fstep>p:has(.wpcf7-submit) {position:relative; margin:0 0 14px !important; padding:0 !important}
#hr-pharmacy-cf7 .hr-fstep>p:has(.wpcf7-submit)>br {display:none !important}
#hr-pharmacy-cf7 .wpcf7-spinner {position:absolute; right:8px; bottom:12px; margin:0 !important}
#hr-pharmacy-cf7 .hr-step-note {margin:10px 0 0; text-align:center; font-size:.85rem; color:var(--muted)}
#hr-pharmacy-cf7 .hr-consent {margin:14px 0 0 !important; font-size:12px !important; line-height:1.7 !important; color:var(--muted)}
#hr-pharmacy-cf7 .hr-optin {margin:12px 0 0 !important; font-size:13px; line-height:1.6}
#hr-pharmacy-cf7 .hr-optin p {margin:0 !important}
#hr-pharmacy-cf7 .hr-optin .wpcf7-list-item {margin:0}
#hr-pharmacy-cf7 .hr-optin label {display:flex !important; align-items:flex-start; gap:8px; cursor:pointer}
#hr-pharmacy-cf7 .hr-optin input[type="checkbox"] {appearance:auto !important; flex:0 0 16px !important; width:16px !important; height:16px !important; min-height:0 !important; margin:3px 0 0 !important; padding:0 !important; accent-color:var(--navy)}
/* Step 2: two fields in a row (name + email, mobile + city) so the form is shorter; one per row on phones.
   Everything that is not a field (picked line, button, notes, errors) keeps the full width. */
@media (min-width: 481px) {
#hr-pharmacy-cf7 .hr-fstep-2:not([hidden]) {display: grid !important; grid-template-columns: repeat(2, minmax(0, 1fr)); column-gap: 14px}
#hr-pharmacy-cf7 .hr-fstep-2 > * {grid-column: 1 / -1}
#hr-pharmacy-cf7 .hr-fstep-2 > .hr-field {grid-column: auto; min-width: 0}
}
#hr-pharmacy-cf7 .wpcf7-not-valid-tip, #hr-pharmacy-cf7 .hr-err {position:static !important; display:block; box-sizing:border-box !important; width:100% !important; max-width:100% !important; min-height:0 !important; height:auto !important; margin:6px 0 0 !important; padding:0 !important; border:0 !important; border-radius:0 !important; background:transparent !important; box-shadow:none !important; color:#C62828 !important; font:400 12px/1.5 Mulish,system-ui,sans-serif !important; text-align:left !important; white-space:normal !important}
#hr-pharmacy-cf7 .wpcf7-not-valid-tip::before, #hr-pharmacy-cf7 .wpcf7-not-valid-tip::after {display:none !important}
#hr-pharmacy-cf7 .wpcf7-response-output {margin:16px 0 0 !important; padding:10px; font-size:13px; line-height:1.5}
#hr-pharmacy-cf7 .wpcf7-response-output:empty {display:none !important}
/* Billing choice buttons (value goes into the hidden bed_size field as a plain string). */
#hr-pharmacy-cf7 .hr-bill-options {display:grid !important; grid-template-columns:repeat(2,minmax(0,1fr)) !important; gap:10px !important; width:100%}
#hr-pharmacy-cf7 .hr-bill-options>br {display:none !important}
#hr-pharmacy-cf7 .hr-bill-options>p {display:contents !important}
#hr-pharmacy-cf7 .hr-bill-option {position:relative !important; display:flex !important; flex-direction:column !important; align-items:flex-start !important; justify-content:center !important; gap:4px !important; box-sizing:border-box !important; width:100% !important; min-width:0 !important; min-height:76px !important; margin:0 !important; padding:12px 12px 12px 40px !important; border:1px solid #D9E3EE !important; border-radius:12px !important; background:#fff !important; background-image:none !important; box-shadow:none !important; color:#123E6B !important; font:600 14px/1.4 Mulish,system-ui,sans-serif !important; text-align:left !important; text-transform:none !important; letter-spacing:normal !important; cursor:pointer; appearance:none; transition:border-color .15s,background .15s}
#hr-pharmacy-cf7 .hr-bill-option::before {content:""; position:absolute; left:13px; top:50%; width:16px; height:16px; margin-top:-8px; border:2px solid #9AACBF; border-radius:50%; background:#fff; box-sizing:border-box}
#hr-pharmacy-cf7 .hr-bill-option:hover {border-color:#1A5692 !important; background:#F8FBFE !important}
#hr-pharmacy-cf7 .hr-bill-option.is-selected {border-color:#1A5692 !important; background:#F0F7FF !important; box-shadow:0 0 0 1px #1A5692 !important}
#hr-pharmacy-cf7 .hr-bill-option.is-selected::before {border:5px solid #1A5692}
#hr-pharmacy-cf7 .hr-bill-option:focus-visible {outline:3px solid #F59E0B !important; outline-offset:2px}
#hr-pharmacy-cf7 .hr-bill-title {display:block; font-size:14px; font-weight:700; line-height:1.35; color:#123E6B}
#hr-pharmacy-cf7 .hr-bill-option .hr-choice-note {display:block !important; margin:0 !important; font-size:12.5px !important; font-weight:400 !important; line-height:1.4 !important; color:#5F6670 !important}
#hr-pharmacy-cf7 .hr-software.invalid .hr-bill-option {border-color:#C62828 !important}
@media (max-width:480px) {
  #hr-pharmacy-cf7 .hr-bill-options {grid-template-columns:1fr !important; gap:8px !important}
  #hr-pharmacy-cf7 .hr-bill-option {min-height:60px !important; padding:10px 14px 10px 40px !important}
}
</style>

<main class="hr-pmo hr-pharmacy-landing" id="hr-pmo">

<!-- ============ HERO ============ -->
<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <span class="eyebrow">For medical stores &amp; chemists</span>
      <h1>Pharmacy Management Software for Medical Stores in India</h1>
      <p class="sub">Billing, batch-wise stock, expiry alerts, purchase entry and GST reports in one pharmacy software. Works on computer, laptop or mobile.</p>
      <p class="free-line">
        <?php echo $hr_circle; ?>
        <span>Free forever: 10 bills a month, 1 user. No card needed. No auto payment.</span>
      </p>
      <ul class="trust">
        <li><?php echo $hr_tick; ?>Trusted by 2000+ pharmacies across India</li>
        <li><?php echo $hr_tick; ?>24×7 support</li>
        <li><?php echo $hr_tick; ?>Old data moved free</li>
      </ul>
    </div>

    <!-- ============ FORM (moves into the popup when a button is clicked, then back) ============ -->
    <div class="form-card" id="hr-pmo-start">
      <div class="hr-h2">Start free today</div>
      <p class="hint">Takes 30 seconds. We'll help you set up and move your old data free.</p>
      <div id="hr-pharmacy-form-home">
        <div id="hr-pharmacy-cf7">
          <?php if ($hr_pharmacy_cf7) : ?>
            <?php echo $hr_pharmacy_cf7; // Trusted output generated by the CF7 shortcode. ?>
          <?php else : ?>
            <p role="alert">Please activate Contact Form 7 to display the pharmacy form.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ============ PRODUCT TOUR VIDEO ============ -->
<section class="tour" aria-labelledby="hr-pmo-tour-title">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow">1-minute video</span>
      <h2 id="hr-pmo-tour-title">See the pharmacy software in 1 minute</h2>
      <p class="lead">Watch a bill being made and sent on WhatsApp, a purchase being added, and the expiry list. This is the real software.</p>
    </div>
    <div class="tour-frame">
      <!-- No audio track and no controls: plays by itself when scrolled into view, pauses when out of view. -->
      <video id="hr-pmo-tour-video" width="1280" height="682" muted playsinline loop disablepictureinpicture preload="metadata"
             poster="<?php echo esc_url($hr_media . 'tour-poster.jpg'); ?>" aria-label="1-minute tour of Healthray pharmacy software">
        <source src="<?php echo esc_url($hr_media . 'product-tour.mp4'); ?>" type="video/mp4">
      </video>
    </div>
    <div class="tour-cta">
      <p>Want this for your medical store?</p>
      <a class="btn btn-cta js-start" href="#hr-pmo-start" data-cta="tour_start">Start free</a>
    </div>
  </div>
</section>

<!-- ============ PROOF STRIP ============ -->
<div class="proof">
  <div class="wrap">
    <p class="logos-title">2,000+ pharmacies use Healthray</p>
    <!-- Scrolls right to left in a loop; the second set is a copy so the loop has no gap. Pauses on hover. -->
    <div class="logo-marquee">
      <div class="logo-track">
        <?php for ($hr_set = 0; $hr_set < 2; $hr_set++) : ?>
        <div class="logo-set"<?php echo $hr_set ? ' aria-hidden="true"' : ''; ?>>
          <?php foreach ($hr_logos as $hr_file => $hr_name) : ?>
          <img src="<?php echo esc_url($hr_logo_base . $hr_file); ?>" alt="<?php echo $hr_set ? '' : esc_attr($hr_name); ?>" width="150" height="150" loading="lazy">
          <?php endforeach; ?>
        </div>
        <?php endfor; ?>
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

<!-- ============ PROBLEMS ============ -->
<section class="problems">
  <div class="wrap">
    <div class="sec-head"><h2>Does this happen at your counter?</h2></div>
    <ul class="pain-list">
      <li><?php echo $hr_warn; ?>Expired stock is found too late, and the company won't take it back.</li>
      <li><?php echo $hr_warn; ?>The software hangs at 8 pm when 5 customers are waiting.</li>
      <li><?php echo $hr_warn; ?>Hours go in purchase entry of distributor bills.</li>
      <li><?php echo $hr_warn; ?>Your CA keeps asking for the same GST data again.</li>
      <li><?php echo $hr_warn; ?>You don't know what discount and udhaar your staff are giving.</li>
    </ul>
  </div>
</section>

<!-- ============ FEATURES ============ -->
<section id="features">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Features</span><h2>One pharmacy management software for billing, stock, expiry and GST</h2></div>

    <div class="features">
      <!-- 1 Billing -->
      <div class="feature">
        <div class="feature-head">
          <div class="feature-copy">
            <span class="eyebrow">Billing (POS)</span>
            <h3>Fast billing, even when the counter is full</h3>
            <p>Turn a prescription into a bill in seconds. Find any medicine by name, salt or barcode. Sell a full strip or loose tablets, and the price is calculated for you.</p>
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
          <img src="<?php echo esc_url($hr_media); ?>feature-billing.webp" srcset="<?php echo esc_url($hr_media); ?>feature-billing-768x581.webp 768w, <?php echo esc_url($hr_media); ?>feature-billing-1024x774.webp 1024w, <?php echo esc_url($hr_media); ?>feature-billing.webp 1300w" sizes="(max-width: 960px) calc(100vw - 32px), 600px" width="650" height="491" loading="lazy" decoding="async"
               alt="Healthray pharmacy billing software sale screen with four items billed for ₹229, and the customer's WhatsApp showing the bill PDF with a thank-you message">
        </div>
      </div>

      <!-- 2 Expiry & stock alerts -->
      <div class="feature">
        <div class="feature-head">
          <div class="feature-copy">
            <span class="eyebrow">Inventory &amp; expiry management</span>
            <h3>Catch near-expiry stock before it becomes a loss</h3>
            <p>See near-expiry stock batch-wise with party name, so you can send it for expiry return on time. The list comes to your WhatsApp by itself.</p>
          </div>
          <div class="feature-points">
            <ul class="ticks">
              <li>Batch-wise stock (inventory) with expiry date</li>
              <li>FEFO alerts (first expiry, first out): sell the batch that expires first</li>
              <li>Alert when an expired batch is billed</li>
              <li>Near-expiry list with party name</li>
              <li>Shortage list daily, expiry list monthly, on WhatsApp</li>
            </ul>
          </div>
        </div>
        <div class="feature-visual">
          <img src="<?php echo esc_url($hr_media); ?>feature-expiry.webp" srcset="<?php echo esc_url($hr_media); ?>feature-expiry-768x561.webp 768w, <?php echo esc_url($hr_media); ?>feature-expiry-1024x748.webp 1024w, <?php echo esc_url($hr_media); ?>feature-expiry.webp 1300w" sizes="(max-width: 960px) calc(100vw - 32px), 600px" width="650" height="475" loading="lazy" decoding="async"
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
          <img src="<?php echo esc_url($hr_media); ?>feature-purchase.webp" srcset="<?php echo esc_url($hr_media); ?>feature-purchase-768x602.webp 768w, <?php echo esc_url($hr_media); ?>feature-purchase-1024x803.webp 1024w, <?php echo esc_url($hr_media); ?>feature-purchase.webp 1300w" sizes="(max-width: 960px) calc(100vw - 32px), 600px" width="650" height="509" loading="lazy" decoding="async"
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
          <img src="<?php echo esc_url($hr_media); ?>feature-gst.webp" srcset="<?php echo esc_url($hr_media); ?>feature-gst-768x619.webp 768w, <?php echo esc_url($hr_media); ?>feature-gst-1024x825.webp 1024w, <?php echo esc_url($hr_media); ?>feature-gst.webp 1300w" sizes="(max-width: 960px) calc(100vw - 32px), 600px" width="650" height="524" loading="lazy" decoding="async"
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
          <img src="<?php echo esc_url($hr_media); ?>feature-staff.webp" srcset="<?php echo esc_url($hr_media); ?>feature-staff-768x567.webp 768w, <?php echo esc_url($hr_media); ?>feature-staff-1024x756.webp 1024w, <?php echo esc_url($hr_media); ?>feature-staff.webp 1300w" sizes="(max-width: 960px) calc(100vw - 32px), 600px" width="650" height="480" loading="lazy" decoding="async"
               alt="Staff roles in Healthray pharmacy software: permissions for Counter Staff with View Profit and Edit Discount turned off, the activity log, and the owner's WhatsApp with the nightly collection report">
        </div>
      </div>
    </div>

    <div class="also-wrap">
      <h3>Also included in the pharmacy software</h3>
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

<!-- ============ HOW TO CHOOSE (buying guide) ============ -->
<section class="problems" id="choose">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Buying guide</span><h2>How to choose the best pharmacy management software</h2><p class="lead">Check these 6 things before you pick software for your medical store.</p></div>
    <ul class="pain-list choose-list">
      <?php foreach ($hr_choose as $point) : ?>
      <li><?php echo $hr_circle; ?><span><b><?php echo esc_html($point['title']); ?></b> <?php echo esc_html($point['text']); ?></span></li>
      <?php endforeach; ?>
    </ul>
    <div class="tour-cta">
      <p>Healthray does all 6. Check it yourself on the free plan.</p>
      <a class="btn btn-cta js-start" href="#hr-pmo-start" data-cta="choose_start">Start free</a>
    </div>
  </div>
</section>

<!-- ============ INTERNET QUESTION ============ -->
<section class="internet" id="internet">
  <div class="wrap internet-grid">
    <div>
      <span class="eyebrow">Every chemist asks this</span>
      <h2>“What if the internet goes down?”</h2>
      <p style="margin:0">Healthray is online pharmacy software, so you need internet. Here is why medical stores are fine with it:</p>
    </div>
    <ul class="internet-points">
      <li><strong>Works on any device</strong>Use it on your counter computer, laptop or mobile. If Wi-Fi stops, use your phone's hotspot.</li>
      <li><strong>Your data is safe</strong>Data is saved online, so a broken computer or a virus can't delete it.</li>
      <li><strong>Help any time</strong>Our team is available 24×7.</li>
    </ul>
  </div>
</section>

<!-- ============ HOW TO START ============ -->
<section>
  <div class="wrap">
    <div class="sec-head"><h2>Start in 3 easy steps</h2></div>
    <div class="steps">
      <div class="step"><h3>Start free</h3><p>Fill a short form. No card needed.</p></div>
      <div class="step"><h3>We move your old data free</h3><p>Medicines, stock, suppliers and customers from your old software or Excel.</p></div>
      <div class="step"><h3>Start billing</h3><p>Our team sets up your printer and bill format. Then start billing. Help is there 24×7.</p></div>
    </div>
  </div>
</section>

<!-- ============ TESTIMONIALS ============ -->
<section class="testi">
  <div class="wrap">
    <div class="sec-head"><h2>What chemists say</h2></div>
    <div class="testi-grid">
      <?php foreach ($hr_testimonials as $t) : ?>
      <figure class="testi-card">
        <blockquote><?php echo esc_html($t['quote']); ?></blockquote>
        <figcaption class="who"><span class="avatar"><img src="<?php echo esc_url($hr_logo_base . $t['logo']); ?>" alt="<?php echo esc_attr($t['name'] . ' logo'); ?>" width="44" height="44" loading="lazy"></span><span><b><?php echo esc_html($t['name']); ?></b><small>Healthray Pharmacy customer</small></span></figcaption>
      </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ PRICING ============ -->
<section id="pricing">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Pricing</span><h2>Pharmacy software price: simple plans for every medical store</h2><p class="lead">Start free. Upgrade only when you need more bills or users.</p></div>
    <!-- Monthly price is the yearly price ÷ 12. The yearly amount and GST stay right under it. -->
    <div class="pricing-grid">
      <div class="plan">
        <h3>Free</h3>
        <div class="price">₹0 <small>/month</small></div>
        <div class="yearly">Free forever. No card needed.</div>
        <ul><li>10 bills a month</li><li>1 user</li><li>Sales &amp; purchase</li><li>Basic reports</li></ul>
        <a class="btn btn-cta js-start" href="#hr-pmo-start" data-cta="pricing_free">Start free</a>
      </div>
      <div class="plan">
        <h3>Basic</h3>
        <div class="price">₹417 <small>/month</small></div>
        <div class="yearly">Paid yearly: ₹4,999 + 18% GST<br>(₹5,899 a year)</div>
        <ul><li>Unlimited bills</li><li>3 users</li><li>GST report &amp; vouchers</li><li>Label print &amp; CSV import</li><li>Staff roles</li></ul>
        <a class="btn btn-outline js-start" href="#hr-pmo-start" data-cta="pricing_basic">Start free &amp; upgrade</a>
      </div>
      <div class="plan popular">
        <span class="badge">Popular</span>
        <h3>Advance</h3>
        <div class="price">₹833 <small>/month</small></div>
        <div class="yearly">Paid yearly: ₹9,999 + 18% GST<br>(₹11,799 a year)</div>
        <ul><li>Everything in Basic</li><li>10 users</li><li>Purchase orders &amp; reorder</li><li>Discount approval &amp; bill lock</li><li>Activity report</li><li>Expense tracking</li></ul>
        <a class="btn btn-cta js-start" href="#hr-pmo-start" data-cta="pricing_advance">Start free &amp; upgrade</a>
      </div>
    </div>

    <div class="ent">
      <div>
        <h3>Have more than one medical store?</h3>
        <p>Ask about the Enterprise plan: unlimited users and bills, manage all your stores from one place, Tally export and faster support.</p>
      </div>
      <a class="btn btn-outline js-demo" href="#hr-pmo-start" data-cta="pricing_enterprise">Book a free demo</a>
    </div>

    <div class="pricing-foot">
      <p>We move your old data free, and help is there 24×7. 3-year and 5-year plans also available.</p>
    </div>
  </div>
</section>

<!-- ============ FAQ ============ -->
<section class="faq-sec" id="faq">
  <div class="wrap">
    <div class="sec-head"><h2>Questions chemists ask us</h2></div>
    <div class="faq">
      <?php foreach ($hr_faq as $i => $item) : ?>
      <details name="hr-pharmacy-faq"<?php echo 0 === $i ? ' open' : ''; ?>><summary><?php echo esc_html($item['q']); ?></summary><p><?php echo esc_html($item['a']); ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="final">
  <div class="wrap">
    <div class="final-card">
      <div class="hr-h2">Try free pharmacy software in your medical store</div>
      <p>10 free bills a month, forever. No card. We'll move your old data free.</p>
      <div class="btns">
        <a class="btn btn-cta js-start" href="#hr-pmo-start" data-cta="final_start">Start free</a>
      </div>
    </div>
  </div>
</section>

<!-- Sticky "Start free" bar on phones, shown once the hero form is off screen. -->
<div class="sticky" id="hr-pmo-sticky" aria-hidden="true">
  <a class="btn btn-cta js-start" href="#hr-pmo-start" data-cta="sticky_start" tabindex="-1">Start free</a>
</div>

<!-- The one popup. Every .js-start / .js-demo button opens it with the same CF7 form. -->
<dialog id="hr-pharmacy-modal" aria-labelledby="hr-pharmacy-modal-title" aria-describedby="hr-pharmacy-modal-hint">
  <button type="button" class="hr-pharmacy-modal-close" aria-label="Close form">×</button>
  <div class="hr-h2" id="hr-pharmacy-modal-title">Start free today</div>
  <p class="hint" id="hr-pharmacy-modal-hint">Fill in your details. Our team will help you get started.</p>
  <div id="hr-pharmacy-modal-slot"></div>
</dialog>

<script id="hr-pmo-js">
(function () {
  'use strict';

  function init() {
    var root = document.getElementById('hr-pmo');
    if (!root) return;

    var params = new URLSearchParams(location.search);
    var reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;
    window.dataLayer = window.dataLayer || [];

    function track(event, data) {
      window.dataLayer.push(Object.assign({event: event, segment: 'retail', channel: 'organic', variant: ''}, data || {}));
    }

    /* ===== Popup: every .js-start / .js-demo button opens it; the one CF7 form moves in and back out ===== */
    var formHost = document.getElementById('hr-pharmacy-cf7');
    var formHome = document.getElementById('hr-pharmacy-form-home');
    var dialog = document.getElementById('hr-pharmacy-modal');
    var modalSlot = document.getElementById('hr-pharmacy-modal-slot');
    var modalTitle = document.getElementById('hr-pharmacy-modal-title');
    var modalHint = document.getElementById('hr-pharmacy-modal-hint');
    var heroForm = document.getElementById('hr-pmo-start');
    var startTitle = modalTitle.textContent;
    var startHint = modalHint.textContent;
    var previousFocus = null;
    var activeMode = 'start_free', activeLocation = 'hero';

    // Fill the CF7 hidden fields (cta_type, cta_location, utm_*, gclid ...) that exist in the form.
    function setTracking(mode, loc) {
      activeMode = mode;
      activeLocation = loc || '';
      var values = {variant: '', cta_type: mode, cta_location: loc || ''};
      ['gclid', 'fbclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (k) {
        var val = params.get(k);
        if (val) values[k] = val.slice(0, 200);
      });
      formHost.querySelectorAll('input[type="hidden"]').forEach(function (input) {
        var key = input.name.replace(/_cf7$/, '');
        if (Object.prototype.hasOwnProperty.call(values, key)) input.value = values[key];
      });
    }
    setTracking('start_free', 'hero');

    function openForm(e, mode, loc) {
      e.preventDefault();
      previousFocus = e.currentTarget;
      var demo = mode === 'demo';
      modalTitle.textContent = demo ? 'Book a free demo' : startTitle;
      modalHint.textContent = demo ? "Fill in your details. We'll call to arrange your free demo." : startHint;
      setTracking(mode, loc);
      track('cta_click', {cta_type: mode, cta_location: loc});
      if (typeof dialog.showModal !== 'function') {
        heroForm.scrollIntoView({behavior: reduceMotion ? 'auto' : 'smooth'});
        return;
      }
      modalSlot.appendChild(formHost);
      dialog.showModal();
      document.documentElement.classList.add('hr-pharmacy-modal-open');
      focusFormStep();
    }
    root.querySelectorAll('.js-start,.js-demo').forEach(function (el) {
      el.setAttribute('aria-haspopup', 'dialog');
      el.setAttribute('aria-controls', 'hr-pharmacy-modal');
      el.addEventListener('click', function (e) {
        openForm(e, el.classList.contains('js-demo') ? 'demo' : 'start_free', el.dataset.cta);
      });
    });
    dialog.querySelector('.hr-pharmacy-modal-close').addEventListener('click', function () { dialog.close(); });
    dialog.addEventListener('click', function (e) {
      if (e.target !== dialog) return;
      var r = dialog.getBoundingClientRect();
      if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dialog.close();
    });
    dialog.addEventListener('close', function () {
      formHome.appendChild(formHost);
      setTracking('start_free', 'hero');
      document.documentElement.classList.remove('hr-pharmacy-modal-open');
      if (previousFocus) previousFocus.focus({preventScroll: true});
    });

    var started = false;
    formHost.addEventListener('input', function () {
      if (!started) { started = true; track('form_start'); }
    });
    formHost.addEventListener('wpcf7mailsent', function () { track('form_success', {form: 'pharmacy_ppc'}); });

    /* ===== Two-step CF7 form: billing choice (buttons -> hidden bed_size), then contact details ===== */
    var cf7Form = formHost.querySelector('form');
    var stepOne = formHost.querySelector('.hr-fstep-1');
    var stepTwo = formHost.querySelector('.hr-fstep-2');
    var currentStep = 1;

    function focusFormStep() {
      var scope = stepOne && stepTwo ? (currentStep === 1 ? stepOne : stepTwo) : formHost;
      var first = scope.querySelector('input:not([type="hidden"]):not([type="submit"]):not([disabled]),select,textarea') || scope.querySelector('button');
      if (first) first.focus({preventScroll: true});
    }

    if (cf7Form && stepOne && stepTwo) {
      var billInput = cf7Form.querySelector('input[name="bed_size"]');
      var billButtons = stepOne.querySelectorAll('.hr-bill-option');
      var next = stepOne.querySelector('.hr-next');
      var back = stepTwo.querySelector('.hr-back');
      var softwareError = stepOne.querySelector('.hr-software-err');
      var softwareBox = stepOne.querySelector('.hr-software');
      var mobileField = cf7Form.querySelector('input[name="your-number"]:not([type="hidden"])') || cf7Form.querySelector('input[name="mobile-local"]');
      var mobileError = stepTwo.querySelector('.hr-mobile-err');
      var stepLabel = formHost.querySelector('.hr-fsteps-label');
      var pickedLabel = stepTwo.querySelector('.hr-picked-label');
      var MOBILE_MSG = 'Please enter a 10-digit mobile number.';

      function normalizeMobile(value) {
        var digits = value.replace(/\D/g, '');
        if (digits.length === 12 && digits.slice(0, 2) === '91') return digits.slice(2);
        if (digits.length === 11 && digits.charAt(0) === '0') return digits.slice(1);
        return digits.slice(0, 10);
      }
      // Send the number as "+91 XXXXXXXXXX".
      cf7Form.addEventListener('formdata', function (event) {
        var digits = mobileField ? normalizeMobile(mobileField.value) : '';
        event.formData.set('your-number', /^\d{10}$/.test(digits) ? '+91 ' + digits : '');
      });

      // Announce step changes to screen readers.
      var status = document.createElement('span');
      status.setAttribute('role', 'status');
      status.setAttribute('aria-live', 'polite');
      status.style.cssText = 'position:absolute;width:1px;height:1px;padding:0;overflow:hidden;clip-path:inset(50%);white-space:nowrap';
      formHost.appendChild(status);

      function chosenSoftware() { return billInput && billInput.value ? billInput : null; }
      function selectBill(btn) {
        billButtons.forEach(function (b) {
          var on = b === btn;
          b.classList.toggle('is-selected', on);
          b.setAttribute('aria-pressed', on ? 'true' : 'false');
        });
        billInput.value = btn.dataset.bed;
        if (softwareError) softwareError.hidden = true;
        if (softwareBox) softwareBox.classList.remove('invalid');
      }
      function clearBill() {
        if (billInput) billInput.value = '';
        billButtons.forEach(function (b) {
          b.classList.remove('is-selected');
          b.setAttribute('aria-pressed', 'false');
        });
      }
      billButtons.forEach(function (btn) {
        btn.addEventListener('click', function () { selectBill(btn); });
      });

      function showStep(step, focus) {
        currentStep = step;
        formHost.dataset.step = String(step);
        stepOne.hidden = step !== 1;
        stepTwo.hidden = step !== 2;
        var text = 'Step ' + step + ' of 2';
        if (stepLabel) stepLabel.textContent = text;
        status.textContent = text;
        var chosen = chosenSoftware();
        if (pickedLabel) pickedLabel.textContent = chosen ? chosen.value : '';
        if (focus) focusFormStep();
      }
      function goNext() {
        var chosen = chosenSoftware();
        if (softwareError) softwareError.hidden = !!chosen;
        if (softwareBox) softwareBox.classList.toggle('invalid', !chosen);
        if (!chosen) {
          if (billButtons[0]) billButtons[0].focus();
          status.textContent = 'Please pick one.';
          return false;
        }
        showStep(2, true);
        return true;
      }
      if (next) next.addEventListener('click', goNext);
      if (back) back.addEventListener('click', function () { showStep(1, true); });

      function validMobile() {
        var valid = !!mobileField && /^\d{10}$/.test(mobileField.value);
        if (mobileError) { mobileError.hidden = valid; mobileError.textContent = MOBILE_MSG; }
        if (mobileField) {
          mobileField.setCustomValidity(valid ? '' : MOBILE_MSG);
          mobileField.setAttribute('aria-invalid', valid ? 'false' : 'true');
        }
        return valid;
      }

      var nameField = cf7Form.querySelector('input[name="your-name"]');
      if (nameField && !nameField.getAttribute('autocomplete')) nameField.setAttribute('autocomplete', 'name');
      if (mobileField) {
        if (!mobileField.getAttribute('autocomplete')) mobileField.setAttribute('autocomplete', 'tel-national');
        mobileField.setAttribute('inputmode', 'numeric');
        mobileField.setAttribute('pattern', '[0-9]{10}');
        mobileField.removeAttribute('maxlength');
        mobileField.value = normalizeMobile(mobileField.value);
        if (mobileError) {
          mobileError.id = 'hr-pharmacy-mobile-error';
          mobileError.textContent = MOBILE_MSG;
          var describedBy = (mobileField.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
          if (describedBy.indexOf(mobileError.id) === -1) describedBy.push(mobileError.id);
          mobileField.setAttribute('aria-describedby', describedBy.join(' '));
        }
        mobileField.addEventListener('input', function (event) {
          mobileField.value = event.inputType === 'insertText' ? mobileField.value.replace(/\D/g, '').slice(0, 10) : normalizeMobile(mobileField.value);
          if (mobileError && !mobileError.hidden) {
            validMobile();
          } else {
            mobileField.setCustomValidity('');
            mobileField.setAttribute('aria-invalid', 'false');
          }
        });
      }

      // Runs before CF7's own submit handler (capture phase); CF7 still does the sending.
      cf7Form.addEventListener('submit', function (e) {
        if (currentStep === 1 || !chosenSoftware()) {
          e.preventDefault();
          e.stopImmediatePropagation();
          if (currentStep !== 1) showStep(1, false);
          goNext();
          return;
        }
        if (!validMobile()) {
          e.preventDefault();
          e.stopImmediatePropagation();
          if (mobileField) mobileField.focus();
        }
      }, true);

      cf7Form.addEventListener('reset', function () {
        setTimeout(function () {
          clearBill();
          if (softwareError) softwareError.hidden = true;
          if (softwareBox) softwareBox.classList.remove('invalid');
          if (mobileError) mobileError.hidden = true;
          if (mobileField) {
            mobileField.setCustomValidity('');
            mobileField.removeAttribute('aria-invalid');
          }
          showStep(1, false);
          setTracking(activeMode, activeLocation);
        }, 0);
      });

      formHost.addEventListener('wpcf7invalid', function (e) {
        var invalid = (e.detail && e.detail.apiResponse && e.detail.apiResponse.invalid_fields) || [];
        var softwareInvalid = invalid.some(function (field) { return field.field === 'bed_size'; });
        showStep(softwareInvalid ? 1 : 2, true);
      });

      showStep(1, false);
    }

    /* ===== FAQ: only one open at a time (fallback for browsers without <details name>) ===== */
    var faqItems = root.querySelectorAll('#faq .faq details');
    faqItems.forEach(function (item) {
      item.addEventListener('toggle', function () {
        if (!item.open) return;
        faqItems.forEach(function (other) { if (other !== item) other.open = false; });
      });
    });

    /* ===== Sticky mobile bar + tour video play/pause on scroll ===== */
    var sticky = document.getElementById('hr-pmo-sticky');
    var tour = document.getElementById('hr-pmo-tour-video');
    var stickyShown = false, ticking = false, tourStarted = false, tourDone = false;

    function onScroll() {
      ticking = false;
      var r = heroForm.getBoundingClientRect();
      var show = r.bottom < 0 || r.top > innerHeight;
      if (show !== stickyShown) {
        stickyShown = show;
        sticky.classList.toggle('show', show);
        sticky.setAttribute('aria-hidden', show ? 'false' : 'true');
        sticky.querySelectorAll('a').forEach(function (a) { a.tabIndex = show ? 0 : -1; });
      }
      if (tour) {
        var t = tour.getBoundingClientRect();
        var visible = Math.min(t.bottom, innerHeight) - Math.max(t.top, 0);
        if (visible > t.height * 0.35) {
          if (tour.paused) tour.play().catch(function () {});
        } else if (!tour.paused) {
          tour.pause();
        }
      }
    }
    addEventListener('scroll', function () {
      if (!ticking) { ticking = true; requestAnimationFrame(onScroll); }
    }, {passive: true});
    addEventListener('resize', onScroll);
    onScroll();

    if (tour) {
      tour.addEventListener('play', function () {
        if (!tourStarted) { tourStarted = true; track('video_start', {video: 'product_tour'}); }
      });
      tour.addEventListener('timeupdate', function () {
        if (!tourDone && tour.duration && tour.currentTime > tour.duration - 1) {
          tourDone = true;
          track('video_complete', {video: 'product_tour'});
        }
      });
    }
  }

  // Wait for the whole page (CF7's script loads in the footer, after this).
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
</script>
</main>