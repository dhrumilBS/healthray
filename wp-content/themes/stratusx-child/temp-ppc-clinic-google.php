<?php

/**
 * Template Name: PPC - Clinic Google
 *
 * Page 82618, /ppc/clinic-google/. CSS/JS: css/ppc-clinic.css, js/ppc-clinic.js (loaded by functions.php, key "clinic").
 * Form: CF7 "PPC - Clinic", 2 steps; step 1 speciality goes in bed_size; Odoo keys bed_size, name, mobile, email, ref.
 */

defined('ABSPATH') || exit;

$hr_cl_form = '[contact-form-7 id="e8978a5" title="PPC - Clinic"]';
$hr_cl_up   = 'https://healthray.com/wp-content/uploads';

// Google Ads (India) rejects drug names in images. doctor-screen-desk.webp and doctor-screen-phone.webp were
// exported again on 24 Nov 2026: they now show "Allergy: Dust" (no medicine name). Upload the new files first
// (replace /2026/11/doctor-screen-desk.webp and /2026/11/doctor-screen-phone.webp), then keep this true.
$hr_cl_rx_media = true;
$hr_cl_n        = 0;

$hr_cl_wa = hr_ppc_wa_url('Hi, I want a free demo of Healthray clinic software.');
?>
<div class="hr-page" id="hr-page">

  <button type="button" class="ai-rib" data-ai-open aria-haspopup="dialog"><span class="ai-dot" aria-hidden="true"></span><span><b>New: AI Scribe</b> <span class="long">writes your notes while you talk to the patient.</span><span class="short">writes your notes as you talk.</span></span><span class="go">Watch 10 sec ▸</span></button>
  <div class="top">
    <div class="in"><img src="<?php echo esc_url($hr_cl_up . '/2024/02/Healthray-Logo.svg'); ?>" alt="Healthray"><a class="btn" href="#demo" data-hr-pop>Book a free demo</a></div>
  </div>

  <section class="a-hero" id="demo">
    <div class="in">
      <div class="a-l">
        <div class="a-copy">
          <h1 class="kw">AI-powered clinic management software</h1>
          <p class="big">Easy clinic software, made for <span class="spw" id="sp-word">your speciality</span>.</p>
          <p class="lead">Prescriptions, appointments and bills in one place, with ready templates for 40+ specialities. Talk to your patient and AI Scribe writes the notes. You check and save.</p>
        </div>
        <div class="a-after">
          <ul class="ticks">
            <li>We move your old patient data, free</li>
            <li>We train you and your staff, free</li>
            <li>Help on call in Hindi, Gujarati or English</li>
          </ul>
          <div class="a-trust">
            <div class="hrow">
              <div class="faces" aria-hidden="true"><img src="<?php echo esc_url($hr_cl_up . '/2024/04/Dr.-Milan-Modi.webp'); ?>" alt=""><img src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-kaivan-shah.webp'); ?>" alt=""><img src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-Gautam-Beladiya.webp'); ?>" alt=""><img src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-Purav-Kansara.webp'); ?>" alt=""><img src="<?php echo esc_url($hr_cl_up . '/2026/10/Dr.-Hinal-Parmar-Rainbow-Hospital.webp'); ?>" alt=""></div>
              <p><b>2,500+ hospitals and clinics</b> across India use Healthray</p>
            </div>
            <div class="hrow">
              <div class="tiles"><span><img src="<?php echo esc_url($hr_cl_up . '/2026/07/NABH-ehr-certified-150x150.webp'); ?>" alt="NABH certified"></span><span><img src="<?php echo esc_url($hr_cl_up . '/2026/03/abdm-healthray-150x150.webp'); ?>" alt="ABDM compliant"></span><span><img src="<?php echo esc_url($hr_cl_up . '/2025/07/NHA.webp'); ?>" alt="NHA approved"></span><span><img src="<?php echo esc_url($hr_cl_up . '/2026/03/iso-27001-healthray-150x150.webp'); ?>" alt="ISO 27001"></span></div>
              <p><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
            </div>
            <div class="rates"><span><b>4.8</b> <i class="stars">★★★★★</i> Google</span><span><b>4.8</b> <i class="stars">★★★★★</i> Capterra</span><span><b>5.0</b> <i class="stars">★★★★★</i> G2</span></div>
          </div>
        </div>
      </div>
      <div class="card" id="hr-clinic-form">
        <h2>Book a free demo</h2>
        <p>A free video call. We show you how it works in a clinic like yours.</p>
        <div class="prog"><small id="a-lbl">STEP 1 OF 2 · TAKES 20 SECONDS</small>
          <div><i id="a-bar"></i></div>
        </div>
        <?php echo do_shortcode($hr_cl_form); ?>
        <p class="small" style="text-align:center">No charge. No need to decide on the call.</p>
      </div>
    </div>
  </section>

  <section class="crow">
    <div class="in">
      <div class="crow-h">
        <h2>Clinics and small hospitals that <em>use Healthray</em></h2>
      </div>
    </div>
    <div class="cr-rail">
      <div class="cr-track">
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Dr.-Dave-Eye-Hospital.webp'); ?>" alt=""></div>
          <div><b>Dave Eye Hospital</b><span>Surat, Gujarat</span></div>
        </div>
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Modi-Children-Hospital.webp'); ?>" alt=""></div>
          <div><b>Modi Children Hospital</b><span>Vyara, Gujarat</span></div>
        </div>
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Zenith-Doctor-House.webp'); ?>" alt=""></div>
          <div><b>Zenith Doctor House</b><span>Valsad, Gujarat</span></div>
        </div>
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/04/Kanha-Medical-Centre.webp'); ?>" alt=""></div>
          <div><b>Kanha Medical Centre</b><span>Chegur, Telangana</span></div>
        </div>
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2024/09/Chakraa-Medical-Center.webp'); ?>" alt=""></div>
          <div><b>Chakraa Medical Center</b><span>Tiruppur, Tamil Nadu</span></div>
        </div>
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Mehta-Hospital.webp'); ?>" alt=""></div>
          <div><b>Mehta Hospital</b><span>Valsad, Gujarat</span></div>
        </div>
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Pardi-Hospital.webp'); ?>" alt=""></div>
          <div><b>Pardi Hospital</b><span>Vapi, Gujarat</span></div>
        </div>
        <div class="bdg">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/04/Kishori-Hospital.webp'); ?>" alt=""></div>
          <div><b>Kishori Hospital</b><span>Bargarh, Odisha</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Dr.-Dave-Eye-Hospital.webp'); ?>" alt=""></div>
          <div><b>Dave Eye Hospital</b><span>Surat, Gujarat</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Modi-Children-Hospital.webp'); ?>" alt=""></div>
          <div><b>Modi Children Hospital</b><span>Vyara, Gujarat</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Zenith-Doctor-House.webp'); ?>" alt=""></div>
          <div><b>Zenith Doctor House</b><span>Valsad, Gujarat</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/04/Kanha-Medical-Centre.webp'); ?>" alt=""></div>
          <div><b>Kanha Medical Centre</b><span>Chegur, Telangana</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2024/09/Chakraa-Medical-Center.webp'); ?>" alt=""></div>
          <div><b>Chakraa Medical Center</b><span>Tiruppur, Tamil Nadu</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Mehta-Hospital.webp'); ?>" alt=""></div>
          <div><b>Mehta Hospital</b><span>Valsad, Gujarat</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/08/Pardi-Hospital.webp'); ?>" alt=""></div>
          <div><b>Pardi Hospital</b><span>Vapi, Gujarat</span></div>
        </div>
        <div class="bdg" aria-hidden="true">
          <div class="lg"><img src="<?php echo esc_url($hr_cl_up . '/2025/04/Kishori-Hospital.webp'); ?>" alt=""></div>
          <div><b>Kishori Hospital</b><span>Bargarh, Odisha</span></div>
        </div>
      </div>
    </div>
  </section>

  <section class="sec see tint">
    <div class="in">
      <div class="col960">
        <div class="sh-wrap">
          <h2 class="sh">Watch one patient go through <em>your clinic.</em></h2>
          <p class="sh-sub">A short video of the software at work. It has no sound, so you can watch it between patients.</p>
        </div>
        <div class="player">
          <video id="s2a-v" muted playsinline preload="none" poster="<?php echo esc_url($hr_cl_up . '/2026/11/clinic-tour-v2-poster.jpg'); ?>" src="<?php echo esc_url($hr_cl_up . '/2026/11/clinic-tour-v2-web.mp4'); ?>"></video>
          <button class="pp" id="s2a-pp" type="button" aria-label="Play or pause the video">▶</button>
          <button class="fs" id="s2a-fs" type="button" aria-label="Watch the video full screen"><svg class="fs-in" viewBox="0 0 24 24" aria-hidden="true"><path d="M4 9V4h5M20 9V4h-5M4 15v5h5M20 15v5h-5"/></svg><svg class="fs-out" viewBox="0 0 24 24" aria-hidden="true"><path d="M9 4v5H4M15 4v5h5M9 20v-5H4M15 20v-5h5"/></svg></button>
        </div>
      </div>
    </div>
  </section>

  <section class="sec see" id="desks" style="--dp-n:<?php echo $hr_cl_rx_media ? 5 : 4; ?>">
    <div class="in">
      <div class="dp-head">
        <h2 class="sh">One software <em>for your whole clinic.</em></h2>
        <p class="sh-sub">Each desk in your clinic gets its own screen, and all of them see the same patient file.</p>
      </div>
      <div class="dp-pin" id="dp-pin">
        <div class="dp">
          <div class="dp-list auto" id="dp-list" role="tablist" aria-label="Desks of the clinic">
            <button type="button" class="on" role="tab"><small><?php printf('%02d', ++$hr_cl_n); ?></small>Front desk and token<i></i></button>
            <button type="button" role="tab"><small><?php printf('%02d', ++$hr_cl_n); ?></small>Appointments<i></i></button>
            <?php if ($hr_cl_rx_media) : ?><button type="button" role="tab"><small><?php printf('%02d', ++$hr_cl_n); ?></small>Doctor's prescription<i></i></button><?php endif; ?>
            <button type="button" role="tab"><small><?php printf('%02d', ++$hr_cl_n); ?></small>Billing<i></i></button>
            <button type="button" role="tab"><small><?php printf('%02d', ++$hr_cl_n); ?></small>Owner's report<i></i></button>
          </div>
          <div>
            <div class="dp-shot" id="dp-shot">
              <picture>
                <source media="(max-width: 820px)" srcset="<?php echo esc_url($hr_cl_up . '/2026/11/token-tv-phone.webp'); ?>"><img loading="lazy" decoding="async" class="d-shadow d-token on full" src="<?php echo esc_url($hr_cl_up . '/2026/11/token-tv-desk.webp'); ?>" alt="The waiting-area TV for one doctor and the printed token">
              </picture>
              <img loading="lazy" decoding="async" class="d-shadow d-appt" src="<?php echo esc_url($hr_cl_up . '/2026/11/appointments.webp'); ?>" alt="The appointment list with online bookings and WhatsApp reminders">
              <?php if ($hr_cl_rx_media) : ?><picture>
                  <source media="(max-width: 820px)" srcset="<?php echo esc_url($hr_cl_up . '/2026/11/doctor-screen-phone.webp'); ?>"><img loading="lazy" decoding="async" class="d-shadow d-rx full" src="<?php echo esc_url($hr_cl_up . '/2026/11/doctor-screen-desk.webp'); ?>" alt="The doctor's screen filling itself while AI Scribe listens, with Review and save">
                </picture><?php endif; ?>
              <picture>
                <source media="(max-width: 820px)" srcset="<?php echo esc_url($hr_cl_up . '/2026/11/billing-phone.webp'); ?>"><img loading="lazy" decoding="async" class="d-shadow d-bill full" src="<?php echo esc_url($hr_cl_up . '/2026/11/billing-desk.webp'); ?>" alt="The bill screen and the printed bill, paid by UPI">
              </picture>
              <img loading="lazy" decoding="async" class="d-shadow d-owner" src="<?php echo esc_url($hr_cl_up . '/2026/11/owner-reports.webp'); ?>" alt="The day's collection on WhatsApp">
            </div>
            <div id="dp-bodies">
              <div class="dp-body on">
                <div>
                  <h3>Patients get a token and see their turn on the TV</h3>
                  <p>Type the mobile number, the old patient comes up, the token prints. Any TV with internet shows each doctor's token, room and waiting time.</p><a class="more" href="#demo" data-hr-pop>See this in a demo →</a>
                </div>
                <ul class="dp-ticks">
                  <li>The same patient is never added twice</li>
                  <li>Walk-ins and online bookings on one list</li>
                  <li>One TV per floor, or one per doctor</li>
                  <li>Reception sees who has waited, and for how long</li>
                </ul>
              </div>
              <div class="dp-body">
                <div>
                  <h3>Patients book online and get reminders on WhatsApp</h3>
                  <p>Set each doctor's timing once. Online bookings and walk-in patients come on the same list, and a reminder goes before every visit.</p><a class="more" href="#demo" data-hr-pop>See this in a demo →</a>
                </div>
                <ul class="dp-ticks">
                  <li>Mark the doctor's leave in advance</li>
                  <li>Follow-up reminders</li>
                  <li>A list of patients who missed a follow-up</li>
                  <li>Video consultation in the same software</li>
                </ul>
              </div>
              <?php if ($hr_cl_rx_media) : ?>
                <div class="dp-body">
                  <div>
                    <h3>Old reports and today's prescription on one screen</h3>
                    <p>The doctor sees past visits, reports and medicines, and writes today's prescription from their own templates. It prints on your letterhead and goes to the patient's WhatsApp.</p><a class="more" href="#demo" data-hr-pop>See this in a demo →</a>
                  </div>
                  <ul class="dp-ticks">
                    <li>Last prescription repeated in one click</li>
                    <li>Allergies pop up in red</li>
                    <li>Hindi, Gujarati or 8 more languages</li>
                    <li>AI Scribe writes notes as you talk</li>
                  </ul>
                </div>
              <?php endif; ?>
              <div class="dp-body">
                <div>
                  <h3>One bill for the doctor's fee and the tests</h3>
                  <p>The right consultation fee is on the bill as soon as the appointment is booked. Add tests, take cash, UPI or card, and send the bill on WhatsApp.</p><a class="more" href="#demo" data-hr-pop>See this in a demo →</a>
                </div>
                <ul class="dp-ticks">
                  <li>Payment link for patients who pay later</li>
                  <li>Advance deposits and refunds</li>
                  <li>OPD packages</li>
                  <li>A list of tests that were not billed</li>
                </ul>
              </div>
              <div class="dp-body">
                <div>
                  <h3>Today's patients and collection on your phone</h3>
                  <p>The day's collection comes on your WhatsApp at night. The full report opens on your phone any time, from anywhere.</p><a class="more" href="#demo" data-hr-pop>See this in a demo →</a>
                </div>
                <ul class="dp-ticks">
                  <li>Doctor-wise new and follow-up patients</li>
                  <li>Cash, UPI and card</li>
                  <li>Every discount and refund in one report</li>
                  <li>How long patients waited</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="sec dk">
    <div class="in">
      <div class="sh-wrap">
        <h2 class="sh">“Will anyone answer my call <em>after I pay?</em>”</h2>
        <p class="sh-sub">Ask every software company this before you buy. Here is our answer, and a clinic doctor's.</p>
      </div>
      <div class="ap1">
        <figure class="ap-say">
          <blockquote>“If I face any issue, I can call them and <mark>someone responds who understands the problem</mark>, not just a standard answer. For a clinic like mine, that kind of support really matters because we don't have an IT person here all the time.”</blockquote>
          <figcaption class="ap-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2024/04/Dr.-Milan-Modi.webp'); ?>" alt="Dr. Milan Modi"><span><b>Dr. Milan Modi</b>Modi Allergy &amp; Chest Clinic</span></figcaption>
        </figure>
        <ul class="ap-list">
          <li><b>We answer, 24×7.</b><span>On phone call or chat, in English, Hindi or Gujarati.</span></li>
          <li><b>Your patient data is yours.</b><span>If you ever stop using Healthray, we give you all of it within 30 days.</span></li>
          <li><b>Internet stopped? Keep working.</b><span>Connect your mobile hotspot. Your data stays safe on servers in India.</span></li>
        </ul>
      </div>
      <p class="cta-line">Ask us anything on a free demo call. <a class="btn" href="#demo" data-hr-pop>Book a free demo</a></p>
    </div>
  </section>

  <section class="sec s6">
    <div class="in">
      <div class="sh-wrap">
        <h2 class="sh">Start using it in about 7 days. <em>Our team does the work.</em></h2>
        <p class="sh-sub">You keep seeing patients. Our team sets up your clinic, moves your old data and trains your staff.</p>
      </div>
      <ol class="pl">
        <li>
          <div class="n"><b>1</b></div><small>Free demo</small>
          <h3>See it on a video call</h3>
          <p>We show Healthray set up for a clinic like yours: a patient, a prescription and a bill. No need to decide on the call.</p>
        </li>
        <li>
          <div class="n"><b>2</b></div><small>Setup</small>
          <h3>We move your old patient data</h3>
          <p>From your register, Excel or old software, usually in a day. Your fees, bill format and letterhead are set up for you.</p>
        </li>
        <li>
          <div class="n"><b>3</b></div><small>Training</small>
          <h3>We train your staff</h3>
          <p>Each person learns only their own screen. Your receptionist learns it in a day or two.</p>
        </li>
        <li>
          <div class="n"><b>4</b></div><small>After you start</small>
          <h3>24×7 help on call</h3>
          <p>On phone call or chat, in English, Hindi or Gujarati.</p>
        </li>
      </ol>
      <div class="spec6"><b>Set up for your speciality.</b><span>General physicians and gynaec, child, bone, eye, ENT, skin and heart doctors get screens made for their work.</span></div>
      <p class="cta-line">Want us to set up your clinic? <a class="btn" href="#demo" data-hr-pop>Book a free demo</a></p>
    </div>
  </section>

  <section class="sec tint">
    <div class="in">
      <div class="rv-h">
        <div class="sh-wrap">
          <h2 class="sh">What doctors say <em>after using it.</em></h2>
          <p class="sh-sub">Doctors who use Healthray in their OPD every day. You can read these on healthray.com.</p>
        </div>
        <div class="rv-arrows"><button type="button" id="rv-prev" aria-label="Earlier reviews">←</button><button type="button" id="rv-next" aria-label="More reviews">→</button></div>
      </div>
      <div class="rv-rail" id="rv-rail">
        <article class="rv k1">
          <p>“The reminders changed our days; <mark>no-shows dropped from about a fifth of bookings to roughly one in eight</mark>, so specialists stopped losing slots. Training took two rounds for some of us, but the team stayed with us until it stuck.”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/11/Dr.-Gunjan-Desai.webp'); ?>" alt="Dr. Gunjan Desai">
            <div><b>Dr. Gunjan Desai</b><span>Lilavati Clinic &amp; Wellness, Ahmedabad</span></div>
          </div>
        </article>
        <article class="rv k2">
          <p>“Most of our patients come for follow-ups, so having their previous consultation history, reports and prescriptions in one place is very helpful. <mark>It saves time during OPD</mark> and makes it easier to continue treatment without searching for old records. The system has been simple to use in daily practice and fits well into our routine workflow.”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-kaivan-shah.webp'); ?>" alt="Dr. Kaivan Shah">
            <div><b>Dr. Kaivan Shah</b><span>Bhavna Super Speciality Eye Care</span></div>
          </div>
        </article>
        <article class="rv k3">
          <p>“Simple thing I like about Healthray: when an old patient comes back, I can see the previous chest reports, medicines and consultation notes while the patient is sitting in front of me. <mark>I don’t have to ask someone to find the old record first.</mark>”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2024/04/Dr.-Samir-Gami.webp'); ?>" alt="Dr. Samir Gami">
            <div><b>Dr. Samir Gami</b><span>Shivam Chest Clinic</span></div>
          </div>
        </article>
        <article class="rv k1">
          <p>“Most of my diabetic patients come for regular follow-ups. I need their previous reports, medicines and consultation history in front of me, not somewhere in an old file. With Healthray I can open the patient and continue from the previous visit. <mark>It is simple, but it makes everyday follow-up much easier.</mark>”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2024/04/Dr.-Pradip-Dalwadi.webp'); ?>" alt="Dr. Pradip Dalwadi">
            <div><b>Dr. Pradip Dalwadi</b><span>Pratham Endocrine &amp; Diabetes Centre</span></div>
          </div>
        </article>
        <article class="rv k2">
          <p>“The automated appointment reminders have been quite useful for our front desk. <mark>It reduces a lot of routine calling</mark> because patients are automatically reminded, and when they return I can instantly open their earlier eye records and understand the full history without starting from scratch.”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-Gautam-Beladiya.webp'); ?>" alt="Dr. Gautam Beladiya">
            <div><b>Dr. Gautam Beladiya</b><span>Beladiya Eye &amp; Dental Hospital</span></div>
          </div>
        </article>
        <article class="rv k3">
          <p>“After using Healthray for some time, I can clearly see the benefit in follow-up patients. Many of my patients need long-term care, so having all their old reports, images and consultation notes in one place is very helpful. <mark>I can quickly open the previous visit and compare it with the current condition</mark> without searching in different files.”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-Purav-Kansara.webp'); ?>" alt="Dr. Purav Kansara">
            <div><b>Dr. Purav Kansara</b><span>Limb Deformity Correction Center</span></div>
          </div>
        </article>
        <article class="rv k1">
          <p>“<mark>Morning OPD is usually very busy, so the system needs to be fast.</mark> I need old consultation, reports and medicines quickly while seeing the patient. Healthray shows all this on one screen, and even when lab or pharmacy updates come in, they reflect in the same patient record, so I don’t have to ask staff for old files. It saves my time during consultation.”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2026/10/Dr.-Dipak-Viradia.webp'); ?>" alt="Dr. Dipak Viradia">
            <div><b>Dr. Dipak Viradia</b><span>Chest Physician, Universal Hospital</span></div>
          </div>
        </article>
        <article class="rv k2">
          <p>“One thing I can say after using Healthray in our daily OPD work is that <mark>their support team has been genuinely helpful even after implementation</mark>. We’ve reached out a few times during OPD hours over call or email, and they respond properly and help us sort out the issue instead of just giving a generic reply.”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-Arpit-gajjar.webp'); ?>" alt="Dr. Arpit Gajjar">
            <div><b>Dr. Arpit Gajjar</b><span>Shubh Multispeciality Hospital</span></div>
          </div>
        </article>
        <article class="rv k3">
          <p>“No two specialties work exactly the same way. We needed a system that fits our urology workflow, not the other way around. <mark>Healthray was customized based on our requirements</mark>, so it works according to our process. Whenever we face any issue, the support team is helpful and resolves it within time.”</p>
          <div class="rv-who"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2024/04/Dr.-Ketan-Rupala.webp'); ?>" alt="Dr. Ketan Rupala">
            <div><b>Dr. Ketan Rupala</b><span>Rupala Kidney &amp; Prostate Hospital</span></div>
          </div>
        </article>
      </div>
      <div class="cs">
        <div class="cs-pic"><img src="<?php echo esc_url($hr_cl_up . '/2025/11/Lilavati-Clinic-Wellness.webp'); ?>" alt="The front desk of Lilavati Clinic &amp; Wellness, Ahmedabad" loading="lazy"></div>
        <div class="cs-body">
          <div class="cs-who"><small>Case study</small><b>Lilavati Clinic &amp; Wellness</b><span>Ahmedabad · 50+ OPD rooms</span></div>
          <div class="cs-n">
            <div><b>2 hrs</b><span>saved every day at the front desk</span></div>
            <div><b>21% → 13%</b><span>patients who did not come for their appointment</span></div>
            <div><b>10,400</b><span>old patient records moved</span></div>
            <div><b>11 days</b><span>to start, counted in working days</span></div>
          </div>
        </div>
      </div>
      <p class="cta-line">Want this for your clinic too? <a class="btn" href="#demo" data-hr-pop>Book a free demo</a></p>
    </div>
  </section>

  <section class="sec s8">
    <div class="in">
      <div class="sh-wrap">
        <h2 class="sh">First see it free. <em>Then decide.</em></h2>
        <p class="sh-sub">You pay nothing until you choose to buy.</p>
      </div>
      <div class="p3">
        <div>
          <span class="zero">₹0</span>
          <span class="zl">You pay nothing for these three:</span>
          <ul class="tk">
            <li>The demo on a video call</li>
            <li>Moving your old patient data</li>
            <li>Training you and your staff</li>
          </ul>
        </div>
        <div class="p3-after">
          <h3>After that</h3>
          <b class="big3">One price a year for your clinic.</b>
          <p>It depends on how many doctors and branches you have, and what you add, like pharmacy or lab. You get your price in writing within 24 hours of the demo. Healthray is paid software.</p>
          <a class="btn" href="#demo" data-hr-pop>Get the price for my clinic</a>
        </div>
      </div>
    </div>
  </section>

  <section class="sec tint">
    <div class="in">
      <div class="sh-wrap">
        <h2 class="sh">Questions doctors <em>ask us.</em></h2>
      </div>
      <div class="faq">
        <details name="clinic-faq" open>
          <summary>I have only one doctor. Is Healthray too big for my clinic?</summary>
          <p>No. Single-doctor clinics use it. You start with appointments, prescriptions and bills, and add pharmacy or lab later in the same software.</p>
        </details>
        <details name="clinic-faq">
          <summary>Is it made for my speciality?</summary>
          <p>Yes. Healthray has ready templates for 40+ specialities, like general physician, gynaec, child, bone, eye, ENT, skin and heart. Every doctor also makes their own prescription templates, so the medicines you write most are one click away.</p>
        </details>
        <details name="clinic-faq">
          <summary>My receptionist is not good with computers. Can they use it?</summary>
          <p>Yes. Each person learns only their own screen, and a receptionist usually learns it in a day or two. Training is free.</p>
        </details>
        <details name="clinic-faq">
          <summary>Do I have to type every prescription?</summary>
          <p>No. Repeat the last prescription in one click, pick from your own templates, or talk to your patient and let AI Scribe write the notes. You check and save.</p>
        </details>
        <details name="clinic-faq">
          <summary>Can I use it on my phone?</summary>
          <p>Yes. It works in Chrome on a computer, tablet or mobile. Nothing to install. Doctors also use the Healthray DR app on Android and iPhone to see appointments and a patient's history, and to write prescriptions.</p>
        </details>
        <details name="clinic-faq">
          <summary>Is it free?</summary>
          <p>No. Healthray is paid software. The demo, moving your old data and training your staff are free. Your price comes in writing within 24 hours of the demo.</p>
        </details>
        <details name="clinic-faq">
          <summary>Does it make ABHA IDs?</summary>
          <p>Yes. The ABHA ID is made or checked while the patient is being registered. Healthray is NHA approved and ABDM compliant.</p>
        </details>
      </div>
      <p class="notfor"><b>Who Healthray is not for:</b> students looking for a project, and anyone looking for free software or source code.</p>
    </div>
  </section>

  <section class="sec last">
    <div class="in">
      <div class="faces" aria-hidden="true"><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2024/04/Dr.-Milan-Modi.webp'); ?>" alt=""><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-kaivan-shah.webp'); ?>" alt=""><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-Gautam-Beladiya.webp'); ?>" alt=""><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2025/10/Dr.-Purav-Kansara.webp'); ?>" alt=""><img loading="lazy" decoding="async" src="<?php echo esc_url($hr_cl_up . '/2026/10/Dr.-Hinal-Parmar-Rainbow-Hospital.webp'); ?>" alt=""></div>
      <h2 class="sh">See Healthray set up for <em>a clinic like yours.</em></h2>
      <p class="sh-sub">A free video call. We register a patient, write a prescription and make a bill, the way your clinic works. No charge, and no need to decide on the call.</p>
      <div class="last-btns"><a class="btn" href="#demo" data-hr-pop>Book a free demo</a><a class="btn wa" href="<?php echo esc_url($hr_cl_wa); ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></div>
    </div>
  </section>

  <footer class="foot">
    <div class="in"><span class="fid"><b>© <?php echo esc_html(gmdate('Y')); ?> Healthray Technologies Pvt. Ltd.</b> · Since 2019</span><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>" rel="nofollow">Privacy policy</a></div>
  </footer>

</div>
<div class="mbar" id="mbar" aria-hidden="true"><a class="btn" href="#demo" tabindex="-1">Book a free demo</a></div>

<div class="hr-pop" id="hr-pop" role="dialog" aria-modal="true" aria-label="Book a free demo" hidden>
  <div class="hr-pop-in"><button type="button" class="hr-pop-x" aria-label="Close">&times;</button></div>
</div>

<div class="ai-sheet" id="ai-sheet" hidden role="dialog" aria-modal="true" aria-label="AI Scribe, 10-second demo">
  <div class="ais-win">
    <div class="ais-top"><img src="<?php echo esc_url($hr_cl_up . '/2024/02/Healthray-Logo.svg'); ?>" alt=""><b>Arogya Clinic</b><span class="ais-tab">Calendar</span><span class="ais-tab on">OPD</span><span class="ais-tab">Billing</span><button type="button" class="ais-x" id="ais-x" aria-label="Close">×</button></div>
    <div class="ais-pt"><span class="ais-av" id="ais-av">R</span>
      <div class="ais-who"><b id="ais-name">Mr. Ramesh Patel</b><small id="ais-meta">58 Y · M · 4th visit</small></div><span class="ais-tpl" id="ais-tpl">General physician template</span>
    </div>
    <div class="ais-body">
      <div class="ais-panel">
        <div class="ais-ph"><b>AI Scribe</b><small>Consultation · Pro</small></div>
        <div class="ais-modes"><span class="on">Consultation</span><span>Dictation</span></div>
        <div class="ais-rec" id="ais-rec"><i></i><span id="ais-rec-t">Recording · 00:00</span><span class="ais-wave" aria-hidden="true"><s></s><s></s><s></s><s></s><s></s><s></s><s></s><s></s><s></s><s></s><s></s><s></s></span></div>
        <div class="ais-lab">Heard in the room</div>
        <div class="ais-tr" id="ais-tr"></div>
        <div class="ais-status" id="ais-status" aria-live="polite"></div>
        <div class="ais-act"><span class="go" id="ais-go">Review &amp; save</span><span class="ghost">Discard</span></div>
        <div class="ais-bd"><span>Encrypted</span><span>Review before save</span><span>Multilingual</span></div>
      </div>
      <div class="ais-chart">
        <div class="ais-lab">Today's notes</div>
        <div class="ais-rows" id="ais-rows"></div>
        <div class="ais-extra" id="ais-extra"></div>
      </div>
    </div>
    <div class="ais-foot"><span>See AI Scribe on your own patients in the free demo.</span><a class="btn" href="#demo" id="ais-cta" data-hr-pop>Book a free demo</a></div>
  </div>
</div>