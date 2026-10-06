<?php add_shortcode('lab_solution', 'healthray_solution_shortcode');

function healthray_solution_shortcode($atts)
{
	// Default attributes
	$atts = shortcode_atts(array(
		'title'    => 'Boost Your Healthcare Practice',
		'text' => 'Discover how Healthray can streamline your workflow and improve patient care.',
		'button_text' => 'Book a Free Demo Today!',
	), $atts, 'healthray_cta');

	ob_start(); ?>

<style>
  

  .hr-lims-section {
    --hr-blue:       #1565C0;
    --hr-blue-mid:   #1976D2;
    --hr-blue-light: #E3F2FD;
    --hr-blue-pale:  #F0F7FF;
    --hr-accent:     #FF6D00; 
    --hr-text:       #1A2340; 
    --hr-muted:      #4A5568; 
    --hr-border:     #BBDEFB; 
    --hr-white:      #FFFFFF;
    --hr-shadow:     0 2px 12px rgba(21,101,192,.10);
    --hr-radius:     10px;
  }

  .hr-lims-title { text-align: center; font-size: clamp(20px, 3vw, 30px); font-weight: 700; color: var(--hr-blue); margin-bottom: 36px; line-height: 1.3; }
  .hr-lims-title span { color: var(--hr-blue); border-bottom: 3px solid var(--hr-accent); padding-bottom: 2px; }

  .hr-card { background: var(--hr-white); border: 1.5px solid var(--hr-border); border-radius: var(--hr-radius); box-shadow: var(--hr-shadow); overflow: hidden; }

  /* Card header bar */
  .hr-card-head { display: flex; align-items: center; gap: 8px; background: var(--hr-blue-light); padding: 10px 16px; border-bottom: 1.5px solid var(--hr-border); }
  .hr-card-head .hr-icon { display: flex; align-items: center; justify-content: center; width: 28px; height: 28px; background: var(--hr-blue); border-radius: 6px; flex-shrink: 0; }
  .hr-card-head .hr-icon svg { width: 16px; height: 16px; fill: #fff; }
  .hr-card-head h3 { font-size: 15px; font-weight: 700; color: var(--hr-blue); line-height: 1.25; margin: 0; }

  /* Card body */
  .hr-card-body { padding: 14px 16px 16px; font-size: 13.5px; color: var(--hr-muted); line-height: 1.65; }

  .hr-top-card { max-width: 520px; margin: 0 auto; }

  .hr-connector { display: flex; flex-direction: column; align-items: center; padding: 6px 0; }
  .hr-connector-line { width: 2px; background: var(--hr-blue); opacity: .35; flex: 1; min-height: 22px; }
  .hr-connector-arrow { width: 0; height: 0; border-left: 7px solid transparent; border-right: 7px solid transparent; border-top: 9px solid var(--hr-blue); opacity: .5; }

  .hr-branch { position: relative; height: 48px; margin: 0 auto; width: 100%; max-width: 900px; }
  .hr-branch svg { width: 100%; height: 100%; overflow: visible; }

  .hr-grid { display: grid; gap: 20px; width: 100%; }
  .hr-grid-3 { grid-template-columns: repeat(3, 1fr); } .hr-grid-2 { grid-template-columns: repeat(2, 1fr); max-width: 80%; margin: 0 auto; } 
  .hr-bottom-card { max-width: 520px; margin: 0 auto; }

  @media (max-width: 767px) { 
    .hr-grid-3, .hr-grid-2 { grid-template-columns: 1fr; }
    .hr-branch { display: none; }
    .hr-mobile-gap { height: 12px; }
  }
  @media (min-width: 480px) and (max-width: 767px) {
    .hr-grid-2 { grid-template-columns: repeat(2,1fr); }
  }

  .hr-card { transition: transform .18s ease, box-shadow .18s ease; }
  .hr-card:hover { transform: translateY(-3px); box-shadow: 0 6px 24px rgba(21,101,192,.16); }
  </style>
</head>
<body id="artifacts-component-root-html">

<section class="hr-lims-section">
  <div class="hr-top-card">
    <div class="hr-card">
      <div class="hr-card-head">
        <div class="hr-icon">
          <!-- Flask / sample icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 3v8L4.5 18A2 2 0 006.3 21h11.4a2 2 0 001.8-3L15 11V3M9 3h6M9 3H7m8 0h2"></path>
            <path d="M8 21h8" stroke-width="0" fill="none"></path>
          </svg>
        </div>
        <h3>Sample Lifecycle Management</h3>
      </div>
      <div class="hr-card-body">
        Manage every step of a diagnostic sample's lifecycle with automated sample tracking in a lab information management system.
      </div>
    </div>
  </div>
  <!-- Branch down → 3 columns -->
  <!-- Desktop branch SVG -->
  <div class="hr-branch" aria-hidden="true" style="">
    <svg viewBox="0 0 900 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <!-- stem down -->
      <line x1="450" y1="0" x2="450" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <!-- horizontal bar -->
      <line x1="150" y1="24" x2="750" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <!-- three drops -->
      <line x1="150" y1="24" x2="150" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="450" y1="24" x2="450" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="750" y1="24" x2="750" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <!-- arrowheads -->
      <polygon points="144,42 156,42 150,48" fill="#1565C0" fill-opacity=".5"></polygon>
      <polygon points="444,42 456,42 450,48" fill="#1565C0" fill-opacity=".5"></polygon>
      <polygon points="744,42 756,42 750,48" fill="#1565C0" fill-opacity=".5"></polygon>
    </svg>
  </div>
  <!-- Mobile simple connector -->
  <div class="hr-connector" style="display: none;" aria-hidden="true" id="mob-conn-1">
    <div class="hr-connector-line" style="min-height:16px"></div>
    <div class="hr-connector-arrow"></div>
  </div>

  <!-- ② Three cards -->
  <div class="hr-grid hr-grid-3">

    <!-- Analyzer Integrations -->
    <div class="hr-card">
      <div class="hr-card-head">
        <div class="hr-icon">
          <!-- Circuit / analyzer icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <rect x="5" y="5" width="14" height="14" rx="2" fill="none" stroke="#fff" stroke-width="2"></rect>
            <path d="M9 9h6M9 12h6M9 15h3" stroke="#fff" stroke-width="1.5" stroke-linecap="round" fill="none"></path>
          </svg>
        </div>
        <h3>Analyzer Integrations</h3>
      </div>
      <div class="hr-card-body">
        Seamlessly integrate with various diagnostic analyzers to import test results automatically and avoid errors in the LIMS software.
      </div>
    </div>

    <!-- Workflow Automation -->
    <div class="hr-card">
      <div class="hr-card-head">
        <div class="hr-icon">
          <!-- Gear / automation icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M12 15a3 3 0 100-6 3 3 0 000 6z" fill="none" stroke="#fff" stroke-width="2"></path>
            <path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z" fill="none" stroke="#fff" stroke-width="1.5"></path>
          </svg>
        </div>
        <h3>Workflow Automation</h3>
      </div>
      <div class="hr-card-body">
        Automate various tasks of a technician or a lab technician in workflow automation in the lab information management system.
      </div>
    </div>

    <!-- Quality Control -->
    <div class="hr-card">
      <div class="hr-card-head">
        <div class="hr-icon">
          <!-- Checkmark shield icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M9 12l2 2 4-4" fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"></path>
            <path d="M12 2l7 4v6c0 5-4 8-7 10C8 20 5 17 5 12V6l7-4z" fill="none" stroke="#fff" stroke-width="2"></path>
          </svg>
        </div>
        <h3>Quality Control</h3>
      </div>
      <div class="hr-card-body">
        Ensure the accuracy of a diagnostic test with quality control through automated monitoring and validation in the pathology lab software.
      </div>
    </div>

  </div>

  <!-- Branch 3 → 2 columns -->
  <div class="hr-branch" aria-hidden="true" style="max-width: 700px;">
    <svg viewBox="0 0 700 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <!-- three inputs meet -->
      <line x1="117" y1="0" x2="117" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="350" y1="0" x2="350" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="583" y1="0" x2="583" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="117" y1="24" x2="583" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <!-- two drops -->
      <line x1="233" y1="24" x2="233" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="466" y1="24" x2="466" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <polygon points="227,42 239,42 233,48" fill="#1565C0" fill-opacity=".5"></polygon>
      <polygon points="460,42 472,42 466,48" fill="#1565C0" fill-opacity=".5"></polygon>
    </svg>
  </div>
  <div class="hr-connector" style="display: none;" aria-hidden="true" id="mob-conn-2">
    <div class="hr-connector-line" style="min-height:16px"></div>
    <div class="hr-connector-arrow"></div>
  </div>

  <!-- ③ Two cards -->
  <div class="hr-grid hr-grid-2">

    <!-- Inventory & Reagent Management -->
    <div class="hr-card">
      <div class="hr-card-head">
        <div class="hr-icon">
          <!-- Box / inventory icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M21 16V8a2 2 0 00-1-1.73l-7-4a2 2 0 00-2 0l-7 4A2 2 0 003 8v8a2 2 0 001 1.73l7 4a2 2 0 002 0l7-4A2 2 0 0021 16z" fill="none" stroke="#fff" stroke-width="2"></path>
            <polyline points="3.27 6.96 12 12.01 20.73 6.96" fill="none" stroke="#fff" stroke-width="2"></polyline>
            <line x1="12" y1="22.08" x2="12" y2="12" stroke="#fff" stroke-width="2"></line>
          </svg>
        </div>
        <h3>Inventory &amp; Reagent Management</h3>
      </div>
      <div class="hr-card-body">
        Monitor lab consumables with real-time stock tracking, low-inventory alerts, and automated reagent expiry in your lab information management system.
      </div>
    </div>

    <!-- Results & Reporting Management -->
    <div class="hr-card">
      <div class="hr-card-head">
        <div class="hr-icon">
          <!-- File / report icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z" fill="none" stroke="#fff" stroke-width="2"></path>
            <polyline points="14 2 14 8 20 8" fill="none" stroke="#fff" stroke-width="2"></polyline>
            <line x1="8" y1="13" x2="16" y2="13" stroke="#fff" stroke-width="2" stroke-linecap="round"></line>
            <line x1="8" y1="17" x2="16" y2="17" stroke="#fff" stroke-width="2" stroke-linecap="round"></line>
            <polyline points="10 9 9 9 8 9" fill="none" stroke="#fff" stroke-width="2"></polyline>
          </svg>
        </div>
        <h3>Results &amp; Reporting Management</h3>
      </div>
      <div class="hr-card-body">
        Generate structured diagnostic reports instantly with automated validation, digital sharing, and centralized storage using lab reporting software.
      </div>
    </div>

  </div>

  <!-- Branch 2 → 1 (converge) -->
  <div class="hr-branch" aria-hidden="true" style="max-width: 540px;">
    <svg viewBox="0 0 540 48" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
      <line x1="135" y1="0" x2="135" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="405" y1="0" x2="405" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="135" y1="24" x2="405" y2="24" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <line x1="270" y1="24" x2="270" y2="48" stroke="#1565C0" stroke-width="2" stroke-opacity=".35"></line>
      <polygon points="264,42 276,42 270,48" fill="#1565C0" fill-opacity=".5"></polygon>
    </svg>
  </div>
  <div class="hr-connector" style="display: none;" aria-hidden="true" id="mob-conn-3">
    <div class="hr-connector-line" style="min-height:16px"></div>
    <div class="hr-connector-arrow"></div>
  </div>

  <!-- ④ Billing & Package Management -->
  <div class="hr-bottom-card">
    <div class="hr-card">
      <div class="hr-card-head">
        <div class="hr-icon">
          <!-- Credit card / billing icon -->
          <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <rect x="2" y="5" width="20" height="14" rx="2" fill="none" stroke="#fff" stroke-width="2"></rect>
            <line x1="2" y1="10" x2="22" y2="10" stroke="#fff" stroke-width="2"></line>
            <line x1="6" y1="15" x2="10" y2="15" stroke="#fff" stroke-width="2" stroke-linecap="round"></line>
          </svg>
        </div>
        <h3>Billing &amp; Package Management</h3>
      </div>
      <div class="hr-card-body">
        Simplify test billing and manage diagnostic packages with automated invoicing and integrated payment tracking in your lab system.
      </div>
    </div>
  </div>
</section>

<script>
/* On mobile, hide SVG branch connectors and show simple vertical ones */
(function(){
  function applyMobile(){
    var isMobile = window.innerWidth < 768;
    document.querySelectorAll('.hr-branch').forEach(function(el){
      el.style.display = isMobile ? 'none' : '';
    });
    ['mob-conn-1','mob-conn-2','mob-conn-3'].forEach(function(id){
      var el = document.getElementById(id);
      if(el) el.style.display = isMobile ? 'flex' : 'none';
    });
  }
  applyMobile();
  window.addEventListener('resize', applyMobile);
})();
</script>

<?php
	return ob_get_clean();
}