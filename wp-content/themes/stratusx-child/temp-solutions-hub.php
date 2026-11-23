<?php
/**
 * Template Name: Solutions Hub
 * Template Post Type: page
 *
 * /solutions/ (page 82549): the hub for the solution pages under it (small hospitals & nursing homes,
 * multispeciality hospitals, hospital chains & groups). Card text comes from those pages.
 * Everything for this page is in this ONE file: cards, schema, CSS (<style>) and JS (<script>).
 * The Roots wrapper prints <head>, the site header and footer, so no wp_head / enqueue hooks here.
 * CSS is scoped to .sxh, and to #myPopup for the popup. Yoast handles title, meta, canonical and base schema.
 *
 * Lead form: the PPC Form (CF7 11eec7d), in the hero card and in the site popup (#myPopup), as on the solution
 * pages. Every "Book a demo" button has .hr-cta-btn, so js/script.js opens that popup (hr_ppc_popup_args).
 */
defined('ABSPATH') || exit;

$sxh_up   = 'https://healthray.com/wp-content/uploads/';
$sxh_form = '[contact-form-7 id="11eec7d" title="PPC Form"]';

// Site popup: same form, heading and line as the hero card.
add_filter('hr_ppc_popup_args', static function () use ($sxh_form) {
	return array(
		'form'  => $sxh_form,
		'title' => 'Book a free demo',
		'text'  => "A free 30-minute demo, set up for your hospital's size.",
	);
});

// One card per solution page (text from each page's own hero and sections)
$sxh_cards = array(
	array(
		'size'  => '10 to 50 beds',
		'title' => 'Small hospitals & nursing homes',
		'text'  => 'Run your small hospital or nursing home on one simple software.',
		'ticks' => array('OPD, admissions, billing, cashless claims and pharmacy in one place', 'Find a free bed without phoning the ward', 'Discharge papers and the cashless file, ready on time'),
		'img'   => '2026/11/ward-peach.webp',
		'alt'   => 'A patient file with doctor notes, medication sheet and vital sheet',
		'url'   => home_url('/solutions/small-hospitals-nursing-homes/'),
		'cta'   => 'See small hospitals',
		'tone'  => 'peach',
	),
	array(
		'size'  => '50 beds or more',
		'title' => 'Multispeciality hospitals',
		'text'  => 'Every department of your hospital works on the same patient file.',
		'ticks' => array('OPD, wards, OT, ICU, lab, pharmacy, billing and the TPA desk on one file', 'The front desk knows which bed is free and cleaned', 'Every charge reaches the bill as it happens'),
		'img'   => '2026/11/dk-w1.webp',
		'alt'   => 'The bed board: occupied, vacant and under-cleaning beds by ward',
		'url'   => home_url('/solutions/multispeciality-hospitals/'),
		'cta'   => 'See multispeciality hospitals',
		'tone'  => 'mint',
	),
	array(
		'size'  => 'Two or more hospitals',
		'title' => 'Hospital chains & groups',
		'text'  => 'See every hospital in your group from one screen.',
		'ticks' => array('One patient record across your hospitals', 'Each hospital keeps its own rates, medicine list and departments', 'Add a new hospital without buying a new system'),
		'img'   => '2026/11/gv-v3.webp',
		'alt'   => 'The group dashboard with OPD, admissions and beds for each hospital',
		'url'   => home_url('/solutions/hospital-chains/'),
		'cta'   => 'See hospital groups',
		'tone'  => 'lilac',
	),
);

// Other kinds of healthcare business
$sxh_more = array(
	array('Clinics', 'One doctor or many', home_url('/clinic-management-software/')),
	array('Labs', 'Pathology and diagnostic centres', home_url('/laboratory-information-management-system/')),
	array('Pharmacies', 'Medical stores and hospital pharmacies', home_url('/pharmacy-management-system/')),
	array('All modules', 'OPD, IPD, lab, pharmacy and more', home_url('/modules/')),
);

$sxh_url    = get_permalink();
$sxh_schema = array(
	'@context' => 'https://schema.org',
	'@type'    => 'CollectionPage',
	'@id'      => $sxh_url . '#solutions',
	'url'      => $sxh_url,
	'name'     => 'Healthray solutions for hospitals',
	'mainEntity' => array(
		'@type'           => 'ItemList',
		'itemListElement' => array_map(static function ($c, $i) {
			return array('@type' => 'ListItem', 'position' => $i + 1, 'name' => $c['title'], 'url' => $c['url']);
		}, $sxh_cards, array_keys($sxh_cards)),
	),
);
?>
<script type="application/ld+json"><?php echo wp_json_encode($sxh_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>

<style id="sxh-css">
/* Solutions hub. Every rule is scoped to .sxh (and #myPopup for the popup). Same look as the solution pages. */
.sxh, #myPopup{--navy:#0D1B3E; --navy2:#1B3C74; --blue:#0F68EA; --ink:#14284F; --body:#4A5D73; --muted:#6C8093;
  --tint:#EFF4FF; --line:#D9E2F2; --white:#FFFFFF; --sky:#8FB2F5; --soft:#C8D5E3; --green:#157A4A;
  --peach:#FDF1E7; --mint:#E7F6F1; --lilac:#EFEBFC;
  --font:"Mulish",system-ui,-apple-system,"Segoe UI",sans-serif;
  color-scheme:light;}
.sxh, .sxh *{box-sizing:border-box}
.sxh [hidden]{display:none!important}
.sxh{margin:0;background:var(--white);color:var(--body);font-family:var(--font);font-size:16px;line-height:1.6}
.sxh h1, .sxh h2, .sxh h3{color:var(--ink);margin:0;line-height:1.15;text-wrap:balance;letter-spacing:-.015em;text-transform:none}
.sxh h2{font-size:clamp(27px,3.4vw,40px);font-weight:900}
.sxh h3{font-size:21px;font-weight:900}
.sxh p, .sxh ul{margin:0}
.sxh p, .sxh li{line-height:1.6}
.sxh img{max-width:100%;display:block}
.sxh a{color:var(--blue)}
.sxh a:focus-visible, .sxh button:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
.sxh .in{max-width:1160px;margin:0 auto;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
.sxh .sec{padding-block:88px}
.sxh .tint{background:var(--tint)}
.sxh .head{max-width:760px;display:flex;flex-direction:column;gap:12px;margin:0 auto 44px;text-align:center;align-items:center}
.sxh .head p{font-size:18px}
.sxh .label{font-size:12px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:var(--blue)}
.sxh .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--blue);color:#fff;font-weight:800;text-decoration:none;border:0;border-radius:12px;padding:15px 26px;font-family:var(--font);font-size:16px;line-height:1.2;cursor:pointer;box-shadow:none;transition:none}
.sxh .btn:hover{background:#0B57C7;color:#fff}
.sxh .ticks{list-style:none;padding:0;display:flex;flex-direction:column;gap:10px}
.sxh .ticks li{position:relative;padding-left:30px;color:var(--ink);font-weight:700;margin:0}
.sxh .ticks li::before{content:"";position:absolute;left:0;top:3px;width:20px;height:20px;border-radius:50%;background:#E3F4EC}
.sxh .ticks li::after{content:"";position:absolute;left:6px;top:8px;width:8px;height:4px;border-left:2px solid var(--green);border-bottom:2px solid var(--green);transform:rotate(-45deg)}

/* hero */
.sxh .hero{background:linear-gradient(135deg,#0D1B3E 0%,#1B3C74 58%,#0F68EA 135%);color:var(--soft)}
.sxh .hero .in{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:56px;padding-block:60px 72px;align-items:center}
.sxh .hero-l{display:flex;flex-direction:column;gap:20px}
.sxh .copy{display:flex;flex-direction:column;gap:20px}
.sxh .hero h1{font-size:13px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:var(--sky)}
.sxh .big{color:#fff;font-weight:900;font-size:clamp(32px,3.9vw,48px);line-height:1.07;letter-spacing:-.02em;text-wrap:balance}
.sxh .lead{font-size:18.5px;color:#DCE6F5;max-width:52ch}
.sxh .jump{display:flex;flex-wrap:wrap;gap:10px}
.sxh .jump a{display:inline-flex;align-items:center;gap:8px;padding:9px 14px;border-radius:999px;background:rgba(255,255,255,.1);border:1px solid rgba(255,255,255,.22);color:#fff;font-size:14.5px;font-weight:700;text-decoration:none}
.sxh .jump a:hover{background:rgba(255,255,255,.18)}
.sxh .jump small{color:var(--sky);font-size:13px;font-weight:700}
.sxh .htrust{display:flex;flex-direction:column;gap:18px;margin-top:6px}
.sxh .hrow{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.sxh .hrow p{font-size:15px;color:#DCE6F5;flex:1;min-width:200px}
.sxh .hrow p b{color:#fff}
.sxh .hrow+.hrow{border-top:1px solid rgba(255,255,255,.14);padding-top:18px}
.sxh .faces{display:flex}
.sxh .faces img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #fff;background:#fff}
.sxh .faces img+img{margin-left:-10px}
.sxh .tiles{display:flex;gap:10px}
.sxh .tiles span{width:52px;height:52px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;padding:5px}
.sxh .tiles img{max-height:100%;width:auto}
.sxh .form{color:var(--body);background:#fff;border-radius:20px;padding:28px;box-shadow:0 30px 70px rgba(3,10,30,.45);display:flex;flex-direction:column;gap:14px;scroll-margin-top:90px}
.sxh .form h2{font-size:24px}
.sxh .form .sub{font-size:14.5px;margin-top:-6px}
.sxh .form .wpcf7, .sxh .form .wpcf7 form{width:100%;max-width:100%;min-width:0;margin:0}

/* the three solutions */
.sxh .cards{list-style:none;padding:0;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:24px}
.sxh .card{display:flex;flex-direction:column;background:#fff;border:1px solid var(--line);border-radius:20px;overflow:hidden;margin:0;transition:box-shadow .2s,transform .2s}
.sxh .card:hover{box-shadow:0 22px 50px -24px rgba(13,27,62,.35);transform:translateY(-2px)}
.sxh .card .pic{display:block;aspect-ratio:3/2;overflow:hidden;background:var(--tint)}
.sxh .card.peach .pic{background:var(--peach)}
.sxh .card.mint .pic{background:var(--mint)}
.sxh .card.lilac .pic{background:var(--lilac)}
.sxh .card .pic img{width:100%;height:100%;object-fit:cover;object-position:top left}
.sxh .card .body{flex:1;display:flex;flex-direction:column;gap:14px;padding:24px 24px 26px}
.sxh .size{align-self:flex-start;font-size:12.5px;font-weight:800;letter-spacing:.06em;text-transform:uppercase;color:var(--blue);background:var(--tint);border-radius:999px;padding:4px 10px}
.sxh .card h3 a{color:var(--ink);text-decoration:none}
.sxh .card h3 a:hover{color:var(--blue)}
.sxh .card .text{font-size:16.5px}
.sxh .card .ticks{gap:8px;font-size:15px}
.sxh .card .go{margin-top:auto;padding-top:6px;display:flex;flex-direction:column;gap:12px}
.sxh .card .go .btn{width:100%}
.sxh .card .demo{text-align:center;font-weight:800;font-size:15px;text-decoration:none}

/* same for every hospital */
.sxh .same{list-style:none;padding:0;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:20px}
.sxh .same li{background:#fff;border:1px solid var(--line);border-radius:16px;padding:22px;margin:0}
.sxh .same b{display:block;color:var(--ink);font-size:17px;font-weight:900;line-height:1.3}
.sxh .same span{display:block;margin-top:6px;font-size:15px}

/* other kinds of business */
.sxh .more{list-style:none;padding:0;display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:16px}
.sxh .more li{margin:0}
.sxh .more a{display:flex;flex-direction:column;gap:4px;height:100%;padding:20px 22px;border:1.5px solid var(--line);border-radius:16px;background:#fff;text-decoration:none}
.sxh .more a:hover{border-color:var(--blue);background:#F7FAFF}
.sxh .more b{color:var(--ink);font-size:17px;font-weight:900}
.sxh .more b::after{content:" →";color:var(--blue)}
.sxh .more span{color:var(--body);font-size:14.5px}

/* last call */
.sxh .final{background:var(--tint);text-align:center;border-top:1px solid var(--line)}
.sxh .final .in{display:flex;flex-direction:column;align-items:center;gap:18px;padding-block:84px}
.sxh .final h2{max-width:24ch}
.sxh .final p{font-size:18px;max-width:60ch}

/* PPC Form (CF7 11eec7d) in the hero card and in the site popup (same rules as the small-hospitals page) */
:is(.sxh, #myPopup) .lead-form.hr-demo-form{display:block;color:var(--body);font-size:16px;line-height:1.6}
:is(.sxh, #myPopup) .hr-form-step{display:flex;flex-direction:column;gap:12px}
:is(.sxh, #myPopup) .hr-progress{display:flex;flex-direction:column;gap:6px;margin:0 0 2px}
:is(.sxh, #myPopup) .hr-progress-label{font-size:12px;font-weight:800;letter-spacing:.08em;color:var(--muted)}
:is(.sxh, #myPopup) .hr-progress-bar{height:4px;border-radius:2px;background:var(--line);overflow:hidden}
:is(.sxh, #myPopup) .hr-progress-bar span{display:block;height:100%;border-radius:2px;background:var(--blue)}
:is(.sxh, #myPopup) .hr-step-title{font-weight:800;color:var(--ink);font-size:16.5px}
:is(.sxh, #myPopup) .hr-bed-options{display:flex;flex-direction:column;gap:9px}
:is(.sxh, #myPopup) .hr-bed-option{display:flex;justify-content:space-between;align-items:center;gap:10px;width:100%;min-height:0;margin:0;padding:13px 16px;border:1.5px solid var(--line);border-radius:12px;background:#fff;box-shadow:none;color:var(--ink);font-family:var(--font);font-size:15.5px;font-weight:800;line-height:normal;letter-spacing:normal;text-align:left;text-transform:none;cursor:pointer}
:is(.sxh, #myPopup) .hr-bed-option:hover{border-color:var(--blue);background:#F2F7FF}
:is(.sxh, #myPopup) .hr-bed-option strong{font-weight:inherit}
:is(.sxh, #myPopup) .hr-bed-option.hr-bed-full{font-weight:700;color:var(--muted)}
:is(.sxh, #myPopup) .hr-arrow{flex:none;color:var(--blue);font-size:22px;line-height:1}
:is(.sxh, #myPopup) .hr-selected-bed{display:flex;align-items:center;gap:8px;background:var(--tint);border-radius:10px;padding:9px 12px;color:var(--ink);font-size:14px;font-weight:800}
:is(.sxh, #myPopup) .hr-selected-bed-value{flex:1;min-width:0}
:is(.sxh, #myPopup) .hr-selected-bed button{margin:0;padding:0;border:0;border-radius:0;background:none;box-shadow:none;color:var(--blue);font-family:var(--font);font-size:14px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(.sxh, #myPopup) .hr-selected-bed .hr-back-step{padding:0 2px;font-size:22px;line-height:1}
:is(.sxh, #myPopup) .hr-demo-form button:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(.sxh, #myPopup) .hr-demo-form .row-2{display:flex;flex-direction:column;gap:12px}
:is(.sxh, #myPopup) .hr-demo-form .field{display:flex;flex-direction:column;gap:4px;min-width:0;margin:0}
:is(.sxh, #myPopup) .hr-demo-form .field label{position:static;display:block;width:auto;height:auto;margin:0;padding:0;overflow:visible;clip:auto;clip-path:none;white-space:normal;color:var(--ink);font-size:12.5px;font-weight:800;line-height:inherit}
:is(.sxh, #myPopup) .lead-form .row-2 .field .wpcf7-form-control{width:100%;min-width:0;height:auto;margin:0;padding:11px 12px;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:none;color:var(--body);font-family:var(--font);font-size:15px;line-height:normal;transition:none}
:is(.sxh, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus{border-color:var(--line);outline:0}
:is(.sxh, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
/* Mobile number: "+91" box in front of the field (built by the script below) */
:is(.sxh, #myPopup) .hr-phone{display:flex;border:1px solid var(--line);border-radius:10px;background:#fff;overflow:hidden}
:is(.sxh, #myPopup) .hr-phone:focus-within{outline:3px solid var(--sky);outline-offset:2px}
:is(.sxh, #myPopup) .hr-phone:has(.wpcf7-not-valid){border-color:#DC3232}
:is(.sxh, #myPopup) .hr-cc{flex:none;display:flex;align-items:center;padding:0 10px 0 12px;border-right:1px solid var(--line);background:var(--tint);color:var(--ink);font-size:15px;font-weight:700}
:is(.sxh, #myPopup) .lead-form .row-2 .field .hr-phone .wpcf7-form-control{flex:1;border:0;border-radius:0;outline:0}
:is(.sxh, #myPopup) .hr-demo-form .wpcf7-not-valid-tip{margin-top:4px;color:#B42318;font-size:12.5px;font-weight:700;line-height:1.3}
:is(.sxh, #myPopup) .hr-demo-form .form-submit-btn{display:flex;flex-direction:column;position:relative;margin:0}
:is(.sxh, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit{display:block;width:100%;height:auto;margin:0;padding:15px 26px;border:0;border-radius:12px;background:var(--blue);box-shadow:none;color:#fff;font-family:var(--font);font-size:16px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(.sxh, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:hover{background:#0B57C7;box-shadow:none}
:is(.sxh, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(.sxh, #myPopup) .hr-demo-form .form-note{margin:0;color:var(--muted);font-size:13.5px;line-height:inherit;text-align:center}
/* Site popup (#myPopup, templates/lead-popup.php) on this page: drawn like the hero form card */
body #myPopup{max-width:480px;padding:28px;border-radius:20px;color:var(--body);font-family:var(--font)}
body #myPopup .widget-heading{margin:0 0 14px;padding-right:32px;text-align:left}
body #myPopup .widget-heading .heading-title{margin:0;color:var(--ink);font-size:24px;font-weight:900;line-height:1.15;letter-spacing:-.015em}
body #myPopup .widget-heading .description-text{margin:8px 0 0;color:var(--body);font-size:14.5px;line-height:1.6}
@media (max-width:600px){
  body #myPopup{padding:22px}
}
@media (max-width:1020px){
.sxh .hero .in{grid-template-columns:1fr;gap:22px;padding-block:36px 48px}
.sxh .hero-l{display:contents}
.sxh .hero .copy{order:0}
.sxh .hero .form{order:1;margin-top:6px}
.sxh .htrust{order:2;margin-top:8px}
.sxh .cards{grid-template-columns:1fr;max-width:620px;margin-inline:auto}
.sxh .same, .sxh .more{grid-template-columns:1fr 1fr}
.sxh .sec{padding-block:60px}
}
@media (max-width:600px){
.sxh .lead{font-size:16.5px}
.sxh .head{margin-bottom:28px}
.sxh .head p{font-size:16.5px}
.sxh .form{padding:22px}
.sxh .same, .sxh .more{grid-template-columns:1fr}
.sxh .final .in{padding-block:56px}
.sxh .final p{font-size:16.5px}
}
@media (prefers-reduced-motion:reduce){.sxh .card{transition:none}.sxh .card:hover{transform:none}}
</style>

<div class="sxh">

<section class="hero">
  <div class="in">
    <div class="hero-l">
      <div class="copy">
        <h1>Healthray solutions for hospitals</h1>
        <p class="big">Hospital software, set up for the way your hospital runs</p>
        <p class="lead">A 20-bed nursing home, a 200-bed multispeciality hospital and a group of hospitals each work differently. Pick yours to see how Healthray runs a hospital like it.</p>
        <nav class="jump" aria-label="Solutions">
          <?php foreach ($sxh_cards as $c) : ?>
          <a href="<?php echo esc_url($c['url']); ?>"><?php echo esc_html($c['title']); ?> <small><?php echo esc_html($c['size']); ?></small></a>
          <?php endforeach; ?>
        </nav>
      </div>
      <div class="htrust">
        <div class="hrow">
          <div class="faces" aria-hidden="true"><img src="<?php echo esc_url($sxh_up . '2024/04/Dr.-Vimal-Dhaduk.webp'); ?>" alt=""><img src="<?php echo esc_url($sxh_up . '2025/12/Dr.-Bhaumik-Rathore-Vibrant-Multispecialty-Hospital.webp'); ?>" alt=""><img src="<?php echo esc_url($sxh_up . '2026/10/Dr.-Sanjeev-Desai-Lotus-Hospital.webp'); ?>" alt=""><img src="<?php echo esc_url($sxh_up . '2026/10/Dr.-Hinal-Parmar-Rainbow-Hospital.webp'); ?>" alt=""><img src="<?php echo esc_url($sxh_up . '2026/10/Dr.-Dipak-Viradia.webp'); ?>" alt=""></div>
          <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
        </div>
        <div class="hrow">
          <div class="tiles"><span><img src="<?php echo esc_url($sxh_up . '2026/07/NABH-ehr-certified-150x150.webp'); ?>" alt="NABH certified"></span><span><img src="<?php echo esc_url($sxh_up . '2026/03/abdm-healthray-150x150.webp'); ?>" alt="ABDM compliant"></span><span><img src="<?php echo esc_url($sxh_up . '2025/07/NHA.webp'); ?>" alt="NHA approved"></span><span><img src="<?php echo esc_url($sxh_up . '2026/03/iso-27001-healthray-150x150.webp'); ?>" alt="ISO 27001"></span></div>
          <p><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
        </div>
      </div>
    </div>

    <div class="form" id="demo">
      <h2>Book a free demo</h2>
      <p class="sub">A free 30-minute demo, set up for your hospital's size.</p>
      <?php if (shortcode_exists('contact-form-7')) { echo do_shortcode($sxh_form); } ?>
    </div>
  </div>
</section>

<section class="sec" aria-labelledby="sxh-pick">
  <div class="in">
    <div class="head">
      <span class="label">Solutions</span>
      <h2 id="sxh-pick">Which one is your hospital?</h2>
      <p>Each page shows the screens, a patient's day and what changes for a hospital your size.</p>
    </div>
    <ul class="cards">
      <?php foreach ($sxh_cards as $c) : ?>
      <li class="card <?php echo esc_attr($c['tone']); ?>">
        <a class="pic" href="<?php echo esc_url($c['url']); ?>" tabindex="-1" aria-hidden="true"><img src="<?php echo esc_url($sxh_up . $c['img']); ?>" alt="" loading="lazy" decoding="async" width="600" height="400"></a>
        <div class="body">
          <span class="size"><?php echo esc_html($c['size']); ?></span>
          <h3><a href="<?php echo esc_url($c['url']); ?>"><?php echo esc_html($c['title']); ?></a></h3>
          <p class="text"><?php echo esc_html($c['text']); ?></p>
          <ul class="ticks">
            <?php foreach ($c['ticks'] as $t) : ?><li><?php echo esc_html($t); ?></li><?php endforeach; ?>
          </ul>
          <div class="go">
            <a class="btn" href="<?php echo esc_url($c['url']); ?>"><?php echo esc_html($c['cta']); ?></a>
            <a class="demo hr-cta-btn" href="#demo">Book a free demo</a>
          </div>
        </div>
      </li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="sec tint" aria-labelledby="sxh-same">
  <div class="in">
    <div class="head">
      <h2 id="sxh-same">The same help for every hospital</h2>
      <p>Whatever your size, starting on Healthray works the same way.</p>
    </div>
    <ul class="same">
      <li><b>We move your old data</b><span>From your registers, Excel or old software.</span></li>
      <li><b>We train your staff at your hospital</b><span>Each person learns their own work.</span></li>
      <li><b>One department at a time</b><span>Most hospitals start in 1 to 3 weeks.</span></li>
      <li><b>Help 24×7</b><span>On call or chat, in English, Hindi or Gujarati.</span></li>
    </ul>
  </div>
</section>

<section class="sec" aria-labelledby="sxh-more">
  <div class="in">
    <div class="head">
      <h2 id="sxh-more">Not a hospital?</h2>
      <p>Healthray also runs clinics, labs and pharmacies.</p>
    </div>
    <ul class="more">
      <?php foreach ($sxh_more as $m) : ?>
      <li><a href="<?php echo esc_url($m[2]); ?>"><b><?php echo esc_html($m[0]); ?></b><span><?php echo esc_html($m[1]); ?></span></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
</section>

<section class="final">
  <div class="in">
    <h2>See Healthray set up for a hospital your size</h2>
    <p>A free 30-minute video call with your own departments and beds. No charge, and no need to decide on the call.</p>
    <a class="btn hr-cta-btn" href="#demo">Book a free demo</a>
  </div>
</section>

</div>

<script id="sxh-js">
/* Solutions hub: the PPC Form steps and mobile box (same as the solution pages). */
(function(){
  if(!document.querySelector('.sxh')) return;
  /* PPC Form steps and mobile box (as on the small-hospitals page). The popup is printed in the footer, after
     this script, so this runs once the page has loaded. */
  function hrForm(){
  /* PPC Form, hero card and popup. Its own script (js/ppc-hims.js) loads only on the PPC templates, so its steps
     run here: a bed button fills the hidden bed_size field and opens step 2, "change" goes back to step 1. */
  document.querySelectorAll('.sxh .hr-demo-form, #myPopup .hr-demo-form').forEach(function(box){
    var s1=box.querySelector('.hr-step-1'),s2=box.querySelector('.hr-step-2'),wrap=box.closest('.wpcf7');
    if(!s1||!s2) return;
    var bed=box.querySelector('[name="bed_size"]'),pick=box.querySelector('.hr-selected-bed-value'); // by name: CF7 drops the id on the popup copy
    function step(n){s1.style.display=n===1?'':'none';s2.style.display=n===2?'':'none';}
    box.querySelectorAll('.hr-bed-option').forEach(function(b){b.addEventListener('click',function(){
      var val=b.getAttribute('data-bed'); if(!val) return;
      if(bed) bed.value=val; if(pick) pick.textContent=val;
      step(2); var first=s2.querySelector('input:not([type="hidden"])'); if(first) first.focus();
    });});
    box.querySelectorAll('.hr-back-step, .hr-change-bed').forEach(function(b){b.addEventListener('click',function(){
      step(1); var opt=s1.querySelector('.hr-bed-option'); if(opt) opt.focus();
    });});
    /* the form's labels are not tied to their fields: give each field its label as its name */
    box.querySelectorAll('.field').forEach(function(f){
      var l=f.querySelector('label'),i=f.querySelector('input:not([type="hidden"])');
      if(l&&i&&!i.hasAttribute('aria-label')) i.setAttribute('aria-label',l.textContent.replace('*','').trim());
    });
    /* CF7 empties the fields after a sent form: start again at step 1. The form reset leaves a hidden field's
       value as set by script, so bed_size is cleared here. */
    if(wrap) wrap.addEventListener('wpcf7reset',function(){if(bed) bed.value='';if(pick) pick.textContent='';step(1);});
  });

  /* Mobile number, as in js/ppc-hims.js: "+91" in its own box, the field takes the 10-digit number, and "+91 " is
     added to the value CF7 sends (the server check needs the country code). Pasted numbers keep their last 10 digits. */
  var CODE='+91',MAX=10;
  function digits(val){var d=val.replace(/\D/g,'').replace(/^0+/,'');return d.length>MAX?d.slice(-MAX):d;}
  document.querySelectorAll('.sxh .hr-demo-form input[name="your-number"], #myPopup .hr-demo-form input[name="your-number"]').forEach(function(input){
    var box=document.createElement('span'),cc=document.createElement('span'),f=input.closest('form');
    box.className='hr-phone';cc.className='hr-cc';cc.textContent=CODE;cc.setAttribute('aria-hidden','true');
    input.parentNode.insertBefore(box,input);box.appendChild(cc);box.appendChild(input);
    input.value=digits(input.value);input.placeholder='98765 43210';
    input.setAttribute('inputmode','numeric');input.setAttribute('autocomplete','tel-national');input.setAttribute('aria-label','Mobile number, India '+CODE);
    input.addEventListener('input',function(e){
      var next=e.inputType==='insertText'?input.value.replace(/\D/g,'').replace(/^0+/,'').slice(0,MAX):digits(input.value);
      if(next!==input.value) input.value=next;
    });
    if(f) f.addEventListener('formdata',function(e){var d=digits(input.value);e.formData.set('your-number',d?CODE+' '+d:'');});
  });
  }
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',hrForm); else hrForm();
})();
</script>
