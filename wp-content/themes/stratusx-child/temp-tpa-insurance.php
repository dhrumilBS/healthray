<?php
/**
 * Template Name: Healthray - TPA & Insurance Management Software
 * Template Post Type: page
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

$hr_cf7_shortcode = '[contact-form-7 id="11eec7d" title="PPC Form"]';

$hr_up = trailingslashit( wp_upload_dir()['baseurl'] );

if ( ! defined( 'WPSEO_VERSION' ) && ! class_exists( 'RankMath' ) ) {
	add_filter( 'pre_get_document_title', function () { return "TPA & Insurance Management Software for Hospitals | Healthray"; } );
	add_action( 'wp_head', function () {
		echo '<meta name="description" content="' . esc_attr( "TPA & insurance management software for hospitals: every cashless patient in one list with the TPA, insurance company, claim number and paid or due. Cashless bills in each TPA's rate list. Free 30-minute demo." ) . '">' . "\n";
	}, 1 );
}

?>

<div class="hr-tpa">
<div class="hr-page">

  <section class="d-hero">
    <img class="d-bg" src="<?php echo esc_url( $hr_up ); ?>2026/11/bg-rainbow.webp" srcset="<?php echo esc_url( $hr_up ); ?>2026/11/bg-rainbow-900.webp 900w, <?php echo esc_url( $hr_up ); ?>2026/11/bg-rainbow.webp 1600w" sizes="100vw" alt="" width="1600" height="900" fetchpriority="high" decoding="async">
    <div class="in">
      <div class="d-l">
        <div class="d-copy">
          <h1 class="eb">TPA &amp; insurance management software</h1>
          <p class="hl1">TPA claims ready in <em>1 day, not 3</em>.</p>
          <p class="lead">Rainbow Hospital, Vapi did it with Healthray. Every cashless patient is in one list, with the claim number and paid or due.</p>
          <a class="more" href="#how">Watch the 1-minute video</a>
        </div>
        <div class="d-trust">
          <div class="hrow">
            <div class="faces" aria-hidden="true"><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Dr.-Bhaumik-Rathore.webp" alt=""><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Dr.-Monil-Parmar.webp" alt=""><img src="<?php echo esc_url( $hr_up ); ?>2024/04/Dr.-Vimal-Dhaduk.webp" alt=""><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Dr.-Arpit-gajjar.webp" alt=""></div>
            <p><b>2,500+ hospitals and clinics</b> across India use Healthray</p>
          </div>
          <div class="hrow">
            <div class="tiles"><span><img src="<?php echo esc_url( $hr_up ); ?>2026/07/NABH-ehr-certified.webp" alt="NABH certified"></span><span><img src="<?php echo esc_url( $hr_up ); ?>2026/03/abdm-healthray.webp" alt="ABDM"></span><span><img src="<?php echo esc_url( $hr_up ); ?>2025/07/NHA.webp" alt="NHA approved"></span><span><img src="<?php echo esc_url( $hr_up ); ?>2026/03/iso-27001-healthray.webp" alt="ISO 27001"></span></div>
            <p><b>NABH certified, follows ABDM rules</b><br>NHA approved · ISO 27001 data security</p>
          </div>
          <div class="rates"><span><b>4.8</b> <i class="stars">★★★★★</i> Google</span><span><b>4.8</b> <i class="stars">★★★★★</i> Capterra</span><span><b>5.0</b> <i class="stars">★★★★★</i> G2</span></div>
        </div>
      </div>
      <div class="card" id="a-form">
        <h2>See your TPA list and cashless bills in a free demo</h2>
        <p>In a <span class="nw">30-minute</span> video call, we show the TPA list, a cashless bill and the pending claims, for a hospital like yours.</p>
        <?php

        if ( '' !== $hr_cf7_shortcode ) {
        	echo do_shortcode( $hr_cf7_shortcode );
        }
        ?>
        <p class="small" style="text-align:center">The demo is free. You do not have to buy.</p>
      </div>
    </div>
    <p class="d-cred">Rainbow Super Speciality Hospital, Vapi · uses Healthray</p>
  </section>

  <section class="logos" aria-label="Hospitals using Healthray"><div class="in">
    <p class="lg-label">Hospitals and medical colleges that use Healthray</p>
    <div class="marquee"><div class="marquee-track" id="lgtrack">
      <ul id="lglist">
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2024/09/HZB-Arogyam-Multispeciality-Hospital.webp" alt="HZB Arogyam Multispeciality Hospital, Hazaribagh, Jharkhand" width="1336" height="534"></span><span class="lg-badge">150 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2024/06/Vibrant-Multispeciality-Hospital.webp" alt="Vibrant Multispecialty Hospital, Vapi" width="1600" height="489"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2025/08/Shraddha-Arogya-Mandir.webp" alt="Shraddha Arogya Mandir, Vapi"></span><span class="lg-badge">120+ beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2024/11/Sri-Manakula-Vinayagar-Hospital.webp" alt="Sri Manakula Vinayagar Medical College, Puducherry"></span><span class="lg-badge mc">Medical college · 1,100 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2025/07/Parivar-Hospital.webp" alt="Parivar Super Speciality Hospital, Madhya Pradesh"></span><span class="lg-badge">140 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2026/11/jeevan-rekha-hospital.webp" alt="Jeevan Rekha Hospital, West Bengal"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2025/12/Budha-Baba-Multispecialty-Hospital.webp" alt="Budha Baba Multispecialty Hospital, Cuttack, Odisha"></span><span class="lg-badge">130 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2026/11/heritage-medical-college.webp" alt="Heritage Medical College, Varanasi"></span><span class="lg-badge mc">Medical college · 1,000 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2026/11/pims-multi-superspeciality-hospital.webp" alt="PIMS Multi-Superspeciality Hospital, Udaipur"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2026/11/jj-plus-hospitals.webp" alt="JJ Plus Hospitals"></span><span class="lg-badge">135 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2024/07/Jabalpur-Hospital-Research-Center.webp" alt="Jabalpur Hospital &amp; Research Centre, Jabalpur, MP"></span><span class="lg-badge">350 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2025/08/Prabh-Aasra-Unified-Family.webp" alt="Prabh Aasra Charitable Hospital, Punjab"></span><span class="lg-badge">120 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2026/11/heritage-hospital.webp" alt="Heritage Hospital, Noida"></span><span class="lg-badge">100 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Universal-Hospital.webp" alt="Universal Hospital, Surat"></span><span class="lg-badge">250 beds</span></li>
        <li><span class="lg-tile"><img src="<?php echo esc_url( $hr_up ); ?>2024/07/Jupiter-Hospital.webp" alt="Jupiter Hospital Group, Vadodara"></span><span class="lg-badge">100 beds</span></li>
      </ul>
    </div></div>
  </div></section>

  <section class="sec tint" id="how" aria-labelledby="walkTitle"><div class="in">
    <div class="sh c">
      <p class="eyebrow">Video tour</p>
      <h2 id="walkTitle">Watch how a <span class="hl">cashless TPA bill</span> is made in Healthray</h2>
      <p class="lead">One TPA patient from admission to the cashless bill: the payer details, the TPA's rate list, room rent added every night, and the pending amount. The patient is not real. The screens are real Healthray screens.</p>
    </div>
    <div class="player" id="player">
      <div class="player-screen">
        <img class="player-poster" src="<?php echo esc_url( $hr_up ); ?>2026/11/tpa-tour-poster.jpg" width="1920" height="1080" alt="Healthray product tour: the IPD bill of a cashless patient, paid by Medi TPA Services, with room rent at the TPA's rate" loading="lazy">
        <video id="walkVideo" muted loop playsinline preload="none" disablepictureinpicture width="1920" height="1080" aria-hidden="true"><source data-src="<?php echo esc_url( $hr_up ); ?>2026/11/tpa-tour-web.mp4" type="video/mp4"></video>
      </div>
      <button type="button" class="vt" id="walkBtn" aria-label="Play the video tour">
        <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true"><path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z"/></svg>
        <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true"><rect x="6" y="5" width="4" height="14" rx="1"/><rect x="14" y="5" width="4" height="14" rx="1"/></svg>
      </button>
      <button type="button" class="vt vt-fs" id="walkFs" aria-label="Watch the video tour full screen">
        <svg viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 9V5a1 1 0 0 1 1-1h4M15 4h4a1 1 0 0 1 1 1v4M20 15v4a1 1 0 0 1-1 1h-4M9 20H5a1 1 0 0 1-1-1v-4"/></svg>
      </button>
    </div>
    <div class="walk-cta">
      <p><b>Want to see it with the TPAs your hospital works with?</b> Book a free <span class="nw">30-minute</span> demo. We show the TPA list and a cashless bill for a hospital like yours.</p>
      <a class="btn" href="#a-form">Book a free demo</a>
    </div>
  </div></section>

  <section class="sec m4" id="money">
  <div class="in">
    <div class="sh">
      <h2>Where cashless money gets <span class="hl">cut, or comes late</span></h2>
      <p class="lead">In 2023-24, insurance companies cut ₹15,100 crore from health claims, and 6 in 10 insured patients waited 6 to 48 hours to go home. Healthray cannot change the insurance company's rules. It helps your TPA desk send the right bill and follow up every claim until it is paid.</p>
    </div>
    <div class="m4a">
      <ol id="m4a-list">
        <li class="on"><button type="button" aria-expanded="true"><h3>Money cut from the bill</h3></button><div class="b"><p class="tx">One reason TPAs cut money: a charge is not at the TPA's rate. In Healthray, the bill of a TPA patient uses that TPA's own rate list for the ward, and room rent is added every night.</p><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-rate-m.webp" alt="IPD Billing Master in Healthray: Medi TPA Services selected, room rent, doctor visit and nursing rates for General, Private and ICU. Sample figures" width="1149" height="942" loading="lazy"></div></li>
        <li><button type="button" aria-expanded="false"><h3>Claim sent back for missing details</h3></button><div class="b"><p class="tx">The TPA, insurance company, policy number and card number are saved once, at admission. The cashless bill is printed from the same patient file, so your staff do not type them again.</p><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-payer-m.webp" alt="Payer Details at admission in Healthray: TPA, insurance company, policy number and card number. Sample patient" width="1149" height="1098" loading="lazy"></div></li>
        <li><button type="button" aria-expanded="false"><h3>Family waiting at discharge</h3></button><div class="b"><p class="tx">Healthray notes the time of each discharge step: marked for discharge, sent for billing, bill prepared, discharge approved. You see where the patient is waiting.</p><p class="rule">IRDAI rule (2024): the insurance company must give discharge approval within 3 hours of the hospital's request.</p><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-track-m.webp" alt="IPD Tracking in Healthray: time of each discharge step, discharge approval not yet given. Sample patient" width="1149" height="813" loading="lazy"></div></li>
        <li><button type="button" aria-expanded="false"><h3>Payment that comes late</h3></button><div class="b"><p class="tx">Every cashless patient stays in one TPA list with the claim number. Select "Due" to see the claims not yet paid, search by claim number, and download the list to Excel to follow up.</p><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-list-m.webp" alt="TPA Patient List in Healthray with Bill status Due: claims not yet paid with TPA and claim number. Sample patients" width="1149" height="1077" loading="lazy"></div></li>
      </ol>
      <div class="pic" id="m4a-pic">
        <img class="dk on" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-rate.webp" alt="IPD Billing Master in Healthray: Medi TPA Services selected, room rent, doctor visit and nursing rates for General, Private and ICU. Sample figures" width="1240" height="744" loading="lazy">
        <img class="dk" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-payer.webp" alt="Payer Details at admission in Healthray: payer type TPA, TPA, insurance company, policy number and card number. Sample patient" width="1240" height="748" loading="lazy">
        <img class="dk" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-track.webp" alt="IPD Tracking in Healthray: time of each discharge step, discharge approval not yet given. Sample patient" width="1240" height="664" loading="lazy">
        <img class="dk" src="<?php echo esc_url( $hr_up ); ?>2026/11/s4-list.webp" alt="TPA Patient List in Healthray with Bill status Due: claims not yet paid with TPA and claim number. Sample patients" width="1240" height="776" loading="lazy">
      </div>
    </div>
    <div class="m4-cta"><p><b>See these 4 things for your hospital.</b> In the free demo we show them with the TPAs your hospital works with.</p><a class="btn" href="#a-form">Book a free demo</a></div>
  </div>
</section>

  <section class="sec p5" id="payers">
  <div class="in">
    <div class="sh">
      <h2>One software for <span class="hl">all your cashless patients</span>: TPA, insurance company, Ayushman and company patients</h2>
      <p class="lead">Some of your patients pay through a TPA, some through an insurance company directly, some through Ayushman, and some through their company. Healthray keeps all of them in one software, each with its own rates.</p>
    </div>
    <div class="p5a">
      <div class="c"><h3>Patients with a TPA</h3><p class="tx">Your desk saves the TPA and the insurance company at admission. The bill uses that TPA's rates, prints as a cashless bill, and the claim number stays in your TPA list.</p><div class="pic"><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s5-tpa-m.webp" alt="Print menu on an IPD bill in Healthray with Cashless Bill Print chosen. Sample patient" width="1149" height="921" loading="lazy"></div></div>
      <div class="c"><h3>Patients whose insurance company has no TPA</h3><p class="tx">Some insurance companies handle claims themselves: about 3 in 10 health claims in 2024-25. Your desk selects the insurance company as the one who pays, with the patient's policy and card number.</p><div class="pic"><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s5-ins-m.webp" alt="Payer Details in Healthray: insurance company as payer, no TPA, policy number. Sample patient" width="1149" height="906" loading="lazy"></div></div>
      <div class="c"><h3>Ayushman (PM-JAY) patients</h3><p class="tx">Your Ayushman patients are billed in the same software. Select Ayushman in the "Payer" list to see only them.</p><div class="pic"><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s5-ayu-m.webp" alt="TPA Patient List in Healthray with the payer list open and Ayushman chosen" width="1149" height="861" loading="lazy"></div></div>
      <div class="c"><h3>Patients sent by a company</h3><p class="tx">When a company sends its staff to your hospital, they are billed at the rates you agreed with that company, for each ward.</p><div class="pic"><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s5-comp-m.webp" alt="IPD Billing Master in Healthray with a company chosen and its General ward rates. Sample company" width="1149" height="837" loading="lazy"></div></div>
    </div>
    <div class="p5-cta"><p><b>Tell us which TPAs and insurance companies your hospital works with.</b> We show them in the free demo.</p><a class="btn" href="#a-form">Book a free demo</a></div>
  </div>
</section>

  <section class="sec o6 o6a" id="owe">
  <div class="in">
    <div class="hd">
      <div class="sh">
        <h2>See the <span class="hl">pending amount of every TPA patient</span>, every morning</h2>
        <p class="lead">You and your TPA staff see the same list. No need to call the billing counter or keep a separate Excel sheet.</p>
      </div>
      <div class="arr"><button type="button" id="o6a-prev" aria-label="Previous">‹</button><button type="button" id="o6a-next" aria-label="Next">›</button></div>
    </div>
    <div class="trk" id="o6a-trk">
      <article class="cd"><div class="ph"><img class="dk" src="<?php echo esc_url( $hr_up ); ?>2026/11/s6-summary.webp" alt="Bill Summary in Healthray with one TPA chosen: each admitted patient's due amount and bill amount, Download PDF and Excel. Sample patients" width="1240" height="736" loading="lazy"><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s6-summary-m.webp" alt="Bill Summary with one TPA chosen: due amount of each patient. Sample patients" width="1149" height="1041" loading="lazy"></div><div class="t"><h3>Select a TPA, see the pending amount</h3><p class="tx">Open Bill Summary and select a TPA. You see every admitted patient of that TPA, with the bill amount and the pending amount. Download it in PDF or Excel.</p></div></article>
      <article class="cd"><div class="ph"><img class="dk" src="<?php echo esc_url( $hr_up ); ?>2026/11/s6-bed.webp" alt="Bed board cards in Healthray showing who pays and a Due mark. Sample patients" width="1240" height="506" loading="lazy"><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s6-bed-m.webp" alt="Bed card showing who pays and a Due mark. Sample patient" width="1149" height="669" loading="lazy"></div><div class="t"><h3>Due or Paid on each bed's card</h3><p class="tx">Each bed card shows who pays the bill: cash, TPA or Ayushman. It also shows Due or Paid. Click Due to see only those beds.</p></div></article>
      <article class="cd"><div class="ph"><img class="dk" src="<?php echo esc_url( $hr_up ); ?>2026/11/s6-reports.webp" alt="Reports in Healthray: TPA Report, Company Wise Report, Date Wise Due Bill and TDS Report" width="1240" height="662" loading="lazy"><img class="mb" src="<?php echo esc_url( $hr_up ); ?>2026/11/s6-reports-m.webp" alt="Reports: TPA Report, Company Wise Report, Date Wise Due Bill, TDS Report" width="1149" height="948" loading="lazy"></div><div class="t"><h3>Reports for your accounts team</h3><p class="tx">TPA Report, Company Wise Report, Date Wise Due Bill and TDS Report are in the Reports menu. Download them any time.</p></div></article>
    </div>
    <div class="dots" id="o6a-dots"></div>
    <div class="o6-cta"><p><b>Want to see these lists?</b> We show them in the free demo.</p><a class="btn" href="#a-form">Book a free demo</a></div>
  </div>
</section>

  <section class="sec r7" id="results">
  <div class="in">
    <div class="sh">
      <h2>What changed at <span class="hl">4 hospitals</span> that use Healthray</h2>
      <p class="lead">Numbers from each hospital's case study on healthray.com. We show the TPA and discharge numbers first.</p>
    </div>
    <div class="r7a">
      <div class="c"><div class="ph"><img src="<?php echo esc_url( $hr_up ); ?>2026/10/Rainbow-Hospital.webp" alt="Rainbow Super Speciality Hospital, Vapi" width="900" height="506" loading="lazy"></div><div class="t"><p class="hos">Rainbow Super Speciality Hospital<small>Vapi · 100 beds</small></p><b class="big">1 day, not 3</b><p class="what">to get a TPA claim ready</p><ul class="r7l"><li>Wait on discharge day: 3 hours, not 6</li><li>Started in 14 working days</li></ul><a class="lk" href="https://healthray.com/case-studies/rainbow-hospital-vapi/">Read Rainbow's story →</a></div></div>
      <div class="c"><div class="ph"><img src="<?php echo esc_url( $hr_up ); ?>2025/11/Jabalpur-Hospital.webp" alt="Jabalpur Hospital and Research Centre" width="900" height="352" loading="lazy"></div><div class="t"><p class="hos">Jabalpur Hospital &amp; Research Centre<small>Jabalpur · 300+ beds</small></p><b class="big">50% faster</b><p class="what">billing, pharmacy and TPA work</p><ul class="r7l"><li>95% correct patient details</li></ul><div class="q"><img src="<?php echo esc_url( $hr_up ); ?>2026/11/rajesh-dhirawani.webp" alt="" width="40" height="40"><span>"Every work operation from diagnosis to discharge can now be accessed in just one single click."<b>Dr. Rajesh B. Dhirawani, Surgeon</b></span></div><a class="lk" href="https://healthray.com/case-studies/jabalpur-hospital/">Read Jabalpur's story →</a></div></div>
      <div class="c"><div class="ph"><img src="<?php echo esc_url( $hr_up ); ?>2025/12/Budha-Baba-Multispecialty-Hospital-1.webp" alt="Budha Baba Multispeciality Hospital, Cuttack" width="900" height="450" loading="lazy"></div><div class="t"><p class="hos">Budha Baba Multispeciality Hospital<small>Cuttack · 100 beds</small></p><b class="big">30% faster</b><p class="what">billing and discharge</p><ul class="r7l"><li>40% fewer mistakes in bills and records</li><li>Old data moved in 1 day</li></ul><a class="lk" href="https://healthray.com/case-studies/bbmh-hospital/">Read BBMH's story →</a></div></div>
      <div class="c"><div class="ph"><img src="<?php echo esc_url( $hr_up ); ?>2025/12/Vibrant-Hospital-Healthray.webp" alt="Vibrant Multispeciality Hospital, Vapi" width="900" height="450" loading="lazy"></div><div class="t"><p class="hos">Vibrant Multispeciality Hospital<small>Vapi · 200+ beds</small></p><b class="big">₹25 lakh</b><p class="what">saved in a year</p><ul class="r7l"><li>40% fewer mistakes in bills and records</li></ul><div class="q"><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Dr.-Bhaumik-Rathore.webp" alt="" width="40" height="40"><span>"Now patient records, billing, pharmacy and inventory are all connected in one system."<b>Dr. Bhaumik Rathore</b></span></div><a class="lk" href="https://healthray.com/case-studies/vibrant-hospital/">Read Vibrant's story →</a></div></div>
    </div>
    <div class="r7-cta"><p><b>Want results like these?</b> See Healthray for your hospital in a free demo.</p><a class="btn" href="#a-form">Book a free demo</a></div>
  </div>
</section>

  <section class="sec s8 s8c" id="start">
  <div class="in">
    <div class="sh">
      <h2>How you start, and <span class="hl">what it costs</span></h2>
      <p class="lead">There is no fixed price list. You get your price in writing within 24 hours of the free demo.</p>
    </div>
    <div class="two">
      <ol class="steps">
        <li><b>Free <span class="nw">30-minute</span> demo</b><span>We show the TPA list and a cashless bill for a hospital like yours.</span></li>
        <li><b>Your price in writing</b><span>Within 24 hours of the demo. Moving your old records and rate lists, and training at your hospital, are included.</span></li>
        <li><b>Start in 2 weeks or less</b><span>Our team brings in your old records and rate lists, and teaches your TPA staff at your hospital.</span></li>
      </ol>
      <div class="card8">
        <div class="z"><b>₹0</b><span>for your <span class="nw">30-minute</span> demo</span></div>
        <h3>Your price depends on</h3>
        <ul class="dep"><li><b>Number of beds</b><span>You pay for the beds you have today.</span></li><li><b>The parts of Healthray you use</b><span>Start with admission, billing and TPA. Add pharmacy, lab and the rest later.</span></li><li><b>Online, or on your own computer in the hospital</b><span>Use it online, or keep it on your own computer (server) inside the hospital.</span></li></ul>
        <p class="note8">WhatsApp and SMS messages are paid separately.</p>
      </div>
    </div>
    <div class="facts8">
      <div><b class="num">No break</b><p class="lab">in billing</p><p class="sm">at 7 hospitals while they moved to Healthray</p></div>
      <div><b class="num">1 day</b><p class="lab">to bring in old records</p><p class="sm">at BBMH and Vibrant</p></div>
      <div><b class="num">2 weeks</b><p class="lab">or less to start</p><p class="sm">at most hospitals in our case studies</p></div>
      <div><b class="num">24×7</b><p class="lab">support</p></div>
    </div>
    <div class="s8-cta"><p><b>Tell us your number of beds.</b> We send your price after the free demo.</p><a class="btn" href="#a-form">Get a price for my hospital</a></div>
  </div>
</section>

  <section class="sec f9 f9b" id="faq">
  <div class="in">
    <div class="sh"><h2>Questions hospitals ask about <span class="hl">TPA and insurance software</span></h2>
      <p class="lead">Did not find your question? <a href="#a-form">Ask it in the free demo</a>.</p></div>
    <div class="list">
        <details name="tpa-faq" open><summary>Does it work for TPA patients and for insurance companies without a TPA?</summary><p>Yes. At admission your desk selects the TPA, or the insurance company if there is no TPA, with the policy and card number. Ayushman and company patients are in the same software too.</p></details>
        <details name="tpa-faq"><summary>Can we print the cashless bill in the format the TPA wants?</summary><p>Yes. The IPD bill has 13 print formats, including the cashless bill, the cashless detail bill and the package detail bill.</p></details>
        <details name="tpa-faq"><summary>Who can change a TPA patient's bill?</summary><p>Only the staff you allow. Each person has their own login with OTP. Every change is saved with the person's name and the time.</p></details>
        <details name="tpa-faq"><summary>Does it connect with Tally?</summary><p>Yes. Healthray connects with Tally. Ask us to show it in the free demo.</p></details>
        <details name="tpa-faq"><summary>What if the internet stops?</summary><p>Use your mobile hotspot, or keep Healthray on your own computer (server) inside the hospital.</p></details>
        <details name="tpa-faq"><summary>Do we need new computers?</summary><p>No. Healthray opens in Chrome or any internet browser on the computers you already have.</p></details>
        <details name="tpa-faq"><summary>If we stop using Healthray, do we get our data?</summary><p>Yes. We give you all your data within 30 days, in writing, as per India's data law. Your data stays in India, or on your own computer in the hospital.</p></details>
    </div>
  </div>
</section>

  <section class="sec l10" id="book">
  <div class="in"><div class="c10">
    <div class="who10"><span class="f"><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Dr.-Bhaumik-Rathore.webp" alt="" width="44" height="44"><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Dr.-Monil-Parmar.webp" alt="" width="44" height="44"><img src="<?php echo esc_url( $hr_up ); ?>2024/04/Dr.-Vimal-Dhaduk.webp" alt="" width="44" height="44"><img src="<?php echo esc_url( $hr_up ); ?>2025/10/Dr.-Arpit-gajjar.webp" alt="" width="44" height="44"></span>2,500+ hospitals and clinics use Healthray</div>
    <h2>See the TPA list and a cashless bill in Healthray, <span class="hl">before you decide</span></h2>
    <p class="lead">In a free <span class="nw">30-minute</span> video call, we show the TPA list, a cashless bill and the pending amount of each TPA patient, for a hospital like yours.</p>
    <a class="btn big" href="#a-form">Book my free demo →</a>
    <p class="prom">The demo is free. You do not have to buy.</p>
  </div></div>
</section>

</div>

<dialog class="hr-pop" id="hr-pop" aria-labelledby="hr-pop-title">
  <div class="hr-pop-box">
    <button type="button" class="hr-pop-x" aria-label="Close">×</button>
    <h2 id="hr-pop-title">See your TPA list and cashless bills in a free demo</h2>
    <p>In a <span class="nw">30-minute</span> video call, we show the TPA list, a cashless bill and the pending claims, for a hospital like yours.</p>
    <?php
    if ( '' !== $hr_cf7_shortcode ) {
    	echo do_shortcode( $hr_cf7_shortcode );
    }
    ?>
    <p class="small" style="text-align:center">The demo is free. You do not have to buy.</p>
  </div>
</dialog>

</div>