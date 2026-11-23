<?php
/**
 * Template Name: Multispeciality Hospitals
 * Template Post Type: page
 *
 * /solutions/multispeciality-hospitals/ (page 82553). Also picked up by slug (page-{slug}.php).
 * Everything for this page is in this ONE file: images, FAQs, schema, CSS (<style>) and JS (<script>).
 * The Roots wrapper prints <head>, the site header and footer, so no wp_head / enqueue hooks here.
 * CSS is scoped to .msh, and to #myPopup for the popup. Yoast handles title, meta, canonical and base schema; this adds FAQPage.
 * Lead form: the PPC Form (CF7 11eec7d), in the hero card and in the site popup (#myPopup). Every "Book a demo"
 * button has .hr-cta-btn, so js/script.js opens that popup; the popup gets this form through hr_ppc_popup_args.
 */
defined('ABSPATH') || exit;

$msh_up   = 'https://healthray.com/wp-content/uploads/';
$msh_logo = get_stylesheet_directory_uri() . '/assets/ppc-hims/';
$msh_form = '[contact-form-7 id="11eec7d" title="PPC Form"]';

// Site popup: same form, heading and line as the hero card.
add_filter('hr_ppc_popup_args', static function () use ($msh_form) {
	return array(
		'form'  => $msh_form,
		'title' => 'Book a free demo',
		'text'  => 'A free 30-minute demo, set up for your departments. Bring your administrator and billing head.',
	);
});

// Images (media library + theme logos)
$msh_img = array(
	'badges/nabh'             => $msh_up . '2026/07/NABH-ehr-certified-150x150.webp',
	'badges/abdm'             => $msh_up . '2026/03/abdm-healthray-150x150.webp',
	'badges/nha'              => $msh_up . '2025/07/NHA.webp',
	'badges/iso-27001'        => $msh_up . '2026/03/iso-27001-healthray-150x150.webp',
	'logos/universal'         => $msh_logo . 'universal-hospital.webp',
	'logos/hzb-arogyam'       => $msh_logo . 'hzb-arogyam-multispeciality-hospital.webp',
	'logos/vibrant'           => $msh_logo . 'vibrant-hospital.webp',
	'logos/parivar'           => $msh_logo . 'parivar-super-speciality-hospital.webp',
	'logos/shraddha'          => $msh_up . '2025/08/Shraddha-Arogya-Mandir.webp',
	'logos/jeevan-rekha'      => $msh_logo . 'jeevan-rekha-hospital.webp',
	'logos/bbmh'              => $msh_logo . 'budha-baba-multispeciality-hospital.webp',
	'logos/pims'              => $msh_logo . 'pims-multi-superspeciality-hospital.webp',
	'logos/jabalpur'          => $msh_logo . 'jabalpur-hospital-research-center.webp',
	'logos/jj-plus'           => $msh_logo . 'jj-plus-hospitals.webp',
	'logos/prabh-aasra'       => $msh_logo . 'prabh-aasra-charitable-hospital.webp',
	'logos/heritage-hospital' => $msh_logo . 'heritage-hospital.webp',
	'people/bhaumik-rathore'  => $msh_up . '2025/12/Dr.-Bhaumik-Rathore-Vibrant-Multispecialty-Hospital.webp',
	'people/sanjeev-desai'    => $msh_up . '2026/10/Dr.-Sanjeev-Desai-Lotus-Hospital.webp',
	'people/deepika-singhavi' => $msh_up . '2026/10/Dr.-Deepika-Singhavi.webp',
	'people/dipak-viradia'    => $msh_up . '2026/10/Dr.-Dipak-Viradia.webp',
	'people/monil-parmar'     => $msh_up . '2025/10/Dr.-Monil-Parmar.webp',
	'people/vimal-dhaduk'     => $msh_up . '2024/04/Dr.-Vimal-Dhaduk.webp',
	'people/hinal-parmar'     => $msh_up . '2026/10/Dr.-Hinal-Parmar-Rainbow-Hospital.webp',
	'people/rajesh-dhirawani' => $msh_up . '2026/10/Dr.-Rajesh-B.-Dhirawani.webp',
	'photos/t-vibrant'        => $msh_up . '2025/12/Vibrant-Hospital-Healthray.webp',
	'photos/t-rainbow'        => $msh_up . '2026/10/Rainbow-Hospital.webp',
	'photos/t-shraddha'       => $msh_up . '2026/10/Shraddha-Aarogya-Mandir.webp',
	'photos/t-lotus'          => $msh_up . '2026/10/Lotus-Hospital.webp',
	'photos/t-jabalpur'       => $msh_up . '2025/11/Jabalpur-Hospital.webp',
	'photos/t-bbmh'           => $msh_up . '2025/12/Budha-Baba-Multispecialty-Hospital-1.webp',
	'photos/t-universal'      => $msh_up . '2025/10/Universal-Hospital.webp',
);

// FAQs: visible list and schema
$msh_faq = array(
	array('q' => 'We already use separate software for lab and pharmacy. Do we keep it?', 'a' => 'You can move everything to Healthray, which has its own lab, pharmacy, radiology and billing. Your old data is moved before you start, so nothing is lost. Vibrant replaced separate software in each department with Healthray.'),
	array('q' => 'Do TPA, insurance and Ayushman patients work in the same software?', 'a' => 'Yes. Charges from the ward, OT, lab and pharmacy reach the bill as they happen, and TPA, insurance and Ayushman claims are billed in the same software, connected to NHCX. Bills sync with Tally. Insurers must give final cashless approval within 3 hours of your request (IRDAI, May 2024), so the bill needs to be ready when you ask. At Rainbow Hospital, getting a TPA claim ready went from 3 days to 1.'),
	array('q' => 'Can our lab machines and PACS connect?', 'a' => 'Yes. Healthray connects with lab machines (HL7) and PACS, so results come into the patient\'s file without typing.'),
	array('q' => 'What if the internet goes down?', 'a' => 'Run Healthray on a server inside your hospital, or use cloud and a server together. Then OPD, billing and pharmacy keep running without internet.'),
	array('q' => 'Does it help with NABH?', 'a' => 'Healthray is NABH certified as hospital software, and every department\'s records stay in one file. "It helps us to comply with hospital regulations, which was very difficult for us earlier," says Divyesh Gandhi of Universal Hospital.'),
	array('q' => 'We plan to open a second hospital. Can both run on Healthray?', 'a' => 'Yes. All your hospitals run on one system with shared patient records. Each one keeps its own rates, medicine list, departments and reports.'),
	array('q' => 'Where is our data kept, and do we get it back if we leave?', 'a' => 'On Healthray\'s cloud, on a server inside your hospital, or both. Your hospital owns its data and can export it any time. It is encrypted and backed up automatically, and Healthray is ISO 27001 certified for data security. If you leave, all your data is given back within 30 days. This is written in the data agreement we sign with every hospital.'),
	array('q' => 'Who should join the demo?', 'a' => 'Your administrator, your billing or TPA head, and your IT person if you have one. In 30 minutes we show registration to discharge, billing, TPA claims and reports, set up for your departments.'),
);

$msh_url    = get_permalink();
$msh_schema = array(
	'@context'   => 'https://schema.org',
	'@type'      => 'FAQPage',
	'@id'        => $msh_url . '#faq',
	'mainEntity' => array_map(function ($f) {
		return array('@type' => 'Question', 'name' => $f['q'], 'acceptedAnswer' => array('@type' => 'Answer', 'text' => $f['a']));
	}, $msh_faq),
);
?>
<script type="application/ld+json"><?php echo wp_json_encode($msh_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG); ?></script>

<style id="msh-css">
/* Healthray solutions page: multispeciality hospitals.
   Every rule is scoped to .msh so nothing leaks into the theme. Light theme only, like healthray.com. */
.msh, #myPopup{--navy:#0D1B3E; --navy2:#1B3C74; --blue:#0F68EA; --ink:#14284F; --body:#4A5D73; --muted:#6C8093;
  --tint:#EFF4FF; --line:#D9E2F2; --white:#FFFFFF; --sky:#8FB2F5; --soft:#C8D5E3;
  --peach:#FDF1E7; --mint:#E7F6F1; --lilac:#EFEBFC; --sun:#FFF6DF; --green:#157A4A;
  --note-bg:#FFF6D6; --note-line:#E0B33A; --note-ink:#6B4E00;
  --font:"Mulish",system-ui,-apple-system,"Segoe UI",sans-serif;
  color-scheme:light;}
.msh, .msh *{box-sizing:border-box}
.msh [hidden]{display:none!important}
.msh{margin:0;background:var(--white);color:var(--body);font-family:var(--font);font-size:16px;line-height:1.6}
.msh h1, .msh h2, .msh h3{color:var(--ink);margin:0;line-height:1.15;text-wrap:balance;letter-spacing:-.015em}
.msh h2{font-size:clamp(27px,3.4vw,40px);font-weight:900}
.msh h3{font-size:19px;font-weight:800}
.msh p{margin:0}
.msh img, .msh video{max-width:100%;display:block}
.msh a{color:var(--blue)}
.msh a:focus-visible, .msh button:focus-visible, .msh summary:focus-visible, .msh input:focus-visible, .msh select:focus-visible, .msh video:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
.msh .in{max-width:1160px;margin:0 auto;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
.msh .sec{padding-block:88px}
.msh .tint{background:var(--tint)}
.msh .head{max-width:760px;display:flex;flex-direction:column;gap:12px;margin-bottom:44px}
.msh .head.c{margin-inline:auto;text-align:center;align-items:center}
.msh .head p{font-size:18px}
.msh .label{font-size:12px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:var(--blue)}
.msh .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--blue);color:#fff;font-weight:800;text-decoration:none;border:0;border-radius:12px;padding:15px 26px;font-family:var(--font);font-size:16px;cursor:pointer;line-height:1.2}
.msh .btn:hover{background:#0B57C7}
.msh .btn.line{background:#fff;color:var(--ink);border:1.5px solid var(--line)}
.msh .btn.line:hover{border-color:var(--blue);background:#F7FAFF}
.msh .small{font-size:13.5px;color:var(--muted)}
.msh .need{display:inline-block;background:var(--note-bg);border:1.5px dashed var(--note-line);color:var(--note-ink);border-radius:8px;padding:1px 8px;font-size:12.5px;font-weight:800;line-height:1.6}
.msh .stars{color:#F6B73C;letter-spacing:1px}
.msh .ticks{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px}
.msh .ticks li{position:relative;padding-left:30px;color:var(--ink);font-weight:700}
.msh .ticks li::before{content:"";position:absolute;left:0;top:3px;width:20px;height:20px;border-radius:50%;background:#E3F4EC}
.msh .ticks li::after{content:"";position:absolute;left:6px;top:8px;width:8px;height:4px;border-left:2px solid var(--green);border-bottom:2px solid var(--green);transform:rotate(-45deg)}
.msh .hero{background:linear-gradient(135deg,#0D1B3E 0%,#1B3C74 58%,#0F68EA 135%);color:var(--soft)}
.msh .hero .in{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:56px;padding-block:60px 72px;align-items:center}
.msh .hero-l{display:flex;flex-direction:column;gap:20px}
.msh .hero h1{font-size:13px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:var(--sky)}
.msh .big{color:#fff;font-weight:900;font-size:clamp(32px,3.9vw,48px);line-height:1.07;letter-spacing:-.02em;text-wrap:balance}
.msh .lead{font-size:18.5px;color:#DCE6F5;max-width:52ch}
.msh .htrust{display:flex;flex-direction:column;gap:18px;margin-top:6px}
.msh .hrow{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
.msh .hrow p{font-size:15px;color:#DCE6F5;flex:1;min-width:200px}
.msh .hrow p b{color:#fff}
.msh .hrow+.hrow{border-top:1px solid rgba(255,255,255,.14);padding-top:18px}
.msh .faces{display:flex}
.msh .faces img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #fff;background:#fff}
.msh .faces img+img{margin-left:-10px}
.msh .tiles{display:flex;gap:10px}
.msh .tiles span{width:52px;height:52px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;padding:5px}
.msh .tiles img{max-height:100%;width:auto}
.msh .hrates{display:flex;flex-wrap:wrap;gap:6px 20px;font-size:14px;color:#DCE6F5}
.msh .hrates b{color:#fff;font-size:15px}
.msh .hrates i{font-style:normal;color:#F6B73C;font-size:12px;letter-spacing:1px}
.msh .form{color:var(--body);background:#fff;border-radius:20px;padding:28px;box-shadow:0 30px 70px rgba(3,10,30,.45);display:flex;flex-direction:column;gap:14px;scroll-margin-top:90px}
.msh .form h2{font-size:24px}
.msh .form .sub{font-size:14.5px;margin-top:-6px}
.msh .form .wpcf7, .msh .form .wpcf7 form{width:100%;max-width:100%;min-width:0;margin:0}
.msh .hero .in{padding-top:calc(60px + var(--msh-header,0px))}
.msh .logos{border-bottom:1px solid var(--line)}
.msh .logos .in{padding-block:30px 26px}
.msh .logos h2{font-size:13px;font-weight:700;color:var(--muted);text-align:center;margin-bottom:8px;letter-spacing:0}
.msh .marquee{overflow:hidden;padding-top:14px;-webkit-mask-image:linear-gradient(90deg,transparent,#000 5%,#000 95%,transparent);mask-image:linear-gradient(90deg,transparent,#000 5%,#000 95%,transparent)}
.msh .marquee-track{display:flex;width:max-content;animation:msh-logoscroll 60s linear infinite}
.msh .marquee:hover .marquee-track{animation-play-state:paused}
.msh .marquee ul{display:flex;gap:24px;margin:0;padding:0 24px 0 0;list-style:none}
.msh .marquee li{position:relative}
.msh .lg-tile{width:200px;height:94px;padding:0 12px;border:1px solid var(--line);border-radius:14px;background:#fff;display:grid;place-items:center;transition:border-color .2s,box-shadow .2s}
.msh .marquee li:hover .lg-tile{border-color:#BBD2FA;box-shadow:0 8px 20px rgba(27,60,116,.08)}
.msh .lg-tile img{width:168px;height:62px;max-width:none;object-fit:contain}
.msh .lg-badge{position:absolute;top:-11px;left:50%;transform:translateX(-50%);display:inline-flex;align-items:center;gap:5px;background:rgba(239,244,255,.92);border:1px solid #BBD2FA;color:var(--ink);font-size:11.5px;font-weight:700;border-radius:30px;padding:4px 9px 4px 7px;white-space:nowrap;line-height:1}
.msh .lg-badge svg{width:13px;height:13px;color:var(--blue);flex:none}
@keyframes msh-logoscroll{from{transform:translateX(0)}to{transform:translateX(-50%)}}
.msh .vid-wrap{max-width:980px;margin:0 auto}
.msh .frame{position:relative;border-radius:18px;overflow:hidden;background:#0D1B3E;box-shadow:0 30px 70px rgba(27,60,116,.25);border:1px solid var(--line)}
.msh .frame-bar{display:flex;align-items:center;gap:6px;padding:10px 14px;background:#fff;border-bottom:1px solid var(--line)}
.msh .frame-bar i{width:10px;height:10px;border-radius:50%;background:#DCE3EE}
.msh .frame-bar span{margin-left:10px;font-size:12px;color:var(--muted);background:var(--tint);border-radius:6px;padding:2px 12px}
.msh .frame video{width:100%;height:auto;aspect-ratio:16/9;cursor:pointer;background:#0D1B3E}
.msh .vplay{display:none;position:absolute;left:50%;top:calc(50% + 18px);transform:translate(-50%,-50%);width:72px;height:72px;border-radius:50%;border:0;background:#fff;color:var(--blue);cursor:pointer;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(13,27,62,.35)}
.msh .vplay svg{width:28px;height:28px;margin-left:4px}
.msh .frame.paused .vplay{display:flex}
.msh .vid-foot{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:14px;margin-top:22px}
.msh .vid-line{font-size:17px;color:var(--ink);font-weight:600;max-width:60ch;flex:1;min-width:240px}
.msh .story-s{display:grid;grid-template-columns:minmax(0,.9fr) minmax(0,1.1fr);gap:56px}
.msh .steps-s{display:flex;flex-direction:column}
.msh .step{min-height:62vh;display:flex;flex-direction:column;justify-content:center;gap:14px;padding-block:28px;opacity:.35;transition:opacity .3s}
.msh .step.on{opacity:1}
.msh .step .who-to{font-size:12px;font-weight:900;letter-spacing:.08em;text-transform:uppercase;color:var(--blue)}
.msh .step h3{font-size:clamp(23px,2.5vw,30px);font-weight:900}
.msh .step .bf{font-size:15.5px;color:var(--muted)}
.msh .step .bf b{color:#C8432F;font-weight:800}
.msh .step .nw{font-size:16.5px;color:var(--ink)}
.msh .step .pr{display:flex;align-items:baseline;gap:10px;font-size:14.5px}
.msh .step .pr b{font-size:28px;font-weight:900;color:var(--green);line-height:1;white-space:nowrap}
.msh .step .m{display:none}
.msh .hold{position:sticky;top:90px;height:min(70vh,560px);display:flex;align-items:center}
.msh .screens{position:relative;width:100%;aspect-ratio:3/2;border-radius:20px;overflow:hidden;background:var(--tint);border:1px solid var(--line);box-shadow:0 30px 70px rgba(27,60,116,.16)}
.msh .screens img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:top left;opacity:0;transition:opacity .45s}
.msh .screens img.on{opacity:1}
.msh .count{position:absolute;right:14px;bottom:14px;background:rgba(13,27,62,.85);color:#fff;font-size:13px;font-weight:800;border-radius:999px;padding:4px 12px}
.msh .extra{margin-top:22px;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:22px}
.msh .ex{display:flex;gap:14px;align-items:center;background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px 20px}
.msh .ex b{font-size:24px;font-weight:900;color:var(--green);white-space:nowrap}
.msh .ex p{font-size:15px;color:var(--ink)}
.msh .ex p small{display:block;color:var(--muted);font-size:13px}
.msh .hq{display:flex;gap:14px;align-items:center;max-width:760px;margin-top:30px}
.msh .hq img{width:56px;height:56px;border-radius:50%;object-fit:cover;flex:none}
.msh .hq p{font-size:16px;color:var(--ink);font-weight:600}
.msh .hq small{display:block;font-weight:500;color:var(--muted);font-size:13.5px;margin-top:4px}
.msh .story{background:var(--navy);color:var(--soft);overflow:hidden}
.msh .story .in{display:flex;flex-direction:column;gap:34px}
.msh .rs-head{text-align:center;display:flex;flex-direction:column;align-items:center;gap:10px}
.msh .rs-head .label{color:var(--sky)}
.msh .rs-head h2{color:#fff;max-width:24ch}
.msh .rs-stage{display:grid;grid-template-columns:minmax(0,1fr) 300px minmax(0,1fr);gap:40px;align-items:center}
.msh .phone{background:#05080F;border-radius:44px;padding:12px;box-shadow:0 40px 90px rgba(0,0,0,.55),inset 0 0 0 2px #2A3550}
.msh .vv{position:relative;overflow:hidden;background:#000;aspect-ratio:9/16;border-radius:34px}
.msh .vv img, .msh .vv video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover}
.msh .notch{position:absolute;top:10px;left:50%;transform:translateX(-50%);width:84px;height:22px;border-radius:12px;background:#05080F;z-index:2}
.msh .play{position:absolute;left:50%;top:46%;transform:translate(-50%,-50%);width:68px;height:68px;border-radius:50%;border:0;background:#fff;color:var(--blue);cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(0,0,0,.35)}
.msh .play svg{width:26px;height:26px;margin-left:4px}
.msh .side{display:flex;flex-direction:column;gap:16px}
.msh .bubble{background:rgba(255,255,255,.07);border:1px solid rgba(255,255,255,.12);border-radius:18px;padding:16px 18px}
.msh .bubble small{display:block;color:var(--sky);font-size:13px;font-weight:700;margin-bottom:4px}
.msh .bubble p{color:#fff;font-size:18px;font-weight:800;line-height:1.35}
.msh .sticker{display:flex;align-items:center;gap:14px;background:#fff;color:var(--ink);border-radius:18px;padding:14px 18px;box-shadow:0 18px 40px rgba(0,0,0,.3)}
.msh .sticker b{font-size:30px;font-weight:900;color:var(--green);line-height:1;white-space:nowrap}
.msh .sticker span{font-size:14px;font-weight:700}
.msh .sticker.gold b{color:var(--ink)}
.msh .sticker.gold i{font-style:normal;color:#F6B73C;letter-spacing:1px}
.msh .side.l .bubble:nth-child(2){margin-left:28px}
.msh .side.r .sticker:nth-child(2){margin-right:28px}
.msh .rs-foot{text-align:center;font-size:14.5px}
.msh .rs-foot a{color:#fff;font-weight:800;display:inline-block;padding:8px 0}
@media (max-width:980px){
.msh .rs-stage{grid-template-columns:1fr;gap:22px;justify-items:center}
.msh .rs-stage .phone{order:-1;width:270px}
.msh .side{width:100%;max-width:440px}
.msh .side.l .bubble:nth-child(2), .msh .side.r .sticker:nth-child(2){margin:0}
}
.msh .rd-head{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:end;gap:14px 24px;margin-bottom:34px}
.msh .rd-head .head{margin-bottom:0}
.msh .rd-arrows{display:flex;gap:8px}
.msh .rd-arrows button{width:46px;height:46px;border-radius:50%;border:1.5px solid var(--line);background:#fff;color:var(--ink);font-size:20px;cursor:pointer;font-family:var(--font)}
.msh .rd-arrows button:hover{border-color:var(--blue);color:var(--blue)}
.msh .deck{display:grid;grid-auto-flow:column;grid-auto-columns:minmax(290px,350px);gap:18px;overflow-x:auto;scroll-snap-type:x mandatory;padding:4px 2px 14px;scrollbar-width:thin}
.msh .dc{scroll-snap-align:start;background:#fff;border:1px solid var(--line);border-radius:22px;overflow:hidden;display:flex;flex-direction:column}
.msh .dc .ph{position:relative}
.msh .dc .ph img{width:100%;aspect-ratio:16/10;height:auto;object-fit:cover}
.msh .dc .res{position:absolute;left:14px;bottom:-26px;background:#fff;border-radius:16px;padding:10px 14px;box-shadow:0 12px 28px rgba(13,27,62,.16);max-width:calc(100% - 28px)}
.msh .dc .res b{display:block;font-size:28px;font-weight:900;color:var(--green);line-height:1.05}
.msh .dc .res span{font-size:13px;color:var(--ink);font-weight:700}
.msh .dc-t{padding:40px 20px 20px;display:flex;flex-direction:column;gap:12px;flex:1}
.msh .dc .meta{font-size:13px;font-weight:800;color:var(--muted)}
.msh .dc ul{margin:0;padding:0;list-style:none;display:flex;flex-direction:column;gap:6px;font-size:14.5px;color:var(--ink)}
.msh .dc li b{color:var(--green)}
.msh .dc .rq2{display:flex;gap:12px;align-items:flex-start;font-size:15px;color:var(--ink);font-weight:600}
.msh .dc .rq2 img{width:44px;height:44px;border-radius:50%;object-fit:cover;flex:none}
.msh .dc .rq2 small{display:block;color:var(--muted);font-size:12.5px;font-weight:500;margin-top:3px}
.msh .dc .rq2.plain{font-weight:500;color:var(--muted)}
.msh .dc a{margin-top:auto;font-weight:800;text-decoration:none;font-size:14.5px;display:inline-block;padding:8px 0 2px}
.msh .gl-grid{display:grid;grid-template-columns:minmax(0,.8fr) minmax(0,1.2fr);gap:48px;align-items:start}
.msh .zero{display:flex;flex-direction:column;gap:14px;position:sticky;top:96px}
.msh .z-big{font-size:clamp(96px,12vw,150px);font-weight:900;color:var(--green);line-height:.85;letter-spacing:-.04em}
.msh .z-big-l{font-size:22px;font-weight:900;color:var(--ink);line-height:1.25}
.msh .zero p{font-size:16px}
.msh .zero ol{margin:6px 0 0;padding-left:20px;display:flex;flex-direction:column;gap:6px;font-size:15px;color:var(--ink);font-weight:700}
.msh .gl-sup{display:flex;flex-wrap:wrap;gap:10px 28px;font-size:14.5px;margin-top:6px}
.msh .gl-sup b{color:var(--ink);font-size:18px;font-weight:900;margin-right:4px}
.msh .hlist{display:flex;flex-direction:column;border-top:2px solid var(--ink)}
.msh .hr{display:grid;grid-template-columns:64px minmax(0,1fr) auto;gap:16px;align-items:center;padding:14px 0;border-bottom:1px solid var(--line)}
.msh .hr img{width:64px;height:46px;border-radius:10px;object-fit:cover}
.msh .hr b{display:block;color:var(--ink);font-size:16px}
.msh .hr span{font-size:13.5px;color:var(--muted)}
.msh .hr .t{text-align:right;font-weight:900;color:var(--ink);font-size:15.5px;white-space:nowrap}
.msh .hr .t small{display:block;font-size:12.5px;font-weight:800;color:var(--green)}
.msh .gl-src{font-size:13px;color:var(--muted);margin-top:10px}
@media (max-width:1020px){
.msh .gl-grid{grid-template-columns:1fr;gap:28px}
.msh .zero{position:static}
}
@media (max-width:600px){
.msh .hr{grid-template-columns:48px minmax(0,1fr) auto;gap:10px;padding:12px 0}
.msh .hr img{width:48px;height:38px}
.msh .hr b{font-size:14.5px}
.msh .hr span{font-size:12.5px}
.msh .hr .t{font-size:13.5px;white-space:normal;max-width:120px}
.msh .hr .t small{font-size:12px}
}
.msh .price{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:48px;align-items:start}
.msh .price ol{margin:0;padding:0;list-style:none;counter-reset:p;display:flex;flex-direction:column;gap:16px}
.msh .price ol li{counter-increment:p;position:relative;padding-left:46px;color:var(--ink);font-weight:800;font-size:17px}
.msh .price ol li::before{content:counter(p);position:absolute;left:0;top:0;width:32px;height:32px;border-radius:10px;background:#fff;color:var(--blue);display:flex;align-items:center;justify-content:center;font-weight:900}
.msh .price ol li span{display:block;font-weight:500;color:var(--body);font-size:15px}
.msh .incl{background:#fff;border:1px solid var(--line);border-radius:18px;padding:24px;display:flex;flex-direction:column;gap:12px}
.msh .incl ul{margin:0;padding-left:18px;display:flex;flex-direction:column;gap:6px;color:var(--ink);font-weight:700}
.msh .faq{max-width:860px;border-top:2px solid var(--ink)}
.msh .faq details{border-bottom:1px solid var(--line)}
.msh .faq summary{cursor:pointer;padding:20px 0;font-weight:800;color:var(--ink);font-size:17px;list-style:none;display:flex;justify-content:space-between;gap:14px}
.msh .faq summary::-webkit-details-marker{display:none}
.msh .faq summary::after{content:"+";color:var(--blue);font-size:24px;line-height:1}
.msh .faq details[open] summary::after{content:"–"}
.msh .faq details p{padding:0 0 20px;max-width:70ch}
.msh .final{background:var(--tint);text-align:center;border-top:1px solid var(--line)}
.msh .final .in{display:flex;flex-direction:column;align-items:center;gap:18px;padding-block:84px}
.msh .final h2{max-width:24ch}
.msh .final p{font-size:18px;max-width:60ch}
.msh .final .ctas{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:6px}
.msh .final .alt{font-size:14.5px;color:var(--muted);line-height:2.2}
@media (max-width:1020px){
.msh .hero .in, .msh .story .in, .msh .golive, .msh .price{grid-template-columns:1fr}
.msh .hero .in{gap:22px;padding-block:36px 48px}
.msh .hero-l{display:contents}
.msh .hero .form{order:1;margin-top:6px}
.msh .htrust{order:2;margin-top:8px}
.msh .vert{max-width:360px;margin:0 auto;width:100%}
.msh .story-s{grid-template-columns:1fr;gap:0}
.msh .hold{display:none}
.msh .step{min-height:0;opacity:1;padding-block:24px;border-bottom:1px solid var(--line)}
.msh .step:first-child{padding-top:0}
.msh .step .m{display:block;border-radius:16px;overflow:hidden;border:1px solid var(--line);aspect-ratio:3/2;background:var(--tint)}
.msh .step .m img{width:100%;height:100%;object-fit:cover;object-position:top left}
.msh .sec{padding-block:60px}
}
@media (max-width:760px){
.msh .extra{grid-template-columns:1fr}
.msh .rd-arrows{display:none}
.msh .deck{grid-auto-columns:85%}
.msh .stats{grid-template-columns:1fr 1fr 1fr}
.msh .tbl th:nth-child(4), .msh .tbl td:nth-child(4){display:none}
.msh .marquee ul{gap:18px}
.msh .lg-tile{width:176px;height:84px}
.msh .lg-tile img{width:150px;height:54px}
.msh .marquee-track{animation-duration:45s}
}
@media (max-width:600px){
.msh .lead{font-size:16.5px}
.msh .head{margin-bottom:28px}
.msh .form{padding:22px}
.msh .stats{grid-template-columns:1fr}
.msh .stat b{font-size:24px}
.msh .vert{max-width:290px}
.msh .tbl{font-size:14px}
.msh .tbl th, .msh .tbl td{padding:10px}
}
@media (prefers-reduced-motion:reduce){
.msh .marquee{overflow-x:auto;-webkit-mask-image:none;mask-image:none}
.msh .marquee-track{animation:none}
.msh .marquee ul[aria-hidden]{display:none}
}
/* PPC Form (CF7 11eec7d) in the hero card and in the site popup (same rules as the small-hospitals page) */
:is(.msh, #myPopup) .lead-form.hr-demo-form{display:block;color:var(--body);font-size:16px;line-height:1.6}
:is(.msh, #myPopup) .hr-form-step{display:flex;flex-direction:column;gap:12px}
:is(.msh, #myPopup) .hr-progress{display:flex;flex-direction:column;gap:6px;margin:0 0 2px}
:is(.msh, #myPopup) .hr-progress-label{font-size:12px;font-weight:800;letter-spacing:.08em;color:var(--muted)}
:is(.msh, #myPopup) .hr-progress-bar{height:4px;border-radius:2px;background:var(--line);overflow:hidden}
:is(.msh, #myPopup) .hr-progress-bar span{display:block;height:100%;border-radius:2px;background:var(--blue)}
:is(.msh, #myPopup) .hr-step-title{font-weight:800;color:var(--ink);font-size:16.5px}
:is(.msh, #myPopup) .hr-bed-options{display:flex;flex-direction:column;gap:9px}
:is(.msh, #myPopup) .hr-bed-option{display:flex;justify-content:space-between;align-items:center;gap:10px;width:100%;min-height:0;margin:0;padding:13px 16px;border:1.5px solid var(--line);border-radius:12px;background:#fff;box-shadow:none;color:var(--ink);font-family:var(--font);font-size:15.5px;font-weight:800;line-height:normal;letter-spacing:normal;text-align:left;text-transform:none;cursor:pointer}
:is(.msh, #myPopup) .hr-bed-option:hover{border-color:var(--blue);background:#F2F7FF}
:is(.msh, #myPopup) .hr-bed-option strong{font-weight:inherit}
:is(.msh, #myPopup) .hr-bed-option.hr-bed-full{font-weight:700;color:var(--muted)}
:is(.msh, #myPopup) .hr-arrow{flex:none;color:var(--blue);font-size:22px;line-height:1}
:is(.msh, #myPopup) .hr-selected-bed{display:flex;align-items:center;gap:8px;background:var(--tint);border-radius:10px;padding:9px 12px;color:var(--ink);font-size:14px;font-weight:800}
:is(.msh, #myPopup) .hr-selected-bed-value{flex:1;min-width:0}
:is(.msh, #myPopup) .hr-selected-bed button{margin:0;padding:0;border:0;border-radius:0;background:none;box-shadow:none;color:var(--blue);font-family:var(--font);font-size:14px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(.msh, #myPopup) .hr-selected-bed .hr-back-step{padding:0 2px;font-size:22px;line-height:1}
:is(.msh, #myPopup) .hr-demo-form button:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(.msh, #myPopup) .hr-demo-form .row-2{display:flex;flex-direction:column;gap:12px}
:is(.msh, #myPopup) .hr-demo-form .field{display:flex;flex-direction:column;gap:4px;min-width:0;margin:0}
:is(.msh, #myPopup) .hr-demo-form .field label{position:static;display:block;width:auto;height:auto;margin:0;padding:0;overflow:visible;clip:auto;clip-path:none;white-space:normal;color:var(--ink);font-size:12.5px;font-weight:800;line-height:inherit}
:is(.msh, #myPopup) .lead-form .row-2 .field .wpcf7-form-control{width:100%;min-width:0;height:auto;margin:0;padding:11px 12px;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:none;color:var(--body);font-family:var(--font);font-size:15px;line-height:normal;transition:none}
:is(.msh, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus{border-color:var(--line);outline:0}
:is(.msh, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
/* Mobile number: "+91" box in front of the field (built by the script below) */
:is(.msh, #myPopup) .hr-phone{display:flex;border:1px solid var(--line);border-radius:10px;background:#fff;overflow:hidden}
:is(.msh, #myPopup) .hr-phone:focus-within{outline:3px solid var(--sky);outline-offset:2px}
:is(.msh, #myPopup) .hr-phone:has(.wpcf7-not-valid){border-color:#DC3232}
:is(.msh, #myPopup) .hr-cc{flex:none;display:flex;align-items:center;padding:0 10px 0 12px;border-right:1px solid var(--line);background:var(--tint);color:var(--ink);font-size:15px;font-weight:700}
:is(.msh, #myPopup) .lead-form .row-2 .field .hr-phone .wpcf7-form-control{flex:1;border:0;border-radius:0;outline:0}
:is(.msh, #myPopup) .hr-demo-form .wpcf7-not-valid-tip{margin-top:4px;color:#B42318;font-size:12.5px;font-weight:700;line-height:1.3}
:is(.msh, #myPopup) .hr-demo-form .form-submit-btn{display:flex;flex-direction:column;position:relative;margin:0}
:is(.msh, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit{display:block;width:100%;height:auto;margin:0;padding:15px 26px;border:0;border-radius:12px;background:var(--blue);box-shadow:none;color:#fff;font-family:var(--font);font-size:16px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(.msh, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:hover{background:#0B57C7;box-shadow:none}
:is(.msh, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(.msh, #myPopup) .hr-demo-form .form-note{margin:0;color:var(--muted);font-size:13.5px;line-height:inherit;text-align:center}
/* Site popup (#myPopup, templates/lead-popup.php) on this page: drawn like the hero form card */
body #myPopup{max-width:480px;padding:28px;border-radius:20px;color:var(--body);font-family:var(--font)}
body #myPopup .widget-heading{margin:0 0 14px;padding-right:32px;text-align:left}
body #myPopup .widget-heading .heading-title{margin:0;color:var(--ink);font-size:24px;font-weight:900;line-height:1.15;letter-spacing:-.015em}
body #myPopup .widget-heading .description-text{margin:8px 0 0;color:var(--body);font-size:14.5px;line-height:1.6}
@media (max-width:600px){
  body #myPopup{padding:22px}
}
</style>
<div class="msh">
<div class="msh-main">


<!-- 1. HERO -->
<section class="hero">
  <div class="in">
    <div class="hero-l">
      <h1>Hospital software for multispeciality hospitals</h1>
      <p class="big">Every department of your hospital works on the same patient file</p>
      <p class="lead">For multispeciality hospitals with 50 beds or more. OPD, wards, OT, ICU, lab, pharmacy, billing and the TPA desk see one file, so nobody calls around and every charge reaches the bill. Most hospitals go live in 1 to 3 weeks, one department at a time.</p>
      <div class="htrust">
        <div class="hrow">
          <div class="faces" aria-hidden="true"><img src="<?php echo esc_url( $msh_img['people/bhaumik-rathore'] ); ?>" alt=""><img src="<?php echo esc_url( $msh_img['people/sanjeev-desai'] ); ?>" alt=""><img src="<?php echo esc_url( $msh_img['people/deepika-singhavi'] ); ?>" alt=""><img src="<?php echo esc_url( $msh_img['people/dipak-viradia'] ); ?>" alt=""><img src="<?php echo esc_url( $msh_img['people/monil-parmar'] ); ?>" alt=""></div>
          <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
        </div>
        <div class="hrow">
          <div class="tiles"><span><img src="<?php echo esc_url( $msh_img['badges/nabh'] ); ?>" alt="NABH certified"></span><span><img src="<?php echo esc_url( $msh_img['badges/abdm'] ); ?>" alt="ABDM compliant"></span><span><img src="<?php echo esc_url( $msh_img['badges/nha'] ); ?>" alt="NHA approved"></span><span><img src="<?php echo esc_url( $msh_img['badges/iso-27001'] ); ?>" alt="ISO 27001"></span></div>
          <p><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="hrates"><span><b>4.8</b> <i class="stars">★★★★★</i> Google</span><span><b>4.8</b> <i class="stars">★★★★★</i> Capterra</span><span><b>5.0</b> <i class="stars">★★★★★</i> G2</span></div>
      </div>
    </div>

    <div class="form" id="demo">
      <h2>Book a free demo</h2>
      <p class="sub">A free 30-minute demo, set up for your departments. Bring your administrator and billing head.</p>
      <?php if ( shortcode_exists( 'contact-form-7' ) ) { echo do_shortcode( $msh_form ); } ?>
    </div>
  </div>
</section>

<!-- 2. HOSPITALS LIKE YOURS -->
<section class="logos" aria-labelledby="lg-h">
  <div class="in">
    <h2 id="lg-h">Multispeciality hospitals that run on Healthray</h2>
  </div>
  <div class="marquee">
    <div class="marquee-track">
      <ul id="logoList">
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/universal'] ); ?>" alt="Universal Hospital, Surat"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>200 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/hzb-arogyam'] ); ?>" alt="HZB Arogyam Multispeciality Hospital, Hazaribagh"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>150 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/vibrant'] ); ?>" alt="Vibrant Multispeciality Hospital, Vapi"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>200+ beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/parivar'] ); ?>" alt="Parivar Super Speciality Hospital, Madhya Pradesh"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>140 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/shraddha'] ); ?>" alt="Shraddha Aarogya Mandir, Vapi"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>150 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/jeevan-rekha'] ); ?>" alt="Jeevan Rekha Hospital, Raiganj, West Bengal"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>220 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/bbmh'] ); ?>" alt="Budha Baba Multispeciality Hospital, Cuttack"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>100 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/pims'] ); ?>" alt="PIMS Multi-Superspeciality Hospital, Udaipur"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/jabalpur'] ); ?>" alt="Jabalpur Hospital and Research Centre, Jabalpur"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>300+ beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/jj-plus'] ); ?>" alt="JJ Plus Hospitals"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>135 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/prabh-aasra'] ); ?>" alt="Prabh Aasra Charitable Hospital, Punjab"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $msh_img['logos/heritage-hospital'] ); ?>" alt="Heritage Hospital, Noida"></span><span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6"/><circle cx="7" cy="11.5" r="2"/></svg>100 beds</span></li>
      </ul>
    </div>
  </div>
</section>

<!-- 3. VIDEO -->
<section class="sec tint" id="video">
  <div class="in">
    <div class="head c">
      <span class="label">1-minute video</span>
      <h2>Watch a Patient Go Through Every Department</h2>
      <p>Reception, the doctor, admission, billing, TPA and discharge, all on one patient file. Sample patient, real software.</p>
    </div>
    <div class="vid-wrap">
      <div class="frame">
        <div class="frame-bar"><i></i><i></i><i></i><span>app.healthray.com</span></div>
        <video id="walk" muted playsinline loop preload="none" poster="<?php echo esc_url( $msh_up . '2026/11/dashboard.webp' ); ?>" aria-label="Healthray walkthrough: one patient from registration to discharge. Click to pause or play."><source src="<?php echo esc_url( $msh_up . '2026/11/healthray-walkthrough.mp4' ); ?>" type="video/mp4"></video>
        <button class="vplay" id="vplay" type="button" aria-label="Play the video"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button>
      </div>
      <div class="vid-foot">
        <p class="vid-line">Want to see it with your own departments and rates? Book a free 30-minute demo and we will set it up for your hospital.</p>
        <a class="btn hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
    </div>
  </div>
</section>

<!-- 4. HANDOVERS BETWEEN DEPARTMENTS -->
<section class="sec">
  <div class="in">
    <div class="head">
      <span class="label">Where the day gets stuck</span>
      <h2>Your Departments Stop Waiting on Each Other</h2>
      <p>In a multispeciality hospital, most delays happen between departments: a report that hasn't reached the doctor, a bed nobody marked free, a charge that never reached the bill. Healthray puts every department on the same patient file.</p>
    </div>
    <div class="story-s">
      <div class="steps-s">
        <div class="step on"><div class="m"><img src="<?php echo esc_url( $msh_up . '2026/11/consult-crop.webp' ); ?>" alt="" loading="lazy"></div><span class="who-to">Lab and radiology → Consultant</span><h3>Reports reach the doctor's screen, not a file runner</h3><p class="bf"><b>Today:</b> someone walks to the lab or the record room while the patient waits.</p><p class="nw">Lab and radiology reports land in the patient's file. The consultant sees old visits, reports and scans on one screen, and sends new tests to the lab with one click.</p><p class="pr"><b>60%</b>faster diagnostic reporting at Jabalpur Hospital, 300+ beds</p></div>
        <div class="step"><div class="m"><img src="<?php echo esc_url( $msh_up . '2026/11/beds-wash.webp' ); ?>" alt="" loading="lazy"></div><span class="who-to">Ward → Front desk</span><h3>The front desk knows which bed is free and cleaned</h3><p class="bf"><b>Today:</b> reception phones ward after ward to find a bed that is free and ready.</p><p class="nw">Every bed's status is on one board: free, taken or being cleaned. The patient's OPD file moves to the ward, so nothing is typed again.</p><p class="pr"><b>30%</b>faster bed allocation at Lotus Hospital, 200 beds</p></div>
        <div class="step"><div class="m"><img src="<?php echo esc_url( $msh_up . '2026/11/ward-peach.webp' ); ?>" alt="" loading="lazy"></div><span class="who-to">Day shift → Night shift</span><h3>Nurses hand over on screen, not on paper</h3><p class="bf"><b>Today:</b> notes are passed on paper at shift change, and some never reach the next nurse.</p><p class="nw">The doctor's orders, medicines given, vitals and handover notes are in one file. The next shift and the doctor on rounds read the same thing.</p><p class="pr"><b>30%</b>fewer missed nursing updates at Lotus Hospital</p></div>
        <div class="step"><div class="m"><img src="<?php echo esc_url( $msh_up . '2026/11/bill-purple.webp' ); ?>" alt="" loading="lazy"></div><span class="who-to">Every department → Billing &amp; TPA</span><h3>Every charge reaches the bill as it happens</h3><p class="bf"><b>Today:</b> on discharge day, billing collects charges from the ward, OT, lab and pharmacy, and the family waits.</p><p class="nw">Charges from each department land on the patient's bill as they happen. Cash, TPA, insurance and Ayushman patients are billed in the same software.</p><p class="pr"><b>30%</b>faster billing and discharge at BBMH Hospital, 100 beds</p></div>
      </div>
      <div class="hold"><div class="screens" id="screens">
        <img class="on" src="<?php echo esc_url( $msh_up . '2026/11/consult-crop.webp' ); ?>" alt="Healthray consultation screen: medicines from a template, tests ordered, orders sent to the lab and pharmacy, and previous visits">
        <img src="<?php echo esc_url( $msh_up . '2026/11/beds-wash.webp' ); ?>" alt="Healthray bed screen showing which beds are free, taken or being cleaned" loading="lazy">
        <img src="<?php echo esc_url( $msh_up . '2026/11/ward-peach.webp' ); ?>" alt="Healthray inpatient file with doctor notes, nursing notes, medicines given and the shift handover" loading="lazy">
        <img src="<?php echo esc_url( $msh_up . '2026/11/bill-purple.webp' ); ?>" alt="Healthray inpatient bill with room rent, doctor visits, lab tests and medicines" loading="lazy">
        <span class="count" id="count">1 of 4</span>
      </div></div>
    </div>

    <div class="extra">
      <div class="ex"><b>25%</b><p>fewer OT clashes, with every surgeon booking on one OT calendar<small>Lotus Hospital, Valsad · 4 operation theatres</small></p></div>
      <div class="ex"><b>20%</b><p>less medicine wasted, because the pharmacy sees expiry dates early<small>Lotus Hospital, Valsad</small></p></div>
    </div>

    <div class="hq">
      <img src="<?php echo esc_url( $msh_img['people/vimal-dhaduk'] ); ?>" alt="Dr. Vimal Dhaduk" loading="lazy">
      <p>"A surgical patient passes through many teams before going home, so coordination between departments is very important for us."<small>Dr. Vimal Dhaduk, Gastrointestinal Surgeon, VR Group of Hospitals</small></p>
    </div>
  </div>
</section>

<!-- 5. CUSTOMER STORY -->
<section class="sec story">
  <div class="in">
    <div class="rs-head"><span class="label">Customer story · Universal Hospital, Surat · 200 beds · on Healthray for 2+ years</span><h2>"We Have Found Exceptional Results From Every Department"</h2></div>
    <div class="rs-stage">
      <div class="side l">
        <div class="bubble"><small>Does it save your hospital money?</small><p>"Yes, it does. It saves more than 20–30% of operational costs."</p></div>
        <div class="bubble"><small>Was it easy for your staff?</small><p>"It has been adopted by all departments very easily and very quickly."</p></div>
      </div>
      <div class="phone"><div class="vv" id="vstory" data-src="<?php echo esc_url( $msh_up . '2026/08/Universal-Hospital-Surat-Review_-How-Healthray-Streamlined-Our-Workflow.mp4' ); ?>"><span class="notch"></span><img src="<?php echo esc_url( $msh_up . '2026/08/universal-hospital-healthray.webp' ); ?>" alt="Divyesh Gandhi, Head of Operations at Universal Hospital, speaking about Healthray" width="720" height="1280" loading="lazy"><button class="play" type="button" id="playStory" aria-label="Play the interview with Divyesh Gandhi of Universal Hospital, with sound"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button></div></div>
      <div class="side r">
        <div class="sticker gold"><b>10/10</b><span>his rating<br><i>★★★★★</i></span></div>
        <div class="sticker"><b>45%</b><span>shorter patient waiting</span></div>
        <div class="sticker"><b>₹8 lakh</b><span>saved a year on paper</span></div>
      </div>
    </div>
    <p class="rs-foot">Divyesh Gandhi, Head of Operations · <a href="https://healthray.com/case-studies/universal-hospital/">Read Universal Hospital's story →</a></p>
  </div>
</section>
<!-- 6. MORE RESULTS -->
<section class="sec tint">
  <div class="in">
    <div class="rd-head">
      <div class="head">
        <span class="label">Results</span>
        <h2>Results at Hospitals Your Size</h2>
        <p>Every number below comes from a published Healthray case study.</p>
      </div>
      <div class="rd-arrows"><button type="button" id="rdPrev" aria-label="Previous hospitals">←</button><button type="button" id="rdNext" aria-label="Next hospitals">→</button></div>
    </div>
    <div class="deck" id="deck">
      <article class="dc"><div class="ph"><img src="<?php echo esc_url( $msh_img['photos/t-vibrant'] ); ?>" alt="Vibrant Multispeciality Hospital building, Vapi" width="900" height="506" loading="lazy"><div class="res"><b>₹25 lakh</b><span>saved a year</span></div></div><div class="dc-t"><span class="meta">Vibrant Multispeciality Hospital · Vapi · 200+ beds</span><ul><li><b>30%</b> less time on billing</li><li><b>40%</b> fewer manual mistakes</li></ul><p class="rq2"><img src="<?php echo esc_url( $msh_img['people/bhaumik-rathore'] ); ?>" alt="" loading="lazy"><span>"Now patient records, billing, pharmacy and inventory are all connected in one system."<small>Dr. Bhaumik Rathore</small></span></p><a href="https://healthray.com/case-studies/vibrant-hospital/">Read the story →</a></div></article>
      <article class="dc"><div class="ph"><img src="<?php echo esc_url( $msh_img['photos/t-rainbow'] ); ?>" alt="Rainbow Super Speciality Hospital building, Vapi" width="900" height="506" loading="lazy"><div class="res"><b>6 → 3 hrs</b><span>wait on discharge day</span></div></div><div class="dc-t"><span class="meta">Rainbow Super Speciality Hospital · Vapi · 100 beds</span><ul><li><b>3 → 1 day</b> to get a TPA claim ready</li><li><b>90%</b> OT use</li></ul><p class="rq2"><img src="<?php echo esc_url( $msh_img['people/hinal-parmar'] ); ?>" alt="" loading="lazy"><span>"Now the past visits, scans and notes are already on the screen when she walks in."<small>Dr. Hinal Parmar, Neuro Physician</small></span></p><a href="https://healthray.com/case-studies/rainbow-hospital-vapi/">Read the story →</a></div></article>
      <article class="dc"><div class="ph"><img src="<?php echo esc_url( $msh_img['photos/t-shraddha'] ); ?>" alt="Shraddha Aarogya Mandir building, Vapi" width="900" height="506" loading="lazy"><div class="res"><b>25+ years</b><span>of records moved in</span></div></div><div class="dc-t"><span class="meta">Shraddha Aarogya Mandir · Vapi · 150 beds</span><ul><li><b>12</b> departments on one file</li><li><b>3x</b> faster to open a file</li></ul><p class="rq2"><img src="<?php echo esc_url( $msh_img['people/deepika-singhavi'] ); ?>" alt="" loading="lazy"><span>"Now I type her name and the whole history is right there."<small>Dr. Deepika Singhavi, Obstetrics &amp; Gynaecology</small></span></p><a href="https://healthray.com/case-studies/shraddha-aarogya-mandir-hospital/">Read the story →</a></div></article>
      <article class="dc"><div class="ph"><img src="<?php echo esc_url( $msh_img['photos/t-lotus'] ); ?>" alt="Lotus Hospital building, Valsad" width="900" height="506" loading="lazy"><div class="res"><b>30%</b><span>faster bed allocation</span></div></div><div class="dc-t"><span class="meta">Lotus Hospital · Valsad · 200 beds</span><ul><li><b>25%</b> fewer OT clashes</li><li><b>20%</b> less medicine wasted</li></ul><p class="rq2"><img src="<?php echo esc_url( $msh_img['people/sanjeev-desai'] ); ?>" alt="" loading="lazy"><span>"We have found it much easier to keep things organised across departments."<small>Dr. Sanjeev Desai</small></span></p><a href="https://healthray.com/case-studies/lotus-hospital/">Read the story →</a></div></article>
      <article class="dc"><div class="ph"><img src="<?php echo esc_url( $msh_img['photos/t-jabalpur'] ); ?>" alt="Jabalpur Hospital and Research Centre building" width="900" height="506" loading="lazy"><div class="res"><b>50%</b><span>faster billing, pharmacy and TPA work</span></div></div><div class="dc-t"><span class="meta">Jabalpur Hospital &amp; Research Centre · 300+ beds</span><ul><li><b>60%</b> faster diagnostic reporting</li><li><b>95%</b> accurate patient information</li></ul><p class="rq2"><img src="<?php echo esc_url( $msh_img['people/rajesh-dhirawani'] ); ?>" alt="" loading="lazy"><span>"Every work operation from diagnosis to discharge can now be accessed in just one single click."<small>Dr. Rajesh B. Dhirawani, Surgeon</small></span></p><a href="https://healthray.com/case-studies/jabalpur-hospital/">Read the story →</a></div></article>
      <article class="dc"><div class="ph"><img src="<?php echo esc_url( $msh_img['photos/t-bbmh'] ); ?>" alt="Budha Baba Multispeciality Hospital building, Cuttack" width="900" height="506" loading="lazy"><div class="res"><b>35%</b><span>less admin work</span></div></div><div class="dc-t"><span class="meta">Budha Baba Multispeciality Hospital · Cuttack · 100 beds</span><ul><li><b>30%</b> faster billing and discharge</li><li><b>40%</b> fewer manual mistakes</li></ul><p class="rq2 plain">Old data moved in 1 day, with zero downtime.</p><a href="https://healthray.com/case-studies/bbmh-hospital/">Read the story →</a></div></article>
    </div>
  </div>
</section>
<!-- 8. GO-LIVE -->
<section class="sec" id="golive">
  <div class="in">
    <div class="gl-grid">
      <div class="zero">
        <span class="label">Switching</span>
        <div class="z-big">0</div>
        <p class="z-big-l">days of downtime while 7 hospitals your size switched to Healthray</p>
        <p>One department at a time, so the billing counter stays open.</p>
        <ol><li>We watch how each department works</li><li>Your old data is moved in</li><li>Each team learns its own screens</li><li>One department goes live at a time</li></ol>
        <p class="gl-sup"><span><b>24×7</b>help after go-live</span><span><b>1 hour</b>reply to a critical problem</span></p>
      </div>
      <div>
        <h2 class="sr" style="font-size:22px;margin-bottom:14px">How Long It Took at Hospitals Your Size</h2>
        <div class="hlist">
          <div class="hr"><img src="<?php echo esc_url( $msh_img['photos/t-bbmh'] ); ?>" alt="" loading="lazy"><div><b>BBMH Hospital</b><span>Cuttack · 100 beds</span></div><div class="t">Under 2 weeks<small>Old data in 1 day</small></div></div>
          <div class="hr"><img src="<?php echo esc_url( $msh_img['photos/t-rainbow'] ); ?>" alt="" loading="lazy"><div><b>Rainbow Hospital</b><span>Vapi · 100 beds</span></div><div class="t">14 working days<small>0 downtime</small></div></div>
          <div class="hr"><img src="<?php echo esc_url( $msh_img['photos/t-shraddha'] ); ?>" alt="" loading="lazy"><div><b>Shraddha Aarogya Mandir</b><span>Vapi · 150 beds</span></div><div class="t">18 working days<small>25 years of records moved</small></div></div>
          <div class="hr"><img src="<?php echo esc_url( $msh_img['photos/t-universal'] ); ?>" alt="" loading="lazy"><div><b>Universal Hospital</b><span>Surat · 200 beds</span></div><div class="t">Under 2 weeks<small>0 downtime</small></div></div>
          <div class="hr"><img src="<?php echo esc_url( $msh_img['photos/t-lotus'] ); ?>" alt="" loading="lazy"><div><b>Lotus Hospital</b><span>Valsad · 200 beds</span></div><div class="t">Under 2 weeks<small>0 downtime</small></div></div>
          <div class="hr"><img src="<?php echo esc_url( $msh_img['photos/t-vibrant'] ); ?>" alt="" loading="lazy"><div><b>Vibrant Hospital</b><span>Vapi · 200+ beds</span></div><div class="t">Under 2 weeks<small>Old data in 1 day</small></div></div>
          <div class="hr"><img src="<?php echo esc_url( $msh_img['photos/t-jabalpur'] ); ?>" alt="" loading="lazy"><div><b>Jabalpur Hospital</b><span>Jabalpur · 300+ beds</span></div><div class="t">Under 2 weeks<small>0 downtime</small></div></div>
        </div>
        <p class="gl-src">From Healthray's published case studies.</p>
      </div>
    </div>
  </div>
</section>
<!-- 9. PRICE -->
<section class="sec tint" id="price">
  <div class="in">
    <div class="price">
      <div style="display:flex;flex-direction:column;gap:20px">
        <h2>What It Costs for a Hospital Your Size</h2>
        <p style="font-size:17px">There is no fixed price list, because no two hospitals use the same departments. Three things decide your price, and a 100-bed hospital pays less than a 300-bed hospital.</p>
        <ol>
          <li>Number of beds<span>You pay for the size you are today.</span></li>
          <li>Departments you switch on<span>OPD, IPD, OT, lab, pharmacy and the rest. Add more later in the same software.</span></li>
          <li>Cloud or your own server<span>Hosted by us in India, installed inside your hospital, or both.</span></li>
        </ol>
      </div>
      <div class="incl">
        <span class="label">Included in every price</span>
        <ul><li>Moving your old data</li><li>Training for every department at your hospital</li><li>Your price in writing within 24 hours of the demo</li></ul>
        <p class="small">WhatsApp and SMS messages are paid separately.</p>
        <a class="btn hr-cta-btn" href="#demo">Get a price for my hospital</a>
      </div>
    </div>
  </div>
</section>

<!-- 10. FAQ -->
<section class="sec">
  <div class="in">
    <div class="head"><h2>Questions Hospital Teams Ask Us</h2></div>
    <div class="faq">
      <?php foreach ( $msh_faq as $msh_i => $msh_faq ) : ?>
      <details<?php echo 0 === $msh_i ? ' open' : ''; ?>><summary><?php echo esc_html( $msh_faq['q'] ); ?></summary><p><?php echo esc_html( $msh_faq['a'] ); ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 11. FINAL CTA -->
<section class="final">
  <div class="in">
    <h2>See Your Departments Working on One Patient File</h2>
    <p>A free 30-minute video call, set up with your own departments. Your price comes in writing within 24 hours, and you do not have to buy.</p>
    <div class="ctas"><a class="btn hr-cta-btn" href="#demo">Book a free demo</a><a class="btn line" href="#" id="shareAll">Send this page to your partners</a></div>
    <p class="alt">Under 50 beds? <a href="<?php echo esc_url( home_url( '/solutions/small-hospitals-nursing-homes/' ) ); ?>">See software for small hospitals</a> · More than one hospital? <a href="<?php echo esc_url( home_url( '/solutions/hospital-chains/' ) ); ?>">See software for hospital chains</a></p>
  </div>
</section>


</div>
</div>

<script id="msh-js">
/* Healthray solutions page: multispeciality hospitals. Loaded only on this page template. */
(function(){
  var ROOT=document.querySelector('.msh'); if(!ROOT) return;
  var BASE=ROOT.getAttribute('data-base')||'';
  window.dataLayer=window.dataLayer||[];
  function push(ev,extra){var o={event:ev,form_name:'multispeciality_demo'};for(var k in (extra||{}))o[k]=extra[k];window.dataLayer.push(o);}
  /* logo strip: a second copy makes the slide loop without a gap */
  var ll=document.getElementById('logoList'),cp=ll.cloneNode(true);cp.removeAttribute('id');cp.setAttribute('aria-hidden','true');cp.querySelectorAll('img').forEach(function(i){i.alt='';});ll.parentNode.appendChild(cp);

  /* scroll story: the screen on the right follows the step in the middle of the screen */
  var sImgs=document.querySelectorAll('#screens img'),sCnt=document.getElementById('count'),sSteps=document.querySelectorAll('.step'),sCur=-1,sTick=false;
  function sPick(){
    sTick=false;
    var mid=innerHeight/2,best=0,bd=1e9;
    sSteps.forEach(function(s,k){var r=s.getBoundingClientRect();var d=Math.abs(r.top+r.height/2-mid);if(d<bd){bd=d;best=k;}});
    if(best===sCur)return; sCur=best;
    sSteps.forEach(function(s,k){s.classList.toggle('on',k===best);});
    sImgs.forEach(function(m,k){m.classList.toggle('on',k===best);});
    sCnt.textContent=(best+1)+' of 4';
  }
  addEventListener('scroll',function(){if(!sTick){sTick=true;requestAnimationFrame(sPick);}},{passive:true});
  addEventListener('resize',sPick); sPick();

  /* results deck: arrows move one card */
  var dk=document.getElementById('deck');function dStep(d){var c=dk.querySelector('.dc');dk.scrollBy({left:d*(c.getBoundingClientRect().width+18),behavior:'smooth'});}
  document.getElementById('rdPrev').addEventListener('click',function(){dStep(-1);});document.getElementById('rdNext').addEventListener('click',function(){dStep(1);});

  /* product video: no controls, plays when on screen */
  var v=document.getElementById('walk'),fr=v.closest('.frame'),pb=document.getElementById('vplay');
  var calm=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches, userPaused=false;
  function go(){v.preload='auto';v.play().catch(function(){fr.classList.add('paused');});}
  function toggle(){if(v.paused){userPaused=false;go();}else{userPaused=true;v.pause();fr.classList.add('paused');}}
  v.addEventListener('click',toggle); pb.addEventListener('click',toggle);
  v.addEventListener('playing',function(){fr.classList.remove('paused');});
  if(calm) fr.classList.add('paused');
  if('IntersectionObserver' in window){
    new IntersectionObserver(function(es){es.forEach(function(e){
      if(e.isIntersecting){ if(!calm&&!userPaused) go(); } else if(!v.paused){v.pause();}
    });},{threshold:.45}).observe(v);
  }

  /* customer interview: plays in place with sound and controls */
  document.getElementById('playStory').addEventListener('click',function(){push('video_play',{video:'universal_interview'});
    var c=document.getElementById('vstory'), r=document.createElement('video');
    r.src=c.getAttribute('data-src')||(BASE+'video/universal-review-web.mp4');r.controls=true;r.autoplay=true;r.playsInline=true;r.setAttribute('aria-label','Video interview with Divyesh Gandhi, Universal Hospital');
    c.querySelectorAll('img,.play').forEach(function(n){n.remove();});c.appendChild(r);r.play().catch(function(){});
  });

  /* WhatsApp share for each role card, and for the whole page */
  var base=location.href.split('#')[0];
  function wa(text,hash){return 'https://wa.me/?text='+encodeURIComponent(text+'\n'+base+(hash?'#'+hash:''));}
  var sa=document.getElementById('shareAll'); sa.addEventListener('click',function(){push('whatsapp_share',{place:'final_section'});}); sa.href=wa('Have a look: Healthray for multispeciality hospitals. Every department on one patient file.');sa.target='_blank';sa.rel='noopener';

  /* PPC Form steps and mobile box (as on the small-hospitals page). The popup is printed in the footer, after
     this script, so this runs once the page has loaded. */
  function hrForm(){
  /* PPC Form, hero card and popup. Its own script (js/ppc-hims.js) loads only on the PPC templates, so its steps
     run here: a bed button fills the hidden bed_size field and opens step 2, "change" goes back to step 1. */
  document.querySelectorAll('.msh .hr-demo-form, #myPopup .hr-demo-form').forEach(function(box){
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
  document.querySelectorAll('.msh .hr-demo-form input[name="your-number"], #myPopup .hr-demo-form input[name="your-number"]').forEach(function(input){
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

  /* FAQ: first one open by default, only one open at a time */
  var fq=document.querySelectorAll('.msh .faq details');
  fq.forEach(function(d){d.addEventListener('toggle',function(){if(d.open)fq.forEach(function(o){if(o!==d)o.open=false;});});});
})();
</script>
