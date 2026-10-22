<?php /* Template name: DPA */ ?>
<style>
  .hr-dpa { 
     --hr-navy: #1b2a7a;
     --hr-navy-dark: #0f1a5c;
     --hr-blue: #1a5fd6;
     --hr-head-bg: #e4effc;
     --hr-head-border: #c9dcf5;
     --hr-mark-bg: #eef0f6;
     --hr-text: #33374a;
     --hr-muted: #5d6278;
     --hr-line: #e3e7ef;
     color: var(--hr-text);
     font-size: 17px;
     padding: 48px 0 72px;
   }
  .hr-dpa *,
  .hr-dpa *::before,
  .hr-dpa *::after { box-sizing: border-box; }
  .hr-dpa p { margin: 0 0 14px; }
  .hr-dpa a { color: var(--hr-blue); text-decoration: none; }
  .hr-dpa a:hover { text-decoration: underline; }
  .hr-dpa a:focus-visible { outline: 2px solid var(--hr-blue); outline-offset: 2px; border-radius: 2px; }
  .hr-dpa strong.hr-term { color: var(--hr-navy); font-weight: 600; }
  .hr-dpa .hr-title { font-size: clamp(30px, 4.4vw, 44px); line-height: 1.2; font-weight: 600; color: var(--hr-navy); margin: 0 0 28px; letter-spacing: -.01em; }
  .hr-dpa .hr-meta { font-size: 15px; color: var(--hr-muted); margin: -16px 0 28px; }
  .hr-dpa .hr-intro { background: #fff; border: 1px solid var(--hr-line); border-left: 4px solid var(--hr-navy); border-radius: 16px; padding: 28px 30px 16px; margin-bottom: 40px; text-align: justify; }
  .hr-dpa .hr-toc { border: 1px solid var(--hr-line); border-radius: 12px; padding: 20px 26px; margin-bottom: 40px; background: #fbfcfe; }
  .hr-dpa .hr-toc h2 { font-size: 18px; font-weight: 600; color: var(--hr-navy); margin: 0 0 10px; }
  .hr-dpa .hr-toc ol { margin: 0; padding-left: 22px; columns: 2; column-gap: 40px; font-size: 15.5px; }
  .hr-dpa .hr-toc li { break-inside: avoid; margin-bottom: 2px; }
  .hr-dpa .hr-sec { border: 1px solid var(--hr-head-border); border-radius: 14px; margin-bottom: 28px; overflow: hidden; background: #fff; scroll-margin-top: 110px; }
  .hr-dpa .hr-sec-head { display: flex; align-items: center; gap: 22px; background: var(--hr-head-bg); border-bottom: 1px solid var(--hr-head-border); padding: 16px 22px; margin: 0; font-size: 19px; font-weight: 500; color: #1f2433; }
  .hr-dpa .hr-num { flex: 0 0 auto; width: 32px; height: 32px; border-radius: 7px; background: var(--hr-navy-dark); color: #fff; font-size: 13px; font-weight: 700; display: flex; align-items: center; justify-content: center; padding-top: 3px; }
  .hr-dpa .hr-sec-body { padding: 24px 18px 12px; }
  .hr-dpa .hr-item { display: flex; gap: 10px; align-items: flex-start; margin-bottom: 14px; }
  .hr-dpa .hr-mark { flex: 0 0 auto; min-width: 26px; padding: 1px 6px; margin-top: 3px; border-radius: 5px; background: var(--hr-mark-bg); color: var(--hr-navy); font-size: 12.5px; font-weight: 600; line-height: 22px; text-align: center; }
  .hr-dpa .hr-item-body { flex: 1 1 auto; min-width: 0; text-align: justify; }
  .hr-dpa .hr-sub { margin-top: 12px; padding-left: 18px; }
  .hr-dpa .hr-table-wrap { overflow-x: auto; margin: 6px 0 16px; border: 1px solid var(--hr-line); border-radius: 10px; }
  .hr-dpa table { border-collapse: collapse; width: 100%; font-size: 15.5px; line-height: 1.6; }
  .hr-dpa th,
  .hr-dpa td { padding: 11px 14px; text-align: left; vertical-align: top; border-bottom: 1px solid var(--hr-line); }
  .hr-dpa th { background: var(--hr-head-bg); color: var(--hr-navy); font-weight: 600; white-space: nowrap; }
  .hr-dpa tr:last-child td { border-bottom: 0; }
  .hr-dpa td:first-child { font-weight: 500; color: #1f2433; min-width: 180px; }
  .hr-dpa .hr-contact { display: grid; grid-template-columns: max-content 1fr; gap: 6px 18px; background: #fbfcfe; border: 1px solid var(--hr-line); border-radius: 10px; padding: 16px 20px; margin: 4px 0 16px; }
  .hr-dpa .hr-contact dt { font-weight: 600; color: var(--hr-navy); }
  .hr-dpa .hr-contact dd { margin: 0; }
  @media (max-width:720px) {
    .hr-dpa { font-size: 16px; padding: 32px 0 56px; }
    .hr-dpa .hr-intro { padding: 22px 18px 10px; text-align: left; }
    .hr-dpa .hr-toc ol { columns: 1; }
    .hr-dpa .hr-sec-head { gap: 14px; padding: 14px 16px; font-size: 17.5px; }
    .hr-dpa .hr-sec-body { padding: 18px 12px 6px; }
    .hr-dpa .hr-sub { padding-left: 6px; }
    .hr-dpa .hr-item-body { text-align: left; }
    .hr-dpa .hr-contact { grid-template-columns: 1fr; } 
  }
  @media print {
    .hr-dpa .hr-toc { display: none; }
    .hr-dpa .hr-sec { break-inside: auto; border-color: #bbb; } 
  }
  .hr-dpa .hr-title,
  .hr-dpa .hr-sec-head,
  .hr-dpa .hr-toc h2 { text-transform: none; }
  .hr-dpa .hr-sec-head,
  .hr-dpa .hr-toc h2 { letter-spacing: normal; }
</style>
<div class="container">
  <div class="hr-dpa">

    <h1 class="hr-title">Healthray Data Protection Addendum</h1>
    <p class="hr-meta">Last updated: October 2026 &nbsp;|&nbsp; Version 2.0 (replaces the version dated February 2025)</p>

    <div class="hr-intro">
      <p>This Data Protection Addendum (&ldquo;<strong class="hr-term">Addendum</strong>&rdquo;) between Healthray Technologies Pvt. Ltd. (&ldquo;<strong class="hr-term">Healthray</strong>&rdquo;) and the Customer (as defined in the Agreement) forms part of the Healthray Technologies Pvt. Ltd. Terms of Service set forth at <a href="https://healthray.com/terms-condition/">https://healthray.com/terms-condition/</a> or such other written or electronic agreement incorporating this Addendum, in each case governing the Customer&rsquo;s access to and use of the Services (the &ldquo;<strong class="hr-term">Agreement</strong>&rdquo;).</p>
      <p>This Addendum is made under, and is intended to satisfy, Section 8(2) of the Digital Personal Data Protection Act, 2023, which requires a Data Fiduciary to engage a Data Processor only under a valid contract, and Rule 6 of the Digital Personal Data Protection Rules, 2025, which requires such contract to provide for reasonable security safeguards.</p>
      <p>The Customer enters into this Addendum on behalf of itself and any Affiliates authorised to use the Services under the Agreement that have not entered into a separate contractual arrangement with Healthray. For the purposes of this Addendum only, and except where otherwise indicated, references to &ldquo;Customer&rdquo; include the Customer and such Affiliates.</p>
      <p>The Parties agree that the terms and conditions set out below are added as an Addendum to the Agreement.</p>
    </div>

    <nav class="hr-toc" aria-label="Contents">
      <h2>Contents</h2>
      <ol>
        <li><a href="#dpa-1">Definitions</a></li>
        <li><a href="#dpa-2">Scope and application</a></li>
        <li><a href="#dpa-3">Roles of the Parties</a></li>
        <li><a href="#dpa-4">Customer obligations</a></li>
        <li><a href="#dpa-5">Healthray obligations</a></li>
        <li><a href="#dpa-6">Security safeguards</a></li>
        <li><a href="#dpa-7">Sub-processors</a></li>
        <li><a href="#dpa-8">Data location and transfers outside India</a></li>
        <li><a href="#dpa-9">Artificial intelligence features</a></li>
        <li><a href="#dpa-10">Personal data breach</a></li>
        <li><a href="#dpa-11">Assistance with Data Principal rights</a></li>
        <li><a href="#dpa-12">Children and persons with disability</a></li>
        <li><a href="#dpa-13">Impact assessments and Significant Data Fiduciaries</a></li>
        <li><a href="#dpa-14">Audit and information</a></li>
        <li><a href="#dpa-15">Retention, return and deletion</a></li>
        <li><a href="#dpa-16">Liability and indemnity</a></li>
        <li><a href="#dpa-17">Precedence, severability and changes</a></li>
        <li><a href="#dpa-18">Governing law and disputes</a></li>
        <li><a href="#dpa-19">Data Protection Officer and grievance contact</a></li>
        <li><a href="#dpa-a1">Annex 1 &ndash; Details of processing</a></li>
        <li><a href="#dpa-a2">Annex 2 &ndash; Sub-processors</a></li>
        <li><a href="#dpa-a3">Annex 3 &ndash; Security measures</a></li>
      </ol>
    </nav>

    <!-- 1 -->
    <section class="hr-sec" id="dpa-1">
      <h2 class="hr-sec-head"><span class="hr-num">1</span>Definitions</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">In this Addendum, the following terms shall have the meanings set out below and cognate terms shall be construed accordingly:
            <div class="hr-sub">
              <div class="hr-item"><span class="hr-mark">A.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Act</strong>&rdquo; or &ldquo;<strong class="hr-term">DPDP Act</strong>&rdquo; means the Digital Personal Data Protection Act, 2023, as amended from time to time;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">B.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Rules</strong>&rdquo; or &ldquo;<strong class="hr-term">DPDP Rules</strong>&rdquo; means the Digital Personal Data Protection Rules, 2025 notified under the Act, as amended from time to time;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">C.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Data Protection Laws</strong>&rdquo; means the Act, the Rules, the Information Technology Act, 2000 and the rules made under it (to the extent in force), directions issued by the Indian Computer Emergency Response Team (&ldquo;<strong class="hr-term">CERT-In</strong>&rdquo;) under Section 70B of the Information Technology Act, 2000, and any other law in force in India that governs the processing of personal data or health records, including the Aadhaar (Targeted Delivery of Financial and Other Subsidies, Benefits and Services) Act, 2016 and the Mental Healthcare Act, 2017, each as amended or replaced;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">D.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Affiliate</strong>&rdquo; means an entity that owns or controls, is owned or controlled by, or is under common control or ownership with either the Customer or Healthray (as the context allows), where control means the possession, directly or indirectly, of the power to direct or cause the direction of the management and policies of an entity, whether through ownership of voting securities, by contract or otherwise;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">E.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Authorised User</strong>&rdquo; means any doctor, nurse, employee, consultant or other person whom the Customer permits to access the Services;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">F.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Board</strong>&rdquo; means the Data Protection Board of India established under Section 18 of the Act;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">G.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Customer Personal Data</strong>&rdquo; means any Personal Data provided or made available by the Customer or its Authorised Users to Healthray, or collected or generated by Healthray on behalf of the Customer, which is processed by Healthray to perform the Services, as further described in Annex 1;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">H.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Health Data</strong>&rdquo; means Customer Personal Data relating to the physical or mental health of a Data Principal, including medical history, diagnoses, prescriptions, laboratory and radiology reports, clinical notes and treatment records;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">I.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">ABDM</strong>&rdquo; means the Ayushman Bharat Digital Mission of the Ministry of Health and Family Welfare, implemented by the National Health Authority, together with its Health Data Management Policy and related standards;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">J.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Instructions</strong>&rdquo; means the Agreement, this Addendum, the Customer&rsquo;s configuration and use of the Services&rsquo; features and functionality, and any other documented instruction from the Customer;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">K.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Security Incident</strong>&rdquo; means a personal data breach, as defined in Section 2(u) of the Act, affecting Customer Personal Data processed by Healthray or its Sub-processors. Unsuccessful attempts or activities that do not compromise the security of Customer Personal Data, such as unsuccessful login attempts, pings, port scans or blocked network attacks, are not Security Incidents;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">L.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Services</strong>&rdquo; means the cloud-based hospital management, electronic medical record and related software services, mobile applications and support supplied by Healthray to the Customer under the Agreement;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">M.</span>
                <div class="hr-item-body">&ldquo;<strong class="hr-term">Sub-processor</strong>&rdquo; means any third party engaged by Healthray that processes Customer Personal Data.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">The terms &ldquo;Data Fiduciary&rdquo;, &ldquo;Data Principal&rdquo;, &ldquo;Data Processor&rdquo;, &ldquo;Consent Manager&rdquo;, &ldquo;Significant Data Fiduciary&rdquo;, &ldquo;Personal Data&rdquo;, &ldquo;digital personal data&rdquo;, &ldquo;processing&rdquo;, &ldquo;personal data breach&rdquo; and &ldquo;child&rdquo; have the meanings given to them in Section 2 of the Act.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Capitalised terms not otherwise defined in this Addendum have the meanings given to them in the Agreement.</div>
        </div>
      </div>
    </section>

    <!-- 2 -->
    <section class="hr-sec" id="dpa-2">
      <h2 class="hr-sec-head"><span class="hr-num">2</span>Scope and application</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">This Addendum applies to Healthray&rsquo;s processing of Customer Personal Data under the Agreement, including Health Data, to the extent such processing is subject to Data Protection Laws.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">The purpose of processing under this Addendum is the provision of the Services under the Agreement and any Order Form(s). The subject matter and details of processing are set out in Annex 1. The Parties may make reasonable amendments to Annex 1 by mutual written agreement, as reasonably necessary to reflect the Services or to meet the requirements of Data Protection Laws.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">This Addendum does not apply to Personal Data that Healthray processes for its own purposes, such as Customer account administration, billing contacts, website visitors, sales enquiries and Healthray&rsquo;s own employees. For that processing Healthray acts as a Data Fiduciary, as described in the Healthray Privacy Policy.</div>
        </div>
      </div>
    </section>

    <!-- 3 -->
    <section class="hr-sec" id="dpa-3">
      <h2 class="hr-sec-head"><span class="hr-num">3</span>Roles of the Parties</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">With regard to the processing of Customer Personal Data, the Customer is the <strong class="hr-term">Data Fiduciary</strong>, which determines the purpose and means of processing, and Healthray is a <strong class="hr-term">Data Processor</strong>, which processes Customer Personal Data on behalf of the Customer.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Where the Customer participates in ABDM as a Health Information Provider or Health Information User, or submits claims under Ayushman Bharat Pradhan Mantri Jan Arogya Yojana or any other government health scheme, the Customer remains the Data Fiduciary for those activities and Healthray acts as its technology service provider. The National Health Authority, State Health Agencies, insurers and third-party administrators that receive data from the Customer are separate Data Fiduciaries for their own processing.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">The Customer is responsible for all communications with its Affiliates or any other Data Fiduciary receiving the Services, insofar as such communications are required by Data Protection Laws.</div>
        </div>
      </div>
    </section>

    <!-- 4 -->
    <section class="hr-sec" id="dpa-4">
      <h2 class="hr-sec-head"><span class="hr-num">4</span>Customer obligations</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">The Customer shall comply with all Data Protection Laws in connection with this Addendum and its processing of Customer Personal Data. As the Data Fiduciary, the Customer remains responsible under Section 8(1) of the Act for processing undertaken by it or on its behalf. In particular, the Customer shall:
            <div class="hr-sub">
              <div class="hr-item"><span class="hr-mark">A.</span>
                <div class="hr-item-body">give Data Principals the notice required by Section 5 of the Act and Rule 3, with the option to access it in English or any language specified in the Eighth Schedule to the Constitution of India;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">B.</span>
                <div class="hr-item-body">have a lawful ground under Section 4 of the Act for each purpose of processing, being valid consent under Section 6 or a legitimate use under Section 7 (including responding to a medical emergency under Section 7(f)), and record that ground in the Services where the Services provide for it;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">C.</span>
                <div class="hr-item-body">obtain verifiable consent of the parent or lawful guardian where required by Section 9 of the Act and Rules 10 and 11, noting that the exemption for clinical establishments and healthcare professionals in Rule 12 and the Fourth Schedule applies only to the extent necessary to provide health services;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">D.</span>
                <div class="hr-item-body">ensure the completeness, accuracy and consistency of Customer Personal Data it enters or uses to make decisions, as required by Section 8(3) of the Act;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">E.</span>
                <div class="hr-item-body">manage its Authorised Users, including assigning appropriate roles, promptly removing access for persons who leave, and securing the devices, networks and credentials under its control;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">F.</span>
                <div class="hr-item-body">configure retention periods that comply with the laws that apply to it, including medical record, medico-legal, tax and government-scheme retention requirements;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">G.</span>
                <div class="hr-item-body">publish the business contact information of its Data Protection Officer or authorised person (Section 8(9) and Rule 9), operate a grievance redressal mechanism (Sections 8(10) and 13 and Rule 14), and respond to Data Principal requests;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">H.</span>
                <div class="hr-item-body">give intimation of any personal data breach to the Board and to each affected Data Principal, as required by Section 8(6) and Rule 7; and</div>
              </div>
              <div class="hr-item"><span class="hr-mark">I.</span>
                <div class="hr-item-body">ensure that any third party it connects to the Services on its own initiative (for example its own laboratory partner, insurer, third-party administrator or messaging provider) is engaged under a lawful contract. Such third parties are not Healthray Sub-processors.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">The Customer shall not give Healthray any Instruction that would cause Healthray to breach Data Protection Laws.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Healthray processes Customer Personal Data as entered, uploaded or instructed by the Customer and its Authorised Users. Healthray does not verify, and is not responsible for, the accuracy or completeness of that data, the adequacy of any notice given or consent obtained by the Customer, or any clinical, diagnostic, billing or administrative decision taken using the Services. These remain the responsibility of the Customer.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">The Customer is responsible for the security of the elements under its control, including Authorised User credentials and OTP devices, the Customer&rsquo;s computers, mobile devices, networks and printers, the assignment and removal of user access, and any data that Authorised Users print, download, export or share from the Services. The Customer shall promptly inform Healthray of any suspected compromise of credentials or unauthorised use of the Services.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">V.</span>
          <div class="hr-item-body">Where Customer Personal Data leaves the Services at the Customer&rsquo;s Instruction or through an action of an Authorised User (for example an export, print, email, message, or a transmission to ABDM, a government scheme portal, an insurer, a third-party administrator or an integration enabled by the Customer), Healthray&rsquo;s responsibility for that copy ends when it is delivered to the intended recipient or destination. Processing by that recipient is not processing by or on behalf of Healthray.</div>
        </div>
      </div>
    </section>

    <!-- 5 -->
    <section class="hr-sec" id="dpa-5">
      <h2 class="hr-sec-head"><span class="hr-num">5</span>Healthray obligations</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Healthray shall comply with Data Protection Laws applicable to it in processing Customer Personal Data, and shall:
            <div class="hr-sub">
              <div class="hr-item"><span class="hr-mark">A.</span>
                <div class="hr-item-body"><strong class="hr-term">Process on Instructions.</strong> Process Customer Personal Data only on behalf of the Customer, for the purpose of providing the Services and in accordance with the Instructions. Healthray shall not sell Customer Personal Data or use it for marketing, profiling or any other purpose of its own;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">B.</span>
                <div class="hr-item-body"><strong class="hr-term">Flag unlawful Instructions.</strong> Promptly inform the Customer if, in Healthray&rsquo;s opinion, an Instruction infringes Data Protection Laws, or if Healthray determines that it can no longer meet its obligations under this Addendum. Healthray may suspend the affected processing until the Instruction is confirmed or changed;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">C.</span>
                <div class="hr-item-body"><strong class="hr-term">Permitted processing.</strong> Process Customer Personal Data only to the extent necessary to (i) perform the Services and its obligations under the Agreement; (ii) operate, maintain, support, test and secure the Services; (iii) produce aggregated statistics about use of the Services in a form that does not identify, and cannot reasonably be used to re-identify, any individual or the Customer; (iv) protect the Services or Customer Personal Data from a threat; or (v) comply with a legally binding requirement under Clause 5.I(G);</div>
              </div>
              <div class="hr-item"><span class="hr-mark">D.</span>
                <div class="hr-item-body"><strong class="hr-term">No combination.</strong> Not combine Customer Personal Data with Personal Data that Healthray receives from or on behalf of any other customer or collects from its own interactions with individuals, except where necessary to perform the Services for the Customer. Patient records of different customers are kept separate even where the same individual is a patient of more than one customer;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">E.</span>
                <div class="hr-item-body"><strong class="hr-term">Confidentiality.</strong> Ensure that Healthray personnel and Sub-processors authorised to process Customer Personal Data are bound by written confidentiality obligations or an appropriate statutory obligation of confidentiality, including under Section 72A of the Information Technology Act, 2000;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">F.</span>
                <div class="hr-item-body"><strong class="hr-term">Cease processing.</strong> Where the Customer informs Healthray that a Data Principal has withdrawn consent, or otherwise instructs Healthray to stop processing particular Customer Personal Data, cease, and cause its Sub-processors to cease, that processing within a reasonable time and to the extent technically feasible, as contemplated by Section 6(6) of the Act, unless retention is required by law;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">G.</span>
                <div class="hr-item-body"><strong class="hr-term">Disclosure requests.</strong> To the extent legally permitted, promptly notify the Customer of any legally binding request (such as a court order, a direction from a competent authority, or a requisition by the Board or the Central Government under Section 28 or Section 36 of the Act) for disclosure of Customer Personal Data, and disclose only the minimum data legally required. Requests that are not legally binding will be refused and the Customer will be informed. Healthray shall keep a record of all such requests;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">H.</span>
                <div class="hr-item-body"><strong class="hr-term">Records.</strong> Maintain records reasonably necessary to demonstrate compliance with its obligations under this Addendum; and</div>
              </div>
              <div class="hr-item"><span class="hr-mark">I.</span>
                <div class="hr-item-body"><strong class="hr-term">Compliance features.</strong> Make available in the Services the features listed in Annex 3 that help the Customer meet its obligations relating to notice, consent, Data Principal rights, retention and breach intimation.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Healthray may process limited technical information (such as login times, IP addresses and device identifiers) for the security, integrity and fraud protection of its platform as a whole. To that limited extent Healthray acts as a Data Fiduciary, as described in the Healthray Privacy Policy.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Information that has been aggregated or anonymised so that it does not identify, and cannot reasonably be used to identify, any individual is not Personal Data. Healthray may retain and use such information to operate, analyse and improve its products and services, including after termination of the Agreement, provided that Healthray does not attempt to re-identify any individual and does not disclose information that identifies the Customer without its consent.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">Instructions are given through the Services&rsquo; standard features and settings. An Instruction that requires changes to the standard functionality of the Services, or work outside it, is subject to technical feasibility and to mutual agreement, and may be charged at Healthray&rsquo;s then-current rates.</div>
        </div>
      </div>
    </section>

    <!-- 6 -->
    <section class="hr-sec" id="dpa-6">
      <h2 class="hr-sec-head"><span class="hr-num">6</span>Security safeguards</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Healthray shall implement and maintain reasonable security safeguards, as required by Section 8(5) of the Act and Rule 6, appropriate to the nature, scope and sensitivity of Customer Personal Data (including Health Data), and in particular:
            <div class="hr-sub">
              <div class="hr-item"><span class="hr-mark">A.</span>
                <div class="hr-item-body">securing Customer Personal Data through encryption, obfuscation, masking or the use of virtual tokens mapped to that data, including masking or tokenisation of Aadhaar numbers where they are collected;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">B.</span>
                <div class="hr-item-body">appropriate measures to control access to the computer resources used for processing Customer Personal Data;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">C.</span>
                <div class="hr-item-body">visibility on access to Customer Personal Data through appropriate logs, monitoring and review, so that unauthorised access can be detected, investigated, remediated and prevented from recurring;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">D.</span>
                <div class="hr-item-body">reasonable measures for continued processing if the confidentiality, integrity or availability of Customer Personal Data is compromised, including data backups and timely restoration of access;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">E.</span>
                <div class="hr-item-body">retention of such logs and associated Personal Data for a period of at least one (1) year for the detection of unauthorised access, its investigation and remediation, unless otherwise required by law, and retention of ICT system logs within India for at least one hundred and eighty (180) days as required by CERT-In directions;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">F.</span>
                <div class="hr-item-body">logical separation of the Customer&rsquo;s data from the data of other customers; and</div>
              </div>
              <div class="hr-item"><span class="hr-mark">G.</span>
                <div class="hr-item-body">regular testing, assessment and evaluation of the effectiveness of these technical and organisational measures.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Healthray applies the principles of privacy by design and by default, and maintains an information security and privacy management system aligned with ISO/IEC 27001:2022 and ISO/IEC 27701:2019. The measures in force are summarised in Annex 3. Healthray may update them from time to time, provided the overall level of protection is not reduced.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Access by Healthray support personnel to patient-level Customer Personal Data is limited to what is needed to resolve a support request or incident, is time-limited and logged, and, except in a platform emergency, is subject to the Customer&rsquo;s approval.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">The Customer acknowledges that no system can be guaranteed to be completely secure. The occurrence of a Security Incident shall not, by itself, mean that Healthray has failed to meet its obligations under this Clause 6, where Healthray had implemented and maintained the safeguards described in this Addendum.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">V.</span>
          <div class="hr-item-body">Security is a shared responsibility. Healthray is responsible for the safeguards on the infrastructure, application and systems under its control. The Customer is responsible for the matters set out in Clause 4.IV and for using the security features of the Services that Healthray makes available, such as role-based access settings and authentication options.</div>
        </div>
      </div>
    </section>

    <!-- 7 -->
    <section class="hr-sec" id="dpa-7">
      <h2 class="hr-sec-head"><span class="hr-num">7</span>Sub-processors</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">The Customer generally authorises Healthray to engage Sub-processors, and specifically authorises the Sub-processors listed in Annex 2, subject to Healthray:
            <div class="hr-sub">
              <div class="hr-item"><span class="hr-mark">A.</span>
                <div class="hr-item-body">notifying the Customer at least thirty (30) calendar days in advance of any intended addition or replacement of a Sub-processor, by email to the Customer&rsquo;s registered contact;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">B.</span>
                <div class="hr-item-body">entering into a written contract with each Sub-processor containing data protection obligations no less protective than those in this Addendum, including reasonable security safeguards, confidentiality, breach notification and deletion on instruction; and</div>
              </div>
              <div class="hr-item"><span class="hr-mark">C.</span>
                <div class="hr-item-body">remaining responsible to the Customer for any failure by a Sub-processor to fulfil its obligations in relation to Customer Personal Data.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">The Customer may, within thirty (30) days of a notice under Clause 7.I(A), object in writing on reasonable data protection grounds. The Parties will then, for no more than thirty (30) days, work together in good faith to find a commercially reasonable solution that avoids use of that Sub-processor. If no solution is found, either Party may terminate the affected Services on written notice, without damages or penalty, and without prejudice to fees incurred before termination. This is the Customer&rsquo;s sole remedy for an objection to a Sub-processor.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Where a Sub-processor must be replaced urgently for reasons of security, service continuity or legal compliance, Healthray may do so with shorter notice and will inform the Customer as soon as reasonably practicable. The objection right in Clause 7.II applies from the date of that notice.</div>
        </div>
      </div>
    </section>

    <!-- 8 -->
    <section class="hr-sec" id="dpa-8">
      <h2 class="hr-sec-head"><span class="hr-num">8</span>Data location and transfers outside India</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Healthray hosts and stores Customer Personal Data on cloud infrastructure located in India, as specified in Annex 2.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Healthray shall not transfer Customer Personal Data outside India except to a Sub-processor whose processing location outside India is disclosed in Annex 2, or on the Customer&rsquo;s documented Instruction.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Healthray shall not transfer Customer Personal Data to any country or territory to which transfer is restricted by notification of the Central Government under Section 16 of the Act, and shall comply with any requirement specified under Rule 15 and with any law in force in India that provides a higher degree of protection for, or restriction on, transfer of Personal Data outside India.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">Where Customer Personal Data is processed outside India, Healthray shall ensure it remains subject to the safeguards in this Addendum, including encryption in transit and at rest, and shall minimise the data transferred to what is necessary for the purpose.</div>
        </div>
      </div>
    </section>

    <!-- 9 -->
    <section class="hr-sec" id="dpa-9">
      <h2 class="hr-sec-head"><span class="hr-num">9</span>Artificial intelligence features</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Certain features of the Services use artificial intelligence and machine learning technologies. Processing for these features is carried out in accordance with Clause 8, is limited to providing those features of the Services to the Customer, and is conducted only to the extent necessary for that purpose.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Healthray shall not use Customer Personal Data to train artificial intelligence or machine learning models for use by Healthray or any other person, except with the Customer&rsquo;s prior written consent, and then only on data that has been de-identified.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Outputs of artificial intelligence features are suggestions to assist Authorised Users. They may be incomplete or inaccurate, do not replace the clinical judgement of a registered medical practitioner, and must be reviewed by an Authorised User before they are relied on. Healthray gives no warranty as to the accuracy or suitability of such outputs for any clinical purpose, and the Customer is responsible for decisions taken using them. Use of artificial intelligence features is at the Customer&rsquo;s option. Where the Customer is notified as a Significant Data Fiduciary, Healthray shall provide reasonable information about these features to support the Customer&rsquo;s due diligence under Rule 13.</div>
        </div>
      </div>
    </section>

    <!-- 10 -->
    <section class="hr-sec" id="dpa-10">
      <h2 class="hr-sec-head"><span class="hr-num">10</span>Personal data breach</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Upon becoming aware of a Security Incident, Healthray shall notify the Customer without undue delay and, where reasonably practicable, within twenty-four (24) hours, so that the Customer can meet its obligation under Section 8(6) of the Act and Rule 7 to intimate each affected Data Principal without delay and to intimate the Board, including by a detailed report within seventy-two (72) hours.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">The notice shall include the following, to the extent then reasonably available to Healthray. Where not all information is available at once, Healthray may provide it in phases without undue delay:
            <div class="hr-sub">
              <div class="hr-item"><span class="hr-mark">A.</span>
                <div class="hr-item-body">a description of the Security Incident, including its nature, extent, timing and location;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">B.</span>
                <div class="hr-item-body">the categories of Customer Personal Data and the approximate number of Data Principals affected;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">C.</span>
                <div class="hr-item-body">the likely consequences for affected Data Principals;</div>
              </div>
              <div class="hr-item"><span class="hr-mark">D.</span>
                <div class="hr-item-body">the measures taken or proposed to contain the incident and mitigate risk; and</div>
              </div>
              <div class="hr-item"><span class="hr-mark">E.</span>
                <div class="hr-item-body">a contact person who can provide further information.</div>
              </div>
            </div>
          </div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Healthray shall take all measures reasonably necessary to remedy or mitigate the effects of the Security Incident, preserve relevant evidence, keep the Customer informed of developments, and provide the information the Customer reasonably needs for its report to the Board, including the facts, circumstances and reasons for the breach, mitigation measures, any findings regarding the person who caused it, and remedial measures taken to prevent recurrence.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">Healthray shall report cyber security incidents affecting its platform to CERT-In within the time required by CERT-In directions, and shall inform the Customer that it has done so.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">V.</span>
          <div class="hr-item-body">The Customer is responsible for giving intimation to the Board, affected Data Principals and any other authority as required by law. Healthray shall not notify the Customer&rsquo;s Data Principals directly unless the Customer agrees or the law requires it.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">VI.</span>
          <div class="hr-item-body">Healthray&rsquo;s notification of, or response to, a Security Incident is not an acknowledgement of fault or liability.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">VII.</span>
          <div class="hr-item-body">Where a personal data breach arises from causes within the Customer&rsquo;s responsibility under Clause 4 (such as compromised Authorised User credentials, the Customer&rsquo;s devices or networks, or a third party engaged by the Customer), the obligations in this Clause 10 apply only to the extent that the incident affects Healthray&rsquo;s systems. Healthray will provide reasonable assistance on request, and may charge for assistance beyond its standard support.</div>
        </div>
      </div>
    </section>

    <!-- 11 -->
    <section class="hr-sec" id="dpa-11">
      <h2 class="hr-sec-head"><span class="hr-num">11</span>Assistance with Data Principal rights</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Taking into account the nature of the processing, Healthray shall, through features of the Services and reasonable assistance, help the Customer respond to requests from Data Principals to exercise their rights under Chapter III of the Act, namely:
            <div class="hr-sub">
              <div class="hr-item"><span class="hr-mark">A.</span>
                <div class="hr-item-body">the right to access a summary of their Personal Data, the processing activities undertaken, and the identities of other Data Fiduciaries and Data Processors with whom it has been shared (Section 11);</div>
              </div>
              <div class="hr-item"><span class="hr-mark">B.</span>
                <div class="hr-item-body">the right to correction, completion, updating and erasure (Section 12);</div>
              </div>
              <div class="hr-item"><span class="hr-mark">C.</span>
                <div class="hr-item-body">the right of grievance redressal (Section 13 and Rule 14);</div>
              </div>
              <div class="hr-item"><span class="hr-mark">D.</span>
                <div class="hr-item-body">the right to nominate another individual to exercise their rights in the event of death or incapacity (Section 14); and</div>
              </div>
              <div class="hr-item"><span class="hr-mark">E.</span>
                <div class="hr-item-body">the right to withdraw consent (Section 6(4)).</div>
              </div>
            </div>
          </div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">If Healthray receives a request, complaint or other communication from a Data Principal, the Board or any other authority relating to Customer Personal Data, it shall, to the extent legally permitted, promptly forward it to the Customer and shall not respond except to confirm that it has been forwarded, unless authorised by the Customer or required by law.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">The Customer decides each request, including whether a law requiring retention of medical records prevents erasure, and responds within the period prescribed under the Rules.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">Standard features of the Services are provided at no extra charge. Healthray may charge reasonable fees, agreed in advance, for assistance that goes materially beyond those features.</div>
        </div>
      </div>
    </section>

    <!-- 12 -->
    <section class="hr-sec" id="dpa-12">
      <h2 class="hr-sec-head"><span class="hr-num">12</span>Children and persons with disability</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">The Services enable the Customer to identify Data Principals who are children (below eighteen years of age) or persons with disability who have a lawful guardian, to link them to a parent or lawful guardian, and to record verifiable consent where required under Section 9 of the Act and Rules 10 and 11.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Healthray shall not undertake tracking or behavioural monitoring of children or targeted advertising directed at children using Customer Personal Data, and shall not process children&rsquo;s data in a manner likely to cause any detrimental effect on their well-being (Section 9(2) and 9(3)).</div>
        </div>
      </div>
    </section>

    <!-- 13 -->
    <section class="hr-sec" id="dpa-13">
      <h2 class="hr-sec-head"><span class="hr-num">13</span>Impact assessments and Significant Data Fiduciaries</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Taking into account the nature of the processing and the information available to it, Healthray shall provide reasonable assistance to the Customer with any data protection impact assessment the Customer carries out in relation to the Services.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Where the Customer is notified as a Significant Data Fiduciary under Section 10 of the Act, Healthray shall provide reasonable information and cooperation for the Customer&rsquo;s periodic Data Protection Impact Assessment and audit under Section 10(2) and Rule 13.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Standard documentation is provided at no extra charge; the Customer shall pay for Healthray&rsquo;s time and out-of-pocket expenses for assistance beyond it, at rates agreed in advance.</div>
        </div>
      </div>
    </section>

    <!-- 14 -->
    <section class="hr-sec" id="dpa-14">
      <h2 class="hr-sec-head"><span class="hr-num">14</span>Audit and information</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Healthray shall make available to the Customer the information reasonably necessary to demonstrate compliance with this Addendum. In the first instance, once per year during the term of the Agreement, Healthray will provide responses to the Customer&rsquo;s reasonable security and privacy questionnaires, together with evidence such as its ISO/IEC 27001 certification and a summary of its most recent vulnerability assessment and penetration test.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Only where the Customer cannot reasonably establish compliance from that information (and not more than once in any twelve-month period, except where required by the Board or another competent authority or following a Security Incident), or where required by the Board or another competent authority, or following a Security Incident affecting Customer Personal Data, may the Customer, or an independent auditor bound by confidentiality who is not a competitor of Healthray, inspect Healthray&rsquo;s relevant processing operations. The Customer shall give reasonable prior notice, conduct the audit during normal business hours, avoid unnecessary disruption, and not access other customers&rsquo; data or information whose disclosure would compromise the security of the Services. Audits shall be conducted remotely where reasonably possible. All audit information and reports are Healthray&rsquo;s confidential information.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">The Customer shall pay for Healthray&rsquo;s reasonable time and out-of-pocket expenses for on-site audits, except where the audit reveals a material breach of this Addendum by Healthray.</div>
        </div>
      </div>
    </section>

    <!-- 15 -->
    <section class="hr-sec" id="dpa-15">
      <h2 class="hr-sec-head"><span class="hr-num">15</span>Retention, return and deletion</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">During the term of the Agreement, Healthray shall retain Customer Personal Data in accordance with the retention settings configured by the Customer, and shall erase Customer Personal Data on the Customer&rsquo;s Instruction, including where a Data Principal withdraws consent or the specified purpose is no longer served, as required of the Customer by Section 8(7) of the Act, unless retention is required by law.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Erased data is removed from production systems within thirty (30) days and from backups as they expire under Healthray&rsquo;s backup retention cycle. If a backup is restored, data previously erased is removed again before the restored system is used.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">On termination or expiry of the Agreement, Healthray shall cease processing Customer Personal Data and, at the Customer&rsquo;s option, return it in a commonly used, machine-readable format or delete it (including by rendering it permanently unreadable), within thirty (30) days, unless and only to the extent that law requires Healthray to retain it. Healthray shall provide written confirmation of deletion on request. Healthray has no obligation to retain Customer Personal Data after that period and is not responsible for any loss arising from the Customer&rsquo;s failure to export its data within it.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">The Customer is responsible for retaining any records it is required by law to keep after termination. Any Customer Personal Data retained by Healthray remains subject to this Addendum and the confidentiality obligations in the Agreement.</div>
        </div>
      </div>
    </section>

    <!-- 16 -->
    <section class="hr-sec" id="dpa-16">
      <h2 class="hr-sec-head"><span class="hr-num">16</span>Liability and indemnity</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Each Party&rsquo;s liability arising out of or in connection with this Addendum is subject to the limitations and exclusions of liability in the Agreement. This Addendum and the Agreement together are subject to a single aggregate limit of liability. Where the Agreement does not set one, Healthray&rsquo;s total aggregate liability arising out of or in connection with this Addendum shall not exceed the fees paid by the Customer to Healthray for the Services in the twelve (12) months preceding the event giving rise to the claim.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Neither Party shall be liable for any indirect, incidental, special or consequential loss, or for loss of profit, revenue, goodwill or reputation, arising out of or in connection with this Addendum.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Healthray shall not be liable for any loss, claim or penalty to the extent it arises from: (a) the Customer&rsquo;s Instructions; (b) the acts or omissions of the Customer or its Authorised Users, including breach of Clause 4; (c) systems, devices, networks or third parties within the Customer&rsquo;s responsibility under Clause 4; (d) data after it has left the Services as described in Clause 4.V; or (e) events beyond Healthray&rsquo;s reasonable control that occur despite Healthray having implemented the safeguards described in this Addendum.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">As the Data Fiduciary, the Customer remains responsible for its own obligations under the Act, including those relating to notice, consent, children&rsquo;s data, Data Principal rights, grievance redressal and breach intimation. Any penalty imposed on the Customer for its failure to meet those obligations is borne by the Customer.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">V.</span>
          <div class="hr-item-body">Subject to Clauses 16.I to 16.III, Healthray shall be liable to the Customer only for losses, including any penalty imposed by the Board, to the extent finally determined by a competent authority or court to have been caused by Healthray&rsquo;s breach of this Addendum, and in proportion to Healthray&rsquo;s contribution to that loss.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">VI.</span>
          <div class="hr-item-body">To the extent permitted by law, the Customer shall defend, indemnify and hold harmless Healthray and its Affiliates from and against any claims, losses, damages, liabilities, penalties, settlements, costs and expenses (including reasonable legal fees) arising from the Customer&rsquo;s breach of this Addendum or of its obligations under Data Protection Laws, including any unlawful Instruction or any failure to give notice or obtain a valid lawful ground for processing.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">VII.</span>
          <div class="hr-item-body">Nothing in this Addendum limits or excludes liability for fraud, wilful misconduct, or any other liability that cannot be limited or excluded under applicable law.</div>
        </div>
      </div>
    </section>

    <!-- 17 -->
    <section class="hr-sec" id="dpa-17">
      <h2 class="hr-sec-head"><span class="hr-num">17</span>Precedence, severability and changes</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">This Addendum supplements the Agreement. If there is any inconsistency between this Addendum and the Agreement regarding the processing of Customer Personal Data, this Addendum prevails. If there is any inconsistency between this Addendum and Data Protection Laws, Data Protection Laws prevail.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">If any provision of this Addendum is held by any court or competent authority to be unlawful or unenforceable, the remaining provisions remain in full force.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">III.</span>
          <div class="hr-item-body">Healthray may update this Addendum to reflect changes in Data Protection Laws, guidance from the Board or the Ministry of Electronics and Information Technology, or changes to the Services. Healthray will publish the updated version on this page and notify Customers at least thirty (30) days before material changes take effect, unless a shorter period is required by law. Updates will not reduce the overall level of protection for Customer Personal Data. Continued use of the Services after an update takes effect constitutes acceptance of the updated Addendum.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">IV.</span>
          <div class="hr-item-body">The Parties warrant that they, and their staff and subcontractors, will comply with their respective obligations under Data Protection Laws for the term of the Agreement.</div>
        </div>
      </div>
    </section>

    <!-- 18 -->
    <section class="hr-sec" id="dpa-18">
      <h2 class="hr-sec-head"><span class="hr-num">18</span>Governing law and disputes</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">This Addendum is governed by the laws of India. Disputes shall be resolved in accordance with the dispute resolution and jurisdiction provisions of the Agreement.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Nothing in this Addendum affects the powers of the Data Protection Board of India or the Appellate Tribunal under the Act, or the right of any Data Principal to approach them after exhausting the grievance redressal mechanism of the relevant Data Fiduciary.</div>
        </div>
      </div>
    </section>

    <!-- 19 -->
    <section class="hr-sec" id="dpa-19">
      <h2 class="hr-sec-head"><span class="hr-num">19</span>Data Protection Officer and grievance contact</h2>
      <div class="hr-sec-body">
        <div class="hr-item"><span class="hr-mark">I.</span>
          <div class="hr-item-body">Patients and other Data Principals whose data is held in the Services by a hospital, clinic or other Customer should first contact that Customer, which is the Data Fiduciary for their data. Healthray will forward any request it receives to the relevant Customer under Clause 11.II.</div>
        </div>
        <div class="hr-item"><span class="hr-mark">II.</span>
          <div class="hr-item-body">Customers, and individuals with questions or grievances about Healthray&rsquo;s own processing, may contact Healthray&rsquo;s Data Protection Officer, who also acts as Grievance Officer:
            <dl class="hr-contact">
              <dt>Email</dt>
              <dd><a href="mailto:contact@healthray.com">contact@healthray.com</a></dd>
              <dt>Company</dt>
              <dd>Healthray Technologies Pvt. Ltd.</dd>
            </dl>
          </div>
        </div>
      </div>
    </section>

    <!-- Annex 1 -->
    <section class="hr-sec" id="dpa-a1">
      <h2 class="hr-sec-head"><span class="hr-num">A1</span>Annex 1 &ndash; Details of processing</h2>
      <div class="hr-sec-body">
        <div class="hr-table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Item</th>
                <th scope="col">Details</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Data Fiduciary</td>
                <td>The Customer (hospital, clinic, diagnostic centre or other healthcare organisation) and its Affiliates covered by this Addendum</td>
              </tr>
              <tr>
                <td>Data Processor</td>
                <td>Healthray Technologies Pvt. Ltd.</td>
              </tr>
              <tr>
                <td>Nature of processing</td>
                <td>Collection through the Services on the Customer&rsquo;s behalf, recording, organisation, structuring, storage, retrieval, display, transmission on Instruction, backup, support, security monitoring, restriction and erasure</td>
              </tr>
              <tr>
                <td>Purpose</td>
                <td>Providing the Services and related support to the Customer under the Agreement</td>
              </tr>
              <tr>
                <td>Duration</td>
                <td>The term of the Agreement and the post-termination period set out in Clause 15</td>
              </tr>
              <tr>
                <td>Data Principals</td>
                <td>Patients (including children and persons with disability), their parents, lawful guardians, nominees and attendants; the Customer&rsquo;s doctors, nurses, staff and contractors; referring doctors; visitors</td>
              </tr>
              <tr>
                <td>Patient data</td>
                <td>Demographic and contact details; Aadhaar (masked or tokenised, where collected); ABHA number and address; other government identifiers; insurance and scheme details; clinical records, diagnoses, prescriptions, laboratory and radiology reports and images, operation and nursing notes, discharge summaries, allergies, vitals, vaccination records, consent forms and documents; billing and payment information</td>
              </tr>
              <tr>
                <td>Staff data</td>
                <td>Identity and contact details, professional registration, user accounts, roles and permissions, attendance, and HR information configured by the Customer</td>
              </tr>
              <tr>
                <td>Financial data</td>
                <td>Bills, invoices, receipts, payments, and insurance, TPA and PM-JAY claims</td>
              </tr>
              <tr>
                <td>Sensitive records</td>
                <td>Health Data, including records subject to special confidentiality under the Mental Healthcare Act, 2017, the HIV and AIDS (Prevention and Control) Act, 2017, the Pre-Conception and Pre-Natal Diagnostic Techniques Act, 1994 and the Medical Termination of Pregnancy Act, 1971</td>
              </tr>
              <tr>
                <td>Location</td>
                <td>India</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

    <!-- Annex 2 -->
    <section class="hr-sec" id="dpa-a2">
      <h2 class="hr-sec-head"><span class="hr-num">A2</span>Annex 2 &ndash; Sub-processors</h2>
      <div class="hr-sec-body">
        <div class="hr-table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Sub-processor</th>
                <th scope="col">Service</th>
                <th scope="col">Data processed</th>
                <th scope="col">Location</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Amazon Web Services (AWS India)</td>
                <td>Cloud service provider: hosting, storage and backup</td>
                <td>All Customer Personal Data, encrypted</td>
                <td>India</td>
              </tr>
            </tbody>
          </table>
        </div>
        <p>Healthray will notify Customers of changes to this list in accordance with Clause 7.</p>
      </div>
    </section>

    <!-- Annex 3 -->
    <section class="hr-sec" id="dpa-a3">
      <h2 class="hr-sec-head"><span class="hr-num">A3</span>Annex 3 &ndash; Security measures and compliance features</h2>
      <div class="hr-sec-body">
        <div class="hr-table-wrap">
          <table>
            <thead>
              <tr>
                <th scope="col">Area</th>
                <th scope="col">Measures</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <td>Encryption</td>
                <td>Encryption of data in transit using TLS and at rest using industry-standard encryption; masking or tokenisation of sensitive identifiers</td>
              </tr>
              <tr>
                <td>Access control</td>
                <td>Role-based access; password and OTP authentication; least-privilege access for Healthray personnel; time-limited, logged support access</td>
              </tr>
              <tr>
                <td>Logging and monitoring</td>
                <td>Audit and activity logs of access to and changes in Customer Personal Data; monitoring and review for unauthorised access; log retention of at least one year</td>
              </tr>
              <tr>
                <td>Tenant separation</td>
                <td>Logical isolation of each Customer&rsquo;s data from other Customers&rsquo; data</td>
              </tr>
              <tr>
                <td>Continuity</td>
                <td>Regular backups and disaster recovery arrangements to restore availability and access in a timely manner</td>
              </tr>
              <tr>
                <td>Temporary files</td>
                <td>Processing does not generate temporary files containing Customer Personal Data outside Healthray&rsquo;s secured systems</td>
              </tr>
              <tr>
                <td>Testing</td>
                <td>Regular testing, assessment and evaluation of technical and organisational measures, including vulnerability assessment and penetration testing</td>
              </tr>
              <tr>
                <td>People</td>
                <td>Confidentiality undertakings and privacy and security awareness for personnel with access</td>
              </tr>
              <tr>
                <td>Incident response</td>
                <td>Documented incident response process; Customer notification within 24 hours; CERT-In reporting; evidence preservation and root cause analysis</td>
              </tr>
              <tr>
                <td>Standards</td>
                <td>Information security and privacy management aligned with ISO/IEC 27001:2022 and ISO/IEC 27701:2019</td>
              </tr>
              <tr>
                <td>Compliance features for Customers</td>
                <td>Integration with ABDM; tools that support notice, consent records, Data Principal requests, retention settings, data export and breach intimation, as available in the Services</td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </section>

  </div>
</div>