<?php

/**
 * Template Name: Healthray Home - RS
 * Template Post Type: page
 */
if (! defined('ABSPATH')) {
  exit;
}
$hr_img = 'https://healthray.com/wp-content/uploads/2026/11'; // the 9 department pictures (NN-name.webp, NN-name-phone.webp)
?>
<script id="ppc-img-fallback">
  document.addEventListener("error", function(e) {
    var i = e.target,
      s, f;
    if (i.tagName !== "IMG" || !i.closest(".hr-home-main")) return;
    s = i.getAttribute("data-fallback-src");
    if (s) {
      i.removeAttribute("data-fallback-src");
      i.src = s;
      return;
    }
    f = i.getAttribute("data-fallback");
    if (f) {
      i.parentNode.textContent = f;
      return;
    }
    i.parentNode.classList.add("noimg");
  }, true);
</script>
<div class="ppc-hims">
  <div class="hr-home-main" id="hr-home-main">
    <!-- 1. HERO: keyword match + the #1 fear answered + form without scrolling -->
    <section class="hero">
      <div class="wrap hero-grid">
        <div class="hero-copy">
          <h1 class="eyebrow">Hospital Management Software</h1>
          <p class="hero-title">Run your OPD, IPD, billing, pharmacy and lab on one system</p>
          <p class="hero-sub"><span class="hs-a">Made for Indian hospitals of every size, from single-doctor clinics to 1,100-bed medical colleges.</span><span class="hs-b">We move your old data, train your staff at your hospital and stay on call 24×7. Most hospitals start in 1 to 3 weeks.</span></p>
          <p class="mini-trust" role="group" aria-label="Rated 4.8, used by 2,500+ hospitals and clinics, NABH certified, ABDM compliant"><b>★ 4.8</b> <i>·</i> <b>2,500+</b> hospitals &amp; clinics <i class="sep-x">·</i> <span class="nw">NABH <i>·</i> ABDM</span></p>
        </div>

        <div class="form-card" id="demo">
          <h2>Book a free demo</h2>
          <p class="fc-sub">See Healthray set up for your hospital's size and departments. Free, 30 minutes, and you do not have to buy.</p>

          <?php echo do_shortcode('[contact-form-7 id="9a13f7a" title="NEW LEAD FORM - Home Page"]'); ?>
        </div>

        <div class="hero-proof">
          <div class="faces">
            <div class="pile">
              <span><img
                  src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp"
                  alt="Dr. Ketan Rupala" width="44" height="44" data-fallback="KR"></span>
              <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Bhaumik-Rathore-150x150.webp"
                  alt="Dr. Bhaumik Rathore" width="44" height="44" data-fallback="BR"></span>
              <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Maharshi-Desai-150x150.webp"
                  alt="Dr. Maharshi Desai" width="44" height="44" data-fallback="MD"></span>
              <span><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp"
                  alt="Dr. Vimal Dhaduk" width="44" height="44" data-fallback="VD"></span>
              <span><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp"
                  alt="Dr. Arpit Gajjar" width="44" height="44" data-fallback="AG"></span>
            </div>
            <p><b>2,500+ hospitals and clinics</b> across India run on Healthray</p>
          </div>
          <div class="trust-row">
            <div class="seals">
              <span class="seal"><img
                  src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp"
                  alt="NABH certified" width="42" height="42"><span class="fb">NABH</span></span>
              <span class="seal"><img src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp"
                  alt="ABDM compliant" width="42" height="42"><span class="fb">ABDM</span></span>
              <span class="seal"><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="NHA approved"
                  width="42" height="42"><span class="fb">NHA</span></span>
              <span class="seal"><img
                  src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="ISO 27001"
                  width="42" height="42"><span class="fb">ISO 27001</span></span>
            </div>
            <p class="seal-text"><b>NABH certified, ABDM compliant</b><br>NHA approved · ISO 27001 data security</p>
          </div>
          <div class="ratings" role="group" aria-label="Ratings">
            <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span>
            <span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span>
            <span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span>
          </div>
        </div>
      </div>
    </section>

    <!-- 2. HOSPITALS + SIZE: "a hospital like mine uses this". Bed badge on each tile (confirmed numbers only). -->
    <section class="logos" aria-label="Hospitals using Healthray">
      <div class="wrap">
        <p class="lg-label">Hospitals and medical colleges that run on Healthray</p>
        <div class="marquee">
          <div class="marquee-track">
            <ul>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/hzb-arogyam-multispeciality-hospital.webp" alt="HZB Arogyam Multispeciality Hospital, Hazaribagh, Jharkhand" width="1336" height="534" loading="lazy">
                  <span class="lg-name"><b>HZB Arogyam Multispeciality Hospital</b><small>Hazaribagh, Jharkhand</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>150 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/vibrant-hospital.webp" alt="Vibrant Multispecialty Hospital, Vapi" width="1600" height="489" loading="lazy">
                  <span class="lg-name"><b>Vibrant Multispecialty Hospital</b><small>Vapi</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp" alt="Shraddha Arogya Mandir, Vapi" width="900" height="270" loading="lazy">
                  <span class="lg-name"><b>Shraddha Arogya Mandir</b><small>Vapi</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120+ beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/shri-manakula-vinayak-medical-college.webp" alt="Sri Manakula Vinayagar Medical College, Puducherry" width="502" height="170" loading="lazy">
                  <span class="lg-name"><b>Sri Manakula Vinayagar Medical College</b><small>Puducherry</small></span>
                </span>
                <span class="lg-badge mc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 9l10-5 10 5-10 5z" />
                    <path d="M6 11v5c3 2 9 2 12 0v-5" />
                  </svg>Medical college · 1,100 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/parivar-super-speciality-hospital.webp" alt="Parivar Super Speciality Hospital, Madhya Pradesh" width="454" height="124" loading="lazy">
                  <span class="lg-name"><b>Parivar Super Speciality Hospital</b><small>Madhya Pradesh</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>140 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jeevan-rekha-hospital.webp" alt="Jeevan Rekha Hospital, West Bengal" width="525" height="150" loading="lazy">
                  <span class="lg-name"><b>Jeevan Rekha Hospital</b><small>West Bengal</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/mamta-medical-college-hospital.webp" alt="Mamta Medical College &amp; Hospital, Siwan, Bihar" width="433" height="433" loading="lazy">
                  <span class="lg-name"><b>Mamta Medical College &amp; Hospital</b><small>Siwan, Bihar</small></span>
                </span>
                <span class="lg-badge mc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 9l10-5 10 5-10 5z" />
                    <path d="M6 11v5c3 2 9 2 12 0v-5" />
                  </svg>Medical college · 670 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/budha-baba-multispeciality-hospital.webp" alt="Budha Baba Multispecialty Hospital, Cuttack, Odisha" width="239" height="240" loading="lazy">
                  <span class="lg-name"><b>Budha Baba Multispecialty Hospital</b><small>Cuttack, Odisha</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>130 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-medical-college.webp" alt="Heritage Medical College, Varanasi" width="401" height="150" loading="lazy">
                  <span class="lg-name"><b>Heritage Medical College</b><small>Varanasi</small></span>
                </span>
                <span class="lg-badge mc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 9l10-5 10 5-10 5z" />
                    <path d="M6 11v5c3 2 9 2 12 0v-5" />
                  </svg>Medical college · 1,000 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/pims-multi-superspeciality-hospital.webp" alt="PIMS Multi-Superspeciality Hospital, Udaipur" width="1732" height="573" loading="lazy">
                  <span class="lg-name"><b>PIMS Multi-Superspeciality Hospital</b><small>Udaipur</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jj-plus-hospitals.webp" alt="JJ Plus Hospitals" width="298" height="60" loading="lazy">
                  <span class="lg-name"><b>JJ Plus Hospitals</b></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>135 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jabalpur-hospital-research-center.webp" alt="Jabalpur Hospital &amp; Research Centre, Jabalpur, MP" width="541" height="176" loading="lazy">
                  <span class="lg-name"><b>Jabalpur Hospital &amp; Research Centre</b><small>Jabalpur, MP</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>350 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/uploads/2025/08/Prabh-Aasra-Unified-Family.webp" alt="Prabh Aasra Charitable Hospital, Punjab" width="575" height="150" loading="lazy">
                  <span class="lg-name"><b>Prabh Aasra Charitable Hospital</b><small>Punjab</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-hospital.webp" alt="Heritage Hospital, Noida" width="640" height="222" loading="lazy">
                  <span class="lg-name"><b>Heritage Hospital</b><small>Noida</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>100 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/universal-hospital.webp" alt="Universal Hospital, Surat" width="4573" height="880" loading="lazy">
                  <span class="lg-name"><b>Universal Hospital</b><small>Surat</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>250 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jupiter-hospital-group.webp" alt="Jupiter Hospital Group, Vadodara" width="220" height="220" loading="lazy">
                  <span class="lg-name"><b>Jupiter Hospital Group</b><small>Vadodara</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>100 beds</span>
              </li>
            </ul>
            <ul aria-hidden="true">
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/hzb-arogyam-multispeciality-hospital.webp" alt="" width="1336" height="534" loading="lazy">
                  <span class="lg-name"><b>HZB Arogyam Multispeciality Hospital</b><small>Hazaribagh, Jharkhand</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>150 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/vibrant-hospital.webp" alt="" width="1600" height="489" loading="lazy">
                  <span class="lg-name"><b>Vibrant Multispecialty Hospital</b><small>Vapi</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/uploads/2025/08/Shraddha-Arogya-Mandir.webp" alt="" width="900" height="270" loading="lazy">
                  <span class="lg-name"><b>Shraddha Arogya Mandir</b><small>Vapi</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120+ beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/shri-manakula-vinayak-medical-college.webp" alt="" width="502" height="170" loading="lazy">
                  <span class="lg-name"><b>Sri Manakula Vinayagar Medical College</b><small>Puducherry</small></span>
                </span>
                <span class="lg-badge mc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 9l10-5 10 5-10 5z" />
                    <path d="M6 11v5c3 2 9 2 12 0v-5" />
                  </svg>Medical college · 1,100 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/parivar-super-speciality-hospital.webp" alt="" width="454" height="124" loading="lazy">
                  <span class="lg-name"><b>Parivar Super Speciality Hospital</b><small>Madhya Pradesh</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>140 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jeevan-rekha-hospital.webp" alt="" width="525" height="150" loading="lazy">
                  <span class="lg-name"><b>Jeevan Rekha Hospital</b><small>West Bengal</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/mamta-medical-college-hospital.webp" alt="" width="433" height="433" loading="lazy">
                  <span class="lg-name"><b>Mamta Medical College &amp; Hospital</b><small>Siwan, Bihar</small></span>
                </span>
                <span class="lg-badge mc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 9l10-5 10 5-10 5z" />
                    <path d="M6 11v5c3 2 9 2 12 0v-5" />
                  </svg>Medical college · 670 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/budha-baba-multispeciality-hospital.webp" alt="" width="239" height="240" loading="lazy">
                  <span class="lg-name"><b>Budha Baba Multispecialty Hospital</b><small>Cuttack, Odisha</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>130 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-medical-college.webp" alt="" width="401" height="150" loading="lazy">
                  <span class="lg-name"><b>Heritage Medical College</b><small>Varanasi</small></span>
                </span>
                <span class="lg-badge mc"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M2 9l10-5 10 5-10 5z" />
                    <path d="M6 11v5c3 2 9 2 12 0v-5" />
                  </svg>Medical college · 1,000 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/pims-multi-superspeciality-hospital.webp" alt="" width="1732" height="573" loading="lazy">
                  <span class="lg-name"><b>PIMS Multi-Superspeciality Hospital</b><small>Udaipur</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jj-plus-hospitals.webp" alt="" width="298" height="60" loading="lazy">
                  <span class="lg-name"><b>JJ Plus Hospitals</b></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>135 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jabalpur-hospital-research-center.webp" alt="" width="541" height="176" loading="lazy">
                  <span class="lg-name"><b>Jabalpur Hospital &amp; Research Centre</b><small>Jabalpur, MP</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>350 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/uploads/2025/08/Prabh-Aasra-Unified-Family.webp" alt="" width="575" height="150" loading="lazy">
                  <span class="lg-name"><b>Prabh Aasra Charitable Hospital</b><small>Punjab</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>120 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/heritage-hospital.webp" alt="" width="640" height="222" loading="lazy">
                  <span class="lg-name"><b>Heritage Hospital</b><small>Noida</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>100 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/universal-hospital.webp" alt="" width="4573" height="880" loading="lazy">
                  <span class="lg-name"><b>Universal Hospital</b><small>Surat</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>250 beds</span>
              </li>
              <li>
                <span class="lg-tile">
                  <img src="https://healthray.com/wp-content/themes/stratusx-child/assets/ppc-hims/jupiter-hospital-group.webp" alt="" width="220" height="220" loading="lazy">
                  <span class="lg-name"><b>Jupiter Hospital Group</b><small>Vadodara</small></span>
                </span>
                <span class="lg-badge"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M3 7v12M3 15h18v4M21 15v-3a3 3 0 0 0-3-3h-7v6" />
                    <circle cx="7" cy="11.5" r="2" />
                  </svg>100 beds</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
    </section>

    <!-- 3. PRODUCT WALKTHROUGH: "what does it look like, is it easy?" - shown right after peer proof. -->
    <section class="section tint walk" id="video-tour" aria-labelledby="walkTitle">
      <div class="wrap">
        <div class="sec-head">
          <p class="eyebrow">1-minute video</p>
          <h2 class="h2" id="walkTitle">Watch how Healthray works in 1 minute</h2>
          <p class="lead">One patient goes through reception, the doctor, admission, billing, TPA and discharge. Sample patient, real software.</p>
        </div>
        <div class="player" id="player">
          <div class="player-screen">
            <img class="player-poster" loading="lazy" decoding="async"
              src="https://healthray.com/wp-content/uploads/2026/11/home-tour-poster.jpg" width="1920" height="1080"
              alt="Healthray dashboard: OPD visits, IPD admissions, bed occupancy, collections and today's patients">
            <video id="walkVideo" muted loop playsinline preload="none" disablepictureinpicture disableremoteplayback
              controlslist="nodownload noremoteplayback" width="1920" height="1080" aria-hidden="true">
              <source src="https://healthray.com/wp-content/uploads/2026/11/home-tour-web.mp4" type="video/mp4">
            </video>
          </div>
          <button type="button" class="vt" id="walkBtn" aria-label="Play the walkthrough">
            <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true">
              <path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" />
            </svg>
            <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true">
              <rect x="6" y="5" width="4" height="14" rx="1" />
              <rect x="14" y="5" width="4" height="14" rx="1" />
            </svg>
          </button>
          <button type="button" class="vt vt-fs" id="walkFs" aria-label="Watch the walkthrough full screen">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"
              stroke-linejoin="round" aria-hidden="true">
              <path d="M4 9V5a1 1 0 0 1 1-1h4M15 4h4a1 1 0 0 1 1 1v4M20 15v4a1 1 0 0 1-1 1h-4M9 20H5a1 1 0 0 1-1-1v-4" />
            </svg>
          </button>
        </div>
        <div class="walk-cta">
          <p><b>Want to see this with your own departments?</b> Book a free 30-minute demo and we will set it up for your hospital's size.</p>
          <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
        </div>
      </div>
    </section>

    <!-- 4. DEPARTMENTS COVERED: large still of the real screen, its text underneath, numbered list on the left.
       Computer: the list and picture stay pinned under the top bar while the page scrolls, one department per step of scrolling,
       and the page moves on after Reports (js/departments.js). Phone: the names are a row you swipe, and it changes by itself. -->
    <section class="section dp dpG auto" id="departments" aria-labelledby="departmentsTitle" style="--auto:6000ms" data-auto="6000">
      <div class="wrap">
        <div class="dp-head">
          <h2 id="departmentsTitle">One hospital management system for <em>every department</em></h2>
          <p>The Healthray hospital management system gives each department its own screens, and all of them work on the same patient record. Find yours.</p>
        </div>
        <div class="dp-pin">
          <div class="g3-grid">
            <div>
              <div class="st-stack">
                <div class="on" data-i="0">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/01-registration-opd-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/01-registration-opd.webp" alt="Healthray patient queue with waiting, engaged and completed counts, and the printed OPD case paper with the token number" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="1">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/02-doctor-emr-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/02-doctor-emr.webp" alt="Healthray doctor screen: patient vitals, complaints and diagnosis, with the prescription below" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="2">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/03-ipd-beds-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/03-ipd-beds.webp" alt="Healthray bed availability board: ICU beds with patients, days admitted and bill status, and a vacant bed with an Admit Patient button" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="3">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/04-lab-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/04-lab.webp" alt="Healthray lab dashboard: lab orders today, pending, sample collected, reports delivered and average turnaround time" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="4">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/05-pharmacy-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/05-pharmacy.webp" alt="Healthray pharmacy: medicines issued to an admitted patient with batch and expiry, and the same amount added to her IPD bill" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="5">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/06-billing-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/06-billing.webp" alt="Healthray IPD billing: the Create IPD Bill screen with bed, visit, nursing, lab and pharmacy charges, and the printed final bill ready at discharge" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="6">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/07-insurance-tpa-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/07-insurance-tpa.webp" alt="Healthray TPA Patient List: cashless patients with claim number, insurer and paid or due status, filtered by TPA and insurer" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="7">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/08-discharge-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/08-discharge.webp" alt="Healthray discharge summary: final diagnosis, ICD-10 code, hospital course and advice for an admitted patient, with Daily Visit, Template and Print" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
                <div data-i="8">
                  <picture>
                    <source media="(max-width:900px)" srcset="<?php echo esc_url($hr_img); ?>/09-reports-phone.webp"><img src="<?php echo esc_url($hr_img); ?>/09-reports.webp" alt="Healthray OPD and IPD dashboard: OPD visits today, IPD admissions, bed occupancy, average length of stay, OPD trend and admissions versus discharges" width="1800" height="1013" loading="lazy">
                  </picture>
                </div>
              </div>
              <div class="g3-copy">
                <div class="on" data-i="0">
                  <div>
                    <h3>Patient registration and OPD management</h3>
                    <p>A new patient is registered once. They get a token, the doctor&#x27;s list updates, and no other counter asks for the details again.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a><a href="https://healthray.com/modules/opd-management/">More about OPD software →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Registration with ABHA</li>
                    <li>Appointments and tokens</li>
                    <li>Health <span class="nw">check-up</span> packages</li>
                    <li>Visitor management</li>
                  </ul>
                </div>
                <div data-i="1">
                  <div>
                    <h3>EMR and <span class="nw">e-prescription</span> for doctors</h3>
                    <p>The patient&#x27;s history is open before they sit down. Tests and medicines are ordered from the same screen as the prescription.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a><a href="https://healthray.com/emr-software/">More about EMR software →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Patient history</li>
                    <li>E-prescription</li>
                    <li>Lab and medicine orders</li>
                    <li>Emergency and home care</li>
                  </ul>
                </div>
                <div data-i="2">
                  <div>
                    <h3>IPD, bed and OT management</h3>
                    <p>Everyone sees the same bed chart. Each bed shows as vacant, occupied or being cleaned, and the patient is admitted on that same screen.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a><a href="https://healthray.com/modules/ipd-management/">More about IPD software →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Bed availability</li>
                    <li>Admission and discharge</li>
                    <li>OT management</li>
                    <li>CSSD</li>
                  </ul>
                </div>
                <div data-i="3">
                  <div>
                    <h3>Laboratory (LIMS), radiology and blood bank</h3>
                    <p>The doctor&#x27;s test order reaches the lab without a slip. The report goes back to the patient&#x27;s record and the charge goes to the bill.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a><a href="https://healthray.com/laboratory-information-management-system/">More about lab software →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Laboratory (LIMS)</li>
                    <li>Lab machine connection</li>
                    <li>Radiology with PACS</li>
                    <li>Blood bank</li>
                  </ul>
                </div>
                <div data-i="4">
                  <div>
                    <h3>Pharmacy and store management</h3>
                    <p>Every medicine issued lands on the patient&#x27;s bill. Stock and expiry are tracked for the pharmacy and for every store.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a><a href="https://healthray.com/pharmacy-management-system/">More about pharmacy software →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Pharmacy</li>
                    <li>Inventory and stores</li>
                    <li>Equipment management</li>
                    <li>Biomedical waste</li>
                  </ul>
                </div>
                <div data-i="5">
                  <div>
                    <h3>Hospital billing software, built in</h3>
                    <p>The bill builds itself during the stay. Charges arrive from each department, so the final bill is ready at discharge. GST invoices and receipts go to the patient on WhatsApp.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a><a href="https://healthray.com/medical-billing-software/">More about billing software →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>OPD and IPD billing</li>
                    <li>Treatment packages</li>
                    <li>Payments</li>
                    <li>Tally sync</li>
                  </ul>
                </div>
                <div data-i="6">
                  <div>
                    <h3>Insurance and TPA claims</h3>
                    <p>The whole claim goes out from one screen. You can see what the insurer has approved and what the patient has to pay.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Claim status</li>
                    <li>Claim documents</li>
                    <li>Who pays what</li>
                  </ul>
                </div>
                <div data-i="7">
                  <div>
                    <h3>Discharge summary and final settlement</h3>
                    <p>The checklist, the discharge summary and the final settlement are finished together, so the patient is not kept waiting for paperwork.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Discharge checklist</li>
                    <li>Discharge summary</li>
                    <li>Final settlement</li>
                  </ul>
                </div>
                <div data-i="8">
                  <div>
                    <h3>Reports for the owner and management</h3>
                    <p>OPD visits, admissions, bed occupancy and collection for today are on one screen, for one hospital or for all your branches.</p>
                    <p class="dp-go"><a class="dp-main" href="#demo">See this in a demo →</a></p>
                  </div>
                  <ul class="dp-has">
                    <li>Daily dashboard</li>
                    <li>MRD and HRMS</li>
                    <li>Feedback and complaints</li>
                    <li>Multi-branch control</li>
                  </ul>
                </div>
              </div>
            </div>
            <ul class="col-list g3-rail">
              <li class="col-item on" data-i="0"><button type="button" class="col-btn" data-go="0">Registration &amp; OPD</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="1"><button type="button" class="col-btn" data-go="1">Doctor (EMR)</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="2"><button type="button" class="col-btn" data-go="2">IPD &amp; beds</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="3"><button type="button" class="col-btn" data-go="3">Lab</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="4"><button type="button" class="col-btn" data-go="4">Pharmacy</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="5"><button type="button" class="col-btn" data-go="5">Billing</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="6"><button type="button" class="col-btn" data-go="6">Insurance &amp; TPA</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="7"><button type="button" class="col-btn" data-go="7">Discharge</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
              <li class="col-item" data-i="8"><button type="button" class="col-btn" data-go="8">Reports</button>
                <div class="col-bar" aria-hidden="true"><i></i></div>
              </li>
            </ul>
          </div>
        </div>
        <div class="dp-foot">
          <p>It also connects with Tally, lab machines, PACS, WhatsApp and <a href="https://healthray.com/abdm/">ABDM</a>, and runs on cloud or on your own server. No beds? See our <a href="https://healthray.com/clinic-management-software/">clinic management software</a>.</p>
        </div>
      </div>
    </section>

    <!-- 2c. WHO IT IS FOR (version 2, chosen 17 Nov 2026): the owner's question, answered with one big "Yes.", then the 8 kinds of hospital as a tick grid with a real customer under the ones we can name. Only facts from healthray.com pages and real customers. -->
    <section class="section ty" id="hospital-types" aria-labelledby="typesTitle">
      <div class="wrap t2-grid">
        <div class="t2-head">
          <p class="eyebrow">Who it is for</p>
          <h2 id="typesTitle">Will Healthray work for my hospital?</h2>
          <p class="t2-yes" aria-hidden="true">Yes.</p>
          <p class="t2-under">From a small nursing home to a <span class="nw">1,100-bed</span> medical college.</p>
          <a class="btn btn-primary hr-cta-btn" href="#demo">Book a demo for my hospital</a>
        </div>
        <div>
          <ul class="t2-list">
            <li>
              <h3>Nursing homes</h3>
            </li>
            <li>
              <h3><span class="nw">Super-speciality</span> hospitals</h3><small>Gastron, Surat</small>
            </li>
            <li>
              <h3>Trust &amp; charitable hospitals</h3><small>SGL Charitable, Jalandhar</small>
            </li>
            <li>
              <h3>Maternity &amp; IVF centres</h3>
            </li>
            <li>
              <h3>Eye hospitals</h3><small>Dr. Dave Eye Hospital, Surat</small>
            </li>
            <li>
              <h3>Cancer centres</h3><small>Akshara Apollo CBCC</small>
            </li>
            <li>
              <h3>Medical colleges</h3><small>Sri Manakula Vinayagar, 1,100 beds</small>
            </li>
            <li>
              <h3>Hospital chains</h3>
            </li>
          </ul>
          <p class="t2-roles">Owners, hospital administrators, medical superintendents and accounts teams each get their own login and their own reports.</p>
          <p class="t2-cta"><a class="btn btn-primary hr-cta-btn" href="#demo">Book a demo for my hospital</a></p>
        </div>
      </div>
    </section>

    <!-- 3. THE REAL FEAR: "will my staff use it / will switching break things?" answered by customers, not by us -->
    <section class="section tint" id="staff">
      <div class="wrap">
        <div class="sec-head">
          <p class="eyebrow">The question every hospital asks first</p>
          <h2 class="h2">Will your staff use it? See what other hospitals say.</h2>
        </div>
        <div class="fear">
          <div>
            <blockquote>
              <p class="big-quote">For us, buying the software was not the main concern. The real question was whether our
                team would actually be able to use it in daily work… The Healthray team came to the hospital, trained the
                staff on-site and showed each workflow in a simple way… Now the staff uses it comfortably in their own
                roles.</p>
            </blockquote>
            <div class="q-by">
              <span class="q-icon q-initials" aria-hidden="true">MP</span>
              <span><b>Dr. Monil Parmar</b>Universal Superspeciality Hospital<br><span class="verified"><svg width="12"
                    height="12" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <path d="m5 12.5 4.5 4.5L19 7.5" stroke="#047857" stroke-width="3" stroke-linecap="round"
                      stroke-linejoin="round" />
                  </svg>Verified Healthray customer</span></span>
            </div>
            <div class="bbmh-note">
              <p>"We already had different systems and years of data, so switching was not something we wanted to do
                casually… Healthray's team worked with us through the setup and configuration, and we were able to bring
                the major workflows into one system."<br><span class="bbmh-by">CIO / IT Head, Multispecialty Hospital ·
                  Verified customer</span></p>
              <p><b>Moving your old data and training your staff are part of every setup.</b> One department switches at a
                time, so daily work doesn't stop.</p>
            </div>
          </div>
          <div class="reel-wrap">
            <div class="video" id="video">
              <video id="storyVideo" playsinline preload="none" disablepictureinpicture controlslist="nodownload"
                src="https://healthray.com/wp-content/uploads/2026/08/Universal-Hospital-Surat-Review_-How-Healthray-Streamlined-Our-Workflow.mp4#t=0.5"></video>
              <button type="button" id="playBtn" class="story-cover">
                <img src="https://i.ytimg.com/vi/VRHZ9ejnBWk/oardefault.jpg" alt="" width="1080" height="1920" loading="lazy"
                  data-fallback-src="https://i.ytimg.com/vi/VRHZ9ejnBWk/hqdefault.jpg">
                <span class="shade"></span>
                <span class="tag">Customer story</span>
                <span class="cap"><span class="watch"><i><svg width="14" height="14" viewBox="0 0 24 24"
                        aria-hidden="true">
                        <path d="M8 5v14l11-7z" fill="#fff" />
                      </svg></i>Watch his story</span><b>Divyesh Gandhi</b>Head of Operations, Universal Hospital,
                  Surat</span>
              </button>
              <button type="button" class="vt" id="storyBtn" aria-label="Pause the video" hidden>
                <svg class="i-play" viewBox="0 0 24 24" aria-hidden="true">
                  <path d="M8 5.5v13a1 1 0 0 0 1.5.86l10.4-6.5a1 1 0 0 0 0-1.72L9.5 4.64A1 1 0 0 0 8 5.5z" />
                </svg>
                <svg class="i-pause" viewBox="0 0 24 24" aria-hidden="true">
                  <rect x="6" y="5" width="4" height="14" rx="1" />
                  <rect x="14" y="5" width="4" height="14" rx="1" />
                </svg>
              </button>
            </div>
            <p class="video-alt">Universal Hospital, Surat: how Healthray streamlined their workflow.</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 3b. HOW THE SWITCH HAPPENS: placed right after the staff worry, so the "how" answers the fear (real sequence, so it is numbered) + real certification badges -->
    <section class="section sw-sec" id="switching">
      <div class="wrap">
        <div class="sec-head">
          <p class="eyebrow">Switching to Healthray</p>
          <h2 class="h2">Start in 1 to 3 weeks. Your billing does not stop.</h2>
          <p class="lead">Even a 200-bed hospital usually starts in one to three weeks. You switch one department at a
            time, so OPD and billing keep running.</p>
        </div>
        <ol class="plan">
          <li>
            <div class="n"><b>1</b></div><small>Setup</small>
            <h3>Set up and move your data</h3>
            <p>Departments, tariffs and bill formats are set up. Your old data moves over, usually in a day.</p>
          </li>
          <li>
            <div class="n"><b>2</b></div><small>Training</small>
            <h3>Train every department</h3>
            <p>Reception, nurses, pharmacy, billing and doctors practise on their own screens before anything starts.
            </p>
          </li>
          <li>
            <div class="n"><b>3</b></div><small>Start</small>
            <h3>Switch one department at a time</h3>
            <p>OPD and registration first, then IPD billing, pharmacy and lab. Any issue stays small and gets fixed
              quickly.</p>
          </li>
          <li>
            <div class="n"><b>4</b></div><small>After you start</small>
            <h3>24×7 support</h3>
            <p>In English, Hindi or Gujarati, from a team that knows hospital work.</p>
          </li>
        </ol>
      </div>
    </section>

    <!-- 7. CASE STUDIES: six hospitals, real building photo, one number each, whole row links to the case study -->
    <section class="section tint rs" id="case-studies">
      <div class="wrap">
        <div class="sec-head">
          <p class="eyebrow">Case studies</p>
          <h2 class="h2">Results from six hospitals that use Healthray</h2>
          <p class="lead">From a clinic to a 300-bed hospital. Click any one for the full story.</p>
        </div>
        <p class="swipe-hint">Swipe to see all six →</p>
        <div class="cs-rows">
          <a class="cs-row" href="https://healthray.com/case-studies/rainbow-hospital-vapi/">
            <div class="pic"><img class="rs-photo" src="https://healthray.com/wp-content/uploads/2026/10/Rainbow-Hospital-1024x577.webp" width="1024" height="577" alt="Rainbow Hospital, Vapi" loading="lazy"></div>
            <div class="in"><small>Rainbow Hospital, Vapi · 100 beds</small><b>6 hours → 3 hours</b><span>for a patient to be discharged</span><em>Read the story →</em></div>
          </a>
          <a class="cs-row" href="https://healthray.com/case-studies/shraddha-aarogya-mandir-hospital/">
            <div class="pic"><img class="rs-photo" src="https://healthray.com/wp-content/uploads/2026/10/Shraddha-Aarogya-Mandir-1024x577.webp" width="1024" height="577" alt="Shraddha Aarogya Mandir" loading="lazy"></div>
            <div class="in"><small>Shraddha Aarogya Mandir · 150 beds</small><b>25 years</b><span>of records moved</span><em>Read the story →</em></div>
          </a>
          <a class="cs-row" href="https://healthray.com/case-studies/vibrant-hospital/">
            <div class="pic"><img class="rs-photo" src="https://healthray.com/wp-content/uploads/2025/12/Vibrant-Hospital-Healthray-1024x512.webp" width="1024" height="512" alt="Vibrant Hospital, Vapi" loading="lazy"></div>
            <div class="in"><small>Vibrant Hospital, Vapi · Multispeciality</small><b>₹25 lakh</b><span>saved in a year</span><em>Read the story →</em></div>
          </a>
          <a class="cs-row" href="https://healthray.com/case-studies/lilavati-clinic/">
            <div class="pic"><img class="rs-photo" src="https://healthray.com/wp-content/uploads/2025/11/Lilavati-Clinic-Wellness-1024x490.webp" width="1024" height="490" alt="Lilavati Clinic" loading="lazy"></div>
            <div class="in"><small>Lilavati Clinic · 50+ OPD rooms</small><b>1 in 5 → 1 in 8</b><span>patients missing their appointment</span><em>Read the story →</em></div>
          </a>
          <a class="cs-row" href="https://healthray.com/case-studies/jeevan-rekha-hospital/">
            <div class="pic"><img class="rs-photo" src="https://healthray.com/wp-content/uploads/2025/12/Jeevanrekha-Hospital-case-study-1024x512.webp" width="1024" height="512" alt="Jeevan Rekha Hospital" loading="lazy"></div>
            <div class="in"><small>Jeevan Rekha Hospital · 220 beds</small><b>80% faster</b><span>lab sample processing</span><em>Read the story →</em></div>
          </a>
          <a class="cs-row" href="https://healthray.com/case-studies/jabalpur-hospital/">
            <div class="pic"><img class="rs-photo" src="https://healthray.com/wp-content/uploads/2025/11/Jabalpur-Hospital-1024x402.webp" width="1024" height="402" alt="Jabalpur Hospital" loading="lazy"></div>
            <div class="in"><small>Jabalpur Hospital · 300+ beds</small><b>14 modules</b><span>running in under two weeks</span><em>Read the story →</em></div>
          </a>
        </div>
        <p class="cs-all"><a href="https://healthray.com/case-studies/">See all case studies →</a></p>
      </div>
    </section>
    <!-- 8. TESTIMONIALS: doctor reviews in a sliding row (js/proof.js), in the style of the IPD page -->
    <section class="section rs" id="testimonials" data-rail>
      <div class="wrap">
        <div class="tm-head">
          <div class="sec-head">
            <p class="eyebrow">Testimonials</p>
            <h2 class="h2">What doctors say about Healthray</h2>
            <p class="lead">Doctors who use it in their OPD, wards and operation theatres. All of these are on our Reviews page.</p>
          </div>
          <div class="tm-nav"><button type="button" data-prev aria-label="Earlier reviews"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M12 4l-6 6 6 6" />
              </svg></button><button type="button" data-next aria-label="More reviews"><svg viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="M8 4l6 6-6 6" />
              </svg></button></div>
        </div>
        <p class="swipe-hint">Swipe for more reviews →</p>
        <div class="tm-rail" tabindex="0" aria-label="Doctor reviews">
          <figure class="tm c0">
            <figcaption><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp" alt="" loading="lazy" width="60" height="60"><span><b>Dr. Vimal Dhaduk</b><small>Gastrointestinal Surgeon, Gastron Hospital</small></span></figcaption>
            <blockquote>A surgical patient passes through many teams before going home, so coordination between departments is very important for us. Consultation, admission, investigations, OT, medicines and billing all need to stay aligned without repeated follow-ups between staff. With Healthray, <mark>these departments are connected on the same patient record</mark>.</blockquote>
          </figure>
          <figure class="tm c1">
            <figcaption><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Maharshi-Desai-150x150.webp" alt="" loading="lazy" width="60" height="60"><span><b>Dr. Maharshi Desai</b><small>Critical Care Specialist, Akshara Apollo CBCC Cancer Centre</small></span></figcaption>
            <blockquote>I'll be honest, earlier we were managing everything through different systems and it used to slow things down during rounds. Now I just open the patient and <mark>I can see what's been done, what's pending, and the latest reports in one place</mark>.</blockquote>
          </figure>
          <figure class="tm c2">
            <figcaption><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp" alt="" loading="lazy" width="60" height="60"><span><b>Dr. Ketan Rupala</b><small>Urologist, Rupala Kidney &amp; Prostate Hospital</small></span></figcaption>
            <blockquote>No two specialties work exactly the same way. We needed a system that fits our urology workflow, not the other way around. Healthray was <mark>customized based on our requirements</mark>, so it works according to our process. Whenever we face any issue, the support team is helpful and resolves it within time.</blockquote>
          </figure>
          <figure class="tm c3">
            <figcaption><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="" loading="lazy" width="60" height="60"><span><b>Dr. Arpit Gajjar</b><small>Shubh Multispeciality Hospital</small></span></figcaption>
            <blockquote>Their support team has been genuinely helpful even after implementation. We've reached out a few times during OPD hours over call or email, and <mark>they respond properly and help us sort out the issue</mark> instead of just giving a generic reply.</blockquote>
          </figure>
          <figure class="tm c4">
            <figcaption><img src="https://healthray.com/wp-content/uploads/2026/10/Dr.-Sanjeev-Desai-Lotus-Hospital.webp" alt="" loading="lazy" width="60" height="60"><span><b>Dr. Sanjeev Desai</b><small>Lotus Hospital, Valsad</small></span></figcaption>
            <blockquote>Managing different workflows across departments was challenging, especially when teams needed to coordinate information. Healthray has <mark>brought these workflows together on one platform</mark>, making it easier for our teams to access information, coordinate their work and manage daily operations.</blockquote>
          </figure>
          <figure class="tm c0">
            <figcaption><img src="https://healthray.com/wp-content/uploads/2026/10/Dr.-Rajesh-B.-Dhirawani-150x150.webp" alt="" loading="lazy" width="60" height="60"><span><b>Dr. Rajesh B. Dhirawani</b><small>Surgeon, Jabalpur Hospital &amp; Research Centre</small></span></figcaption>
            <blockquote>Healthray has given us complete patient visibility; every work operation <mark>from diagnosis to discharge can now be accessed in just one single click</mark>.</blockquote>
          </figure>
        </div>
        <div class="tm-bar"><span><b>4.8</b><i aria-hidden="true">★★★★★</i>Google</span><span><b>4.8</b><i aria-hidden="true">★★★★★</i>Capterra</span><span><b>5.0</b><i aria-hidden="true">★★★★★</i>G2</span><a href="https://healthray.com/reviews/">Read all reviews →</a></div>
      </div>
    </section>

    <!-- 9. PRICE: what decides the price, beside what is always included. No figure is shown because none is published. -->
    <section class="section tint pr" id="pricing" aria-labelledby="pricingTitle">
      <div class="wrap">
        <div class="p1-head">
          <h2 id="pricingTitle">How much does Healthray cost?</h2>
          <p class="pr-lead">There is no one fixed price. Three things decide yours.</p>
        </div>
        <div class="p1-grid">
          <ol class="p1-list">
            <li>
              <h3>How many beds you have</h3>
              <p>A 40-bed hospital pays less than a 300-bed hospital.</p>
            </li>
            <li>
              <h3>Which departments you use</h3>
              <p>OPD, IPD, pharmacy, lab and the rest. You pay for the ones your hospital needs.</p>
            </li>
            <li>
              <h3>Cloud or your own server</h3>
              <p>Healthray runs on either. You choose.</p>
            </li>
          </ol>
          <div class="p1-box">
            <h3>Included in every price</h3>
            <ul class="p1-inc">
              <li>Data migration from your old software</li>
              <li>Training your staff at your hospital</li>
              <li>Your price in writing, within 24 hours of the demo</li>
            </ul>
            <a class="btn btn-primary hr-cta-btn" href="#demo">Get a price for my hospital</a>
            <p class="pr-note">The demo is free. You do not have to buy.</p>
          </div>
        </div>
      </div>
    </section>
    <!-- 10. DATA SAFETY AND GOVERNMENT RULES: navy band. The promise and three numbers from the published data agreement (healthray.com/dpa/), with the real certificates on a white card. -->
    <section class="d1 dz" id="data-safety" aria-labelledby="dataSafetyTitle">
      <div class="wrap d1-grid">
        <div>
          <p class="dz-eyebrow">Is your data safe?</p>
          <h2 id="dataSafetyTitle">Your patient data is safe, and it is always yours</h2>
          <p class="d1-sub">We give every hospital these three promises in writing, in a data agreement made under India's <span class="nw">DPDP Act, 2023</span>.</p>
          <dl class="d1-nums">
            <div>
              <dt>30 days</dt>
              <dd>If you stop using Healthray, we give you all your data within 30 days.</dd>
            </div>
            <div>
              <dt>24 hours</dt>
              <dd>If anything goes wrong with your data, we tell you within 24 hours.</dd>
            </div>
            <div>
              <dt>In India</dt>
              <dd>Your data stays in India, or on a computer inside your hospital.</dd>
            </div>
          </dl>
          <ul class="d1-safe" aria-label="How your data is kept safe">
            <li>
              <h3>Role-based access</h3>
              <p>Each staff member has their own login with OTP and sees only the screens for their job.</p>
            </li>
            <li>
              <h3>Audit trail</h3>
              <p>Every record opened and every change made is saved with the person's name and the time.</p>
            </li>
            <li>
              <h3>Encryption</h3>
              <p>Your data is encrypted while it travels and while it is stored.</p>
            </li>
            <li>
              <h3>Cloud backups</h3>
              <p>Regular backups on cloud servers in India, so your data can be restored.</p>
            </li>
          </ul>
          <div class="d1-go"><a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a><a class="d1-ghost" href="https://healthray.com/dpa/">Read the full promise</a></div>
        </div>
        <div class="d1-card">
          <h3>Approved and certified</h3>
          <div class="dz-badges">
            <figure><img src="https://healthray.com/wp-content/uploads/2026/07/NABH-ehr-certified-150x150.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>NABH certified</figcaption>
            </figure>
            <figure><img src="https://healthray.com/wp-content/uploads/2026/09/ABDM-Logo-Healthray.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>ABDM compliant</figcaption>
            </figure>
            <figure><img src="https://healthray.com/wp-content/uploads/2025/07/NHA.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>NHA approved</figcaption>
            </figure>
            <figure><img src="https://healthray.com/wp-content/uploads/2026/03/abha-healthray-150x150.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>Makes ABHA numbers</figcaption>
            </figure>
            <figure><img src="https://healthray.com/wp-content/uploads/2026/03/iso-27001-healthray-150x150.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>ISO 27001</figcaption>
            </figure>
            <figure><img src="https://healthray.com/wp-content/uploads/2026/03/Hipaa-healthray-150x150.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>HIPAA-aligned</figcaption>
            </figure>
            <figure><img src="https://healthray.com/wp-content/uploads/2026/03/hl7-healthray-150x150.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>HL7, lab machines</figcaption>
            </figure>
            <figure><img src="https://healthray.com/wp-content/uploads/2026/10/NHCX-logo.webp" alt="" loading="lazy" width="66" height="66">
              <figcaption>NHCX integrated</figcaption>
            </figure>
          </div>
        </div>
      </div>
    </section>

    <!-- 11. QUESTIONS: a small demo box and 11 questions. The FAQ structured data below repeats exactly these questions and answers. -->
    <section class="section tint fq" id="faq" aria-labelledby="faqTitle">
      <div class="wrap f1-grid">
        <div class="f1-side">
          <h2 id="faqTitle">Common questions about hospital management software</h2>
          <p class="fq-lead">Short answers. If yours is not here, ask us.</p>
          <div class="f1-talk">
            <h3>Did not find your question?</h3>
            <p>Ask us in a free demo.</p>
            <a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a>
          </div>
        </div>
        <div class="fq-list">
          <details class="fq-q">
            <summary>What is hospital management software?</summary>
            <p>One software that every part of a hospital works in: reception, doctors, wards, lab, pharmacy and billing. A patient is entered once, and every department sees the same record.</p>
          </details>
          <details class="fq-q">
            <summary>Is HMS the same as HIS and HMIS?</summary>
            <p>Yes. Hospital management system (HMS), hospital information system (HIS) and HMIS are different names for the same software. Healthray is all of them.</p>
          </details>
          <details class="fq-q" open>
            <summary>What is the price of Healthray?</summary>
            <p>There is no one fixed price. It depends on your number of beds, the departments you want, and whether it runs online or on your own server. You get your price in writing within 24 hours of the demo.</p>
          </details>
          <details class="fq-q">
            <summary>How many days does it take to start?</summary>
            <p>Most hospitals start in 1 to 3 weeks. Rainbow Hospital in Vapi, with 100 beds, started in 14 working days.</p>
          </details>
          <details class="fq-q">
            <summary>Can you move our data from our old software?</summary>
            <p>Yes. Data migration is included in the price, and your old data usually moves over in a day. Shraddha Aarogya Mandir moved 25 years of records to Healthray.</p>
          </details>
          <details class="fq-q">
            <summary>Does it work for TPA, cashless and Ayushman (PMJAY) patients?</summary>
            <p>Yes. The insurance company and policy number are noted at admission. All cashless patients come in one list with their claim status. Ayushman patients are billed in the same software.</p>
          </details>
          <details class="fq-q">
            <summary>Cloud or on-premise: where does Healthray run?</summary>
            <p>You choose. On the cloud, your data is kept on servers in India and you open Healthray from any computer with internet. If the internet stops, connect a mobile hotspot and keep working. On-premise, Healthray runs on a server inside your hospital, on your own network.</p>
          </details>
          <details class="fq-q">
            <summary>Do we need to buy new computers?</summary>
            <p>No. It runs in the internet browser on the computers you already have. It also connects to your lab machines and Tally.</p>
          </details>
          <details class="fq-q">
            <summary>Is there a Healthray mobile app?</summary>
            <p>Yes. Doctors use the Healthray DR app on Android and iPhone to see their appointments, a patient's history and lab reports, and to write prescriptions. Patients can book appointments with the Healthray for Patients app.</p>
            <div class="fq-apps">
              <div><b>Doctor app</b><a href="https://play.google.com/store/apps/details?id=com.healthray.doctor" target="_blank" rel="noopener">Android <span class="vh">(Google Play)</span></a><a href="https://apps.apple.com/in/app/healthray-dr-for-doctors/id1513592834" target="_blank" rel="noopener">iPhone <span class="vh">(App Store)</span></a></div>
              <div><b>Patient app</b><a href="https://play.google.com/store/apps/details?id=com.healthray.patient" target="_blank" rel="noopener">Android <span class="vh">(Google Play)</span></a><a href="https://apps.apple.com/in/app/healthray-for-patients/id6470002190" target="_blank" rel="noopener">iPhone <span class="vh">(App Store)</span></a></div>
            </div>
          </details>
          <details class="fq-q">
            <summary>Is it only for big hospitals?</summary>
            <p>No. Nursing homes, clinics, small hospitals and medical colleges with 1,000+ beds all use it. You start with the departments you need and add more later.</p>
          </details>
          <details class="fq-q">
            <summary>Can one Healthray run all our branches?</summary>
            <p>Yes. Each branch has its own OPD, wards, pharmacy and billing. Rates and bill formats can stay the same in every branch, and the owner sees the reports of all branches together.</p>
          </details>
        </div>
      </div>
    </section>
    <script type="application/ld+json" id="faq-schema">
      {
        "@context": "https://schema.org",
        "@type": "FAQPage",
        "mainEntity": [{
            "@type": "Question",
            "name": "What is hospital management software?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "One software that every part of a hospital works in: reception, doctors, wards, lab, pharmacy and billing. A patient is entered once, and every department sees the same record."
            }
          },
          {
            "@type": "Question",
            "name": "Is HMS the same as HIS and HMIS?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Yes. Hospital management system (HMS), hospital information system (HIS) and HMIS are different names for the same software. Healthray is all of them."
            }
          },
          {
            "@type": "Question",
            "name": "What is the price of Healthray?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "There is no one fixed price. It depends on your number of beds, the departments you want, and whether it runs online or on your own server. You get your price in writing within 24 hours of the demo."
            }
          },
          {
            "@type": "Question",
            "name": "How many days does it take to start?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Most hospitals start in 1 to 3 weeks. Rainbow Hospital in Vapi, with 100 beds, started in 14 working days."
            }
          },
          {
            "@type": "Question",
            "name": "Can you move our data from our old software?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Yes. Data migration is included in the price, and your old data usually moves over in a day. Shraddha Aarogya Mandir moved 25 years of records to Healthray."
            }
          },
          {
            "@type": "Question",
            "name": "Does it work for TPA, cashless and Ayushman (PMJAY) patients?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Yes. The insurance company and policy number are noted at admission. All cashless patients come in one list with their claim status. Ayushman patients are billed in the same software."
            }
          },
          {
            "@type": "Question",
            "name": "Cloud or on-premise: where does Healthray run?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "You choose. On the cloud, your data is kept on servers in India and you open Healthray from any computer with internet. If the internet stops, connect a mobile hotspot and keep working. On-premise, Healthray runs on a server inside your hospital, on your own network."
            }
          },
          {
            "@type": "Question",
            "name": "Do we need to buy new computers?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "No. It runs in the internet browser on the computers you already have. It also connects to your lab machines and Tally."
            }
          },
          {
            "@type": "Question",
            "name": "Is there a Healthray mobile app?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Yes. Doctors use the Healthray DR app on Android and iPhone to see their appointments, a patient's history and lab reports, and to write prescriptions. Patients can book appointments with the Healthray for Patients app."
            }
          },
          {
            "@type": "Question",
            "name": "Is it only for big hospitals?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "No. Nursing homes, clinics, small hospitals and medical colleges with 1,000+ beds all use it. You start with the departments you need and add more later."
            }
          },
          {
            "@type": "Question",
            "name": "Can one Healthray run all our branches?",
            "acceptedAnswer": {
              "@type": "Answer",
              "text": "Yes. Each branch has its own OPD, wards, pharmacy and billing. Rates and bill formats can stay the same in every branch, and the owner sees the reports of all branches together."
            }
          }
        ]
      }
    </script>

    <!-- 12. CLOSING: the real doctors from this page, one line, one button to the demo form at the top. -->
    <section class="section n2 cz" id="book-demo" aria-labelledby="bookDemoTitle">
      <div class="wrap">
        <div class="n2-faces" aria-hidden="true"><img src="https://healthray.com/wp-content/uploads/2026/10/Dr.-Hinal-Parmar-Rainbow-Hospital-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2026/10/Dr.-Deepika-Singhavi-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2025/12/Dr.-Bhaumik-Rathore-Vibrant-Multispecialty-Hospital-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2025/11/Dr.-Gunjan-Desai-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2026/10/Dr.-Ishita-Das-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2026/10/Dr.-Rajesh-B.-Dhirawani-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Vimal-Dhaduk-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Maharshi-Desai-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2024/04/Dr.-Ketan-Rupala-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Arpit-gajjar-150x150.webp" alt="" loading="lazy" width="68" height="68"><img src="https://healthray.com/wp-content/uploads/2025/10/Dr.-Monil-Parmar-150x150.webp" alt="" loading="lazy" width="68" height="68"></div>
        <h2 id="bookDemoTitle">Every doctor on this page started with a 30-minute demo</h2>
        <p class="n2-sub">Yours is free, on a video call, set up for a hospital your size. You get your price in writing within 24 hours.</p>
        <div class="cz-go center"><a class="btn btn-primary hr-cta-btn cz-btn" href="#demo">Book a free demo</a><span class="cz-or">or call <a href="tel:+919714874435">+91 97148 74435</a></span></div>
      </div>
    </section>
  </div>
  <div class="mcta" id="mcta"><a class="btn btn-primary hr-cta-btn" href="#demo">Book a free demo</a></div>
</div>