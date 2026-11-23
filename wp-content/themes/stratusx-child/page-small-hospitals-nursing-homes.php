<?php
/**
 * Template Name: Small Hospitals & Nursing Homes
 * Template Post Type: page
 *
 * Solutions page for 10-50 bed hospitals and nursing homes (/solutions/small-hospitals-nursing-homes/).
 * WordPress also picks this file by slug (page-{slug}.php), so the page can stay on "Default template".
 *
 * Built from the design file small-hospitals-nursing-homes/index.html: same content, markup, CSS and JS. Images are
 * the files already in the media library (wp-content/uploads), mapped in $hr_shn_map below. An empty entry in that map
 * means the file is not uploaded yet: the block that uses it is left out (or shown without the picture) instead of
 * printing a broken image. Paste the uploads path (e.g. '2026/11/beds.webp') into the map and it appears. The Roots wrapper prints <head>, the site header and the site footer
 * around this file, so the design file's placeholder top bar and footer are not used and there are no wp_head or
 * enqueue hooks here. CSS is scoped to #hr-shn, and to #myPopup for the popup.
 *
 * Lead form: the PPC Form (CF7 11eec7d), in the hero card and in the site popup (#myPopup). Every "Book a demo"
 * button has .hr-cta-btn, so js/script.js opens that popup; the popup gets this form and heading through the
 * hr_ppc_popup_args filter (templates/footer.php -> templates/lead-popup.php).
 * Yoast handles the title, meta description, canonical and schema.
 */
defined('ABSPATH') || exit;

$hr_shn_form = '[contact-form-7 id="11eec7d" title="PPC Form"]';
$hr_shn_base = trailingslashit(wp_get_upload_dir()['baseurl']);
$hr_shn_map  = array(
	// Already in the media library
	'img/people/milan-modi.webp'                      => '2024/04/Dr.-Milan-Modi-150x150.webp',
	'img/people/purav-kansara.webp'                   => '2025/10/Dr.-Purav-Kansara-150x150.webp',
	'img/people/kaivan-shah.webp'                     => '2025/10/Dr.-kaivan-shah-150x150.webp',
	'img/people/gautam-beladiya.webp'                 => '2025/10/Dr.-Gautam-Beladiya-150x150.webp',
	'img/badges/nabh.webp'                            => '2026/07/NABH-ehr-certified-150x150.webp',
	'img/badges/abdm.webp'                            => '2025/07/ABDM.webp',
	'img/badges/nha.webp'                             => '2025/07/NHA.webp',
	'img/badges/iso-27001.webp'                       => '2026/03/iso-27001-healthray.webp',
	'img/Rainbow-Hospital.webp'                       => '2026/10/Rainbow-Hospital.webp',
	'img/shri-manakula-vinayak-medical-college.webp'  => '2024/11/Sri-Manakula-Vinayagar-Hospital.webp',
	'img/payal-thumb.webp'                            => '2024/05/Dr-Payal-Choksi.webp',
	// Not in the media library yet: upload, then paste the path here (blank = block is skipped or shown without it)
	'img/people/hinal-parmar.webp'                    => '2026/10/Dr.-Hinal-Parmar-Rainbow-Hospital.webp',
	'img/dashboard.webp'                              => '2026/11/dashboard.webp',
	'img/beds.webp'                                   => '2026/11/ward-peach.webp',
	'img/bill-purple.webp'                            => '2026/11/bill-purple.webp',
	'img/discharge-green.webp'                        => '2026/11/discharge-green.webp',
	'img/owner-yellow.webp'                           => '2026/11/owner-yellow.webp',
	'video/healthray-walkthrough.mp4'                 => '2026/11/healthray-walkthrough.mp4',
	'video/payal-choksi.mp4'                          => '2026/08/Healthray-EMR-feedback-by-Dr-Payal-Choksi.mp4',
);
$hr_shn_url  = static function ($path) use ($hr_shn_base, $hr_shn_map) {
	return empty($hr_shn_map[$path]) ? '' : esc_url($hr_shn_base . $hr_shn_map[$path]);
};
$hr_shn_walk   = $hr_shn_url('video/healthray-walkthrough.mp4');
$hr_shn_review = $hr_shn_url('video/payal-choksi.mp4');

// Site popup: same form, heading and line as the hero card.
add_filter('hr_ppc_popup_args', static function () use ($hr_shn_form) {
	return array(
		'form'  => $hr_shn_form,
		'title' => 'Book a free demo',
		'text'  => "A free 30-minute demo, set up for your hospital's size.",
	);
});
?>
<style id="hr-shn-css">
/* Small hospitals & nursing homes page (design file: small-hospitals-nursing-homes/index.html).
   Every rule is scoped to #hr-shn, or #myPopup for the popup, so nothing reaches the site header and footer. */
#hr-shn, #myPopup{--navy:#0D1B3E; --navy2:#1B3C74; --blue:#0F68EA; --ink:#14284F; --body:#4A5D73; --muted:#6C8093;
  --tint:#EFF4FF; --line:#D9E2F2; --white:#FFFFFF; --sky:#8FB2F5; --soft:#C8D5E3;
  --peach:#FDF1E7; --peach-ink:#B4581A; --mint:#E7F6F1; --mint-ink:#157A4A; --lilac:#EFEBFC; --lilac-ink:#5B3FC4; --sun:#FFF6DF; --sun-ink:#94650A;
  --font:"Mulish",system-ui,-apple-system,"Segoe UI",sans-serif;
  color-scheme:light;}
#hr-shn, #hr-shn *{box-sizing:border-box}
#hr-shn [hidden]{display:none!important}
/* Base rules from the design file. The additions undo site-wide rules inside #hr-shn: style.css capitalises
   headings, sets p/li line-height, removes link underlines and adds link transitions; the Elementor kit sets
   link size/line-height and button padding; Bootstrap sets .lead to weight 300 and .btn shadows/transitions. */
html:has(#hr-shn){scroll-behavior:smooth}
#hr-shn{display:block;background:var(--white);color:var(--body);font-family:var(--font);font-size:16px;line-height:1.6}
#hr-shn h1, #hr-shn h2, #hr-shn h3{color:var(--ink);margin:0;line-height:1.15;text-wrap:balance;letter-spacing:-.015em;text-transform:none}
#hr-shn h2{font-size:clamp(27px,3.4vw,40px);font-weight:900}
#hr-shn h3{font-size:19px;font-weight:800}
#hr-shn p{margin:0;line-height:inherit}
#hr-shn li{line-height:inherit}
#hr-shn img, #hr-shn video{max-width:100%;display:block}
#hr-shn button{padding:0;font-family:var(--font);letter-spacing:normal}
#hr-shn a{color:var(--blue);text-decoration:underline;font-size:inherit;line-height:inherit;transition:none}
#hr-shn a:focus-visible, #hr-shn button:focus-visible, #hr-shn summary:focus-visible, #hr-shn input:focus-visible, #hr-shn select:focus-visible, #hr-shn video:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
#hr-shn .in{max-width:1160px;margin:0 auto;padding-left:max(16px,env(safe-area-inset-left,0px));padding-right:max(16px,env(safe-area-inset-right,0px))}
#hr-shn .sec{padding-block:88px}
#hr-shn .tint{background:var(--tint)}
#hr-shn .head{max-width:740px;display:flex;flex-direction:column;gap:12px;margin-bottom:44px}
#hr-shn .head.c{margin-inline:auto;text-align:center;align-items:center}
#hr-shn .head p{font-size:18px}
#hr-shn .label{font-size:12px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:var(--blue)}
#hr-shn .btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;background:var(--blue);color:#fff;font-weight:800;text-decoration:none;border:0;border-radius:12px;padding:15px 26px;font-family:var(--font);font-size:16px;cursor:pointer;line-height:1.2;box-shadow:none;transition:none}
#hr-shn .btn:hover{background:#0B57C7}
#hr-shn .small{font-size:13.5px;color:var(--muted)}
#hr-shn .stars{color:#F6B73C;letter-spacing:1px}
/* 1 hero */
#hr-shn .hero{background:linear-gradient(135deg,#0D1B3E 0%,#1B3C74 58%,#0F68EA 135%);color:var(--soft)}
#hr-shn .hero .in{display:grid;grid-template-columns:minmax(0,1.1fr) minmax(0,.9fr);gap:56px;padding-block:60px 72px;align-items:center}
#hr-shn .hero-l{display:flex;flex-direction:column;gap:20px}
#hr-shn .hero h1{font-size:13px;font-weight:800;letter-spacing:.09em;text-transform:uppercase;color:var(--sky)}
#hr-shn .big{color:#fff;font-weight:900;font-size:clamp(32px,3.9vw,48px);line-height:1.07;letter-spacing:-.02em;text-wrap:balance}
#hr-shn .big em{font-style:normal;color:#9EC2FF}
#hr-shn .lead{font-size:18.5px;color:#DCE6F5;max-width:50ch;font-weight:400}
#hr-shn .htrust{display:flex;flex-direction:column;gap:18px;margin-top:6px}
#hr-shn .hrow{display:flex;align-items:center;gap:16px;flex-wrap:wrap}
#hr-shn .hrow p{font-size:15px;color:#DCE6F5;flex:1;min-width:200px}
#hr-shn .hrow p b{color:#fff}
#hr-shn .hrow+.hrow{border-top:1px solid rgba(255,255,255,.14);padding-top:18px}
#hr-shn .faces{display:flex}
#hr-shn .faces img{width:40px;height:40px;border-radius:50%;object-fit:cover;border:2px solid #fff;background:#fff}
#hr-shn .faces img+img{margin-left:-10px}
#hr-shn .tiles{display:flex;gap:10px}
#hr-shn .tiles span{width:52px;height:52px;border-radius:10px;background:#fff;display:flex;align-items:center;justify-content:center;padding:5px}
#hr-shn .tiles img{max-height:100%;max-width:100%;width:auto;object-fit:contain}
#hr-shn .hrates{display:flex;flex-wrap:wrap;gap:6px 20px;font-size:14px;color:#DCE6F5}
#hr-shn .hrates b{color:#fff;font-size:15px}
#hr-shn .hrates i{font-style:normal;color:#F6B73C;font-size:12px;letter-spacing:1px}
/* form */
#hr-shn .form{color:var(--body);background:#fff;border-radius:20px;padding:28px;box-shadow:0 30px 70px rgba(3,10,30,.45);display:flex;flex-direction:column;gap:14px;scroll-margin-top:calc(var(--hr-header-h, 60px) + 30px)}
#hr-shn .form h2{font-size:24px}
#hr-shn .form .sub{font-size:14.5px;margin-top:-6px}
/* 3 video */
#hr-shn .vid-wrap{max-width:980px;margin:0 auto}
#hr-shn .frame{border-radius:18px;overflow:hidden;background:#0D1B3E;box-shadow:0 30px 70px rgba(27,60,116,.25);border:1px solid var(--line)}
#hr-shn .frame-bar{display:flex;align-items:center;gap:6px;padding:10px 14px;background:#fff;border-bottom:1px solid var(--line)}
#hr-shn .frame-bar i{width:10px;height:10px;border-radius:50%;background:#DCE3EE}
#hr-shn .frame-bar span{margin-left:10px;font-size:12px;color:var(--muted);background:var(--tint);border-radius:6px;padding:2px 12px}
#hr-shn .frame{position:relative}
#hr-shn .frame video{width:100%;height:auto;aspect-ratio:16/9;cursor:pointer;background:#0D1B3E}
#hr-shn .vplay{display:none;position:absolute;left:50%;top:calc(50% + 18px);transform:translate(-50%,-50%);width:72px;height:72px;border-radius:50%;border:0;background:#fff;color:var(--blue);cursor:pointer;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(13,27,62,.35)}
#hr-shn .vplay svg{width:28px;height:28px;margin-left:4px}
#hr-shn .frame.paused .vplay{display:flex}
#hr-shn .vid-foot{display:flex;flex-wrap:wrap;justify-content:space-between;align-items:center;gap:14px;margin-top:22px}
#hr-shn .vid-line{font-size:17px;color:var(--ink);font-weight:600;max-width:60ch;flex:1;min-width:240px}
/* 4 kinds */
#hr-shn .kinds{display:grid;grid-template-columns:repeat(4,minmax(0,1fr));gap:18px}
#hr-shn .kind{display:flex;flex-direction:column;border:1px solid var(--line);border-radius:20px;overflow:hidden;background:#fff;text-decoration:none;color:inherit;transition:transform .15s,box-shadow .15s}
#hr-shn .kind:hover{transform:translateY(-3px);box-shadow:0 18px 40px rgba(27,60,116,.12)}
#hr-shn .kind-pic{padding:20px 18px;min-height:196px;display:flex;align-items:center}
#hr-shn .kind.k1 .kind-pic{background:var(--peach)}
#hr-shn .kind.k2 .kind-pic{background:var(--mint)}
#hr-shn .kind.k3 .kind-pic{background:var(--lilac)}
#hr-shn .kind.k4 .kind-pic{background:var(--sun)}
#hr-shn .mini{width:100%;background:#fff;border-radius:12px;padding:12px 14px;box-shadow:0 8px 20px rgba(20,40,79,.08);font-size:13px;color:var(--ink)}
#hr-shn .mini-h{font-size:12px;font-weight:800;letter-spacing:.08em;text-transform:uppercase;color:var(--muted);margin-bottom:8px}
#hr-shn .mini-row{display:flex;justify-content:space-between;align-items:center;gap:8px;padding:5px 0;border-top:1px solid #EEF2F8;font-weight:700}
#hr-shn .mini-row:first-of-type{border-top:0}
#hr-shn .mini-row small{font-size:12px;color:var(--muted);font-weight:600}
#hr-shn .chip{font-size:12px;font-weight:800;border-radius:999px;padding:2px 8px;white-space:nowrap}
#hr-shn .k1 .chip{background:var(--peach);color:var(--peach-ink)}
#hr-shn .k2 .chip{background:var(--mint);color:var(--mint-ink)}
#hr-shn .k3 .chip{background:var(--lilac);color:var(--lilac-ink)}
#hr-shn .k4 .chip{background:var(--sun);color:var(--sun-ink)}
#hr-shn .kind-txt{padding:20px 20px 22px;display:flex;flex-direction:column;gap:8px;flex:1}
#hr-shn .kind-txt p{font-size:15px}
#hr-shn .kind-txt .go{margin-top:auto;padding-top:6px;font-weight:800;color:var(--blue);font-size:14.5px}
/* 5 jobs */
#hr-shn .job{display:grid;grid-template-columns:minmax(0,.85fr) minmax(0,1.15fr);gap:60px;align-items:center;padding-block:48px}
#hr-shn .job+.job{border-top:1px solid var(--line)}
#hr-shn .job:nth-of-type(even) .pic{order:-1}
#hr-shn .job .txt{display:flex;flex-direction:column;gap:16px}
#hr-shn .job.nopic{display:block;max-width:760px}
#hr-shn .num{font-size:13px;font-weight:900;color:var(--blue);letter-spacing:.06em}
#hr-shn .job h3{font-size:clamp(23px,2.6vw,31px);font-weight:900;line-height:1.15}
#hr-shn .before{display:flex;gap:10px;align-items:flex-start;font-size:15px;color:var(--body);background:#fff;border:1px solid var(--line);border-radius:12px;padding:11px 14px}
#hr-shn .before b{flex:none;font-size:12px;font-weight:900;letter-spacing:.08em;color:#B4581A;background:var(--peach);border-radius:6px;padding:2px 7px;margin-top:2px}
#hr-shn .ticks{list-style:none;margin:0;padding:0;display:flex;flex-direction:column;gap:10px}
#hr-shn .ticks li{position:relative;padding-left:30px;color:var(--ink);font-weight:700}
#hr-shn .ticks li::before{content:"";position:absolute;left:0;top:3px;width:20px;height:20px;border-radius:50%;background:#E3F4EC}
#hr-shn .ticks li::after{content:"";position:absolute;left:6px;top:8px;width:8px;height:4px;border-left:2px solid #157A4A;border-bottom:2px solid #157A4A;transform:rotate(-45deg)}
#hr-shn .pic{margin:0;border-radius:22px;overflow:hidden}
#hr-shn .pic img{width:100%;height:auto}
/* 6 case study */
#hr-shn .case{background:var(--navy);color:var(--soft)}
#hr-shn .case .in{display:grid;grid-template-columns:minmax(0,.95fr) minmax(0,1.05fr);gap:56px;align-items:stretch}
#hr-shn .case-photo{margin:0;position:relative;border-radius:20px;overflow:hidden;min-height:420px}
#hr-shn .case-photo img{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:12% 60%}
#hr-shn .case-photo figcaption{position:absolute;left:14px;bottom:14px;right:14px;display:flex;flex-wrap:wrap;gap:8px}
#hr-shn .case-photo figcaption span{background:rgba(13,27,62,.85);color:#fff;font-weight:800;font-size:13.5px;border-radius:999px;padding:6px 12px}
#hr-shn .case h2{color:#fff}
#hr-shn .case-r{display:flex;flex-direction:column;gap:20px}
#hr-shn .case-r>p{font-size:17px}
#hr-shn .stats{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px}
#hr-shn .stat{background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:16px;padding:18px}
#hr-shn .stat b{display:block;color:#fff;font-size:30px;font-weight:900;line-height:1.1;letter-spacing:-.01em}
#hr-shn .stat b s{color:#7F93B5;font-size:20px;font-weight:700;text-decoration-thickness:2px}
#hr-shn .stat span{font-size:14.5px}
#hr-shn .case-q{display:flex;gap:14px;align-items:center;border-top:1px solid rgba(255,255,255,.14);padding-top:18px}
#hr-shn .case-q img{width:56px;height:56px;border-radius:50%;object-fit:cover;flex:none}
#hr-shn .case-q .ini{width:56px;height:56px;border-radius:50%;flex:none;display:flex;align-items:center;justify-content:center;background:rgba(255,255,255,.12);color:#fff;font-weight:800;font-size:17px}
#hr-shn .case-q p{color:#E3EBF7;font-size:15px}
#hr-shn .case-q small{display:block;color:var(--sky);font-size:13px;margin-top:4px}
#hr-shn .case a.more{color:#fff;font-weight:800;display:inline-block;padding:10px 0}
/* 7 worries */
#hr-shn .worry{display:grid;grid-template-columns:minmax(0,1.25fr) minmax(0,.75fr);gap:56px;align-items:start}
#hr-shn .chat{display:flex;flex-direction:column;gap:22px}
#hr-shn .pair{display:flex;flex-direction:column;gap:8px}
#hr-shn .say{align-self:flex-start;max-width:86%;background:#F1F3F7;color:var(--ink);font-weight:800;font-size:17px;border-radius:18px 18px 18px 4px;padding:12px 16px}
#hr-shn .say small{display:block;font-size:12px;font-weight:800;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;margin-bottom:2px}
#hr-shn .ans{align-self:flex-end;max-width:86%;background:var(--blue);color:#fff;font-size:15.5px;border-radius:18px 18px 4px 18px;padding:12px 16px}
#hr-shn .ans small{display:block;font-size:12px;font-weight:800;color:#CFE0FF;letter-spacing:.06em;text-transform:uppercase;margin-bottom:2px}
#hr-shn .voice-card{position:sticky;top:calc(var(--hr-header-h, 60px) + 36px);background:var(--tint);border-radius:22px;padding:28px;display:flex;flex-direction:column;gap:16px}
#hr-shn .voice-card img{width:88px;height:88px;border-radius:50%;object-fit:cover;border:4px solid #fff;box-shadow:0 8px 20px rgba(27,60,116,.15)}
#hr-shn .voice-card blockquote{margin:0;font-size:20px;font-weight:800;color:var(--ink);line-height:1.35}
#hr-shn .voice-card .who b{display:block;color:var(--ink)}
#hr-shn .voice-card .who span{font-size:14px}
#hr-shn .facts{display:grid;grid-template-columns:1fr 1fr;gap:10px;border-top:1px solid var(--line);padding-top:16px}
#hr-shn .facts div{font-size:13px}
#hr-shn .facts b{display:block;color:var(--ink);font-size:20px;font-weight:900}
/* 8 grow */
#hr-shn .track{position:relative;display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}
#hr-shn .track::before{content:"";position:absolute;left:24px;right:24px;top:23px;height:4px;border-radius:2px;background:linear-gradient(90deg,var(--blue),#9EC2FF 60%,var(--line))}
#hr-shn .stop{position:relative;display:flex;flex-direction:column;gap:14px}
#hr-shn .dot{width:50px;height:50px;border-radius:50%;background:#fff;border:4px solid var(--line);display:flex;align-items:center;justify-content:center;font-weight:900;color:var(--muted);position:relative;z-index:1}
#hr-shn .stop.now .dot{background:var(--blue);border-color:#CFE0FF;color:#fff}
#hr-shn .here{position:absolute;left:60px;top:12px;background:var(--ink);color:#fff;font-size:12px;font-weight:800;border-radius:999px;padding:3px 10px}
#hr-shn .stop h3{font-size:20px}
#hr-shn .stop>p{font-size:14.5px;margin-top:-8px}
#hr-shn .mods{display:flex;flex-direction:column;gap:8px}
#hr-shn .mod{display:flex;align-items:center;gap:10px;background:#fff;border:1px solid var(--line);border-radius:12px;padding:10px 12px;font-weight:800;color:var(--ink);font-size:15px}
#hr-shn .stop.now .mod{border-color:#BBD2FA;box-shadow:0 6px 16px rgba(15,104,234,.08)}
#hr-shn .mod svg{flex:none;width:22px;height:22px;color:var(--blue)}
#hr-shn .stop:not(.now) .mod svg{color:var(--muted)}
#hr-shn .scale{margin-top:30px;display:flex;flex-wrap:wrap;align-items:center;gap:14px 20px;background:#fff;border:1px solid var(--line);border-radius:16px;padding:16px 20px}
#hr-shn .scale img{height:52px;width:auto}
#hr-shn .scale p{color:var(--ink);font-weight:700;flex:1;min-width:240px}
/* 9 reviews */
#hr-shn .revs{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:22px;align-items:stretch}
#hr-shn .vcard{position:relative;border-radius:22px;overflow:hidden;background:#000;min-height:420px}
#hr-shn .vcard img, #hr-shn .vcard video{position:absolute;inset:0;width:100%;height:100%;object-fit:cover;object-position:45% 35%}
#hr-shn .vcard.novid .shade{position:absolute}
#hr-shn .vcard .shade{position:absolute;inset:auto 0 0 0;padding:70px 22px 22px;background:linear-gradient(transparent,rgba(5,12,30,.9));color:#fff}
#hr-shn .vcard .shade b{display:block;font-size:19px}
#hr-shn .vcard .shade span{font-size:14px;color:#DCE6F5}
#hr-shn .play{position:absolute;left:50%;top:44%;transform:translate(-50%,-50%);width:76px;height:76px;border-radius:50%;border:0;background:#fff;color:var(--blue);cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:0 12px 30px rgba(0,0,0,.35)}
#hr-shn .play svg{width:30px;height:30px;margin-left:4px}
#hr-shn .qcards{display:flex;flex-direction:column;gap:16px}
#hr-shn .qc{background:#fff;border:1px solid var(--line);border-radius:18px;padding:20px 22px;display:flex;flex-direction:column;gap:14px}
#hr-shn .qc p{color:var(--ink);font-size:16px;font-weight:600}
#hr-shn .qc .who{display:flex;gap:12px;align-items:center;font-size:13.5px}
#hr-shn .qc .who img{width:48px;height:48px;border-radius:50%;object-fit:cover}
#hr-shn .qc .who b{display:block;color:var(--ink);font-size:15px}
/* 10 price */
#hr-shn .price{display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1fr);gap:48px;align-items:start}
#hr-shn .price ol{margin:0;padding:0;list-style:none;counter-reset:p;display:flex;flex-direction:column;gap:16px}
#hr-shn .price ol li{counter-increment:p;position:relative;padding-left:46px;color:var(--ink);font-weight:800;font-size:17px}
#hr-shn .price ol li::before{content:counter(p);position:absolute;left:0;top:0;width:32px;height:32px;border-radius:10px;background:#fff;color:var(--blue);display:flex;align-items:center;justify-content:center;font-weight:900}
#hr-shn .price ol li span{display:block;font-weight:500;color:var(--body);font-size:15px}
#hr-shn .incl{background:#fff;border:1px solid var(--line);border-radius:18px;padding:24px;display:flex;flex-direction:column;gap:12px}
#hr-shn .incl ul{margin:0;padding-left:18px;display:flex;flex-direction:column;gap:6px;color:var(--ink);font-weight:700}
/* 11 faq */
#hr-shn .faq{max-width:860px;border-top:2px solid var(--ink)}
#hr-shn .faq details{border-bottom:1px solid var(--line)}
#hr-shn .faq summary{cursor:pointer;padding:20px 0;font-weight:800;color:var(--ink);font-size:17px;list-style:none;display:flex;justify-content:space-between;gap:14px}
#hr-shn .faq summary::-webkit-details-marker{display:none}
#hr-shn .faq summary::after{content:"+";color:var(--blue);font-size:24px;line-height:1}
#hr-shn .faq details[open] summary::after{content:"–"}
#hr-shn .faq details p{padding:0 0 20px;max-width:70ch}
/* 12 final */
#hr-shn .final{background:var(--tint);text-align:center;border-top:1px solid var(--line)}
#hr-shn .final .in{display:flex;flex-direction:column;align-items:center;gap:18px;padding-block:84px}
#hr-shn .final h2{max-width:24ch}
#hr-shn .final p{font-size:18px;max-width:58ch}
#hr-shn .final .ctas{display:flex;flex-wrap:wrap;gap:12px;justify-content:center;margin-top:6px}
#hr-shn .final .alt{font-size:14.5px;color:var(--muted);line-height:2.2}
/* mobile sticky CTA */
#hr-shn .mbar{display:none}
/* PPC Form (CF7 11eec7d) in the hero card and in the site popup, drawn like the design file's form.
   The form's own styles (css/ppc-hims.css) load only on the PPC templates. These rules replace them here and
   override the site-wide CF7 rules in style.css (hidden labels, 44px fields, smaller popup fields). */
#hr-shn .form .wpcf7, #hr-shn .form .wpcf7 form{width:100%;min-width:0;margin:0}
:is(#hr-shn, #myPopup) .lead-form.hr-demo-form{display:block;color:var(--body);font-size:16px;line-height:1.6}
:is(#hr-shn, #myPopup) .hr-form-step{display:flex;flex-direction:column;gap:12px}
:is(#hr-shn, #myPopup) .hr-progress{display:flex;flex-direction:column;gap:6px;margin:0 0 2px}
:is(#hr-shn, #myPopup) .hr-progress-label{font-size:12px;font-weight:800;letter-spacing:.08em;color:var(--muted)}
:is(#hr-shn, #myPopup) .hr-progress-bar{height:4px;border-radius:2px;background:var(--line);overflow:hidden}
:is(#hr-shn, #myPopup) .hr-progress-bar span{display:block;height:100%;border-radius:2px;background:var(--blue)}
:is(#hr-shn, #myPopup) .hr-step-title{font-weight:800;color:var(--ink);font-size:16.5px}
:is(#hr-shn, #myPopup) .hr-bed-options{display:flex;flex-direction:column;gap:9px}
:is(#hr-shn, #myPopup) .hr-bed-option{display:flex;justify-content:space-between;align-items:center;gap:10px;width:100%;min-height:0;margin:0;padding:13px 16px;border:1.5px solid var(--line);border-radius:12px;background:#fff;box-shadow:none;color:var(--ink);font-family:var(--font);font-size:15.5px;font-weight:800;line-height:normal;letter-spacing:normal;text-align:left;text-transform:none;cursor:pointer}
:is(#hr-shn, #myPopup) .hr-bed-option:hover{border-color:var(--blue);background:#F2F7FF}
:is(#hr-shn, #myPopup) .hr-bed-option strong{font-weight:inherit}
:is(#hr-shn, #myPopup) .hr-bed-option.hr-bed-full{font-weight:700;color:var(--muted)}
:is(#hr-shn, #myPopup) .hr-arrow{flex:none;color:var(--blue);font-size:22px;line-height:1}
:is(#hr-shn, #myPopup) .hr-selected-bed{display:flex;align-items:center;gap:8px;background:var(--tint);border-radius:10px;padding:9px 12px;color:var(--ink);font-size:14px;font-weight:800}
:is(#hr-shn, #myPopup) .hr-selected-bed-value{flex:1;min-width:0}
:is(#hr-shn, #myPopup) .hr-selected-bed button{margin:0;padding:0;border:0;border-radius:0;background:none;box-shadow:none;color:var(--blue);font-family:var(--font);font-size:14px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(#hr-shn, #myPopup) .hr-selected-bed .hr-back-step{padding:0 2px;font-size:22px;line-height:1}
:is(#hr-shn, #myPopup) .hr-demo-form button:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(#hr-shn, #myPopup) .hr-demo-form .row-2{display:flex;flex-direction:column;gap:12px}
:is(#hr-shn, #myPopup) .hr-demo-form .field{display:flex;flex-direction:column;gap:4px;min-width:0;margin:0}
:is(#hr-shn, #myPopup) .hr-demo-form .field label{position:static;display:block;width:auto;height:auto;margin:0;padding:0;overflow:visible;clip:auto;clip-path:none;white-space:normal;color:var(--ink);font-size:12.5px;font-weight:800;line-height:inherit}
:is(#hr-shn, #myPopup) .lead-form .row-2 .field .wpcf7-form-control{width:100%;min-width:0;height:auto;margin:0;padding:11px 12px;border:1px solid var(--line);border-radius:10px;background:#fff;box-shadow:none;color:var(--body);font-family:var(--font);font-size:15px;line-height:normal;transition:none}
:is(#hr-shn, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus{border-color:var(--line);outline:0}
:is(#hr-shn, #myPopup) .lead-form .row-2 .field .wpcf7-form-control:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
/* Mobile number: "+91" box in front of the field (built by the script below) */
:is(#hr-shn, #myPopup) .hr-phone{display:flex;border:1px solid var(--line);border-radius:10px;background:#fff;overflow:hidden}
:is(#hr-shn, #myPopup) .hr-phone:focus-within{outline:3px solid var(--sky);outline-offset:2px}
:is(#hr-shn, #myPopup) .hr-phone:has(.wpcf7-not-valid){border-color:#DC3232}
:is(#hr-shn, #myPopup) .hr-cc{flex:none;display:flex;align-items:center;padding:0 10px 0 12px;border-right:1px solid var(--line);background:var(--tint);color:var(--ink);font-size:15px;font-weight:700}
:is(#hr-shn, #myPopup) .lead-form .row-2 .field .hr-phone .wpcf7-form-control{flex:1;border:0;border-radius:0;outline:0}
:is(#hr-shn, #myPopup) .hr-demo-form .wpcf7-not-valid-tip{margin-top:4px;color:#B42318;font-size:12.5px;font-weight:700;line-height:1.3}
:is(#hr-shn, #myPopup) .hr-demo-form .form-submit-btn{display:flex;flex-direction:column;position:relative;margin:0}
:is(#hr-shn, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit{display:block;width:100%;height:auto;margin:0;padding:15px 26px;border:0;border-radius:12px;background:var(--blue);box-shadow:none;color:#fff;font-family:var(--font);font-size:16px;font-weight:800;line-height:1.2;letter-spacing:normal;text-transform:none;cursor:pointer}
:is(#hr-shn, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:hover{background:#0B57C7;box-shadow:none}
:is(#hr-shn, #myPopup) .hr-demo-form .form-submit-btn .wpcf7-submit:focus-visible{outline:3px solid var(--sky);outline-offset:2px}
:is(#hr-shn, #myPopup) .hr-demo-form .form-note{margin:0;color:var(--muted);font-size:13.5px;line-height:inherit;text-align:center}
/* Site popup (#myPopup, templates/lead-popup.php) on this page: drawn like the hero form card */
body #myPopup{max-width:480px;padding:28px;border-radius:20px;color:var(--body);font-family:var(--font)}
body #myPopup .widget-heading{margin:0 0 14px;padding-right:32px;text-align:left}
body #myPopup .widget-heading .heading-title{margin:0;color:var(--ink);font-size:24px;font-weight:900;line-height:1.15;letter-spacing:-.015em}
body #myPopup .widget-heading .description-text{margin:8px 0 0;color:var(--body);font-size:14.5px;line-height:1.6}
@media (max-width:600px){
  body #myPopup{padding:22px}
}
/* responsive */
@media (max-width:1180px){
  #hr-shn .kinds{grid-template-columns:repeat(2,minmax(0,1fr))}
  #hr-shn .kind-pic{min-height:0}
}
@media (max-width:1020px){
  #hr-shn .hero .in, #hr-shn .case .in, #hr-shn .worry, #hr-shn .revs, #hr-shn .price{grid-template-columns:1fr}
  #hr-shn .kinds{grid-template-columns:repeat(2,minmax(0,1fr))}
  #hr-shn .job{grid-template-columns:1fr;gap:24px;padding-block:36px}
  #hr-shn .job:nth-of-type(even) .pic{order:0}
  #hr-shn .voice-card{position:static}
  #hr-shn .case-photo{min-height:0;aspect-ratio:16/10}
  #hr-shn .hero .in{gap:22px;padding-block:36px 48px}
  #hr-shn .hero-l{display:contents}
  #hr-shn .hero .form{order:1;margin-top:6px}
  #hr-shn .htrust{order:2;margin-top:8px}
  #hr-shn .sec{padding-block:60px}
}
@media (max-width:760px){
  #hr-shn .track{grid-template-columns:1fr;gap:28px}
  #hr-shn .track::before{display:none}
  #hr-shn .mbar{display:block;position:fixed;left:0;right:0;bottom:0;z-index:30;background:#fff;border-top:1px solid var(--line);padding:10px max(16px,env(safe-area-inset-left,0px)) calc(10px + env(safe-area-inset-bottom,0px));transform:translateY(110%);transition:transform .2s}
  #hr-shn .mbar.on{transform:none}
  #hr-shn .mbar .btn{width:100%}
  #hr-shn .stat b{font-size:24px}
  #hr-shn .stat b s{font-size:16px}
  #hr-shn .vcard{min-height:340px}
}
@media (max-width:600px){
  #hr-shn .kinds{grid-template-columns:1fr}
  #hr-shn .stats{grid-template-columns:1fr 1fr}
  #hr-shn .stat b{font-size:24px}
  #hr-shn .lead{font-size:16.5px;font-weight:400}
  #hr-shn .head{margin-bottom:28px}
  #hr-shn .form{padding:22px}
  #hr-shn .vcard{min-height:360px}
  #hr-shn .say, #hr-shn .ans{max-width:94%}
}
@media (max-width:400px){
  #hr-shn .stat{padding:14px}
  #hr-shn .stat b{font-size:21px;white-space:nowrap}
  #hr-shn .stat b s{font-size:14px}
}
@media (prefers-reduced-motion:reduce){
  html:has(#hr-shn){scroll-behavior:auto}
  #hr-shn .kind, #hr-shn .mbar{transition:none}
}
</style>

<main class="hr-shn" id="hr-shn">

<!-- 1. HERO: promise + form -->
<section class="hero">
  <div class="in">
    <div class="hero-l">
      <h1>Hospital software for small hospitals &amp; nursing homes</h1>
      <p class="big">Run your small hospital or nursing home on one simple software</p>
      <p class="lead">For hospitals with 10 to 50 beds. OPD, admissions, billing, cashless claims and pharmacy in one place. We move your old data, train your staff at your hospital and help you 24×7. Most hospitals start in 1 to 3 weeks.</p>
      <div class="htrust">
        <div class="hrow">
          <div class="faces" aria-hidden="true"><img src="<?php echo $hr_shn_url( 'img/people/milan-modi.webp' ); ?>" alt=""><img src="<?php echo $hr_shn_url( 'img/people/purav-kansara.webp' ); ?>" alt=""><img src="<?php echo $hr_shn_url( 'img/people/kaivan-shah.webp' ); ?>" alt=""><img src="<?php echo $hr_shn_url( 'img/people/gautam-beladiya.webp' ); ?>" alt=""><img src="<?php echo $hr_shn_url( 'img/people/hinal-parmar.webp' ); ?>" alt=""></div>
          <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
        </div>
        <div class="hrow">
          <div class="tiles"><span><img src="<?php echo $hr_shn_url( 'img/badges/nabh.webp' ); ?>" alt="NABH certified"></span><span><img src="<?php echo $hr_shn_url( 'img/badges/abdm.webp' ); ?>" alt="ABDM compliant"></span><span><img src="<?php echo $hr_shn_url( 'img/badges/nha.webp' ); ?>" alt="NHA approved"></span><span><img src="<?php echo $hr_shn_url( 'img/badges/iso-27001.webp' ); ?>" alt="ISO 27001"></span></div>
          <p><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="hrates"><span><b>4.8</b> <i class="stars">★★★★★</i> Google</span><span><b>4.8</b> <i class="stars">★★★★★</i> Capterra</span><span><b>5.0</b> <i class="stars">★★★★★</i> G2</span></div>
      </div>
    </div>

    <div class="form" id="demo">
      <h2>Book a free demo</h2>
      <p class="sub">A free 30-minute demo, set up for your hospital's size.</p>
      <?php if ( shortcode_exists( 'contact-form-7' ) ) { echo do_shortcode( $hr_shn_form ); } ?>
    </div>
  </div>
</section>

<!-- 3. VIDEO: plays by itself when seen. Left out until the walkthrough video is in the media library. -->
<?php if ( $hr_shn_walk ) : ?>
<section class="sec tint" id="video">
  <div class="in">
    <div class="head c">
      <span class="label">1-minute video</span>
      <h2>Watch a Patient Go From OPD to Discharge</h2>
      <p>One patient goes through reception, the doctor, admission, the bill and discharge. Sample patient, real software.</p>
    </div>
    <div class="vid-wrap">
      <div class="frame">
        <div class="frame-bar"><i></i><i></i><i></i><span>app.healthray.com</span></div>
        <video id="walk" muted playsinline loop preload="none"<?php $hr_shn_poster = $hr_shn_url( 'img/dashboard.webp' ); if ( $hr_shn_poster ) { echo ' poster="' . $hr_shn_poster . '"'; } ?> aria-label="Healthray walkthrough: registration, doctor, admission, bill and discharge. Click to pause or play."><source src="<?php echo $hr_shn_walk; ?>" type="video/mp4"></video>
        <button class="vplay" id="vplay" type="button" aria-label="Play the video"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button>
      </div>
      <div class="vid-foot">
        <p class="vid-line">Want to see this with your own wards and counters? Book a free 30-minute demo and we will set it up for your hospital's size.</p>
        <a class="btn hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- 4. KINDS OF SMALL HOSPITAL -->
<section class="sec">
  <div class="in">
    <div class="head">
      <span class="label">Your kind of hospital</span>
      <h2>Works the Way Your Hospital Works</h2>
      <p>A maternity home and an eye hospital do not bill the same way. Tell us what you do, and your packages, bills and doctor screens are ready before you start.</p>
    </div>
    <div class="kinds">
      <a class="kind k1" href="https://healthray.com/emr-software-for-gynaecologists/">
        <div class="kind-pic"><div class="mini" aria-hidden="true">
          <div class="mini-h">Package on the bill</div>
          <div class="mini-row"><span>C-section package</span><span class="chip">One price</span></div>
          <div class="mini-row"><small>Room, 3 days</small><small>included</small></div>
          <div class="mini-row"><small>OT and surgeon</small><small>included</small></div>
          <div class="mini-row"><small>Medicines and nursing</small><small>included</small></div>
        </div></div>
        <div class="kind-txt"><h3>Maternity &amp; nursing homes</h3><p>Delivery and C-section packages at one fixed price. Screens made for gynaecology.</p><span class="go">Gynaecology software →</span></div>
      </a>
      <a class="kind k2" href="https://healthray.com/emr-software-for-orthopedics/">
        <div class="kind-pic"><div class="mini" aria-hidden="true">
          <div class="mini-h">Today's OT list</div>
          <div class="mini-row"><span>9:00 · Knee replacement</span><span class="chip">OT 1</span></div>
          <div class="mini-row"><span>11:30 · Fracture fixing</span><span class="chip">OT 1</span></div>
          <div class="mini-row"><span>2:30 · Hernia repair</span><span class="chip">OT 2</span></div>
        </div></div>
        <div class="kind-txt"><h3>Surgical &amp; orthopaedic</h3><p>Book OT time, see the day's list and put surgery packages on the bill.</p><span class="go">Orthopaedic software →</span></div>
      </a>
      <a class="kind k3" href="https://healthray.com/emr-software-for-pediatric/">
        <div class="kind-pic"><div class="mini" aria-hidden="true">
          <div class="mini-h">Bill · Bed 7</div>
          <div class="mini-row"><small>Day 1 · Room and nursing</small><small>✓</small></div>
          <div class="mini-row"><small>Day 2 · Room and nursing</small><small>✓</small></div>
          <div class="mini-row"><span>Day 3 · Room and nursing</span><span class="chip">Added by itself</span></div>
        </div></div>
        <div class="kind-txt"><h3>General &amp; child care</h3><p>Daily room and nursing charges added on their own. Day care and emergency lists.</p><span class="go">Child care software →</span></div>
      </a>
      <a class="kind k4" href="https://healthray.com/emr-software-for-ophthalmologist/">
        <div class="kind-pic"><div class="mini" aria-hidden="true">
          <div class="mini-h">Day care · Today</div>
          <div class="mini-row"><small>Admitted</small><span>8:30 am</span></div>
          <div class="mini-row"><small>Cataract surgery</small><span>10:00 am</span></div>
          <div class="mini-row"><small>Gone home</small><span class="chip">Same day</span></div>
        </div></div>
        <div class="kind-txt"><h3>Eye, ENT &amp; other speciality</h3><p>Same-day admissions and procedure packages. Screens made for your speciality.</p><span class="go">Eye hospital software →</span></div>
      </a>
    </div>
    <p class="small" style="margin-top:14px">Screens above are samples, drawn to show the idea.</p>
  </div>
</section>

<!-- 5. OWNER'S FOUR JOBS -->
<section class="sec tint">
  <div class="in">
    <div class="head">
      <span class="label">What changes for you</span>
      <h2>No More Calling the Ward, the Counter or the Accountant</h2>
      <p>In a small hospital, the owner ends up finding beds, checking bills and asking about money. Healthray does these four jobs for you.</p>
    </div>

    <?php $hr_shn_pic = $hr_shn_url( 'img/beds.webp' ); ?>
    <div class="job<?php echo $hr_shn_pic ? '' : ' nopic'; ?>">
      <div class="txt">
        <span class="num">01 · ADMISSION</span>
        <h3>Find a free bed without phoning the ward</h3>
        <p class="before"><b>BEFORE</b><span>Reception calls the ward, the ward calls back, and the family waits at the counter.</span></p>
        <ul class="ticks"><li>Every bed on one screen: free, taken or being cleaned</li><li>The patient's OPD file moves to the ward. Nothing is typed again</li><li>Wrist band and file stickers print at admission</li></ul>
      </div>
      <?php if ( $hr_shn_pic ) : ?><figure class="pic"><img src="<?php echo $hr_shn_pic; ?>" alt="Healthray bed screen: general ward beds shown as free, occupied or being cleaned, with an admit button and a printed wrist band" width="2400" height="1600" loading="lazy"></figure><?php endif; ?>
    </div>

    <?php $hr_shn_pic = $hr_shn_url( 'img/bill-purple.webp' ); ?>
    <div class="job<?php echo $hr_shn_pic ? '' : ' nopic'; ?>">
      <div class="txt">
        <span class="num">02 · BILLING</span>
        <h3>The bill adds up by itself while the patient stays</h3>
        <p class="before"><b>BEFORE</b><span>On discharge day, someone adds up room rent, medicines and tests from three places.</span></p>
        <ul class="ticks"><li>Room rent added every day on its own</li><li>Lab tests and medicines come on the same bill</li><li>See the bill, deposit and balance any time</li></ul>
      </div>
      <?php if ( $hr_shn_pic ) : ?><figure class="pic"><img src="<?php echo $hr_shn_pic; ?>" alt="Healthray inpatient bill on day 4: room rent, doctor visits, lab tests and medicines, with deposit and amount to pay" width="2400" height="1600" loading="lazy"></figure><?php endif; ?>
    </div>

    <?php $hr_shn_pic = $hr_shn_url( 'img/discharge-green.webp' ); ?>
    <div class="job<?php echo $hr_shn_pic ? '' : ' nopic'; ?>">
      <div class="txt">
        <span class="num">03 · DISCHARGE &amp; CASHLESS</span>
        <h3>Discharge papers and the cashless file, ready on time</h3>
        <p class="before"><b>BEFORE</b><span>The summary is written by hand at the end, and TPA papers sit in a separate file.</span></p>
        <ul class="ticks"><li>Discharge summary made from a ready format and the daily notes</li><li>Insurance noted at admission. The bill prints in the cashless format</li><li>Ayushman patients billed in the same software, every claim on one list</li></ul>
      </div>
      <?php if ( $hr_shn_pic ) : ?><figure class="pic"><img src="<?php echo $hr_shn_pic; ?>" alt="Healthray discharge summary with hospital stay notes, test results, medicines and follow-up date" width="2400" height="1600" loading="lazy"></figure><?php endif; ?>
    </div>

    <?php $hr_shn_pic = $hr_shn_url( 'img/owner-yellow.webp' ); ?>
    <div class="job<?php echo $hr_shn_pic ? '' : ' nopic'; ?>">
      <div class="txt">
        <span class="num">04 · MONEY</span>
        <h3>See who still has to pay, without calling the counter</h3>
        <p class="before"><b>BEFORE</b><span>To know the day's collection, you call the counter between patients.</span></p>
        <ul class="ticks"><li>Every admitted patient, with what each one still has to pay</li><li>The day's collection on your WhatsApp at night</li><li>Every discount and refund in one report</li></ul>
      </div>
      <?php if ( $hr_shn_pic ) : ?><figure class="pic"><img src="<?php echo $hr_shn_pic; ?>" alt="Healthray admitted patients list with each patient's bill till today and the total still to collect" width="2400" height="1600" loading="lazy"></figure><?php endif; ?>
    </div>
  </div>
</section>

<!-- 6. CASE STUDY -->
<section class="sec case">
  <div class="in">
    <figure class="case-photo">
      <img src="<?php echo $hr_shn_url( 'img/Rainbow-Hospital.webp' ); ?>" alt="Rainbow Super Speciality Hospital building, Vapi" width="2508" height="1412" loading="lazy">
      <figcaption><span>Rainbow Hospital, Vapi</span><span>100 beds</span><span>Live in 14 working days</span></figcaption>
    </figure>
    <div class="case-r">
      <span class="label" style="color:var(--sky)">Case study</span>
      <h2>Rainbow Hospital Had the Same Problems. Here Is What Changed.</h2>
      <p>Beds were found by phone, bills were made on discharge day and TPA files were built by hand. After Healthray:</p>
      <div class="stats">
        <div class="stat"><b>20 min</b><span>less to admit a patient</span></div>
        <div class="stat"><b><s>6 hrs</s> 3 hrs</b><span>wait on discharge day</span></div>
        <div class="stat"><b><s>3 days</s> 1 day</b><span>to get a TPA claim ready</span></div>
        <div class="stat"><b>0</b><span>days of work stopped while switching</span></div>
      </div>
      <div class="case-q">
        <?php $hr_shn_hp = $hr_shn_url( 'img/people/hinal-parmar.webp' ); if ( $hr_shn_hp ) : ?><img src="<?php echo $hr_shn_hp; ?>" alt="Dr. Hinal Parmar" loading="lazy"><?php else : ?><span class="ini" aria-hidden="true">HP</span><?php endif; ?>
        <p>"Now the past visits, scans and notes are already on the screen when she walks in."<small>Dr. Hinal Parmar, Neuro Physician, Rainbow Hospital</small></p>
      </div>
      <p class="small" style="color:var(--sky)"><a class="more" href="https://healthray.com/case-studies/rainbow-hospital-vapi/">Read Rainbow Hospital's story →</a></p>
    </div>
  </div>
</section>

<!-- 7. WORRIES ABOUT SWITCHING -->
<section class="sec">
  <div class="in">
    <div class="head">
      <span class="label">Switching</span>
      <h2>Worried About Moving to Software?</h2>
      <p>Most small hospitals have no IT person. Here is what owners ask us before they switch, and what really happens.</p>
    </div>
    <div class="worry">
      <div class="chat">
        <div class="pair"><p class="say"><small>You</small>"My staff is not good with computers."</p><p class="ans"><small>Healthray</small>Each person learns only their own screen, at your hospital. Reception, nurses, billing and pharmacy are trained one by one.</p></div>
        <div class="pair"><p class="say"><small>You</small>"We have years of records in registers and old software."</p><p class="ans"><small>Healthray</small>We move them for you before you start. It is part of every setup.</p></div>
        <div class="pair"><p class="say"><small>You</small>"Will billing stop while we change?"</p><p class="ans"><small>Healthray</small>No. One counter moves at a time. OPD first, then admission and billing, then pharmacy and lab.</p></div>
        <div class="pair"><p class="say"><small>You</small>"Who will help if something stops at night?"</p><p class="ans"><small>Healthray</small>Call, chat or share your screen 24×7, in English, Hindi or Gujarati. A critical problem gets a reply within 1 hour.</p></div>
        <div class="pair"><p class="say"><small>You</small>"What if the internet goes?"</p><p class="ans"><small>Healthray</small>Use a mobile hotspot, or run Healthray on a server inside your hospital.</p></div>
      </div>
      <aside class="voice-card">
        <img src="<?php echo $hr_shn_url( 'img/people/milan-modi.webp' ); ?>" alt="Dr. Milan Modi" width="150" height="150" loading="lazy">
        <blockquote>"If I face any issue, I can call them and someone responds who understands the problem. That kind of support really matters because we don't have an IT person here all the time."</blockquote>
        <div class="who"><b>Dr. Milan Modi</b><span>Chest Physician, Modi Allergy &amp; Chest Clinic</span></div>
        <div class="facts"><div><b>24×7</b>help, 3 languages</div><div><b>1 hour</b>reply to a critical problem</div></div>
      </aside>
    </div>
  </div>
</section>

<!-- 8. START SMALL -->
<section class="sec tint">
  <div class="in">
    <div class="head">
      <span class="label">Start small</span>
      <h2>Start With What You Need. Add More Later.</h2>
      <p>You do not have to switch on everything on day one. Add the rest when you are ready, in the same software, with the same patient files.</p>
    </div>
    <div class="track">
      <div class="stop now">
        <span class="dot">1</span><span class="here">Most start here</span>
        <h3>Day one</h3>
        <div class="mods">
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>Registration and OPD</span>
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 18V7M3 14h18v4M21 14v-2a3 3 0 0 0-3-3h-7v5"/><circle cx="7" cy="11" r="2"/></svg>Beds and admission</span>
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 4h12M6 9h12M13 21 6 13h3a4 4 0 0 0 0-9"/></svg>Billing, TPA and Ayushman</span>
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/></svg>Owner's reports</span>
        </div>
      </div>
      <div class="stop">
        <span class="dot">2</span>
        <h3>When you are ready</h3>
        <div class="mods">
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="8" width="18" height="8" rx="4"/><path d="M12 8v8"/></svg>Pharmacy</span>
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M9 3h6M10 3v6l-5 9a2 2 0 0 0 2 3h10a2 2 0 0 0 2-3l-5-9V3"/></svg>Lab, with machines connected</span>
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 3H6a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V9z"/><path d="M14 3v6h6M8 13h8M8 17h5"/></svg>Doctor's notes and prescriptions</span>
        </div>
      </div>
      <div class="stop">
        <span class="dot">3</span>
        <h3>As you grow</h3>
        <div class="mods">
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 20 14 10M14 10l3-3a2 2 0 1 1 3 3l-3 3M14 10l3 3"/></svg>Operation theatre</span>
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>More beds and wards</span>
          <span class="mod"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-5h6v5"/></svg>A second branch</span>
        </div>
      </div>
    </div>
    <div class="scale">
      <img src="<?php echo $hr_shn_url( 'img/shri-manakula-vinayak-medical-college.webp' ); ?>" alt="Sri Manakula Vinayagar Medical College logo" loading="lazy">
      <p>You will not outgrow it. The same software runs Sri Manakula Vinayagar Medical College, Puducherry, with 1,100 beds.</p>
    </div>
  </div>
</section>

<!-- 9. REVIEWS -->
<section class="sec">
  <div class="in">
    <div class="head">
      <span class="label">Reviews</span>
      <h2>What Doctors Say After Using Healthray</h2>
      <p>Doctors at eye, skin and bone hospitals, in their own words.</p>
    </div>
    <div class="revs">
      <div class="vcard<?php echo $hr_shn_review ? '' : ' novid'; ?>" id="vcard">
        <img src="<?php echo $hr_shn_url( 'img/payal-thumb.webp' ); ?>" alt="Dr. Payal Choksi speaking about Healthray" width="1024" height="823" loading="lazy">
        <?php if ( $hr_shn_review ) : ?><button class="play" type="button" id="playRev" data-video="<?php echo $hr_shn_review; ?>" aria-label="Play video review from Dr. Payal Choksi"><svg viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M8 5v14l11-7z"/></svg></button><?php endif; ?>
        <div class="shade"><b>Dr. Payal Choksi</b><span>Dermatologist and Cosmetologist, Prasann Hospital · Video review</span></div>
      </div>
      <div class="qcards">
        <div class="qc"><p>"The automated appointment reminders have been quite useful for our front desk. It reduces a lot of routine calling because patients are automatically reminded."</p><div class="who"><img src="<?php echo $hr_shn_url( 'img/people/gautam-beladiya.webp' ); ?>" alt="Dr. Gautam Beladiya" loading="lazy"><span><b>Dr. Gautam Beladiya</b>Ophthalmologist, Beladiya Eye &amp; Dental Hospital</span></div></div>
        <div class="qc"><p>"Having their previous consultation history, reports and prescriptions in one place is very helpful. The system has been simple to use in daily practice."</p><div class="who"><img src="<?php echo $hr_shn_url( 'img/people/kaivan-shah.webp' ); ?>" alt="Dr. Kaivan Shah" loading="lazy"><span><b>Dr. Kaivan Shah</b>Ophthalmologist, Bhavna Super Speciality Eye Care</span></div></div>
        <div class="qc"><p>"I can quickly open the previous visit and compare it with the current condition without searching in different files."</p><div class="who"><img src="<?php echo $hr_shn_url( 'img/people/purav-kansara.webp' ); ?>" alt="Dr. Purav Kansara" loading="lazy"><span><b>Dr. Purav Kansara</b>Limb Deformity Correction Center</span></div></div>
      </div>
    </div>
  </div>
</section>

<!-- 10. PRICE -->
<section class="sec tint" id="price">
  <div class="in">
    <div class="price">
      <div style="display:flex;flex-direction:column;gap:20px">
        <h2>What It Costs for a Hospital Your Size</h2>
        <p style="font-size:17px">There is no fixed price list. Three things decide your price, and a 20-bed nursing home pays less than a 50-bed hospital.</p>
        <ol>
          <li>Number of beds<span>You pay for the size you are today.</span></li>
          <li>Departments you switch on<span>Start with OPD, admissions and billing. Add the rest later.</span></li>
          <li>Cloud or your own server<span>Hosted by us in India, or installed inside your hospital.</span></li>
        </ol>
      </div>
      <div class="incl">
        <span class="label">Included in every price</span>
        <ul><li>Moving your old records</li><li>Training at your hospital</li><li>Your price in writing within 24 hours of the demo</li></ul>
        <p class="small">WhatsApp and SMS messages are paid separately.</p>
        <a class="btn hr-cta-btn" href="#demo">Get a price for my hospital</a>
      </div>
    </div>
  </div>
</section>

<!-- 11. FAQ -->
<section class="sec">
  <div class="in">
    <div class="head"><h2>Questions Owners Ask Us</h2></div>
    <div class="faq">
      <details><summary>We have only 15 beds. Is this too big for us?</summary><p>No. Start with OPD, admission and billing. Pharmacy, lab and OT can be switched on later in the same software.</p></details>
      <details><summary>How long does it take to start?</summary><p>Most hospitals go live in 1 to 3 weeks. Your old records are moved and your staff is trained before you start.</p></details>
      <details><summary>Do we need to buy new computers?</summary><p>No. Healthray runs in the web browser on the computers you already have.</p></details>
      <details><summary>Do Ayushman and cashless patients work in the same software?</summary><p>Yes. The insurance is noted at admission, Ayushman patients are billed in the same software, and every claim shows on one list. Healthray is connected to the National Health Claims Exchange (NHCX).</p></details>
      <details><summary>Our accountant uses Tally. Will it connect?</summary><p>Yes. Healthray connects with Tally, so bills do not have to be typed again.</p></details>
      <details><summary>If we stop using Healthray, do we get our data back?</summary><p>Yes. All your data is given back within 30 days. This is written in the data agreement we sign with every hospital.</p></details>
    </div>
  </div>
</section>

<!-- 12. FINAL CTA (button, no form) -->
<section class="final">
  <div class="in">
    <h2>See Healthray Set Up for a Hospital Your Size</h2>
    <p>A free 30-minute video call. We admit a patient, make the bill and discharge them, the way your hospital works. Your price comes in writing within 24 hours, and you do not have to buy.</p>
    <div class="ctas"><a class="btn hr-cta-btn" href="#demo">Book a free demo</a></div>
    <p class="alt">No beds, OPD only? <a href="https://healthray.com/clinic-management-software/">See clinic software</a> · More than 50 beds? <a href="https://healthray.com/hospital-information-management-system/">See software for bigger hospitals</a></p>
  </div>
</section>

<div class="mbar" id="mbar"><a class="btn hr-cta-btn" href="#demo">Book a free demo</a></div>

</main>

<script id="hr-shn-js">
/* Design file JS, plus the steps of the PPC Form. The site popup (second copy of the form) is printed after
   this script, so everything starts on DOMContentLoaded. */
(function(){
  function init(){
  var root=document.getElementById('hr-shn'); if(!root) return;

  /* product video: no controls, plays when on screen, click to pause */
  var v=document.getElementById('walk');
  if(v){
  var calm=window.matchMedia&&window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  var userPaused=false, fr=v.closest('.frame'), pb=document.getElementById('vplay');
  function go(){v.preload='auto';v.play().catch(function(){fr.classList.add('paused');});}
  function toggle(){if(v.paused){userPaused=false;go();}else{userPaused=true;v.pause();fr.classList.add('paused');}}
  v.addEventListener('click',toggle); if(pb) pb.addEventListener('click',toggle);
  v.addEventListener('playing',function(){fr.classList.remove('paused');});
  if(calm) fr.classList.add('paused');
  if('IntersectionObserver' in window){
    new IntersectionObserver(function(es){es.forEach(function(e){
      if(e.isIntersecting){ if(!calm&&!userPaused) go(); }
      else if(!v.paused){v.pause();}
    });},{threshold:.45}).observe(v);
  }
  }

  /* video review: plays in place with sound and controls */
  var rev=document.getElementById('playRev');
  if(rev) rev.addEventListener('click',function(){
    var c=document.getElementById('vcard');
    var r=document.createElement('video');
    r.src=this.getAttribute('data-video');r.controls=true;r.autoplay=true;r.playsInline=true;r.setAttribute('aria-label','Video review from Dr. Payal Choksi');
    c.innerHTML='';c.appendChild(r);r.play().catch(function(){});
  });

  /* mobile: show the bottom demo button after the hero form scrolls away; hide it over the final CTA and the site footer */
  var mb=document.getElementById('mbar'),form=document.getElementById('demo'),fin=root.querySelector('.final'),foot=document.querySelector('footer.hr-home-footer, footer.footer-section');
  var formOut=false,finIn=false,footIn=false;
  function setBar(){mb.classList.toggle('on',formOut&&!finIn&&!footIn);}
  if(mb&&form&&fin&&'IntersectionObserver' in window){
    new IntersectionObserver(function(es){es.forEach(function(e){formOut=!e.isIntersecting&&e.boundingClientRect.top<0;setBar();});}).observe(form);
    new IntersectionObserver(function(es){es.forEach(function(e){finIn=e.isIntersecting;setBar();});}).observe(fin);
    if(foot) new IntersectionObserver(function(es){es.forEach(function(e){footIn=e.isIntersecting;setBar();});}).observe(foot);
  }

  /* PPC Form, hero card and popup. Its own script (js/ppc-hims.js) loads only on the PPC templates, so its steps
     run here: a bed button fills the hidden bed_size field and opens step 2, "change" goes back to step 1. */
  document.querySelectorAll('#hr-shn .hr-demo-form, #myPopup .hr-demo-form').forEach(function(box){
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
  document.querySelectorAll('#hr-shn .hr-demo-form input[name="your-number"], #myPopup .hr-demo-form input[name="your-number"]').forEach(function(input){
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