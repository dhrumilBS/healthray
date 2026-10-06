<?php
/**
 * 410 Gone — URL list.
 *
 * This file is included by 410-gone-urls.php.
 * Edit this file to add or remove URLs without touching the logic.
 *
 * ⚠️  Three items to verify before deploying:
 *  1. /wp-content/ and /wp-admin/ are prefix-matched — this also hits theme/
 *     plugin assets. Only keep them if assets are served via CDN or those
 *     paths are fully retired.
 *  2. /laboratory-information-manag looks truncated — verify the full path.
 *  3. The catch-all /* is excluded. Uncomment ONLY for full-site deprecation.
 */

return [
    'prefixes' => [
        '/en-ng/*',
        '/en-ng/wp-admin/*',
        '/en-ng/wp-*.php',
        '/en-ng/wp-content/*',
        '/en-ng/wp-content/plugins/*',
        '/en-ng/wp-content/uploads/sites/2/*',
        '/en-ng/wp-content/themes/stratusx/*',
        '/en-ng/wp-content/themes/stratusx-child/*',
        '/en-gh/wp-content/themes/stratusx/*',
        '/en-gh/wp-*.php',
        '/en-gh/wp-content/uploads/sites/12/*',
        '/en-gh/*',
        '/en-gh/wp-admin/*',
        '/en-gh/wp-content/*',
        '/en-gh/wp-content/themes/stratusx-child/*',
        '/en-gh/wp-content/plugins/*',
        '/en-ng/wp-admin/*',
        '/wp-admin/*',
        '/wp-content/*',
        '/wp-content/plugins/*',
        '/wp-content/plugins/telephone-input-for-contact-form-7',
        '/wp-content/themes/stratusx-child/*',
        '/wp-content/themes/stratusx/*',
        '/wp-content/uploads/*',
        '/wp-content/uploads/complianz/css/banner-{banner_id}-{type}.css?v=14',
        '/wp-json/complianz/v1/',
    ],

    // ── Regex matches ─────────
    'regex' => [
        '#^/wp-[^/]+\.php$#i',   // /wp-login.php, /wp-cron.php, etc.
    ],

    // ── Query-string matches ─────────
    'query_strings' => [
        '/(^|&)page_id=65338(&|$)/',   // /?page_id=65338
    ],

    // ── Exact path matches ─────────
    'exact_paths' => [
        '/blog/author/mayank-chanllawala/',
        '/blog/author/yogesh-balar/',
        '/blog/category/blog/pms/feed/',
        '/blog/category/blog/complaint-management/feed/',
        '/blog/category/blog/hospital-management-system/feed/',
        '/blog/hospital-management-software/',
        '/blog/hospital-management-software/hospitals-important-lets-understand/feed/',
        '/blog/hospital-management-software/significance-of-mrd-management-in-healthcare-a-full-guide/feed/',
        '/blog/page/33/',
        '/blog/top-benefits-implementing-pharmacy-management-system-pms-business/feed/',
        '/category/blog/complaint-management/',
        '/category/blog/electronic-prescriptions/',
        '/category/blog/electronic-prescriptions/feed/',
        '/category/blog/emr-ehr/',
        '/category/blog/hrms/',
        '/category/blog/hrms/feed/',
        '/category/blog/patient-appoinment/',
        '/category/blog/patient-health-records/',
        '/category/blog/patient-portal/',
        '/category/blog/patient-registration/',
        '/category/blog/patient-registration/feed/',
        '/hospital-management-system/page/2/',
        '/hospital-management-system/page/3/',
        '/hospital-management-system/page/4/',
        '/emr/page/6/',

        '/ehr-emr-software/cardiology-ehr-software/',
        '/emr-for-consultant-physician/',
        '/emr-for-dermatologist/',
        '/emr-for-gastroenterologists/',
        '/emr-for-gynecologist/',
        '/emr-for-gynecologist',
        '/emr-for-orthopedics/',
        '/emr-for-pulmonologist/',
        '/emr-for-pulmonologist',
        '/emr-software-for-aesthetic-clinic/',
        '/emr-software-for-cardiothoracic-surgeons/',
        '/emr-software-for-psychiatry/',
        '/emr-software-for-spa/',
        '/emr-software-for-tattoo-removal/',
        '/emr-software-for-trichologist/',

        '/events/global-digital-health-summit-2024-the-future-of-healthcare-innovation/',
        '/events/india-med-expo-2024-international-exhibition-on-medical-surgical-hospital-equipment/',
        '/events/india-med-expo-2024-medical-surgical-hospital-equipment-exhibition/',
        '/events/medical-fair-india-2024-connecting-healthcare-markets/',
        '/events/medical-fair-india-2024-connecting-healthcare-markets-2/',
        '/events/thanacon-2024-healthcare-transformation-networking-conference/',

        // Doctor / specialty clinic pages
        '/aesthetic-clinic/',
        '/ayurveda/',
        '/bariatric-surgeons/',
        '/cardiologist-new/',
        '/cardiothoracic-surgeons/',
        '/consultant-physicians-new/',
        '/dental-care/',
        '/dermatologist-new/',
        '/diabetologist-new/',
        '/doctor/',
        '/endocrinologist/',
        '/ent-surgeon/',
        '/family-medicine/',
        '/functional-medicine/',
        '/gastroenterologists-new/',
        '/general-medicine/',
        '/general-surgeon/',
        '/gynaecologist/',
        '/gynaecologists-new/',
        '/hematologist/',
        '/homeopathy/',
        '/immunologists/',
        '/laparoscopic-surgeries/',
        '/mental-health/',
        '/nephrologists-new/',
        '/neurologist/',
        '/neuropsychiatrists/',
        '/nutritionist/',
        '/occupational-health-physician/',
        '/oncologist/',
        '/ophthalmologist/',
        '/orthopedics-new/',
        '/orthopaedics/',
        '/osteopathy/',
        '/pain-management/',
        '/pediatric/',
        '/physiotherapists/',
        '/plastic-surgery/',
        '/podiatry/',
        '/psychiatry/',
        '/pulmonologists/',
        '/rheumatologist/',
        '/spa/',
        '/tattoo-removal/',
        '/trichologist/',
        '/urologist/',
        '/vascular-medicine/',

        // Misc pages
        '/best-hospital-management-software-new-zealand/',
        '/best-pms-software-india/',
        '/contact-us',
        '/faq/',
        '/health-management-information-system/',
        '/Healthray',
        '/laboratory-information-manag',
        '/terms-condition-2/',
    ],
];
