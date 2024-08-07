<?php
/*
Template Name: EMR State
*/
// the_content();

global $post;
$state_name = get_field('best_software_statecity_name');
$state_img = get_field('best_software_statecity_image');

if (has_post_thumbnail($post->ID) && get_post_thumbnail_id() != $state_img  ) {
	set_post_thumbnail($post->ID, $state_img);
}  else { 
	has_post_thumbnail($post->ID);
}
?>

<div class="best-in best-in-state best-emr-in-state">
	<!-- Hero -->
	<div class="section section-best-in">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="heading">
						<div class="headline-color">
							<h1 class="elementor-heading-title elementor-size-default">Best <span>EMR</span> Software in <span><?= $state_name; ?></span></h1>
						</div>
						<div class="description">
							<p>Looking for the best EMR software in <?= $state_name; ?>? Want affordable EMR software? Then, you are landing on the right page. Healthray is the single solution to your hospital's challenging situation. Try it now!</p>
						</div>
					</div>

					<div class="form-wrapper">
						<?= do_shortcode(' [contact-form-7 id="5803552" title="Hero Section CTA"]'); ?>
					</div>
				</div>
				<div class="half-width">
					<div class="image">
						<img width="500" height="397" src="https://healthray.com/wp-content/uploads/2024/07/Best-EMR-Software-in-india-Healthray.webp" class="attachment-full size-full wp-image-39234" alt="Best Emr Software In India Healthray">
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Healthcare System -->
	<div class="section section-healthcare">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="heading sec-heading">
						<div class="headline-color">
							<h2 class="elementor-heading-title elementor-size-default"><?= get_field('free_title'); ?> </h2>
						</div>
						<p><?= get_field('free_text'); ?></p>
					</div>
				</div>
				<div class="half-width">  
					<div class="image"> <?= wp_get_attachment_image(get_field('free_image'), 'full'); ?> </div>
				</div>
			</div>
		</div>
	</div>

	<!-- EMR functionalities -->
	<div class="section section-functionaly">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('leading_functions_title'); ?> </h2>
				</div>
			</div>

			<?php if (have_rows('leading_functions')) { ?>
			<div class="icon-box-container">
				<?php while (have_rows('leading_functions')) {
	the_row(); ?>
				<div class="icon-box-wrapper left-align">
					<div class="icon-box-icon">
						<span class="icon"> <?= wp_get_attachment_image(get_sub_field('icon'), 'full'); ?></span>
					</div>
					<div class="icon-box-content">
						<h3 class="icon-box-title"> <?= get_sub_field('title'); ?></h3>
						<p class="icon-box-description"><?= get_sub_field('text'); ?></p>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- EMR Software -->
	<div class="section section-benefits">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('advantages_title'); ?> </h2>
				</div>
			</div>

			<?php if (have_rows('advantages')) { ?>
			<div class="icon-box-container">
				<?php while (have_rows('advantages')) {
	the_row(); ?>
				<div class="icon-box-wrapper">
					<div class="icon-box-icon">
						<span class="icon"> <?= wp_get_attachment_image(get_sub_field('icon'), 'full'); ?></span>
					</div>
					<div class="icon-box-content">
						<h3 class="icon-box-title"> <?= get_sub_field('title'); ?></h3>
						<p class="icon-box-description"><?= get_sub_field('text'); ?></p>
					</div>
				</div>
				<?php } ?>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- Our Client Precence -->
	<div class="section section-client-precence section-template">
		<div class="container-full">
			<?= do_shortcode('[elementor-template id="40574"]'); ?>
		</div>
	</div>

	<!-- Healthray’s Products -->
	<div class="section section-procuct">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> Healthray’s <span>Products</span></h2>
				</div>
			</div>


			<?php if (have_rows('healthray_products')) { ?>
			<div class="swiper healthray-products-slider">
				<div class="swiper-wrapper">
					<?php while (have_rows('healthray_products')) {
	the_row(); ?>

					<div class="swiper-slide">
						<div class="product-slider">
							<div class="half-width">
								<div class="product-content">
									<h2> <?= get_sub_field('title'); ?></h2>
									<p><?= get_sub_field('text'); ?></p>
									<?= get_sub_field('extra_text'); ?>
								</div>
							</div>
							<div class="half-width">
								<div class="product-image image">
									<?= wp_get_attachment_image( get_sub_field('image'), 'full'); ?>
								</div>
							</div>
						</div>
					</div>

					<?php } ?>
				</div>

				<div class="flex">
					<div class="swiper-pagination"></div> 
				</div>
			</div>
			<?php } ?>
		</div>
	</div>

	<!-- All India -->
	<div class="section section-all-india">
		<div class="container">

			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default">Best EMR Management Software In <span><?= $state_name; ?></span></h2>
				</div>
			</div>
			<div class="image">
				<?= wp_get_attachment_image($state_img, 'full') ?>
			</div>
			<style>
				.list-widget { display: flex; flex-wrap: wrap; gap: 12px; margin-top: 20px; }
				.list-widget .list-widget-text { display: flex; }
				.list-widget .list-widget-text a {width: 100%; background-color: #FFFFFF; border: 1px solid var(--e-global-color-secondary); border-radius: 8px; padding: 8px 12px; }
			</style>
			<div class="list-widget width-auto">

				<div class="list-widget-text">
					<a href="https://healthray.com/best-emr-software-india/" target="_blank">
						<span>Best EMR Software In India</span>
					</a>
				</div> 
				<?php
	$prefix = "";
				global $post;
				$args = [
					'meta_key' => '_wp_page_template',
					'meta_value' => 'templates/template-emr-state-city.php',
					'posts_per_page' => -1
				];
				$post_id = $post->ID;
				$pages = get_pages($args);
				foreach ($pages as $index => $item) {
					$title = get_post_field('post_title', $item->ID, 'raw');
					
					$state = get_field('state_name_link', $item->ID);
					$state_id = $state->ID;
					if ($state_id == $post_id) { ?>
				<div class="list-widget-text">
					<a href="<?= get_permalink($item->ID); ?>" target="_blank" rel="nofollow"> <?= $title; ?> </a>
				</div>
				<?php }
				}
				?>
			</div>
		</div>
	</div>

	<style>
		.section-doctor{ background-image: url(<?= get_field('how_to_bg'); ?>);}
	</style>
	<div class="section section-doctor">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('how_to_title'); ?> </h2>
				</div>
			</div>

			<div class="th-d-flex">
				<div class="left-content content-box half-width">
					<h3>EMR software presides over a paper record system</h3>
					<p>Nowadays, most physicians write prescriptions on the computer, not on paper. Furthermore, they write on the electronic medical record software because of its tremendous functionalities. The advantages of Healthray’s EMR software are defined below: </p>
					<ul>
						<li>Connect with several hospital segments in an unified platform.</li>
						<li>Minimize upfront investment and reduce the major expenses on acquiring cabinets, paper and printing.</li>
						<li>Healthray is the best EMR software in <?= $state_name; ?> because it is 100% configurable and has a simple interface.</li>
						<li>EMR software requires limited resources to conduct their medical tasks and aids to optimize hospital space.</li>
						<li>With Healthray’s EMR software, the medical procedure is taking less amount of time and personnel efforts</li>
						<li>Helps to easily back-up the medical information and simplifies to find any particular information.</li>
						<li>EMR software can provide data access to anyone from any location and mitigates healthcare mistakes.</li>
						<li>Ease to track and analyze medical reports and facilitating high end security data.</li>
					</ul>
				</div>
				<div class="width-auto image">
					<img decoding="async" width="436" height="560" src="https://healthray.com/wp-content/uploads/2024/07/Doctor-Healthray.webp" class="attachment-large size-large wp-image-39208" alt="Doctor Healthray">
				</div>

				<div class="right-content content-box half-width">
					<h3>Redesign your healthcare system with an EMR software</h3>
					<p>With Healthray’s EMR software, it is easy to accumulate and extract data from any location. Moreover, this software aids to innovate the medical system and encompasses a flexible template that generates different medical forms in a short span of time. Here are a few of the advantages: </p>
					<ul>
						<li>Enhance physician practices with numerous AI technologies.Therefore, it improves healthcare efficiency.</li>
						<li>Provide a patient healthcare portal for systematically maintaining patient information. Also, provide some rights to them.</li>
						<li>Minimizes healthcare expenses on acquiring unnecessary stationary items, and efficiently administrates medical revenue.</li>
						<li>Patients can book appointments from anywhere, record all history, and can reschedule or cancel appointments as per their requirements.</li>
						<li>Supports in generating digital bills in the right format and conformance of billing regulation.</li>
					</ul>
				</div>
			</div>
		</div>
	</div>



	<!-- Technical Challenges -->
	<div class="section section-tech-challange">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<?php 
					$html= '';
					if (have_rows('technical_challenges')) { ?>
					<div class="icon-box-container technical-challenges-icon">
						<?php while (have_rows('technical_challenges')) {
						the_row();  
						$html .= '<div class="icon-box-wrapper">
							<div class="icon-box-icon">
								<span class="icon">'.wp_get_attachment_image(get_sub_field("icon"), "full").'</span>
							</div>
							<div class="icon-box-content">
								<h3 class="icon-box-title"> '. get_sub_field('title') .'</h3>
								<p class="icon-box-description">'. get_sub_field('text') .'</p>
							</div>
						</div>';

						if(get_row_index() % 2 == 0){
							echo '<div class="icon-box-wrapper-1">'. $html.'</div>';
							$html = '';
						}
					} ?>
					</div>
					<?php } ?>
				</div>
				<div class="half-width">
					<div class="heading sec-heading">
						<div class="headline-color">
							<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('technical_challenges_title'); ?> </h2>
						</div>
						<div class="texh-content"> <?= get_field('technical_challenges_text'); ?> </div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<?php
	$arrow = '<span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true"> <span class="elementor-toggle-icon-closed"><svg class="e-font-icon-svg e-fas-caret-right" viewBox="0 0 192 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"></path></svg></span>	<span class="elementor-toggle-icon-opened"><svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg></span></span>';
	?>
	<div class="section section-faq">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="pre-title">
					<p class="elementor-heading-title elementor-size-default">Get Answer</p>
				</div>
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default">Frequently Asked Questions</h2>
				</div>
			</div>

			<div class="elementor-toggle accordion-list">
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-1" class="elementor-tab-title" data-tab="1" role="button" aria-controls="elementor-tab-content-1" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">What is the importance of EMR Software?</a>
					</div>
					<div id="elementor-tab-content-1" class="elementor-tab-content elementor-clearfix answer" data-tab="1" role="region" aria-labelledby="elementor-tab-title-1">
						<p>Electronic medical record software has vast importance in transforming medical facilities. Moreover, it centralizes the medical data information for finding critical patient medical data and making decisions related to medical inventory and medical finance. Furthermore, it continuously updates patient data and maintains hospital workflow. Consequently, streamlines operational tasks and lowers medical personnel efforts.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-2" class="elementor-tab-title" data-tab="2" role="button" aria-controls="elementor-tab-content-2" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">Why to use the best EMR software for small practices?</a>
					</div>
					<div id="elementor-tab-content-2" class="elementor-tab-content elementor-clearfix answer" data-tab="2" role="region" aria-labelledby="elementor-tab-title-2">
						<p>Small clinics have less resources and space. Moreover, the electronic medical record software comprises many attributes, which eases the medical work and incurs minimal investment. Moreover, the best attributes are stock management, custom templates, data integrity, consistently follows healthcare regulation, and interlaced to advanced patient tools.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-3" class="elementor-tab-title" data-tab="3" role="button" aria-controls="elementor-tab-content-3" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">How does EMR Software improve operational workflow?</a>
					</div>
					<div id="elementor-tab-content-3" class="elementor-tab-content elementor-clearfix answer" data-tab="3" role="region" aria-labelledby="elementor-tab-title-3">
						<p>The electronic medical record software is eradicating the traditional healthcare system and bringing an advanced system that totally reshapes the healthcare services whether it is related to managing patients, tracking employee activity, minimizing expenses, efficiently administering financial accounts, and generating healthcare invoices. Therefore, it improves all operational workflow.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-4" class="elementor-tab-title" data-tab="4" role="button" aria-controls="elementor-tab-content-4" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">Does EMR software eliminate medication errors?</a>
					</div>
					<div id="elementor-tab-content-4" class="elementor-tab-content elementor-clearfix answer" data-tab="4" role="region" aria-labelledby="elementor-tab-title-4">
						<p>Indeed, the EMR Software eliminates medication errors. This software automates the clinical workflow, digitizes the healthcare system, and facilitates remote device integrity. Moreover, it aids to reduce healthcare errors whether it is related to writing prescriptions or billing.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-5" class="elementor-tab-title" data-tab="5" role="button" aria-controls="elementor-tab-content-5" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">How to maintain administrative records by EMR Software?</a>
					</div>
					<div id="elementor-tab-content-5" class="elementor-tab-content elementor-clearfix answer" data-tab="5" role="region" aria-labelledby="elementor-tab-title-5">
						<p>The EMR Software assists to store innumerable documents whether it is related to insurance or staff experience certificates. Moreover, it sorts all data and makes data more organized. After that, data is created in the visualization format . Consequently, it is a cinch to make the correct decision and share medical records to anyone.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-6" class="elementor-tab-title" data-tab="6" role="button" aria-controls="elementor-tab-content-6" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">What are the advantages of custom templates?</a>
					</div>
					<div id="elementor-tab-content-6" class="elementor-tab-content elementor-clearfix answer" data-tab="6" role="region" aria-labelledby="elementor-tab-title-6">
						<p>The custom templates can create any type of medical form whether it is consent form or discharge template. Moreover, it has a drag and drop attribute which provides convenience to patients and clinical staff. It has other features such as a sharing option and editing tool. Consequently, it makes diagnostic procedures fast, saves physicians and clinical staff’s time.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-7" class="elementor-tab-title" data-tab="7" role="button" aria-controls="elementor-tab-content-7" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">Which is the leading EMR software in India?</a>
					</div>
					<div id="elementor-tab-content-7" class="elementor-tab-content elementor-clearfix answer" data-tab="7" role="region" aria-labelledby="elementor-tab-title-7">
						<p>Without any confusion, Healthray is the leading EMR Software in India. Moreover, it makes a cinch to administering physicians appointments, manages several customers at one time, minimizes errors, getting alerts promptly, and effortlessly associates with other experienced physicians. Therefore, it improves your healthcare procedure, simplifies the patient medical system and enhances organization revenue.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-8" class="elementor-tab-title" data-tab="8" role="button" aria-controls="elementor-tab-content-8" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">Does EMR Software eradicate the patient waiting system?</a>
					</div>
					<div id="elementor-tab-content-8" class="elementor-tab-content elementor-clearfix answer" data-tab="8" role="region" aria-labelledby="elementor-tab-title-8">
						<p>Indeed, EMR Software eradicates the patient waiting system. It is the online healthcare platform that has the immense ability to perform all medical work with ease. Furthermore, the work may allocate tasks among employees, managing appointments, create an invoice bill, handling patient queries, virtual consultation, and arranging data properly. Moreover, these tasks basically don’t need in-person visits with physicians.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-9" class="elementor-tab-title" data-tab="9" role="button" aria-controls="elementor-tab-content-9" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">Does EMR Software improve clinical efficiency?</a>
					</div>
					<div id="elementor-tab-content-9" class="elementor-tab-content elementor-clearfix answer" data-tab="9" role="region" aria-labelledby="elementor-tab-title-9">
						<p>Indeed, EMR Software improves clinical efficiency. Moreover, It includes innumerable tasks such as scheduling management, effortless creation of e-invoices, managing medical inventory, and efficiently records whole data. Additionally , it improves managerial level performance and clinical efficiency.</p>
					</div>
				</div>
				<div class="elementor-toggle-item">
					<div id="elementor-tab-title-10" class="elementor-tab-title" data-tab="10" role="button" aria-controls="elementor-tab-content-10" aria-expanded="false">
						<?= $arrow; ?>
						<a class="elementor-toggle-title" tabindex="0">What are the advantages of eliminating the paper records system?</a>
					</div>
					<div id="elementor-tab-content-10" class="elementor-tab-content elementor-clearfix answer" data-tab="10" role="region" aria-labelledby="elementor-tab-title-10">
						<p>The primary benefit of eliminating the paper record system is to create a sustainable environment. Moreover, it minimizes numerous expenses that are incurred on procuring paper, printing, stamps and managing them. This is possible by applying modern healthcare technology like Healthray’s EMR Software.</p>
					</div>
				</div>
			</div>
			<script type="application/ld+json">
                        {"@context":"https:\/\/schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":"What is the importance of EMR Software?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;Electronic medical record software has vast importance in transforming medical facilities. Moreover, it centralizes the medical data information for finding critical patient medical data and making decisions related to medical inventory and medical finance. Furthermore, it continuously updates patient data and maintains hospital workflow. Consequently, streamlines operational tasks and lowers medical personnel efforts.&lt;\/p&gt;\n"}},{"@type":"Question","name":"Why to use the best EMR software for small practices?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;Small clinics have less resources and space. Moreover, the electronic medical record software comprises many attributes, which eases the medical work and incurs minimal investment. Moreover, the best attributes are stock management, custom templates, data integrity, consistently follows healthcare regulation, and interlaced to advanced patient tools.&lt;\/p&gt;\n"}},{"@type":"Question","name":"How does EMR Software improve operational workflow?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;The electronic medical record software is eradicating the traditional healthcare system and bringing an advanced system that totally reshapes the healthcare services whether it is related to managing patients, tracking employee activity, minimizing expenses, efficiently administering financial accounts, and generating healthcare invoices. Therefore, it improves all operational workflow.&lt;\/p&gt;\n"}},{"@type":"Question","name":"Does EMR software eliminate medication errors?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;Indeed, the EMR Software eliminates medication errors. This software automates the clinical workflow, digitizes the healthcare system, and facilitates remote device integrity. Moreover, it aids to reduce healthcare errors whether it is related to writing prescriptions or billing.&lt;\/p&gt;\n"}},{"@type":"Question","name":"How to maintain administrative records by EMR Software?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;The EMR Software assists to store innumerable documents whether it is related to insurance or staff experience certificates. Moreover, it sorts all data and makes data more organized. After that, data is created in the visualization format . Consequently, it is a cinch to make the correct decision and share medical records to anyone.&lt;\/p&gt;\n"}},{"@type":"Question","name":"What are the advantages of custom templates?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;The custom templates can create any type of medical form whether it is consent form or discharge template. Moreover, it has a drag and drop attribute which provides convenience to patients and clinical staff. It has other features such as a sharing option and editing tool. Consequently, it makes diagnostic procedures fast, saves physicians and clinical staff\u2019s time.&lt;\/p&gt;\n"}},{"@type":"Question","name":"Which is the leading EMR software in India?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;Without any confusion, Healthray is the leading EMR Software in India. Moreover, it makes a cinch to administering physicians appointments, manages several customers at one time, minimizes errors, getting alerts promptly, and effortlessly associates with other experienced physicians. Therefore, it improves your healthcare procedure, simplifies the patient medical system and enhances organization revenue.&lt;\/p&gt;\n"}},{"@type":"Question","name":"Does EMR Software eradicate the patient waiting system?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;Indeed, EMR Software eradicates the patient waiting system. It is the online healthcare platform that has the immense ability to perform all medical work with ease. Furthermore, the work may allocate tasks among employees, managing appointments, create an invoice bill, handling patient queries, virtual consultation, and arranging data properly. Moreover, these tasks basically don&#8217;t need in-person visits with physicians.&lt;\/p&gt;\n"}},{"@type":"Question","name":"Does EMR Software improve clinical efficiency?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;Indeed, EMR Software improves clinical efficiency. Moreover, It includes innumerable tasks such as scheduling management, effortless creation of e-invoices, managing medical inventory, and efficiently records whole data. Additionally , it improves managerial level performance and clinical efficiency.&lt;\/p&gt;\n"}},{"@type":"Question","name":"What are the advantages of eliminating the paper records system?","acceptedAnswer":{"@type":"Answer","text":"&lt;p&gt;The primary benefit of eliminating the paper record system is to create a sustainable environment. Moreover, it minimizes numerous expenses that are incurred on procuring paper, printing, stamps and managing them. This is possible by applying modern healthcare technology like Healthray\u2019s EMR Software.&lt;\/p&gt;\n"}}]}				</script>
		</div>
	</div>

	<div class="section section-footer-cta section-template">
		<div class="container-full">
			<?= do_shortcode('[elementor-template id="26869"]'); ?>
		</div>
	</div>
</div>