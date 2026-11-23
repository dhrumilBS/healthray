<?php
/**
 * Template Name: Hospital Chains & Groups
 * Template Post Type: page
 *
 * Solutions page for groups with two or more hospitals (/solutions/hospital-chains/).
 * WordPress also picks this file by slug (page-{slug}.php), so the page can stay on "Default template".
 *
 * Built from the design files template-hospital-chains.php / chains.css / chains.js: same content, markup, CSS and
 * JS. The Roots wrapper prints <head>, the site header and the site footer around this file, so there are no
 * get_header/get_footer, wp_head or enqueue hooks here. CSS is scoped to #hr-chains, and to #myPopup for the popup.
 * Yoast handles the title, meta description, canonical and base schema; this file adds FAQPage.
 *
 * Lead form: "PPC - Hospital Chains" (CF7 03f048f, a copy of the PPC Form), in the hero card and in the site popup (#myPopup). Every "Book a demo"
 * button has .hr-cta-btn, so js/script.js opens that popup; the popup gets this form and heading through the
 * hr_ppc_popup_args filter (templates/footer.php -> templates/lead-popup.php). On this page only, step 1 of the
 * form asks "How many hospitals are in your group?" (wpcf7_form_elements filter below); the answer is sent in the
 * form's hidden bed_size field, so the form, its mail and its lead hooks stay the same.
 */
defined('ABSPATH') || exit;

$hr_chn_form = '[contact-form-7 id="03f048f" title="PPC - Hospital Chains"]';
$hr_chn_up   = 'https://healthray.com/wp-content/uploads/';
$hr_chn_logo = get_stylesheet_directory_uri() . '/assets/ppc-hims/';

// Images and videos (media library + theme logos)
$hr_chn_img = array(
	'badges/nabh'             => $hr_chn_up . '2026/07/NABH-ehr-certified-150x150.webp',
	'badges/abdm'             => $hr_chn_up . '2026/03/abdm-healthray-150x150.webp',
	'badges/nha'              => $hr_chn_up . '2025/07/NHA.webp',
	'badges/iso-27001'        => $hr_chn_up . '2026/03/iso-27001-healthray-150x150.webp',
	'logos/jupiter'           => $hr_chn_logo . 'jupiter-hospital-group.webp',
	'logos/universal'         => $hr_chn_logo . 'universal-hospital.webp',
	'logos/hzb-arogyam'       => $hr_chn_logo . 'hzb-arogyam-multispeciality-hospital.webp',
	'logos/vibrant'           => $hr_chn_logo . 'vibrant-hospital.webp',
	'logos/parivar'           => $hr_chn_logo . 'parivar-super-speciality-hospital.webp',
	'logos/shraddha'          => $hr_chn_up . '2025/08/Shraddha-Arogya-Mandir.webp',
	'logos/jeevan-rekha'      => $hr_chn_logo . 'jeevan-rekha-hospital.webp',
	'logos/bbmh'              => $hr_chn_logo . 'budha-baba-multispeciality-hospital.webp',
	'logos/pims'              => $hr_chn_logo . 'pims-multi-superspeciality-hospital.webp',
	'logos/jabalpur'          => $hr_chn_logo . 'jabalpur-hospital-research-center.webp',
	'logos/jj-plus'           => $hr_chn_logo . 'jj-plus-hospitals.webp',
	'logos/prabh-aasra'       => $hr_chn_logo . 'prabh-aasra-charitable-hospital.webp',
	'logos/heritage-hospital' => $hr_chn_logo . 'heritage-hospital.webp',
	'people/vimal-dhaduk'     => $hr_chn_up . '2024/04/Dr.-Vimal-Dhaduk.webp',
	'people/bhaumik-rathore'  => $hr_chn_up . '2025/12/Dr.-Bhaumik-Rathore-Vibrant-Multispecialty-Hospital.webp',
	'people/sanjeev-desai'    => $hr_chn_up . '2026/10/Dr.-Sanjeev-Desai-Lotus-Hospital.webp',
	'people/rajesh-dhirawani' => $hr_chn_up . '2026/10/Dr.-Rajesh-B.-Dhirawani.webp',
	'people/monil-parmar'     => $hr_chn_up . '2025/10/Dr.-Monil-Parmar.webp',
	'people/hinal-parmar'     => $hr_chn_up . '2026/10/Dr.-Hinal-Parmar-Rainbow-Hospital.webp',
	'people/deepika-singhavi' => $hr_chn_up . '2026/10/Dr.-Deepika-Singhavi.webp',
	'people/arpit-gajjar'     => $hr_chn_up . '2025/10/Dr.-Arpit-gajjar-150x150.webp',
	'people/ketan-rupala'     => $hr_chn_up . '2024/04/Dr.-Ketan-Rupala-150x150.webp',
	'photos/t-vibrant'        => $hr_chn_up . '2025/12/Vibrant-Hospital-Healthray.webp',
	'photos/t-shraddha'       => $hr_chn_up . '2026/10/Shraddha-Aarogya-Mandir.webp',
	'photos/t-universal'      => $hr_chn_up . '2025/10/Universal-Hospital.webp',
	'photos/t-jabalpur'       => $hr_chn_up . '2025/11/Jabalpur-Hospital.webp',
	'gv-v3'                   => $hr_chn_up . '2026/11/gv-v3.webp',
	'gv-v3m'                  => $hr_chn_up . '2026/11/gv-v3m.webp',
	'dk-f1'                   => $hr_chn_up . '2026/11/dk-f1.webp',
	'dk-f1m'                  => $hr_chn_up . '2026/11/dk-f1m.webp',
	'dk-w1'                   => $hr_chn_up . '2026/11/dk-w1.webp',
	'dk-w1m'                  => $hr_chn_up . '2026/11/dk-w1m.webp',
	'dk-b3'                   => $hr_chn_up . '2026/11/dk-b3.webp',
	'dk-b3m'                  => $hr_chn_up . '2026/11/dk-b3m.webp',
	'st-v1'                   => $hr_chn_up . '2026/11/st-v1.webp',
	'st-v1m'                  => $hr_chn_up . '2026/11/st-v1m.webp',
	'video/tour'              => $hr_chn_up . '2026/11/chains-tour-v2.mp4',
	'video/tour-phone'        => $hr_chn_up . '2026/11/chains-tour-v2-phone.mp4',
	'video/poster-phone'      => $hr_chn_up . '2026/11/chains-tour-v2-sq-poster.webp',
	'video/poster'            => $hr_chn_up . '2026/11/chains-tour-v2-poster.webp',
);

// Hospitals in the sliding logo strip: image key, alt text, beds
$hr_chn_logos = array(
	array('logos/jupiter', 'Jupiter Hospital Group, Vadodara', '100 beds'),
	array('logos/universal', 'Universal Hospital, Surat', '200 beds'),
	array('logos/hzb-arogyam', 'HZB Arogyam Multispeciality Hospital, Hazaribagh', '150 beds'),
	array('logos/vibrant', 'Vibrant Multispeciality Hospital, Vapi', '200+ beds'),
	array('logos/parivar', 'Parivar Super Speciality Hospital, Madhya Pradesh', '140 beds'),
	array('logos/shraddha', 'Shraddha Aarogya Mandir, Vapi', '150 beds'),
	array('logos/jeevan-rekha', 'Jeevan Rekha Hospital, Raiganj, West Bengal', '220 beds'),
	array('logos/bbmh', 'Budha Baba Multispeciality Hospital, Cuttack', '100 beds'),
	array('logos/pims', 'PIMS Multi-Superspeciality Hospital, Udaipur', '120 beds'),
	array('logos/jabalpur', 'Jabalpur Hospital and Research Centre, Jabalpur', '300+ beds'),
	array('logos/jj-plus', 'JJ Plus Hospitals', '135 beds'),
	array('logos/prabh-aasra', 'Prabh Aasra Charitable Hospital, Punjab', '120 beds'),
	array('logos/heritage-hospital', 'Heritage Hospital, Noida', '100 beds'),
);

// Quote wall: two rows, each card is image key, name, hospital, quote
$hr_chn_quotes = array(
	array(
		array('people/vimal-dhaduk', 'Dr. Vimal Dhaduk', 'VR Group of Hospitals', '“A surgical patient passes through many teams before going home. With Healthray, these departments are connected on the same patient record, which has made the overall workflow smoother and reduced confusion for our team.”'),
		array('people/bhaumik-rathore', 'Dr. Bhaumik Rathore', 'Vibrant Multispeciality Hospital, Vapi', '“Now patient records, billing, pharmacy and inventory are all connected in one system.”'),
		array('people/hinal-parmar', 'Dr. Hinal Parmar', 'Rainbow Hospital, Vapi', '“Now the past visits, scans and notes are already on the screen when she walks in.”'),
		array('people/deepika-singhavi', 'Dr. Deepika Singhavi', 'Shraddha Aarogya Mandir, Vapi', '“Now I type her name and the whole history is right there.”'),
		array('people/sanjeev-desai', 'Dr. Sanjeev Desai', 'Lotus Hospital, Valsad', '“We have found it much easier to keep things organised across departments.”'),
	),
	array(
		array('people/rajesh-dhirawani', 'Dr. Rajesh B. Dhirawani', 'Jabalpur Hospital & Research Centre', '“Every work operation from diagnosis to discharge can now be accessed in just one single click.”'),
		array('people/monil-parmar', 'Dr. Monil Parmar', 'Universal Superspeciality Hospital, Surat', '“The Healthray team came to the hospital, trained the staff on-site and showed each workflow in a simple way. Now the staff uses it comfortably in their own roles.”'),
		array('people/arpit-gajjar', 'Dr. Arpit Gajjar', 'Shubh Multispeciality Hospital', '“Their support team has been genuinely helpful even after implementation. We\'ve reached out a few times during OPD hours over call or email, and they respond properly and help us sort out the issue.”'),
		array('people/ketan-rupala', 'Dr. Ketan Rupala', 'Rupala Kidney & Prostate Hospital', '“We needed a system that fits our urology workflow, not the other way around. Healthray was customized based on our requirements, so it works according to our process.”'),
	),
);

// FAQs: visible list and schema
$hr_chn_faq = array(
	array('q' => 'Can each hospital keep its own rates and medicine list?', 'a' => 'Yes. Each hospital keeps its own rates, medicine list, departments and reports, while the group sees everything together.'),
	array('q' => 'Does a patient have one file across our hospitals?', 'a' => 'Yes. Your hospitals share patient records, so a patient who visits two of them has one history, not two.'),
	array('q' => 'Can staff at one hospital see another hospital\'s patients?', 'a' => 'Only if you allow it. Access is set by hospital and by role, so each person sees only what their job needs.'),
	array('q' => 'Can we see one hospital\'s numbers on their own?', 'a' => 'Yes. The group dashboard shows all your hospitals together, and you can open any one hospital to see only its own numbers.'),
	array('q' => 'We are buying another hospital. Can it join?', 'a' => 'Yes. It joins the same system, with no new software to buy. Its old patient, billing and stock data is moved in before it starts, and it goes live one department at a time.'),
	array('q' => 'What if one hospital\'s internet goes down?', 'a' => 'Run Healthray on a server inside that hospital, or use cloud and a server together. Then OPD, billing and pharmacy keep running there without internet.'),
	array('q' => 'Where is our data kept, and do we get it back if we leave?', 'a' => 'On Healthray\'s cloud, on servers in your hospitals, or both. Your group owns its data and can export it any time. It is encrypted and backed up automatically, and Healthray is ISO 27001 certified. If you leave, all your data is given back within 30 days, as written in the data agreement we sign. From 13 May 2027, the DPDP Rules make every hospital answerable for how its software company handles patient data, so ask to see this agreement in your demo.'),
	array('q' => 'Who should join the demo?', 'a' => 'Your group operations head, your finance head and your IT head. In 30 minutes we show one hospital\'s patient journey, the group dashboard and how a new hospital is added.'),
);

$hr_chn_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => get_permalink() . '#faq',
	'mainEntity' => array_map(static function ($f) {
		return array('@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f['a']));
	}, $hr_chn_faq),
);

// Site popup: same form, heading and line as the hero card.
add_filter('hr_ppc_popup_args', static function () use ($hr_chn_form) {
	return array(
		'form'  => $hr_chn_form,
		'title' => 'Book a free demo',
		'text'  => 'A free 30-minute demo for your group. Bring your operations head, finance head and IT head.',
	);
});

// PPC Form step 1 on this page: number of hospitals instead of number of beds (hero card and popup copy).
// The chosen answer still goes into the hidden bed_size field, which the form's script below fills.
add_filter('wpcf7_form_elements', static function ($html) {
	// Only the shared PPC Form (bed question) is rewritten; a form that already asks about hospitals is left alone.
	if (false === strpos($html, 'hr-bed-options') || false === strpos($html, 'How many beds')) {
		return $html;
	}
	$opts = array(
		array('2 hospitals', '2 hospitals', ''),
		array('3 – 5 hospitals', '3 – 5', ''),
		array('6 – 10 hospitals', '6 – 10', ''),
		array('More than 10 hospitals', '10+', ''),
		array('Opening our second hospital', 'We are opening our second hospital', ' hr-bed-full'),
		array('Just one hospital', 'Just one hospital', ' hr-bed-full'),
	);
	$buttons = '';
	foreach ($opts as $o) {
		$buttons .= '<button type="button" class="hr-bed-option' . $o[2] . '" data-bed="' . esc_attr($o[0]) . '"><span>' . esc_html($o[1]) . '</span><span class="hr-arrow" aria-hidden="true">›</span></button>';
	}
	$html = preg_replace_callback('~<div class="hr-step-title">.*?</div>~s', static function () {
		return '<div class="hr-step-title">How many hospitals are in your group?</div>';
	}, $html, 1);
	$html = preg_replace_callback('~<div class="hr-bed-options">.*?</div>~s', static function () use ($buttons) {
		return '<div class="hr-bed-options hr-grp-options">' . $buttons . '</div>';
	}, $html, 1);
	return str_replace('<label>City *</label>', '<label>Head office city *</label>', $html);
});

$hr_chn_badge = '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>';
?>
<script type="application/ld+json"><?php echo wp_json_encode($hr_chn_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>

<style id="hr-chains-css">
/* Hospital chains & groups page (design files: template-hospital-chains.php, chains.css).
   Every rule is scoped to #hr-chains, or #myPopup for the popup, so nothing reaches the site header and footer. */
#hr-chains, #myPopup{--navy:#0D1B3E; --navy2:#1B3C74; --blue:#0F68EA; --ink:#14284F; --body:#4A5D73; --muted:#6C8093;
  --tint:#EFF4FF; --line:#D9E2F2; --white:#FFFFFF; --sky:#8FB2F5; --soft:#C8D5E3; --green:#157A4A;
  --font:"Mulish",system-ui,-apple-system,"Segoe UI",sans-serif;
  color-scheme:light;}
#hr-chains, #hr-chains *{box-sizing:border-box}
#hr-chains [hidden]{display:none!important}
/* Base rules from the design file. The additions undo site-wide rules inside #hr-chains: style.css capitalises
   headings, sets p/li line-height, removes link underlines and adds link transitions; the Elementor kit sets
   link size/line-height and button padding/hover colours; Bootstrap sets .lead to weight 300 and .btn shadows. */
html{scroll-behavior:smooth}
#hr-chains :where(h1,h2,h3,h4,h5,h6){font-family:inherit;text-transform:none;font-style:normal;text-shadow:none}
#hr-chains :where(p,li,span,small,b,strong,label,figcaption,blockquote,summary,details){font-family:inherit;text-transform:none}
#hr-chains :where(img,video,svg,picture){border:0;border-radius:0;box-shadow:none;max-width:100%}
#hr-chains :where(figure){margin:0}
#hr-chains{display:block;background:var(--white);color:var(--body);font-family:var(--font);font-size:16px;line-height:1.6}
#hr-chains h1, #hr-chains h2, #hr-chains h3{color:var(--ink);margin:0;line-height:1.15;text-wrap:balance;letter-spacing:-.015em;text-transform:none}
#hr-chains h2{font-size:clamp(27px,3.4vw,40px);font-weight:900}
#hr-chains h3{font-size:19px;font-weight:800}
#hr-chains p{margin:0;line-height:inherit}
#hr-chains li{line-height:inherit}
#hr-chains img, #hr-chains video{max-width:100%;display:block}
#hr-chains button{padding:0;font-family:var(--font);letter-spacing:normal}
#hr-chains a{color:var(--blue);text-decoration:underline;font-size:inherit;line-height:inherit;transition:none}
#hr-chains a:focus-visible, #hr-chains button:focus-visible, #hr-chains summary:focus-visible, #hr-chains video:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
#hr-chains .in{max-width:1160px;margin:0 auto;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
#hr-chains .sec{padding-block:88px}
#hr-chains .tint{background:var(--tint)}
#hr-chains .head{max-width:760px;display:flex;flex-direction:column;gap:12px;margin-bottom:44px}
#hr-chains .head.c{margin-inline:auto;text-align:center;align-items:center}
#hr-chains .head p{font-size:18px}
#hr-chains .label{font-size:12px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:var(--blue)}
#hr-chains .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--blue);color:#fff;font-weight:800;text-decoration:none;border:0;border-radius:12px;padding:15px 26px;font-family:var(--font);font-size:16px;cursor:pointer;line-height:1.2;box-shadow:none;transition:none}
#hr-chains .btn:hover{background:#0B57C7;color:#fff}
#hr-chains .btn.line{background:#fff;color:var(--ink);border:1.5px solid var(--line)}
#hr-chains .btn.line:hover{border-color:var(--blue);background:#F7FAFF;color:var(--ink)}
#hr-chains .small{font-size:13.5px;color:var(--muted)}
#hr-chains .stars{color:#F6B73C;letter-spacing:1px}
/* 1 hero: solid navy + real hospital band */
#hr-chains .hx h1{font-size:13px;font-weight:800;letter-spacing:.09em;text-transform:uppercase}
#hr-chains .hB{background:var(--navy);color:var(--soft)}
#hr-chains .hB .hB-top{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:56px;align-items:center;padding-block:60px 56px}
#hr-chains .hB h1{color:var(--sky)}
#hr-chains .hB .hB-big{margin-top:16px;color:#fff;font-weight:800;font-size:clamp(34px,4vw,52px);line-height:1.06;letter-spacing:-.03em;text-wrap:balance}
#hr-chains .hB .hB-lead{margin-top:18px;font-size:18px;color:#DCE6F5;max-width:52ch}
#hr-chains .hB .htrust{margin-top:26px}
#hr-chains .htrust{display:flex;flex-direction:column;gap:18px}
#hr-chains .hrow{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
#hr-chains .hrow p{font-size:15px;color:#DCE6F5;flex:1;min-width:200px}
#hr-chains .hrow p b{color:#fff}
#hr-chains .hrow+.hrow{border-top:1px solid rgba(255,255,255,.14);padding-top:18px}
#hr-chains .faces{display:flex}
#hr-chains .faces img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #fff;background:#fff}
#hr-chains .faces img+img{margin-left:-10px}
#hr-chains .tiles{display:flex;gap:10px}
#hr-chains .tiles span{width:52px;height:52px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;padding:5px}
#hr-chains .tiles img{max-height:100%;width:auto}
#hr-chains .hrates{display:flex;flex-wrap:wrap;gap:6px 20px;font-size:14px;color:#DCE6F5}
#hr-chains .hrates b{color:#fff;font-size:15px}
#hr-chains .hrates i{font-style:normal;color:#F6B73C;font-size:12px;letter-spacing:1px}
#hr-chains .hB .band{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:2px;background:var(--navy)}
#hr-chains .hB .band figure{margin:0;position:relative;height:230px;overflow:hidden}
#hr-chains .hB .band img{width:100%;height:100%;object-fit:cover;display:block}
#hr-chains .hB .band figcaption{position:absolute;left:0;right:0;bottom:0;padding:40px 18px 14px;background:linear-gradient(180deg,rgba(13,27,62,0),rgba(13,27,62,.85));color:#fff;font-size:14px;font-weight:700;line-height:1.35}
#hr-chains .hB .band figcaption span{display:block;font-weight:600;color:#C8D5E3;font-size:12.5px}
#hr-chains .hB .band-l{padding-block:14px;font-size:13px;color:#9FB2CC;font-weight:600}
/* form card */
#hr-chains .form{color:var(--body);background:#fff;border-radius:20px;padding:28px;box-shadow:0 30px 70px rgba(3,10,30,.45);display:flex;flex-direction:column;gap:14px;scroll-margin-top:90px}
#hr-chains .form h2{font-size:24px}
#hr-chains .form .sub{font-size:14.5px;margin-top:-6px}
/* 2 logos: sliding strip */
#hr-chains .logos{border-bottom:1px solid var(--line)}
#hr-chains .logos .in{padding-block:30px 26px}
#hr-chains .logos h2{font-size:13px;font-weight:700;color:var(--muted);text-align:center;margin-bottom:8px;letter-spacing:0}
#hr-chains .marquee{overflow:hidden;padding-top:14px;-webkit-mask-image:linear-gradient(90deg,transparent,#000 5%,#000 95%,transparent);mask-image:linear-gradient(90deg,transparent,#000 5%,#000 95%,transparent)}
#hr-chains .marquee-track{display:flex;width:max-content;animation:hr-chn-logoscroll 60s linear infinite}
#hr-chains .marquee:hover .marquee-track{animation-play-state:paused}
#hr-chains .marquee ul{display:flex;gap:24px;margin:0;padding:0 24px 0 0;list-style:none}
#hr-chains .marquee li{position:relative;margin:0}
#hr-chains .lg-tile{width:200px;height:94px;padding:0 12px;border:1px solid var(--line);border-radius:14px;background:#fff;display:grid;place-items:center;transition:border-color .2s,box-shadow .2s}
#hr-chains .marquee li:hover .lg-tile{border-color:#BBD2FA;box-shadow:0 8px 20px rgba(27,60,116,.08)}
#hr-chains .lg-tile img{width:168px;height:62px;max-width:none;object-fit:contain}
#hr-chains .lg-badge{position:absolute;top:-11px;left:50%;transform:translateX(-50%);display:inline-flex;align-items:center;gap:5px;background:rgba(239,244,255,.92);border:1px solid #BBD2FA;color:var(--ink);font-size:12px;font-weight:700;border-radius:30px;padding:4px 9px 4px 7px;white-space:nowrap;line-height:1}
#hr-chains .lg-badge svg{width:13px;height:13px;color:var(--blue);flex:none}
@keyframes hr-chn-logoscroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
/* 3 group view */
#hr-chains .gv3{padding-top:112px;background:#fff;overflow:hidden}
#hr-chains .gv3-head{max-width:820px}
#hr-chains .gv3-eyebrow{font-size:13px;font-weight:800;color:var(--blue);letter-spacing:.02em}
#hr-chains .gv3 h2{margin:14px 0 0;color:var(--ink);font-size:clamp(34px,5.2vw,64px);font-weight:900;line-height:1.04;letter-spacing:-.035em}
#hr-chains .gv3 h2 span{color:#9AA8BB}
#hr-chains .gv3-sub{margin-top:22px;font-size:clamp(17px,1.6vw,20px);line-height:1.55;max-width:58ch}
#hr-chains .gvx{margin:36px 0 0;border-radius:20px;overflow:hidden;box-shadow:0 30px 70px -40px rgba(13,27,62,.45)}
#hr-chains .gvx img{display:block;width:100%;height:auto}
#hr-chains .gv3-points{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:40px;padding-top:8px;margin-top:-8px;position:relative}
#hr-chains .gv3-points>div{border-top:1px solid var(--ink);padding-top:18px}
#hr-chains .gv3-points b{display:block;color:var(--ink);font-size:17px;font-weight:800;margin-bottom:6px}
#hr-chains .gv3-points p{font-size:15px;line-height:1.55}
#hr-chains .gv3-src{font-size:12.5px;color:var(--muted);padding-top:28px;padding-bottom:104px}
/* 3b modules tour */
#hr-chains .mx{padding-block:88px;border-top:1px solid var(--line)}
#hr-chains .mx h2{margin:0;color:var(--ink);font-size:clamp(28px,3.2vw,40px);font-weight:800;letter-spacing:-.02em;line-height:1.12;text-wrap:balance}
#hr-chains .mx .mx-sub{margin-top:12px;font-size:17px;max-width:52ch}
#hr-chains .mx button:focus-visible, #hr-chains .mx a:focus-visible{outline:2px solid var(--blue);outline-offset:2px}
#hr-chains .mx .pic{border-radius:16px;overflow:hidden}
#hr-chains .mx .pic img{display:block;width:100%;height:auto}
#hr-chains .mx .ml{display:inline-flex;align-items:center;gap:8px;font-weight:700;font-size:15.5px;color:var(--ink);text-decoration:none}
#hr-chains .mx a.ml span{color:var(--blue);transition:transform .2s}
#hr-chains .mx a.ml:hover{color:var(--blue)}
#hr-chains .mx a.ml:hover span{transform:translateX(3px)}
#hr-chains .mx .ml.nl{color:var(--body)}
#hr-chains .mA .a-grid{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:56px;align-items:start}
#hr-chains .mA .a-tabs{margin-top:32px;display:flex;flex-direction:column;border-top:1px solid var(--line)}
#hr-chains .mA .a-tabs button{font:inherit;text-align:left;background:none;border:0;border-bottom:1px solid var(--line);border-radius:0;box-shadow:none;color:inherit;padding:18px 4px 18px 18px;cursor:pointer;position:relative;text-transform:none}
#hr-chains .mA .a-tabs button:hover, #hr-chains .mA .a-tabs button:focus{background:none;color:inherit}
#hr-chains .mA .a-tabs button::before{content:"";position:absolute;left:0;top:18px;bottom:18px;width:3px;border-radius:2px;background:transparent}
#hr-chains .mA .a-tabs button[aria-selected="true"]::before{background:var(--blue)}
#hr-chains .mA .a-tabs b{display:block;font-size:18px;font-weight:800;color:var(--muted)}
#hr-chains .mA .a-tabs button[aria-selected="true"] b{color:var(--ink)}
#hr-chains .mA .a-tabs small{display:block;margin-top:3px;font-size:14px;color:var(--muted)}
#hr-chains .mA .a-panel p{margin-top:18px;font-size:16.5px;color:var(--ink);font-weight:600}
#hr-chains .mA .a-links{margin-top:12px;display:flex;flex-wrap:wrap;gap:10px 24px}
/* 4 shared vs own */
#hr-chains .sh2{background:var(--tint)}
#hr-chains .sh2 .in{padding-block:64px}
#hr-chains .sh2-head{max-width:720px}
#hr-chains .sh2-head h2{font-size:clamp(26px,2.8vw,34px)}
#hr-chains .sh2-head p{margin-top:10px;font-size:17px}
#hr-chains .sh2-boxes{margin-top:28px;display:grid;grid-template-columns:minmax(0,1.15fr) minmax(0,.85fr);gap:16px}
#hr-chains .sh2-boxes>div{border-radius:16px;padding:22px 24px}
#hr-chains .sh2-boxes b{display:block;font-size:12.5px;letter-spacing:.1em;text-transform:uppercase;font-weight:800}
#hr-chains .sh2-boxes ul{list-style:none;margin:14px 0 0;padding:0;display:flex;flex-wrap:wrap;gap:8px}
#hr-chains .sh2-boxes li{border-radius:999px;padding:8px 14px;font-size:15px;font-weight:700;margin:0}
#hr-chains .sh2-shared{background:var(--ink)}
#hr-chains .sh2-shared b{color:var(--sky)}
#hr-chains .sh2-shared li{background:rgba(255,255,255,.1);color:#fff}
#hr-chains .sh2-own{background:#fff;border:1px solid var(--line)}
#hr-chains .sh2-own b{color:var(--blue)}
#hr-chains .sh2-own li{background:var(--tint);color:var(--ink)}
#hr-chains .sh2-next{margin-top:18px;font-size:15.5px;color:var(--body)}
#hr-chains .sh2-next b{color:var(--blue)}
/* 5 video */
#hr-chains .vid-wrap{max-width:980px;margin:0 auto}
#hr-chains .frame{position:relative;border-radius:18px;overflow:hidden;background:#0D1B3E;box-shadow:0 30px 70px rgba(27,60,116,.25);border:1px solid var(--line)}
#hr-chains .frame-bar{display:flex;align-items:center;gap:6px;padding:10px 14px;background:#fff;border-bottom:1px solid var(--line)}
#hr-chains .frame-bar i{width:10px;height:10px;border-radius:50%;background:#DCE3EE}
#hr-chains .frame-bar span{margin-left:10px;font-size:12px;color:var(--muted);background:var(--tint);border-radius:6px;padding:2px 12px}
#hr-chains .frame video{width:100%;height:auto;aspect-ratio:16/9;cursor:pointer;background:#0D1B3E}
#hr-chains .vplay{display:none;position:absolute;left:50%;top:calc(50% + 18px);transform:translate(-50%,-50%);width:72px;height:72px;padding:0;border-radius:50%;border:0;background:#fff;color:var(--blue);cursor:pointer;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(13,27,62,.35)}
#hr-chains .vplay:hover, #hr-chains .vplay:focus{background:#fff;color:var(--blue)}
#hr-chains .vplay svg{width:28px;height:28px;margin-left:4px}
#hr-chains .frame.paused .vplay{display:flex}
#hr-chains .vid-foot{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:14px;margin-top:22px}
#hr-chains .vid-line{font-size:17px;color:var(--ink);font-weight:600;max-width:60ch;flex:1;min-width:240px}
/* 6 adding a hospital */
#hr-chains .gl-grid{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:48px;align-items:start}
#hr-chains .zero{display:flex;flex-direction:column;gap:14px;position:sticky;top:96px}
#hr-chains .z-big{font-size:clamp(96px,12vw,150px);font-weight:900;color:var(--green);line-height:.85;letter-spacing:-.04em}
#hr-chains .z-big-l{font-size:22px;font-weight:900;color:var(--ink);line-height:1.25}
#hr-chains .zero p{font-size:16px}
#hr-chains .gl-src{font-size:13px;color:var(--muted);margin-top:10px}
#hr-chains .steps{list-style:none;margin:0;padding:0;counter-reset:s;display:flex;flex-direction:column}
#hr-chains .steps li{counter-increment:s;position:relative;padding:0 0 22px 52px;margin:0}
#hr-chains .steps li::before{content:counter(s);position:absolute;left:0;top:0;width:36px;height:36px;border-radius:50%;background:var(--blue);color:#fff;font-weight:900;display:flex;align-items:center;justify-content:center}
#hr-chains .steps li::after{content:"";position:absolute;left:17px;top:40px;bottom:4px;width:2px;background:var(--line)}
#hr-chains .steps li:last-child::after{display:none}
#hr-chains .steps b{display:block;color:var(--ink);font-size:17px}
/* 7 voices: sliding quote wall */
#hr-chains .s7x h2{font-weight:800;letter-spacing:-.02em;line-height:1.12;text-wrap:balance;margin:0;color:var(--ink)}
#hr-chains .s7x .s7eye{display:block;color:var(--muted);font-size:14px;font-weight:600;margin-bottom:12px}
#hr-chains .s7x .s7src{font-size:12.5px;color:var(--muted)}
#hr-chains .s7x a{color:inherit}
#hr-chains .s7c{padding-block:96px;overflow:hidden}
#hr-chains .s7c .c7-top{text-align:center;max-width:720px;margin:0 auto}
#hr-chains .s7c h2{font-size:clamp(30px,3.6vw,44px)}
#hr-chains .s7c .c7-rows{margin-top:52px;display:flex;flex-direction:column;gap:18px;-webkit-mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent);mask-image:linear-gradient(90deg,transparent,#000 8%,#000 92%,transparent)}
#hr-chains .s7c .c7-track{display:flex;width:max-content;animation:hr-chn-s7mq 70s linear infinite}
#hr-chains .s7c .c7-rows:hover .c7-track{animation-play-state:paused}
#hr-chains .s7c .c7-row2 .c7-track{animation-direction:reverse;animation-duration:80s}
@keyframes hr-chn-s7mq{to{transform:translateX(-50%)}}
#hr-chains .s7c .c7-card{width:340px;margin-right:18px;flex:none;background:#fff;border:1px solid #E3E8F0;border-radius:12px;padding:20px 22px 22px}
#hr-chains .s7c .c7-who{display:flex;gap:12px;align-items:center}
#hr-chains .s7c .c7-who img{width:40px;height:40px;border-radius:8px;object-fit:cover;flex:none}
#hr-chains .s7c .c7-who b{display:block;color:var(--ink);font-size:15px;font-weight:700;line-height:1.3}
#hr-chains .s7c .c7-who span{display:block;color:var(--muted);font-size:13px;line-height:1.35;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;max-width:240px}
#hr-chains .s7c .c7-card p{margin-top:12px;font-size:15px;line-height:1.55;color:var(--body)}
#hr-chains .s7c .c7-res{margin:48px auto 0;max-width:980px;text-align:center;font-size:15.5px;line-height:1.9;color:var(--body)}
#hr-chains .s7c .c7-res a{text-decoration:none;white-space:nowrap}
#hr-chains .s7c .c7-res b{color:var(--ink)}
#hr-chains .s7c .c7-res i{font-style:normal;color:var(--line);padding:0 10px}
#hr-chains .s7c .s7src{margin-top:10px;text-align:center}
/* 8 price */
#hr-chains .price{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:48px;align-items:start}
#hr-chains .price ol{margin:0;padding:0;list-style:none;counter-reset:p;display:flex;flex-direction:column;gap:16px}
#hr-chains .price ol li{counter-increment:p;position:relative;padding-left:46px;color:var(--ink);font-weight:800;font-size:17px;margin:0}
#hr-chains .price ol li::before{content:counter(p);position:absolute;left:0;top:0;width:32px;height:32px;border-radius:10px;background:#fff;color:var(--blue);display:flex;align-items:center;justify-content:center;font-weight:900}
#hr-chains .price ol li span{display:block;font-weight:500;color:var(--body);font-size:15px}
#hr-chains .incl{background:#fff;border:1px solid var(--line);border-radius:18px;padding:24px;display:flex;flex-direction:column;gap:12px}
#hr-chains .incl ul{margin:0;padding-left:18px;display:flex;flex-direction:column;gap:6px;color:var(--ink);font-weight:700}
/* 9 faq */
#hr-chains .faq{max-width:860px;border-top:2px solid var(--ink)}
#hr-chains .faq details{border-bottom:1px solid var(--line)}
#hr-chains .faq summary{cursor:pointer;padding:20px 0;font-weight:800;color:var(--ink);font-size:17px;list-style:none;display:flex;justify-content:space-between;gap:14px}
#hr-chains .faq summary::-webkit-details-marker{display:none}
#hr-chains .faq summary::after{content:"+";color:var(--blue);font-size:24px;line-height:1}
#hr-chains .faq details[open] summary::after{content:"–"}
#hr-chains .faq details p{padding:0 0 20px;max-width:70ch}
/* 10 final */
#hr-chains .final{background:var(--tint);text-align:center;border-top:1px solid var(--line)}
#hr-chains .final .in{display:flex;flex-direction:column;align-items:center;gap:18px;padding-block:84px}
#hr-chains .final h2{max-width:24ch}
#hr-chains .final p{font-size:18px;max-width:60ch}
#hr-chains .final .ctas{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:6px}
#hr-chains .final .alt{font-size:14.5px;color:var(--muted);line-height:2.2}
/* PPC Form (CF7 11eec7d) in the hero card and in the site popup, drawn like the design file's form.
   The form's own styles (css/ppc-hims.css) load only on the PPC templates. These rules replace them here and
   override the site-wide CF7 rules in style.css (hidden labels, 44px fields, smaller popup fields). */
#hr-chains .form .wpcf7, #hr-chains .form .wpcf7 form{width:100%;min-width:0;margin:0}
:is(#hr-chains, #myPopup) .lead-form.hr-demo-form{display:block;color:var(--body);font-size:16px;line-height:1.6}
:is(#hr-chains, #myPopup) .hr-form-step{display:flex;flex-direction:column;gap:12px}
:is(#hr-chains, #myPopup) .hr-progress{display:flex;flex-direction:column;gap:6px;margin:0 0 2px}
:is(#hr-chains, #myPopup) .hr-progress-label{font-size:12px;font-weight:800;letter-spacing:.08em;color:var(--muted)}
:is(#hr-chains, #myPopup) .hr-progress-bar{height:4px;border-radius:2px;background:var(--line);overflow:hidden}
:is(#hr-chains, #myPopup) .hr-progress-bar span{display:block;height:100%;border-radius:2px;background:var(--blue)}
:is(#hr-chains, #myPopup) .hr-step-title{font-weight:800;color:var(--ink);font-size:16.5px}
:is(#hr-chains, #myPopup) .hr-bed-options{display:grid;grid-template-columns:1fr 1fr;gap:9px}
:is(#hr-chains, #myPopup) .hr-bed-option{display:flex;justify-content:space-between;align-items:center;gap:10px;width:100%;min-height:0;margin:0;padding:13px 14px;border:1.5px solid var(--line);border-radius:12px;background:#fff;box-shadow:none;color:var(--ink);font-family:var(--font);font-size:15.5px;font-weight:800;line-height:normal;letter-spacing:normal;text-align:left;text-transform:none;cursor:pointer}
:is(#hr-chains, #myPopup) .hr-bed-option:hover, :is(#hr-chains, #myPopup) .hr-bed-option:focus{border-color:var(--blue);background:#F2F7FF;color:var(--ink)}
:is(#hr-chains, #myPopup) .hr-bed-option strong{font-weight:inherit}
:is(#hr-chains, #myPopup) .hr-bed-option.hr-bed-full{grid-column:1/-1;font-weight:700;color:var(--muted)}
:is(#hr-chains, #myPopup) .hr-arrow{flex:none;color:var(--blue);font-size:22px;line-height:1}
:is(#hr-chains, #myPopup) .hr-selected-bed{display:flex;align-items:center;gap:8px;background:var(--tint);border-radius:10px;padding:9px 12px;color:var(--ink);font-size:14px;font-weight:800}
:is(#hr-chains, #myPopup) .hr-selected-bed-value{flex:1;min-width:0}
:is(#hr-chains, #myPopup) .hr-selected-bed button{margin:0;padding:0;border:0;border-radius:0;background:none;box-shadow:none;color:var(--blue);font-family:var(--font);font-size:14px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(#hr-chains, #myPopup) .hr-selected-bed button:hover, :is(#hr-chains, #myPopup) .hr-selected-bed button:focus{background:none;color:var(--blue)}
:is(#hr-chains, #myPopup) .hr-selected-bed .hr-back-step{padding:0 2px;font-size:22px;line-height:1}
:is(#hr-chains, #myPopup) .hr-demo-form button:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(#hr-chains, #myPopup) .hr-demo-form .row-2{display:flex;flex-direction:column;gap:12px}
:is(#hr-chains, #myPopup) .hr-demo-form .field{display:flex;flex-direction:column;gap:4px;min-width:0;margin:0}
:is(#hr-chains, #myPopup) .hr-demo-form .field label{position:static;display:block;width:auto;height:auto;margin:0;padding:0;overflow:visible;clip:auto;clip-path:none;white-space:normal;color:var(--ink);font-size:12.5px;font-weight:800;line-height:inherit}
:is(#hr-chains, #myPopup) .lead-form .row-2 .field .wpcf7-form-control{width:100%;min-width:0;height:auto;margin:0;padding:11px 12px;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:none;color:var(--body);font-family:var(--font);font-size:15px;line-height:normal;transition:none}
:is(#hr-chains, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus{border-color:var(--line);outline:0}
:is(#hr-chains, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
/* Mobile number: "+91" box in front of the field (built by the script below) */
:is(#hr-chains, #myPopup) .hr-phone{display:flex;border:1px solid var(--line);border-radius:10px;background:#fff;overflow:hidden}
:is(#hr-chains, #myPopup) .hr-phone:focus-within{outline:3px solid var(--sky);outline-offset:2px}
:is(#hr-chains, #myPopup) .hr-phone:has(.wpcf7-not-valid){border-color:#DC3232}
:is(#hr-chains, #myPopup) .hr-cc{flex:none;display:flex;align-items:center;padding:0 10px 0 12px;border-right:1px solid var(--line);background:var(--tint);color:var(--ink);font-size:15px;font-weight:700}
:is(#hr-chains, #myPopup) .lead-form .row-2 .field .hr-phone .wpcf7-form-control{flex:1;border:0;border-radius:0;outline:0}
:is(#hr-chains, #myPopup) .hr-demo-form .wpcf7-not-valid-tip{margin-top:4px;color:#B42318;font-size:12.5px;font-weight:700;line-height:1.3}
:is(#hr-chains, #myPopup) .hr-demo-form .form-submit-btn{display:flex;flex-direction:column;position:relative;margin:0}
:is(#hr-chains, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit{display:block;width:100%;height:auto;margin:0;padding:15px 26px;border:0;border-radius:12px;background:var(--blue);box-shadow:none;color:#fff;font-family:var(--font);font-size:16px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(#hr-chains, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:hover{background:#0B57C7;box-shadow:none}
:is(#hr-chains, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(#hr-chains, #myPopup) .hr-demo-form .form-note{margin:0;color:var(--muted);font-size:13.5px;line-height:inherit;text-align:center}
/* Site popup (#myPopup, templates/lead-popup.php) on this page: drawn like the hero form card */
body #myPopup{max-width:480px;padding:28px;border-radius:20px;color:var(--body);font-family:var(--font)}
body #myPopup .widget-heading{margin:0 0 14px;padding-right:32px;text-align:left}
body #myPopup .widget-heading .heading-title{margin:0;color:var(--ink);font-size:24px;font-weight:900;line-height:1.15;letter-spacing:-.015em}
body #myPopup .widget-heading .description-text{margin:8px 0 0;color:var(--body);font-size:14.5px;line-height:1.6}
/* responsive */
@media (max-width:1020px){
  #hr-chains .price{grid-template-columns:1fr}
  #hr-chains .gl-grid{grid-template-columns:1fr;gap:28px}
  #hr-chains .zero{position:static}
  #hr-chains .sec{padding-block:60px}
}
@media (max-width:900px){
  #hr-chains .hB .hB-top{grid-template-columns:minmax(0,1fr);gap:28px;padding-top:36px}
  #hr-chains .hB .band{grid-template-columns:repeat(2,minmax(0,1fr))}
  #hr-chains .hB .band figure{height:150px}
  #hr-chains .mx{padding-block:56px}
  #hr-chains .mA .a-grid{grid-template-columns:minmax(0,1fr);gap:20px}
  #hr-chains .mA .a-tabs{flex-direction:row;overflow-x:auto;border-top:0;gap:8px;margin-top:20px;padding-bottom:4px}
  #hr-chains .mA .a-tabs button{flex:none;border:1.5px solid var(--line);border-radius:999px;padding:8px 14px}
  #hr-chains .mA .a-tabs button::before{display:none}
  #hr-chains .mA .a-tabs button[aria-selected="true"]{background:var(--ink);border-color:var(--ink)}
  #hr-chains .mA .a-tabs button[aria-selected="true"] b{color:#fff}
  #hr-chains .mA .a-tabs b{font-size:14.5px}
  #hr-chains .mA .a-tabs small{display:none}
  #hr-chains .mA .a-links{gap:0 22px;margin-top:6px}
  #hr-chains .mx .ml{min-height:44px}
  #hr-chains .sh2 .in{padding-block:48px}
  #hr-chains .sh2-boxes{grid-template-columns:minmax(0,1fr)}
  #hr-chains .s7c{padding-block:60px}
  #hr-chains .s7c .c7-card{width:280px}
  #hr-chains .s7c .c7-res a{display:inline-block;padding-block:12px}
}
@media (max-width:820px){
  #hr-chains .gv3{padding-top:72px}
  #hr-chains .gv3-points{grid-template-columns:1fr;gap:26px}
  #hr-chains .gv3-src{padding-bottom:72px}
}
@media (max-width:760px){
  #hr-chains .marquee ul{gap:18px}
  #hr-chains .lg-tile{width:176px;height:84px}
  #hr-chains .lg-tile img{width:150px;height:54px}
  #hr-chains .marquee-track{animation-duration:45s}
}
@media (max-width:640px){
  #hr-chains .frame video{aspect-ratio:1/1}
  #hr-chains .s7c .c7-res i{display:none}
  #hr-chains .s7c .c7-res a{display:block;padding-block:9px}
}
@media (max-width:600px){
  #hr-chains .head{margin-bottom:28px}
  #hr-chains .form{padding:22px}
  :is(#hr-chains, #myPopup) .hr-bed-options{grid-template-columns:1fr}
  body #myPopup{padding:22px}
}
@media (prefers-reduced-motion:reduce){
  html{scroll-behavior:auto}
  #hr-chains .mx a.ml span{transition:none}
  #hr-chains .marquee{overflow-x:auto;-webkit-mask-image:none;mask-image:none}
  #hr-chains .marquee-track{animation:none}
  #hr-chains .marquee ul[aria-hidden]{display:none}
  #hr-chains .s7c .c7-track{animation:none}
  #hr-chains .s7c .c7-rows{overflow-x:auto}
}
</style>

<main class="hr-chains" id="hr-chains">

<!-- 1. HERO -->
<section class="hx hB">
  <div class="in hB-top">
    <div>
      <h1>Hospital software for hospital chains &amp; groups</h1>
      <p class="hB-big">See every hospital in your group from one screen</p>
      <p class="hB-lead">For groups with two or more hospitals. One patient record across your hospitals and one dashboard for the group, while each hospital keeps its own rates, medicine list and departments. Add a new hospital without buying a new system.</p>
      <div class="htrust">
        <div class="hrow">
          <div class="faces" aria-hidden="true"><img src="<?php echo esc_url( $hr_chn_img['people/vimal-dhaduk'] ); ?>" alt=""><img src="<?php echo esc_url( $hr_chn_img['people/bhaumik-rathore'] ); ?>" alt=""><img src="<?php echo esc_url( $hr_chn_img['people/sanjeev-desai'] ); ?>" alt=""><img src="<?php echo esc_url( $hr_chn_img['people/rajesh-dhirawani'] ); ?>" alt=""><img src="<?php echo esc_url( $hr_chn_img['people/monil-parmar'] ); ?>" alt=""></div>
          <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
        </div>
        <div class="hrow">
          <div class="tiles"><span><img src="<?php echo esc_url( $hr_chn_img['badges/nabh'] ); ?>" alt="NABH certified"></span><span><img src="<?php echo esc_url( $hr_chn_img['badges/abdm'] ); ?>" alt="ABDM compliant"></span><span><img src="<?php echo esc_url( $hr_chn_img['badges/nha'] ); ?>" alt="NHA approved"></span><span><img src="<?php echo esc_url( $hr_chn_img['badges/iso-27001'] ); ?>" alt="ISO 27001"></span></div>
          <p><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="hrates"><span><b>4.8</b> <i class="stars">★★★★★</i> Google</span><span><b>4.8</b> <i class="stars">★★★★★</i> Capterra</span><span><b>5.0</b> <i class="stars">★★★★★</i> G2</span></div>
      </div>
    </div>
    <div class="form" id="demo">
      <h2>Book a free demo</h2>
      <p class="sub">A free 30-minute demo for your group. Bring your operations head, finance head and IT head.</p>
      <?php if ( shortcode_exists( 'contact-form-7' ) ) { echo do_shortcode( $hr_chn_form ); } ?>
    </div>
  </div>
  <div class="band"><figure><img src="<?php echo esc_url( $hr_chn_img['photos/t-vibrant'] ); ?>" alt="Vibrant Multispeciality Hospital, Vapi" loading="lazy"><figcaption>Vibrant Multispeciality Hospital<span>Vapi · 200+ beds</span></figcaption></figure><figure><img src="<?php echo esc_url( $hr_chn_img['photos/t-shraddha'] ); ?>" alt="Shraddha Aarogya Mandir, Vapi" loading="lazy"><figcaption>Shraddha Aarogya Mandir<span>Vapi · 150 beds</span></figcaption></figure><figure><img src="<?php echo esc_url( $hr_chn_img['photos/t-universal'] ); ?>" alt="Universal Hospital, Surat" loading="lazy"><figcaption>Universal Hospital<span>Surat · 200 beds</span></figcaption></figure><figure><img src="<?php echo esc_url( $hr_chn_img['photos/t-jabalpur'] ); ?>" alt="Jabalpur Hospital &amp; Research Centre, Jabalpur" loading="lazy"><figcaption>Jabalpur Hospital &amp; Research Centre<span>Jabalpur · 300+ beds</span></figcaption></figure></div>
  <div class="in"><p class="band-l">Some of the hospitals that run on Healthray.</p></div>
</section>

<!-- 2. HOSPITALS ON HEALTHRAY -->
<section class="logos" aria-labelledby="lg-h">
  <div class="in">
    <h2 id="lg-h">Hospitals and groups that run on Healthray</h2>
  </div>
  <div class="marquee">
    <div class="marquee-track">
      <ul id="logoList">
        <?php foreach ( $hr_chn_logos as $hr_chn_l ) : ?>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_chn_img[ $hr_chn_l[0] ] ); ?>" alt="<?php echo esc_attr( $hr_chn_l[1] ); ?>"></span><span class="lg-badge"><?php echo $hr_chn_badge; ?><?php echo esc_html( $hr_chn_l[2] ); ?></span></li>
        <?php endforeach; ?>
      </ul>
    </div>
  </div>
</section>

<!-- 3. THE GROUP VIEW -->
<section class="gv3" aria-labelledby="gv3-h">
  <div class="in">
    <div class="gv3-head">
      <p class="gv3-eyebrow">For hospital groups</p>
      <h2 id="gv3-h">Every hospital in your group. <span>One login.</span></h2>
      <p class="gv3-sub">Move from one hospital to the next without signing out. Each one opens with its own dashboard, its own beds and its own bills, so you see how every hospital is doing today.</p>
    </div>
    <figure class="gvx"><picture><source media="(max-width:640px)" srcset="<?php echo esc_url( $hr_chn_img['gv-v3m'] ); ?>" width="1600" height="1860"><img src="<?php echo esc_url( $hr_chn_img['gv-v3'] ); ?>" alt="Healthray OPD and IPD dashboard for the Vapi hospital, with the Surat and Valsad hospitals stacked behind it and a Signed in once badge for the group COO" width="2400" height="1400" loading="lazy"></picture></figure>
    <div class="gv3-points">
      <div><b>Switch hospital in one click</b><p>Pick a hospital from the menu. Its dashboard, wards and billing open, with no second login.</p></div>
      <div><b>Each hospital keeps its own way</b><p>Its own rates, medicine list, departments and staff, set up for how that hospital works.</p></div>
      <div><b>Group numbers in one place</b><p>OPD, admissions, beds and collection for the whole group, side by side.</p></div>
    </div>
    <p class="gv3-src">Healthray dashboard with demo data.</p>
  </div>
</section>

<!-- 3b. WHAT EACH HOSPITAL GETS -->
<section class="mx mA" aria-labelledby="mA-h"><div class="in"><div class="a-grid">
  <div>
    <h2 id="mA-h">Every hospital in the group gets the full system</h2>
    <p class="mx-sub">Each hospital switches on only the parts it needs, and adds the rest later.</p>
    <div class="a-tabs" role="tablist" aria-label="Parts of the system">
      <button type="button" role="tab" id="mt-front" aria-controls="mp-front" aria-selected="true" data-k="front"><b>Front desk and doctors</b><small>OPD, Doctor's notes (EMR), ABDM and ABHA</small></button>
      <button type="button" role="tab" id="mt-wards" aria-controls="mp-wards" aria-selected="false" data-k="wards" tabindex="-1"><b>Wards and operation theatre</b><small>IPD and wards, Operation theatre</small></button>
      <button type="button" role="tab" id="mt-money" aria-controls="mp-money" aria-selected="false" data-k="money" tabindex="-1"><b>Billing and insurance</b><small>Billing, TPA and insurance</small></button>
      <button type="button" role="tab" id="mt-stock" aria-controls="mp-stock" aria-selected="false" data-k="stock" tabindex="-1"><b>Pharmacy, lab and stores</b><small>Pharmacy, Laboratory, Stores and inventory</small></button>
    </div>
  </div>
  <div>
    <div class="a-panel" role="tabpanel" id="mp-front" aria-labelledby="mt-front"><div class="pic"><picture><source media="(max-width:640px)" srcset="<?php echo esc_url( $hr_chn_img['dk-f1m'] ); ?>" width="1000" height="834"><img src="<?php echo esc_url( $hr_chn_img['dk-f1'] ); ?>" alt="Healthray OPD token board with each doctor's queue, tokens, ABHA ticks, status and waiting time, and the waiting-area token message" width="2400" height="1600" loading="lazy"></picture></div><p>Appointments, the OPD queue, prescriptions and ABHA IDs.</p><div class="a-links"><a class="ml" href="https://healthray.com/modules/opd-management/">OPD<span aria-hidden="true">→</span></a><a class="ml" href="https://healthray.com/emr-software/">Doctor's notes (EMR)<span aria-hidden="true">→</span></a><a class="ml" href="https://healthray.com/abdm/">ABDM and ABHA<span aria-hidden="true">→</span></a></div></div>
    <div class="a-panel" role="tabpanel" id="mp-wards" aria-labelledby="mt-wards" hidden><div class="pic"><picture><source media="(max-width:640px)" srcset="<?php echo esc_url( $hr_chn_img['dk-w1m'] ); ?>" width="1000" height="919"><img src="<?php echo esc_url( $hr_chn_img['dk-w1'] ); ?>" alt="Healthray bed availability board with occupied, vacant and under-cleaning beds in ICU and General Ward, Admit Patient and Ready for discharge" width="2400" height="1600" loading="lazy"></picture></div><p>Admissions, beds, doctor and nursing notes, and the OT.</p><div class="a-links"><a class="ml" href="https://healthray.com/modules/ipd-management/">IPD and wards<span aria-hidden="true">→</span></a><span class="ml nl">Operation theatre</span></div></div>
    <div class="a-panel" role="tabpanel" id="mp-money" aria-labelledby="mt-money" hidden><div class="pic"><picture><source media="(max-width:640px)" srcset="<?php echo esc_url( $hr_chn_img['dk-b3m'] ); ?>" width="1000" height="748"><img src="<?php echo esc_url( $hr_chn_img['dk-b3'] ); ?>" alt="Healthray final IPD bill showing what the insurer pays and what the patient pays, with the deposit already taken off" width="2400" height="1600" loading="lazy"></picture></div><p>OPD and IPD bills, deposits, interim bills and TPA patients.</p><div class="a-links"><a class="ml" href="https://healthray.com/medical-billing-software/">Billing<span aria-hidden="true">→</span></a><span class="ml nl">TPA and insurance</span></div></div>
    <div class="a-panel" role="tabpanel" id="mp-stock" aria-labelledby="mt-stock" hidden><div class="pic"><picture><source media="(max-width:640px)" srcset="<?php echo esc_url( $hr_chn_img['st-v1m'] ); ?>" width="1000" height="853"><img src="<?php echo esc_url( $hr_chn_img['st-v1'] ); ?>" alt="Healthray prescription screen showing each medicine with this hospital's pharmacy stock, and the order sent to the pharmacy" width="2400" height="1600" loading="lazy"></picture></div><p>The drug library, lab tests, and store stock and indents.</p><div class="a-links"><a class="ml" href="https://healthray.com/pharmacy-management-system/">Pharmacy<span aria-hidden="true">→</span></a><a class="ml" href="https://healthray.com/laboratory-information-management-system/">Laboratory<span aria-hidden="true">→</span></a><a class="ml" href="https://healthray.com/inventory-management-software/">Stores and inventory<span aria-hidden="true">→</span></a></div></div>
  </div>
</div></div></section>

<!-- 4. SHARED AND LOCAL -->
<section class="sh2">
  <div class="in">
    <div class="sh2-head">
      <h2>What the Group Shares, What Each Hospital Keeps</h2>
      <p>Your hospitals don't all charge the same or stock the same medicines. Healthray keeps those differences.</p>
    </div>
    <div class="sh2-boxes">
      <div class="sh2-shared"><b>Shared by the whole group</b><ul><li>One patient record</li><li>Group dashboard and reports</li><li>Central stores and purchase</li><li>Who can see what</li></ul></div>
      <div class="sh2-own"><b>Each hospital keeps its own</b><ul><li>Rates</li><li>Medicine list</li><li>Departments</li><li>Reports</li></ul></div>
    </div>
    <p class="sh2-next"><b>+ Your next hospital</b> joins the same system. No new software to buy.</p>
  </div>
</section>

<!-- 5. VIDEO -->
<section class="sec" id="video">
  <div class="in">
    <div class="head c">
      <span class="label">Product tour</span>
      <h2>Watch One Day at a Hospital Group on Healthray</h2>
      <p>One login for every hospital, one patient file from Vapi to Surat, each hospital's own rates, one central store, and a new hospital joining. A demo group with demo data.</p>
    </div>
    <div class="vid-wrap">
      <div class="frame">
        <div class="frame-bar"><i></i><i></i><i></i><span>app.healthray.com</span></div>
        <video id="walk" muted playsinline loop preload="none"<?php if ( $hr_chn_img['video/poster'] ) : ?> poster="<?php echo esc_url( $hr_chn_img['video/poster'] ); ?>"<?php endif; ?> data-poster-phone="<?php echo esc_url( $hr_chn_img['video/poster-phone'] ); ?>" aria-label="Healthray for hospital groups: one day at a demo group of three hospitals, from the group dashboard to a new hospital joining. Click to pause or play."><source src="<?php echo esc_url( $hr_chn_img['video/tour-phone'] ); ?>" type="video/mp4" media="(max-width:640px)"><source src="<?php echo esc_url( $hr_chn_img['video/tour'] ); ?>" type="video/mp4"></video>
        <button class="vplay" id="vplay" type="button" aria-label="Play the video"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button>
      </div>
      <div class="vid-foot">
        <p class="vid-line">Want to see it with your own hospitals and rates? Book a free 30-minute demo and we will set it up for your group.</p>
        <a class="btn hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
    </div>
  </div>
</section>

<!-- 6. ADDING A HOSPITAL -->
<section class="sec tint" id="golive">
  <div class="in">
    <div class="gl-grid">
      <div class="zero">
        <span class="label">Adding a hospital</span>
        <div class="z-big">0</div>
        <p class="z-big-l">days of downtime at the 7 hospitals in our case studies when they switched to Healthray</p>
        <p>A new or newly bought hospital joins the same way: one department at a time, so its billing counter stays open.</p>
      </div>
      <div>
        <h2 style="font-size:clamp(24px,2.6vw,32px);margin-bottom:16px">Add a Hospital Without Buying a New System</h2>
        <ol class="steps">
          <li><b>We watch how the new hospital works</b>Our team sits with each department, as we did for a week at each department of Shraddha Aarogya Mandir.</li>
          <li><b>Its old data comes with it</b>Patient, billing and stock records are moved in before it starts. At BBMH and Vibrant, this took 1 day.</li>
          <li><b>Its teams learn their own screens</b>Reception, doctors, nurses, OT, pharmacy and billing are trained at that hospital.</li>
          <li><b>It goes live one department at a time</b>OPD first, then IPD and billing, then pharmacy and lab. Group reports include it from day one.</li>
        </ol>
        <p class="gl-src">Single hospitals in our case studies went live in under 2 weeks to 18 working days.</p>
      </div>
    </div>
  </div>
</section>

<!-- 7. VOICES AND RESULTS -->
<section class="s7x s7c" aria-label="What doctors say about Healthray">
  <div class="in">
    <div class="c7-top">
      <span class="s7eye">2,500+ hospitals and clinics in India run on Healthray</span>
      <h2>In their own words</h2>
    </div>
  </div>
  <div class="c7-rows">
    <?php foreach ( $hr_chn_quotes as $hr_chn_r => $hr_chn_row ) : ?>
    <div class="c7-row<?php echo (int) $hr_chn_r + 1; ?>"><div class="c7-track">
      <?php foreach ( array( false, true ) as $hr_chn_copy ) : foreach ( $hr_chn_row as $hr_chn_q ) : ?>
      <div class="c7-card"<?php echo $hr_chn_copy ? ' aria-hidden="true"' : ''; ?>><div class="c7-who"><img src="<?php echo esc_url( $hr_chn_img[ $hr_chn_q[0] ] ); ?>" alt="<?php echo $hr_chn_copy ? '' : esc_attr( $hr_chn_q[1] ); ?>" width="40" height="40" loading="lazy"><div><b><?php echo esc_html( $hr_chn_q[1] ); ?></b><span><?php echo esc_html( $hr_chn_q[2] ); ?></span></div></div><p><?php echo esc_html( $hr_chn_q[3] ); ?></p></div>
      <?php endforeach; endforeach; ?>
    </div></div>
    <?php endforeach; ?>
  </div>
  <div class="in">
    <p class="c7-res"><a href="https://healthray.com/case-studies/vibrant-hospital/"><b>₹25 lakh</b> saved a year at Vibrant</a><i>|</i><a href="https://healthray.com/case-studies/universal-hospital/"><b>45%</b> shorter waiting at Universal</a><i>|</i><a href="https://healthray.com/case-studies/jabalpur-hospital/"><b>50%</b> faster billing at Jabalpur</a><i>|</i><a href="https://healthray.com/case-studies/lotus-hospital/"><b>30%</b> faster bed allocation at Lotus</a></p>
    <p class="s7src">Quotes and numbers from Healthray's published case studies and reviews.</p>
  </div>
</section>

<!-- 8. PRICE -->
<section class="sec tint" id="price">
  <div class="in">
    <div class="price">
      <div style="display:flex;flex-direction:column;gap:20px">
        <h2>What It Costs for Your Group</h2>
        <p style="font-size:17px">There is no fixed price list, because no two groups are the same size. Four things decide your price.</p>
        <ol>
          <li>Number of hospitals<span>A group of 2 pays less than a group of 10.</span></li>
          <li>Beds in each hospital<span>You pay for the size each hospital is today.</span></li>
          <li>Departments you switch on<span>Each hospital can start with what it needs and add the rest later.</span></li>
          <li>Cloud or your own servers<span>Hosted by us in India, installed in your hospitals, or both.</span></li>
        </ol>
      </div>
      <div class="incl">
        <span class="label">Included in every price</span>
        <ul><li>Moving each hospital's old data</li><li>Training at each hospital</li><li>Your price in writing within 24 hours of the demo</li></ul>
        <p class="small">WhatsApp and SMS messages are paid separately.</p>
        <a class="btn hr-cta-btn" href="#demo">Get a price for my group</a>
      </div>
    </div>
  </div>
</section>

<!-- 9. FAQ -->
<section class="sec">
  <div class="in">
    <div class="head"><h2>Questions Hospital Groups Ask Us</h2></div>
    <div class="faq">
      <?php foreach ( $hr_chn_faq as $hr_chn_i => $hr_chn_f ) : ?>
      <details<?php echo 0 === $hr_chn_i ? ' open' : ''; ?>><summary><?php echo esc_html( $hr_chn_f['q'] ); ?></summary><p><?php echo esc_html( $hr_chn_f['a'] ); ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 10. FINAL CTA -->
<section class="final">
  <div class="in">
    <h2>See Your Whole Group on One Screen</h2>
    <p>A free 30-minute video call, set up with your own hospitals. Your price comes in writing within 24 hours, and you do not have to buy.</p>
    <div class="ctas"><a class="btn hr-cta-btn" href="#demo">Book a free demo</a><a class="btn line" href="#" id="shareAll">Send this page to your partners</a></div>
    <p class="alt">Just one hospital? <a href="<?php echo esc_url( home_url( '/solutions/multispeciality-hospitals/' ) ); ?>">See software for multispeciality hospitals</a> · Under 50 beds? <a href="<?php echo esc_url( home_url( '/solutions/small-hospitals-nursing-homes/' ) ); ?>">See software for small hospitals</a></p>
  </div>
</section>


</main>

<script id="hr-chains-js">
/* Design file JS (chains.js), plus the steps of the PPC Form. The site popup (second copy of the form) is printed
   after this script, so everything starts on DOMContentLoaded. */
(function(){
  function init(){
  var root=document.getElementById('hr-chains'); if(!root) return;
  window.dataLayer=window.dataLayer||[];
  function push(ev,extra){var o={event:ev,form_name:'hospital_chains_demo'};for(var k in (extra||{}))o[k]=extra[k];window.dataLayer.push(o);}

  /* logo strip: a second copy makes the slide loop without a gap */
  var ll=document.getElementById('logoList');
  if(ll){var cp=ll.cloneNode(true);cp.removeAttribute('id');cp.setAttribute('aria-hidden','true');cp.querySelectorAll('img').forEach(function(i){i.alt='';});ll.parentNode.appendChild(cp);}

  /* modules tour: pick a desk, show its screen (arrow keys move between tabs) */
  var tabs=[].slice.call(root.querySelectorAll('.mA .a-tabs button'));
  function pickTab(t,focus){
    tabs.forEach(function(x){var on=x===t;x.setAttribute('aria-selected',on?'true':'false');x.tabIndex=on?0:-1;var p=document.getElementById('mp-'+x.getAttribute('data-k'));if(p)p.hidden=!on;});
    if(focus) t.focus();
  }
  tabs.forEach(function(t,i){
    t.addEventListener('click',function(){pickTab(t);});
    t.addEventListener('keydown',function(e){
      var n=null;
      if(e.key==='ArrowRight'||e.key==='ArrowDown') n=tabs[(i+1)%tabs.length];
      else if(e.key==='ArrowLeft'||e.key==='ArrowUp') n=tabs[(i-1+tabs.length)%tabs.length];
      else if(e.key==='Home') n=tabs[0]; else if(e.key==='End') n=tabs[tabs.length-1];
      if(n){e.preventDefault();pickTab(n,true);}
    });
  });

  /* product video: no controls, plays when on screen, click to pause */
  var v=document.getElementById('walk');
  if(v){
    var fr=v.closest('.frame'),pb=document.getElementById('vplay');
    if(window.matchMedia&&matchMedia('(max-width:640px)').matches&&v.getAttribute('data-poster-phone')){v.poster=v.getAttribute('data-poster-phone');}
    var calm=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches, userPaused=false;
    function go(){v.preload='auto';v.play().catch(function(){fr.classList.add('paused');});}
    function toggle(){if(v.paused){userPaused=false;go();}else{userPaused=true;v.pause();fr.classList.add('paused');}}
    v.addEventListener('click',toggle); pb.addEventListener('click',toggle);
    v.addEventListener('playing',function(){fr.classList.remove('paused');});
    if(calm) fr.classList.add('paused');
    if('IntersectionObserver' in window){
      new IntersectionObserver(function(es){es.forEach(function(e){
        if(e.isIntersecting){ if(!calm&&!userPaused) go(); }
        else if(!v.paused){v.pause();}
      });},{threshold:.45}).observe(v);
    }else{fr.classList.add('paused');}
  }

  /* FAQ: first one open by default, only one open at a time */
  var fq=root.querySelectorAll('.faq details');
  fq.forEach(function(d){d.addEventListener('toggle',function(){if(d.open)fq.forEach(function(o){if(o!==d)o.open=false;});});});

  /* WhatsApp share for the whole page */
  var sa=document.getElementById('shareAll');
  if(sa){
    var base=location.href.split('#')[0];
    sa.href='https://wa.me/?text='+encodeURIComponent('Have a look: Healthray for hospital groups. Every hospital in the group on one screen.\n'+base);
    sa.target='_blank';sa.rel='noopener';
    sa.addEventListener('click',function(){push('whatsapp_share',{place:'final_section'});});
  }

  /* PPC Form, hero card and popup. Its own script (js/ppc-hims.js) loads only on the PPC templates, so its steps
     run here: a "how many hospitals" button fills the hidden bed_size field and opens step 2, "change" goes back. */
  document.querySelectorAll('#hr-chains .hr-demo-form, #myPopup .hr-demo-form').forEach(function(box){
    var s1=box.querySelector('.hr-step-1'),s2=box.querySelector('.hr-step-2'),wrap=box.closest('.wpcf7');
    if(!s1||!s2) return;
    var bed=box.querySelector('[name="bed_size"]'),pick=box.querySelector('.hr-selected-bed-value'); // by name: CF7 drops the id on the popup copy
    function step(n){s1.style.display=n===1?'':'none';s2.style.display=n===2?'':'none';}
    box.querySelectorAll('.hr-bed-option').forEach(function(b){b.addEventListener('click',function(){
      var val=b.getAttribute('data-bed'); if(!val) return;
      if(bed) bed.value=val; if(pick) pick.textContent=val;
      push('form_step',{step:2,group_size:val});
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
    if(wrap){
      /* a server-side error on a step-1 answer (e.g. bed_size missing) would be hidden on step 1: show step 2 */
      wrap.addEventListener('wpcf7invalid',function(){step(2);});
      /* CF7 empties the fields after a sent form: start again at step 1. The form reset leaves a hidden field's
         value as set by script, so bed_size is cleared here. */
      wrap.addEventListener('wpcf7reset',function(){if(bed) bed.value='';if(pick) pick.textContent='';step(1);});
    }
  });

  /* Mobile number, as in js/ppc-hims.js: "+91" in its own box, the field takes the 10-digit number, and "+91 " is
     added to the value CF7 sends (the server check needs the country code). Pasted numbers keep their last 10 digits. */
  var CODE='+91',MAX=10;
  function digits(val){var d=val.replace(/\D/g,'').replace(/^0+/,'');return d.length>MAX?d.slice(-MAX):d;}
  document.querySelectorAll('#hr-chains .hr-demo-form input[name="your-number"], #myPopup .hr-demo-form input[name="your-number"]').forEach(function(input){
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
  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded',init); else init();
})();
</script>
