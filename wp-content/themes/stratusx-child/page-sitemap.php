<?php
global $wpdb;

$query = "SELECT 
        p.ID AS main_id, 
        p.post_name AS main_post_name, 
        p.post_title AS main_post_title, 
        p.post_status AS main_post_status, 
        p.menu_order AS main_menu_order, 
        pm.meta_key AS main_meta_key,
        pm.meta_value AS main_meta_value,
        sub_p.ID AS sub_id,
        sub_p.post_name AS sub_post_name,
        sub_p.post_title AS sub_post_title,
        sub_p.post_status AS sub_post_status,
        sub_p.menu_order AS sub_menu_order,
        sub_pm.meta_key AS sub_meta_key,
        sub_pm.meta_value AS sub_meta_value
    FROM 
        {$wpdb->posts} p
    INNER JOIN 
        {$wpdb->postmeta} pm ON p.ID = pm.post_id
    LEFT JOIN 
        {$wpdb->postmeta} sub_pm ON sub_pm.meta_value = p.ID
    LEFT JOIN 
        {$wpdb->posts} sub_p ON sub_pm.post_id = sub_p.ID AND sub_p.post_type = 'page'
    WHERE 
        sub_p.post_status = 'publish' AND p.post_type = 'page' AND pm.meta_value = 'templates/template-hms-international.php'
    ORDER BY 
        p.post_date DESC, sub_p.post_title ASC;
";

$results = $wpdb->get_results($query, ARRAY_A);

$location_groups = [];
if (! empty($results)) {
	$temp_array  = [];
	$last_main_id = null;

	foreach ($results as $row) {
		$main_id = $row['main_id'];

		if ($main_id !== $last_main_id && $last_main_id !== null) {
			$location_groups[count($location_groups) - 1]['textdata'] = $temp_array;
			$temp_array = [];
		}

		if ($main_id !== $last_main_id) {
			$location_groups[] = [
				'id'         => $row['main_id'],
				'post_name'  => $row['main_post_name'],
				'post_status' => $row['main_post_status'],
				'post_title' => $row['main_post_title'],
			];
			$last_main_id = $main_id;
		}

		if (! empty($row['sub_id'])) {
			$temp_array[] = [
				'id'        => $row['sub_id'],
				'post_name' => $row['sub_post_name'],
				'post_title' => $row['sub_post_title'],
			];
		}
	}
	if (! empty($temp_array)) {
		$location_groups[count($location_groups) - 1]['textdata'] = $temp_array;
	}
}

// Speciality pages - flat, alphabetical (see action item #1 above)
$speciality_args = [
	'post_type'      => 'page',
	'meta_key'       => '_wp_page_template',
	'meta_value'     => 'templates/template-speciality.php',
	'posts_per_page' => -1,
	'orderby'        => 'title',
	'order'          => 'ASC',
	'post_status'    => 'publish',
];
$speciality_pages = new WP_Query($speciality_args);
?>

<style>
	body .hero { padding: 40px 0; }
	body .site-search { position: relative; max-width: 480px; margin-top: 4px; }
	body .site-search input { width: 100%; height: 50px; border: 1.5px solid var(--line); border-radius: 12px; padding: 0 46px 0 16px; font: inherit; font-size: .95rem; color: var(--ink); background: #fff; transition: .15s; }
	body .site-search input:focus { outline: none; border-color: var(--brand); box-shadow: 0 0 0 4px var(--brand-glow); }
	body .site-search svg { position: absolute; right: 16px; top: 50%; transform: translateY(-50%); width: 18px; height: 18px; stroke: var(--ink-soft); pointer-events: none; }
	body .search-status { margin-top: 10px; font-size: .85rem; color: var(--ink-soft); min-height: 1.2em; }
	body .jump-nav { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 24px; padding: 0; list-style: none; }
	body .jump-nav a { font-size: .82rem; font-weight: 600; color: var(--ink); background: #fff; border: 1px solid var(--line); border-radius: 99px; padding: 7px 14px; display: inline-block; transition: .15s; }
	body .jump-nav a:hover { border-color: var(--brand); color: var(--brand); }
	body .block { scroll-margin-top: 90px; border: 1px solid var(--line); border-radius: var(--radius); background: #fff; padding: 26px; margin-bottom: 20px; margin-top: 20px; }
	body .block h2 { font-size: 1.25rem; font-weight: 700; }
	body .block-desc { margin: 8px 0 18px; color: var(--ink-soft); font-size: .92rem; max-width: 640px; }
	body ul.link-list { list-style: none; margin: 0; padding: 0; display: grid; grid-template-columns: repeat(3, 1fr); gap: 2px 20px; }
	body ul.link-list li { position: relative; padding-left: 16px; }
	body ul.link-list li::before { content: ""; position: absolute; left: 0; top: 14px; width: 6px; height: 6px; border-radius: 1px; background: var(--brand); transform: rotate(45deg); }
	body ul.link-list a { display: inline-block; padding: 8px 6px; border-radius: 8px; color: var(--ink-soft); font-size: .93rem; transition: .15s; }
	body ul.link-list a:hover { background: var(--brand-tint); color: var(--brand-dark); }
	body mark { background: #FFF1B8; color: inherit; border-radius: 3px; padding: 0 2px; }
	body li.is-hidden { display: none; }
	body .block.is-empty,
	body details.region.is-empty { display: none; }
	@media (max-width:980px) {
		body ul.link-list,
		body .faq-list details.region ul.link-list { grid-template-columns: repeat(2, 1fr); }
	}

	@media (max-width:720px) {
		body ul.link-list,
		body .faq-list details.region ul.link-list { grid-template-columns: 1fr; }
		body .block { padding: 18px; }
	}
</style>

<main class="sitemap-landing">

	<nav class="wrap crumbs" aria-label="Breadcrumb">
		<ol>
			<li><a href="<?php echo esc_url(home_url('/')); ?>">Home</a></li>
			<li aria-current="page">Sitemap</li>
		</ol>
	</nav>

	<section class="hero">
		<div class="wrap">
			<span class="eyebrow">Sitemap</span>
			<h1>Every Healthray page, organized in one place</h1>
			<p class="hero-sub">Jump straight to any product, speciality, ABDM resource or location-wise page. Search below or use the quick-jump links to find what you need.</p>

			<div class="site-search">
				<input type="search" id="sitemapSearch" placeholder="Search pages, e.g. &ldquo;cardiologist&rdquo; or &ldquo;India&rdquo;" aria-label="Search sitemap links">
				<svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
					<circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="2" />
					<path d="M21 21l-4.3-4.3" stroke="currentColor" stroke-width="2" stroke-linecap="round" />
				</svg>
			</div>
			<p class="search-status" id="searchStatus" aria-live="polite"></p>

			<nav class="jump-nav" aria-label="Quick jump">
				<a href="#company">Company</a>
				<a href="#products">Products</a>
				<a href="#speciality">Speciality</a>
				<a href="#abha">ABHA &amp; Government</a>
				<a href="#india-flagship">Best Software in India</a>
				<a href="#locations">Location-wise Pages</a>
				<a href="#alternatives">Compare Alternatives</a>
				<a href="#extra">Extra Links</a>
				<a href="#legal">Legal</a>
			</nav>
		</div>
	</section>

	<div class="wrap" id="sitemapRoot">

		<section class="block" id="company">
			<h2>Company</h2>
			<p class="block-desc">Learn about Healthray, get in touch with our team, or explore resources and proof points.</p>
			<ul class="link-list">
				<li><a href="https://healthray.com/">Home</a></li>
				<li><a href="https://healthray.com/blogs/">Blogs</a></li>
				<li><a href="https://healthray.com/why-healthray/">Why Healthray</a></li>
				<li><a href="https://healthray.com/contact/">Contact</a></li>
				<li><a href="https://healthray.com/whitepaper/">Whitepaper</a></li>
				<li><a href="https://healthray.com/case-studies/">Case Studies</a></li>
				<li><a href="https://healthray.com/faqs/">FAQs</a></li>
				<li><a href="https://healthray.com/events/">Events</a></li>
				<li><a href="https://healthray.com/become-a-partner/">Become a Partner</a></li>
				<li><a href="https://healthray.com/reviews/">Healthray Reviews</a></li>
				<li><a href="https://healthray.com/awards/">Awards</a></li>
				<li><a href="https://healthray.com/pdf/Healthray.pdf">Brochure</a></li>
			</ul>
		</section>

		<section class="block" id="products">
			<h2>Healthray Products</h2>
			<p class="block-desc">Our core software modules, from full hospital management to standalone pharmacy and lab systems.</p>
			<ul class="link-list">
				<li><a href="https://healthray.com/hospital-information-management-system/">Hospital Information Management System</a></li>
				<li><a href="https://healthray.com/emr-software/">EMR Software</a></li>
				<li><a href="https://healthray.com/ehr-software/">EHR Software</a></li>
				<li><a href="https://healthray.com/pharmacy-management-system/">Pharmacy Management System</a></li>
				<li><a href="https://healthray.com/laboratory-information-management-system/">Laboratory Information Management System</a></li>
				<li><a href="https://healthray.com/clinic-management-software/">Clinic Management Software</a></li>
			</ul>
		</section>

		<section class="block" id="speciality">
			<h2>Speciality</h2>
			<p class="block-desc">EMR built for how each speciality actually works.</p>
			<ul class="link-list">
				<?php if ($speciality_pages->have_posts()) : while ($speciality_pages->have_posts()) : $speciality_pages->the_post(); ?>
				<li><a href="<?php the_permalink(); ?>">
					<?php the_title(); ?>
					</a></li>
				<?php endwhile;
				wp_reset_postdata();
				endif; ?>
			</ul>
		</section>

		<section class="block" id="abha">
			<h2>ABHA &amp; Government</h2>
			<p class="block-desc">India's digital health mission - ABHA IDs, ABDM, PMJAY and DHIS, built into the Healthray workflow.</p>
			<ul class="link-list">
				<li><a href="https://healthray.com/abha/">ABHA</a></li>
				<li><a href="https://healthray.com/abdm/">ABDM</a></li>
				<li><a href="https://healthray.com/pmjay/">PMJAY</a></li>
				<li><a href="https://healthray.com/dhis/">DHIS</a></li>
			</ul>
		</section>

		<section class="block" id="india-flagship">
			<h2>Best Healthcare Software For India</h2>
			<p class="block-desc">Our India-wide landing pages for each product line - the starting point before drilling into a state or city below.</p>
			<ul class="link-list">
				<li><a href="https://healthray.com/best-lab-software-india/">Best Lab Software In India</a></li>
				<li><a href="https://healthray.com/best-emr-software-india/">Best EMR Software In India</a></li>
				<li><a href="https://healthray.com/best-ehr-software-india/">Best EHR Software In India</a></li>
				<li><a href="https://healthray.com/best-pharmacy-management-software-india/">Best Pharmacy Management Software In India</a></li>
			</ul>
		</section>

		<section class="block" id="alternatives">
			<h2>HIMS Compare With</h2>
			<ul class="link-list">
				<li><a href="https://healthray.com/advancedmd-alternative/">AdvancedMD Alternative</a></li>
				<li><a href="https://healthray.com/docon-alternative/">Docon Alternative</a></li>
				<li><a href="https://healthray.com/docpulse-alternative/">Docpulse Alternative</a></li>
				<li><a href="https://healthray.com/healthplix-alternative/">Healthplix Alternative</a></li>
				<li><a href="https://healthray.com/karexpert-alternative/">Karexpert Alternative</a></li>
				<li><a href="https://healthray.com/mocdoc-alternative/">MocDoc Alternative</a></li>
				<li><a href="https://healthray.com/practo-alternative/">Practo Alternative</a></li>
				<li><a href="https://healthray.com/akhil-system-alternative/">Akhil System Alternative</a></li>
				<li><a href="https://healthray.com/ezovion-alternative/">Ezovion Alternative</a></li>
				<li><a href="https://healthray.com/epic-emr-alternative/">Epic EMR Alternative</a></li>
			</ul>
		</section>

		<section class="block" id="lims-alternatives">
			<h2>LIMS Compare With</h2>
			<ul class="link-list">
				<li><a href="https://healthray.com/crelio-laboratory-alternative/">Crelio Alternative</a></li>
				<li><a href="https://healthray.com/alternatives/labsmart/">LabSmart Alternative</a></li>
				<li><a href="https://healthray.com/alternatives/labguru/">Labguru Alternative</a></li>
				<li><a href="https://healthray.com/elabassist-alternative/">eLabAssist Alternative</a></li>
				<li><a href="https://healthray.com/flabs-alternative/">Flabs Alternative</a></li>
			</ul>
		</section>

		<section class="block" id="pharmacy-alternatives">
			<h2>Pharmacy Management Compare With</h2>
			<ul class="link-list">
				<li><a href="https://healthray.com/marg-erp-9-alternative/">Marg ERP 9 Alternative</a></li>
				<li><a href="https://healthray.com/gofrugal-alternative/">Gofrugal Alternative</a></li>
				<li><a href="https://healthray.com/wondersoft-alternative/">Wondersoft Alternative</a></li>
			</ul>
		</section>


		<?php foreach ($location_groups as $group) :
		if (empty($group['textdata']) || ! is_array($group['textdata'])) continue;
		$count = count($group['textdata']) + 1; // +1 for the group's own "all" page
		?>
		<section class="block">
			<h2>
				<?php echo esc_html($group['post_title']); ?>
			</h2>
			<ul class="link-list">
				<li><a href="https://healthray.com/<?php echo esc_attr($group['post_name']); ?>/">
					<?php echo esc_html($group['post_title']); ?>
					</a></li>
				<?php foreach ($group['textdata'] as $sub) : ?>
				<li><a href="https://healthray.com/<?php echo esc_attr($sub['post_name']); ?>/">
					<?php echo esc_html($sub['post_title']); ?>
					</a></li>
				<?php endforeach; ?>
			</ul>
		</section>
		<?php endforeach; ?>


		<section class="block" id="pharmacy-alternatives">
			<h2>Best Hospital Management Software In Kuwait</h2>
			<ul class="link-list">
				<li><a href="https://healthray.com/best-hospital-management-software-kuwait/">Best Hospital Management Software In Kuwait</a></li>
			</ul>
		</section>

		<section class="block" id="extra">
			<h2>Extra Links</h2>
			<ul class="link-list">
				<li><a href="https://healthray.com/medical-billing-services/">Medical Billing Services</a></li>
				<li><a href="https://healthray.com/telehealth-services/">Telehealth Services</a></li>
				<li><a href="https://healthray.com/patient-mobile-app/">Patient Mobile App</a></li>
				<li><a href="https://healthray.com/doctor-mobile-app/">Doctor Mobile App</a></li>
				<li><a href="https://healthray.com/medical-billing-software/">Medical Billing Software</a></li>
				<li><a href="https://healthray.com/patient-engagement-software/">Patient Engagement Software</a></li>
				<li><a href="https://healthray.com/medical-iot-solution/">Medical IoT Solution</a></li>
				<li><a href="https://healthray.com/patient-flow/">Patient Flow</a></li>
				<li><a href="https://healthray.com/best-pathology-lab-software/">Best Pathology Lab Software</a></li>
				<li><a href="https://healthray.com/free-pathology-lab-software/">Free Pathology Lab Software</a></li>
				<li><a href="https://healthray.com/free-pharmacy-software/">Free Pharmacy Software</a></li>
				<li><a href="https://healthray.com/inventory-management-software/">Inventory Management Software</a></li>
				<li><a href="https://healthray.com/electronic-patient-records/">Electronic Patient Records</a></li>
				<li><a href="https://healthray.com/medical-college-software/">Medical College Software</a></li>
				<li><a href="https://healthray.com/doctor-appointment-system/">Doctor Appointment System</a></li>
			</ul>
		</section>

		<section class="block" id="legal">
			<h2>Legal Pages</h2>
			<ul class="link-list">
				<li><a href="https://healthray.com/privacy-policy/">Privacy Policy</a></li>
				<li><a href="https://healthray.com/terms-condition/">Terms &amp; condition</a></li>
				<li><a href="https://healthray.com/refund-cancellation-policy/">Refund &amp; Cancellation Policy</a></li>
				<li><a href="https://healthray.com/dpa/">DPA</a></li>
			</ul>
		</section>

	</div>
</main>
<script>

	(function () {
		var input = document.getElementById('sitemapSearch');
		var status = document.getElementById('searchStatus');
		var root = document.getElementById('sitemapRoot');
		if (!input || !root) return;

		var allItems = Array.prototype.slice.call(root.querySelectorAll('li'));

		function clearHighlight(el) {
			el.querySelectorAll('mark').forEach(function (m) {
				m.replaceWith(document.createTextNode(m.textContent));
			});
		}

		input.addEventListener('input', function () {
			var q = this.value.trim().toLowerCase();
			var visibleCount = 0;

			allItems.forEach(function (li) {
				var a = li.querySelector('a');
				if (!a) return;
				var text = a.textContent;
				clearHighlight(li);

				if (q === '') {
					li.classList.remove('is-hidden');
					visibleCount++;
					return;
				}

				var idx = text.toLowerCase().indexOf(q);
				if (idx > -1) {
					li.classList.remove('is-hidden');
					visibleCount++;
					a.innerHTML = text.slice(0, idx) + '<mark>' + text.slice(idx, idx + q.length) + '</mark>' + text.slice(idx + q.length);
					var region = li.closest('details.region');
					if (region) region.open = true;
				} else {
					li.classList.add('is-hidden');
				}
			});

			root.querySelectorAll('details.region').forEach(function (region) {
				var anyVisible = region.querySelectorAll('li:not(.is-hidden)').length > 0;
				region.classList.toggle('is-empty', q !== '' && !anyVisible);
			});
			root.querySelectorAll('.block').forEach(function (block) {
				var anyVisible = block.querySelectorAll('li:not(.is-hidden)').length > 0;
				block.classList.toggle('is-empty', q !== '' && !anyVisible);
			});

			status.textContent = q === '' ? '' : (visibleCount + ' matching link' + (visibleCount === 1 ? '' : 's'));
		});
	})();

</script>