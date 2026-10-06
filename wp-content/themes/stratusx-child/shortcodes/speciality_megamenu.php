<?php 

add_shortcode('speciality_megamenu', 'emr_specialities_shortcode');

function emr_specialities_shortcode() {
	// Example: load or define the $emr_specialties array.
	// You can replace this with your actual source (DB query, ACF field, etc.)
	$emr_specialties = [
		[
			"category" => "Specialists",
			"color" => "#16a34a",
			"col" => 1,
			"specialties" => [
				["id" => 62881, "title" => "Orthopedics", "text" => "Simplifying the treatment of the musculoskeletal system"],
				["id" => 63384, "title" => "Gastroenterologists", "text" => "Optimizing gastroenterologist practice from front desk to OT"],
				["id" => 63638, "title" => "Nephrologists", "text" => "Effective solution for kidney disorder and dialysis management"],
				["id" => 63967, "title" => "Pulmonologists", "text" => "Specifically built for advancing pulmonologist practice"],
				["id" => 64466, "title" => "Diabetologist", "text" => "Smart EMR for diabetes and insulin management"],
				["id" => 64613, "title" => "Cardiologist", "text" => "Top-rated solution for managing heart and blood vessel disease"],
				["id" => 62801, "title" => "Rheumatologist", "text" => "Smart EMR for joint care and autoimmune management"],
				["id" => 63640, "title" => "Urologist", "text" => "Streamlined EMR for diagnostics, imaging, and procedures"],
				["id" => 63785, "title" => "Oncologist", "text" => "Integrated cancer care from chemo to follow-up"],
				["id" => 63952, "title" => "Ophthalmologist", "text" => "Connected vision charts, imaging, and prescriptions instantly"],
				["id" => 64395, "title" => "Hematologist", "text" => "Manage blood tests, transfusions, and reports seamlessly"],
				["id" => 64565, "title" => "General Surgeon", "text" => "Unified surgical scheduling, documentation & recovery tracking"],
				["id" => 65089, "title" => "Nutritionist", "text" => "Designed for nutrition management and effective meal planning"],
				["id" => 64722, "title" => "Neuropsychiatrists", "text" => "Combine therapy, medication, and neurology insights efficiently"],
				["id" => 64860, "title" => "ENT Surgeon", "text" => "Complete workflow for audiology, diagnosis, and procedures"],
				["id" => 64879, "title" => "Endocrinologist", "text" => "Specifically for thyroid disorder, diabetes, and hormonal imbalances"],
				["id" => 64957, "title" => "Neurologist", "text" => "Intuitive EMR platform for brain, spinal cord, and nerve diseases"],
				["id" => 64008, "title" => "Dermatologist", "text" => "Comprehensive platform for improving skin health"],
				["id" => 65404, "title" => "Osteopathy", "text" => "Structure assessments, treatments, and follow-ups clearly"],
				["id" => 63096, "title" => "Gynaecologists", "text" => "Specialized solution for managing pregnancy, IVF treatment"],
				["id" => 65552, "title" => "Family Medicine", "text" => "Intuitive EMR solution for improving family practice"],
			]
		],
		[
			"category" => "Primary",
			"color" => "#d97706",
			"col" => 2,
			"specialties" => [
				["id" => 62935, "title" => "Consultant Physicians", "text" => "Doctor’s first choice for optimizing clinical outcomes"],
				["id" => 64731, "title" => "General Medicine", "text" => "AI-powered EMR software for enhancing care and clinic efficiency"],
				["id" => 65816, "title" => "Internal Medicine", "text" => "Manage chronic diseases, tests, and care continuity"],
				// 			["id" => 65668, "title" => "Functional Medicine", "text" => "Designed to know the root cause of any healthcare problem"],
				["id" => 65974, "title" => "Pediatric", "text" => "Integrate growth charts, vaccinations, and follow-up care"],
				["id" => 63400, "title" => "Dental Care", "text" => "Simplify charting, billing, and patient dental workflows"],
			]
		],
		[
			"category" => "Allied",
			"emoji" => "🌿",
			"color" => "#dc2626",
			"col" => 2,
			"specialties" => [
				["id" => 65224, "title" => "Ayurveda", "text" => "Digitalize prakriti, panchakarma, and herbal treatment records"],
				["id" => 65300, "title" => "Homeopathy", "text" => "Organize repertory, prescriptions, and progress efficiently"]
			]
		],


	];

	if (empty($emr_specialties)) {
		return '<p>No EMR specialties found.</p>';
	}
	$grouped = [];
	foreach ($emr_specialties as $cat) {
		$col = isset($cat['col']) ? intval($cat['col']) : 1; // default col 1
		$grouped[$col][] = $cat;
	}
	ob_start(); // start buffering output
?>
<div class="speciality-megamenu-grid">
	<?php foreach ($grouped as $col_num => $categories): ?>
	<div class="column column-<?php echo esc_attr($col_num); ?>">
		<?php foreach ($categories as $cat): ?>
		<div class="category <?= esc_html($cat['category']); ?>">
			<h2 class="mb-3"><?= esc_html($cat['category']); ?></h2>
			<ul>
				<?php foreach ($cat['specialties'] as $item):
	$title = preg_replace('/^Best EMR Software For \s*/i', '', get_the_title($item['id']));
	$filename = $title.'.svg';

	// Define URLs
	$icon_url = 'https://healthray.com/wp-content/themes/stratusx-child/assets/speciIcon/' . $filename;
	$default_icon = 'https://healthray.com/wp-content/themes/stratusx-child/assets/speciIcon/doctor.svg';
				?>
				<li>
					<a class="mega-menu-link" href="<?= esc_url(get_permalink($item['id'])); ?>">
						<div class="mega-box">
							<div class="mega-icon">
								<img src="<?php echo esc_url($icon_url); ?>" alt="<?= get_the_title($item['id']); ?>">
							</div>
							<div class="mega-title">
								<div class="title"><?= esc_html($item['title']); ?></div>
								<div class="text"><?= esc_html($item['text']); ?></div>
							</div>
						</div>
					</a>
				</li>
				<?php endforeach; ?>
			</ul>
		</div>
		<?php endforeach; ?>
	</div>
	<?php endforeach; ?>
</div>
<?php
	return ob_get_clean(); // return buffered HTML
}