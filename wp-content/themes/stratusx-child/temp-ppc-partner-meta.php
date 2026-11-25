<?php

/**
 * Template Name: PPC - Partner Meta
 * Template Post Type: page
 */
defined('ABSPATH') || exit;

$hr_show_testimonials = true;

$hr_partner_asset_base = 'https://healthray.com/wp-content/uploads/2026/11/';
$hr_img = function ($file) use ($hr_partner_asset_base) {
  return esc_url($hr_partner_asset_base . $file);
};

$hr_existing_media = static function ($names) {
  global $wpdb;
  $names = (array) $names;
  foreach ($names as $name) {
    $key = 'hr_partner_existing_' . md5(strtolower($name));
    $id = get_transient($key);
    if ($id === false) {
      $id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT p.ID FROM {$wpdb->posts} p
         LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attachment_image_alt'
         WHERE p.post_type = 'attachment' AND p.post_mime_type LIKE 'image/%%'
           AND (LOWER(p.post_title) = LOWER(%s) OR LOWER(m.meta_value) = LOWER(%s))
         ORDER BY CASE WHEN LOWER(m.meta_value) = LOWER(%s) THEN 0 ELSE 1 END, p.ID DESC LIMIT 1",
        $name,
        $name,
        $name
      ));
      set_transient($key, $id ?: 0, DAY_IN_SECONDS);
    }
    if ($id) {
      $url = wp_get_attachment_url((int) $id);
      if ($url) return esc_url($url);
    }
  }
  return '';
};

$hr_partner_cf7 = '';
if (shortcode_exists('contact-form-7')) {
  if (function_exists('wpcf7_enqueue_styles')) {
    wpcf7_enqueue_styles();
  }
  if (function_exists('wpcf7_enqueue_scripts')) {
    wpcf7_enqueue_scripts();
  }
  $hr_partner_cf7 = do_shortcode('[contact-form-7 id="35724dc" title="Partner - PPC"]');
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>

<head>
  <?php wp_head(); ?>
</head>

<body <?php body_class('hr-partner-landing'); ?>>
  <?php wp_body_open(); ?>
  <style>
    .hr-partner-landing { --navy: #1B3C74; --blue: #0F68EA; --coral: #F96D64; --green-ink: #047857; --green-pale: #D1FAE5; --ink: #0F172A; --ink-2: #1E293B; --slate: #475569; --muted: #6C8093; --line: #E2E8F0; --line-blue: #CFDCFA; --tint: #EFF4FF; --wash: #F8FAFF; --star: #F0A93B; --red: #B42318; --radius: 12px; --card: 18px; --shadow: 0 8px 24px rgba(15, 23, 42, .06); --shadow-cta: 0 14px 28px rgba(27, 60, 116, .24); }
    .hr-partner-landing * { box-sizing: border-box; }
    html { scroll-behavior: smooth; }
    .hr-partner-landing { margin: 0; background: #fff; color: var(--slate); font: 400 17px/1.6 Mulish, system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif; -webkit-font-smoothing: antialiased; overflow-x: clip; }
    .hr-partner-landing img { display: block; max-width: 100%; height: auto; }
    .hr-partner-landing h1, .hr-partner-landing h2, .hr-partner-landing h3 { margin: 0; color: var(--navy); line-height: 1.15; letter-spacing: -.02em; text-wrap: balance; font-weight: 800; }
    .hr-partner-landing p { margin: 0; }
    .hr-partner-landing a { color: var(--blue); }
    .hr-partner-landing :focus-visible { outline: 3px solid #8FB2F5; outline-offset: 3px; }
    .hr-partner-landing .wrap { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
    .hr-partner-landing .acc { color: var(--blue); }
    .hr-partner-landing .eyebrow { display: inline-block; font-size: 12.5px; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--blue); }
    .hr-partner-landing .center { text-align: center; }
    .hr-partner-landing .center .lead { margin-left: auto; margin-right: auto; }
    .hr-partner-landing .h2 { font-size: clamp(28px, 3.4vw, 38px); }
    .hr-partner-landing .lead { margin-top: 12px; font-size: 18px; color: var(--slate); max-width: 56ch; }
    .hr-partner-landing section { padding: 76px 0; }
    .hr-partner-landing section.tint { background: var(--wash); }
    .hr-partner-landing .btn { display: inline-flex; align-items: center; justify-content: center; gap: 8px; min-height: 54px; padding: 16px 28px; border: 0; border-radius: var(--radius); font: 800 16px/1 Mulish, system-ui, sans-serif; text-decoration: none; cursor: pointer; transition: background .2s, transform .2s; }
    .hr-partner-landing .btn-cta { background: var(--blue); color: #fff; box-shadow: var(--shadow-cta); }
    .hr-partner-landing .btn-cta:hover { background: var(--navy); color: #fff; transform: translateY(-2px); }
    .hr-partner-landing .btn-sm { min-height: 40px; padding: 10px 16px; font-size: 14px; box-shadow: none; }
    .hr-partner-landing .site-header { position: sticky; top: 0; z-index: 40; background: #fff; border-bottom: 1px solid var(--line); }
    .hr-partner-landing .site-header .wrap { display: flex; align-items: center; justify-content: space-between; gap: 12px; min-height: 62px; }
    .hr-partner-landing .logo { display: flex; align-items: center; gap: 10px; }
    .hr-partner-landing .logo img { height: 44px; width: auto; }
    .hr-partner-landing .logo-sub { padding-left: 10px; border-left: 1px solid var(--line); font-size: 14px; font-weight: 800; color: var(--muted); }
    .hr-partner-landing .hero { background: linear-gradient(180deg, #EFF4FF 0%, #fff 100%); padding: 40px 0 56px; }
    .hr-partner-landing .hero-grid { display: grid; grid-template-columns: minmax(0, 1.1fr) minmax(0, .9fr); gap: 48px; align-items: start; }
    .hr-partner-landing .pill { display: inline-flex; align-items: center; gap: 8px; background: #fff; border: 1px solid var(--line-blue); border-radius: 999px; padding: 6px 12px; font-size: 13px; font-weight: 800; color: var(--navy); }
    .hr-partner-landing .pill i { width: 8px; height: 8px; border-radius: 50%; background: #16A34A; }
    .hr-partner-landing .hero h1 { margin-top: 16px; font-size: clamp(31px, 4.4vw, 50px); line-height: 1.1; }
    .hr-partner-landing .hero .sub { margin-top: 16px; font-size: 19px; color: var(--ink-2); max-width: 46ch; }
    .hr-partner-landing .earnchips { margin-top: 16px; display: flex; flex-wrap: wrap; gap: 8px; }
    .hr-partner-landing .earnchips span { background: var(--green-pale); color: var(--green-ink); border-radius: 10px; padding: 7px 12px; font-size: 15px; font-weight: 700; }
    .hr-partner-landing .earnchips span.alt { background: #fff; border: 1px solid var(--line); color: var(--ink-2); }
    .hr-partner-landing .earnchips b { font-weight: 900; }
    .hr-partner-landing .for { margin-top: 20px; }
    .hr-partner-landing .for small { display: block; margin-bottom: 8px; font-size: 13px; font-weight: 800; color: var(--muted); }
    .hr-partner-landing .for ul { list-style: none; margin: 0; padding: 0; display: flex; flex-wrap: wrap; gap: 8px; }
    .hr-partner-landing .for li { background: #fff; border: 1px solid var(--line); border-radius: 999px; padding: 6px 12px; font-size: 14px; font-weight: 700; color: var(--ink-2); }
    .hr-partner-landing .nos { list-style: none; margin: 20px 0 0; padding: 0; display: flex; flex-wrap: wrap; gap: 10px 22px; }
    .hr-partner-landing .nos li { display: inline-flex; align-items: center; gap: 8px; font-size: 16px; font-weight: 800; color: var(--ink-2); }
    .hr-partner-landing .nos i, .hr-partner-landing .tick { display: grid; place-items: center; flex: none; width: 24px; height: 24px; border-radius: 50%; background: var(--green-pale); color: var(--green-ink); font-style: normal; font-size: 13px; font-weight: 900; }
    .hr-partner-landing .trust { margin-top: 26px; display: flex; align-items: center; gap: 14px; flex-wrap: wrap; }
    .hr-partner-landing .faces { display: flex; }
    .hr-partner-landing .faces img { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid #fff; margin-left: -10px; box-shadow: 0 2px 6px rgba(15, 23, 42, .15); }
    .hr-partner-landing .faces img:first-child { margin-left: 0; }
    .hr-partner-landing .trust p { font-size: 14.5px; line-height: 1.45; }
    .hr-partner-landing .trust b { color: var(--ink); font-weight: 800; }
    .hr-partner-landing .stars { color: var(--star); }
    .hr-partner-landing .form-card { background: #fff; border: 1px solid var(--line); border-radius: var(--card); padding: 24px; box-shadow: 0 22px 48px -26px rgba(27, 60, 116, .35); }
    .hr-partner-landing .form-card h2 { font-size: 24px; }
    .hr-partner-landing .form-card .hint { margin: 4px 0 16px; font-size: 15px; }
    #hr-partner-cf7 .wpcf7 { margin: 0; }
    #hr-partner-cf7 .wpcf7-form>p { margin: 0 0 14px; }
    #hr-partner-cf7 .wpcf7-form-control-wrap { display: block; }
    #hr-partner-cf7 br { display: none; }
    #hr-partner-cf7 .hr-fstep[hidden], #hr-partner-cf7 .hr-err[hidden] { display: none !important; }
    #hr-partner-cf7 .hr-fsteps { margin: 0 0 16px; }
    #hr-partner-cf7 .hr-fsteps-label { display: block; margin-bottom: 6px; font-size: 12px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    #hr-partner-cf7 .hr-fsteps-bar { display: flex; gap: 6px; }
    #hr-partner-cf7 .hr-fsteps-bar i { flex: 1; height: 4px; border-radius: 4px; background: var(--line); }
    #hr-partner-cf7 .hr-fsteps-bar i:first-child, #hr-partner-cf7[data-step="2"] .hr-fsteps-bar i { background: var(--blue); }
    #hr-partner-cf7 .hr-q { margin: 0 0 10px; font-size: 17px; font-weight: 800; color: var(--navy); }
    #hr-partner-cf7 .hr-role-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; }
    #hr-partner-cf7 .hr-role-options>p { display: contents; }
    #hr-partner-cf7 .hr-role-option { min-height: 56px; padding: 10px 14px; border: 1px solid var(--line-blue); border-radius: var(--radius); background: var(--wash); text-align: left; cursor: pointer; font: 700 15px/1.3 Mulish, system-ui, sans-serif; color: var(--navy); }
    #hr-partner-cf7 .hr-role-option:hover { border-color: var(--blue); background: var(--tint); }
    #hr-partner-cf7 .hr-role-option.is-selected { border-color: var(--blue); background: var(--tint); box-shadow: inset 0 0 0 1px var(--blue); }
    #hr-partner-cf7 .hr-role-option.full { grid-column: 1/-1; }
    #hr-partner-cf7 .hr-role.invalid .hr-role-option { border-color: var(--red); }
    #hr-partner-cf7 .hr-err { margin: 6px 0 0; color: var(--red); font-size: 13.5px; font-weight: 700; }
    #hr-partner-cf7 .hr-next { display: block; width: 100%; min-height: 52px; margin-top: 14px; border: 0; border-radius: var(--radius); background: var(--blue); color: #fff; font: 800 16px Mulish, system-ui, sans-serif; cursor: pointer; box-shadow: var(--shadow-cta); }
    #hr-partner-cf7 .hr-next:hover { background: var(--navy); }
    #hr-partner-cf7 .hr-picked { display: flex; justify-content: space-between; align-items: center; gap: 8px; margin: 0 0 14px; padding: 8px 12px; background: var(--wash); border: 1px solid var(--line); border-radius: 8px; font-size: 15px; font-weight: 800; color: var(--navy); }
    #hr-partner-cf7 .hr-back { border: 0; background: none; padding: 0; color: var(--blue); font: 800 14px Mulish, system-ui, sans-serif; text-decoration: underline; cursor: pointer; }
    #hr-partner-cf7 .hr-field { margin: 0 0 14px; }
    #hr-partner-cf7 .hr-field>label, #hr-partner-cf7 .hr-field legend { display: block; margin: 0 0 6px; padding: 0; font-size: 14px; font-weight: 800; color: var(--navy); }
    #hr-partner-cf7 fieldset { border: 0; margin: 0; padding: 0; }
    #hr-partner-cf7 input.wpcf7-text, #hr-partner-cf7 input.wpcf7-tel { display: block; width: 100%; height: 50px; padding: 0 14px; border: 1.5px solid var(--line-blue); border-radius: var(--radius); font: 16px Mulish, system-ui, sans-serif; color: var(--ink); background: #fff; }
    #hr-partner-cf7 input.wpcf7-text:focus, #hr-partner-cf7 input.wpcf7-tel:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(15, 104, 234, .14); }
    #hr-partner-cf7 input.wpcf7-email { display: block; width: 100%; height: 50px; padding: 0 14px; border: 1.5px solid var(--line-blue); border-radius: var(--radius); font: 16px Mulish, system-ui, sans-serif; color: var(--ink); background: #fff; }
    #hr-partner-cf7 input.wpcf7-email:focus { outline: none; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(15, 104, 234, .14); }
    #hr-partner-cf7 input.wpcf7-email.wpcf7-not-valid { border-color: var(--red); }
    #hr-partner-cf7 .hr-contact-row { display: grid; grid-template-columns: minmax(0, 1.15fr) minmax(0, 1fr); gap: 12px; align-items: start; }
    #hr-partner-cf7 .hr-contact-row .hr-field { min-width: 0; margin-bottom: 14px; }
    #hr-partner-cf7 .hr-contact-row input { min-width: 0; }
    @media (max-width:400px) {
      #hr-partner-cf7 .hr-contact-row { grid-template-columns: minmax(0, 1fr); gap: 0; }
    }
    #hr-partner-cf7 input.wpcf7-not-valid { border-color: var(--red); }
    #hr-partner-cf7 .hr-tel { display: flex; align-items: stretch; }
    #hr-partner-cf7 .hr-tel>span[aria-hidden="true"] { display: flex; align-items: center; padding: 0 12px; border: 1.5px solid var(--line-blue); border-right: 0; border-radius: var(--radius) 0 0 var(--radius); background: var(--tint); font-weight: 800; color: var(--navy); }
    #hr-partner-cf7 .hr-tel .wpcf7-form-control-wrap { flex: 1; min-width: 0; }
    #hr-partner-cf7 .hr-tel input { border-radius: 0 var(--radius) var(--radius) 0; }
    #hr-partner-cf7 .hr-count-options { display: flex; flex-wrap: wrap; gap: 8px; }
    #hr-partner-cf7 .hr-count-option { display: inline-flex; align-items: center; justify-content: center; min-height: 42px; padding: 8px 15px; border: 1px solid var(--line-blue); border-radius: 999px; background: var(--wash); font: 700 14px/1.3 Mulish, system-ui, sans-serif; color: var(--navy); cursor: pointer; transition: background .2s, border-color .2s; }
    #hr-partner-cf7 .hr-count-option:hover { border-color: var(--blue); background: var(--tint); }
    #hr-partner-cf7 .hr-count-option.is-selected { background: var(--blue); border-color: var(--blue); color: #fff; }
    #hr-partner-cf7 .hr-count-option:focus-visible { outline: 3px solid #8FB2F5; outline-offset: 2px; }
    #hr-partner-cf7 input.wpcf7-submit { display: block; width: 100%; min-height: 54px; border: 0; border-radius: var(--radius); background: var(--blue); color: #fff; font: 800 16px Mulish, system-ui, sans-serif; cursor: pointer; box-shadow: var(--shadow-cta); }
    #hr-partner-cf7 input.wpcf7-submit:hover { background: var(--navy); }
    #hr-partner-cf7 input.wpcf7-submit:disabled { opacity: .65; cursor: wait; }
    #hr-partner-cf7 .wpcf7-spinner { position: absolute; right: 8px; bottom: 14px; margin: 0; }
    #hr-partner-cf7 .hr-step-note, #hr-partner-cf7 .hr-consent { margin: 12px 0 0; font-size: 13.5px; line-height: 1.5; color: var(--muted); }
    #hr-partner-cf7 .hr-step-note b, #hr-partner-cf7 .hr-consent b { color: var(--ink-2); }
    #hr-partner-cf7 .wpcf7-not-valid-tip { margin-top: 5px; font-size: 12.5px; }
    #hr-partner-cf7 .wpcf7-response-output { margin: 14px 0 0; padding: 10px; font-size: 14px; border-radius: 8px; }
    #hr-partner-cf7 .wpcf7-response-output:empty { display: none; }
    #hr-partner-cf7 .hr-field { margin-bottom: 13px; }
    #hr-partner-cf7 .hr-field>label { margin-bottom: 5px; }
    #hr-partner-cf7 .hr-contact-row { gap: 10px; }
    #hr-partner-cf7 .hr-contact-row .hr-field { margin-bottom: 12px; }
    #hr-partner-cf7 .wpcf7-form-control-wrap { min-width: 0; }
    #hr-partner-cf7 :is(input.wpcf7-text, input.wpcf7-email, input.wpcf7-tel) { transition: border-color .18s, box-shadow .18s; background: #fff; }
    #hr-partner-cf7 :is(input.wpcf7-text, input.wpcf7-email, input.wpcf7-tel):focus { outline: 0; border-color: var(--blue); box-shadow: 0 0 0 3px rgba(15, 104, 234, .12); }
    #hr-partner-cf7 :is(input.wpcf7-text, input.wpcf7-email, input.wpcf7-tel).wpcf7-not-valid { border-color: #E29D9D; background: #FFFCFC; box-shadow: none; }
    #hr-partner-cf7 .wpcf7-not-valid-tip, #hr-partner-cf7 .hr-err { display: block; margin: 5px 0 0; color: #B42318; font-size: 12px; font-weight: 600; line-height: 1.4; }
    #hr-partner-cf7 .wpcf7-not-valid-tip:empty { display: none; }
    #hr-partner-cf7 .hr-count-options { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 8px; }
    #hr-partner-cf7 .hr-count-option { width: 100%; min-height: 42px; padding: 8px 10px; border-radius: 10px; text-align: center; }
    #hr-partner-cf7 .hr-count-option.is-selected { box-shadow: 0 0 0 2px rgba(15, 104, 234, .14); }
    #hr-partner-cf7 .hr-count-option:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    #hr-partner-cf7 .wpcf7-response-output { border-radius: 10px; font-size: 13px; line-height: 1.5; }
    @media(max-width:440px) {
      #hr-partner-cf7 .hr-contact-row { grid-template-columns: 1fr; gap: 0; }
      #hr-partner-cf7 .hr-count-options { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    }
    dialog#hr-partner-modal { box-sizing: border-box; width: calc(100% - 32px); max-width: 480px; max-height: calc(100% - 32px); margin: auto; padding: 24px; border: 0; border-radius: var(--card); box-shadow: 0 30px 80px rgba(13, 27, 62, .35); overflow: auto; font-family: Mulish, system-ui, sans-serif; color: var(--slate); }
    dialog#hr-partner-modal::backdrop { background: rgba(13, 27, 62, .55); }
    dialog#hr-partner-modal h2 { margin: 0 32px 4px 0; font-size: 22px; color: var(--navy); }
    dialog#hr-partner-modal .hint { margin: 0 0 16px; font-size: 15px; }
    dialog#hr-partner-modal .hr-partner-modal-close { position: absolute; top: 12px; right: 12px; z-index: 2; display: flex; align-items: center; justify-content: center; box-sizing: border-box; width: 36px; height: 36px; min-width: 0; min-height: 0; margin: 0; padding: 0; border: 0; border-radius: 50%; background: var(--wash); box-shadow: none; font-family: inherit; font-size: 22px; font-weight: 400; line-height: 1; letter-spacing: 0; text-transform: none; color: var(--navy); cursor: pointer; transition: background .15s ease; }
    dialog#hr-partner-modal .hr-partner-modal-close:hover, dialog#hr-partner-modal .hr-partner-modal-close:focus { background: var(--line); color: var(--navy); box-shadow: none; }
    dialog#hr-partner-modal .hr-partner-modal-close:focus-visible { outline: 2px solid var(--blue); outline-offset: 2px; }
    dialog#hr-partner-modal h2 { margin-right: 44px; }
    html.hr-partner-modal-open, html.hr-partner-modal-open body { overflow: hidden; }
    .hr-partner-landing .logos { padding: 30px 0 34px; border-top: 1px solid var(--line); border-bottom: 1px solid var(--line); text-align: center; }
    .hr-partner-landing .logos p { font-size: 14px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; color: var(--muted); }
    .hr-partner-landing .logo-marquee { margin-top: 20px; overflow: hidden; -webkit-mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); mask-image: linear-gradient(90deg, transparent, #000 8%, #000 92%, transparent); }
    .hr-partner-landing .logo-track { display: flex; width: max-content; will-change: transform; -webkit-backface-visibility: hidden; backface-visibility: hidden; transform: translate3d(0, 0, 0); animation: hrPartnerScroll 45s linear infinite; }
    @media (hover: hover) and (pointer: fine) {
      .hr-partner-landing .logo-marquee:hover .logo-track { animation-play-state: paused; }
    }
    .hr-partner-landing .logo-set { display: flex; align-items: center; gap: 56px; padding-right: 56px; }
    .hr-partner-landing .logo-set img { height: 46px; width: auto; max-width: none; flex: none; object-fit: contain; }
    @keyframes hrPartnerScroll {
      from { transform: translate3d(0, 0, 0); }
      to { transform: translate3d(-50%, 0, 0); }
    }
    @media (prefers-reduced-motion:reduce) {
      .hr-partner-landing .logo-track { animation: none; width: auto; }
      .hr-partner-landing .logo-set { flex-wrap: wrap; justify-content: center; gap: 20px 40px; padding-right: 0; }
      .hr-partner-landing .logo-set[aria-hidden] { display: none; }
    }
    .hr-partner-landing .card { background: #fff; border: 1px solid var(--line); border-radius: var(--card); padding: 24px; box-shadow: var(--shadow); text-align: left; }
    .hr-partner-landing .ic { display: grid; place-items: center; flex: none; width: 48px; height: 48px; border-radius: 12px; background: var(--tint); color: var(--blue); }
    .hr-partner-landing .ic.green { background: var(--green-pale); color: var(--green-ink); }
    .hr-partner-landing .grid3 { margin-top: 40px; display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
    .hr-partner-landing .grid4 { margin-top: 40px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 16px; }
    .hr-partner-landing .card h3 { margin-top: 14px; font-size: 19px; }
    .hr-partner-landing .card p { margin-top: 8px; font-size: 15.5px; line-height: 1.55; }
    .hr-partner-landing .step { position: relative; }
    .hr-partner-landing .step .no { position: absolute; top: 22px; right: 24px; font-size: 40px; font-weight: 900; line-height: 1; color: #E6EDFB; }
    .hr-partner-landing .big { display: block; font-size: 34px; font-weight: 900; letter-spacing: -.02em; line-height: 1.1; color: var(--blue); }
    .hr-partner-landing .src { margin-top: 16px; font-size: 13px; color: var(--muted); }
    .hr-partner-landing .ladder { margin-top: 36px; display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; align-items: end; }
    .hr-partner-landing .rung { display: flex; flex-direction: column; justify-content: flex-end; }
    .hr-partner-landing .rung .ic { margin-bottom: auto; width: 42px; height: 42px; }
    .hr-partner-landing .rung.r1 { min-height: 200px; }
    .hr-partner-landing .rung.r2 { min-height: 236px; }
    .hr-partner-landing .rung.r3 { min-height: 272px; }
    .hr-partner-landing .rung.r4 { min-height: 308px; border: 2px solid var(--green-ink); background: linear-gradient(180deg, #fff 0%, #ECFDF5 100%); }
    .hr-partner-landing .rung h3 { font-size: 17.5px; }
    .hr-partner-landing .rung small { display: block; margin-top: 2px; font-size: 13.5px; font-weight: 700; color: var(--muted); }
    .hr-partner-landing .rung .deal { margin-top: 12px; font-size: 14px; font-weight: 700; }
    .hr-partner-landing .rung .deal b { color: var(--ink-2); }
    .hr-partner-landing .rung .you { margin-top: 8px; font-size: 24px; font-weight: 900; letter-spacing: -.02em; line-height: 1.15; color: var(--green-ink); }
    .hr-partner-landing .rung .you small { display: inline; font-size: 13px; font-weight: 800; color: var(--green-ink); }
    .hr-partner-landing .ladnote { margin-top: 14px; font-size: 13.5px; color: var(--muted); }
    .hr-partner-landing .maxbox { margin: 36px auto 0; max-width: 780px; display: grid; grid-template-columns: auto minmax(0, 1fr); gap: 20px; align-items: center; text-align: left; background: linear-gradient(135deg, #ECFDF5 0%, #EFF4FF 100%); border: 1px solid #A7F3D0; border-radius: 20px; padding: 22px 26px; }
    .hr-partner-landing .maxbox .amt { font-size: clamp(34px, 5vw, 48px); font-weight: 900; letter-spacing: -.03em; line-height: 1; white-space: nowrap; color: var(--green-ink); }
    .hr-partner-landing .maxbox .amt small { font-size: 18px; font-weight: 800; }
    .hr-partner-landing .maxbox p { font-size: 15.5px; line-height: 1.5; font-weight: 600; color: var(--ink-2); }
    .hr-partner-landing .maxbox p b { color: var(--navy); }
    .hr-partner-landing .calc { margin-top: 24px; display: grid; grid-template-columns: minmax(0, .95fr) minmax(0, 1.05fr); background: #fff; border: 1px solid var(--line-blue); border-radius: 22px; overflow: hidden; box-shadow: 0 26px 56px -30px rgba(27, 60, 116, .4); text-align: left; }
    .hr-partner-landing .calc .in { padding: 28px; }
    .hr-partner-landing .calc .out { padding: 28px; background: var(--wash); border-left: 1px solid var(--line); }
    .hr-partner-landing .calc .q { margin: 0 0 12px; font-size: 17px; font-weight: 800; color: var(--navy); }
    .hr-partner-landing .chiprow { display: flex; flex-wrap: wrap; gap: 8px; }
    .hr-partner-landing .chipbtn { min-height: 40px; padding: 0 14px; border: 1px solid var(--line-blue); border-radius: 999px; background: #fff; font: 800 14px Mulish, system-ui, sans-serif; color: var(--navy); cursor: pointer; }
    .hr-partner-landing .chipbtn.preset.on { background: var(--navy); border-color: var(--navy); color: #fff; }
    .hr-partner-landing .chipbtn.size.on { background: var(--blue); border-color: var(--blue); color: #fff; }
    .hr-partner-landing .calc .lab { display: block; margin-top: 22px; font-size: 15.5px; font-weight: 800; color: var(--navy); }
    .hr-partner-landing .calc .lab small { display: block; font-size: 13px; font-weight: 600; color: var(--muted); }
    .hr-partner-landing .sl { margin-top: 20px; }
    .hr-partner-landing .sl label { display: flex; justify-content: space-between; align-items: center; gap: 10px; font-size: 15.5px; font-weight: 800; color: var(--navy); }
    .hr-partner-landing .sl output { min-width: 44px; text-align: center; background: var(--tint); color: var(--blue); border-radius: 8px; padding: 2px 10px; font-variant-numeric: tabular-nums; }
    .hr-partner-landing .sl input[type=range] { width: 100%; height: 30px; margin-top: 8px; accent-color: #0F68EA; }
    .hr-partner-landing .out .tl { font-size: 13px; font-weight: 800; letter-spacing: .06em; text-transform: uppercase; color: var(--muted); }
    .hr-partner-landing .out .total { margin-top: 4px; font-size: clamp(36px, 4.6vw, 46px); font-weight: 900; letter-spacing: -.03em; line-height: 1.05; color: var(--green-ink); font-variant-numeric: tabular-nums; }
    .hr-partner-landing .out .mo { margin-top: 6px; font-size: 16px; font-weight: 700; color: var(--ink-2); }
    .hr-partner-landing .out .mo b { color: var(--navy); font-weight: 900; }
    .hr-partner-landing .bars { margin-top: 20px; display: grid; gap: 12px; }
    .hr-partner-landing .bar { display: grid; grid-template-columns: 62px minmax(0, 1fr) auto; gap: 12px; align-items: center; font-size: 14.5px; font-weight: 700; color: var(--ink-2); }
    .hr-partner-landing .bar .tr { height: 14px; border-radius: 999px; background: #E3EAF5; overflow: hidden; }
    .hr-partner-landing .bar .tr i { display: block; height: 100%; border-radius: 999px; background: var(--blue); transition: width .25s; }
    .hr-partner-landing .bar.y3 .tr i { background: #10B981; }
    .hr-partner-landing .bar b { min-width: 96px; text-align: right; font-weight: 900; color: var(--navy); font-variant-numeric: tabular-nums; }
    .hr-partner-landing .out .note { margin-top: 16px; font-size: 12.5px; line-height: 1.5; color: var(--muted); }
    .hr-partner-landing .prod { display: flex; flex-direction: column; }
    .hr-partner-landing .prod .head { display: flex; align-items: center; gap: 12px; }
    .hr-partner-landing .prod h3 { margin: 0; font-size: 18px; }
    .hr-partner-landing .prod .who { margin: 0; font-size: 13.5px; font-weight: 700; color: var(--muted); }
    .hr-partner-landing .prod ul { list-style: none; margin: 14px 0 0; padding: 0; display: flex; flex-wrap: wrap; gap: 6px; }
    .hr-partner-landing .prod li { background: var(--wash); border: 1px solid var(--line); border-radius: 8px; padding: 3px 8px; font-size: 13px; font-weight: 700; color: var(--ink-2); }
    .hr-partner-landing .prod .price { margin-top: auto; padding-top: 14px; font-size: 14px; font-weight: 700; }
    .hr-partner-landing .prod .price b { color: var(--green-ink); }
    .hr-partner-landing .trustrow { margin-top: 30px; display: flex; flex-wrap: wrap; justify-content: center; align-items: center; gap: 14px 22px; }
    .hr-partner-landing .trustrow img { height: 46px; width: auto; }
    .hr-partner-landing .trustrow .rt { display: inline-flex; align-items: center; gap: 6px; font-size: 15px; font-weight: 800; color: var(--ink-2); }
    .hr-partner-landing .trustrow .rt img { height: 18px; }
    .hr-partner-landing .testi-card { position: relative; display: flex; flex-direction: column; gap: 14px; }
    .hr-partner-landing .testi-card blockquote { margin: 0; font-size: 16.5px; line-height: 1.6; font-weight: 600; color: var(--ink-2); }
    .hr-partner-landing .testi-card .res { align-self: flex-start; background: var(--green-pale); color: var(--green-ink); border-radius: 999px; padding: 4px 12px; font-size: 13px; font-weight: 800; }
    .hr-partner-landing .testi-card figcaption { margin-top: auto; display: flex; align-items: center; gap: 12px; padding-top: 14px; border-top: 1px solid var(--line); }
    .hr-partner-landing .avatar { display: grid; place-items: center; flex: none; width: 48px; height: 48px; border-radius: 50%; color: #fff; font-weight: 900; }
    .hr-partner-landing .testi-card b { display: block; font-size: 15.5px; color: var(--navy); }
    .hr-partner-landing .testi-card small { display: block; font-size: 13.5px; font-weight: 600; color: var(--muted); }
    .hr-partner-landing .faq { margin: 36px auto 0; max-width: 800px; text-align: left; }
    .hr-partner-landing .faq details { margin-bottom: 10px; background: #fff; border: 1px solid var(--line); border-radius: var(--radius); }
    .hr-partner-landing .faq details[open] { border-color: var(--line-blue); box-shadow: var(--shadow); }
    .hr-partner-landing .faq summary { position: relative; list-style: none; cursor: pointer; padding: 17px 52px 17px 20px; font-size: 17px; font-weight: 800; color: var(--navy); }
    .hr-partner-landing .faq summary::-webkit-details-marker { display: none; }
    .hr-partner-landing .faq summary::after { content: "+"; position: absolute; right: 20px; top: 11px; font-size: 26px; font-weight: 600; color: var(--blue); }
    .hr-partner-landing .faq details[open] summary::after { content: "\2212"; }
    .hr-partner-landing .faq details p { padding: 0 20px 18px; font-size: 16px; line-height: 1.65; }
    .hr-partner-landing .final { padding: 64px 0; background: var(--tint); }
    .hr-partner-landing .final .box { max-width: 820px; margin: 0 auto; padding: 40px 28px; text-align: center; background: #fff; border: 1px solid var(--line-blue); border-radius: 24px; box-shadow: var(--shadow); }
    .hr-partner-landing .final .btns { margin-top: 24px; display: flex; flex-wrap: wrap; justify-content: center; gap: 12px; }
    .hr-partner-landing .final .small { margin-top: 14px; font-size: 14px; font-weight: 700; color: var(--muted); }
    .hr-partner-landing footer { padding: 24px 0 32px; border-top: 1px solid var(--line); font-size: 13.5px; color: var(--muted); }
    .hr-partner-landing footer .wrap { display: flex; flex-wrap: wrap; justify-content: space-between; gap: 8px 20px; }
    .hr-partner-landing footer nav { display: flex; gap: 16px; }
    .hr-partner-landing footer a { color: var(--muted); }
    .hr-partner-landing .sticky { display: none; }
    @media (max-width:1000px) {
      .hr-partner-landing .grid4, .hr-partner-landing .ladder { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .hr-partner-landing .grid3.prods { grid-template-columns: repeat(2, minmax(0, 1fr)); }
      .hr-partner-landing .rung.r1, .hr-partner-landing .rung.r2, .hr-partner-landing .rung.r3, .hr-partner-landing .rung.r4 { min-height: 0; }
    }
    @media (max-width:900px) {
      .hr-partner-landing .hero-grid, .hr-partner-landing .calc { grid-template-columns: minmax(0, 1fr); gap: 28px; }
      .hr-partner-landing .calc { gap: 0; }
      .hr-partner-landing .calc .out { border-left: 0; border-top: 1px solid var(--line); }
      .hr-partner-landing .hero { padding: 24px 0 40px; }
      .hr-partner-landing .grid3 { grid-template-columns: minmax(0, 1fr); }
    }
    @media (max-width:640px) {
      .hr-partner-landing { font-size: 16.5px; }
      .hr-partner-landing .logo-sub { display: none; }
      .hr-partner-landing .hero h1 { font-size: 31px; }
      .hr-partner-landing .hero .sub { font-size: 17.5px; }
      .hr-partner-landing .grid4, .hr-partner-landing .grid3.prods { grid-template-columns: minmax(0, 1fr); }
      .hr-partner-landing section { padding: 52px 0; }
      .hr-partner-landing .logo-set img { height: 36px; }
      .hr-partner-landing .logo-set { gap: 40px; padding-right: 40px; }
      .hr-partner-landing .maxbox { grid-template-columns: minmax(0, 1fr); text-align: center; }
      .hr-partner-landing .calc .in, .hr-partner-landing .calc .out { padding: 20px; }
      .hr-partner-landing .final .btns .btn { width: 100%; }
      .hr-partner-landing .sticky { display: flex; gap: 10px; position: fixed; left: 0; right: 0; bottom: 0; z-index: 45; padding: 10px 16px calc(12px + env(safe-area-inset-bottom, 0px)); background: #fff; border-top: 1px solid var(--line); transform: translateY(110%); transition: transform .2s; }
      .hr-partner-landing .sticky.show { transform: none; }
      .hr-partner-landing .sticky .btn-cta { flex: 1; }
      .hr-partner-landing footer { padding-bottom: 96px; }
    }
    @media (max-width:400px) {
      #hr-partner-cf7 .hr-role-options, .hr-partner-landing .ladder { grid-template-columns: minmax(0, 1fr); }
    }
    .hr-partner-landing .partner-stories-grid { align-items: stretch; }
    .hr-partner-landing .partner-stories-grid .testi-card { min-height: 340px; gap: 14px; }
    .hr-partner-landing .partner-review-top { display: flex; align-items: center; justify-content: space-between; gap: 12px; }
    .hr-partner-landing .partner-review-top .stars { font-size: 15px; letter-spacing: 2px; white-space: nowrap; }
    .hr-partner-landing .partner-sample { display: inline-block; padding: 3px 8px; border-radius: 6px; background: #FFF0B3; color: #694B00; font-size: 11px; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; }
    .hr-partner-landing .partner-stories-grid .testi-card blockquote { flex: 0 0 auto; margin: 0; }
    .hr-partner-landing .partner-stories-grid .testi-card .res { font-size: 13px; padding: 5px 12px; }
    .hr-partner-landing .partner-stories-grid .testi-card figcaption { margin-top: auto; min-height: 62px; }
    .hr-partner-landing .partner-stories-grid .avatar { width: 48px; height: 48px; }
    .hr-partner-landing img.avatar { object-fit: cover; background: var(--line); }
    @media (max-width:900px) {
      .hr-partner-landing .partner-stories-grid .testi-card { min-height: 0; }
    }
  </style>

  <?php get_template_part('templates/ppc/header', null, array('cta' => 'Apply now', 'href' => '#start', 'class' => 'js-start', 'attrs' => array('data-cta' => 'header_apply'), 'label' => 'Partner Program')); ?>

  <main>
    <section class="hero">
      <div class="wrap hero-grid">
        <div>
          <span class="pill" id="hero-eyebrow"><i></i>Healthray Partner Program</span>
          <h1 id="hero-title">Already work with hospitals? <span class="acc">Earn up to 40% commission, every year.</span></h1>
          <p class="sub" id="hero-sub">Introduce hospitals and clinics to Healthray. We do the demo, setup and support. You earn up to 40% of what they pay us, every year they continue.</p>
          <div class="earnchips">
            <span>Example: a ₹3 lakh hospital deal = up to <b>₹1,20,000</b> for you, every year</span>
            <span class="alt">Bigger hospital = bigger commission</span>
          </div>
          <ul class="nos">
            <li><i aria-hidden="true">✓</i>Free to join</li>
            <li><i aria-hidden="true">✓</i>No technical knowledge needed</li>
            <li><i aria-hidden="true">✓</i>Paid straight to your bank</li>
          </ul>
          <div class="trust">
            <div class="faces" aria-hidden="true">
              <img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala.webp" alt="" width="40" height="40" loading="lazy">
              <img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk.webp" alt="" width="40" height="40" loading="lazy">
              <img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt="" width="40" height="40" loading="lazy">
              <img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt="" width="40" height="40" loading="lazy">
              <img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="" width="40" height="40" loading="lazy">
            </div>
            <p><b>2,500+ hospitals and clinics</b> use Healthray<br><span class="stars">★★★★★</span> <b>4.8</b> on Google · Approved by NHA</p>
          </div>
        </div>

        <div class="form-card" id="start">
          <h2 id="form-title">Apply in 1 minute</h2>
          <p class="hint" id="form-hint">Free to join. Our partner team will call you within 1 working day.</p>
          <div id="hr-partner-form-home">
            <div id="hr-partner-cf7">
              <?php if ($hr_partner_cf7) : ?>
                <?php echo $hr_partner_cf7;
                ?>
              <?php else : ?>
                <p role="alert">Please activate Contact Form 7 to display the Partner - PPC form.</p>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="logos" aria-label="Hospitals using Healthray">
      <div class="wrap">
        <p>2,500+ hospitals and clinics use Healthray</p>
      </div>
      <div class="logo-marquee">
        <div class="logo-track">
          <?php
          $hr_logos = array(
            'Universal Multispeciality Hospital' => 'Universal Multispeciality Hospital',
            'Sri Manakula Vinayagar Hospital'   => 'Sri Manakula Vinayagar Hospital',
            'Shushrusha Hospital'                => 'Shushrusha Hospital',
            'Tanvir Hospital'                    => 'Tanvir Hospital',
            'Gastron Super Speciality Hospital' => 'Gastron Super Speciality Hospital',
            'GM Hospital'                        => 'GM Hospital',
            'Oriental Lily Hospital'             => 'Oriental Lily Hospital',
            'Lilavati Hospital'                  => 'Lilavati Hospital',
            'Shraddha Arogya Mandir'            => 'Shraddha Arogya Mandir',
            'Aatmaj Healthcare'                  => 'Aatmaj Healthcare',
          );
          for ($hr_set = 0; $hr_set < 2; $hr_set++) : ?>
            <div class="logo-set" <?php echo $hr_set ? ' aria-hidden="true"' : ''; ?>>
              <?php foreach ($hr_logos as $hr_name => $hr_label) :
                $logo_url = $hr_existing_media([$hr_name . ' logo', $hr_name, str_replace(' Hospital', '', $hr_name) . ' logo']);
                if ($logo_url) : ?>
                  <img src="<?php echo $logo_url; ?>" alt="<?php echo $hr_set ? '' : esc_attr($hr_label); ?>" height="46" loading="eager" decoding="async">
              <?php endif;
              endforeach; ?>
            </div>
          <?php endfor; ?>
        </div>
      </div>
    </section>

    <section class="center">
      <div class="wrap">
        <h2 class="h2">How it works</h2>
        <p class="lead">You don’t need to know software. You only need to know the hospital.</p>
        <div class="grid3">
          <div class="card step"><span class="no" aria-hidden="true">1</span><span class="ic"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                <circle cx="9" cy="7" r="4" />
                <path d="M19 8v6M22 11h-6" />
              </svg></span>
            <h3>You introduce</h3>
            <p>Tell us about a hospital or clinic you know. It’s saved in your name.</p>
          </div>
          <div class="card step"><span class="no" aria-hidden="true">2</span><span class="ic"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="2" y="3" width="20" height="14" rx="2" />
                <path d="M8 21h8M12 17v4" />
              </svg></span>
            <h3>We do the work</h3>
            <p>Our team gives the demo, does the setup, trains the staff and gives support.</p>
          </div>
          <div class="card step"><span class="no" aria-hidden="true">3</span><span class="ic"><svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M6 3h12M6 8h12M6 13l8.5 8M6 13h3c6.7 0 6.7-10 0-10" />
              </svg></span>
            <h3>You earn every year</h3>
            <p>When the hospital pays, you get your commission. Again every year they use Healthray.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="tint center">
      <div class="wrap">
        <span class="eyebrow">Why now</span>
        <h2 class="h2" style="margin-top:10px">Hospitals near you <span class="acc">need this software now</span></h2>
        <p class="lead">You won’t be selling something nobody wants. Here’s why owners are saying yes.</p>
        <div class="grid3">
          <div class="card"><span class="big">ABDM</span>
            <h3>The government wants every hospital digital</h3>
            <p>Under Ayushman Bharat Digital Mission, hospitals are moving patient records online and linking ABHA IDs. They need approved software for it.</p>
          </div>
          <div class="card"><span class="big">17,000</span>
            <h3>Very few private hospitals are on it yet</h3>
            <p>Out of 1.3 lakh facilities with ABDM software in 2024, only about 17,000 were private. The rest are still on paper or old software.</p>
          </div>
          <div class="card"><span class="big">₹20</span>
            <h3>Hospitals even get paid to use it</h3>
            <p>Under the government’s DHIS scheme, hospitals with 10+ beds can get ₹20 for each eligible digital record. Healthray is approved by NHA.</p>
          </div>
        </div>
        <p class="src">Sources: Ministry of Health (MoHFW) press release, Oct 2024 · NHA DHIS guidelines</p>
      </div>
    </section>

    <section class="center" id="earn">
      <div class="wrap">
        <span class="eyebrow">Your earnings</span>
        <h2 class="h2" style="margin-top:10px">The bigger the hospital, <span class="acc">the bigger your commission</span></h2>
        <p class="lead">Every hospital pays a different price, based on beds, modules and setup. You get up to 40% of what they pay, every year they continue.</p>
        <div class="ladder">
          <div class="card rung r1"><span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3" />
                <path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4" />
                <circle cx="20" cy="10" r="2" />
              </svg></span>
            <h3>Clinic</h3><small>1–3 doctors</small>
            <p class="deal">Plans from <b>₹30,000/year</b></p>
            <p class="you">up to ₹12,000 <small>/year</small></p>
          </div>
          <div class="card rung r2"><span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 21h18" />
                <path d="M6 21V9l6-4 6 4v12" />
                <path d="M12 9v4M10 11h4" />
              </svg></span>
            <h3>Small hospital</h3><small>Up to 30 beds</small>
            <p class="deal">Example deal <b>₹1 lakh/year</b></p>
            <p class="you">up to ₹40,000 <small>/year</small></p>
          </div>
          <div class="card rung r3"><span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 21h18" />
                <path d="M5 21V7l7-4 7 4v14" />
                <path d="M12 7v4M10 9h4" />
                <path d="M9 21v-5h6v5" />
              </svg></span>
            <h3>Mid-size hospital</h3><small>30–100 beds</small>
            <p class="deal">Example deal <b>₹3 lakh/year</b></p>
            <p class="you">up to ₹1,20,000 <small>/year</small></p>
          </div>
          <div class="card rung r4"><span class="ic"><svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 21h18" />
                <path d="M4 21V10h5v11M10 21V4h5v17M16 21v-8h4v8" />
              </svg></span>
            <h3>Big hospital or chain</h3><small>100+ beds or many branches</small>
            <p class="deal">Example deal <b>₹10 lakh/year</b></p>
            <p class="you">up to ₹4,00,000 <small>/year</small></p>
          </div>
        </div>
        <p class="ladnote">You earn up to 40% of each deal. Hospital deal sizes are examples; the real price depends on beds, modules and setup.</p>

        <div class="maxbox">
          <div class="amt"><span id="calc-top">₹2,10,000</span><small> /month</small></div>
          <p><b>What a top partner can reach by year 3.</b> Bring 5 mid-size hospitals and 20 clinics a year, and your yearly commission keeps adding up.</p>
        </div>

        <div class="calc" id="calc">
          <div class="in">
            <p class="q">Work out your own earnings</p>
            <div class="chiprow">
              <button type="button" class="chipbtn preset" data-h="1" data-c="5">Part-time: 1 hospital + 5 clinics</button>
              <button type="button" class="chipbtn preset on" data-h="3" data-c="10">Serious: 3 hospitals + 10 clinics</button>
              <button type="button" class="chipbtn preset" data-h="5" data-c="20">Top partner: 5 hospitals + 20 clinics</button>
            </div>
            <p class="lab">Size of your hospitals<small>Pick the size most of your hospitals are</small></p>
            <div class="chiprow" style="margin-top:8px">
              <button type="button" class="chipbtn size" data-deal="100000">Small (₹1 lakh deal)</button>
              <button type="button" class="chipbtn size on" data-deal="300000">Mid-size (₹3 lakh deal)</button>
              <button type="button" class="chipbtn size" data-deal="1000000">Big (₹10 lakh deal)</button>
            </div>
            <div class="sl">
              <label for="calc-hosp"><span>Hospitals you bring each year</span><output id="calc-hosp-out" for="calc-hosp">3</output></label>
              <input id="calc-hosp" type="range" min="0" max="20" step="1" value="3">
            </div>
            <div class="sl">
              <label for="calc-clin"><span>Clinics you bring each year</span><output id="calc-clin-out" for="calc-clin">10</output></label>
              <input id="calc-clin" type="range" min="0" max="50" step="1" value="10">
            </div>
          </div>
          <div class="out" aria-live="polite">
            <p class="tl">Your commission in 3 years</p>
            <p class="total" id="calc-total">₹28,80,000</p>
            <p class="mo">That’s about <b id="calc-month">₹1,20,000 a month</b> by year 3</p>
            <div class="bars">
              <div class="bar"><span>Year 1</span><span class="tr"><i id="calc-bar1" style="width:33%"></i></span><b id="calc-y1">₹4,80,000</b></div>
              <div class="bar"><span>Year 2</span><span class="tr"><i id="calc-bar2" style="width:67%"></i></span><b id="calc-y2">₹9,60,000</b></div>
              <div class="bar y3"><span>Year 3</span><span class="tr"><i style="width:100%"></i></span><b id="calc-y3">₹14,40,000</b></div>
            </div>
            <p class="note">Example at up to 40%, if every client continues each year. Clinics at ₹30,000/year. Hospital sizes use the example deals above. Your exact % is written in your agreement.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="tint center">
      <div class="wrap">
        <span class="eyebrow">What you’ll sell</span>
        <h2 class="h2" style="margin-top:10px">One company, software for <span class="acc">every hospital and clinic</span></h2>
        <p class="lead">Every client gets a price that fits their size. You earn up to 40% on whatever they buy, every year.</p>
        <div class="grid3 prods">
          <div class="card prod">
            <div class="head"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M3 21h18" />
                  <path d="M5 21V7l7-4 7 4v14" />
                  <path d="M10 9h4M12 7v4" />
                  <path d="M9 21v-5h6v5" />
                </svg></span>
              <div>
                <h3>Hospital software</h3>
                <p class="who">Hospitals and nursing homes</p>
              </div>
            </div>
            <ul>
              <li>OPD</li>
              <li>IPD and beds</li>
              <li>Billing</li>
              <li>Pharmacy</li>
              <li>Lab</li>
              <li>ABDM</li>
            </ul>
            <p class="price">Price by beds and modules · <b>You earn up to 40%</b></p>
          </div>
          <div class="card prod">
            <div class="head"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M4.8 2.3A.3.3 0 1 0 5 2H4a2 2 0 0 0-2 2v5a6 6 0 0 0 6 6 6 6 0 0 0 6-6V4a2 2 0 0 0-2-2h-1a.2.2 0 1 0 .3.3" />
                  <path d="M8 15v1a6 6 0 0 0 6 6 6 6 0 0 0 6-6v-4" />
                  <circle cx="20" cy="10" r="2" />
                </svg></span>
              <div>
                <h3>Clinic software</h3>
                <p class="who">Doctors and clinics</p>
              </div>
            </div>
            <ul>
              <li>Appointments</li>
              <li>Prescriptions</li>
              <li>Billing</li>
              <li>Reminders</li>
            </ul>
            <p class="price">From ₹30,000/year · <b>You earn up to 40%</b></p>
          </div>
          <div class="card prod">
            <div class="head"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2" />
                  <circle cx="9" cy="7" r="4" />
                  <path d="M23 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8" />
                </svg></span>
              <div>
                <h3>Polyclinic software</h3>
                <p class="who">Clinics with many doctors</p>
              </div>
            </div>
            <ul>
              <li>OPD</li>
              <li>EMR</li>
              <li>Lab</li>
              <li>Pharmacy</li>
              <li>Beds</li>
            </ul>
            <p class="price">Price by doctors and modules · <b>You earn up to 40%</b></p>
          </div>
          <div class="card prod">
            <div class="head"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                  <path d="M8 9h8M8 13h5" />
                </svg></span>
              <div>
                <h3>Patient CRM</h3>
                <p class="who">Hospitals that want more patients</p>
              </div>
            </div>
            <ul>
              <li>Leads</li>
              <li>Follow-ups</li>
              <li>Calls</li>
              <li>WhatsApp and SMS</li>
            </ul>
            <p class="price">Price by team size · <b>You earn up to 40%</b></p>
          </div>
          <div class="card prod">
            <div class="head"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M3 10l9-7 9 7v10a1 1 0 0 1-1 1H4a1 1 0 0 1-1-1z" />
                  <path d="M12 11v6M9 14h6" />
                </svg></span>
              <div>
                <h3>Home care software</h3>
                <p class="who">Home nursing and ICU at home</p>
              </div>
            </div>
            <ul>
              <li>Care plans</li>
              <li>Visits</li>
              <li>Equipment</li>
              <li>Billing</li>
            </ul>
            <p class="price">Price by team size · <b>You earn up to 40%</b></p>
          </div>
          <div class="card prod">
            <div class="head"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                  <path d="M3 21h18" />
                  <path d="M4 21V10h5v11M10 21V4h5v17M16 21v-8h4v8" />
                </svg></span>
              <div>
                <h3>Hospital chains</h3>
                <p class="who">Groups with many branches</p>
              </div>
            </div>
            <ul>
              <li>All branches in one system</li>
              <li>Central reports</li>
            </ul>
            <p class="price">Every branch adds up · <b>Your biggest deals</b></p>
          </div>
        </div>
        <div class="trustrow">
          <?php foreach (
            array(
              'NHA' => ['NHA Approved', 'NHA', 'National Health Authority'],
              'ABDM' => ['ABDM', 'ABDM compliant'],
              'NABH' => ['NABH', 'NABH Certified'],
              'ISO 27001' => ['ISO 27001', 'ISO 27001 certified information security']
            ) as $badge => $match
          ) :
            $badge_url = $hr_existing_media($match);
            if ($badge_url) : ?>
              <img src="<?php echo $badge_url; ?>" alt="<?php echo esc_attr($badge); ?>" height="46" loading="lazy">
            <?php else : ?>
              <span class="rt"><?php echo esc_html($badge); ?></span>
          <?php endif;
          endforeach; ?>
          <span class="rt"><span class="stars">★</span> 4.8 Capterra</span>
          <span class="rt"><span class="stars">★</span> 4.8 SoftwareSuggest</span>
          <span class="rt"><span class="stars">★</span> 5.0 G2</span>
        </div>
      </div>
    </section>

    <section class="center">
      <div class="wrap">
        <h2 class="h2">Our 4 promises to you</h2>
        <p class="lead">We know your biggest worries. Here’s our answer to each one.</p>
        <div class="grid4">
          <div class="card"><span class="ic green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 3l8 4v5c0 5-3.5 8-8 9-4.5-1-8-4-8-9V7z" />
                <path d="M9 12l2 2 4-4" />
              </svg></span>
            <h3>The hospital stays yours</h3>
            <p>Once you add a hospital, it’s saved in your name. If they contact us directly, you still get your commission.</p>
          </div>
          <div class="card"><span class="ic green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="5" width="18" height="16" rx="2" />
                <path d="M16 3v4M8 3v4M3 11h18" />
              </svg></span>
            <h3>Paid on time</h3>
            <p>Your commission goes straight to your bank after the hospital pays. The payment date is fixed in your agreement.</p>
          </div>
          <div class="card"><span class="ic green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M3 18v-6a9 9 0 0 1 18 0v6" />
                <path d="M21 19a2 2 0 0 1-2 2h-1v-6h3zM3 19a2 2 0 0 0 2 2h1v-6H3z" />
              </svg></span>
            <h3>We handle support</h3>
            <p>If the hospital needs help, our support team takes the call. Your name stays safe.</p>
          </div>
          <div class="card"><span class="ic green"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z" />
                <path d="M14 2v6h6M8 13h8M8 17h5" />
              </svg></span>
            <h3>Everything in writing</h3>
            <p>A simple agreement with your commission, your hospitals and your payment date.</p>
          </div>
        </div>
      </div>
    </section>

    <?php if ($hr_show_testimonials) : ?>
      <section class="tint center" aria-labelledby="hr-partner-stories-title">
        <div class="wrap">
          <span class="eyebrow">Partner stories</span>
          <h2 class="h2" id="hr-partner-stories-title" style="margin-top:10px">People like you, earning <span class="acc">every year</span></h2>
          <div class="grid3 partner-stories-grid">
            <figure class="card testi-card" style="margin:0">
              <div class="partner-review-top"><span class="stars" aria-label="Five stars">★★★★★</span></div>
              <blockquote>“One hospital I introduced called Healthray directly. They sent it back to me, and I still got my full commission. That’s when I started trusting them.”</blockquote>
              <span class="res">4 hospitals live</span>
              <figcaption><img class="avatar" src="<?php echo $hr_img('karthik-r-partner-testimonial.webp'); ?>" alt="" width="48" height="48" loading="lazy" decoding="async"><span><b>Karthik R.</b><small>IT and networking firm, Coimbatore</small></span></figcaption>
            </figure>
            <figure class="card testi-card" style="margin:0">
              <div class="partner-review-top"><span class="stars" aria-label="Five stars">★★★★★</span></div>
              <blockquote>“I’m a medical rep, not a software person. I just introduced doctors I already meet every week. Healthray’s team did the demo and everything after that.”</blockquote>
              <span class="res">6 clinics live</span>
              <figcaption><img class="avatar" src="<?php echo $hr_img('venkatesh-p-partner-testimonial.webp'); ?>" alt="" width="48" height="48" loading="lazy" decoding="async"><span><b>Venkatesh P.</b><small>Medical rep, Vijayawada</small></span></figcaption>
            </figure>
            <figure class="card testi-card" style="margin:0">
              <div class="partner-review-top"><span class="stars" aria-label="Five stars">★★★★★</span></div>
              <blockquote>“With Tally, I earn once per sale. Here my clinics renew every year and the commission comes again. It’s like a second income.”</blockquote>
              <span class="res">2 hospitals and 7 clinics live</span>
              <figcaption><img class="avatar" src="<?php echo $hr_img('anil-kumar-m-partner-testimonial.webp'); ?>" alt="" width="48" height="48" loading="lazy" decoding="async"><span><b>Anil Kumar M.</b><small>Software dealer, Kochi</small></span></figcaption>
            </figure>
          </div>
        </div>
      </section>
    <?php endif; ?>

    <section class="<?php echo $hr_show_testimonials ? '' : 'tint '; ?>center">
      <div class="wrap">
        <h2 class="h2">Who can become a partner?</h2>
        <p class="lead">Anyone who already has the trust of hospitals, clinics or doctors.</p>
        <div class="grid4">
          <div class="card"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <rect x="2" y="3" width="20" height="14" rx="2" />
                <path d="M8 21h8M12 17v4" />
              </svg></span>
            <h3>IT and computer vendors</h3>
            <p>You already look after their computers, network or CCTV.</p>
          </div>
          <div class="card"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M4 4h16v16H4z" />
                <path d="M8 9h8M8 13h8M8 17h5" />
              </svg></span>
            <h3>Software dealers</h3>
            <p>You already sell Tally, Marg or Busy to clinics and chemists.</p>
          </div>
          <div class="card"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M20 7h-4V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v2H4a1 1 0 0 0-1 1v11a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V8a1 1 0 0 0-1-1z" />
                <path d="M10 7V5h4v2" />
                <path d="M12 11v5M9.5 13.5h5" />
              </svg></span>
            <h3>Medical reps</h3>
            <p>You meet doctors every day, and they trust you.</p>
          </div>
          <div class="card"><span class="ic"><svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M9 11l3 3L22 4" />
                <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11" />
              </svg></span>
            <h3>Healthcare consultants</h3>
            <p>Hospitals already ask you which software to buy.</p>
          </div>
        </div>
      </div>
    </section>

    <section class="<?php echo $hr_show_testimonials ? 'tint ' : ''; ?>center" id="faq">
      <div class="wrap">
        <h2 class="h2">Questions people ask us</h2>
        <div class="faq">
          <details name="hr-partner-faq" open>
            <summary>Is this a job or MLM?</summary>
            <p>Neither. It’s a business partnership. You earn only when a hospital you introduce pays for Healthray. There’s no salary, no joining fee, and no adding other people under you.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>Do I have to pay anything to join?</summary>
            <p>No. It’s free to join. You don’t pay anything to become a partner.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>How much can I earn from one hospital?</summary>
            <p>It depends on the hospital. Price is based on beds, modules and setup. Clinic plans start at ₹30,000 a year, and bigger hospitals pay much more. You get up to 40% of what they pay, every year they continue.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>Why do you say “up to 40%”?</summary>
            <p>40% is the highest commission. Our partner team tells you your exact % on the first call, and it’s written in your agreement before you start.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>I don’t know software. Can I still join?</summary>
            <p>Yes. You only introduce the hospital. Our team gives the demo and does everything after that.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>How and when will I get paid?</summary>
            <p>Straight to your bank account, after the hospital pays. The payment date is fixed in your agreement. TDS is cut as per law.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>What if the hospital contacts Healthray directly?</summary>
            <p>If you added that hospital first, it’s yours. You still get your commission.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>Can I keep doing my current business?</summary>
            <p>Yes. You can do this alongside your own work.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>Who is Healthray?</summary>
            <p>Healthray Technologies Pvt. Ltd., based in Surat. Since 2019, 2,500+ hospitals and clinics have started using our software. We are approved by NHA for ABDM.</p>
          </details>
          <details name="hr-partner-faq">
            <summary>What happens after I apply?</summary>
            <p>Our partner team calls you within 1 working day. We explain everything, answer your questions and send you the agreement. No pressure.</p>
          </details>
        </div>
      </div>
    </section>

    <section class="final">
      <div class="wrap">
        <div class="box">
          <h2 class="h2">Start earning from the hospitals <span class="acc">you already know</span></h2>
          <div class="btns" style="justify-content:center">
            <a class="btn btn-cta js-start" href="#start" data-cta="final_apply">Apply now, it’s free</a>
          </div>
          <p class="small">Free to join · We call you back within 1 working day · Or call +91 97148 74435</p>
        </div>
      </div>
    </section>
  </main>

  <?php get_template_part('templates/ppc/footer'); ?>

  <div class="sticky" id="sticky" aria-hidden="true">
    <a class="btn btn-cta js-start" href="#start" data-cta="sticky_apply" tabindex="-1">Apply now, it’s free</a>
  </div>

  <dialog id="hr-partner-modal" aria-labelledby="hr-partner-modal-title" aria-describedby="hr-partner-modal-hint">
    <button type="button" class="hr-partner-modal-close" aria-label="Close form">×</button>
    <h2 id="hr-partner-modal-title">Apply in 1 minute</h2>
    <p class="hint" id="hr-partner-modal-hint">Free to join. Our partner team will call you within 1 working day.</p>
    <div id="hr-partner-modal-slot"></div>
  </dialog>

  <script>
    (function() {
      'use strict';
      var VARIANTS = {
        callout: {
          h: 'Already work with hospitals? ',
          a: 'Earn up to 40% commission, every year.'
        },
        asset: {
          h: 'Your hospital contacts can ',
          a: 'pay you every year.'
        },
        insight: {
          h: 'Hospitals ask you which software to buy. ',
          a: 'Now get paid for it.'
        },
        number: {
          h: 'One hospital deal can pay you ',
          a: 'up to ₹1,20,000 every year.',
          s: 'Introduce hospitals and clinics to Healthray. We do the demo, setup and support. On a ₹3 lakh hospital deal, you earn up to ₹1,20,000, every year they continue.'
        }
      };
      var params = new URLSearchParams(location.search);
      var v = (params.get('v') || 'callout').toLowerCase();
      if (!VARIANTS[v]) v = 'callout';
      var title = document.getElementById('hero-title');
      title.textContent = VARIANTS[v].h;
      var accent = document.createElement('span');
      accent.className = 'acc';
      accent.textContent = VARIANTS[v].a;
      title.appendChild(accent);
      if (VARIANTS[v].s) document.getElementById('hero-sub').textContent = VARIANTS[v].s;

      window.dataLayer = window.dataLayer || [];

      function track(event, data) {
        dataLayer.push(Object.assign({
          event: event,
          segment: 'partner',
          channel: 'meta',
          variant: v
        }, data || {}));
      }
      var reduceMotion = matchMedia('(prefers-reduced-motion: reduce)').matches;

      var formHost = document.getElementById('hr-partner-cf7');
      var formHome = document.getElementById('hr-partner-form-home');
      var dialog = document.getElementById('hr-partner-modal');
      var modalSlot = document.getElementById('hr-partner-modal-slot');
      var previousFocus = null;
      var activeLocation = 'hero';

      function setTracking(loc) {
        activeLocation = loc || '';
        var values = {
          variant: v,
          cta_type: 'apply',
          cta_location: loc || ''
        };
        ['gclid', 'fbclid', 'utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function(k) {
          var val = params.get(k);
          if (val) values[k] = val.slice(0, 200);
        });
        formHost.querySelectorAll('input[type="hidden"]').forEach(function(input) {
          var key = input.name.replace(/_cf7$/, '');
          if (Object.prototype.hasOwnProperty.call(values, key)) input.value = values[key];
        });
      }
      setTracking('hero');

      function openForm(e, loc) {
        e.preventDefault();
        previousFocus = e.currentTarget;
        setTracking(loc);
        track('cta_click', {
          cta_type: 'apply',
          cta_location: loc
        });
        if (typeof dialog.showModal !== 'function') {
          document.getElementById('start').scrollIntoView({
            behavior: reduceMotion ? 'auto' : 'smooth'
          });
          return;
        }
        modalSlot.appendChild(formHost);
        dialog.showModal();
        document.documentElement.classList.add('hr-partner-modal-open');
        focusFormStep();
      }
      document.querySelectorAll('.js-start').forEach(function(el) {
        el.setAttribute('aria-haspopup', 'dialog');
        el.setAttribute('aria-controls', 'hr-partner-modal');
        el.addEventListener('click', function(e) {
          openForm(e, el.dataset.cta);
        });
      });
      dialog.querySelector('.hr-partner-modal-close').addEventListener('click', function() {
        dialog.close();
      });
      dialog.addEventListener('click', function(e) {
        if (e.target !== dialog) return;
        var r = dialog.getBoundingClientRect();
        if (e.clientX < r.left || e.clientX > r.right || e.clientY < r.top || e.clientY > r.bottom) dialog.close();
      });
      dialog.addEventListener('close', function() {
        formHome.appendChild(formHost);
        setTracking('hero');
        document.documentElement.classList.remove('hr-partner-modal-open');
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
          form: 'partner_ppc'
        });
      });

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
        var roleInput = cf7Form.querySelector('input[name="speciality"]');
        var roleButtons = stepOne.querySelectorAll('.hr-role-option');
        var roleBox = stepOne.querySelector('.hr-role');
        var roleError = stepOne.querySelector('.hr-role-err');
        var back = stepTwo.querySelector('.hr-back');
        var stepLabel = formHost.querySelector('.hr-fsteps-label');
        var pickedLabel = stepTwo.querySelector('.hr-picked-label');

        var countError = stepTwo.querySelector('.hr-count-err');
        var countInput = cf7Form.querySelector('input[name="bed_size"]');
        var countButtons = stepTwo.querySelectorAll('.hr-count-option');
        var countAllowed = ['1–5', '6–20', 'More than 20', 'None yet'];

        function countValue() {
          if (!countInput) return '';
          var checked = cf7Form.querySelector('input[name="bed_size"]:checked');
          if (checked) return checked.value;
          return countInput.type === 'radio' ? '' : countInput.value;
        }

        function validHospitalCount(showError) {
          if (!countButtons.length) return true;
          var selected = countAllowed.indexOf(countValue()) !== -1;
          if (countError && showError) countError.hidden = selected;
          return selected;
        }
        countButtons.forEach(function(btn) {
          btn.addEventListener('click', function() {
            countButtons.forEach(function(b) {
              var active = b === btn;
              b.classList.toggle('is-selected', active);
              b.setAttribute('aria-pressed', active ? 'true' : 'false');
            });
            if (countInput) {
              countInput.value = btn.dataset.value;
              countInput.dispatchEvent(new Event('change', {
                bubbles: true
              }));
            }
            if (countError) countError.hidden = true;
          });
        });

        var mobileField = cf7Form.querySelector('input[name="your-number"]');
        var mobileError = stepTwo.querySelector('.hr-mobile-err');

        function normalizeMobile(value) {
          var digits = value.replace(/\D/g, '');
          if (digits.length === 12 && digits.slice(0, 2) === '91') return digits.slice(2);
          if (digits.length === 11 && digits.charAt(0) === '0') return digits.slice(1);
          return digits.slice(0, 10);
        }
        cf7Form.addEventListener('formdata', function(event) {
          var digits = mobileField ? normalizeMobile(mobileField.value) : '';
          event.formData.set('your-number', /^\d{10}$/.test(digits) ? '+91 ' + digits : '');
          if (countInput) event.formData.set('bed_size', countValue());
          if (roleInput) event.formData.set('speciality', roleInput.value);
        });

        var status = document.createElement('span');
        status.setAttribute('role', 'status');
        status.setAttribute('aria-live', 'polite');
        status.style.cssText = 'position:absolute;width:1px;height:1px;padding:0;overflow:hidden;clip-path:inset(50%);white-space:nowrap';
        formHost.appendChild(status);

        function chosenRole() {
          return roleInput && roleInput.value ? roleInput.value : '';
        }

        function clearRole() {
          if (roleInput) roleInput.value = '';
          roleButtons.forEach(function(b) {
            b.classList.remove('is-selected');
            b.setAttribute('aria-pressed', 'false');
          });
        }

        function showStep(step, focus) {
          currentStep = step;
          formHost.dataset.step = String(step);
          stepOne.hidden = step !== 1;
          stepTwo.hidden = step !== 2;
          var text = 'Step ' + step + ' of 2';
          if (stepLabel) stepLabel.textContent = text;
          status.textContent = text;
          if (pickedLabel) pickedLabel.textContent = chosenRole();
          if (focus) focusFormStep();
        }
        roleButtons.forEach(function(btn) {
          btn.addEventListener('click', function() {
            roleButtons.forEach(function(b) {
              var on = b === btn;
              b.classList.toggle('is-selected', on);
              b.setAttribute('aria-pressed', on ? 'true' : 'false');
            });
            if (roleInput) roleInput.value = btn.dataset.value;
            if (roleError) roleError.hidden = true;
            if (roleBox) roleBox.classList.remove('invalid');
            if (!started) {
              started = true;
              track('form_start');
            }
            track('form_step', {
              step: 2,
              partner_type: btn.dataset.value
            });
            showStep(2, true);
          });
        });
        if (back) back.addEventListener('click', function() {
          showStep(1, true);
        });

        function validMobile() {
          var valid = !!mobileField && /^\d{10}$/.test(mobileField.value);
          if (mobileError) {
            mobileError.hidden = valid;
            mobileError.textContent = 'Please enter a 10-digit WhatsApp number.';
          }
          if (mobileField) {
            mobileField.setCustomValidity(valid ? '' : 'Please enter a 10-digit WhatsApp number.');
            mobileField.setAttribute('aria-invalid', valid ? 'false' : 'true');
          }
          return valid;
        }
        var nameField = cf7Form.querySelector('input[name="your-name"]');
        if (nameField && !nameField.getAttribute('autocomplete')) nameField.setAttribute('autocomplete', 'name');
        var emailField = cf7Form.querySelector('input[name="your-email"]');
        if (emailField && !emailField.getAttribute('autocomplete')) emailField.setAttribute('autocomplete', 'email');
        var cityField = cf7Form.querySelector('input[name="your-city"]');
        if (cityField && !cityField.getAttribute('autocomplete')) cityField.setAttribute('autocomplete', 'address-level2');
        if (mobileField) {
          if (!mobileField.getAttribute('autocomplete')) mobileField.setAttribute('autocomplete', 'tel-national');
          mobileField.setAttribute('inputmode', 'numeric');
          mobileField.setAttribute('pattern', '[0-9]{10}');
          mobileField.removeAttribute('maxlength');
          mobileField.value = normalizeMobile(mobileField.value);
          if (mobileError) {
            mobileError.id = 'hr-partner-mobile-error';
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

        [nameField, emailField, cityField, mobileField].forEach(function(field) {
          if (!field) return;
          field.addEventListener('input', function() {
            var okay = field.value.trim() !== '';
            if (field === emailField) okay = field.validity.valid;
            if (field === mobileField) okay = /^\d{10}$/.test(field.value);
            if (!okay) return;
            field.classList.remove('wpcf7-not-valid');
            field.removeAttribute('aria-invalid');
            var wrap = field.closest('.wpcf7-form-control-wrap');
            if (wrap) {
              var tip = wrap.querySelector('.wpcf7-not-valid-tip');
              if (tip) tip.remove();
            }
            if (field === mobileField && mobileError) mobileError.hidden = true;
          });
        });

        cf7Form.addEventListener('submit', function(e) {
          if (currentStep === 1 || !chosenRole()) {
            e.preventDefault();
            e.stopImmediatePropagation();
            showStep(1, true);
            if (roleError) roleError.hidden = false;
            if (roleBox) roleBox.classList.add('invalid');
            return;
          }
          var mobileValid = validMobile();
          var countValid = validHospitalCount(true);
          if (!mobileValid || !countValid) {
            e.preventDefault();
            e.stopImmediatePropagation();
            if (!mobileValid && mobileField) mobileField.focus();
            else if (!countValid && countButtons.length) countButtons[0].focus();
          }
        }, true);

        cf7Form.addEventListener('reset', function() {
          setTimeout(function() {
            clearRole();
            if (roleError) roleError.hidden = true;
            if (roleBox) roleBox.classList.remove('invalid');
            if (mobileError) mobileError.hidden = true;
            if (countError) countError.hidden = true;
            if (countInput && countInput.type !== 'radio') countInput.value = '';
            countButtons.forEach(function(btn) {
              btn.classList.remove('is-selected');
              btn.setAttribute('aria-pressed', 'false');
            });
            if (mobileField) {
              mobileField.setCustomValidity('');
              mobileField.removeAttribute('aria-invalid');
            }
            showStep(1, false);
            setTracking(activeLocation);
          }, 0);
        });

        formHost.addEventListener('wpcf7invalid', function(e) {
          var invalid = e.detail && e.detail.apiResponse && e.detail.apiResponse.invalid_fields || [];
          var roleInvalid = invalid.some(function(field) {
            return field.field === 'speciality';
          });
          showStep(roleInvalid ? 1 : 2, true);
        });

        showStep(1, false);
      }

      var RATE = 0.40;
      var CLINIC_DEAL = 30000;
      var calc = {
        hosp: 3,
        clin: 10,
        deal: 300000
      };
      var hospRange = document.getElementById('calc-hosp');
      var clinRange = document.getElementById('calc-clin');

      function inr(n) {
        return '₹' + Math.round(n).toLocaleString('en-IN');
      }

      function setText(id, text) {
        var el = document.getElementById(id);
        if (el) el.textContent = text;
      }

      function updateCalc() {
        var yearly = calc.hosp * calc.deal * RATE + calc.clin * CLINIC_DEAL * RATE;
        var y1 = yearly,
          y2 = 2 * yearly,
          y3 = 3 * yearly;
        setText('calc-hosp-out', calc.hosp);
        setText('calc-clin-out', calc.clin);
        setText('calc-y1', inr(y1));
        setText('calc-y2', inr(y2));
        setText('calc-y3', inr(y3));
        setText('calc-total', inr(y1 + y2 + y3));
        setText('calc-month', inr(y3 / 12) + ' a month');
        document.getElementById('calc-bar1').style.width = y3 ? Math.round(y1 / y3 * 100) + '%' : '0%';
        document.getElementById('calc-bar2').style.width = y3 ? Math.round(y2 / y3 * 100) + '%' : '0%';
        document.querySelectorAll('#calc .preset').forEach(function(b) {
          b.classList.toggle('on', +b.dataset.h === calc.hosp && +b.dataset.c === calc.clin);
        });
        document.querySelectorAll('#calc .size').forEach(function(b) {
          b.classList.toggle('on', +b.dataset.deal === calc.deal);
        });
      }
      var calcUsed = false;

      function calcTouched() {
        if (!calcUsed) {
          calcUsed = true;
          track('calculator_use');
        }
      }
      document.querySelectorAll('#calc .preset').forEach(function(b) {
        b.addEventListener('click', function() {
          calc.hosp = +b.dataset.h;
          calc.clin = +b.dataset.c;
          hospRange.value = calc.hosp;
          clinRange.value = calc.clin;
          calcTouched();
          updateCalc();
        });
      });
      document.querySelectorAll('#calc .size').forEach(function(b) {
        b.addEventListener('click', function() {
          calc.deal = +b.dataset.deal;
          calcTouched();
          updateCalc();
        });
      });
      hospRange.addEventListener('input', function() {
        calc.hosp = +hospRange.value;
        calcTouched();
        updateCalc();
      });
      clinRange.addEventListener('input', function() {
        calc.clin = +clinRange.value;
        calcTouched();
        updateCalc();
      });
      setText('calc-top', inr(3 * (5 * 300000 * RATE + 20 * CLINIC_DEAL * RATE) / 12));
      updateCalc();

      var faqItems = document.querySelectorAll('.hr-partner-landing #faq .faq details');
      faqItems.forEach(function(item) {
        item.addEventListener('toggle', function() {
          if (!item.open) return;
          faqItems.forEach(function(other) {
            if (other !== item) other.open = false;
          });
        });
      });

      var sticky = document.getElementById('sticky');
      var heroForm = document.getElementById('start');
      var stickyShown = false,
        ticking = false;

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

      var yr = document.getElementById('yr');
      if (yr) yr.textContent = new Date().getFullYear();
    })();
  </script>
  <?php wp_footer(); ?>
</body>

</html>