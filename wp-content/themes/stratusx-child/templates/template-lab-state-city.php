<?php
/*
Template Name: Lab State City
*/
global $post;
$city_name = get_field('best_software_statecity_name');
$statecity_img = get_field('best_software_statecity_image');

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
							<h1 class="elementor-heading-title elementor-size-default">Best <span>Lab</span> Software in <span><?= $city_name; ?></span></h1>
						</div>
						<div class="description">
							<p>Want to digitize your laboratory software? This one is a perfect fit for you. Upgrade your lab with the best lab software in <?= $city_name; ?>. Get it now!</p>
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
					<h2 class="elementor-heading-title elementor-size-default">Best Lab Management Software In <span><?= $city_name; ?></span></h2>
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
						.list-widget { --gap: 12px;display: flex; flex-wrap: wrap; gap: var(--gap); }
						.list-widget.width-auto .list-widget-text { width: auto; } 
						.list-widget .list-widget-text { display: flex; width: calc((100% - calc((var(--items) - 1) * var(--gap))) / var(--items)); }
						.list-widget .list-widget-text a { width: 100%; background-color: #FFFFFF; border: 1px solid var(--e-global-color-secondary); border-radius: 8px; padding: 8px 12px; }
						.list-widget .list-widget-text a:hover { background-color: var(--e-global-color-secondary); color: #fff; }
					</style>
					<div class="list-widget width-auto">
						<div class="list-widget-text">
							<a href="https://healthray.com/best-lab-software-india/" target="_blank">
								<span>India</span>
							</a>
						</div> 
						<?php $state_name = get_field('state_name'); $state_link = get_field('state_name_link'); ?>
						<div class="list-widget-text">
							<a href="<?= get_permalink($state_link->ID); ?>" target="_blank">
								<span><?= $state_name; ?></span>
							</a>
						</div> 
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
							<a class="elementor-toggle-title" tabindex="0"> How do laboratories choose the leading pathology lab software? </a>
						</div>
						<div id="elementor-tab-content-1" class="elementor-tab-content elementor-clearfix answer" data-tab="1" role="region" aria-labelledby="elementor-tab-title-1">
							<p>Choosing the leading pathology lab software needs to adhere to several steps. Firstly, determine laboratory requirements, find out the challenges, meet with the managers, scrap the top 10 lab software, and finally, choose the one which suits your laboratory.Additionally,  the considerable features are scalability, data integrity, and conformity to laboratory codes.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-2" class="elementor-tab-title" data-tab="2" role="button" aria-controls="elementor-tab-content-2" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">How does LIMS Software seamlessly integrate data?</a>
						</div>
						<div id="elementor-tab-content-2" class="elementor-tab-content elementor-clearfix answer" data-tab="2" role="region" aria-labelledby="elementor-tab-title-2">
							<p>The lab information management system software accumulates lab information from personnel details to the storage information. Moreover, it combined data from various segments such as finance department, sample management department, reporting management, instrument management, and audit management. Additionally, it records patient information with demographic details.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-3" class="elementor-tab-title" data-tab="3" role="button" aria-controls="elementor-tab-content-3" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What are the benefits of barcode scanners in labs?</a>
						</div>
						<div id="elementor-tab-content-3" class="elementor-tab-content elementor-clearfix answer" data-tab="3" role="region" aria-labelledby="elementor-tab-title-3">
							<p>Barcode scanner helps to print bills quickly and ease sending code to any person. Moreover, it aids to convert reports into pdf format, saves immense time, and simplifies the process of collecting large amounts of payments. Consequently, patients are extremely glad with this feature.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-4" class="elementor-tab-title" data-tab="4" role="button" aria-controls="elementor-tab-content-4" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Which is the best lab software in <?= $city_name; ?>?</a>
						</div>
						<div id="elementor-tab-content-4" class="elementor-tab-content elementor-clearfix answer" data-tab="4" role="region" aria-labelledby="elementor-tab-title-4">
							<p>Healthray is one of the best lab software in <?= $city_name; ?>. Moreover, this software has several functions such as revenue management, specimen management, administrative department, and instrument department. Furthermore, it improves lab efficiency, enhances accuracy, specimen traceability, and effective lab decisions.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-5" class="elementor-tab-title" data-tab="5" role="button" aria-controls="elementor-tab-content-5" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Does Lab Software eliminate complexities?</a>
						</div>
						<div id="elementor-tab-content-5" class="elementor-tab-content elementor-clearfix answer" data-tab="5" role="region" aria-labelledby="elementor-tab-title-5">
							<p>Indeed, lab software eliminates complexities. Laboratories have numerous troubles such as difficulty to monitor lab inventories, hard to administer information, take long times to find a single specimen, stand in queue for hours, and waste lab resources. Furthermore, the lab software removes all the discussed challenges through automated laboratory software.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">	
						<div id="elementor-tab-title-6" class="elementor-tab-title" data-tab="6" role="button" aria-controls="elementor-tab-content-6" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What is the importance of the best lab software?</a>
						</div>
						<div id="elementor-tab-content-6" class="elementor-tab-content elementor-clearfix answer" data-tab="6" role="region" aria-labelledby="elementor-tab-title-6">
							<p>The best lab software has numerous functionalities such as recording each specimen information in the well-defined format, ease to track records, retrieve information at any time you want, reduce laboratory mistakes, eliminate human intervention, and ease conformance with laboratory regulations such as ISO 17025 and GAMP. Additionally, it is effortlessly embedded with the electronic signatures.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-7" class="elementor-tab-title" data-tab="7" role="button" aria-controls="elementor-tab-content-7" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What are the benefits of the LIMS system software?</a>
						</div>
						<div id="elementor-tab-content-7" class="elementor-tab-content elementor-clearfix answer" data-tab="7" role="region" aria-labelledby="elementor-tab-title-7">
							<p>The LIMS system software is mainly used to maintain the data in the standardized format and enhance the employee capabilities to work beyond their limits in less time. Furthermore, it has numerous benefits such as scheduling system, prompt reminders, instant message through platform, specimen management, and precise stock control. Additionally, it needs low upfront costs.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-8" class="elementor-tab-title" data-tab="8" role="button" aria-controls="elementor-tab-content-8" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">Does lab software integrate several segments?</a>
						</div>
						<div id="elementor-tab-content-8" class="elementor-tab-content elementor-clearfix answer" data-tab="8" role="region" aria-labelledby="elementor-tab-title-8">
							<p>Indeed, lab software has the vast potency to connect with various segments such as scheduling department, instrument management department, specimen department, reception department, patient department, and digital invoicing department. Moreover, it connects with each other and aids in creating a collaborative environment.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-9" class="elementor-tab-title" data-tab="9" role="button" aria-controls="elementor-tab-content-9" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What are the benefits of digitalization in laboratories?</a>
						</div>
						<div id="elementor-tab-content-9" class="elementor-tab-content elementor-clearfix answer" data-tab="9" role="region" aria-labelledby="elementor-tab-title-9">
							<p>Digitalization in laboratories creates an immense transformation and provides customers too much lab facility. Furthermore, it aids to create a digital presence of laboratories and helps to improve laboratory infrastructure. The best digitalization of laboratories comes with the best lab software lIke Healthray, which simultaneously handles all laboratory activities.</p>
						</div>
					</div>
					<div class="elementor-toggle-item">
						<div id="elementor-tab-title-10" class="elementor-tab-title" data-tab="10" role="button" aria-controls="elementor-tab-content-10" aria-expanded="false">
							<?= $toogleArrow; ?>
							<a class="elementor-toggle-title" tabindex="0">What are the distinct features of the lab software?</a>
						</div>
						<div id="elementor-tab-content-10" class="elementor-tab-content elementor-clearfix answer" data-tab="10" role="region" aria-labelledby="elementor-tab-title-10">
							<p>Laboratory software makes all activities easy with a few clicks. Moreover, this software demand grows steadily in the current landscape. It has various captivating features such as specimen tracking, audit management, quality control information, personnel management, and tailor-made interface. Therefore, it improves the whole laboratory process from specimen collection to accumulating invoices.</p>
						</div>
					</div>
				</div>
				<script type="application/ld+json">{"@context":"https://schema.org","@type":"FAQPage","mainEntity":[{"@type":"Question","name":"How do laboratories choose the leading pathology lab software?","acceptedAnswer":{"@type":"Answer","text":"Choosing the leading pathology lab software needs to adhere to several steps. Firstly, determine laboratory requirements, find out the challenges, meet with the managers, scrap the top 10 lab software, and finally, choose the one which suits your laboratory. Additionally, the considerable features are scalability, data integrity, and conformity to laboratory codes."}},{"@type":"Question","name":"How does LIMS Software seamlessly integrate data?","acceptedAnswer":{"@type":"Answer","text":"The lab information management system software accumulates lab information from personnel details to the storage information. Moreover, it combined data from various segments such as finance department, sample management department, reporting management, instrument management, and audit management. Additionally, it records patient information with demographic details."}},{"@type":"Question","name":"What are the benefits of barcode scanners in labs?","acceptedAnswer":{"@type":"Answer","text":"Barcode scanner helps to print bills quickly and ease sending code to any person. Moreover, it aids to convert reports into pdf format, saves immense time, and simplifies the process of collecting large amounts of payments. Consequently, patients are extremely glad with this feature."}},{"@type":"Question","name":"Which is the best lab software in <?= $city_name; ?>?","acceptedAnswer":{"@type":"Answer","text":"Healthray is one of the best lab software in <?= $city_name; ?>. Moreover, this software has several functions such as revenue management, specimen management, administrative department, and instrument department. Furthermore, it improves lab efficiency, enhances accuracy, specimen traceability, and effective lab decisions."}},{"@type":"Question","name":"Does Lab Software eliminate complexities?","acceptedAnswer":{"@type":"Answer","text":"Indeed, lab software eliminates complexities. Laboratories have numerous troubles such as difficulty to monitor lab inventories, hard to administer information, take long times to find a single specimen, stand in queue for hours, and waste lab resources. Furthermore, the lab software removes all the discussed challenges through automated laboratory software."}},{"@type":"Question","name":"What is the importance of the best lab software?","acceptedAnswer":{"@type":"Answer","text":"The best lab software has numerous functionalities such as recording each specimen information in the well-defined format, ease to track records, retrieve information at any time you want, reduce laboratory mistakes, eliminate human intervention, and ease conformance with laboratory regulations such as ISO 17025 and GAMP. Additionally, it is effortlessly embedded with the electronic signatures."}},{"@type":"Question","name":"What are the benefits of the LIMS system software?","acceptedAnswer":{"@type":"Answer","text":"The LIMS system software is mainly used to maintain the data in the standardized format and enhance the employee capabilities to work beyond their limits in less time. Furthermore, it has numerous benefits such as scheduling system, prompt reminders, instant message through platform, specimen management, and precise stock control. Additionally, it needs low upfront costs."}},{"@type":"Question","name":"Does lab software integrate several segments?","acceptedAnswer":{"@type":"Answer","text":"Indeed, lab software has the vast potency to connect with various segments such as scheduling department, instrument management department, specimen department, reception department, patient department, and digital invoicing department. Moreover, it connects with each other and aids in creating a collaborative environment."}},{"@type":"Question","name":"What are the benefits of digitalization in laboratories?","acceptedAnswer":{"@type":"Answer","text":"Digitalization in laboratories creates an immense transformation and provides customers too much lab facility. Furthermore, it aids to create a digital presence of laboratories and helps to improve laboratory infrastructure. The best digitalization of laboratories comes with the best lab software like Healthray, which simultaneously handles all laboratory activities."}},{"@type":"Question","name":"What are the distinct features of the lab software?","acceptedAnswer":{"@type":"Answer","text":"Laboratory software makes all activities easy with a few clicks. Moreover, this software demand grows steadily in the current landscape. It has various captivating features such as specimen tracking, audit management, quality control information, personnel management, and tailor-made interface. Therefore, it improves the whole laboratory process from specimen collection to accumulating invoices."}}]}</script>

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