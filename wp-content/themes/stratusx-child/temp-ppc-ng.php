<?php

/**
 * Template Name: Healthray Nigeria PPC - Hospital Software
 * Nigeria Google Ads page; CSS/JS/popup wiring in functions.php section 12 (key "ng").
 */

defined('ABSPATH') || exit;

// Lead form shown in the hero card and in the popup opened by every .hr-cta-btn.
$ppc_form = '[contact-form-7 id="11eec7d" title="PPC Form"]';
add_filter('hr_ppc_popup_args', function () use ($ppc_form) {
  return array(
    'form'  => $ppc_form,
    'title' => 'Book a free demo',
    'text'  => "A free 30-minute demo, set up for your hospital's size and departments.",
  );
});

// Settings for js/ppc-ng.js: WhatsApp number as 234XXXXXXXXXX (empty hides the buttons), Ads conversion IDs as AW-XXX/label.
$ppc_ng = array(
  'waNumber'        => '',
  'waText'          => 'Hello Healthray, I would like a demo of your hospital software for my hospital.',
  'adsConversion'   => '',
  'adsConversionWa' => '',
);
wp_add_inline_script('ppc-ng', 'var hrPpcNg = ' . wp_json_encode($ppc_ng) . ';', 'before');
?>
<svg width="0" height="0" style="position:absolute" aria-hidden="true">
  <symbol id="i-wa" viewBox="0 0 32 32">
    <path fill="currentColor" d="M16 3C8.8 3 3 8.7 3 15.8c0 2.5.7 4.9 2 7L3 29l6.4-2c2 1.1 4.3 1.7 6.6 1.7 7.2 0 13-5.7 13-12.8S23.2 3 16 3zm0 23.4c-2.1 0-4.2-.6-6-1.7l-.4-.3-3.8 1.2 1.2-3.7-.3-.4c-1.2-1.8-1.9-3.9-1.9-6.1C4.8 9.7 9.8 4.8 16 4.8s11.2 4.9 11.2 11c0 6-5 10.6-11.2 10.6zm6.1-7.9c-.3-.2-2-1-2.3-1.1-.3-.1-.5-.2-.8.2-.2.3-.9 1.1-1.1 1.3-.2.2-.4.3-.7.1-.3-.2-1.4-.5-2.7-1.7-1-.9-1.7-2-1.9-2.3-.2-.3 0-.5.1-.7l.5-.6c.2-.2.2-.4.3-.6.1-.2 0-.5 0-.6l-1-2.5c-.3-.7-.6-.6-.8-.6h-.7c-.2 0-.6.1-.9.4-.3.3-1.2 1.1-1.2 2.8s1.2 3.3 1.4 3.5c.2.2 2.4 3.7 5.9 5.1 2.9 1.1 3.5.9 4.1.8.6-.1 2-.8 2.3-1.6.3-.8.3-1.5.2-1.6-.1-.1-.4-.2-.7-.4z" />
  </symbol>
  <symbol id="i-check" viewBox="0 0 24 24">
    <path d="m5 12.5 4.5 4.5L19 7.5" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round" />
  </symbol>
</svg>

<header class="top">
  <div class="wrap">
    <?php // Logo not linked on purpose: no exits from an ads page. 
    ?>
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
        <p class="eyebrow"><span class="flag" aria-hidden="true"><i></i><i></i><i></i></span><span id="kwEyebrow">Hospital management software for Nigeria</span></p>
        <h1 id="kwH1">Hospital software that stops revenue leaks and gets HMO claims paid</h1>
        <p class="hero-sub" id="kwSub">One simple system for your front desk, doctors, lab, pharmacy, billing and HMO claims. We host it online for you, so there's no server to buy. Most hospitals start using it in about two weeks.</p>
      </div>

      <div class="form-card" id="demo">
        <h2>Book a free demo</h2>
        <p class="fc-sub">A free 30-minute demo, set up for your hospital's size and departments.</p>
        <?php echo do_shortcode($ppc_form); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped 
        ?>
      </div>

      <div class="hero-proof">
        <div class="faces">
          <div class="pile">
            <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/04/Dr.-Michael-Akinseli-100x100.webp" alt="Dr. Michael Akinseli" width="44" height="44" data-fallback="MA" decoding="async"></span>
            <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2024/12/Dr.-Ibrahim-Yusuf.webp" alt="Dr. Ibrahim Yusuf" width="44" height="44" data-fallback="IY" decoding="async"></span>
            <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2024/12/Dr.-Emeka-Okonkwo.webp" alt="Dr. Emeka Okonkwo" width="44" height="44" data-fallback="EO" decoding="async"></span>
            <span><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2024/12/Dr.-Chinedu-Eze.webp" alt="Dr. Chinedu Eze" width="44" height="44" data-fallback="CE" decoding="async"></span>
          </div>
          <p><b>Used by hospitals in Lagos, Abuja, Port Harcourt and Kano</b>, and 2,500+ hospitals worldwide</p>
        </div>
        <div class="trust-row">
          <div class="seals">
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001" width="42" height="42"><span class="fb">ISO 27001</span></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="HL7" width="42" height="42"><span class="fb">HL7</span></span>
            <span class="seal"><img src="https://healthray.com/wp-content/uploads/2025/07/FHIR.webp" alt="FHIR" width="42" height="42"><span class="fb">FHIR</span></span>
          </div>
          <p class="seal-text"><b>ISO 27001 certified data security</b><br>Your patient data is kept safe and private</p>
        </div>
        <div class="proof-row" aria-label="Ratings">
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span>
          <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
          <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
        </div>
      </div>
    </div>
  </section>

  <!-- Client logos -->
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
          </ul>
        </div>
      </div>
    </div>
  </section>

  <!-- Where money leaks -->
  <section class="section" aria-labelledby="leakTitle">
    <div class="wrap">
      <div class="sec-head center">
        <h2 class="h2" id="leakTitle">Most hospitals lose money in the same six places</h2>
        <p class="lead">Follow one patient through your hospital. See where money is lost, and how Healthray stops it.</p>
      </div>
      <ol class="journey">
        <li class="stop">
          <span class="pin" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect x="4" y="9" width="16" height="10" rx="2" stroke="#1B3C74" stroke-width="1.8" />
              <path d="M8 9V6h8v3" stroke="#0F68EA" stroke-width="1.8" />
            </svg></span>
          <h3>Front desk</h3>
          <p class="leak"><b>The problem</b>Patients see the doctor before paying, or without an HMO PA code.</p>
          <p class="fix"><b>With Healthray</b>Payment or PA code is recorded before the patient sees the doctor.</p>
        </li>
        <li class="stop">
          <span class="pin" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M7 4v6a5 5 0 0 0 10 0V4" stroke="#1B3C74" stroke-width="1.8" stroke-linecap="round" />
              <circle cx="18" cy="17" r="2.4" stroke="#0F68EA" stroke-width="1.8" />
              <path d="M12 15v1a4 4 0 0 0 4 4" stroke="#1B3C74" stroke-width="1.8" />
            </svg></span>
          <h3>Doctor</h3>
          <p class="leak"><b>The problem</b>Tests and drugs written on paper never reach the bill.</p>
          <p class="fix"><b>With Healthray</b>Everything the doctor orders goes straight to the bill.</p>
        </li>
        <li class="stop">
          <span class="pin" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M9 3v6l-5 9a2 2 0 0 0 1.8 3h12.4a2 2 0 0 0 1.8-3l-5-9V3" stroke="#1B3C74" stroke-width="1.8" stroke-linejoin="round" />
              <path d="M8 3h8M7 15h10" stroke="#0F68EA" stroke-width="1.8" stroke-linecap="round" />
            </svg></span>
          <h3>Lab</h3>
          <p class="leak"><b>The problem</b>Tests are done with no record of who ordered them or who paid.</p>
          <p class="fix"><b>With Healthray</b>Every test is linked to a patient and a bill.</p>
        </li>
        <li class="stop">
          <span class="pin" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect x="3" y="9" width="18" height="7" rx="3.5" transform="rotate(-35 12 12.5)" stroke="#1B3C74" stroke-width="1.8" />
              <path d="m10 8.5 4 7" stroke="#0F68EA" stroke-width="1.8" />
            </svg></span>
          <h3>Pharmacy</h3>
          <p class="leak"><b>The problem</b>Drugs go out without a bill. Stock expires on the shelf.</p>
          <p class="fix"><b>With Healthray</b>Every drug given is billed. You get a warning before stock expires.</p>
        </li>
        <li class="stop">
          <span class="pin" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <rect x="3" y="6" width="18" height="12" rx="2" stroke="#1B3C74" stroke-width="1.8" />
              <circle cx="12" cy="12" r="2.6" stroke="#0F68EA" stroke-width="1.8" />
            </svg></span>
          <h3>Cash desk</h3>
          <p class="leak"><b>The problem</b>At the end of the day, cash, POS and transfer totals don't match.</p>
          <p class="fix"><b>With Healthray</b>See what each cashier collected: cash, POS and transfer.</p>
        </li>
        <li class="stop">
          <span class="pin" aria-hidden="true"><svg width="24" height="24" viewBox="0 0 24 24" fill="none">
              <path d="M12 3l7 3v5c0 4.5-3 8.3-7 10-4-1.7-7-5.5-7-10V6z" stroke="#1B3C74" stroke-width="1.8" stroke-linejoin="round" />
              <path d="m9 12 2 2 4-4" stroke="#0F68EA" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" />
            </svg></span>
          <h3>HMO claims</h3>
          <p class="leak"><b>The problem</b>Claims are rejected, or forgotten for months.</p>
          <p class="fix"><b>With Healthray</b>Claims come from the same record. You see what each HMO owes you.</p>
        </li>
      </ol>
      <div class="journey-cta">
        <p><b>Which of these costs your hospital the most?</b> We'll show you the fix on a demo.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
    </div>
  </section>

  <!-- Dashboard -->
  <section class="section tint" aria-labelledby="appTitle">
    <div class="wrap">
      <div class="sec-head center">
        <p class="eyebrow">See the software</p>
        <h2 class="h2" id="appTitle">Your whole hospital on one screen: patients, beds, collections and HMO claims</h2>
        <p class="lead">This is what a hospital owner sees at 9 a.m. Every number updates live as reception, doctors, lab, pharmacy and billing work.</p>
      </div>

      <div class="app" role="img" aria-label="Healthray dashboard with demo data: 142 OPD visits, 18 admissions, 76% bed occupancy, ₦4.8 million collected today, ₦6.4 million in pending HMO claims">
        <aside class="app-side" aria-hidden="true">
          <div class="app-logo"><i><svg width="14" height="14" viewBox="0 0 24 24" fill="none">
                <path d="M3 12h4l2-5 4 10 2-5h6" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round" />
              </svg></i>Healthray</div>
          <div class="app-new">+ New patient</div>
          <div class="app-nav on">Dashboard</div>
          <div class="app-nav">Registration</div>
          <div class="app-nav">OPD &amp; EMR</div>
          <div class="app-nav">IPD &amp; beds</div>
          <div class="app-nav">Lab &amp; radiology</div>
          <div class="app-nav">Pharmacy</div>
          <div class="app-nav">Billing</div>
          <div class="app-nav">HMO &amp; NHIA claims <em>12</em></div>
          <div class="app-nav">Reports</div>
        </aside>
        <div class="app-main" aria-hidden="true">
          <div class="app-top">
            <div>
              <h4>Good morning, Dr. Adebayo</h4><small>Today · 9:02 AM</small>
            </div>
            <div class="app-chips"><span class="chip">Main branch</span><span class="chip"><span class="d"></span>Saved online</span></div>
          </div>
          <div class="kpis">
            <div class="kpi"><small>Patients seen today</small><b>142</b><span class="sub">12 more than last Tuesday</span></div>
            <div class="kpi"><small>Admitted</small><b>18</b><span class="sub">4 going home today</span></div>
            <div class="kpi"><small>Beds in use</small><b>76%</b><span class="sub">91 of 120 beds</span></div>
            <div class="kpi hl"><small>Collected today</small><b>₦4.8M</b><span class="sub">Cash, POS and transfer</span></div>
          </div>
          <div class="app-grid">
            <div class="panel">
              <h5>Today's patients <span>OPD &amp; IPD · live</span></h5>
              <div class="tbl-wrap">
                <table class="tbl">
                  <thead>
                    <tr>
                      <th>Patient</th>
                      <th class="m-hide">Department</th>
                      <th class="m-hide">Doctor</th>
                      <th>Pays by</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td><b>Adaeze Okafor</b></td>
                      <td class="m-hide">Obstetrics</td>
                      <td class="m-hide">Dr. Ifeoma Nnaji</td>
                      <td class="pay">HMO</td>
                      <td><span class="st blue">With doctor</span></td>
                    </tr>
                    <tr>
                      <td><b>Tunde Bakare</b></td>
                      <td class="m-hide">Cardiology</td>
                      <td class="m-hide">Dr. Segun Adeyemi</td>
                      <td class="pay">Self</td>
                      <td><span class="st navy">Admitted</span></td>
                    </tr>
                    <tr>
                      <td><b>Halima Sani</b></td>
                      <td class="m-hide">Paediatrics</td>
                      <td class="m-hide">Dr. Aisha Lawal</td>
                      <td class="pay">NHIA</td>
                      <td><span class="st grey">Waiting</span></td>
                    </tr>
                    <tr>
                      <td><b>Emeka Nwosu</b></td>
                      <td class="m-hide">Orthopaedics</td>
                      <td class="m-hide">Dr. Chukwuma Obi</td>
                      <td class="pay">Company</td>
                      <td><span class="st amber">At billing</span></td>
                    </tr>
                    <tr>
                      <td><b>Funke Adeyemi</b></td>
                      <td class="m-hide">General medicine</td>
                      <td class="m-hide">Dr. Bola Ogunleye</td>
                      <td class="pay">HMO</td>
                      <td><span class="st green">Result ready</span></td>
                    </tr>
                    <tr>
                      <td><b>Musa Abdullahi</b></td>
                      <td class="m-hide">Surgery</td>
                      <td class="m-hide">Dr. Kunle Martins</td>
                      <td class="pay">Self</td>
                      <td><span class="st green">Gone home</span></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
            <div class="panel">
              <h5>Money collected today <span>₦4.8M</span></h5>
              <div class="mix">
                <div><span>POS</span><span class="track"><i style="width:44%"></i></span><span>₦2.1M</span></div>
                <div><span>Transfer</span><span class="track"><i style="width:31%;opacity:.75"></i></span><span>₦1.5M</span></div>
                <div><span>Cash</span><span class="track"><i style="width:25%;opacity:.5"></i></span><span>₦1.2M</span></div>
              </div>
              <div class="claims-mini">
                <div><span>HMOs owe you</span><b>₦6.4M</b></div>
                <div><span>Waiting for PA code</span><b style="color:var(--amber-ink)">3</b></div>
                <div><span>Claims rejected this month</span><b style="color:var(--red-ink)">2</b></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <p class="app-cap">Demo data. Your dashboard shows your own departments, HMOs and branches.</p>

      <div class="inline-cta">
        <p><b>Want to see this with your hospital's departments?</b> We'll set it up live on the call.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
    </div>
  </section>

  <!-- HMO claims -->
  <section class="section" aria-labelledby="hmoTitle">
    <div class="wrap hmo">
      <div>
        <p class="eyebrow">HMO and NHIA patients</p>
        <h2 class="h2" id="hmoTitle">Get paid by HMOs faster, with less paperwork</h2>
        <p class="lead">The claim is made from the same record your doctors, lab and pharmacy already use. Nothing is typed twice.</p>
        <ul class="check-list">
          <li><span class="tick-ic"><svg width="14" height="14">
                <use href="#i-check" />
              </svg></span><span><b>Prices set once.</b> Each HMO's prices are saved and used automatically.</span></li>
          <li><span class="tick-ic"><svg width="14" height="14">
                <use href="#i-check" />
              </svg></span><span><b>PA code first.</b> The front desk adds the PA code before treatment, so claims are not rejected later.</span></li>
          <li><span class="tick-ic"><svg width="14" height="14">
                <use href="#i-check" />
              </svg></span><span><b>Easy month-end checks.</b> Monthly HMO payments and per-visit bills are shown separately.</span></li>
          <li><span class="tick-ic"><svg width="14" height="14">
                <use href="#i-check" />
              </svg></span><span><b>Know who owes you.</b> See what each HMO has paid, still owes, or rejected.</span></li>
        </ul>
      </div>
      <div class="claim-card" role="img" aria-label="HMO claims screen with demo data: ₦6.4 million waiting, ₦3.9 million paid this month, 2 claims rejected">
        <div aria-hidden="true">
          <div class="claim-head">
            <h4>HMO &amp; NHIA claims · October</h4><span class="chip">All HMOs</span>
          </div>
          <div class="claim-sum">
            <div><small>Waiting</small><b>₦6.4M</b></div>
            <div><small>Paid this month</small><b>₦3.9M</b></div>
            <div><small>Rejected</small><b style="color:var(--red-ink)">2</b></div>
          </div>
          <div class="tbl-wrap">
            <table class="tbl">
              <thead>
                <tr>
                  <th>Patient</th>
                  <th class="m-hide">Plan</th>
                  <th>PA code</th>
                  <th>Amount</th>
                  <th>Status</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><b>Adaeze Okafor</b></td>
                  <td class="m-hide">HMO</td>
                  <td>Added</td>
                  <td>₦185,000</td>
                  <td><span class="st blue">Sent</span></td>
                </tr>
                <tr>
                  <td><b>Halima Sani</b></td>
                  <td class="m-hide">NHIA</td>
                  <td>Added</td>
                  <td>₦42,500</td>
                  <td><span class="st green">Paid</span></td>
                </tr>
                <tr>
                  <td><b>Emeka Nwosu</b></td>
                  <td class="m-hide">Company</td>
                  <td>Not needed</td>
                  <td>₦96,000</td>
                  <td><span class="st amber">Ready to send</span></td>
                </tr>
                <tr>
                  <td><b>Funke Adeyemi</b></td>
                  <td class="m-hide">HMO</td>
                  <td>Added</td>
                  <td>₦18,200</td>
                  <td><span class="st blue">Sent</span></td>
                </tr>
                <tr>
                  <td><b>Ibrahim Danjuma</b></td>
                  <td class="m-hide">HMO</td>
                  <td>Missing</td>
                  <td>₦64,000</td>
                  <td><span class="st red">Needs PA code</span></td>
                </tr>
              </tbody>
            </table>
          </div>
          <div class="claim-note"><svg width="16" height="16" viewBox="0 0 24 24" fill="none">
              <path d="M12 8v5M12 16.5v.5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" />
              <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.8" />
            </svg>1 claim is on hold until its PA code is added.</div>
        </div>
      </div>
    </div>
  </section>

  <!-- Cloud -->
  <section class="cloud3" aria-labelledby="cloudTitle">
    <div class="wrap grid">
      <div class="intro">
        <p class="eyebrow">Cloud software</p>
        <h2 class="h2" id="cloudTitle">Your records are kept online. We look after the system.</h2>
        <p>You don't need a server or an IT person in your hospital.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
      </div>
      <ul class="qa">
        <li><b>Who fixes problems?</b><span>Our team, by phone, WhatsApp or email.</span></li>
        <li><b>Are our records safe?</b><span>Yes. They are protected to the ISO 27001 standard, and each staff member sees only their own work.</span></li>
        <li><b>What if there is no light or network?</b><span>Laptops run on battery or inverter, and a MiFi keeps you online.</span></li>
        <li><b>If we stop using Healthray?</b><span>Your records are yours. We give you a copy.</span></li>
      </ul>
    </div>
  </section>

  <!-- Testimonials -->
  <section class="section" aria-labelledby="wallTitle">
    <div class="wrap">
      <div class="sec-head">
        <h2 class="h2" id="wallTitle">What doctors say after switching</h2>
      </div>
      <div class="quotes">
        <figure class="q-main">
          <blockquote>
            <p>Healthray EMR makes managing patient records effortless. With secure access to medical histories and prescriptions, it reduces paperwork and improves efficiency. A must-have for any healthcare facility!</p>
          </blockquote>
          <figcaption>
            <footer><span class="av"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2025/04/Dr.-Michael-Akinseli-100x100.webp" alt="" width="64" height="64" loading="lazy" decoding="async" data-fallback="MA"></span><span><b>Dr. Michael Akinseli</b>Tender Love Medcare, Lagos</span></footer>
          </figcaption>
        </figure>
        <div class="q-side">
          <blockquote class="q-small">
            <p>"With Healthray, our paper-based system is a thing of the past. It's efficient and helps avoid double bookings and missing charts."</p>
            <footer><span class="av"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2024/12/Dr.-Ibrahim-Yusuf.webp" alt="" width="64" height="64" loading="lazy" decoding="async" data-fallback="IY"></span><span><b>Dr. Ibrahim Yusuf</b>Havannah Specialist Clinic, Kano</span></footer>
          </blockquote>
          <blockquote class="q-small">
            <p>"Healthray keeps our surgery records organized and accessible for consultations and follow-ups."</p>
            <footer><span class="av"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2024/12/Dr.-Chinedu-Eze.webp" alt="" width="64" height="64" loading="lazy" decoding="async" data-fallback="CE"></span><span><b>Dr. Chinedu Eze</b>Riverfront Medical Hub, Port Harcourt</span></footer>
          </blockquote>
          <blockquote class="q-small">
            <p>"It's reliable and secure. I trust it completely in my daily rounds."</p>
            <footer><span class="av"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2024/12/Dr.-Emeka-Okonkwo.webp" alt="" width="64" height="64" loading="lazy" decoding="async" data-fallback="EO"></span><span><b>Dr. Emeka Okonkwo</b>St. Niklaus Medical Center</span></footer>
          </blockquote>
        </div>
      </div>
      <div class="review-bar" aria-label="Review ratings">
        <span><b>4.8 / 5</b> on Google</span>
        <span><img src="https://healthray.com/wp-content/uploads/2023/11/capterra-1.webp" alt="Capterra" loading="lazy" decoding="async"><span class="fb">Capterra</span><b>4.8</b></span>
        <span><img src="https://healthray.com/wp-content/uploads/2023/11/softwaresuggest-logo.webp" alt="SoftwareSuggest" loading="lazy" decoding="async"><span class="fb">SoftwareSuggest</span><b>4.8</b></span>
        <span><img src="https://healthray.com/wp-content/uploads/2024/05/g2-logo.webp" alt="G2" loading="lazy" decoding="async"><span class="fb">G2</span><b>5.0</b></span>
      </div>
    </div>
  </section>

  <!-- Modules -->
  <section class="section tint" aria-labelledby="modTitle">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">Hospital management software and EMR</p>
        <h2 class="h2" id="modTitle">Everything your hospital needs, in one system</h2>
      </div>
      <div class="mods">
        <div class="mod">
          <h3>Front desk &amp; doctors</h3>
          <ul>
            <li>Patient registration and files</li>
            <li>Appointments and reminders</li>
            <li>Doctor's notes and prescriptions</li>
            <li>Patient history on one screen</li>
          </ul>
        </div>
        <div class="mod">
          <h3>Wards</h3>
          <ul>
            <li>Admissions and beds</li>
            <li>Nursing notes</li>
            <li>Surgery (theatre) bookings</li>
            <li>Emergency and maternity</li>
          </ul>
        </div>
        <div class="mod">
          <h3>Lab &amp; pharmacy</h3>
          <ul>
            <li>Lab tests and results</li>
            <li>X-ray and scan reports</li>
            <li>Pharmacy and stock</li>
            <li>Store and purchases</li>
          </ul>
        </div>
        <div class="mod">
          <h3>Money &amp; reports</h3>
          <ul>
            <li>Bills for every patient</li>
            <li>HMO and NHIA claims</li>
            <li>Daily cash report</li>
            <li>Owner reports, staff and payroll</li>
          </ul>
        </div>
      </div>
      <p class="it-line">For your IT person: cloud-based, ISO 27001 certified, connects to lab machines (HL7) and supports more than one branch.</p>
    </div>
  </section>

  <!-- Onboarding -->
  <section class="section" aria-labelledby="planTitle">
    <div class="wrap">
      <div class="sec-head">
        <p class="eyebrow">How we get you started</p>
        <h2 class="h2" id="planTitle">Start in about two weeks, while your hospital keeps working</h2>
        <p class="lead">We set it up and move one department at a time, so patients are seen and bills are paid as normal.</p>
      </div>
      <ol class="plan">
        <li>
          <div class="n"><b>1</b></div>
          <h3>We set it up for you</h3>
          <p>We add your departments, prices and HMOs. We move your records from your old software and help you enter key records from paper files.</p>
        </li>
        <li>
          <div class="n"><b>2</b></div>
          <h3>We train your staff</h3>
          <p>Each team learns the part they use: front desk, nurses, doctors, pharmacy and billing.</p>
        </li>
        <li>
          <div class="n"><b>3</b></div>
          <h3>You start one department at a time</h3>
          <p>Usually the front desk first, then billing, pharmacy and lab. Our team is available to help in the first days.</p>
        </li>
        <li>
          <div class="n"><b>4</b></div>
          <h3>We keep supporting you</h3>
          <p>Call, WhatsApp or email us when you need help. We also train new staff when they join.</p>
        </li>
      </ol>
      <div class="plan-cta">
        <p>Want to know how long it would take for your hospital? Ask us on the free demo.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
      </div>

      <div class="certs">
        <p>Security and standards</p>
        <ul>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">ISO</span></span>ISO 27001 certified</li>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">HIPAA</span></span>HIPAA</li>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">HL7</span></span>HL7</li>
          <li><span class="seal"><img src="https://healthray.com/wp-content/uploads/2025/07/FHIR.webp" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">FHIR</span></span>FHIR</li>
          <li><span class="seal txt">ICD-10</span>ICD-10 coding</li>
          <li><span class="seal"><img src="https://healthray.com/en-ng/wp-content/uploads/sites/2/2026/10/Nigeria-Data-Protection-Commission-logo.webp" alt="" width="58" height="58" loading="lazy" decoding="async"><span class="fb">NDPA 2023</span></span>Supports NDPA 2023</li>
        </ul>
      </div>
    </div>
  </section>

  <!-- FAQ -->
  <section class="section tint" aria-labelledby="faqTitle">
    <div class="wrap faq-grid">
      <div class="faq">
        <div class="sec-head">
          <h2 class="h2" id="faqTitle">Questions hospital owners ask us</h2>
        </div>
        <details name="ppc-faq" open>
          <summary>How much does it cost?</summary>
          <p>We have plans for every size, from small clinics to large hospitals and groups. The price depends on your size and the departments you need. After the demo, we send you a clear written quote for your hospital.</p>
        </details>
        <details name="ppc-faq">
          <summary>What happens if our internet goes off?</summary>
          <p>Healthray runs online, so it needs internet. A simple backup, like a second internet line or a MiFi, keeps work going. Your records are never lost, because they are saved online and not on one computer. We'll help you plan your setup on the demo.</p>
        </details>
        <details name="ppc-faq">
          <summary>Can it handle HMO and NHIA patients?</summary>
          <p>Yes. Each HMO's prices are saved in the system. The front desk adds the PA code before treatment, and you can see every claim: sent, paid, rejected or still waiting. Company patients work the same way.</p>
        </details>
        <details name="ppc-faq">
          <summary>Will we lose our records when we switch?</summary>
          <p>No. We bring over your patient, billing and stock records from your current software. We also help you enter key records from paper files.</p>
        </details>
        <details name="ppc-faq">
          <summary>My staff are not good with computers. Will they manage?</summary>
          <p>Yes. Each person only sees the screens for their own job, so it's easy to learn. We train every team before you start. When a staff member leaves, you remove their access in one click.</p>
        </details>
        <details name="ppc-faq">
          <summary>Is our patient data safe?</summary>
          <p>Yes. Healthray is ISO 27001 certified for data security. Each staff member sees only what they need, and every change is recorded with their name. This helps you follow Nigeria's data protection law (NDPA 2023). Your data belongs to you.</p>
        </details>
        <details name="ppc-faq">
          <summary>Is it only for big hospitals?</summary>
          <p>No. Clinics, small hospitals, specialist centres and hospital groups use Healthray. We set it up for your size and only turn on the departments you need.</p>
        </details>
        <details name="ppc-faq">
          <summary>Do we need to buy new computers?</summary>
          <p>In most cases, no. Healthray opens in a web browser, so your current computers, and even your phone, are usually enough. We'll check your setup with you on the demo.</p>
        </details>
        <details name="ppc-faq">
          <summary>How long before we start using it?</summary>
          <p>Most hospitals start in about two weeks. We set it up, bring over your records and train your staff, one department at a time, so your hospital keeps running.</p>
        </details>
        <details name="ppc-faq">
          <summary>We have more than one branch. Can all of them use it?</summary>
          <p>Yes. Each branch runs its own front desk, pharmacy and billing, and you see every branch on one screen.</p>
        </details>
        <details name="ppc-faq">
          <summary>Who helps us after we start?</summary>
          <p>Our team sets everything up, trains your staff and stays with you afterwards. You can reach us on WhatsApp, by phone or by email.</p>
        </details>
        <details name="ppc-faq">
          <summary>What happens in the demo?</summary>
          <p>It's a free online call. We show you registration to discharge, billing, HMO claims and owner reports, set up for a hospital your size, and answer your questions. It helps to bring your administrator or accountant.</p>
        </details>
      </div>
      <aside class="ask">
        <h3>Have another question?</h3>
        <p>Ask us on the demo. We'll answer it for your hospital, using your own departments.</p>
        <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
        <a class="btn btn-wa js-wa" href="#" target="_blank" rel="noopener" hidden><svg class="wa-ico">
            <use href="#i-wa" />
          </svg>Ask on WhatsApp</a>
      </aside>
    </div>
  </section>

  <!-- Final CTA -->
  <section class="final">
    <div class="wrap">
      <div>
        <h2>Find out where your hospital is losing money</h2>
        <p>Book a free demo. We'll show you Healthray set up for a hospital your size, with your departments and your HMOs.</p>
      </div>
      <div class="final-actions">
        <a class="btn btn-white hr-cta-btn" href="#demo">Book a free demo</a>
        <a class="btn btn-ghost js-wa" href="#" target="_blank" rel="noopener" hidden><svg class="wa-ico">
            <use href="#i-wa" />
          </svg>WhatsApp us</a>
      </div>
    </div>
  </section>
</main>

<?php include __DIR__ . '/templates/ppc-hims/footer.php'; ?>

<nav class="mbar" id="mbar" aria-label="Quick actions">
  <a class="go hr-cta-btn" href="#demo">Book a free demo</a>
</nav>