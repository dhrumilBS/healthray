<?php
/*
Template Name: Lab State
*/

global $post;

add_action('do_meta_boxes', 'remove_thumbnail_box');
function remove_thumbnail_box() {
	remove_meta_box( 'postimagediv','page','side' );
}

$state_name = get_field('best_software_statecity_name');
$statecity_img = get_field('best_software_statecity_image');
// echo "state_img-" .$statecity_img;
// set_post_thumbnail($post, $statecity_img);
// echo "get_post_thumbnail_id-".get_post_thumbnail_id();

// Toogle Arrow
$toogleArrow = '<span class="elementor-toggle-icon elementor-toggle-icon-left" aria-hidden="true">
<span class="elementor-toggle-icon-closed"><svg class="e-font-icon-svg e-fas-caret-right" viewBox="0 0 192 512" xmlns="http://www.w3.org/2000/svg"><path d="M0 384.662V127.338c0-17.818 21.543-26.741 34.142-14.142l128.662 128.662c7.81 7.81 7.81 20.474 0 28.284L34.142 398.804C21.543 411.404 0 402.48 0 384.662z"></path></svg></span>
<span class="elementor-toggle-icon-opened"><svg class="elementor-toggle-icon-opened e-font-icon-svg e-fas-caret-up" viewBox="0 0 320 512" xmlns="http://www.w3.org/2000/svg"><path d="M288.662 352H31.338c-17.818 0-26.741-21.543-14.142-34.142l128.662-128.662c7.81-7.81 20.474-7.81 28.284 0l128.662 128.662c12.6 12.599 3.676 34.142-14.142 34.142z"></path></svg></span>
</span>';

?>

<div class="best-in best-in-state" style=" background-color: #F6F9FF; ">

	<!--  ===================================================================================================================================	 -->
	<!--  ========================   Section Best In   ==============================	 -->
	<!--  ===================================================================================================================================	 -->

	<div class="section section-best-in">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="heading">
						<div class="headline-color">
							<h1 class="elementor-heading-title elementor-size-default">Best <span>Lab</span> Software in <span><?= $state_name; ?></span></h1>
						</div>
						<div class="description">
							<p>Administrate multiple laboratory activities for efficient data recording to resource planning within a few minutes. Furthermore, it effortlessly exchanges information with other departments. Want to try the best lab software in <?= $state_name; ?>? Then, first fill out the form below.</p>
						</div>
					</div>

					<div class="form-wrapper">
						<?= do_shortcode('[elementor-template id="39420"]'); ?>
					</div>
				</div>
				<div class="half-width">
					<div class="image">
						<img src="https://healthray.com/wp-content/uploads/2024/07/Best-Lab-Software-in-india-Healthray.webp" alt="Best Lab Software In India Healthray" width="576" height="562">
					</div>
				</div>
			</div>
		</div>
	</div>


	<!-- ===================================================================================================================================	 -->
	<!--  ============================  Section Lab Testimonial  ===========================	 -->
	<!--  ===================================================================================================================================	 -->
	<div class="section section-features section-template">
		<div class="container-full">
			<?= do_shortcode('[elementor-template id="38272"]'); ?>
		</div>
	</div>



	<!--  =================================================================================================================================== -->
	<!--  ============================  Section Features  ===========================	 -->
	<!-- ===================================================================================================================================	 -->

	<div class="section section-features">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"> <?= get_field('selecting_healthray_lab_title'); ?></h2>
				</div>
			</div>

			<?php if (have_rows('selecting_healthray')){ ?>
			<div class="icon-box-container">
				<?php while (have_rows('selecting_healthray')){ the_row(); ?> 
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

	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->

	<div class="section section-qr-reports">
		<div class="container">
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="heading sec-heading">
						<div class="headline-color">
							<h2 class="elementor-heading-title elementor-size-default"><?= get_field('qr_title'); ?></h2>
						</div>
						<p><?= get_field('qr_text'); ?></p>
					</div>
				</div>
				<div class="half-width">
					<div class="image"> <?= wp_get_attachment_image(get_field('qr_image'), 'full'); ?> </div>
				</div>
			</div>
		</div>
	</div>



	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->



	<div class="section section-sms-alert">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color">
					<h2 class="elementor-heading-title elementor-size-default"><?= get_field('sms_alerts_title'); ?></h2>
				</div>
				<p><?= get_field('sms_alerts_text'); ?></p>
			</div>

			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="image"> <?= wp_get_attachment_image(get_field('sms_alerts_image'), 'full'); ?> </div>
				</div>

				<div class="half-width">
					<?php if (have_rows('sms_alerts')){ ?>
					<div class="icon-box-widget"> 
						<div class="icon-box-container vertical-align">
							<?php while (have_rows('sms_alerts')){ the_row(); ?> 
							<div class="icon-box-wrapper icon-left" style="--icon-box-bg: <?= get_sub_field('bg_color'); ?>; ">
								<div class="icon-box-icon">
									<span class="icon"><?= wp_get_attachment_image(get_sub_field('icon'), 'full'); ?> </span>
								</div>
								<div class="icon-box-content">
									<h3 class="icon-box-title"> <?= get_sub_field('title'); ?></h3>
									<p class="icon-box-description"><?= get_sub_field('text'); ?></p>
								</div>
							</div>
							<?php } ?>
						</div>
						<!-- End Loop -->						
					</div>

					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->


	<div class="section section-all-india">
		<div class="container">
			<div class="heading sec-heading centered">
				<div class="headline-color ">
					<h2 class="elementor-heading-title elementor-size-default">Best Lab Management Software In <span><?= $state_name; ?></span></h2>
				</div>
			</div>
			<div class="th-d-flex th-align-items-center">
				<div class="half-width">
					<div class="image">
						<?= wp_get_attachment_image($statecity_img, 'full') ?>
					</div>
				</div>
				<div class="half-width">
					<style>
						.list-widget {--gap: 12px; display: flex; flex-wrap: wrap; gap: var(--gap); }
						.list-widget.width-auto .list-widget-text { width: auto; } 
						.list-widget .list-widget-text { display: flex; width: calc((100% - calc((var(--items) - 1) * var(--gap))) / var(--items)); }
						.list-widget .list-widget-text a { width: 100%; background-color: #FFFFFF; border: 1px solid var(--e-global-color-secondary); border-radius: 8px; padding: 8px 12px; }
						.list-widget .list-widget-text a:hover { background-color: var(--e-global-color-secondary); color: #fff; }
						@media screen and (max-width: 767px){ 
							.list-widget {margin-top: var(--gap); }
						}
					</style>
					<div class="list-widget width-auto">

						<div class="list-widget width-auto">
							<div class="list-widget-text">
								<a href="https://healthray.com/best-lab-software-india/" target="_blank">
									<span>India</span>
								</a>
							</div> 
						</div>

						<?php
	global $post;
						$args = [
							'meta_key' => '_wp_page_template',
							'meta_value' => 'templates/template-lab-state-city.php',
							'posts_per_page' => -1
						];
						$post_id = $post->ID;
						$pages = get_pages($args);
						foreach ($pages as $index => $item) {						 
							$id = $item->ID;
							$state = get_field('state_name_link', $id);
							$state_id = $state->ID;
							$title = get_post_field('post_title', $id, 'raw');
							if ($state_id == $post_id) { ?>
						<div class="list-widget-text"><a href="<?= get_permalink($item->ID); ?>" target="_blank" rel="nofollow"><?= $title; ?></a></div>
						<?php }
						}
						?>
					</div>
				</div>
			</div>
		</div>
	</div>

	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->
	<!--  ===================================================================================================================================	 -->


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

			<div class="faq-content">
				<div class="elementor-toggle accordion-list">
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-1" class="elementor-tab-title" data-tab="1" role="button" aria-controls="elementor-tab-content-1" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Does lab software minimize time and personnel efforts?</a>
						</div>
						<div id="elementor-tab-content-1" class="elementor-tab-content elementor-clearfix answer" data-tab="1" role="region" aria-labelledby="elementor-tab-title-1">
							<p>Indeed, lab software minimizes time and personnel efforts. This software has multiple features that help to take accurate lab decisions and build a collaborative team. Furthermore, the attributes include scheduling notification, automated reports, remote accessibility, persistent regulation compliance, and aids to maintain accurate lab documents.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-2" class="elementor-tab-title" data-tab="2" role="button" aria-controls="elementor-tab-content-2" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Which is the best lab software in <?= $state_name; ?>?</a>
						</div>
						<div id="elementor-tab-content-2" class="elementor-tab-content elementor-clearfix answer" data-tab="2" role="region" aria-labelledby="elementor-tab-title-2">
							<p>Healthray is the best lab software in <?= $state_name; ?>. Not even <?= $state_name; ?> but in most of the Indian regions, it is so popular and too user-friendly healthcare platform. Moreover, it has numerous unique characteristics such as remote patient handling, virtual monitoring, precise specimen tracking steps, cloud system integration, automation tools, and more. Therefore, it minimizes redundancy and improves laboratory workflow.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-3" class="elementor-tab-title" data-tab="3" role="button" aria-controls="elementor-tab-content-3" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What are the unique features of the best lab software?</a>
						</div>
						<div id="elementor-tab-content-3" class="elementor-tab-content elementor-clearfix answer" data-tab="3" role="region" aria-labelledby="elementor-tab-title-3">
							<p>Lab software is the ultimate software that helps to instantly raise lab income and provide modern infrastructure. Furthermore, it has innumerable unique features of the best lab software such as sample management, resource management, implementing lab policies, reports on unified screen, and simple dashboard interface.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-4" class="elementor-tab-title" data-tab="4" role="button" aria-controls="elementor-tab-content-4" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Why should labs select the LIMS software for improving efficiency?</a>
						</div>
						<div id="elementor-tab-content-4" class="elementor-tab-content elementor-clearfix answer" data-tab="4" role="region" aria-labelledby="elementor-tab-title-4">
							<p>Labs select the LIMS software on account of its some functionalities that advances the laboratory system. Moreover, it provides ready-made templates design that can be substituted with another element through drag and drop API. Additionally, it helps to conduct smooth departmental tasks and enhance immense communication among each other.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-5" class="elementor-tab-title" data-tab="5" role="button" aria-controls="elementor-tab-content-5" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What is the main purpose of the LIMS software?</a>
						</div>
						<div id="elementor-tab-content-5" class="elementor-tab-content elementor-clearfix answer" data-tab="5" role="region" aria-labelledby="elementor-tab-title-5">
							<p>Administering all samples at a time and providing unifying information in the single platform. Moreover, this Lab software connects with numerous segments and can meet with the entire team. The main purpose of the LIMS software is to bring entire specimen information in a unified lab platform, handle personnel performance from anywhere, integrate with incorporate with QR code, and timely manage patient inquiries.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">	
						<div id="elementor-tab-title-6" class="elementor-tab-title" data-tab="6" role="button" aria-controls="elementor-tab-content-6" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What are the ways to improve laboratory business?</a>
						</div>
						<div id="elementor-tab-content-6" class="elementor-tab-content elementor-clearfix answer" data-tab="6" role="region" aria-labelledby="elementor-tab-title-6">
							<p>Successful laboratory business needs tons of effort and extensive time for patient centered- care. There are various AI software developed to lower mistakes and improve patient safety. Furthermore, the best lab software in India is Healthray because of its ultimate functionalities such as facilitating BI reports, customized test reports, friendly interface, and efficient sample processing. However, there are other incorporated features for modernizing laboratories.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-7" class="elementor-tab-title" data-tab="7" role="button" aria-controls="elementor-tab-content-7" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Does Lab Software store radiology images?</a>
						</div>
						<div id="elementor-tab-content-7" class="elementor-tab-content elementor-clearfix answer" data-tab="7" role="region" aria-labelledby="elementor-tab-title-7">
							<p>Indeed, lab software stores radiology images. Moreover, It aids in accurate diagnosis, which is the most crucial component to enhance patient care and reduces lab time. Similarly, it improves response time. Physicians can attend to customers from their home and increase their average income.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-8" class="elementor-tab-title" data-tab="8" role="button" aria-controls="elementor-tab-content-8" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Which is the latest innovation in the modern labs?</a>
						</div>
						<div id="elementor-tab-content-8" class="elementor-tab-content elementor-clearfix answer" data-tab="8" role="region" aria-labelledby="elementor-tab-title-8">
							<p>The lab software is the latest innovation in the modern labs. Furthermore, it revolutionized the laboratory workflow system. Moreover, It has a plethora of features such as remote appointment system, workflows management, graphical reports, finance management, and effective specimen tracking. Simultaneously, it reduces laboratory wastes and enhances lab efficiency.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-9" class="elementor-tab-title" data-tab="9" role="button" aria-controls="elementor-tab-content-9" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Does LIMS software help to accumulate historical billing records?</a>
						</div>
						<div id="elementor-tab-content-9" class="elementor-tab-content elementor-clearfix answer" data-tab="9" role="region" aria-labelledby="elementor-tab-title-9">
							<p>Indeed, LIMS software helps to accumulate historical billing records. This software system has included one function that is an online billing system. Furthermore, it aids to create invoices as per requirement. Also, it helps to capture historical records and apply billing codes. Overall, it reduces the patient ‘s stress level to store a huge amount of bills.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-10" class="elementor-tab-title" data-tab="10" role="button" aria-controls="elementor-tab-content-10" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Does LIMS software aid inventory control?</a>
						</div>
						<div id="elementor-tab-content-10" class="elementor-tab-content elementor-clearfix answer" data-tab="10" role="region" aria-labelledby="elementor-tab-title-10">
							<p>Indeed, LIMS software aids inventory control. In laboratories, managing inventory is the most important and cumbersome task. Moreover, this software administrates all lab equipment in the structured format. It provides current inventory information and helps to enhance the quality of lab inventory.</p>
						</div>
					</div>
				</div>
				<script type="application/ld+json">
						{"@context":"https:\/\/schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":"Does lab software minimize time and personnel efforts?","acceptedAnswer":{"@type":"Answer","text":"Indeed, lab software minimizes time and personnel efforts. This software has multiple features that help to take accurate lab decisions and build a collaborative team. Furthermore, the attributes include scheduling notification, automated reports, remote accessibility, persistent regulation compliance, and aids to maintain accurate lab documents."}},{"@type":"Question","name":"Which is the best lab software in <?= $state_name; ?>?","acceptedAnswer":{"@type":"Answer","text":"Healthray is the best lab software in <?= $state_name; ?>. Not even <?= $state_name; ?> but in most of the Indian regions, it is so popular and too user-friendly healthcare platform. Moreover, it has numerous unique characteristics such as remote patient handling, virtual monitoring, precise specimen tracking steps, cloud system integration, automation tools, and more. Therefore, it minimizes redundancy and improves laboratory workflow."}},{"@type":"Question","name":"What are the unique features of the best lab software?","acceptedAnswer":{"@type":"Answer","text":"Lab software is the ultimate software that helps to instantly raise lab income and provide modern infrastructure. Furthermore, it has innumerable unique features of the best lab software such as sample management, resource management, implementing lab policies, reports on unified screen, and simple dashboard interface."}},{"@type":"Question","name":"Why should labs select the LIMS software for improving efficiency?","acceptedAnswer":{"@type":"Answer","text":"Labs select the LIMS software on account of its some functionalities that advances the laboratory system. Moreover, it provides ready-made templates design that can be substituted with another element through drag and drop API. Additionally, it helps to conduct smooth departmental tasks and enhance immense communication among each other."}},{"@type":"Question","name":"What is the main purpose of the LIMS software?","acceptedAnswer":{"@type":"Answer","text":"Administering all samples at a time and providing unifying information in the single platform. Moreover, this Lab software connects with numerous segments and can meet with the entire team. The main purpose of the LIMS software is to bring entire specimen information in a unified lab platform, handle personnel performance from anywhere, integrate with incorporate with QR code, and timely manage patient inquiries."}},{"@type":"Question","name":"What are the ways to improve laboratory business?","acceptedAnswer":{"@type":"Answer","text":"Successful laboratory business needs tons of effort and extensive time for patient centered- care. There are various AI software developed to lower mistakes and improve patient safety. Furthermore, the best lab software in India is Healthray because of its ultimate functionalities such as facilitating BI reports, customized test reports, friendly interface, and efficient sample processing. However, there are other incorporated features for modernizing laboratories."}},{"@type":"Question","name":"Does Lab Software store radiology images?","acceptedAnswer":{"@type":"Answer","text":"Indeed, lab software stores radiology images. Moreover, It aids in accurate diagnosis, which is the most crucial component to enhance patient care and reduces lab time. Similarly, it improves response time. Physicians can attend to customers from their home and increase their average income."}},{"@type":"Question","name":"Which is the latest innovation in the modern labs?","acceptedAnswer":{"@type":"Answer","text":"The lab software is the latest innovation in the modern labs. Furthermore, it revolutionized the laboratory workflow system. Moreover, It has a plethora of features such as remote appointment system, workflows management, graphical reports, finance management, and effective specimen tracking. Simultaneously, it reduces laboratory wastes and enhances lab efficiency."}},{"@type":"Question","name":"Does LIMS software help to accumulate historical billing records?","acceptedAnswer":{"@type":"Answer","text":"Indeed, LIMS software helps to accumulate historical billing records. This software system has included one function that is an online billing system. Furthermore, it aids to create invoices as per requirement. Also, it helps to capture historical records and apply billing codes. Overall, it reduces the patient &#8216;s stress level to store a huge amount of bills."}},{"@type":"Question","name":"Does LIMS software aid inventory control?","acceptedAnswer":{"@type":"Answer","text":"Indeed, LIMS software aids inventory control. In laboratories, managing inventory is the most important and cumbersome task. Moreover, this software administrates all lab equipment in the structured format. It provides current inventory information and helps to enhance the quality of lab inventory.
						"}}]}				</script>
			</div>
		</div>
	</div>
	<!--  ===================================================================================================================================	 -->
	<!--  =========================================================== Footer CTA  ===========================================================	 -->
	<!--  ===================================================================================================================================	 -->
	<div class="section section-footer-cta section-template">
		<div class="container-full">
			<?= do_shortcode('[elementor-template id="26869"]'); ?>
		</div>
	</div>
</div>