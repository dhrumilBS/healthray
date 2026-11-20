<?php
/**
 * Template Name: OPD Landing
 */

$hr_opd_up   = 'https://healthray.com/wp-content/uploads/2026/11';
$hr_opd_faqs = function_exists( 'healthray_opd_faq_items' ) ? healthray_opd_faq_items() : array();
?>
<main class="hx" id="opd-main">

<svg width="0" height="0" style="position:absolute" aria-hidden="true" focusable="false">
  <symbol id="i-wa" viewBox="0 0 24 24"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2Zm0 18.2a8.2 8.2 0 0 1-4.2-1.1l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2Zm4.5-6.1c-.2-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.5.1a6.7 6.7 0 0 1-3.3-2.9c-.3-.4.2-.4.7-1.4a.5.5 0 0 0 0-.4l-.8-1.8c-.2-.5-.4-.4-.6-.4h-.5a1 1 0 0 0-.7.3 2.9 2.9 0 0 0-.9 2.2 5 5 0 0 0 1 2.7 11.6 11.6 0 0 0 4.5 4c1.7.7 2.3.8 3.2.6a2.7 2.7 0 0 0 1.8-1.2 2.2 2.2 0 0 0 .1-1.2c0-.1-.2-.2-.4-.3Z"/></symbol>
</svg>

<section class="hero">
  <div class="wrap hero-grid">
    <div>
      <h1>OPD Management Software for Clinics and Hospitals</h1>
      <p class="sub">Token, appointments, the doctor's prescription, billing and reports in one software. Less crowd at reception, old records in one click, and the day's collection on your WhatsApp at night.</p>
      <p class="offer"><svg viewBox="0 0 24 24" aria-hidden="true"><path fill="currentColor" d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20Zm-1.2 14.2-4-4 1.4-1.4 2.6 2.6 5.6-5.6 1.4 1.4Z"/></svg><span>Free demo on a video call. We move your old data and train your staff for free.</span></p>

      <div class="hero-proof">
        <div class="faces">
          <div class="pile" aria-hidden="true">
            <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt="Dr. Dipak Viradia" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-kaivan-shah-150x150.webp" alt="Dr. Kaivan Shah" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Gautam-Beladiya-150x150.webp" alt="Dr. Gautam Beladiya" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt="Dr. Monil Parmar" loading="lazy" width="44" height="44"></span>
            <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="Dr. Arpit Gajjar" loading="lazy" width="44" height="44"></span>
          </div>
          <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
        </div>
        <div class="trust-row">
          <div class="seals">
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="NABH certified" loading="lazy" width="42" height="42"></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp" alt="ABDM compliant" loading="lazy" width="42" height="42"></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved" loading="lazy" width="42" height="42"></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001" loading="lazy" width="42" height="42"></span>
          </div>
          <p class="seal-text"><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
        </div>
        <div class="ratings" aria-label="Ratings">
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span>
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
          <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
        </div>
      </div>
    </div>
    <div class="formbox" id="demo">
      <h2>Book a Free Demo</h2>
      <p class="fsub">A short video call. We'll show the OPD set up for a place like yours.</p>
      <?php echo do_shortcode( '[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]' ); ?>
    </div>
  </div>
</section>

<section class="moved" aria-label="Hospitals that moved to Healthray">
  <p>Hospitals that moved to Healthray</p>
  <div class="track" aria-hidden="false">
    <ul>
      <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/vibrant-hospital.webp" alt="Vibrant Multispecialty Hospital, Vapi" loading="lazy"></li>
      <li><span class="beds">120+ beds</span><img src="https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp" alt="Shraddha Arogya Mandir, Vapi" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 1,100 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/shri-manakula-vinayak-medical-college.webp" alt="Sri Manakula Vinayagar Medical College, Puducherry" loading="lazy"></li>
      <li><span class="beds">140 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/parivar-super-speciality-hospital.webp" alt="Parivar Super Speciality Hospital, Madhya Pradesh" loading="lazy"></li>
      <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jeevan-rekha-hospital.webp" alt="Jeevan Rekha Hospital, West Bengal" loading="lazy"></li>
      <li><span class="beds">150 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/hzb-arogyam-multispeciality-hospital.webp" alt="HZB Arogyam Multispeciality Hospital, Hazaribagh" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 670 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/mamta-medical-college-hospital.webp" alt="Mamta Medical College and Hospital, Siwan" loading="lazy"></li>
      <li><span class="beds">130 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/budha-baba-multispeciality-hospital.webp" alt="Budha Baba Multispecialty Hospital, Cuttack" loading="lazy"></li>
      <li><span class="beds mc">Medical college · 1,000 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-medical-college.webp" alt="Heritage Medical College, Varanasi" loading="lazy"></li>
    </ul>
    <ul aria-hidden="true">
          <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/vibrant-hospital.webp" alt="Vibrant Hospital" loading="lazy"></li>
          <li><span class="beds">120+ beds</span><img src="https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp" alt="Shraddha Arogya Mandir" loading="lazy"></li>
          <li><span class="beds mc">Medical college · 1,100 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/shri-manakula-vinayak-medical-college.webp" alt="Shri Manakula Vinayak Medical College" loading="lazy"></li>
          <li><span class="beds">140 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/parivar-super-speciality-hospital.webp" alt="Parivar Super Speciality Hospital" loading="lazy"></li>
          <li><span class="beds">120 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jeevan-rekha-hospital.webp" alt="Jeevan Rekha Hospital" loading="lazy"></li>
          <li><span class="beds">150 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/hzb-arogyam-multispeciality-hospital.webp" alt="HZB Arogyam Multispeciality Hospital" loading="lazy"></li>
          <li><span class="beds mc">Medical college · 670 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/mamta-medical-college-hospital.webp" alt="Mamta Medical College Hospital" loading="lazy"></li>
          <li><span class="beds">130 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/budha-baba-multispeciality-hospital.webp" alt="Budha Baba Multispeciality Hospital" loading="lazy"></li>
          <li><span class="beds mc">Medical college · 1,000 beds</span><img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-medical-college.webp" alt="Heritage Medical College" loading="lazy"></li>
    </ul>
  </div>
</section>

<section class="tour" id="video">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow">1-minute video</span>
      <h2>See It Working in 1 Minute</h2>
      <p class="hx-lead">One patient: token, TV, AI Scribe, prescription and bill. The real software, not slides.</p>
    </div>
    <div class="frame">
      <div class="player paused" id="tour">
        <video playsinline muted preload="metadata" disablepictureinpicture poster="<?php echo esc_url( $hr_opd_up ); ?>/opd-tour-poster.jpg" width="1280" height="720" aria-label="Video of Healthray OPD software: one patient from token to prescription on WhatsApp"><source src="<?php echo esc_url( $hr_opd_up ); ?>/opd-tour.mp4" type="video/mp4">Your browser can't play this video. <a href="<?php echo esc_url( $hr_opd_up ); ?>/opd-tour.mp4">Download the video</a>.</video>
        <button class="vbtn play" type="button" aria-label="Play or pause"><svg class="a" viewBox="0 0 24 24"><path d="M6 4h4v16H6zM14 4h4v16h-4z"/></svg><svg class="b" viewBox="0 0 24 24"><path d="M7 4l13 8-13 8z"/></svg></button>
        <button class="vbtn full" type="button" aria-label="Full screen"><svg class="c" viewBox="0 0 24 24"><path d="M4 9V4h5v2H6v3zm11-5h5v5h-2V6h-3zM4 15h2v3h3v2H4zm14 0h2v5h-5v-2h3z"/></svg><svg class="d" viewBox="0 0 24 24"><path d="M9 4v5H4V7h3V4zm6 0h2v3h3v2h-5zM4 15h5v5H7v-3H4zm11 0h5v2h-3v3h-2z"/></svg></button>
      </div>
    </div>
    <p class="after">Want this in your OPD? <a class="btn btn-main" href="#demo">Book a free demo</a></p>
  </div>
</section>

<section class="sec" id="day">
  <div class="wrap">
    <div class="sec-head">
      <h2>Does This Happen in Your OPD?</h2>
      <p class="hx-lead">Five things that happen in every OPD, and what changes with Healthray.</p>
    </div>
    <table class="day">
      <thead><tr><th scope="col">In your OPD today</th><th scope="col" class="fixh">With Healthray</th></tr></thead>
      <tbody>
        <tr>
          <td class="now"><b>Morning rush</b><q>25 patients at the gate. Names in a register. Two patients fight over who came first.</q></td>
          <td class="fix">Every patient gets a token. The TV shows whose number is on, and how long the wait is.</td>
        </tr>
        <tr>
          <td class="now"><b>Phone calls</b><q>The phone rings all morning. 'Is doctor sir coming today? What time?'</q></td>
          <td class="fix">Patients book from your link, see the doctor's timing, and get a reminder on WhatsApp.</td>
        </tr>
        <tr>
          <td class="now"><b>Old reports</b><q>'Where is the old report?' The patient opens a bag of papers. Three minutes gone.</q></td>
          <td class="fix">The doctor opens the old file in one click: old medicines, reports, allergies.</td>
        </tr>
        <tr>
          <td class="now"><b>Follow-ups</b><q>'Come back after one month.' She never came back.</q></td>
          <td class="fix">A WhatsApp reminder goes before the next visit. Patients who missed it show in one list.</td>
        </tr>
        <tr>
          <td class="now"><b>Closing time</b><q>'Sir, I will count and tell you.' Cash is ₹1,800 short.</q></td>
          <td class="fix">The day's collection comes on your WhatsApp: doctor-wise, cash, UPI and card.</td>
        </tr>
      </tbody>
    </table>
  </div>
</section>

<section class="sec soft" id="features">
  <div class="wrap">
    <div class="sec-head">
      <span class="eyebrow">What you get</span>
      <h2>Everything Your OPD Needs, in One Software</h2>
    </div>

    <div class="feat">
      <div>
        <span class="eyebrow">Token and waiting-area TV</span>
        <h3>Patients Get a Token and See Their Turn on the TV</h3>
        <p>Type the mobile number, the old patient comes up, the token prints. Any TV with internet shows each doctor's token, room number and waiting time. Nobody asks reception "when is my number?"</p>
        <ul class="ticks">
          <li>The same patient is never added twice</li>
          <li>One TV per floor, or one per doctor</li>
          <li>Reception sees who has waited, and for how long</li>
        </ul>
      </div>
      <figure class="shot fig"><img src="<?php echo esc_url( $hr_opd_up ); ?>/token-tv.webp" alt="The waiting-area TV in floor mode showing three doctors' rooms with tokens and waiting times, the printed token slip number 7 in front, and a wall clock at 5:13" loading="lazy" width="1200" height="800"></figure>
    </div>

    <div class="feat flip">
      <div>
        <span class="eyebrow">Doctor's screen</span>
        <h3>Old Reports and Today's Prescription on One Screen</h3>
        <p>The doctor sees past visits, reports and medicines, and writes today's prescription from their own templates. It prints on your letterhead in Hindi, Gujarati or 8 more languages, and goes to the patient's WhatsApp.</p>
        <ul class="ticks">
          <li>Last prescription repeated in one click</li>
          <li>Allergies pop up in red when the file opens</li>
          <li>AI Scribe writes the notes. The doctor checks and saves.</li>
        </ul>
        <p class="said"><img class="face" src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt="" loading="lazy" width="48" height="48"><span><q>Morning OPD is usually very busy, so the system needs to be fast. I need old consultation, reports and medicines quickly while seeing the patient. Healthray shows all this on one screen.</q><cite><b>Dr. Dipak Viradia</b>, Pulmonologist, Universal Hospital, Surat</cite></span></p>
      </div>
      <figure class="shot fig"><img src="<?php echo esc_url( $hr_opd_up ); ?>/doctor-screen.webp" alt="Doctor's OPD screen for a returning patient with the AI Scribe panel recording the consultation: the words heard in the room, the draft complaints, diagnosis and advice, and today's prescription repeated from the last visit" loading="lazy" width="1200" height="800"></figure>
    </div>

    <div class="feat">
      <div>
        <span class="eyebrow">Appointments and reminders</span>
        <h3>Patients Book Online and Get Reminders on WhatsApp</h3>
        <p>Set each doctor's timing once. Online bookings and walk-in patients come on the same list, and a reminder goes before every visit.</p>
        <ul class="ticks">
          <li>Mark the doctor's leave in advance. Nobody can book those days.</li>
          <li>Follow-up reminders, and a list of patients who missed one</li>
          <li>Video consultation in the same software</li>
        </ul>
      </div>
      <figure class="shot fig"><img src="<?php echo esc_url( $hr_opd_up ); ?>/appointments.webp" alt="Tomorrow's appointment list at reception with online and walk-in patients on one list, and the patient's phone showing the WhatsApp reminder sent the day before with the same doctor and time" loading="lazy" width="1200" height="790"></figure>
    </div>

    <div class="feat flip">
      <div>
        <span class="eyebrow">OPD billing</span>
        <h3>One Bill for the Doctor's Fee and the Tests</h3>
        <p>The right consultation fee is on the bill as soon as the appointment is booked. Add tests, take cash, UPI or card, and send the bill on WhatsApp.</p>
        <ul class="ticks">
          <li>Payment link for patients who pay later</li>
          <li>Advance deposits, refunds and OPD packages</li>
          <li>Tests the doctor ordered but nobody billed show in a report</li>
        </ul>
      </div>
      <figure class="shot fig"><img src="<?php echo esc_url( $hr_opd_up ); ?>/billing.webp" alt="OPD bill screen with the consultation fee added at booking and an ECG, paid by UPI, and the printed bill on the clinic letterhead stamped PAID" loading="lazy" width="1200" height="760"></figure>
    </div>

    <div class="feat">
      <div>
        <span class="eyebrow">Reports for the owner</span>
        <h3>Today's Patients and Collection on Your Phone</h3>
        <p>The day's collection comes on your WhatsApp at night. The full report opens on your phone any time, from anywhere.</p>
        <ul class="ticks">
          <li>New and follow-up patients, doctor-wise</li>
          <li>How long patients waited for the doctor</li>
          <li>Every discount and refund in one report</li>
        </ul>
      </div>
      <figure class="shot fig"><img src="<?php echo esc_url( $hr_opd_up ); ?>/owner-reports.webp" alt="The day's dashboard with patients, collection and average wait, and the owner's phone showing the 9:30 pm WhatsApp message with the same numbers, doctor-wise and by cash, UPI and card" loading="lazy" width="1200" height="790"></figure>
    </div>

    <div class="also">
      <h3>Also Included</h3>
      <p class="hx-lead">Smaller things that save a call or a form every day.</p>
      <ul class="also-grid">
        <li><i></i><div><b>More doctors and branches</b><span>All in one software. Each doctor has their own login, templates and letterhead.</span></div></li>
        <li><i></i><div><b>ABHA ID at registration</b><span>Made or checked while the patient is being registered. NHA approved, ABDM compliant.</span></div></li>
        <li><i></i><div><b>Lab, pharmacy and IPD</b><span>Join them when you need them. Tests and medicines from the doctor's screen reach the lab, pharmacy and bill.</span></div></li>
        <li><i></i><div><b>Ready for your speciality</b><span>Eye, gynaec, child, physio and psychiatry doctors get screens made for their work.</span></div></li>
        <li><i></i><div><b>Nurse station</b><span>BP, sugar and weight noted before the patient reaches the doctor.</span></div></li>
        <li><i></i><div><b>Certificates in one click</b><span>Medical and fitness certificates printed on your letterhead.</span></div></li>
        <li><i></i><div><b>Computer, tablet or mobile</b><span>Runs in the browser. Nothing to install, nothing special to buy.</span></div></li>
        <li><i></i><div><b>Old patient data moved free</b><span>From your register, Excel or old software, before you start.</span></div></li>
      </ul>
    </div>
  </div>
</section>

<section class="ask" id="busy">
  <div class="wrap ask-grid">
    <div>
      <span class="eyebrow">Every hospital owner asks this</span>
      <h2>“Will it slow down when 300 patients come in one morning?”</h2>
      <p>Healthray runs one-doctor OPDs and hospitals with 50 or more OPD rooms. Here is what keeps it running on a busy day.</p>
      <p class="certs-h">Certified and compliant</p>
    <div class="certs">
        <figure><img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="NABH EHR Certification" loading="lazy" width="56" height="56"><figcaption>NABH certified</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/abdm-healthray-150x150.webp" alt="Ayushman Bharat Digital Mission (ABDM) Compliance" loading="lazy" width="56" height="56"><figcaption>ABDM compliant</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="National Health Authority (NHA) Approval" loading="lazy" width="56" height="56"><figcaption>NHA approved</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/abha-healthray-150x150.webp" alt="Ayushman Bharat Health Account (ABHA) ID" loading="lazy" width="56" height="56"><figcaption>Makes ABHA IDs</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001 Information Security Certification" loading="lazy" width="56" height="56"><figcaption>ISO 27001 data security</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp" alt="HIPAA Compliance" loading="lazy" width="56" height="56"><figcaption>HIPAA-aligned</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="HL7 Healthcare Data Integration" loading="lazy" width="56" height="56"><figcaption>HL7, connects lab machines</figcaption></figure>
        <figure><img src="https://healthray.com/wp-content/uploads/2025/07/Certin.webp" alt="CERT-In Security Audit" loading="lazy" width="56" height="56"><figcaption>CERT-In security audit</figcaption></figure>
    </div>
    </div>
    <dl>
      <dt>Made for a busy morning OPD</dt>
      <dd>Big hospitals run their morning OPD on it every day. Lab and pharmacy updates come into the same patient file while the doctor is seeing patients.</dd>
      <dt>If the internet stops</dt>
      <dd>Connect a mobile hotspot and keep working. A hospital can also run Healthray on its own server inside the hospital.</dd>
      <dt>Your patients' data is safe</dt>
      <dd>ISO 27001 certified for data security. NABH certified, ABDM compliant and NHA approved.</dd>
      <dt>Support during OPD hours, not after</dt>
      <dd class="who"><img class="face" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="" loading="lazy" width="48" height="48"><span><q>We've reached out a few times during OPD hours over call or email, and they respond properly and help us sort out the issue instead of just giving a generic reply.</q><cite><b>Dr. Arpit Gajjar</b>, Shubh Multispeciality Hospital</cite></span></dd>
    </dl>
  </div>
</section>

<section class="sec" id="who">
  <div class="wrap">
    <div class="sec-head">
      <h2>For a Clinic or a Hospital? Both.</h2>
      <p class="hx-lead">The same OPD software, set up two ways. Pick yours and the demo form fills itself.</p>
    </div>
    <div class="two">
      <div>
        <h3>OPD software for a clinic</h3>
        <p class="for">One doctor, or 2 to 5 doctors under one roof</p>
        <ul class="ticks">
          <li>Registration, token and appointments at one counter</li>
          <li>Prescription in your patient's language, on WhatsApp</li>
          <li>One bill, UPI and cash, and the night report on WhatsApp</li>
          <li>Your receptionist learns it in a day or two</li>
          <li>Add pharmacy or lab later, in the same software</li>
        </ul>
        <a class="btn btn-main" href="#demo" data-pick="Clinic with 2 to 5 doctors">Book a demo for a clinic</a>
      </div>
      <div>
        <h3>OPD software for a hospital</h3>
        <p class="for">OPD with IPD, lab and pharmacy, or many OPD rooms</p>
        <ul class="ticks">
          <li>Many counters, and a token TV on every floor</li>
          <li>ABHA ID at registration</li>
          <li>OPD patient admitted without typing details again</li>
          <li>Tests and medicines reach the lab, pharmacy and bill</li>
          <li>Runs on the cloud or on your own server</li>
        </ul>
        <a class="btn btn-main" href="#demo" data-pick="Hospital with OPD and IPD">Book a demo for a hospital</a>
      </div>
    </div>
  </div>
</section>

<section class="sec soft" id="start">
  <div class="wrap">
    <div class="sec-head"><h2>Start in 3 Steps</h2><p class="hx-lead">Even a 200-bed hospital usually starts using it in one to three weeks. A clinic starts faster.</p></div>
    <ol class="steps">
      <li><span class="n">1</span><h3>Book a free demo</h3><p>Fill the short form. On a video call we show the OPD set up for a place like yours: a counter, a doctor and a bill.</p></li>
      <li><span class="n">2</span><h3>We move your data and train your staff, free</h3><p>Patient history from your register, Excel or old software. Reception, doctors and billing each learn their own screen. For hospitals, our team comes on-site.</p></li>
      <li><span class="n">3</span><h3>Start your OPD on Healthray</h3><p>OPD and registration go live first. Our team stays on call, chat and screen share, in English, Hindi or Gujarati.</p></li>
    </ol>
  </div>
</section>

<section class="sec" id="reviews">
  <div class="wrap">
    <div class="rev-head">
      <div class="sec-head">
        <h2>What Doctors Say</h2>
        <p class="hx-lead">Real doctors using Healthray in their OPD every day. All of these are on our Reviews page.</p>
      </div>
    </div>
    <div class="rail" id="rail" aria-label="Doctor reviews" tabindex="0">
      <figure class="rev"><blockquote>Morning OPD is usually very busy, so the system needs to be fast. I need old consultation, reports and medicines quickly while seeing the patient. Healthray shows all this on one screen, so I don't have to ask staff for old files.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt="Dr. Dipak Viradia" loading="lazy" width="48" height="48"><span><b>Dr. Dipak Viradia</b><small>Chest Physician, Universal Hospital, Surat</small></span></figcaption></figure>
      <figure class="rev"><blockquote>At the start, we were unsure how reception, doctors and billing would adjust to it. The Healthray team came to the hospital, trained the staff on-site and showed each workflow in a simple way. Now the staff uses it comfortably in their own roles.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt="Dr. Monil Parmar" loading="lazy" width="48" height="48"><span><b>Dr. Monil Parmar</b><small>Universal Superspeciality Hospital</small></span></figcaption></figure>
      <figure class="rev"><blockquote>The automated appointment reminders have been quite useful for our front desk. It reduces a lot of routine calling because patients are automatically reminded, and when they return I can instantly open their earlier records.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Gautam-Beladiya-150x150.webp" alt="Dr. Gautam Beladiya" loading="lazy" width="48" height="48"><span><b>Dr. Gautam Beladiya</b><small>Beladiya Eye &amp; Dental Hospital</small></span></figcaption></figure>
      <figure class="rev"><blockquote>Their support team has been genuinely helpful even after implementation. We've reached out a few times during OPD hours over call or email, and they respond properly and help us sort out the issue instead of just giving a generic reply.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="Dr. Arpit Gajjar" loading="lazy" width="48" height="48"><span><b>Dr. Arpit Gajjar</b><small>Shubh Multispeciality Hospital</small></span></figcaption></figure>
      <figure class="rev"><blockquote>Most of our patients come for follow-ups, so having their previous consultation history, reports and prescriptions in one place is very helpful. It saves time during OPD and makes it easier to continue treatment without searching for old records.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-kaivan-shah-150x150.webp" alt="Dr. Kaivan Shah" loading="lazy" width="48" height="48"><span><b>Dr. Kaivan Shah</b><small>Bhavna Super Speciality Eye Care</small></span></figcaption></figure>
      <figure class="rev"><blockquote>If I face any issue, I can call them and someone responds who understands the problem, not just a standard answer. For a clinic like mine, that kind of support really matters because we don't have an IT person here all the time.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Milan-Modi-150x150.webp" alt="Dr. Milan Modi" loading="lazy" width="48" height="48"><span><b>Dr. Milan Modi</b><small>Modi Allergy &amp; Chest Clinic</small></span></figcaption></figure>
      <figure class="rev"><blockquote>Most of my diabetic patients come for regular follow-ups. I need their previous reports, medicines and consultation history in front of me, not somewhere in an old file. With Healthray I can open the patient and continue from the previous visit.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Pradip-Dalwadi-150x150.webp" alt="Dr. Pradip Dalwadi" loading="lazy" width="48" height="48"><span><b>Dr. Pradip Dalwadi</b><small>Pratham Endocrine &amp; Diabetes Centre</small></span></figcaption></figure>
      <figure class="rev"><blockquote>Simple thing I like about Healthray: when an old patient comes back, I can see the previous chest reports, medicines and consultation notes while the patient is sitting in front of me. I don't have to ask someone to find the old record first.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Samir-Gami-150x150.webp" alt="Dr. Samir Gami" loading="lazy" width="48" height="48"><span><b>Dr. Samir Gami</b><small>Shivam Chest Clinic</small></span></figcaption></figure>
      <figure class="rev"><blockquote>We were using different software for different departments earlier, which made coordination difficult. After switching to Healthray, we moved everything into one system. Now patient records, billing, pharmacy and inventory are all connected.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt="Dr. Bhaumik Rathore" loading="lazy" width="48" height="48"><span><b>Dr. Bhaumik Rathore</b><small>Vibrant Multispecialty Hospital, Vapi</small></span></figcaption></figure>
      <figure class="rev"><blockquote>Many of my patients need long-term care, so having all their old reports, images and consultation notes in one place is very helpful. I can quickly open the previous visit and compare it with the current condition without searching in different files.</blockquote><figcaption><img class="face" src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Purav-Kansara-150x150.webp" alt="Dr. Purav Kansara" loading="lazy" width="48" height="48"><span><b>Dr. Purav Kansara</b><small>Limb Deformity Correction Center</small></span></figcaption></figure>
    </div>
    <div class="rev-foot">
      <div class="ratings">
        <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span>
        <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
        <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
        <a href="https://healthray.com/reviews/">Read all reviews</a>
      </div>
      <div class="dots" id="dots" aria-label="Go to a set of reviews"></div>
    </div>
  </div>
</section>

<section class="sec soft" id="faq">
  <div class="wrap">
    <div class="sec-head"><h2>Questions Before You Buy</h2></div>
    <div class="faq">
      <?php $hr_faq_i = 0; foreach ( $hr_opd_faqs as $hr_faq ) : ?>
      <details name="hx-faq"<?php echo 0 === $hr_faq_i++ ? ' open' : ''; ?>><summary><?php echo esc_html( $hr_faq['q'] ); ?></summary><p><?php echo ! empty( $hr_faq['html'] ) ? wp_kses_post( $hr_faq['html'] ) : esc_html( $hr_faq['a'] ); ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="final">
  <div class="wrap">
    <div class="doc-row" aria-hidden="true">
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Dipak-Viradia-150x150.webp" alt="Dr. Dipak Viradia" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Milan-Modi-150x150.webp" alt="Dr. Milan Modi" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Pradip-Dalwadi-150x150.webp" alt="Dr. Pradip Dalwadi" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Samir-Gami-150x150.webp" alt="Dr. Samir Gami" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="Dr. Arpit Gajjar" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp" alt="Dr. Bhaumik Rathore" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Gautam-Beladiya-150x150.webp" alt="Dr. Gautam Beladiya" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt="Dr. Monil Parmar" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Purav-Kansara-150x150.webp" alt="Dr. Purav Kansara" loading="lazy" width="56" height="56"></span>
      <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-kaivan-shah-150x150.webp" alt="Dr. Kaivan Shah" loading="lazy" width="56" height="56"></span>
    </div>
    <h2>See It Working for an OPD Like Yours</h2>
    <p>A short video call. We set up a counter, a doctor and a bill the way your OPD runs. No charge. No need to decide on the call.</p>
    <div class="hx-row">
      <a class="btn btn-main" href="#demo">Book a free demo</a>
    </div>
  </div>
</section>

</main>