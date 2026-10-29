<?php

/**
 * Template Name: Healthray Kenya PPC - Hospital Software
 * Kenya Google Ads page; CSS/JS/popup wiring in functions.php section 12 (key "ke", same as "ng").
 */

defined('ABSPATH') || exit;

// Lead form shown in the hero card and in the popup opened by every .hr-cta-btn (same CF7 form as Nigeria and India).
$ppc_form = '[contact-form-7 id="11eec7d" title="PPC Form"]';
add_filter('hr_ppc_popup_args', function () use ($ppc_form) {
  return array(
    'form'  => $ppc_form,
    'title' => 'Book a free demo',
    'text'  => "A free 30-minute demo, set up for your hospital's size and departments.",
  );
});

// Settings for js/ppc-ke.js: Ads conversion ID as AW-XXX/label. The Kenya page is form only (no WhatsApp).
$ppc_ke = array(
  'adsConversion' => '',
);
wp_add_inline_script('ppc-ke', 'var hrPpcKe = ' . wp_json_encode($ppc_ke) . ';', 'before');

// Icons (Google Material Symbols, only the 7 icons used). Better: enqueue this in functions.php with the "ke" CSS.
wp_enqueue_style('ppc-ke-icons', 'https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@24,400,0,0&icon_names=bed,biotech,how_to_reg,info,medication,stethoscope,swap_horiz&display=block', array(), null);

// REPLACE before ads go live: DHA certificate number (shown in the hero and the FAQ).
$ke_cert_no = 'CERT-XXXX-XXXX';

// Sample photos for internal review (theme folder images/ppc-ke/). REPLACE with real, consented Kenyan clients.
$ke_img = trailingslashit(get_theme_file_uri('images/ppc-ke'));

// Logos. These load from the official sites for now; upload them to the Media Library and swap the URLs before launch.
$ke_logo = array(
  'dha'       => 'https://www.dha.go.ke/DHA-LOGO-FA-02.png',   // dark text, for light backgrounds
  'dha_white' => 'https://www.dha.go.ke/DHA-LOGO-FA-04.png',   // white text, for the hero
  'sha'       => 'https://www.sha.go.ke/wp-content/uploads/2025/02/SHA-Logo-Favicon-Ellipsed-150x150.png',
  'mpesa'     => 'https://upload.wikimedia.org/wikipedia/commons/1/15/M-PESA_LOGO-01.svg',
  'flag'      => 'https://upload.wikimedia.org/wikipedia/commons/4/49/Flag_of_Kenya.svg',
);
?>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="i-check" viewBox="0 0 24 24">
    <path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
  </symbol>
</svg>

<header class="top">
  <div class="wrap">
    <?php // Logo not linked on purpose: no exits from an ads page. ?>
    <div class="brand"><img src="https://healthray.com/wp-content/uploads/2024/02/Healthray-Logo.svg" alt="Healthray"
        width="150" height="34"><span class="fb">Healthray</span></div>
    <div class="top-right">
      <a class="btn btn-primary btn-sm hr-cta-btn" href="#demo">Book a free demo</a>
    </div>
  </div>
</header>

<main>
<!-- Hero -->
<section class="hero">
  <div class="wrap hero-grid">
    <div class="hero-copy">
      <p class="cert-line">
        <img class="flag-img" src="<?php echo esc_url($ke_logo['flag']); ?>" alt="Kenya" width="24" height="16" decoding="async" data-fallback-remove>
        <span class="badge"><img class="dha-mini" src="<?php echo esc_url($ke_logo['dha_white']); ?>" alt="DHA" height="18" decoding="async" data-fallback-remove>Certified HMIS</span>
        <span class="cert-no">Cert. no. <span class="ph"><?php echo esc_html($ke_cert_no); ?></span></span>
        <a class="reg-link" href="https://certification.dha.go.ke/registry" target="_blank" rel="noopener nofollow">Check on the DHA registry</a>
      </p>
      <h1 id="kwH1">Hospital management software that gets your <span>SHA claims through</span></h1>
      <p class="hero-sub" id="kwSub">Submit SHA claims, take M‑Pesa payments and send eTIMS invoices from one system. We set it up and train your staff.</p>
    </div>

    <div class="form-card" id="demo">
        <h2>Book a free demo</h2>
        <p class="fc-sub">A free 30-minute demo, set up for your hospital's size and departments.</p>
        <?php echo do_shortcode($ppc_form); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
      </div>

      <div class="hero-proof">
      <ul class="hero-chips">
        <li><svg width="16" height="16"><use href="#i-check"/></svg>DHA certified</li>
        <li><svg width="16" height="16"><use href="#i-check"/></svg>SHA claims built in</li>
        <li><svg width="16" height="16"><use href="#i-check"/></svg>Data stored in Kenya</li>
        <li><svg width="16" height="16"><use href="#i-check"/></svg>M‑Pesa and eTIMS</li>
      </ul>
      <div class="faces">
        <div class="pile">
          <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/rhoda-k.webp" alt="rhoda-k" width="44" height="44" decoding="async" data-fallback="GW"></span>
          <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/dr-ruby-oswere.webp" alt="dr-ruby-oswere" width="44" height="44" decoding="async" data-fallback="JO"></span>
          <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/cosmas-k.webp" alt="cosmas-k" width="44" height="44" decoding="async" data-fallback="AC"></span>
          <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/chris-njuguna.webp" alt="chris-njuguna" width="44" height="44" decoding="async" data-fallback="HR"></span>
        </div>
        <p><b>Used by 2,500+ hospitals and clinics</b>, with support from setup to go-live</p>
      </div>
      <div class="trust-row">
        <div class="seals">
          <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001" width="42" height="42"><span class="fb">ISO 27001</span></span>
          <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="HL7" width="42" height="42"><span class="fb">HL7</span></span>
          <span class="seal"><img src="https://healthray.com/wp-content/uploads/2025/07/FHIR.webp" alt="FHIR" width="42" height="42"><span class="fb">FHIR</span></span>
        </div>
        <p class="world"><b>ISO 27001 certified data security</b><br>Your patient data is kept safe and private</p>
      </div>
      <div class="proof-row" aria-label="Ratings">
        <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span>
        <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
        <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
      </div>
    </div>
  </div>
</section>

<!-- CLIENT LOGOS: Nigerian logos for now. REPLACE with Kenyan clients (with permission) and change the label back to "Facilities in Kenya already using Healthray" -->
<section class="logos" aria-label="Hospitals using Healthray">
  <div class="wrap">
    <p>Hospitals and clinics already using Healthray</p>
    <div class="marquee">
      <div class="marquee-track">
        <ul>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/The-Cardiac-Clinic.webp" alt="The Cardiac Clinic" loading="lazy" decoding="async"><span class="fb">The Cardiac Clinic</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/St.-Niklaus-Medical-Center.webp" alt="St. Niklaus Medical Center, Lagos" loading="lazy" decoding="async"><span class="fb">St. Niklaus Medical Center</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/Redbridge-Healthcare.webp" alt="Redbridge Healthcare, Lagos" loading="lazy" decoding="async"><span class="fb">Redbridge Healthcare</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/Riverfront-Medical-Hub.webp" alt="Riverfront Medical Hub, Port Harcourt" loading="lazy" decoding="async"><span class="fb">Riverfront Medical Hub</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/Havanacare-Specialist-Clinic.webp" alt="Havanacare Specialist Clinic, Kano" loading="lazy" decoding="async"><span class="fb">Havanacare Specialist Clinic</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/04/Biosystems-Healthcare.webp" alt="Biosystems Healthcare" loading="lazy" decoding="async"><span class="fb">Biosystems Healthcare</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/04/Royal-Hands-Healthcare.webp" alt="Royal Hands Healthcare" loading="lazy" decoding="async"><span class="fb">Royal Hands Healthcare</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/EuroCure-MultiCare-Hospital.webp" alt="EuroCure MultiCare Hospital" loading="lazy" decoding="async"><span class="fb">EuroCure MultiCare Hospital</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/Libken-Medical-Centre-Emblem.webp" alt="Libken Medical Centre Limited" loading="lazy" decoding="async"><span class="fb">Libken Medical Centre Limited</span></li>
        </ul>
        <ul aria-hidden="true">
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/The-Cardiac-Clinic.webp" alt="The Cardiac Clinic" loading="lazy" decoding="async"><span class="fb">The Cardiac Clinic</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/St.-Niklaus-Medical-Center.webp" alt="" loading="lazy" decoding="async"><span class="fb">St. Niklaus Medical Center</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/Redbridge-Healthcare.webp" alt="" loading="lazy" decoding="async"><span class="fb">Redbridge Healthcare</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/Riverfront-Medical-Hub.webp" alt="" loading="lazy" decoding="async"><span class="fb">Riverfront Medical Hub</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/Havanacare-Specialist-Clinic.webp" alt="" loading="lazy" decoding="async"><span class="fb">Havanacare Specialist Clinic</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/04/Biosystems-Healthcare.webp" alt="" loading="lazy" decoding="async"><span class="fb">Biosystems Healthcare</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/04/Royal-Hands-Healthcare.webp" alt="" loading="lazy" decoding="async"><span class="fb">Royal Hands Healthcare</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/02/EuroCure-MultiCare-Hospital.webp" alt="" loading="lazy" decoding="async"><span class="fb">EuroCure MultiCare Hospital</span></li>
          <li><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/Libken-Medical-Centre-Emblem.webp" alt="Libken Medical Centre Limited" loading="lazy" decoding="async"><span class="fb">Libken Medical Centre Limited</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- SHA claims -->
<section class="section" aria-labelledby="shaTitle">
  <div class="wrap sha">
    <div>
      <p class="eyebrow">SHA claims</p>
      <h2 class="h2" id="shaTitle">Send SHA claims from the patient's bill, and see what SHA owes you</h2>
      <p class="lead">Healthray is connected to SHA. Your staff check cover, request pre&#8209;authorisation and send claims without opening the Provider Portal. Nothing is typed twice.</p>
      <p class="sha-portal"><span class="msr" aria-hidden="true">swap_horiz</span><span><b>Using the SHA Provider Portal today?</b> Your staff do the same work in Healthray, with the bill and patient records in one place.</span></p>
      <ol class="vsteps">
        <li><span class="n">1</span><div><small>Reception</small><h3>Check the patient's SHA cover</h3><p>Look up the patient's cover when they register, before they see the doctor.</p></div></li>
        <li><span class="n">2</span><div><small>Pre&#8209;authorisation</small><h3>Request it from the same screen</h3><p>For services that need it, send the request and see when it comes back approved.</p></div></li>
        <li><span class="n">3</span><div><small>Treatment</small><h3>Every service goes on the bill</h3><p>Consultation, lab, imaging, drugs and ward days are added as they happen, with your SHA rates saved.</p></div></li>
        <li><span class="n">4</span><div><small>Claim</small><h3>Follow up every claim</h3><p>See which claims are pending, paid or rejected. If SHA rejects one, you see the reason, fix it and send it again.</p></div></li>
      </ol>
      <div class="sha-cta"><p><b>Want to see a claim sent live?</b> We'll show you on the demo.</p><a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a></div>
    </div>
    <div class="claim-card" role="img" aria-label="SHA claims screen with demo data">
      <div aria-hidden="true">
        <div class="claim-head"><h4>SHA claims · October</h4><span class="chip">All funds</span></div>
        <div class="claim-sum">
          <div><small>Pending</small><b>KES 1.84M</b></div>
          <div><small>Paid this month</small><b>KES 1.12M</b></div>
          <div><small>Rejected</small><b style="color:var(--red-ink)">1</b></div>
        </div>
        <div class="tbl-wrap">
          <table class="tbl">
            <thead><tr><th>Patient</th><th class="m-hide">Fund</th><th class="xs-hide">Pre&#8209;auth</th><th>KES</th><th>Status</th></tr></thead>
            <tbody>
              <tr><td><b>Wanjiru Kamau</b></td><td class="m-hide"><span class="fund">SHIF</span></td><td class="xs-hide">Approved</td><td>18,500</td><td><span class="st blue">Submitted</span></td></tr>
              <tr><td><b>Brian Otieno</b></td><td class="m-hide"><span class="fund">PHC</span></td><td class="xs-hide">Not needed</td><td>2,350</td><td><span class="st green">Paid</span></td></tr>
              <tr><td><b>Fatuma Hassan</b></td><td class="m-hide"><span class="fund">SHIF</span></td><td class="xs-hide">Approved</td><td>42,800</td><td><span class="st amber">Ready to send</span></td></tr>
              <tr><td><b>Kevin Kiprotich</b></td><td class="m-hide"><span class="fund">SHIF</span></td><td class="xs-hide">Waiting</td><td>9,600</td><td><span class="st grey">On hold</span></td></tr>
              <tr><td><b>Mercy Achieng</b></td><td class="m-hide"><span class="fund">ECCIF</span></td><td class="xs-hide">Approved</td><td>65,000</td><td><span class="st red">Rejected</span><span class="why">Missing discharge summary</span><span class="fixlink">Fix and resend &rarr;</span></td></tr>
            </tbody>
          </table>
        </div>
        <div class="claim-note"><span class="msr sm" aria-hidden="true">info</span>1 claim is on hold until its pre&#8209;authorisation is approved.</div>
      </div>
    </div>
  </div>
</section>

<!-- Where money is lost -->
<section class="section tint" aria-labelledby="leakTitle">
  <div class="wrap">
    <div class="sec-head center">
      <h2 class="h2" id="leakTitle">Most facilities lose money in the same six places</h2>
      <p class="lead">Follow one patient through your facility. See where money is lost, and how Healthray stops it.</p>
    </div>
    <ol class="journey">
      <li class="stop">
        <span class="pin" aria-hidden="true"><span class="msr">how_to_reg</span></span>
        <h3>Reception</h3>
        <p class="leak"><b>The problem</b>Patients see the doctor before paying, or before their SHA cover is checked.</p>
        <p class="fix"><b>With Healthray</b>Payment or SHA cover is confirmed when the patient registers.</p>
      </li>
      <li class="stop">
        <span class="pin" aria-hidden="true"><span class="msr">stethoscope</span></span>
        <h3>Consultation</h3>
        <p class="leak"><b>The problem</b>Tests and drugs written on paper never reach the bill.</p>
        <p class="fix"><b>With Healthray</b>Whatever the doctor or clinical officer orders goes straight to the bill.</p>
      </li>
      <li class="stop">
        <span class="pin" aria-hidden="true"><span class="msr">biotech</span></span>
        <h3>Lab</h3>
        <p class="leak"><b>The problem</b>Tests are done with no record of who ordered them or who paid.</p>
        <p class="fix"><b>With Healthray</b>Every test is linked to a patient and a bill.</p>
      </li>
      <li class="stop">
        <span class="pin" aria-hidden="true"><span class="msr">medication</span></span>
        <h3>Pharmacy</h3>
        <p class="leak"><b>The problem</b>Drugs are given out without a bill. Stock expires on the shelf.</p>
        <p class="fix"><b>With Healthray</b>Every drug given is billed. You get a warning before stock expires.</p>
      </li>
      <li class="stop">
        <span class="pin" aria-hidden="true"><img class="pin-logo" src="<?php echo esc_url($ke_logo['mpesa']); ?>" alt="" width="40" height="22" loading="lazy" decoding="async"></span>
        <h3>Cash office</h3>
        <p class="leak"><b>The problem</b>A patient says they paid on M‑Pesa, and nobody can find the payment.</p>
        <p class="fix"><b>With Healthray</b>M‑Pesa, cash and card payments are recorded on the patient's bill.</p>
      </li>
      <li class="stop">
        <span class="pin" aria-hidden="true"><span class="msr">bed</span></span>
        <h3>Wards</h3>
        <p class="leak"><b>The problem</b>Ward days, nursing care and theatre items are missed at discharge.</p>
        <p class="fix"><b>With Healthray</b>Each day and item is added to the bill while the patient is admitted.</p>
      </li>
    </ol>
    <div class="journey-cta">
      <p><b>Which of these costs your facility the most?</b> We'll show you the fix on a demo.</p>
      <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
    </div>
  </div>
</section>

<!-- Dashboard (Kenyan demo data) -->
<section class="section" aria-labelledby="appTitle">
  <div class="wrap">
    <div class="sec-head center">
      <p class="eyebrow">See the software</p>
      <h2 class="h2" id="appTitle">Your whole hospital on one screen: patients, beds, M‑Pesa and SHA claims</h2>
      <p class="lead">This is what an owner sees at 9 a.m. The numbers update as reception, doctors, lab, pharmacy and the cash office work.</p>
    </div>

    <div class="app" role="img" aria-label="Healthray dashboard with demo data: 96 patients seen, 14 admitted, 72% of beds in use, KES 412,500 collected today, KES 1.84 million owed by SHA">
      <aside class="app-side" aria-hidden="true">
        <div class="app-logo"><i><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M3 12h4l2-5 4 10 2-5h6" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"/></svg></i>Healthray</div>
        <div class="app-new">+ New patient</div>
        <div class="app-nav on">Dashboard</div>
        <div class="app-nav">Reception</div>
        <div class="app-nav">Consultation</div>
        <div class="app-nav">Wards &amp; beds</div>
        <div class="app-nav">Lab &amp; imaging</div>
        <div class="app-nav">Pharmacy</div>
        <div class="app-nav">Cash office</div>
        <div class="app-nav">SHA claims <em>5</em></div>
        <div class="app-nav">Reports</div>
      </aside>
      <div class="app-main" aria-hidden="true">
        <div class="app-top">
          <div><h4>Good morning, Dr. Mwangi</h4><small>Today · 9:02 AM</small></div>
          <div class="app-chips"><span class="chip">Main branch</span><span class="chip"><span class="d"></span>Stored in Kenya</span></div>
        </div>
        <div class="kpis">
          <div class="kpi"><small>Patients seen today</small><b>96</b><span class="sub">8 more than last Tuesday</span></div>
          <div class="kpi"><small>Admitted</small><b>14</b><span class="sub">3 going home today</span></div>
          <div class="kpi"><small>Beds in use</small><b>72%</b><span class="sub">36 of 50 beds</span></div>
          <div class="kpi hl"><small>Collected today</small><b>KES 412K</b><span class="sub">M‑Pesa, cash and card</span></div>
        </div>
        <div class="app-grid">
          <div class="panel">
            <h5>Today's patients <span>Live</span></h5>
            <div class="tbl-wrap">
              <table class="tbl">
                <thead><tr><th>Patient</th><th class="m-hide">Department</th><th class="m-hide">Seen by</th><th>Pays by</th><th>Status</th></tr></thead>
                <tbody>
                  <tr><td><b>Wanjiru Kamau</b></td><td class="m-hide">Maternity</td><td class="m-hide">Dr. Njeri Mutua</td><td class="pay">SHA</td><td><span class="st blue">With doctor</span></td></tr>
                  <tr><td><b>Brian Otieno</b></td><td class="m-hide">Outpatient</td><td class="m-hide">CO Peter Ouma</td><td class="pay">M‑Pesa</td><td><span class="st amber">At cash office</span></td></tr>
                  <tr><td><b>Fatuma Hassan</b></td><td class="m-hide">Medical ward</td><td class="m-hide">Dr. Ali Abdi</td><td class="pay">SHA</td><td><span class="st navy">Admitted</span></td></tr>
                  <tr><td><b>Kevin Kiprotich</b></td><td class="m-hide">Imaging</td><td class="m-hide">Dr. Ruth Chebet</td><td class="pay">Insurance</td><td><span class="st grey">Waiting</span></td></tr>
                  <tr class="m-row-hide"><td><b>Grace Wambui</b></td><td class="m-hide">Paediatrics</td><td class="m-hide">Dr. James Kariuki</td><td class="pay">Cash</td><td><span class="st green">Result ready</span></td></tr>
                  <tr class="m-row-hide"><td><b>Mercy Achieng</b></td><td class="m-hide">Surgery</td><td class="m-hide">Dr. Paul Odhiambo</td><td class="pay">SHA</td><td><span class="st green">Gone home</span></td></tr>
                </tbody>
              </table>
            </div>
          </div>
          <div class="panel">
            <h5>Collected today <span>KES 412,500</span></h5>
            <div class="mix">
              <div><span>M‑Pesa</span><span class="track"><i style="width:65%"></i></span><span>268,000</span></div>
              <div><span>Cash</span><span class="track"><i style="width:20%;opacity:.75"></i></span><span>82,500</span></div>
              <div><span>Card</span><span class="track"><i style="width:15%;opacity:.5"></i></span><span>62,000</span></div>
            </div>
            <div class="claims-mini">
              <div><span>SHA owes you</span><b>KES 1.84M</b></div>
              <div><span>Waiting for pre-authorisation</span><b style="color:var(--amber-ink)">2</b></div>
              <div><span>Claims rejected this month</span><b style="color:var(--red-ink)">3</b></div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <p class="app-cap">Demo data. Your dashboard shows your own departments, branches and payment types.</p>

    <div class="inline-cta">
      <p><b>Want to see this with your own departments?</b> We'll set it up live on the call.</p>
      <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
    </div>
  </div>
</section>

<!-- HOW WE GET YOU STARTED (moved up for Kenya). CONFIRM: real set-up time -->
<section class="section tint" aria-labelledby="planTitle">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">How we get you started</p>
      <h2 class="h2" id="planTitle">Start in about two weeks, while your facility keeps working</h2>
      <p class="lead">Moving from the SHA Provider Portal, another system or paper? We set it up and move one department at a time, so patients are seen and bills are paid as normal.</p>
    </div>
    <ol class="plan">
      <li><div class="n"><b>1</b></div><h3>We set it up for you</h3><p>We add your departments, prices, SHA rates and insurers. We move your patient records from your old system, and help you enter key records from paper files.</p></li>
      <li><div class="n"><b>2</b></div><h3>We train your staff</h3><p>Each team learns the part they use: reception and triage, doctors and clinical officers, nurses, pharmacy and the cash office.</p></li>
      <li><div class="n"><b>3</b></div><h3>You start one department at a time</h3><p>Usually reception first, then the cash office, pharmacy and lab. Our team is on hand in the first days.</p></li>
      <li><div class="n"><b>4</b></div><h3>We keep supporting you</h3><p>Call or email us when you need help. We also train new staff when they join.</p></li>
    </ol>
    <div class="plan-cta">
      <p>Want to know how long it would take for your facility? Ask us on the free demo.</p>
      <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS: SAMPLE photos, quotes, names and facilities for internal review only. REPLACE with real Kenyan quotes (with permission) before ads go live, then remove the "Sample quote" tags -->
<section class="section" aria-labelledby="wallTitle">
  <div class="wrap">
    <div class="sec-head">
      <h2 class="h2" id="wallTitle">What facility owners in Kenya say</h2>
    </div>
    <div class="quotes">
      <figure class="q-main">
        <blockquote>
            <p>We moved off the SHA Provider Portal in about two weeks. The claim now comes from the same bill, so nobody types it twice, and every morning I can see what SHA still owes us.</p>
        </blockquote>
        <figcaption><footer><span class="av"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/chris-njuguna.webp" alt="Dr. Grace Wanjiru" width="64" height="64" loading="lazy" decoding="async" data-fallback="GW"></span><span><b>Dr. Grace Wanjiru</b>Medical Director, Mwangaza Hospital, Kiambu</span></footer></figcaption>
      </figure>
      <div class="q-side">
        <blockquote class="q-small">
          <p>"M&#8209;Pesa payments used to go missing by evening. Now each payment sits on the patient's bill, and the cashier's report matches at close of day."</p>
          <footer><span class="av"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/cosmas-k.webp" alt="James Otieno" width="40" height="40" loading="lazy" decoding="async" data-fallback="JO"></span><span><b>James Otieno</b>Facility Administrator, Lakeview Medical Centre, Kisumu</span></footer>
        </blockquote>
        <blockquote class="q-small">
          <p>"My staff picked it up within a week. Reception, the lab and the pharmacy all work from the same patient file."</p>
          <footer><span class="av"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/dr-ruby-oswere.webp" alt="Ann Chebet" width="40" height="40" loading="lazy" decoding="async" data-fallback="AC"></span><span><b>Ann Chebet</b>Clinical Officer in-charge, Tumaini Maternity Home, Nakuru</span></footer>
        </blockquote>
      </div>
    </div>
    <p class="ref-line"><b>Want to speak to a facility like yours?</b> Ask us on the demo and we'll connect you.</p>
    <div class="review-bar" aria-label="Review ratings">
      <span><b>4.8 / 5</b> on Google</span>
      <span><img src="https://healthray.com/wp-content/uploads/2023/11/capterra-1.webp" alt="Capterra" loading="lazy" decoding="async"><span class="fb">Capterra</span><b>4.8</b></span>
      <span><img src="https://healthray.com/wp-content/uploads/2023/11/softwaresuggest-logo.webp" alt="SoftwareSuggest" loading="lazy" decoding="async"><span class="fb">SoftwareSuggest</span><b>4.8</b></span>
      <span><img src="https://healthray.com/wp-content/uploads/2024/05/g2-logo.webp" alt="G2" loading="lazy" decoding="async"><span class="fb">G2</span><b>5.0</b></span>
    </div>
  </div>
</section>

<!-- YOUR RECORDS (4 questions). CONFIRM the "What does SHA see?" answer with the product team -->
<section class="cloud3" aria-labelledby="cloudTitle">
  <div class="wrap grid">
    <div class="intro">
      <p class="eyebrow">Your records</p>
      <h2 class="h2" id="cloudTitle">Your records are stored in Kenya. We look after the system.</h2>
      <p>You don't need a server or an IT person at your facility.</p>
      <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
    </div>
    <ul class="qa">
      <li><b>Where are our records kept?</b><span>On servers in Kenya, in line with the Data Protection Act, 2019.</span></li>
      <li><b>What does SHA see?</b><span>The details needed to check cover and process each claim. Your full patient records stay in your system.</span></li>
      <li><b>What if the power or internet goes off?</b><span>Your records are saved online, not on one computer. Laptops on battery and a MiFi or phone hotspot keep reception working.</span></li>
      <li><b>What if we stop using Healthray?</b><span>Your records are yours. We give you a full copy.</span></li>
    </ul>
  </div>
</section>

<!-- Modules -->
<section class="section" aria-labelledby="modTitle">
  <div class="wrap">
    <div class="sec-head">
      <p class="eyebrow">Hospital management system and EMR</p>
      <h2 class="h2" id="modTitle">Everything your hospital or clinic needs, in one system</h2>
    </div>
    <div class="mods">
      <div class="mod"><h3>Reception &amp; doctors</h3><ul><li>Registration and patient files</li><li>Triage and appointments</li><li>Doctor's notes and prescriptions</li><li>Patient history on one screen</li></ul></div>
      <div class="mod"><h3>Wards</h3><ul><li>Admissions and beds</li><li>Nursing notes</li><li>Theatre bookings</li><li>Maternity and casualty</li></ul></div>
      <div class="mod"><h3>Lab &amp; pharmacy</h3><ul><li>Lab tests and results</li><li>X-ray and imaging reports</li><li>Pharmacy and stock</li><li>Store and purchases</li></ul></div>
      <div class="mod"><h3>Money &amp; reports</h3><ul><li>A bill for every patient</li><li>SHA and insurance claims</li><li>M‑Pesa, cash and card</li><li>eTIMS invoices and owner reports</li></ul></div>
    </div>
    <p class="it-line">For your IT person: cloud-based hospital management software, DHA certified, data hosted in Kenya, ISO 27001 certified, connects to lab machines (HL7) and supports more than one branch.</p>

    <div class="certs">
      <p>Certification and standards</p>
      <ul>
        <li><span class="seal logo"><img src="<?php echo esc_url($ke_logo['dha']); ?>" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">DHA</span></span>DHA certified HMIS</li>
        <li><span class="seal logo"><img src="<?php echo esc_url($ke_logo['sha']); ?>" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">SHA</span></span>SHA claims integration</li>
        <li><span class="seal logo flag"><img src="<?php echo esc_url($ke_logo['flag']); ?>" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">KE</span></span>Data stored in Kenya</li>
        <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">ISO</span></span>ISO 27001 certified</li>
        <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">HL7</span></span>HL7 and FHIR</li>
        <li><span class="seal txt">ICD-10</span>ICD-10 coding</li>
      </ul>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section tint" aria-labelledby="faqTitle">
  <div class="wrap faq-grid">
    <div class="faq" id="faq">
      <div class="sec-head">
        <h2 class="h2" id="faqTitle">Questions facility owners ask us</h2>
      </div>
      <details name="ppc-faq" open><summary>Is Healthray DHA certified?</summary><p>Yes. Our certificate number is <span class="ph"><?php echo esc_html($ke_cert_no); ?></span>. You can check it on the <a href="https://certification.dha.go.ke/registry" target="_blank" rel="noopener nofollow">DHA registry</a>.</p></details>
      <details name="ppc-faq"><summary>Can we submit SHA claims from Healthray?</summary><p>Yes. You check the patient's cover at registration, request pre-authorisation where it's needed, and send the claim from the patient's bill. You can see every claim: pending, paid or rejected.</p></details>
      <details name="ppc-faq"><summary>We use the SHA Provider Portal now. How do we move?</summary><p>We set Healthray up for your facility, add your SHA rates and train your staff. Your team then does the same work in Healthray, with the bill and patient records in one place.</p></details>
      <details name="ppc-faq"><summary>How much does it cost?</summary><p>It depends on your facility type, the departments you need and how many staff use it. After the demo, we send you a clear written quote in KES.</p></details>
      <details name="ppc-faq" class="more"><summary>Does it work with M‑Pesa?</summary><p>Yes. M‑Pesa, cash, card and insurance payments are recorded on the patient's bill. At the end of the day you can see what each cashier collected.</p></details>
      <details name="ppc-faq" class="more"><summary>Can it send eTIMS invoices?</summary><p>Yes. Invoices are sent through KRA eTIMS from the patient's bill, so your cash office doesn't type them again.</p></details>
      <details name="ppc-faq" class="more"><summary>My staff are not good with computers. Will they manage?</summary><p>Yes. Each person only sees the screens for their own job, so it's easy to learn. We train every team before you start.</p></details>
      <details name="ppc-faq" class="more"><summary>How long before we start using it?</summary><p>Most facilities start in about two weeks. We set it up, bring over your records and train your staff, one department at a time.</p></details>
      <details name="ppc-faq" class="more"><summary>We have more than one branch. Can all of them use it?</summary><p>Yes. Each branch runs its own reception, pharmacy and cash office, and you see every branch on one screen.</p></details>
      <details name="ppc-faq" class="more"><summary>Who helps us after we start?</summary><p>Our team sets everything up, trains your staff and stays with you afterwards. You can reach us by phone or email, and we train new staff when they join.</p></details>
      <details name="ppc-faq" class="more"><summary>What happens in the demo?</summary><p>It's a free online call of about 30 minutes. We show you registration to discharge, M‑Pesa payments, SHA claims and owner reports, set up for a facility like yours. It helps to bring your administrator or the person who handles claims.</p></details>
      <button type="button" class="btn btn-line faq-more" id="faqMore">More questions</button>
    </div>
    <aside class="ask">
      <h3>Have another question?</h3>
      <p>Ask us on the demo. We'll answer it for your facility, using your own departments.</p>
      <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
    </aside>
  </div>
</section>

<!-- Final CTA -->
<section class="final">
  <div class="wrap">
    <div>
      <h2>See how your SHA claims would run on Healthray</h2>
      <p>Book a free demo. We'll show you Healthray set up for a facility your size, with your departments, SHA claims and M‑Pesa.</p>
    </div>
    <div class="final-actions">
      <a class="btn btn-white hr-cta-btn" href="#demo">Book a free demo</a>
    </div>
  </div>
</section>
</main>

<?php include __DIR__ . '/templates/ppc-hims/footer.php'; ?>

<nav class="mbar" id="mbar" aria-label="Quick actions">
  <a class="go hr-cta-btn" href="#demo">Book a free demo</a>
</nav>