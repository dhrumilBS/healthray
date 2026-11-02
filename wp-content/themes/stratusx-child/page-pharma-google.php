<?php

/**
 * Template Name: Pharmacy Billing Software
 * Template Post Type: page
 * Description: Standalone Healthray pharmacy landing page with original inline CSS and JavaScript.
 *
 * Install in the active child theme. This template supplies its own header/footer.
 */
defined('ABSPATH') || exit;

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
</head>
<body <?php body_class('hr-pharmacy-landing'); ?>>
<?php wp_body_open(); ?>
<style>
:root {--navy:#1A5692; --navy-dark:#123E6B; --sky:#E3F2FD; --sky-2:#F4F9FE; --green:#2E7D32; --red:#C62828; --text:#3E3D3D; --muted:#5F6670; --line:#D9E3EE; --cta:#C2410C; --cta-hover:#9A3412; --white:#fff; --ph:#B45309; --radius:12px; --shadow:0 10px 30px rgba(18,62,107,.12); --section:80px; --gap:56px;}
* {box-sizing:border-box}
html {scroll-behavior:smooth}
body {margin:0;background:var(--white);color:var(--text);font:400 16px/1.6 Mulish,system-ui,-apple-system,"Segoe UI",Roboto,Arial,sans-serif;-webkit-font-smoothing:antialiased}
img,svg,video {max-width:100%}
h1,h2,h3 {color:var(--navy-dark);line-height:1.2;margin:0 0 .5em;text-wrap:balance}
.sec-head p,.feature-copy p {text-wrap:pretty}
.hero .hero-grid h1 {font-size:clamp(1.9rem,4.6vw,2.9rem);font-weight:800;letter-spacing:-.01em}
h2 {font-size:clamp(1.5rem,3.2vw,2.1rem);font-weight:800;letter-spacing:-.005em}
h3 {font-size:clamp(1.2rem,2.2vw,1.45rem);font-weight:800}
p {margin:0 0 1em}
a {color:var(--navy)}
.wrap {max-width:1160px;margin:0 auto;padding:0 16px}
section {padding:var(--section) 0}
.center {text-align:center}
.sec-head {max-width:720px;margin:0 auto 40px;text-align:center}
.sec-head h2 {margin-bottom:.4em}
.sec-head p {margin:0}
.lead {font-size:1.125rem;color:var(--muted)}
.eyebrow {display:inline-block;font-size:.8rem;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--navy);margin-bottom:.6rem}
:focus-visible {outline:3px solid #F59E0B;outline-offset:2px}
/* Review-only markers: remove .review-note and .ph styling before launch */
.review-note {background:#FEF3C7;color:#78350F;font-size:.85rem;text-align:center;padding:6px 16px}
.ph {outline:2px dashed var(--ph);outline-offset:2px;background:#FFFBEB;color:var(--ph);border-radius:4px;padding:0 4px}
/* Buttons */
.btn {display:inline-flex;align-items:center;justify-content:center;gap:.5rem;min-height:48px;padding:12px 24px;border-radius:10px;border:2px solid transparent;font-family:inherit;font-weight:800;font-size:1.05rem;line-height:1.2;text-decoration:none;cursor:pointer;transition:background .15s,border-color .15s}
.btn-cta {background:var(--cta);color:#fff}
.btn-cta:hover {background:var(--cta-hover);color:#fff}
.btn-outline {background:#fff;color:var(--navy);border-color:var(--navy)}
.btn-outline:hover {background:var(--sky-2)}
.link-demo {background:none;border:0;padding:0;font:inherit;font-weight:700;color:var(--navy);text-decoration:underline;cursor:pointer}
/* Header */
.site-header {border-bottom:1px solid var(--line);background:#fff}
.site-header .wrap {display:flex;align-items:center;justify-content:space-between;height:64px}
.logo {display:flex;align-items:center;gap:12px}
.logo img {display:block;height:54px;width:auto}
@media (max-width:720px) {
  .logo img {height:46px}
}
.logo-sub {padding-left:12px;border-left:1px solid var(--line);font-size:.8rem;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)}
.header-actions {display:flex;gap:12px;align-items:center}
.header-actions .btn {min-height:40px;padding:8px 14px;font-size:.95rem}
/* Hero */
.hero {background:linear-gradient(180deg,var(--sky-2),#fff);padding:56px 0 48px}
.hero-grid {display:grid;grid-template-columns:1.15fr .85fr;gap:var(--gap);align-items:start}
.hero-grid>* {min-width:0}
.hero .sub {font-size:1.15rem;color:var(--muted);max-width:36em;margin-bottom:1.25em}
.free-line {display:flex;gap:8px;align-items:flex-start;background:#fff;border:1px solid var(--line);border-left:4px solid var(--green);border-radius:8px;padding:10px 14px;font-weight:700;color:var(--text);max-width:36em;margin:0}
.free-line svg {flex:none;width:20px;height:20px;margin-top:2px;fill:var(--green)}
.trust {list-style:none;padding:0;margin:20px 0 0;display:flex;flex-wrap:wrap;gap:8px 20px;font-weight:700;color:var(--navy-dark);font-size:.95rem}
.trust li {display:flex;align-items:flex-start;gap:6px}
.trust svg {flex:none;width:18px;height:18px;margin-top:2px;fill:var(--green)}
/* Form */
.form-card {background:#fff;border:1px solid var(--line);border-radius:16px;box-shadow:var(--shadow);padding:24px}
.form-card h2 {font-size:1.4rem;margin-bottom:4px}
.form-card .hint {color:var(--muted);font-size:.95rem;margin-bottom:16px}
.field {margin-bottom:16px}
.field label,.field legend {display:block;font-weight:700;margin-bottom:6px;font-size:.95rem;color:var(--text)}
.field legend {padding:0}
.field input[type=text],.field input[type=tel] {width:100%;height:48px;border:1.5px solid #B8C6D6;border-radius:8px;padding:0 12px;font:inherit;color:var(--text);background:#fff}
.field input:focus {border-color:var(--navy);outline:none;box-shadow:0 0 0 3px rgba(26,86,146,.18)}
.tel-wrap {display:flex}
.tel-wrap span {display:flex;align-items:center;padding:0 12px;border:1.5px solid #B8C6D6;border-right:0;border-radius:8px 0 0 8px;background:var(--sky-2);font-weight:700}
.tel-wrap input {border-radius:0 8px 8px 0!important}
fieldset {border:0;margin:0;padding:0}
.chips {display:flex;flex-wrap:wrap;gap:8px}
.chips input {position:absolute;opacity:0;pointer-events:none}
.chips label {display:inline-flex;align-items:center;min-height:44px;margin:0;padding:8px 14px;border:1.5px solid #B8C6D6;border-radius:999px;font-weight:600;cursor:pointer;background:#fff}
.chips input:checked+label {background:var(--navy);border-color:var(--navy);color:#fff}
.chips input:focus-visible+label {outline:3px solid #F59E0B;outline-offset:2px}
.err {display:none;color:var(--red);font-size:.88rem;font-weight:700;margin-top:4px}
.field.invalid .err {display:block}
.field.invalid input[type=text],.field.invalid input[type=tel] {border-color:var(--red)}
.form-card .btn {width:100%}
.consent {font-size:.8rem;color:var(--muted);margin:12px 0 0}
.optin {display:flex;gap:8px;align-items:flex-start;font-size:.85rem;margin-top:10px;color:var(--text)}
.optin input {width:18px;height:18px;margin-top:2px;flex:none}
/* 2-step form */
.fsteps {display:flex;align-items:center;gap:12px;margin:0 0 16px}
.fsteps-label {font-size:.8rem;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--navy);white-space:nowrap}
.fsteps-bar {flex:1;display:flex;gap:6px}
.fsteps-bar i {flex:1;height:5px;border-radius:3px;background:var(--line);transition:background .25s}
.fsteps-bar i.on {background:var(--green)}
.options {display:grid;grid-template-columns:1fr 1fr;gap:10px}
.options input {position:absolute;opacity:0;pointer-events:none}
.options label {position:relative;display:flex;flex-direction:column;justify-content:center;min-height:68px;margin:0;padding:10px 12px 10px 40px;border:1.5px solid #B8C6D6;border-radius:10px;background:#fff;cursor:pointer;line-height:1.3;transition:border-color .15s,background .15s}
.options label::before {content:"";position:absolute;left:13px;top:50%;width:18px;height:18px;margin-top:-9px;border:2px solid #9AACBF;border-radius:50%;background:#fff;box-sizing:border-box}
.options label b {font-weight:800;color:var(--navy-dark);font-size:.95rem}
.options label small {font-size:.8rem;color:var(--muted);font-weight:600}
.options label:hover {border-color:var(--navy)}
.options input:checked+label {border-color:var(--navy);background:var(--sky-2);box-shadow:inset 0 0 0 1px var(--navy)}
.options input:checked+label::before {border:5px solid var(--navy)}
.options input:focus-visible+label {outline:3px solid #F59E0B;outline-offset:2px}
#f-software.invalid .options label {border-color:var(--red)}
.step-note {text-align:center;font-size:.85rem;font-weight:700;color:var(--green);margin:10px 0 0}
.picked {display:flex;justify-content:space-between;align-items:center;gap:8px;background:var(--sky-2);border:1px solid var(--line);border-radius:8px;padding:8px 12px;font-size:.9rem;margin:0 0 16px}
.picked .link-demo {font-size:.9rem}
.fstep.enter {animation:stepIn .25s ease-out}
@keyframes stepIn {
  from {opacity:0;transform:translateX(12px)}
  to {opacity:1;transform:none}
}
@media (prefers-reduced-motion:reduce) {
  .fstep.enter {animation:none}
}
/* Phones: one option per row, so "New medical store" and its note never break into 3 lines. */
@media (max-width:480px) {
  .options {grid-template-columns:1fr;gap:8px}
  .options label {min-height:58px}
}
/* Product tour video */
.tour {padding:16px 0 var(--section)}
.tour-frame {max-width:1040px;margin:0 auto;border-radius:16px;overflow:hidden;background:#0F2236; box-shadow:0 2px 0 rgba(18,62,107,.04),0 24px 60px rgba(18,62,107,.18);border:1px solid rgba(18,62,107,.10)}
.tour-frame video {display:block;width:100%;height:auto;aspect-ratio:1280/682;background:#0F2236}
.tour-cta {display:flex;flex-wrap:wrap;align-items:center;justify-content:center;gap:12px 20px;margin-top:28px}
.tour-cta p {margin:0;color:var(--muted);font-weight:600}
/* Proof strip */
.proof {padding:44px 0 36px;border-top:1px solid var(--line);border-bottom:1px solid var(--line);background:#fff}
.logos-title {margin:0 0 20px;text-align:center;font-weight:800;font-size:1.05rem;color:var(--muted)}
.logo-marquee {overflow:hidden;-webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent);mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}
.logo-track {display:flex;width:max-content;animation:logo-scroll 28s linear infinite}
.logo-marquee:hover .logo-track {animation-play-state:paused}
.logo-set {display:flex;align-items:center;gap:56px;padding-right:56px}
.logo-set img {display:block;width:80px;height:80px;object-fit:contain}
@keyframes logo-scroll {
  to {transform:translateX(-50%)}
}
@media (prefers-reduced-motion:reduce) {
  .logo-marquee {-webkit-mask-image:none;mask-image:none}
  .logo-track {animation:none;width:auto;justify-content:center}
  .logo-set {flex-wrap:wrap;justify-content:center;gap:16px 40px;padding:0}
  .logo-set[aria-hidden] {display:none}
}
.proof-grid {display:grid;grid-template-columns:repeat(4,1fr);gap:16px;text-align:center;margin-top:32px;padding-top:28px;border-top:1px solid #EEF2F6}
.proof-grid strong {display:block;font-size:1.6rem;color:var(--navy-dark);font-weight:800;line-height:1.2}
.proof-grid span {font-size:.92rem;color:var(--muted);font-weight:600}
/* Problems */
.problems {background:var(--sky-2)}
.pain-list {list-style:none;padding:0;margin:0 auto;max-width:760px;display:grid;gap:12px}
.pain-list li {display:flex;gap:12px;align-items:flex-start;background:#fff;border:1px solid var(--line);border-radius:10px;padding:14px 16px;font-weight:600;text-align:left}
.pain-list svg {flex:none;width:22px;height:22px;fill:var(--red);margin-top:1px}
/* Feature blocks */
/* Zig-zag features: text and screenshot side by side (screenshot ~600px), switching sides each row. */
.features {display:grid;gap:96px; background:none;}
.feature {display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.25fr);gap:56px;align-items:center}
.feature:nth-child(even) {grid-template-columns:minmax(0,1.25fr) minmax(0,1fr)}
.feature:nth-child(even) .feature-head {order:2}
.feature-head {display:flex;flex-direction:column;gap:20px}
.feature-copy h3 {margin-bottom:.4em}
.feature-copy p {color:var(--muted);font-size:1.05rem;margin:0}
.feature-points .ticks {margin:0}
.feature-points .plan-note {margin-top:14px}
.feature-visual img {display:block;width:100%;height:auto}
/* The screenshots carry no shadow of their own: this one follows the outline of each window and phone. */
.feature-visual img {filter:drop-shadow(0 12px 22px rgba(18,52,90,.13)) drop-shadow(0 2px 4px rgba(18,52,90,.06))}
.plan-note {display:inline-block;font-size:.85rem;font-weight:700;color:var(--navy);background:var(--sky);border-radius:6px;padding:3px 10px}
.ticks {list-style:none;padding:0;margin:0 0 1.1em}
.ticks li {position:relative;padding-left:30px;margin-bottom:8px;font-weight:600}
.ticks li::before {content:"";position:absolute;left:0;top:4px;width:18px;height:18px;border-radius:50%;background:var(--green) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='white' d='M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z'/%3E%3C/svg%3E") center/14px no-repeat}
.also-wrap {margin-top:88px;padding-top:48px;border-top:1px solid var(--line)}
.also-wrap h3 {text-align:center;margin-bottom:20px}
.also {display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.also div {display:flex;align-items:center;justify-content:center;background:var(--sky-2);border:1px solid var(--line);border-radius:10px;padding:14px 12px;font-weight:700;font-size:.95rem;line-height:1.35;text-align:center}
/* Internet */
.internet {background:var(--navy-dark);color:#E8F1FB}
.internet h2 {color:#fff}
.internet-grid {display:grid;grid-template-columns:1fr 1fr;gap:var(--gap);align-items:center}
.internet-grid>* {min-width:0}
.internet-points {list-style:none;padding:0;margin:0;display:grid;gap:12px}
.internet-points li {background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.15);border-radius:10px;padding:14px 16px}
.internet-points strong {color:#fff;display:block}
/* Steps */
.steps {display:grid;grid-template-columns:repeat(3,1fr);gap:20px;counter-reset:s}
.step {background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:24px;position:relative}
.step::before {counter-increment:s;content:counter(s);display:flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:50%;background:var(--navy);color:#fff;font-weight:800;margin-bottom:12px}
.step h3 {font-size:1.15rem}
.step p {color:var(--muted);margin:0}
/* Testimonials */
.testi {background:var(--sky-2)}
.testi-grid {display:grid;grid-template-columns:repeat(3,1fr);gap:20px}
.testi-card {position:relative;display:flex;flex-direction:column;background:#fff;border:1px solid var(--line);border-radius:var(--radius);padding:24px;box-shadow:0 1px 0 rgba(18,62,107,.03)}
.testi-card blockquote {margin:0 0 20px;flex:1;font-size:1.02rem;line-height:1.6;color:var(--text)}
.testi-card blockquote::before {content:"“";display:block;font-size:2.6rem;line-height:.8;color:var(--navy);opacity:.35;margin-bottom:6px}
.testi-card .who {display:flex;gap:12px;align-items:center;padding-top:16px;border-top:1px solid #EEF2F6}
.testi-card .who b {color:var(--navy-dark)}
.avatar {width:44px;height:44px;border-radius:50%;background:var(--navy);color:#fff;font-weight:800;font-size:.95rem;display:flex;align-items:center;justify-content:center;flex:none}
.testi-card small {display:block;color:var(--muted)}
/* Review-only: dummy testimonials are tagged so nobody publishes them by mistake */
/* Pricing */
.pricing-grid {display:grid;grid-template-columns:repeat(3,1fr);gap:20px;align-items:stretch;max-width:980px;margin:0 auto}
.yearly {font-size:.88rem;color:var(--muted);line-height:1.45;margin:6px 0 14px;min-height:2.9em}
.ent {max-width:980px;margin:24px auto 0;display:flex;align-items:center;justify-content:space-between;gap:16px 32px;background:var(--sky-2);border:1px solid var(--line);border-radius:14px;padding:22px 24px}
.ent h3 {font-size:1.15rem;margin:0 0 4px}
.ent p {margin:0;color:var(--muted);max-width:600px}
.ent .btn {flex:none}
.plan {border:1px solid var(--line);border-radius:14px;padding:22px 18px;display:flex;flex-direction:column;background:#fff}
.plan.popular {border:2px solid var(--navy);box-shadow:var(--shadow);position:relative}
.badge {position:absolute;top:-12px;left:50%;transform:translateX(-50%);background:var(--navy);color:#fff;font-size:.78rem;font-weight:800;padding:3px 10px;border-radius:999px;white-space:nowrap}
.plan h3 {margin-bottom:4px;font-size:1.2rem}
.price {font-size:1.7rem;font-weight:800;color:var(--navy-dark);line-height:1.2}
.price small {font-size:.9rem;font-weight:700;color:var(--muted)}
.plan ul {list-style:none;padding:0;margin:0 0 18px;flex:1}
.plan li {padding:6px 0 6px 24px;position:relative;font-size:.94rem;border-bottom:1px solid #F0F4F8}
.plan li::before {content:"✓";position:absolute;left:2px;color:var(--green);font-weight:800}
.plan .btn {width:100%;font-size:1rem}
.pricing-foot {margin-top:28px;text-align:center;color:var(--muted);font-size:.95rem}
.pricing-foot p {margin:0 0 4px}
/* FAQ */
.faq {max-width:820px;margin:0 auto}
.faq details {border:1px solid var(--line);border-radius:10px;margin-bottom:10px;background:#fff}
.faq summary {cursor:pointer;list-style:none;padding:16px 48px 16px 18px;font-weight:800;color:var(--navy-dark);position:relative}
.faq summary::-webkit-details-marker {display:none}
/* The +/− sits on the first line of the question, also when the question wraps. */
.faq summary::after {content:"+";position:absolute;right:16px;top:16px;width:24px;height:1.6rem;line-height:1.6rem;text-align:center;font-size:1.5rem;font-weight:700;color:var(--navy)}
.faq details[open] summary::after {content:"−"}
.faq details p {padding:0 18px 16px;margin:0;color:var(--muted)}
/* Final CTA */
.final {background:linear-gradient(135deg,var(--navy),var(--navy-dark));color:#E8F1FB;text-align:center}
.final h2 {color:#fff}
.final .btns {display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin:24px 0 0}
.final .ph {color:#FDE68A;background:transparent;outline-color:#FDE68A}
footer {padding:20px 0;font-size:.88rem;color:var(--muted);text-align:center}
footer nav {display:inline}
footer a {display:inline-block;padding:10px 8px;color:var(--muted);white-space:nowrap}
/* Sticky mobile bar */
.sticky {position:fixed;left:0;right:0;bottom:0;display:none;gap:8px;padding:10px 16px calc(10px + env(safe-area-inset-bottom));background:#fff;border-top:1px solid var(--line);box-shadow:0 -6px 20px rgba(0,0,0,.08);z-index:20;transform:translateY(110%);transition:transform .2s}
.sticky.show {transform:none}
.sticky .btn {flex:1;min-height:48px;padding:10px}
@media (max-width:960px) {
  :root {--section:64px;--gap:32px}
  .hero-grid,.internet-grid {grid-template-columns:1fr}
  .feature,.feature:nth-child(even) {grid-template-columns:1fr;gap:28px}
  .feature:nth-child(even) .feature-head {order:0}
  .feature-head {max-width:640px}
  .features {gap:72px}
  .pricing-grid {gap:14px}
  .proof-grid {grid-template-columns:repeat(2,1fr)}
  .also {grid-template-columns:repeat(2,1fr)}
}
@media (max-width:720px) {
  :root {--section:52px;--gap:24px}
  .hero {padding:28px 0 32px}
  .sec-head {margin-bottom:28px}
  .tour {padding-top:8px}
  .tour-frame {border-radius:12px}
  .features {gap:56px}
  .feature,.feature:nth-child(even) {gap:20px}
  .also-wrap {margin-top:56px;padding-top:36px}
  .steps,.testi-grid,.pricing-grid {grid-template-columns:1fr}
  .steps {gap:12px}
  /* Number beside the text instead of above it: the 3 steps fit on about one screen. */
  .step {display:grid;grid-template-columns:40px 1fr;gap:4px 14px;padding:18px 16px}
  .step::before {grid-row:1 / span 2;margin:0}
  .step h3 {margin:8px 0 0}
  .also div {padding:12px 10px}
  footer nav {display:block}
  .logo-set {gap:36px;padding-right:36px}
  .logo-set img {width:64px;height:64px}
  .logo-track {animation-duration:22s}
  .sticky {display:flex}
  .pricing-grid .plan.popular {margin-top:8px}
  .ent {flex-direction:column;align-items:flex-start}
  .ent .btn {width:100%}
  .yearly {min-height:0}
}
@media (prefers-reduced-motion:reduce) {
  html {scroll-behavior:auto}
  .sticky {transition:none}
}
/* CF7 hero and shared popup form. */
.hr-pharmacy-landing #hr-pharmacy-cf7 .wpcf7 {margin: 0}
.hr-pharmacy-landing #hr-pharmacy-cf7 .wpcf7-form>p {margin: 0 0 16px}
.hr-pharmacy-landing #hr-pharmacy-cf7 label {font-family: inherit}
.hr-pharmacy-landing #hr-pharmacy-cf7 .wpcf7-form-control-wrap {display: block}
.hr-pharmacy-landing #hr-pharmacy-cf7 :is(input[type="text"], input[type="email"], input[type="tel"], input[type="url"], input[type="number"], select, textarea) {width: 100%; min-height: 48px; border: 1.5px solid #B8C6D6; border-radius: 8px; padding: 10px 12px; font: inherit; color: var(--text); background: #fff}
.hr-pharmacy-landing #hr-pharmacy-cf7 textarea {min-height: 100px; resize: vertical}
.hr-pharmacy-landing #hr-pharmacy-cf7 :is(input[type="submit"], button[type="submit"]) {display: block; width: 100%; min-height: 48px; padding: 12px 24px; border: 0; border-radius: 10px; background: var(--cta); color: #fff; font: 800 1.05rem/1.2 Mulish, system-ui, sans-serif; cursor: pointer}
.hr-pharmacy-landing #hr-pharmacy-cf7 :is(input[type="submit"], button[type="submit"]):hover {background: var(--cta-hover)}
.hr-pharmacy-landing #hr-pharmacy-cf7 .wpcf7-not-valid-tip {font-size: .85rem}
.hr-pharmacy-landing #hr-pharmacy-cf7 .wpcf7-response-output {margin: 16px 0 0; padding: 10px; font-size: .9rem}
.hr-pharmacy-landing #hr-pharmacy-cf7 .wpcf7-acceptance label {display: flex; align-items: flex-start; gap: 8px}
.hr-pharmacy-landing #hr-pharmacy-cf7 .wpcf7-acceptance input {flex: none; margin-top: 5px}
/* Popup shell: override theme dialog/button defaults in both form steps. */
dialog#hr-pharmacy-modal {box-sizing: border-box !important; position: fixed !important; inset: 0 !important; width: calc(100% - 32px) !important; min-width: 0 !important; max-width: 540px !important; height: fit-content !important; min-height: 0 !important; max-height: calc(100vh - 40px) !important; max-height: calc(100dvh - 40px) !important; margin: auto !important; padding: 28px !important; border: 1px solid #D9E3EE !important; border-radius: 18px !important; background: #fff !important; color: #3E3D3D !important; box-shadow: 0 20px 70px rgba(10,30,50,.24) !important; overflow-x: hidden !important; overflow-y: auto !important; overscroll-behavior: contain; font: 400 16px/1.6 Mulish,system-ui,-apple-system,"Segoe UI",Arial,sans-serif !important; text-align: left !important; transform: none !important; float: none !important; outline: none}
dialog#hr-pharmacy-modal:not([open]) {display: none !important}
dialog#hr-pharmacy-modal[open] {display: block !important}
dialog#hr-pharmacy-modal::backdrop {background: rgba(10,30,50,.62) !important}
dialog#hr-pharmacy-modal button.hr-pharmacy-modal-close {box-sizing: border-box !important; position: absolute !important; top: 12px !important; right: 12px !important; bottom: auto !important; left: auto !important; display: flex !important; align-items: center !important; justify-content: center !important; width: 36px !important; min-width: 36px !important; max-width: 36px !important; height: 36px !important; min-height: 36px !important; margin: 0 !important; padding: 0 !important; border: 0 !important; border-radius: 50% !important; background: #F4F9FE !important; background-image: none !important; color: #123E6B !important; box-shadow: none !important; font: 400 26px/1 Arial,sans-serif !important; text-transform: none !important; letter-spacing: normal !important; cursor: pointer; z-index: 2; appearance: none}
dialog#hr-pharmacy-modal button.hr-pharmacy-modal-close:hover {background: #E3F2FD !important}
dialog#hr-pharmacy-modal button.hr-pharmacy-modal-close:focus-visible {outline: 2px solid #1A5692 !important; outline-offset: 2px}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-title {display: block !important; margin: 0 32px 8px 0 !important; padding: 0 !important; color: #123E6B !important; font: 800 24px/1.3 Mulish,system-ui,sans-serif !important; letter-spacing: normal !important; text-transform: none !important}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-hint {margin: 0 0 20px !important; padding: 0 !important; color: #5F6670 !important; font: 400 14px/1.65 Mulish,system-ui,sans-serif !important}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-slot {display: block; width: 100%; min-width: 0; margin: 0; padding: 0}
html.hr-pharmacy-modal-open, html.hr-pharmacy-modal-open body {overflow: hidden !important}
@media (max-width: 540px) {
  dialog#hr-pharmacy-modal {width: calc(100% - 24px) !important; max-height: calc(100vh - 24px) !important; max-height: calc(100dvh - 24px) !important; padding: 24px 20px !important; border-radius: 16px !important}
  dialog#hr-pharmacy-modal #hr-pharmacy-modal-title {font-size: 22px !important}
}
/* Two-step CF7 controls only; existing page styles are preserved. */
#hr-pharmacy-cf7 .hr-fstep[hidden], #hr-pharmacy-cf7 .hr-err[hidden] {display: none !important}
#hr-pharmacy-cf7 .hr-fsteps {margin: 0 0 20px}
#hr-pharmacy-cf7 .hr-fsteps-label {display: block; margin-bottom: 8px; font-size: .85rem; font-weight: 700; color: var(--muted)}
#hr-pharmacy-cf7 .hr-fsteps-bar {display: flex; gap: 6px}
#hr-pharmacy-cf7 .hr-fsteps-bar i {height: 4px; flex: 1; border-radius: 4px; background: var(--line)}
#hr-pharmacy-cf7 .hr-fsteps-bar i:first-child, #hr-pharmacy-cf7[data-step="2"] .hr-fsteps-bar i {background: var(--navy)}
#hr-pharmacy-cf7 .hr-field {margin-bottom: 16px}
#hr-pharmacy-cf7 .hr-field>label, #hr-pharmacy-cf7 .hr-field legend {display: block; margin-bottom: 6px; font-weight: 700; font-size: .95rem}
#hr-pharmacy-cf7 .hr-next {display: block; width: 100%; min-height: 48px; padding: 12px 24px; border: 0; border-radius: 10px; background: var(--cta); color: #fff; font: 800 1.05rem/1.2 Mulish, system-ui, sans-serif; cursor: pointer}
#hr-pharmacy-cf7 .hr-next:hover {background: var(--cta-hover)}
#hr-pharmacy-cf7 .hr-picked {display: flex; justify-content: space-between; align-items: center; gap: 8px; font-size: .9rem}
#hr-pharmacy-cf7 .hr-back {padding: 4px; border: 0; background: none; color: var(--navy); font: inherit; text-decoration: underline; cursor: pointer}
#hr-pharmacy-cf7 .hr-tel {display: flex}
#hr-pharmacy-cf7 .hr-tel>span[aria-hidden] {display: flex; align-items: center; padding: 0 12px; border: 1.5px solid #B8C6D6; border-right: 0; border-radius: 8px 0 0 8px; background: var(--sky-2); font-weight: 700}
#hr-pharmacy-cf7 .hr-tel>.wpcf7-form-control-wrap {flex: 1; min-width: 0}
#hr-pharmacy-cf7 .hr-tel input {border-radius: 0 8px 8px 0}
#hr-pharmacy-cf7 .hr-err {margin: 4px 0 0; color: var(--red); font-size: .88rem; font-weight: 700}
#hr-pharmacy-cf7 .hr-step-note {margin: 10px 0 0; text-align: center; font-size: .85rem; color: var(--muted)}
#hr-pharmacy-cf7 .hr-consent {margin: 12px 0 0; font-size: .8rem; color: var(--muted)}
#hr-pharmacy-cf7 .hr-optin {margin-top: 10px; font-size: .85rem}
#hr-pharmacy-cf7 .hr-optin .wpcf7-list-item {margin: 0}
/* Consistent two-step CF7 layout in the hero and popup, including theme overrides. */
#hr-pharmacy-cf7, #hr-pharmacy-cf7 .wpcf7, #hr-pharmacy-cf7 form, #hr-pharmacy-cf7 .hr-fstep {width: 100%; min-width: 0; max-width: 100%; box-sizing: border-box}
#hr-pharmacy-cf7 .hr-field {display: block !important; margin: 0 0 18px !important; padding: 0 !important}
#hr-pharmacy-cf7 .hr-field>label {display: block !important; margin: 0 0 8px !important; padding: 0 !important; font-size: 15px; font-weight: 700; line-height: 1.4; text-align: left; }
#hr-pharmacy-cf7 .hr-field>.wpcf7-form-control-wrap {display: block !important; width: 100% !important}
#hr-pharmacy-cf7 .hr-field>br, #hr-pharmacy-cf7 .hr-tel br {display: none !important}
#hr-pharmacy-cf7 input.wpcf7-text, #hr-pharmacy-cf7 input[name="your-name"], #hr-pharmacy-cf7 input[name="your-number"] {display: block !important; box-sizing: border-box !important; float: none !important; width: 100% !important; max-width: 100% !important; min-width: 0 !important; height: 48px !important; min-height: 48px !important; margin: 0 !important; padding: 10px 12px !important; border: 1.5px solid #B8C6D6 !important; border-radius: 8px !important; background: #fff !important; color: var(--text) !important; font: 400 16px/1.4 Mulish, system-ui, sans-serif !important; box-shadow: none !important; appearance: none}
#hr-pharmacy-cf7 input.wpcf7-text:focus {border-color: var(--navy) !important; outline: 2px solid rgba(26, 86, 146, .18) !important; outline-offset: 1px}
#hr-pharmacy-cf7 .hr-tel, #hr-pharmacy-cf7 .hr-tel>p {display: flex !important; align-items: stretch !important; gap: 0 !important; width: 100% !important; min-width: 0 !important; margin: 0 !important; padding: 0 !important}
#hr-pharmacy-cf7 .hr-tel>span[aria-hidden="true"], #hr-pharmacy-cf7 .hr-tel>p>span[aria-hidden="true"] {display: flex !important; align-items: center !important; justify-content: center !important; flex: 0 0 54px !important; height: 48px !important; box-sizing: border-box !important; margin: 0 !important; padding: 0 10px !important; border: 1.5px solid #B8C6D6 !important; border-right: 0 !important; border-radius: 8px 0 0 8px !important; background: var(--sky-2) !important; font-weight: 700}
#hr-pharmacy-cf7 .hr-tel .wpcf7-form-control-wrap {display: block !important; flex: 1 1 0% !important; min-width: 0 !important; width: auto !important; margin: 0 !important}
#hr-pharmacy-cf7 .hr-tel #hr-mobile {border-radius: 0 8px 8px 0 !important}
#hr-pharmacy-cf7 .hr-picked {margin: 0 0 18px !important; line-height: 1.5}
#hr-pharmacy-cf7 .hr-picked>span {min-width: 0}
#hr-pharmacy-cf7 .hr-back {flex: none; min-height: 32px}
#hr-pharmacy-cf7 .hr-fstep input.wpcf7-submit, #hr-pharmacy-cf7 .hr-fstep input[type="submit"], #hr-pharmacy-cf7 .hr-fstep button[type="submit"], #hr-pharmacy-cf7 .hr-next {display: flex !important; align-items: center !important; justify-content: center !important; box-sizing: border-box !important; float: none !important; width: 100% !important; max-width: none !important; min-height: 48px !important; height: auto !important; margin: 4px 0 0 !important; padding: 14px 20px !important; border: 0 !important; border-radius: 10px !important; background: var(--cta) !important; background-image: none !important; color: #fff !important; font: 800 16px/1.3 Mulish, system-ui, sans-serif !important; text-transform: none !important; letter-spacing: normal !important; box-shadow: none !important; cursor: pointer; appearance: none}
#hr-pharmacy-cf7 .hr-fstep input[type="submit"]:hover, #hr-pharmacy-cf7 .hr-fstep button[type="submit"]:hover, #hr-pharmacy-cf7 .hr-next:hover {background: var(--cta-hover) !important}
#hr-pharmacy-cf7 .hr-fstep input[type="submit"]:disabled {opacity: .65; cursor: wait}
#hr-pharmacy-cf7 .hr-fstep>p:has(.wpcf7-submit) {position: relative; margin: 0 0 14px !important; padding: 0 !important}
#hr-pharmacy-cf7 .hr-fstep>p:has(.wpcf7-submit)>br {display: none !important}
#hr-pharmacy-cf7 .wpcf7-spinner {position: absolute; right: 8px; bottom: 12px; margin: 0 !important}
#hr-pharmacy-cf7 .hr-consent {margin: 14px 0 0 !important; font-size: 12px !important; line-height: 1.7 !important}
#hr-pharmacy-cf7 .hr-optin {margin: 12px 0 0 !important; font-size: 13px; line-height: 1.6}
#hr-pharmacy-cf7 .hr-optin p {margin: 0 !important}
#hr-pharmacy-cf7 .hr-optin label {display: flex !important; align-items: flex-start; gap: 8px; cursor: pointer}
#hr-pharmacy-cf7 .hr-optin input[type="checkbox"] {appearance: auto !important; flex: 0 0 16px !important; width: 16px !important; height: 16px !important; min-height: 0 !important; margin: 3px 0 0 !important; padding: 0 !important; accent-color: var(--navy)}
#hr-pharmacy-cf7 .wpcf7-not-valid-tip {margin-top: 5px; font-size: 12px; line-height: 1.5}
#hr-pharmacy-cf7 .wpcf7-response-output {margin: 16px 0 0 !important; font-size: 13px; line-height: 1.5}

/* Popup layout */
dialog#hr-pharmacy-modal{max-width:500px!important;padding:28px!important;border-radius:20px!important;box-shadow:0 24px 80px rgba(10,30,50,.25)!important;}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-title{margin:0 36px 6px 0!important;font-size:23px!important;line-height:1.35!important;}
dialog#hr-pharmacy-modal #hr-pharmacy-modal-hint{margin:0 0 22px!important;font-size:14px!important;}
dialog#hr-pharmacy-modal button.hr-pharmacy-modal-close{top:14px!important;right:14px!important;width:32px!important;min-width:32px!important;max-width:32px!important;height:32px!important;min-height:32px!important;font-size:23px!important;}
/* Prefix remains 48px high; validation text flows naturally below the input. */
#hr-pharmacy-cf7 .hr-tel,#hr-pharmacy-cf7 .hr-tel>p{align-items:flex-start!important;}
#hr-pharmacy-cf7 .hr-tel .wpcf7-form-control-wrap{border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;}
#hr-pharmacy-cf7 .wpcf7-not-valid-tip,#hr-pharmacy-cf7 .hr-err{position:static!important;display:block;box-sizing:border-box!important;width:100%!important;max-width:100%!important;min-height:0!important;height:auto!important;margin:6px 0 0!important;padding:0!important;border:0!important;border-radius:0!important;background:transparent!important;box-shadow:none!important;color:#C62828!important;font:400 12px/1.5 Mulish,system-ui,sans-serif!important;text-align:left!important;white-space:normal!important;}
#hr-pharmacy-cf7 .wpcf7-not-valid-tip::before,#hr-pharmacy-cf7 .wpcf7-not-valid-tip::after{display:none!important;}
#hr-pharmacy-cf7 input.wpcf7-not-valid{border-color:#C62828!important;}
#hr-pharmacy-cf7 .hr-err[hidden]{display:none!important;}
@media(max-width:480px){
  dialog#hr-pharmacy-modal{width:calc(100% - 24px)!important;padding:24px 18px!important;border-radius:16px!important;}
  dialog#hr-pharmacy-modal #hr-pharmacy-modal-title{font-size:21px!important;}
}
/* Phone reading (layout only, no copy changes): every screen gets one clear thing to look at.
   First screen: headline, promise and the start of the form. Features: heading, picture, then ticks. */
@media (max-width:720px) {
  h1 {font-size:1.85rem;line-height:1.15}
  h2 {font-size:1.4rem}
  .hero {padding:20px 0 28px}
  .hero .eyebrow {font-size:.72rem;letter-spacing:.06em;margin-bottom:.45rem}
  .hero .sub {font-size:1.02rem;line-height:1.55;margin-bottom:16px}
  .free-line {padding:9px 12px;font-size:.93rem;line-height:1.45}
  .free-line svg {width:18px;height:18px;margin-top:1px}
  .trust {margin-top:14px;gap:6px 16px;font-size:.88rem}
  .trust svg {width:16px;height:16px;margin-top:2px}
  .form-card {padding:20px 18px}
  .lead {font-size:1rem;line-height:1.55}
  .tour {padding-bottom:40px}
  .tour-cta {margin-top:20px}
  .proof {padding:32px 0 28px}
  .logos-title {margin-bottom:14px;font-size:1rem}
  .proof-grid {margin-top:24px;padding-top:20px;gap:14px}
  .pain-list {gap:10px}
  .pain-list li {padding:12px 14px;font-size:.98rem;line-height:1.5}
  .pain-list svg {width:20px;height:20px;margin-top:2px}
  /* Each feature: label, heading and short text, then its picture, then the ticks. A line separates features. */
  .features {gap:0}
  .feature+.feature {margin-top:40px;padding-top:40px;border-top:1px solid var(--line)}
  .feature-head,.feature:nth-child(even) .feature-head {display:contents}
  .feature-copy {order:1}
  .feature-visual {order:2}
  .feature-points {order:3}
  .feature-copy p {font-size:1rem;line-height:1.6}
  .ticks li {font-size:.98rem;line-height:1.5;margin-bottom:8px}
  .ticks li::before {top:3px}
  .feature-points .plan-note {margin-top:10px;font-size:.8rem}
  .testi-grid {gap:14px}
  .testi-card {padding:20px 18px}
  .testi-card blockquote {font-size:1rem;margin-bottom:16px}
  .testi-card blockquote::before {font-size:2rem;line-height:.7;margin-bottom:4px}
  .testi-card .who {padding-top:14px}
  .plan {padding:20px 18px}
  .plan li {padding:5px 0 5px 26px}
  /* Same green round tick as the feature lists, so the page uses one tick style. */
  .plan li::before {content:"";left:0;top:9px;width:16px;height:16px;border-radius:50%;background:var(--green) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'%3E%3Cpath fill='white' d='M9.5 16.2 5.3 12l-1.4 1.4 5.6 5.6 11-11-1.4-1.4z'/%3E%3C/svg%3E") center/12px no-repeat}
  .pricing-foot {font-size:.9rem;margin-top:22px}
  .faq summary {padding:14px 46px 14px 16px}
  .faq summary::after {top:14px}
  .faq details p {padding:0 16px 14px}
  /* Room for the sticky Start free bar, so it never covers the footer links. */
  footer {padding-bottom:96px}
}
/* CF7's message box only takes space once it has a message. */
#hr-pharmacy-cf7 .wpcf7-response-output:empty {display:none!important}

/* Billing choice buttons (replace the old radio cards; value goes into hidden bed_size as a plain string) */
#hr-pharmacy-cf7 .hr-bill-options{display:grid!important;grid-template-columns:repeat(2,minmax(0,1fr))!important;gap:10px!important;width:100%}
#hr-pharmacy-cf7 .hr-bill-options>br{display:none!important}
#hr-pharmacy-cf7 .hr-bill-options>p{display:contents!important}
#hr-pharmacy-cf7 .hr-bill-option{position:relative!important;display:flex!important;flex-direction:column!important;align-items:flex-start!important;justify-content:center!important;gap:4px!important;box-sizing:border-box!important;width:100%!important;min-width:0!important;min-height:76px!important;margin:0!important;padding:12px 12px 12px 40px!important;border:1px solid #D9E3EE!important;border-radius:12px!important;background:#fff!important;background-image:none!important;box-shadow:none!important;color:#123E6B!important;font:600 14px/1.4 Mulish,system-ui,sans-serif!important;text-align:left!important;text-transform:none!important;letter-spacing:normal!important;cursor:pointer;appearance:none;transition:border-color .15s,background .15s}
#hr-pharmacy-cf7 .hr-bill-option::before{content:"";position:absolute;left:13px;top:50%;width:16px;height:16px;margin-top:-8px;border:2px solid #9AACBF;border-radius:50%;background:#fff;box-sizing:border-box}
#hr-pharmacy-cf7 .hr-bill-option:hover{border-color:#1A5692!important;background:#F8FBFE!important}
#hr-pharmacy-cf7 .hr-bill-option.is-selected{border-color:#1A5692!important;background:#F0F7FF!important;box-shadow:0 0 0 1px #1A5692!important}
#hr-pharmacy-cf7 .hr-bill-option.is-selected::before{border:5px solid #1A5692}
#hr-pharmacy-cf7 .hr-bill-option:focus-visible{outline:3px solid #F59E0B!important;outline-offset:2px}
#hr-pharmacy-cf7 .hr-bill-title{display:block;font-size:14px;font-weight:700;line-height:1.35;color:#123E6B}
#hr-pharmacy-cf7 .hr-bill-option .hr-choice-note{display:block!important;margin:0!important;font-size:11px!important;font-weight:400!important;line-height:1.4!important;color:#697586!important}
#hr-pharmacy-cf7 .hr-software.invalid .hr-bill-option{border-color:#C62828!important}
@media(max-width:480px){
  #hr-pharmacy-cf7 .hr-bill-options{grid-template-columns:1fr!important;gap:8px!important}
  #hr-pharmacy-cf7 .hr-bill-option{min-height:60px!important;padding:10px 14px 10px 40px!important}
}
</style>

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
      <span class="eyebrow" id="hero-eyebrow">Pharmacy management software for&nbsp;medical&nbsp;stores</span>
      <h1 id="hero-title">Pharmacy Billing Software That Warns You Before Medicines Expire</h1>
      <p class="sub" id="hero-sub">Easy medical store billing software: make GST bills fast, keep stock correct and catch expiry on time. Works on computer, laptop or mobile.</p>
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
      <p class="hint" id="form-hint">Takes 30 seconds. We'll help you set up and move your old data free.</p>
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

<!-- ============ PRODUCT TOUR VIDEO (right after the hero) ============ -->
<section class="tour" id="tour" aria-labelledby="tour-title">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow">1-minute video</span>
      <h2 id="tour-title">See the pharmacy software in 1 minute</h2>
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

<!-- ============ PROBLEMS ============ -->
<section class="problems">
  <div class="wrap">
    <div class="sec-head"><h2>Does this happen at your counter?</h2></div>
    <ul class="pain-list">
      <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg>Expired stock is found too late, and the company won't take it back.</li>
      <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg>The software hangs at 8 pm when 5 customers are waiting.</li>
      <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg>Hours go in purchase entry of distributor bills.</li>
      <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg>Your CA keeps asking for the same GST data again.</li>
      <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm1 15h-2v-2h2zm0-4h-2V7h2z"/></svg>You don't know what discount and udhaar your staff are giving.</li>
    </ul>
  </div>
</section>

<!-- ============ FEATURES ============ -->
<section id="features">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">What you get</span><h2>One pharmacy management software for billing, stock, expiry and GST</h2></div>

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

<!-- ============ INTERNET QUESTION ============ -->
<section class="internet" id="internet">
  <div class="wrap internet-grid">
    <div>
      <span class="eyebrow" style="color:#9CC3EA">Every chemist asks this</span>
      <h2>“What if the internet goes down?”</h2>
      <p style="margin:0">Healthray is online pharmacy management software, so you need internet. Here is why medical stores are fine with it:</p>
    </div>
    <ul class="internet-points">
      <li><strong>Works on any device</strong>Use it on your counter computer, laptop or mobile. If Wi-Fi stops, use your phone's hotspot.</li>
      <li><strong>Your data is safe</strong>Data is saved online, so a broken computer or a virus can't delete it.</li>
      <li><strong>Help any time</strong>Our team is available 24×7.</li>
    </ul>
  </div>
</section>

<!-- ============ HOW TO START ============ -->
<!-- White, so it doesn't merge with the light-blue testimonials below into one big empty gap. -->
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

<!-- ============ TESTIMONIALS (placeholders: use only real customers' exact words, with permission) ============ -->
<section class="testi">
  <div class="wrap">
    <div class="sec-head"><h2>What chemists say</h2></div>
    <!-- DUMMY testimonials for layout review only. Replace with real customers' exact words (with permission) before launch. -->
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

<!-- ============ PRICING ============ -->
<section id="pricing">
  <div class="wrap">
    <div class="sec-head"><span class="eyebrow">Pricing</span><h2>Pharmacy software price: simple plans for every medical store</h2><p class="lead">Start free. Upgrade only when you need more bills or users.</p></div>
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
      <details name="hr-pharmacy-faq" open><summary>What if the internet goes down?</summary><p>Healthray Pharmacy works online, so you need internet. It works on any computer, laptop or mobile, so if Wi-Fi stops you can use your phone's hotspot. Your data stays safe online even if a computer breaks, and our team is available 24×7.</p></details>
      <details name="hr-pharmacy-faq"><summary>Is the free plan really free?</summary><p>Yes. 10 bills a month and 1 user, with no time limit. No card needed, no auto payment. Upgrade only if you want to.</p></details>
      <details name="hr-pharmacy-faq"><summary>Can you move my data from Marg or my current software?</summary><p>Yes, free. Our team moves your medicines, stock, suppliers and customers from Marg, any other software or Excel, so everything is ready from day one.</p></details>
      <details name="hr-pharmacy-faq"><summary>Is this pharmacy management software or only billing?</summary><p>It is full pharmacy management software: billing, batch-wise stock and expiry, purchase entry, GST reports and staff control, all in one place. Start with billing and use the rest when you need it.</p></details>
      <details name="hr-pharmacy-faq"><summary>Is my data safe?</summary><p>Your data is saved online, not on one computer, so a broken computer or theft can't delete it. Each staff member sees only what you allow, and every change is saved.</p></details>
      <details name="hr-pharmacy-faq"><summary>Does it have a medicine list?</summary><p>Yes, a ready medicine list is included, so you can start billing straight away. You can add your own items any time.</p></details>
      <details name="hr-pharmacy-faq"><summary>Will it print on my printer?</summary><p>Yes. It prints A4, A5 and 80 mm thermal bills, with GST invoice layouts, your logo, and your DL and GST numbers.</p></details>
      <details name="hr-pharmacy-faq"><summary>How long will my staff take to learn it?</summary><p>It's easy: type a few letters, pick the medicine, press Enter. Search by name, salt or barcode. Our team is one call away while your staff learn.</p></details>
      <details name="hr-pharmacy-faq"><summary>What support do I get?</summary><p>24×7 help on phone, even in the evening and on Sunday.</p></details>
    </div>
  </div>
</section>

<!-- ============ FINAL CTA ============ -->
<section class="final">
  <div class="wrap">
    <h2>Try free pharmacy billing software in your medical store</h2>
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
      <a href="https://healthray.com/terms-and-conditions/" target="_blank" rel="noopener">Terms</a>
      <a href="https://healthray.com/contact-us/" target="_blank" rel="noopener">Contact</a>
    </nav>
  </div>
</footer>

<div class="sticky" id="sticky" aria-hidden="true">
  <a class="btn btn-cta js-start" href="#start" data-cta="sticky_start" tabindex="-1">Start free</a>
</div>


<dialog id="hr-pharmacy-modal" aria-labelledby="hr-pharmacy-modal-title" aria-describedby="hr-pharmacy-modal-hint">
    <button type="button" class="hr-pharmacy-modal-close" aria-label="Close form">×</button>
    <h2 id="hr-pharmacy-modal-title">Start free today</h2>
    <p class="hint" id="hr-pharmacy-modal-hint">Fill in your details. Our team will help you get started.</p>
    <div id="hr-pharmacy-modal-slot"></div>
  </dialog>
<script>
    (function() {
      'use strict';
      /* ===== Headline variants for message match (?v=a|b|c|d|hi) ===== */
      var VARIANTS = {
  a: {h:'Pharmacy Billing Software That Warns You Before Medicines Expire'},
  b: {h:'Medical Store Billing Software for Billing, Stock and GST',
      s:'Make GST bills fast, keep stock correct and catch expiry on time. Works on computer, laptop or mobile.'},
  c: {h:'Free Pharmacy Billing Software for Your Medical Store'},
  d: {h:'Switching from Marg or Other Pharmacy Software? We Move Your Data Free',
      s:'We move your old data free. 24×7 help and a clear yearly price. Fast billing, expiry alerts and GST data for your CA.'},
  e: {h:'Pharmacy Management Software for Medical Stores', k:'For medical stores & chemists',
      s:'Billing, stock, expiry, purchase entry and GST in one pharmacy software. Works on computer, laptop or mobile.'},
  hi:{h:'Medical Store ki Billing, Stock aur Expiry, Ek Hi Software Mein',
      s:'Counter par fast billing, expiry se pehle alert, aur CA ke liye saaf GST data. Kisi bhi PC, laptop ya phone par chalta hai.'}
};
      var params = new URLSearchParams(location.search);
      var v = (params.get('v') || 'a').toLowerCase();
      if (!VARIANTS[v]) v = 'a';
      document.getElementById('hero-title').textContent = VARIANTS[v].h;
      if (VARIANTS[v].s) document.getElementById('hero-sub').textContent = VARIANTS[v].s;
      if (VARIANTS[v].k) document.getElementById('hero-eyebrow').textContent = VARIANTS[v].k;

      window.dataLayer = window.dataLayer || [];

      function track(event, data) {
        dataLayer.push(Object.assign({
          event: event,
          segment: 'retail',
          variant: v
        }, data || {}));
      }
      var reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

      /* ===== Contact Form 7 and shared form popup ===== */
      var formHost = document.getElementById('hr-pharmacy-cf7');
      var formHome = document.getElementById('hr-pharmacy-form-home');
      var dialog = document.getElementById('hr-pharmacy-modal');
      var modalSlot = document.getElementById('hr-pharmacy-modal-slot');
      var previousFocus = null;

      var activeMode = 'start_free',
        activeLocation = 'hero';

      function setTracking(mode, loc) {
        activeMode = mode;
        activeLocation = loc || '';
        var values = {
          variant: v,
          cta_type: mode,
          cta_location: loc || ''
        };
        ['gclid', 'fbclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(k) {
          var val = params.get(k);
          if (val) values[k] = val.slice(0, 200);
        });
        // Populate existing CF7 hidden tags; mail tags remain controlled in CF7 admin.
        formHost.querySelectorAll('input[type="hidden"]').forEach(function(input) {
          var key = input.name.replace(/_cf7$/, '');
          if (Object.prototype.hasOwnProperty.call(values, key)) input.value = values[key];
        });
      }
      setTracking('start_free', 'hero');

      function openForm(e, mode, loc) {
        e.preventDefault();
        previousFocus = e.currentTarget;
        var demo = mode === 'demo';
        document.getElementById('hr-pharmacy-modal-title').textContent = demo ? 'Book a free demo' : 'Start free today';
        document.getElementById('hr-pharmacy-modal-hint').textContent = demo ? "Fill in your details. We'll call to arrange your free demo." : 'Fill in your details. Our team will help you get started.';
        setTracking(mode, loc);
        track('cta_click', {
          cta_type: mode,
          cta_location: loc
        });
        // Keep the initialized CF7 DOM intact, including validation and entered values.
        if (typeof dialog.showModal !== 'function') {
          document.getElementById('start').scrollIntoView({
            behavior: reduceMotion ? 'auto' : 'smooth'
          });
          return;
        }
        modalSlot.appendChild(formHost);
        dialog.showModal();
        document.documentElement.classList.add('hr-pharmacy-modal-open');
        focusFormStep();
      }
      document.querySelectorAll('.js-start,.js-demo').forEach(function(el) {
        el.setAttribute('aria-haspopup', 'dialog');
        el.setAttribute('aria-controls', 'hr-pharmacy-modal');
        el.addEventListener('click', function(e) {
          openForm(e, el.classList.contains('js-demo') ? 'demo' : 'start_free', el.dataset.cta);
        });
      });
      dialog.querySelector('.hr-pharmacy-modal-close').addEventListener('click', function() {
        dialog.close();
      });
      dialog.addEventListener('click', function(e) {
        if (e.target !== dialog) return;
        var r = dialog.getBoundingClientRect();
        if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dialog.close();
      });
      // Native dialog handles Escape and traps focus; restore the form and trigger on close.
      dialog.addEventListener('close', function() {
        formHome.appendChild(formHost);
        setTracking('start_free', 'hero');
        document.documentElement.classList.remove('hr-pharmacy-modal-open');
        if (previousFocus) previousFocus.focus({
          preventScroll: true
        });
      });
      var started = false;
      formHost.addEventListener('input', function() {
        if (!started) {
          started = true;
          track('form_start');
        }
      });
      formHost.addEventListener('wpcf7mailsent', function() {
        track('form_success', {
          form: 'pharmacy_ppc'
        });
      });

      /* Two-step CF7 form: billing choice (buttons -> hidden bed_size string), then contact details. */
      var cf7Form = formHost.querySelector('form');
      var stepOne = formHost.querySelector('.hr-fstep-1');
      var stepTwo = formHost.querySelector('.hr-fstep-2');
      var currentStep = 1;

      function focusFormStep() {
        var scope = stepOne && stepTwo ? (currentStep === 1 ? stepOne : stepTwo) : formHost;
        var first = scope.querySelector('input:not([type="hidden"]):not([type="submit"]):not([disabled]),select,textarea') || scope.querySelector('button');
        if (first) first.focus({
          preventScroll: true
        });
      }

      if (cf7Form && stepOne && stepTwo) {
        var billInput = cf7Form.querySelector('input[name="bed_size"]');
        var billButtons = stepOne.querySelectorAll('.hr-bill-option');
        var next = stepOne.querySelector('.hr-next');
        var back = stepTwo.querySelector('.hr-back');
        var softwareError = stepOne.querySelector('.hr-software-err');
        var softwareBox = stepOne.querySelector('.hr-software');

        // Match the HMS reference: keep the input local and prefix the submitted FormData.
        var mobileField = cf7Form.querySelector('input[name="your-number"]:not([type="hidden"])') || cf7Form.querySelector('input[name="mobile-local"]');
        function normalizeMobile(value) {
          var digits = value.replace(/\D/g, '');
          if (digits.length === 12 && digits.slice(0, 2) === '91') return digits.slice(2);
          if (digits.length === 11 && digits.charAt(0) === '0') return digits.slice(1);
          return digits.slice(0, 10);
        }
        cf7Form.addEventListener('formdata', function(event) {
          var digits = mobileField ? normalizeMobile(mobileField.value) : '';
          event.formData.set('your-number', /^\d{10}$/.test(digits) ? '+91 ' + digits : '');
        });

        var mobileError = stepTwo.querySelector('.hr-mobile-err');
        var stepLabel = formHost.querySelector('.hr-fsteps-label');
        var pickedLabel = stepTwo.querySelector('.hr-picked-label');

        // Announce step changes without reading the decorative progress bar.
        var status = document.createElement('span');
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        status.style.cssText = 'position:absolute;width:1px;height:1px;padding:0;overflow:hidden;clip-path:inset(50%);white-space:nowrap';
        formHost.appendChild(status);

        /* ---- billing buttons ---- */
        function chosenSoftware() {
          return billInput && billInput.value ? billInput : null;
        }
        function selectBill(btn) {
          billButtons.forEach(function(b) {
            var on = b === btn;
            b.classList.toggle('is-selected', on);
            b.setAttribute('aria-pressed', on ? 'true' : 'false');
          });
          billInput.value = btn.dataset.bed; // plain string, not an array
          if (softwareError) softwareError.hidden = true;
          if (softwareBox) softwareBox.classList.remove('invalid');
        }
        function clearBill() {
          if (billInput) billInput.value = '';
          billButtons.forEach(function(b) {
            b.classList.remove('is-selected');
            b.setAttribute('aria-pressed', 'false');
          });
        }
        billButtons.forEach(function(btn) {
          btn.addEventListener('click', function() { selectBill(btn); });
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
        if (back) back.addEventListener('click', function() {
          showStep(1, true);
        });

        function validMobile() {
          var valid = !!mobileField && /^\d{10}$/.test(mobileField.value);
          if (mobileError) {
            mobileError.hidden = valid;
            mobileError.textContent = 'Please enter a 10-digit mobile number.';
          }
          if (mobileField) {
            mobileField.setCustomValidity(valid ? '' : 'Please enter a 10-digit mobile number.');
            mobileField.setAttribute('aria-invalid', valid ? 'false' : 'true');
          }
          return valid;
        }
        if (mobileField) {
          mobileField.setAttribute('inputmode', 'numeric');
          mobileField.setAttribute('pattern', '[0-9]{10}');
          mobileField.removeAttribute('maxlength');
          mobileField.value = normalizeMobile(mobileField.value);
          if (mobileError) {
            mobileError.id = 'hr-pharmacy-mobile-error';
            mobileError.textContent = 'Please enter a 10-digit mobile number.';
            var describedBy = (mobileField.getAttribute('aria-describedby') || '').split(/\s+/).filter(Boolean);
            if (describedBy.indexOf(mobileError.id) === -1) describedBy.push(mobileError.id);
            mobileField.setAttribute('aria-describedby', describedBy.join(' '));
          }
          mobileField.addEventListener('input', function(event) {
            mobileField.value = event.inputType === 'insertText' ? mobileField.value.replace(/\D/g, '').slice(0, 10) : normalizeMobile(mobileField.value);
            if (mobileError && !mobileError.hidden) {
              validMobile();
            } else {
              mobileField.setCustomValidity('');
              mobileField.setAttribute('aria-invalid', 'false');
            }
          });
        }

        // Capture before CF7's submit handler; CF7 remains responsible for sending.
        cf7Form.addEventListener('submit', function(e) {
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
            return;
          }
        }, true);

        cf7Form.addEventListener('reset', function() {
          setTimeout(function() {
            // A hidden input keeps a JS-set value through reset, so clear it explicitly.
            clearBill();
            if (softwareError) softwareError.hidden = true;
            if (softwareBox) softwareBox.classList.remove('invalid');
            if (mobileError) mobileError.hidden = true;
            if (mobileField) {
              mobileField.setCustomValidity('');
              mobileField.removeAttribute('aria-invalid');
            }
            showStep(1, false);
            // Restore CTA and campaign fields after CF7 resets hidden defaults.
            setTracking(activeMode, activeLocation);
          }, 0);
        });

        formHost.addEventListener('wpcf7invalid', function(e) {
          var invalid = e.detail && e.detail.apiResponse && e.detail.apiResponse.invalid_fields || [];
          var softwareInvalid = invalid.some(function(field) {
            return field.field === 'bed_size';
          });
          showStep(softwareInvalid ? 1 : 2, true);
        });

        showStep(1, false);
      }

      // CF7 owns submission, validation, mail delivery and response messages.

      /* ===== FAQ accordion: first open, only one expanded at a time ===== */
      var faqItems = document.querySelectorAll('.hr-pharmacy-landing #faq .faq details');
      faqItems.forEach(function(item) {
        // Fallback for browsers without exclusive details-name grouping.
        item.addEventListener('toggle', function() {
          if (!item.open) return;
          faqItems.forEach(function(other) {
            if (other !== item) other.open = false;
          });
        });
      });

      /* ===== Scroll-driven UI: sticky mobile bar + tour video ===== */
      var sticky = document.getElementById('sticky');
      var heroForm = document.getElementById('start');
      var tour = document.getElementById('tour-video');
      var stickyShown = false,
        ticking = false,
        tourStarted = false,
        tourDone = false;

      function inView(el, share) {
        var r = el.getBoundingClientRect();
        var visible = Math.min(r.bottom, innerHeight) - Math.max(r.top, 0);
        return visible > r.height * share;
      }

      function onScroll() {
        ticking = false;
        var r = heroForm.getBoundingClientRect();
        var show = r.bottom < 0 || r.top > innerHeight;
        if (show !== stickyShown) {
          stickyShown = show;
          sticky.classList.toggle('show', show);
          sticky.setAttribute('aria-hidden', show ? 'false' : 'true');
          sticky.querySelectorAll('a').forEach(function(a) {
            a.tabIndex = show ? 0 : -1;
          });
        }
        // The tour has no sound and no controls: play it while at least a third of it is on screen.
        if (inView(tour, .35)) {
          if (tour.paused) tour.play().catch(function() {});
        } else if (!tour.paused) {
          tour.pause();
        }
      }
      addEventListener('scroll', function() {
        if (!ticking) {
          ticking = true;
          requestAnimationFrame(onScroll);
        }
      }, {
        passive: true
      });
      addEventListener('resize', onScroll);
      onScroll();

      tour.addEventListener('play', function() {
        if (!tourStarted) {
          tourStarted = true;
          track('video_start', {
            video: 'product_tour'
          });
        }
      });
      tour.addEventListener('timeupdate', function() {
        if (!tourDone && tour.duration && tour.currentTime > tour.duration - 1) {
          tourDone = true;
          track('video_complete', {
            video: 'product_tour'
          });
        }
      });
      document.getElementById('yr').textContent = new Date().getFullYear();
    })();
  </script>
<?php wp_footer(); ?>
</body>
</html>